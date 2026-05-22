<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Services\CloudinaryService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Presensi Guru')]
class TeacherAttendanceIndex extends Component
{
    use WithPagination;

    public string $kajianId = '';
    public string $statusFilter = '';
    public string $search = '';
    public int $perPage = 20;

    public function mount(): void
    {
        $this->kajianId = (string) (KajianEvent::whereDate('date', '<=', today())
            ->orderByDesc('date')
            ->orderByDesc('time_start')
            ->first()?->id ?? '');
    }

    public function updatingKajianId(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getKajiansProperty()
    {
        return KajianEvent::orderByDesc('date')
            ->orderByDesc('time_start')
            ->get();
    }

    public function getSelectedKajianProperty(): ?KajianEvent
    {
        return $this->kajianId ? KajianEvent::find($this->kajianId) : null;
    }

    public function getRowsProperty(): LengthAwarePaginator
    {
        $rows = $this->buildRows()
            ->when($this->statusFilter, fn ($rows) => $rows->where('derived_status', $this->statusFilter))
            ->when($this->search, function ($rows) {
                $needle = str($this->search)->lower()->toString();

                return $rows->filter(function ($row) use ($needle) {
                    return str_contains(strtolower($row['teacher_name'] ?? ''), $needle)
                        || str_contains(strtolower($row['username'] ?? ''), $needle)
                        || str_contains(strtolower($row['email'] ?? ''), $needle);
                });
            })
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $rows->forPage($page, $this->perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $rows->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getSummaryProperty(): array
    {
        $rows = $this->buildRows();

        return [
            'total' => $rows->count(),
            'hadir_fisik' => $rows->where('derived_status', Attendance::STATUS_HADIR_FISIK)->count(),
            'hadir_online' => $rows->where('derived_status', Attendance::STATUS_HADIR_ONLINE)->count(),
            'izin' => $rows->where('derived_status', Attendance::STATUS_IZIN)->count(),
            'pending' => $rows->where('derived_status', Attendance::VALIDATION_PENDING)->count(),
            'rejected' => $rows->where('derived_status', Attendance::VALIDATION_REJECTED)->count(),
            'not_started' => $rows->where('derived_status', 'not_started')->count(),
            'alpha' => $rows->where('derived_status', Attendance::STATUS_ALPHA)->count(),
        ];
    }

    protected function buildRows()
    {
        if (! $this->kajianId) {
            return collect();
        }

        $selectedKajian = $this->selectedKajian;
        $eventState = $selectedKajian ? $this->teacherAttendanceEventState($selectedKajian) : 'ended';

        $attendances = Attendance::with(['validator'])
            ->where('kajian_event_id', $this->kajianId)
            ->get()
            ->keyBy('parent_id');

        return ParentModel::with('user')
            ->where(function ($query) {
                $query->where('type', 'teacher')
                    ->orWhere('is_teacher', true);
            })
            ->orderBy('id')
            ->get()
            ->map(function (ParentModel $teacher) use ($attendances, $eventState) {
                $attendance = $attendances->get($teacher->id);
                $derived = $this->deriveTeacherStatus($attendance, $teacher, $eventState);

                return [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->user?->name ?? '-',
                    'username' => $teacher->user?->username ?? '-',
                    'email' => $teacher->user?->email ?? '-',
                    'attendance_id' => $attendance?->id,
                    'raw_status' => $attendance?->status,
                    'method' => $attendance?->method,
                    'validation_status' => $attendance?->validation_status,
                    'derived_status' => $derived['status'],
                    'derived_label' => $derived['label'],
                    'derived_badge' => $derived['badge'],
                    'reason' => $derived['reason'],
                    'proof_url' => $attendance?->proof_file ? CloudinaryService::getDisplayUrl($attendance->proof_file) : null,
                    'notes' => $attendance?->notes,
                    'validated_by' => $attendance?->validator?->name,
                    'submitted_at' => optional($attendance?->created_at)->format('d/m/Y H:i'),
                ];
            });
    }

    protected function teacherAttendanceEventState(KajianEvent $event): string
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

    protected function deriveTeacherStatus(?Attendance $attendance, ParentModel $teacher, string $eventState): array
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

    public function approveTeacherAttendance(int $attendanceId): void
    {
        $attendance = Attendance::whereHas('parent', function ($query) {
                $query->where('type', 'teacher')
                    ->orWhere('is_teacher', true);
            })
            ->whereKey($attendanceId)
            ->firstOrFail();

        if (! $attendance->proof_file) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Guru belum upload dokumen wajib, belum bisa disetujui.',
            ]);
            return;
        }

        $attendance->update([
            'validation_status' => Attendance::VALIDATION_APPROVED,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
            'rejection_reason' => null,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Presensi guru berhasil disetujui.',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.teacher-attendance-index', [
            'rows' => $this->rows,
            'kajians' => $this->kajians,
        ]);
    }
}
