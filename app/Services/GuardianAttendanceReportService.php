<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GuardianAttendanceReportService
{
    public function guardians(): Collection
    {
        return ParentModel::with(['user', 'students.classRoom'])
            ->whereIn('type', ['father', 'mother'])
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

        return $this->guardians()
            ->map(function (ParentModel $guardian) use ($attendances, $event, $eventState) {
                $attendance = $attendances->get($guardian->id);

                return $this->rowFromAttendance($guardian, $event, $attendance, $eventState);
            });
    }

    public function attendanceRows(?string $fromDate, ?string $toDate, ?int $kajianId = null): Collection
    {
        return Attendance::query()
            ->with(['parent.user', 'parent.students.classRoom', 'validator', 'kajianEvent'])
            ->whereHas('parent', fn ($query) => $query->whereIn('type', ['father', 'mother']))
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
                $guardian = $attendance->parent;
                $event = $attendance->kajianEvent;

                if (! $guardian || ! $event) {
                    return null;
                }

                return $this->rowFromAttendance($guardian, $event, $attendance, $this->eventState($event));
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

        $attendances = Attendance::whereIn('kajian_event_id', $events->pluck('id'))
            ->get()
            ->groupBy(fn (Attendance $attendance) => $attendance->kajian_event_id . ':' . $attendance->parent_id);

        return $this->guardians()
            ->when($search, function (Collection $guardians) use ($search) {
                $needle = str($search)->lower()->toString();

                return $guardians->filter(function (ParentModel $guardian) use ($needle) {
                    return str_contains(strtolower($guardian->user?->name ?? ''), $needle)
                        || str_contains(strtolower($guardian->user?->username ?? ''), $needle)
                        || str_contains(strtolower($guardian->user?->email ?? ''), $needle)
                        || str_contains(strtolower($this->childDisplay($guardian)), $needle);
                });
            })
            ->map(function (ParentModel $guardian) use ($events, $attendances) {
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
                    $attendance = $attendances->get($event->id . ':' . $guardian->id)?->first();
                    $derived = $this->deriveStatus($attendance, $eventState);

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
                    'guardian_id' => $guardian->id,
                    'guardian_name' => $guardian->user?->name ?? '-',
                    'username' => $guardian->user?->username ?? '-',
                    'email' => $guardian->user?->email ?? '-',
                    'guardian_type' => $guardian->type_display,
                    'children' => $this->childDisplay($guardian),
                    'total_events' => $events->count(),
                    'participation_rate' => $rate,
                ]);
            })
            ->sort(function (array $a, array $b) {
                return [$b['participation_rate'], $a['alpha'], $a['guardian_name']]
                    <=> [$a['participation_rate'], $b['alpha'], $b['guardian_name']];
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
                    return str_contains(strtolower($row['guardian_name'] ?? ''), $needle)
                        || str_contains(strtolower($row['username'] ?? ''), $needle)
                        || str_contains(strtolower($row['email'] ?? ''), $needle)
                        || str_contains(strtolower($row['children'] ?? ''), $needle)
                        || str_contains(strtolower($row['kajian_title'] ?? ''), $needle);
                });
            })
            ->values();
    }

    public function rowFromAttendance(ParentModel $guardian, KajianEvent $event, ?Attendance $attendance, string $eventState): array
    {
        $derived = $this->deriveStatus($attendance, $eventState);

        return [
            'guardian_id' => $guardian->id,
            'guardian_name' => $guardian->user?->name ?? '-',
            'username' => $guardian->user?->username ?? '-',
            'email' => $guardian->user?->email ?? '-',
            'guardian_type' => $guardian->type_display,
            'children' => $this->childDisplay($guardian),
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
            'document_label' => $attendance?->status === Attendance::STATUS_IZIN ? 'Surat Izin' : 'Bukti Upload',
            'proof_url' => $attendance?->proof_file ? CloudinaryService::getDisplayUrl($attendance->proof_file) : null,
            'notes' => $attendance?->notes,
            'validated_by' => $attendance?->validator?->name,
            'submitted_at' => optional($attendance?->created_at)->format('d/m/Y H:i'),
            'scanned_at' => optional($attendance?->scanned_at)->format('d/m/Y H:i'),
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

    public function deriveStatus(?Attendance $attendance, string $eventState): array
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
                'reason' => 'Belum tercatat hadir, online, atau izin pada kajian ini.',
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
                'reason' => 'Presensi sudah tercatat dan menunggu validasi.',
            ];
        }

        return match ($attendance->status) {
            Attendance::STATUS_HADIR_FISIK => [
                'status' => Attendance::STATUS_HADIR_FISIK,
                'label' => 'Hadir Langsung',
                'badge' => 'bg-emerald-100 text-emerald-700',
                'reason' => 'Kehadiran langsung sudah tercatat.',
            ],
            Attendance::STATUS_HADIR_ONLINE => [
                'status' => Attendance::STATUS_HADIR_ONLINE,
                'label' => 'Menyimak Online',
                'badge' => 'bg-blue-100 text-blue-700',
                'reason' => 'Kehadiran online sudah tervalidasi.',
            ],
            Attendance::STATUS_IZIN => [
                'status' => Attendance::STATUS_IZIN,
                'label' => 'Izin',
                'badge' => 'bg-yellow-100 text-yellow-700',
                'reason' => 'Izin sudah tervalidasi.',
            ],
            default => [
                'status' => Attendance::STATUS_ALPHA,
                'label' => 'Alfa',
                'badge' => 'bg-red-100 text-red-700',
                'reason' => 'Status presensi tidak aktif.',
            ],
        };
    }

    protected function childDisplay(ParentModel $guardian): string
    {
        return $guardian->students
            ->map(fn ($student) => $student->name . ($student->classRoom ? ' (' . $student->classRoom->name . ')' : ''))
            ->filter()
            ->join(', ') ?: '-';
    }
}
