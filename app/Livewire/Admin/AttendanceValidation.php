<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Services\AiProviderService;
use App\Services\WhatsAppNotificationService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceValidation extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending';
    public $perPage = 10;

    // Modal state
    public $showProofModal = false;
    public $showRejectModal = false;
    public $selectedAttendance = null;
    public $rejectionReason = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewProof($id)
    {
        $this->selectedAttendance = Attendance::with('parent.user')->findOrFail($id);
        $this->showProofModal = true;
    }

    public function approve($id)
    {
        $attendance = Attendance::with(['parent.user', 'kajianEvent'])->findOrFail($id);
        $attendance->update([
            'validation_status' => 'approved',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        // Kirim notifikasi WhatsApp via n8n
        try {
            app(WhatsAppNotificationService::class)->sendApprovalNotification($attendance);
        } catch (\Exception $e) {
            // Jangan ganggu proses approve jika WA gagal
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Presensi berhasil disetujui!']);
        $this->showProofModal = false;
    }

    public function openRejectModal($id)
    {
        $this->selectedAttendance = Attendance::findOrFail($id);
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|string|max:500',
        ]);

        $this->selectedAttendance->load(['parent.user', 'kajianEvent']);
        $this->selectedAttendance->update([
            'validation_status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        // Kirim notifikasi WhatsApp via n8n
        try {
            app(WhatsAppNotificationService::class)->sendRejectionNotification($this->selectedAttendance);
        } catch (\Exception $e) {
            // Jangan ganggu proses reject jika WA gagal
        }

        $this->showRejectModal = false;
        $this->showProofModal = false;
        $this->dispatch('notify', ['type' => 'warning', 'message' => 'Presensi ditolak.']);
    }

    public function revertToPending($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update([
            'validation_status' => 'pending',
            'rejection_reason' => null,
            'validated_by' => null,
            'validated_at' => null,
        ]);

        $this->dispatch('notify', ['type' => 'info', 'message' => 'Status presensi dikembalikan ke pending.']);
    }

    public function reviewWithAi($id)
    {
        if (! app(AiProviderService::class)->configured()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'AI belum aktif atau belum lengkap di Pengaturan.',
            ]);
            return;
        }

        $attendance = Attendance::with(['parent.user', 'parent.students.classRoom', 'kajianEvent'])->findOrFail($id);

        try {
            $result = app(AiProviderService::class)->autoReviewAttendance($attendance);
            $fresh = $attendance->fresh();

            if ($fresh->validation_status === Attendance::VALIDATION_APPROVED) {
                try {
                    app(WhatsAppNotificationService::class)->sendApprovalNotification($fresh->load(['parent.user', 'kajianEvent']));
                } catch (\Throwable $exception) {
                    Log::warning('[AI] WhatsApp notification after AI approval failed', [
                        'attendance_id' => $fresh->id,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            $message = $fresh->validation_status === Attendance::VALIDATION_APPROVED
                ? "AI menyetujui presensi ({$result['confidence']}%)."
                : "AI sudah mengecek ({$result['confidence']}%), tetap perlu review admin.";

            $this->dispatch('notify', [
                'type' => $fresh->validation_status === Attendance::VALIDATION_APPROVED ? 'success' : 'warning',
                'message' => $message,
            ]);
        } catch (\Throwable $exception) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'AI gagal mengecek: ' . $exception->getMessage(),
            ]);
        }
    }

    public function reviewPendingWithAi()
    {
        if (! app(AiProviderService::class)->configured()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'AI belum aktif atau belum lengkap di Pengaturan.',
            ]);
            return;
        }

        $attendances = Attendance::where('method', 'upload')
            ->where('validation_status', Attendance::VALIDATION_PENDING)
            ->whereNotNull('proof_file')
            ->oldest()
            ->limit(20)
            ->get();

        $approved = 0;
        $needsReview = 0;
        $failed = 0;

        foreach ($attendances as $attendance) {
            try {
                app(AiProviderService::class)->autoReviewAttendance($attendance);
                $attendance->refresh();
                $attendance->validation_status === Attendance::VALIDATION_APPROVED ? $approved++ : $needsReview++;
            } catch (\Throwable $exception) {
                $failed++;
                Log::warning('[AI] Batch attendance review failed', [
                    'attendance_id' => $attendance->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $this->dispatch('notify', [
            'type' => $failed > 0 ? 'warning' : 'success',
            'message' => "AI selesai: {$approved} disetujui, {$needsReview} tetap pending, {$failed} gagal.",
        ]);
    }

    public function render()
    {
        $attendances = Attendance::with(['parent.user', 'parent.students', 'kajianEvent'])
            ->whereIn('method', ['upload']) // Only those with uploaded proofs
            ->when($this->statusFilter, function ($query) {
                $query->where('validation_status', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('parent.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.attendance-validation', [
            'attendances' => $attendances
        ])->layout('components.layouts.admin', ['title' => 'Validasi Presensi']);
    }
}
