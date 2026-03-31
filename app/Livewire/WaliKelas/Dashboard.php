<?php

namespace App\Livewire\WaliKelas;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    use WithPagination;

    public $filterPeriod = 'monthly';
    public $filterKajian = '';
    public $search = '';

    // Modal State
    public $showDetailModal = false;
    public $selectedStudent = null;

    public function mount()
    {
        $user = auth()->user();
        if (!$user->isWaliKelas() && !$user->isAdmin()) {
            abort(403);
        }
    }

    public function openDetail($id)
    {
        $this->selectedStudent = Student::with(['parents.user', 'attendances.kajianEvent', 'attendances.parent.user'])
            ->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedStudent = null;
    }

    public function render()
    {
        $class = auth()->user()->managedClass;

        if (!$class) {
            return view('livewire.wali-kelas.no-class')
                ->layout('components.layouts.wali-kelas', ['title' => 'Dashboard']);
        }

        $students = Student::where('class_id', $class->id)
            ->with(['parents.user', 'attendances.kajianEvent'])
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        $totalStudents = $class->students()->count();
        $kajianEvents = KajianEvent::orderBy('date', 'desc')->get();

        // Stats
        $stats = [
            'total_students' => $totalStudents,
            'attendance_rate' => $this->calculateAttendanceRate($class->id),
            'top_attendees' => $this->getTopAttendees($class->id),
            'needs_attention' => $this->getNeedsAttention($class->id),
        ];

        return view('livewire.wali-kelas.dashboard', [
            'students' => $students,
            'class' => $class,
            'kajianEvents' => $kajianEvents,
            'stats' => $stats,
        ])->layout('components.layouts.wali-kelas', ['title' => 'Dashboard Wali Kelas']);
    }

    private function calculateAttendanceRate($classId)
    {
        $totalStudents = Student::where('class_id', $classId)->count();
        if ($totalStudents == 0)
            return 0;

        $latestEvent = KajianEvent::where('status', 'closed')->orderBy('date', 'desc')->first();
        if (!$latestEvent)
            return 0;

        $presentCount = Attendance::where('kajian_event_id', $latestEvent->id)
            ->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            })
            ->where('validation_status', 'approved')
            ->count();

        return round(($presentCount / $totalStudents) * 100);
    }

    private function getTopAttendees($classId)
    {
        return Student::where('class_id', $classId)
            ->withCount([
                'attendances' => function ($q) {
                    $q->where('validation_status', 'approved');
                }
            ])
            ->orderBy('attendances_count', 'desc')
            ->take(5)
            ->get();
    }

    private function getNeedsAttention($classId)
    {
        // Students with 0 or few attendances
        return Student::where('class_id', $classId)
            ->withCount([
                'attendances' => function ($q) {
                    $q->where('validation_status', 'approved');
                }
            ])
            ->orderBy('attendances_count', 'asc')
            ->take(5)
            ->get();
    }
}
