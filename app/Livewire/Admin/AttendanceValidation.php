<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Services\WhatsAppNotificationService;
use Illuminate\Support\Facades\Storage;
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
