<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $dashboard = Cache::remember('admin-dashboard:summary', now()->addSeconds(15), function () {
            $totalKajian = KajianEvent::count();
            $totalSiswa = Student::active()->count();
            $totalWaliSantri = ParentModel::guardians()->count();
            $pendingValidation = Attendance::where('validation_status', 'pending')->count();

            // Only completed/current events should be used as the latest
            // attendance reference; a future event must not show 0%.
            $lastEvent = KajianEvent::where('date', '<=', today())
                ->orderByDesc('date')
                ->orderByDesc('time_start')
                ->orderByDesc('id')
                ->first();
            $lastEventAttendance = 0;

            if ($lastEvent) {
                $totalParents = ParentModel::guardians()->targetedByEvent($lastEvent)->count();
                $attendedCount = Attendance::where('kajian_event_id', $lastEvent->id)
                    ->whereIn('status', ['hadir_fisik', 'hadir_online'])
                    ->where('validation_status', 'approved')
                    ->count();

                $lastEventAttendance = $totalParents > 0
                    ? min(100, round(($attendedCount / $totalParents) * 100))
                    : 0;
            }

            return [
                'totalKajian' => $totalKajian,
                'totalSiswa' => $totalSiswa,
                'totalWaliSantri' => $totalWaliSantri,
                'pendingValidation' => $pendingValidation,
                'lastEventAttendance' => $lastEventAttendance,
                'lastEvent' => $lastEvent,
                'recentEvents' => KajianEvent::with('academicYear')->latest('date')->take(5)->get(),
                'recentAttendance' => Attendance::with(['parent.user', 'kajianEvent'])->latest()->take(10)->get(),
                'attendanceTrendData' => $this->getAttendanceTrendData(),
                'attendanceByStatus' => $this->getAttendanceByStatus(),
                'attendanceByClass' => $this->getAttendanceByClass(),
                'monthlyComparison' => $this->getMonthlyComparison(),
                'topClasses' => $this->getTopClasses(),
                'parentsNeedingAttention' => $this->getParentsNeedingAttention(),
            ];
        });

        return view('livewire.admin.dashboard', $dashboard)
            ->layout('components.layouts.admin', ['title' => 'Dashboard']);
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

            Cache::forget('admin-dashboard:summary');
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
            ->with('targetClasses')
            ->orderByDesc('date')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $attendanceByEvent = Attendance::whereIn('kajian_event_id', $events->pluck('id'))
            ->where('validation_status', 'approved')
            ->select('kajian_event_id', 'status', DB::raw('count(*) as total'))
            ->groupBy('kajian_event_id', 'status')
            ->get()
            ->groupBy('kajian_event_id');

        $labels = [];
        $hadirFisik = [];
        $hadirOnline = [];
        $izin = [];
        $alpha = [];

        foreach ($events as $event) {
            $labels[] = $event->date->translatedFormat('d M');

            $eventStats = $attendanceByEvent->get($event->id, collect())->keyBy('status');
            $hadirFisikCount = (int) ($eventStats->get('hadir_fisik')->total ?? 0);
            $hadirOnlineCount = (int) ($eventStats->get('hadir_online')->total ?? 0);
            $izinCount = (int) ($eventStats->get('izin')->total ?? 0);

            // Alpha: only count if event is closed or time is passed
            $isTimePassed = $event->status === 'closed' ||
                $event->date->lt(now()->toDateString()) ||
                ($event->date->equalTo(now()->toDateString()) && Carbon::parse($event->time_end)->lt(now()));

            $alphaCount = 0;
            if ($isTimePassed) {
                $totalAttended = $hadirFisikCount + $hadirOnlineCount + $izinCount;
                $alphaCount = max(0, $this->targetedParentCount($event) - $totalAttended);
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
        // Only completed events should contribute to the distribution.
        $pastEvents = $this->completedEvents();
        $pastEventIds = $pastEvents->pluck('id');

        $stats = Attendance::whereIn('kajian_event_id', $pastEventIds)
            ->where('validation_status', 'approved')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // A class-targeted event does not have the same guardian denominator
        // as an all-class event.
        $totalPossible = $pastEvents->sum(fn (KajianEvent $event) => $this->targetedParentCount($event));

        // Sum of all approved attendances in those past events
        $totalAttendedInPast = Attendance::whereIn('kajian_event_id', $pastEventIds)
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
            ->withCount(['students' => fn($q) => $q->active()])
            ->orderBy('name')
            ->get();

        $labels = [];
        $percentages = [];
        $colors = [];

        $completedEvents = $this->completedEvents();
        $colorPalette = ['#10B981', '#3B82F6', '#8B5CF6', '#F59E0B', '#EF4444', '#EC4899', '#14B8A6', '#6366F1'];

        foreach ($classes as $index => $class) {
            if ($class->students_count === 0)
                continue;

            $labels[] = $class->name;

            // Attendance is recorded once per parent, so the denominator must
            // use distinct guardians rather than the number of children.
            $studentIds = $class->students()->active()->pluck('id');
            $parentIds = DB::table('parent_student')
                ->whereIn('student_id', $studentIds)
                ->pluck('parent_id')
                ->unique();

            $eventIdsForClass = $completedEvents
                ->filter(fn (KajianEvent $event) => $event->targetsAllClasses()
                    || $event->targetClassIds()->contains((int) $class->id))
                ->pluck('id');

            $totalPossible = $parentIds->count() * $eventIdsForClass->count();
            $actualAttendance = Attendance::whereIn('kajian_event_id', $eventIdsForClass)
                ->whereIn('parent_id', $parentIds)
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

        $events = KajianEvent::whereYear('date', $year)
            ->where(function ($query) {
                $query->where('status', 'closed')
                    ->orWhere(function ($query) {
                        $query->where('date', '<', today())
                            ->orWhere(function ($query) {
                                $query->whereDate('date', today())
                                    ->where('time_end', '<', now()->toTimeString());
                            });
                    });
            })
            ->with('targetClasses')
            ->get();

        $guardians = ParentModel::guardians()->with('students')->get();
        $attendanceByEvent = Attendance::whereIn('kajian_event_id', $events->pluck('id'))
            ->whereIn('status', ['hadir_fisik', 'hadir_online'])
            ->where('validation_status', 'approved')
            ->get(['kajian_event_id', 'parent_id'])
            ->groupBy('kajian_event_id');

        $monthStats = $events->groupBy(fn (KajianEvent $event) => $event->date->month)
            ->map(function ($monthEvents) use ($guardians, $attendanceByEvent) {
                $possible = 0;
                $attended = 0;

                foreach ($monthEvents as $event) {
                    $possible += $guardians->filter(fn (ParentModel $parent) => $event->targetsParent($parent))->count();
                    $targetedParentIds = $guardians
                        ->filter(fn (ParentModel $parent) => $event->targetsParent($parent))
                        ->pluck('id');
                    $attended += $attendanceByEvent
                        ->get($event->id, collect())
                        ->whereIn('parent_id', $targetedParentIds)
                        ->count();
                }

                return compact('possible', 'attended');
            });

        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($year, $month, 1)->translatedFormat('M');
            $months[] = $monthName;

            $stats = $monthStats->get($month, ['possible' => 0, 'attended' => 0]);
            $totalPossible = $stats['possible'];
            $actualAttendance = $stats['attended'];

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
            ->withCount(['students' => fn($q) => $q->active()])
            ->get();

        $classStats = [];
        $completedEvents = $this->completedEvents();

        foreach ($classes as $class) {
            if ($class->students_count === 0)
                continue;

            $studentIds = $class->students()->active()->pluck('id');
            $parentIds = DB::table('parent_student')
                ->whereIn('student_id', $studentIds)
                ->pluck('parent_id')
                ->unique();

            $eventIdsForClass = $completedEvents
                ->filter(fn (KajianEvent $event) => $event->targetsAllClasses()
                    || $event->targetClassIds()->contains((int) $class->id))
                ->pluck('id');

            $totalPossible = $parentIds->count() * $eventIdsForClass->count();
            $actualAttendance = Attendance::whereIn('kajian_event_id', $eventIdsForClass)
                ->whereIn('parent_id', $parentIds)
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
        $lastTwoEvents = KajianEvent::with('targetClasses')
            ->where('date', '<=', today())
            ->orderByDesc('date')
            ->take(2)
            ->get();

        if ($lastTwoEvents->count() < 1) {
            return [];
        }

        $eventIds = $lastTwoEvents->pluck('id');
        $parents = ParentModel::guardians()
            ->with(['user', 'students.classRoom', 'attendances' => function ($query) use ($eventIds) {
                $query->whereIn('kajian_event_id', $eventIds)
                    ->whereIn('status', ['hadir_fisik', 'hadir_online', 'izin'])
                    ->where('validation_status', 'approved');
            }])
            ->get()
            ->filter(function (ParentModel $parent) use ($lastTwoEvents) {
                $targetedEvents = $lastTwoEvents->filter(fn (KajianEvent $event) => $event->targetsParent($parent));

                return $targetedEvents->isNotEmpty()
                    && $targetedEvents->every(fn (KajianEvent $event) => ! $parent->attendances->contains('kajian_event_id', $event->id));
            })
            ->take(5)
            ->values();

        return [
            'data' => $parents->toArray(),
            'count' => $lastTwoEvents->count()
        ];
    }

    private function targetedParentCount(KajianEvent $event): int
    {
        return ParentModel::guardians()->targetedByEvent($event)->count();
    }

    private function completedEvents()
    {
        return KajianEvent::with('targetClasses')->where(function ($q) {
            $q->where('status', 'closed')
                ->orWhere(function ($sq) {
                    $sq->where('date', '<', now()->toDateString())
                        ->orWhere(function ($ssq) {
                            $ssq->where('date', '=', now()->toDateString())
                                ->where('time_end', '<', now()->toTimeString());
                        });
                });
        })->get();
    }

}
