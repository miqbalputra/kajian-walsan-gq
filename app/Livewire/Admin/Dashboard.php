<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // Get stats
        $totalKajian = KajianEvent::count();
        $totalSiswa = Student::where('is_active', true)->count();
        $totalWaliSantri = ParentModel::count();
        $pendingValidation = Attendance::where('validation_status', 'pending')->count();

        // Get last event attendance percentage
        $lastEvent = KajianEvent::latest('date')->first();
        $lastEventAttendance = 0;

        if ($lastEvent) {
            $totalParents = ParentModel::count();
            $attendedCount = Attendance::where('kajian_event_id', $lastEvent->id)
                ->whereIn('status', ['hadir_fisik', 'hadir_online'])
                ->where('validation_status', 'approved')
                ->count();

            $lastEventAttendance = $totalParents > 0
                ? round(($attendedCount / $totalParents) * 100)
                : 0;
        }

        // Recent events
        $recentEvents = KajianEvent::with('academicYear')
            ->latest('date')
            ->take(5)
            ->get();

        // Recent attendance
        $recentAttendance = Attendance::with(['parent.user', 'kajianEvent'])
            ->latest()
            ->take(10)
            ->get();

        // ========== CHART DATA ==========

        // 1. Attendance Trend (Last 6 events)
        $attendanceTrendData = $this->getAttendanceTrendData();

        // 2. Attendance by Status (Pie Chart)
        $attendanceByStatus = $this->getAttendanceByStatus();

        // 3. Attendance by Class (Bar Chart)
        $attendanceByClass = $this->getAttendanceByClass();

        // 4. Monthly Comparison (Current Year)
        $monthlyComparison = $this->getMonthlyComparison();

        // 5. Top Attending Classes
        $topClasses = $this->getTopClasses();

        // 6. Parents Needing Attention (Multiple Alphas)
        $parentsNeedingAttention = $this->getParentsNeedingAttention();

        return view('livewire.admin.dashboard', [
            'totalKajian' => $totalKajian,
            'totalSiswa' => $totalSiswa,
            'totalWaliSantri' => $totalWaliSantri,
            'pendingValidation' => $pendingValidation,
            'lastEventAttendance' => $lastEventAttendance,
            'lastEvent' => $lastEvent,
            'recentEvents' => $recentEvents,
            'recentAttendance' => $recentAttendance,
            // Chart Data
            'attendanceTrendData' => $attendanceTrendData,
            'attendanceByStatus' => $attendanceByStatus,
            'attendanceByClass' => $attendanceByClass,
            'monthlyComparison' => $monthlyComparison,
            'topClasses' => $topClasses,
            'parentsNeedingAttention' => $parentsNeedingAttention,
        ])->layout('components.layouts.admin', ['title' => 'Dashboard']);
    }

    public $showFollowUpModal = false;
    public $selectedParent = null;
    public $followUpReason = '';

    public function openFollowUpModal($parentId)
    {
        $this->selectedParent = ParentModel::with('user')->find($parentId);
        $this->followUpReason = '';
        $this->showFollowUpModal = true;
    }

    public function submitFollowUp()
    {
        $this->validate([
            'followUpReason' => 'required|string|max:255',
        ]);

        $lastEvent = KajianEvent::where('date', '<=', today())->orderByDesc('date')->first();

        if ($lastEvent && $this->selectedParent) {
            // Create or update alpha record with reason
            Attendance::updateOrCreate(
                [
                    'kajian_event_id' => $lastEvent->id,
                    'parent_id' => $this->selectedParent->id,
                ],
                [
                    'status' => 'alpha',
                    'method' => 'manual',
                    'notes' => '[Follow-up Admin]: ' . $this->followUpReason,
                    'validation_status' => 'approved',
                    'validated_by' => auth()->id(),
                    'validated_at' => now(),
                ]
            );

            $this->showFollowUpModal = false;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Hasil follow-up berhasil dicatat.']);
        }
    }

    /**
     * Get attendance trend for the last 6 kajian events
     */
    private function getAttendanceTrendData(): array
    {
        $events = KajianEvent::where(function ($q) {
            $q->where('status', 'closed')
                ->orWhere('date', '<=', now()->toDateString());
        })
            ->orderByDesc('date')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $labels = [];
        $hadirFisik = [];
        $hadirOnline = [];
        $izin = [];
        $alpha = [];

        $totalParents = ParentModel::count();

        foreach ($events as $event) {
            $labels[] = $event->date->translatedFormat('d M');

            // Count only approved attendances for the counts
            $attendances = Attendance::where('kajian_event_id', $event->id)
                ->where('validation_status', 'approved')
                ->get();

            $hadirFisikCount = $attendances->where('status', 'hadir_fisik')->count();
            $hadirOnlineCount = $attendances->where('status', 'hadir_online')->count();
            $izinCount = $attendances->where('status', 'izin')->count();

            // Alpha: only count if event is closed or time is passed
            $isTimePassed = $event->status === 'closed' ||
                $event->date->lt(now()->toDateString()) ||
                ($event->date->equalTo(now()->toDateString()) && Carbon::parse($event->time_end)->lt(now()));

            $alphaCount = 0;
            if ($isTimePassed) {
                $totalAttended = $hadirFisikCount + $hadirOnlineCount + $izinCount;
                $alphaCount = max(0, $totalParents - $totalAttended);
            }

            $hadirFisik[] = $hadirFisikCount;
            $hadirOnline[] = $hadirOnlineCount;
            $izin[] = $izinCount;
            $alpha[] = $alphaCount;
        }

        return [
            'labels' => $labels,
            'hadirFisik' => $hadirFisik,
            'hadirOnline' => $hadirOnline,
            'izin' => $izin,
            'alpha' => $alpha,
        ];
    }

    /**
     * Get attendance distribution by status (for pie chart)
     */
    private function getAttendanceByStatus(): array
    {
        // 1. Get counts for recorded attendances
        $stats = Attendance::where('validation_status', 'approved')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // 2. Calculate cumulative Alpha for all past/closed events
        $totalParents = ParentModel::count();

        $pastEvents = KajianEvent::where('status', 'closed')
            ->orWhere(function ($q) {
                $q->where('date', '<', now()->toDateString())
                    ->orWhere(function ($sq) {
                        $sq->where('date', '=', now()->toDateString())
                            ->where('time_end', '<', now()->toTimeString());
                    });
            })
            ->get();

        $totalPossible = $totalParents * $pastEvents->count();

        // Sum of all approved attendances in those past events
        $totalAttendedInPast = Attendance::whereIn('kajian_event_id', $pastEvents->pluck('id'))
            ->where('validation_status', 'approved')
            ->count();

        $alphaCount = max(0, $totalPossible - $totalAttendedInPast);

        return [
            'hadirFisik' => $stats['hadir_fisik'] ?? 0,
            'hadirOnline' => $stats['hadir_online'] ?? 0,
            'izin' => $stats['izin'] ?? 0,
            'alpha' => $alphaCount,
        ];
    }

    /**
     * Get attendance by class (for horizontal bar chart)
     */
    private function getAttendanceByClass(): array
    {
        $classes = ClassRoom::where('is_active', true)
            ->withCount(['students' => fn($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $labels = [];
        $percentages = [];
        $colors = [];

        $totalKajian = KajianEvent::count();
        $colorPalette = ['#10B981', '#3B82F6', '#8B5CF6', '#F59E0B', '#EF4444', '#EC4899', '#14B8A6', '#6366F1'];

        foreach ($classes as $index => $class) {
            if ($class->students_count === 0)
                continue;

            $labels[] = $class->name;

            // Get total possible attendances (students * kajian events)
            $totalPossible = $class->students_count * max(1, $totalKajian);

            // Get actual attendances for students in this class
            $studentIds = $class->students()->pluck('id');
            $parentIds = DB::table('parent_student')
                ->whereIn('student_id', $studentIds)
                ->pluck('parent_id')
                ->unique();

            $actualAttendance = Attendance::whereIn('parent_id', $parentIds)
                ->whereIn('status', ['hadir_fisik', 'hadir_online'])
                ->where('validation_status', 'approved')
                ->count();

            $percentage = $totalPossible > 0
                ? round(($actualAttendance / $totalPossible) * 100)
                : 0;

            $percentages[] = min(100, $percentage);
            $colors[] = $colorPalette[$index % count($colorPalette)];
        }

        return [
            'labels' => $labels,
            'data' => $percentages,
            'colors' => $colors,
        ];
    }

    /**
     * Get monthly attendance comparison for current year
     */
    private function getMonthlyComparison(): array
    {
        $year = Carbon::now()->year;
        $months = [];
        $attendanceData = [];
        $targetData = [];

        $totalParents = ParentModel::count();

        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($year, $month, 1)->translatedFormat('M');
            $months[] = $monthName;

            // Count kajian events in this month
            $eventsInMonth = KajianEvent::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->count();

            // Total possible attendances
            $totalPossible = $totalParents * $eventsInMonth;

            // Actual attendances
            $actualAttendance = Attendance::whereHas('kajianEvent', function ($q) use ($year, $month) {
                $q->whereYear('date', $year)->whereMonth('date', $month);
            })
                ->whereIn('status', ['hadir_fisik', 'hadir_online'])
                ->where('validation_status', 'approved')
                ->count();

            $percentage = $totalPossible > 0
                ? round(($actualAttendance / $totalPossible) * 100)
                : 0;

            $attendanceData[] = $percentage;
            $targetData[] = 80; // Target 80%
        }

        return [
            'months' => $months,
            'attendance' => $attendanceData,
            'target' => $targetData,
        ];
    }

    /**
     * Get top 5 classes by attendance rate
     */
    private function getTopClasses(): array
    {
        $classes = ClassRoom::where('is_active', true)
            ->withCount(['students' => fn($q) => $q->where('is_active', true)])
            ->get();

        $classStats = [];
        $totalKajian = KajianEvent::count();

        foreach ($classes as $class) {
            if ($class->students_count === 0)
                continue;

            $studentIds = $class->students()->pluck('id');
            $parentIds = DB::table('parent_student')
                ->whereIn('student_id', $studentIds)
                ->pluck('parent_id')
                ->unique();

            $totalPossible = $class->students_count * max(1, $totalKajian);
            $actualAttendance = Attendance::whereIn('parent_id', $parentIds)
                ->whereIn('status', ['hadir_fisik', 'hadir_online'])
                ->where('validation_status', 'approved')
                ->count();

            $percentage = $totalPossible > 0
                ? round(($actualAttendance / $totalPossible) * 100)
                : 0;

            $classStats[] = [
                'name' => $class->name,
                'percentage' => min(100, $percentage),
                'students' => $class->students_count,
            ];
        }

        // Sort by percentage descending and take top 5
        usort($classStats, fn($a, $b) => $b['percentage'] - $a['percentage']);

        return array_slice($classStats, 0, 5);
    }

    /**
     * Identify parents who missed the last 2 events
     */
    private function getParentsNeedingAttention(): array
    {
        $lastTwoEvents = KajianEvent::where('date', '<=', today())
            ->orderByDesc('date')
            ->take(2)
            ->get();

        if ($lastTwoEvents->count() < 1) {
            return [];
        }

        $eventIds = $lastTwoEvents->pluck('id');
        $eventCount = $lastTwoEvents->count();

        // Find parents who don't have (approved) attendance for these events
        $parents = ParentModel::with(['user', 'students.classRoom'])
            ->whereDoesntHave('attendances', function ($q) use ($eventIds) {
                $q->whereIn('kajian_event_id', $eventIds)
                    ->whereIn('status', ['hadir_fisik', 'hadir_online', 'izin'])
                    ->where('validation_status', 'approved');
            })
            ->take(5)
            ->get();

        return [
            'data' => $parents->toArray(),
            'count' => $eventCount
        ];
    }
}
