<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardAttendanceTrendTest extends TestCase
{
    use RefreshDatabase;

    public function test_dual_role_fathers_and_mothers_appear_once_in_each_trend(): void
    {
        $event = $this->event();
        $guardian = $this->participant('father');
        $teacher = $this->participant('teacher');
        $fatherTeacher = $this->participant('father', isTeacher: true);
        $motherTeacher = $this->participant('mother', isTeacher: true);

        // More than one child must not multiply a teacher/guardian's attendance.
        $fatherTeacher->students()->attach(Student::create([
            'name' => 'Saudara Santri',
            'nis' => 'TREND-SIBLING',
            'is_active' => true,
            'student_status' => 'active',
        ])->id);

        $this->attendance($event, $guardian, Attendance::STATUS_HADIR_FISIK);
        $this->attendance($event, $teacher, Attendance::STATUS_IZIN);
        $this->attendance($event, $fatherTeacher, Attendance::STATUS_HADIR_FISIK);
        $this->attendance($event, $motherTeacher, Attendance::STATUS_HADIR_ONLINE);

        Livewire::test(Dashboard::class)
            ->assertSee('Tren Kehadiran Orang Tua')
            ->assertSee('Tren Kehadiran Guru')
            ->assertViewHas('attendanceTrendData', $this->trend($event, 2, 1, 0, 0))
            ->assertViewHas('teacherAttendanceTrendData', $this->trend($event, 1, 1, 1, 0));
    }

    public function test_teacher_alpha_is_only_counted_after_closure_without_double_counting_stored_alpha(): void
    {
        $closed = $this->event();
        $open = $this->event('open', daysAgo: 0);
        $storedAlpha = $this->participant('teacher');
        $this->participant('teacher');
        $deleted = $this->participant('teacher');
        $pending = $this->participant('teacher');
        $rejected = $this->participant('teacher');

        $this->attendance($closed, $storedAlpha, Attendance::STATUS_ALPHA);
        $this->attendance($closed, $deleted, Attendance::STATUS_HADIR_FISIK)->delete();
        $this->attendance($closed, $pending, Attendance::STATUS_HADIR_ONLINE, Attendance::VALIDATION_PENDING);
        $this->attendance($closed, $rejected, Attendance::STATUS_IZIN, Attendance::VALIDATION_REJECTED);
        $this->attendance($open, $storedAlpha, Attendance::STATUS_ALPHA);
        $this->attendance($open, $pending, Attendance::STATUS_HADIR_ONLINE, Attendance::VALIDATION_PENDING);

        Livewire::test(Dashboard::class)
            ->assertViewHas('teacherAttendanceTrendData', [
                'labels' => [$closed->date->translatedFormat('d M'), $open->date->translatedFormat('d M')],
                'hadirFisik' => [0, 0],
                'hadirOnline' => [1, 1],
                'izin' => [1, 0],
                'alpha' => [3, 0],
            ]);
    }

    public function test_both_trends_use_the_same_six_recent_events_in_date_order(): void
    {
        $this->participant('teacher');
        $events = collect();
        foreach (range(8, 1) as $daysAgo) {
            $events->push($this->event(daysAgo: $daysAgo));
        }
        $this->event('scheduled', daysAgo: -1);

        $labels = $events->slice(-6)->map(fn ($event) => $event->date->translatedFormat('d M'))->values()->all();

        Livewire::test(Dashboard::class)
            ->assertViewHas('attendanceTrendData', fn ($data) => $data['labels'] === $labels)
            ->assertViewHas('teacherAttendanceTrendData', fn ($data) => $data['labels'] === $labels
                && $data['alpha'] === [1, 1, 1, 1, 1, 1]);
    }

    public function test_poll_refreshes_both_trends_from_the_same_updated_attendance(): void
    {
        $event = $this->event();
        $guardianTeacher = $this->participant('father', isTeacher: true);
        $teacher = $this->participant('teacher');
        $attendance = $this->attendance($event, $guardianTeacher, Attendance::STATUS_HADIR_FISIK);

        $component = Livewire::test(Dashboard::class)
            ->assertViewHas('attendanceTrendData', $this->trend($event, 1, 0, 0, 0))
            ->assertViewHas('teacherAttendanceTrendData', $this->trend($event, 1, 0, 0, 1));

        $attendance->update(['status' => Attendance::STATUS_HADIR_ONLINE]);
        $this->attendance($event, $teacher, Attendance::STATUS_IZIN);

        $component->call('refreshDashboard')
            ->assertViewHas('attendanceTrendData', $this->trend($event, 0, 1, 0, 0))
            ->assertViewHas('teacherAttendanceTrendData', $this->trend($event, 0, 1, 1, 0))
            ->assertDispatched('dashboard-charts-updated', function ($name, $params) use ($event) {
                return $params['trendData'] === $this->trend($event, 0, 1, 0, 0)
                    && $params['teacherTrendData'] === $this->trend($event, 0, 1, 1, 0);
            });
    }

    public function test_both_trends_are_empty_when_there_are_no_events(): void
    {
        $empty = ['labels' => [], 'hadirFisik' => [], 'hadirOnline' => [], 'izin' => [], 'alpha' => []];

        Livewire::test(Dashboard::class)
            ->assertViewHas('attendanceTrendData', $empty)
            ->assertViewHas('teacherAttendanceTrendData', $empty);
    }

    private function event(string $status = 'closed', int $daysAgo = 1): KajianEvent
    {
        return KajianEvent::create([
            'academic_year_id' => AcademicYear::active()->id,
            'title' => 'Kajian Tren '.$daysAgo,
            'date' => today()->subDays($daysAgo),
            'time_start' => '08:00',
            'time_end' => '10:00',
            'status' => $status,
            'category' => 'kajian',
        ]);
    }

    private function participant(string $type, bool $isTeacher = false): ParentModel
    {
        $user = User::factory()->create();
        // The legacy teacher enum migration only alters MySQL. Match the
        // existing teacher fixtures while keeping SQLite checks otherwise on.
        $sqliteTeacher = $type === 'teacher' && DB::getDriverName() === 'sqlite';
        if ($sqliteTeacher) {
            DB::statement('PRAGMA ignore_check_constraints = ON');
        }
        try {
            $parent = ParentModel::create([
                'user_id' => $user->id,
                'type' => $type,
                'is_teacher' => $isTeacher,
                'qr_code_string' => 'TREND-'.$user->id,
            ]);
        } finally {
            if ($sqliteTeacher) {
                DB::statement('PRAGMA ignore_check_constraints = OFF');
            }
        }

        if ($parent->isGuardian()) {
            $parent->students()->attach(Student::create([
                'name' => 'Santri '.$user->id,
                'nis' => 'TREND-'.$user->id,
                'is_active' => true,
                'student_status' => 'active',
            ])->id);
        }

        return $parent;
    }

    private function attendance(KajianEvent $event, ParentModel $parent, string $status, string $validation = Attendance::VALIDATION_APPROVED): Attendance
    {
        return Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $parent->id,
            'status' => $status,
            'method' => Attendance::METHOD_MANUAL,
            'validation_status' => $validation,
        ]);
    }

    private function trend(KajianEvent $event, int $physical, int $online, int $izin, int $alpha): array
    {
        return [
            'labels' => [$event->date->translatedFormat('d M')],
            'hadirFisik' => [$physical],
            'hadirOnline' => [$online],
            'izin' => [$izin],
            'alpha' => [$alpha],
        ];
    }
}
