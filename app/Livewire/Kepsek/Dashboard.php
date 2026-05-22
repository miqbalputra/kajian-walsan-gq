<?php

namespace App\Livewire\Kepsek;

use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Services\TeacherAttendanceReportService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.kepsek')]
#[Title('Dashboard Kepala Sekolah')]
class Dashboard extends Component
{
    public function render(TeacherAttendanceReportService $teacherReports)
    {
        $latestKajian = KajianEvent::whereDate('date', '<=', today())
            ->orderByDesc('date')
            ->orderByDesc('time_start')
            ->first();

        $latestRows = $teacherReports->rowsForEvent($latestKajian);

        return view('livewire.kepsek.dashboard', [
            'latestKajian' => $latestKajian,
            'summary' => $teacherReports->summary($latestRows),
            'totalGuru' => $teacherReports->teachers()->count(),
            'kajianBulanIni' => KajianEvent::whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            'pendingUploads' => Attendance::query()
                ->where('validation_status', Attendance::VALIDATION_PENDING)
                ->whereNotNull('proof_file')
                ->whereHas('parent', function ($query) {
                    $query->where('type', 'teacher')
                        ->orWhere('is_teacher', true);
                })
                ->count(),
        ]);
    }
}
