<?php

namespace App\Livewire\Panitia;

use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Scanner extends Component
{
    public $activeEvent = null;
    public $searchQuery = '';
    public $searchResults = [];
    public $showManualModal = false;
    public $lastScanResult = null;
    public $lastScanSuccess = false;
    public $lastScanMessage = '';

    public function mount()
    {
        // Get active event (open status)
        $this->activeEvent = KajianEvent::where('status', 'open')
            ->whereDate('date', '=', today())
            ->first();
    }

    public function processQrCode($qrCode)
    {
        $this->lastScanResult = null;
        $this->lastScanSuccess = false;

        // Rate limiting - max 20 scans per minute per user/device
        $key = 'scanner:' . auth()->id() . ':' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $seconds = RateLimiter::availableIn($key);
            $this->lastScanMessage = "Terlalu banyak percobaan. Tunggu {$seconds} detik.";
            $this->dispatch('scan-error', message: $this->lastScanMessage);
            return;
        }
        RateLimiter::hit($key, 60);

        if (!$this->activeEvent) {
            $this->lastScanMessage = 'Tidak ada kajian aktif hari ini.';
            $this->dispatch('scan-error', message: $this->lastScanMessage);
            return;
        }

        // Find parent by QR code
        $parent = ParentModel::with(['user', 'students.classRoom'])->where('qr_code_string', $qrCode)->first();

        if (!$parent) {
            $this->lastScanMessage = 'QR Code tidak ditemukan dalam sistem.';
            $this->dispatch('scan-error', message: $this->lastScanMessage);
            return;
        }

        // Check if already scanned today for this event
        $existingAttendance = Attendance::where('kajian_event_id', $this->activeEvent->id)
            ->where('parent_id', $parent->id)
            ->first();

        if ($existingAttendance) {
            $this->lastScanMessage = $parent->user->name . ' sudah tercatat hadir.';
            $this->dispatch('scan-warning', message: $this->lastScanMessage, name: $parent->user->name);
            return;
        }

        // Find all children
        $students = $parent->students;
        $childDisplayNames = [];
        $recordedCount = 0;

        if ($students->count() > 0) {
            foreach ($students as $student) {
                // Check if this specific student already has attendance recorded
                $exists = Attendance::where('kajian_event_id', $this->activeEvent->id)
                    ->where('student_id', $student->id)
                    ->exists();

                if (!$exists) {
                    Attendance::create([
                        'kajian_event_id' => $this->activeEvent->id,
                        'parent_id' => $parent->id,
                        'student_id' => $student->id,
                        'status' => 'hadir_fisik',
                        'method' => 'scan_qr',
                        'validation_status' => 'approved',
                        'validated_by' => auth()->id(),
                        'validated_at' => now(),
                        'scanned_at' => now(),
                        'device_info' => request()->userAgent(),
                    ]);
                    $recordedCount++;
                }
                $childDisplayNames[] = $student->name . ($student->classRoom ? ' (' . $student->classRoom->name . ')' : '');
            }
        } else {
            // Fallback for parents without linked students
            Attendance::create([
                'kajian_event_id' => $this->activeEvent->id,
                'parent_id' => $parent->id,
                'student_id' => null,
                'status' => 'hadir_fisik',
                'method' => 'scan_qr',
                'validation_status' => 'approved',
                'validated_by' => auth()->id(),
                'validated_at' => now(),
                'scanned_at' => now(),
                'device_info' => request()->userAgent(),
            ]);
            $recordedCount = 1;
        }

        $childNameDisplay = count($childDisplayNames) > 0
            ? (count($childDisplayNames) . " Santri: " . implode(', ', $childDisplayNames))
            : 'Tidak ada data santri';

        $this->lastScanSuccess = true;
        $this->lastScanResult = [
            'parent_name' => $parent->user->name,
            'parent_type' => $parent->type_display,
            'child_name' => $childNameDisplay,
            'time' => now()->format('H:i'),
        ];

        $parentType = $parent->type === 'father' ? 'Bapak' : 'Ibu';
        $this->lastScanMessage = "Selamat Datang, {$parentType} {$parent->user->name}. Berhasil mencatat presensi untuk " . ($students->count() ?: 1) . " santri.";

        $this->dispatch('scan-success', [
            'message' => $this->lastScanMessage,
            'parentName' => $parent->user->name,
            'parentType' => $parent->type_display,
            'childName' => $childNameDisplay,
        ]);
    }

    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) >= 2) {
            $this->searchResults = ParentModel::with(['user', 'students.classRoom'])
                ->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchQuery . '%');
                })
                ->orWhereHas('students', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchQuery . '%');
                })
                ->take(10)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function manualCheckIn($parentId)
    {
        if (!$this->activeEvent) {
            session()->flash('error', 'Tidak ada kajian aktif.');
            return;
        }

        $parent = ParentModel::with('user', 'students')->findOrFail($parentId);

        // Check if already checked in
        $existingAttendance = Attendance::where('kajian_event_id', $this->activeEvent->id)
            ->where('parent_id', $parent->id)
            ->first();

        if ($existingAttendance) {
            $this->dispatch('scan-warning', message: $parent->user->name . ' sudah tercatat hadir.');
            $this->showManualModal = false;
            return;
        }

        $students = $parent->students;
        $childDisplayNames = [];

        if ($students->count() > 0) {
            foreach ($students as $student) {
                $exists = Attendance::where('kajian_event_id', $this->activeEvent->id)
                    ->where('student_id', $student->id)
                    ->exists();

                if (!$exists) {
                    Attendance::create([
                        'kajian_event_id' => $this->activeEvent->id,
                        'parent_id' => $parent->id,
                        'student_id' => $student->id,
                        'status' => 'hadir_fisik',
                        'method' => 'manual',
                        'validation_status' => 'approved',
                        'validated_by' => auth()->id(),
                        'validated_at' => now(),
                    ]);
                }
                $childDisplayNames[] = $student->name . ($student->classRoom ? ' (' . $student->classRoom->name . ')' : '');
            }
        } else {
            Attendance::create([
                'kajian_event_id' => $this->activeEvent->id,
                'parent_id' => $parent->id,
                'student_id' => null,
                'status' => 'hadir_fisik',
                'method' => 'manual',
                'validation_status' => 'approved',
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);
        }

        $parentType = $parent->type === 'father' ? 'Bapak' : 'Ibu';
        $childNameDisplay = count($childDisplayNames) > 0
            ? (count($childDisplayNames) . " Santri: " . implode(', ', $childDisplayNames))
            : 'Tidak ada data santri';

        $this->dispatch('scan-success', [
            'message' => "Selamat Datang, {$parentType} {$parent->user->name}! Berhasil mencatat presensi untuk " . ($students->count() ?: 1) . " santri.",
            'parentName' => $parent->user->name,
            'parentType' => $parent->type_display,
            'childName' => $childNameDisplay,
        ]);

        $this->showManualModal = false;
        $this->searchQuery = '';
        $this->searchResults = [];
    }

    public function cancelAttendance($attendanceId)
    {
        // Security: Only allow cancelling attendance from active event
        if (!$this->activeEvent) {
            session()->flash('error', 'Tidak ada kajian aktif.');
            return;
        }

        // Find attendance only from current active event (prevent IDOR)
        $attendance = Attendance::where('id', $attendanceId)
            ->where('kajian_event_id', $this->activeEvent->id)
            ->first();

        if (!$attendance) {
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
        session()->flash('message', 'Presensi berhasil dibatalkan.');
    }

    public function getRecentAttendancesProperty()
    {
        if (!$this->activeEvent) {
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
        if (!$this->activeEvent) {
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
