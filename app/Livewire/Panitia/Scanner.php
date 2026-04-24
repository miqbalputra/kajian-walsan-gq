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
        try {
            $this->lastScanResult = null;
            $this->lastScanSuccess = false;

            // Rate limiting - max 30 scans per minute per user
            $key = 'scanner:' . auth()->id();
            if (RateLimiter::tooManyAttempts($key, 30)) {
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

            // Find all children for display purposes
            $students = $parent->students;
            $childDisplayNames = [];
            
            foreach ($students as $student) {
                $childDisplayNames[] = $student->name . ($student->classRoom ? ' (' . $student->classRoom->name . ')' : '');
            }

            // Check if already scanned today for this event (including soft-deleted records)
            $existingAttendance = Attendance::withTrashed()
                ->where('kajian_event_id', $this->activeEvent->id)
                ->where('parent_id', $parent->id)
                ->first();

            if ($existingAttendance) {
                if ($existingAttendance->trashed()) {
                    // Jika pernah dihapus (dibatalkan), pulihkan datanya daripada buat baru (menghindari duplicate entry)
                    $existingAttendance->restore();
                    $existingAttendance->update([
                        'student_id' => $students->first()?->id,
                        'method' => 'scan_qr',
                        'validation_status' => $parent->isTeacher() ? 'pending' : 'approved',
                        'validated_by' => $parent->isTeacher() ? null : auth()->id(),
                        'validated_at' => $parent->isTeacher() ? null : now(),
                        'scanned_at' => now(),
                        'device_info' => request()->userAgent(),
                    ]);
                } else {
                    $this->lastScanMessage = $parent->user->name . ' sudah tercatat hadir.';
                    $this->dispatch('scan-warning', message: $this->lastScanMessage, name: $parent->user->name);
                    return;
                }
            } else {
                // Record single attendance for the family
                Attendance::create([
                    'kajian_event_id' => $this->activeEvent->id,
                    'parent_id' => $parent->id,
                    'student_id' => $students->first()?->id,
                    'status' => 'hadir_fisik',
                    'method' => 'scan_qr',
                    'validation_status' => $parent->isTeacher() ? 'pending' : 'approved',
                    'validated_by' => $parent->isTeacher() ? null : auth()->id(),
                    'validated_at' => $parent->isTeacher() ? null : now(),
                    'scanned_at' => now(),
                    'device_info' => request()->userAgent(),
                ]);
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

            $parentType = match($parent->type) {
                'father' => 'Bapak',
                'mother' => 'Ibu',
                'teacher' => 'Ustadz/ah',
                default => 'Peserta',
            };

            if ($parent->isTeacher()) {
                $this->lastScanMessage = "Selamat Datang, {$parentType} {$parent->user->name}. Berhasil mencatat, mohon ingatkan untuk upload catatan kajian di dashboard.";
            } else {
                $this->lastScanMessage = "Selamat Datang, {$parentType} {$parent->user->name}. Berhasil mencatat presensi untuk " . ($students->count() ?: 1) . " santri.";
            }

            $this->dispatch('scan-success', [
                'message' => $this->lastScanMessage,
                'parentName' => $parent->user->name,
                'parentType' => $parent->type_display,
                'childName' => $childNameDisplay,
            ]);
        } catch (\Exception $e) {
            Log::error('Scanner Error: ' . $e->getMessage());
            $this->dispatch('scan-error', message: 'Server Error: ' . $e->getMessage());
        }
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

        foreach ($students as $student) {
            $childDisplayNames[] = $student->name . ($student->classRoom ? ' (' . $student->classRoom->name . ')' : '');
        }

        // Record single attendance for manual check-in
        Attendance::create([
            'kajian_event_id' => $this->activeEvent->id,
            'parent_id' => $parent->id,
            'student_id' => $students->first()?->id,
            'status' => 'hadir_fisik',
            'method' => 'manual',
            'validation_status' => $parent->isTeacher() ? 'pending' : 'approved',
            'validated_by' => $parent->isTeacher() ? null : auth()->id(),
            'validated_at' => $parent->isTeacher() ? null : now(),
        ]);

        $parentType = match($parent->type) {
            'father' => 'Bapak',
            'mother' => 'Ibu',
            'teacher' => 'Ustadz/ah',
            default => 'Peserta',
        };

        $childNameDisplay = count($childDisplayNames) > 0
            ? (count($childDisplayNames) . " Santri: " . implode(', ', $childDisplayNames))
            : 'Tidak ada data santri';

        $message = $parent->isTeacher()
            ? "Selamat Datang, {$parentType} {$parent->user->name}! Berhasil mencatat, mohon ingatkan untuk upload catatan kajian."
            : "Selamat Datang, {$parentType} {$parent->user->name}! Berhasil mencatat presensi untuk " . ($students->count() ?: 1) . " santri.";

        $this->dispatch('scan-success', [
            'message' => $message,
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
