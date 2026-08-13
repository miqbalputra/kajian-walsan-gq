<?php

namespace App\Livewire\Panitia;

use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Services\AttendanceScanService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class Scanner extends Component
{
    protected $listeners = ['refreshScannerData' => '$refresh'];

    public $activeEvent = null;

    public $searchQuery = '';

    public $searchResults = [];

    public $showManualModal = false;

    public $lastScanResult = null;

    public $lastScanSuccess = false;

    public $lastScanMessage = '';

    public function mount()
    {
        // Get event opened by admin for attendance.
        $this->activeEvent = KajianEvent::activeForAttendance();
    }

    public function processQrCode($qrCode)
    {
        try {
            $this->lastScanResult = null;
            $this->lastScanSuccess = false;

            // Rate limiting - generous enough for busy check-in lanes but still blocks abuse
            $key = 'scanner:'.auth()->id();
            if (RateLimiter::tooManyAttempts($key, 120)) {
                $seconds = RateLimiter::availableIn($key);
                $this->lastScanMessage = "Terlalu banyak percobaan. Tunggu {$seconds} detik.";
                $this->dispatch('scan-error', message: $this->lastScanMessage);

                return;
            }
            RateLimiter::hit($key, 60);

            if (! $this->activeEvent) {
                $this->lastScanMessage = 'Tidak ada kajian yang sedang dibuka.';
                $this->dispatch('scan-error', message: $this->lastScanMessage);

                return;
            }

            $attendanceScanService = app(AttendanceScanService::class);

            $result = $attendanceScanService->process(
                $this->activeEvent,
                $qrCode,
                auth()->id(),
                request()->userAgent()
            );

            $this->lastScanMessage = $result['message'];

            if ($result['status'] === 'success') {
                $this->lastScanSuccess = true;
                $this->lastScanResult = [
                    'parent_name' => $result['payload']['parentName'],
                    'parent_type' => $result['payload']['parentType'],
                    'child_name' => $result['payload']['childName'],
                    'time' => $result['payload']['time'],
                ];
                $this->dispatch('scan-success', $result['payload']);

                return;
            }

            if ($result['status'] === 'warning') {
                $this->dispatch('scan-warning', message: $this->lastScanMessage);

                return;
            }

            $this->dispatch('scan-error', message: $this->lastScanMessage);
        } catch (\Exception $e) {
            Log::error('Scanner Error: '.$e->getMessage());
            $this->dispatch('scan-error', message: 'Server Error: '.$e->getMessage());
        }
    }

    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) >= 2) {
            $this->searchResults = ParentModel::with(['user', 'students.classRoom'])
                ->when($this->activeEvent, fn ($query) => $query->targetedByEvent($this->activeEvent))
                ->where(function ($query) {
                    $query->whereHas('user', function ($q) {
                        $q->where('name', 'like', '%'.$this->searchQuery.'%');
                    })->orWhereHas('students', function ($q) {
                        $q->where('name', 'like', '%'.$this->searchQuery.'%');
                    });
                })
                ->take(10)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function manualCheckIn($parentId)
    {
        if (! $this->activeEvent) {
            session()->flash('error', 'Tidak ada kajian aktif.');

            return;
        }

        $parent = ParentModel::findOrFail($parentId);
        $result = app(AttendanceScanService::class)->processManual(
            $this->activeEvent,
            $parent,
            auth()->id(),
            request()->userAgent()
        );

        if ($result['status'] === 'success') {
            $this->dispatch('scan-success', $result['payload'] + ['message' => $result['message']]);
        } elseif ($result['status'] === 'warning') {
            $this->dispatch('scan-warning', message: $result['message']);
        } else {
            $this->dispatch('scan-error', message: $result['message']);
        }

        $this->showManualModal = false;
        $this->searchQuery = '';
        $this->searchResults = [];
    }

    public function cancelAttendance($attendanceId)
    {
        // Security: Only allow cancelling attendance from active event
        if (! $this->activeEvent) {
            session()->flash('error', 'Tidak ada kajian aktif.');

            return;
        }

        // Find attendance only from current active event (prevent IDOR)
        $attendance = Attendance::where('id', $attendanceId)
            ->where('kajian_event_id', $this->activeEvent->id)
            ->first();

        if (! $attendance) {
            session()->flash('error', 'Presensi tidak ditemukan.');

            return;
        }

        // Audit log for tracking
        Log::info('Attendance cancelled', [
            'attendance_id' => $attendanceId,
            'parent_name' => $attendance->parent?->user?->name,
            'cancelled_by_user_id' => auth()->id(),
            'cancelled_by_name' => auth()->user()->name,
            'event_id' => $this->activeEvent->id,
            'cancelled_at' => now()->toDateTimeString(),
        ]);

        $attendance->delete();
        $this->activeEvent->updateAttendanceCount();
        session()->flash('message', 'Presensi berhasil dibatalkan.');
    }

    public function getRecentAttendancesProperty()
    {
        if (! $this->activeEvent) {
            return collect();
        }

        return Attendance::with(['parent.user', 'parent.students'])
            ->where('kajian_event_id', $this->activeEvent->id)
            ->latest()
            ->take(5)
            ->get();
    }

    public function getTotalAttendanceProperty()
    {
        if (! $this->activeEvent) {
            return 0;
        }

        return Attendance::where('kajian_event_id', $this->activeEvent->id)->count();
    }

    public function render()
    {
        return view('livewire.panitia.scanner')
            ->layout('components.layouts.panitia', ['title' => 'Scanner Presensi']);
    }
}
