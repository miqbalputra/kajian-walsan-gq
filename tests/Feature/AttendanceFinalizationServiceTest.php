<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\AttendanceFinalizationService;
use App\Services\GuardianAttendanceReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

class AttendanceFinalizationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_alpha_is_derived_only_after_closing_and_roster_is_frozen(): void
    {
        $year = AcademicYear::active();
        $class = ClassRoom::create(['name' => 'Kelas Final', 'level' => '1', 'is_active' => true]);
        $event = KajianEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Kajian Finalisasi',
            'date' => today(),
            'time_start' => '00:00',
            'time_end' => '00:01',
            'status' => 'open',
            'category' => 'kajian',
        ]);
        [$presentParent, $presentStudent] = $this->makeGuardian($class, 'Hadir', 'FINAL-1');
        [$missingParent] = $this->makeGuardian($class, 'Belum Hadir', 'FINAL-2');

        Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $presentParent->id,
            'student_id' => $presentStudent->id,
            'status' => Attendance::STATUS_HADIR_FISIK,
            'method' => Attendance::METHOD_MANUAL,
            'validation_status' => Attendance::VALIDATION_APPROVED,
        ]);

        $reports = app(GuardianAttendanceReportService::class);
        $this->assertSame(0, $reports->rowsForEvent($event)->where('derived_status', Attendance::STATUS_ALPHA)->count());

        app(AttendanceFinalizationService::class)->close($event);
        $event->refresh();

        $this->assertSame('closed', $event->status);
        $this->assertNotNull($event->closed_at);
        $this->assertDatabaseCount('attendance_roster_snapshots', 2);

        $closedRows = $reports->rowsForEvent($event);
        $this->assertSame(2, $closedRows->count());
        $this->assertSame(1, $closedRows->where('derived_status', Attendance::STATUS_ALPHA)->count());
        $this->assertFalse(Attendance::where('parent_id', $missingParent->id)->where('status', Attendance::STATUS_ALPHA)->exists());

        Livewire::test(\App\Livewire\Admin\ReportIndex::class)
            ->set('academicYearId', (string) $year->id)
            ->set('kajianId', (string) $event->id)
            ->assertSee('Alfa')
            ->assertSee('Wali Belum Hadir');

        // A guardian added after closure cannot alter the final denominator.
        $this->makeGuardian($class, 'Terlambat Ditambah', 'FINAL-3');
        $this->assertSame(2, $reports->rowsForEvent($event)->count());

        app(AttendanceFinalizationService::class)->reopen($event);
        $event->refresh();

        $this->assertSame('open', $event->status);
        $this->assertDatabaseCount('attendance_roster_snapshots', 0);
        $this->assertSame(3, $reports->rowsForEvent($event)->count());
        $this->assertSame(0, $reports->rowsForEvent($event)->where('derived_status', Attendance::STATUS_ALPHA)->count());
    }

    public function test_dashboard_poll_dispatches_fresh_chart_data(): void
    {
        Livewire::test(\App\Livewire\Admin\Dashboard::class)
            ->call('refreshDashboard')
            ->assertDispatched('dashboard-charts-updated');
    }

    public function test_legacy_closed_event_can_be_backfilled_without_changing_its_attendance_or_status(): void
    {
        $year = AcademicYear::active();
        $class = ClassRoom::create(['name' => 'Kelas Lama', 'level' => '2', 'is_active' => true]);
        $event = KajianEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Kajian Lama',
            'date' => today()->subDay(),
            'time_start' => '08:00',
            'time_end' => '09:00',
            'status' => 'closed',
            'category' => 'kajian',
        ]);
        [$presentParent, $presentStudent] = $this->makeGuardian($class, 'Lama Hadir', 'LEGACY-1');
        [$missingParent, $missingStudent] = $this->makeGuardian($class, 'Lama Alfa', 'LEGACY-2');

        foreach ([$presentStudent, $missingStudent] as $student) {
            StudentEnrollment::create([
                'student_id' => $student->id,
                'academic_year_id' => $year->id,
                'class_id' => $class->id,
                'class_name' => $class->name,
                'class_level' => $class->level,
                'status' => 'enrolled',
                'started_at' => $year->start_date,
            ]);
        }

        Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $presentParent->id,
            'student_id' => $presentStudent->id,
            'status' => Attendance::STATUS_HADIR_FISIK,
            'method' => Attendance::METHOD_MANUAL,
            'validation_status' => Attendance::VALIDATION_APPROVED,
        ]);

        $finalization = app(AttendanceFinalizationService::class);
        $this->assertSame(2, $finalization->legacyParticipantCount($event));
        $this->assertSame(2, $finalization->backfillLegacyClosedEvent($event));
        $this->assertSame(1, Attendance::count());
        $this->assertSame('closed', $event->fresh()->status);
        $this->assertNull($event->fresh()->closed_at);
        $this->assertDatabaseCount('attendance_roster_snapshots', 2);
        $this->assertSame(
            1,
            app(GuardianAttendanceReportService::class)
                ->rowsForEvent($event->fresh())
                ->where('guardian_id', $missingParent->id)
                ->where('derived_status', Attendance::STATUS_ALPHA)
                ->count()
        );
    }

    public function test_legacy_backfill_command_is_preview_first(): void
    {
        $year = AcademicYear::active();
        $class = ClassRoom::create(['name' => 'Kelas Pratinjau', 'level' => '3', 'is_active' => true]);
        $event = KajianEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Kajian Pratinjau',
            'date' => today()->subDay(),
            'time_start' => '08:00',
            'time_end' => '09:00',
            'status' => 'closed',
            'category' => 'kajian',
        ]);
        [, $student] = $this->makeGuardian($class, 'Pratinjau', 'PREVIEW-1');
        StudentEnrollment::create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'class_id' => $class->id,
            'class_name' => $class->name,
            'class_level' => $class->level,
            'status' => 'enrolled',
            'started_at' => $year->start_date,
        ]);

        Artisan::call('attendance:backfill-roster-snapshots', ['--event' => [$event->id]]);
        $this->assertDatabaseCount('attendance_roster_snapshots', 0);

        Artisan::call('attendance:backfill-roster-snapshots', [
            '--event' => [$event->id],
            '--apply' => true,
        ]);
        $this->assertDatabaseCount('attendance_roster_snapshots', 1);
    }

    private function makeGuardian(ClassRoom $class, string $name, string $suffix): array
    {
        $user = User::factory()->create([
            'name' => 'Wali '.$name,
            'username' => 'wali-'.$suffix,
            'email' => 'wali-'.$suffix.'@example.test',
        ]);
        $parent = ParentModel::create([
            'user_id' => $user->id,
            'type' => 'father',
            'qr_code_string' => 'P-'.$suffix,
        ]);
        $student = Student::create([
            'nis' => 'NIS-'.$suffix,
            'name' => 'Santri '.$name,
            'class_id' => $class->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
        $parent->students()->attach($student->id, [
            'relationship' => 'biological',
            'is_primary_contact' => true,
        ]);

        return [$parent, $student];
    }
}
