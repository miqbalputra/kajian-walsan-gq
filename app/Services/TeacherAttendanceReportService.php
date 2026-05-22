<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TeacherAttendanceReportService
{
    public function teachers(): Collection
    {
        return ParentModel::with('user')
            ->where(function ($query) {
                $query->where('type', 'teacher')
                    ->orWhere('is_teacher', true);
            })
            ->orderBy('id')
            ->get();
    }

    public function rowsForEvent(?KajianEvent $event): Collection
    {
        if (! $event) {
            return collect();
        }

        $eventState = $this->eventState($event);

        $attendances = Attendance::with(['validator'])
            ->where('kajian_event_id', $event->id)
            ->get()
            ->keyBy('parent_id');

        return $this->teachers()
            ->map(function (ParentModel $teacher) use ($attendances, $event, $eventState) {
                $attendance = $attendances->get($teacher->id);

                return $this->rowFromAttendance($teacher, $event, $attendance, $eventState);
            });
    }

    public function attendanceRows(?string $fromDate, ?string $toDate, ?int $kajianId = null): Collection
    {
        return Attendance::query()
            ->with(['parent.user', 'validator', 'kajianEvent'])
            ->whereHas('parent', function ($query) {
                $query->where('type', 'teacher')
                    ->orWhere('is_teacher', true);
            })
            ->when($kajianId, fn ($query) => $query->where('kajian_event_id', $kajianId))
            ->when($fromDate, function ($query) use ($fromDate) {
                $query->whereHas('kajianEvent', fn ($eventQuery) => $eventQuery->whereDate('date', '>=', $fromDate));
            })
            ->when($toDate, function ($query) use ($toDate) {
                $query->whereHas('kajianEvent', fn ($eventQuery) => $eventQuery->whereDate('date', '<=', $toDate));
            })
            ->orderByDesc(
                KajianEvent::select('date')
                    ->whereColumn('kajian_events.id', 'attendances.kajian_event_id')
                    ->limit(1)
            )
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Attendance $attendance) {
                $teacher = $attendance->parent;
                $event = $attendance->kajianEvent;

                if (! $teacher || ! $event) {
                    return null;
                }

                return $this->rowFromAttendance($teacher, $event, $attendance, $this->eventState($event));
            })
            ->filter()
            ->values();
    }

    public function summary(Collection $rows): array
    {
        return [
            'total' => $rows->count(),
            'hadir_fisik' => $rows->where('derived_status', Attendance::STATUS_HADIR_FISIK)->count(),
            'hadir_online' => $rows->where('derived_status', Attendance::STATUS_HADIR_ONLINE)->count(),
            'izin' => $rows->where('derived_status', Attendance::STATUS_IZIN)->count(),
            'pending' => $rows->where('derived_status', Attendance::VALIDATION_PENDING)->count(),
            'rejected' => $rows->where('derived_status', Attendance::VALIDATION_REJECTED)->count(),
            'not_started' => $rows->where('derived_status', 'not_started')->count(),
            'alpha' => $rows->where('derived_status', Attendance::STATUS_ALPHA)->count(),
            'uploaded' => $rows->whereNotNull('proof_url')->count(),
        ];
    }

    public function performance(?string $fromDate, ?string $toDate, ?string $search = null): Collection
    {
        $events = KajianEvent::query()
            ->when($fromDate, fn ($query) => $query->whereDate('date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('date', '<=', $toDate))
            ->whereDate('date', '<=', today())
            ->orderBy('date')
            ->orderBy('time_start')
            ->get();

        $attendances = Attendance::with('validator')
            ->whereIn('kajian_event_id', $events->pluck('id'))
            ->get()
            ->groupBy(fn (Attendance $attendance) => $attendance->kajian_event_id . ':' . $attendance->parent_id);

        return $this->teachers()
            ->when($search, function (Collection $teachers) use ($search) {
                $needle = str($search)->lower()->toString();

                return $teachers->filter(function (ParentModel $teacher) use ($needle) {
                    return str_contains(strtolower($teacher->user?->name ?? ''), $needle)
                        || str_contains(strtolower($teacher->user?->username ?? ''), $needle)
                        || str_contains(strtolower($teacher->user?->email ?? ''), $needle);
                });
            })
            ->map(function (ParentModel $teacher) use ($events, $attendances) {
                $counts = [
                    'hadir_fisik' => 0,
                    'hadir_online' => 0,
                    'izin' => 0,
                    'pending' => 0,
                    'rejected' => 0,
                    'alpha' => 0,
                    'not_started' => 0,
                    'uploaded' => 0,
                    'completed_events' => 0,
                ];

                foreach ($events as $event) {
                    $eventState = $this->eventState($event);
                    $attendance = $attendances->get($event->id . ':' . $teacher->id)?->first();
                    $derived = $this->deriveStatus($attendance, $teacher, $eventState);

                    if ($eventState === 'ended') {
                        $counts['completed_events']++;
                    }

                    if (array_key_exists($derived['status'], $counts)) {
                        $counts[$derived['status']]++;
                    }

                    if ($attendance?->proof_file) {
                        $counts['uploaded']++;
                    }
                }

                $participation = $counts['hadir_fisik']
                    + $counts['hadir_online']
                    + $counts['izin']
                    + $counts['pending']
                    + $counts['rejected'];

                $rate = $counts['completed_events'] > 0
                    ? round(($participation / $counts['completed_events']) * 100)
                    : 0;

                return array_merge($counts, [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->user?->name ?? '-',
                    'username' => $teacher->user?->username ?? '-',
                    'email' => $teacher->user?->email ?? '-',
                    'teacher_type' => $teacher->isPureTeacher() ? 'Guru Murni' : 'Wali Santri + Guru',
                    'total_events' => $events->count(),
                    'participation_rate' => $rate,
                ]);
            })
            ->sort(function (array $a, array $b) {
                return [$b['participation_rate'], $a['alpha'], $a['teacher_name']]
                    <=> [$a['participation_rate'], $b['alpha'], $b['teacher_name']];
            })
            ->values();
    }

    public function filterRows(Collection $rows, ?string $status = null, ?string $search = null): Collection
    {
        return $rows
            ->when($status, fn (Collection $rows) => $rows->where('derived_status', $status))
            ->when($search, function (Collection $rows) use ($search) {
                $needle = str($search)->lower()->toString();

                return $rows->filter(function (array $row) use ($needle) {
                    return str_contains(strtolower($row['teacher_name'] ?? ''), $needle)
                        || str_contains(strtolower($row['username'] ?? ''), $needle)
                        || str_contains(strtolower($row['email'] ?? ''), $needle)
                        || str_contains(strtolower($row['kajian_title'] ?? ''), $needle);
                });
            })
            ->values();
    }

    public function rowFromAttendance(ParentModel $teacher, KajianEvent $event, ?Attendance $attendance, string $eventState): array
    {
        $derived = $this->deriveStatus($attendance, $teacher, $eventState);

        return [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->user?->name ?? '-',
            'username' => $teacher->user?->username ?? '-',
            'email' => $teacher->user?->email ?? '-',
            'teacher_type' => $teacher->isPureTeacher() ? 'Guru Murni' : 'Wali Santri + Guru',
            'kajian_id' => $event->id,
            'kajian_title' => $event->title,
            'kajian_date' => $event->date?->format('d/m/Y'),
            'attendance_id' => $attendance?->id,
            'raw_status' => $attendance?->status,
            'method' => $attendance?->method,
            'validation_status' => $attendance?->validation_status,
            'derived_status' => $derived['status'],
            'derived_label' => $derived['label'],
            'derived_badge' => $derived['badge'],
            'reason' => $derived['reason'],
            'document_label' => $attendance?->status === Attendance::STATUS_IZIN ? 'Surat Izin' : 'Catatan Kajian',
            'proof_url' => $attendance?->proof_file ? CloudinaryService::getDisplayUrl($attendance->proof_file) : null,
            'notes' => $attendance?->notes,
            'validated_by' => $attendance?->validator?->name,
            'submitted_at' => optional($attendance?->created_at)->format('d/m/Y H:i'),
            'scanned_at' => optional($attendance?->scanned_at)->format('d/m/Y H:i'),
            'ai_status' => $attendance?->ai_validation_status,
            'ai_confidence' => $attendance?->ai_validation_confidence,
            'ai_reason' => $attendance?->ai_validation_reason,
        ];
    }

    public function eventState(KajianEvent $event): string
    {
        $start = Carbon::parse($event->date->format('Y-m-d') . ' ' . Carbon::parse($event->time_start)->format('H:i:s'));
        $end = Carbon::parse($event->date->format('Y-m-d') . ' ' . Carbon::parse($event->time_end)->format('H:i:s'));

        if (now()->lt($start)) {
            return 'not_started';
        }

        return now()->lte($end) && $event->status !== 'closed'
            ? 'in_progress'
            : 'ended';
    }

    public function deriveStatus(?Attendance $attendance, ParentModel $teacher, string $eventState): array
    {
        if (! $attendance) {
            if ($eventState !== 'ended') {
                return [
                    'status' => 'not_started',
                    'label' => $eventState === 'not_started' ? 'Belum Mulai' : 'Berjalan',
                    'badge' => 'bg-slate-100 text-slate-700',
                    'reason' => $eventState === 'not_started'
                        ? 'Kajian belum dimulai. Status alfa dihitung setelah kajian selesai.'
                        : 'Kajian sedang berlangsung. Status alfa dihitung setelah kajian selesai.',
                ];
            }

            return [
                'status' => Attendance::STATUS_ALPHA,
                'label' => 'Alfa',
                'badge' => 'bg-red-100 text-red-700',
                'reason' => $teacher->isPureTeacher()
                    ? 'Belum upload catatan kajian dan belum mengirim izin.'
                    : 'Belum scan QR, belum upload catatan, dan belum mengirim izin.',
            ];
        }

        if (! $attendance->proof_file) {
            if ($eventState !== 'ended') {
                return [
                    'status' => Attendance::VALIDATION_PENDING,
                    'label' => 'Menunggu Catatan',
                    'badge' => 'bg-amber-100 text-amber-700',
                    'reason' => $teacher->isPureTeacher()
                        ? 'Kajian belum selesai. Guru masih dapat upload catatan kajian.'
                        : 'Sudah scan QR. Catatan kajian masih dapat diupload sebelum status alfa dihitung.',
                ];
            }

            return [
                'status' => Attendance::STATUS_ALPHA,
                'label' => 'Alfa',
                'badge' => 'bg-red-100 text-red-700',
                'reason' => match (true) {
                    $teacher->isPureTeacher() => 'Belum upload catatan kajian atau dokumen wajib.',
                    $attendance->status === Attendance::STATUS_HADIR_FISIK => 'Sudah scan QR, tetapi belum upload catatan hasil kajian.',
                    default => 'Belum upload dokumen wajib.',
                },
            ];
        }

        if ($attendance->validation_status === Attendance::VALIDATION_REJECTED) {
            return [
                'status' => Attendance::VALIDATION_REJECTED,
                'label' => 'Ditolak',
                'badge' => 'bg-rose-100 text-rose-700',
                'reason' => $attendance->rejection_reason ?: 'Upload ditolak dan perlu diperbaiki.',
            ];
        }

        if ($attendance->validation_status === Attendance::VALIDATION_PENDING) {
            return [
                'status' => Attendance::VALIDATION_PENDING,
                'label' => 'Menunggu Validasi',
                'badge' => 'bg-amber-100 text-amber-700',
                'reason' => 'Dokumen sudah diupload dan menunggu validasi.',
            ];
        }

        return match ($attendance->status) {
            Attendance::STATUS_HADIR_FISIK => [
                'status' => Attendance::STATUS_HADIR_FISIK,
                'label' => 'Hadir Langsung',
                'badge' => 'bg-emerald-100 text-emerald-700',
                'reason' => $teacher->isPureTeacher()
                    ? 'Catatan kajian hadir langsung sudah tervalidasi.'
                    : 'Scan QR dan catatan kajian sudah tervalidasi.',
            ],
            Attendance::STATUS_HADIR_ONLINE => [
                'status' => Attendance::STATUS_HADIR_ONLINE,
                'label' => 'Menyimak Online',
                'badge' => 'bg-blue-100 text-blue-700',
                'reason' => 'Catatan hasil kajian online sudah tervalidasi.',
            ],
            Attendance::STATUS_IZIN => [
                'status' => Attendance::STATUS_IZIN,
                'label' => 'Izin',
                'badge' => 'bg-yellow-100 text-yellow-700',
                'reason' => 'Surat pernyataan izin sudah tervalidasi.',
            ],
            default => [
                'status' => Attendance::STATUS_ALPHA,
                'label' => 'Alfa',
                'badge' => 'bg-red-100 text-red-700',
                'reason' => 'Status tidak memenuhi aturan presensi guru.',
            ],
        };
    }
}
