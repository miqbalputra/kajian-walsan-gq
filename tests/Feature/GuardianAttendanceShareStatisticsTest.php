<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceRosterSnapshot;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceFinalizationService;
use App\Services\GuardianAttendanceReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuardianAttendanceShareStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_closed_event_share_statistics_are_grouped_by_snapshot_class_and_guardian_type(): void
    {
        $year = AcademicYear::active();
        $classA = ClassRoom::create(['name' => 'Mustawa 3 Ikhwan', 'level' => '3', 'is_active' => true]);
        $classB = ClassRoom::create(['name' => 'Mustawa 4 Ikhwan', 'level' => '4', 'is_active' => true]);
        $event = KajianEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Kajian Statistik Kelas',
            'date' => today()->subDay(),
            'time_start' => '08:00',
            'time_end' => '09:00',
            'status' => 'open',
            'category' => 'kajian',
        ]);

        [$fatherPhysical, $fatherPhysicalStudent] = $this->makeGuardian($classA, 'father', 'Fisik A', 'SHARE-FA-1');
        [$fatherMissing] = $this->makeGuardian($classA, 'father', 'Alfa A', 'SHARE-FA-2');
        [$fatherMulti, $multiStudentA] = $this->makeGuardian($classA, 'father', 'Izin Multi', 'SHARE-FA-3');
        $multiStudentB = $this->makeStudent($classB, 'Anak Kedua Multi', 'SHARE-MULTI-B');
        $fatherMulti->students()->attach($multiStudentB->id, [
            'relationship' => 'biological',
            'is_primary_contact' => false,
        ]);
        [$motherOnline, $motherOnlineStudent] = $this->makeGuardian($classA, 'mother', 'Online A', 'SHARE-IA-1');
        [$motherPending, $motherPendingStudent] = $this->makeGuardian($classA, 'mother', 'Pending A', 'SHARE-IA-2');
        [$fatherPhysicalB, $fatherPhysicalBStudent] = $this->makeGuardian($classB, 'father', 'Fisik B', 'SHARE-FB-1');

        $this->attendance($event, $fatherPhysical, $fatherPhysicalStudent, Attendance::STATUS_HADIR_FISIK);
        $this->attendance($event, $fatherMulti, $multiStudentA, Attendance::STATUS_IZIN);
        $this->attendance($event, $motherOnline, $motherOnlineStudent, Attendance::STATUS_HADIR_ONLINE);
        $this->attendance($event, $motherPending, $motherPendingStudent, Attendance::STATUS_HADIR_ONLINE, Attendance::VALIDATION_PENDING);
        $this->attendance($event, $fatherPhysicalB, $fatherPhysicalBStudent, Attendance::STATUS_HADIR_FISIK);

        app(AttendanceFinalizationService::class)->close($event);
        $this->assertDatabaseHas('attendance_roster_snapshots', [
            'kajian_event_id' => $event->id,
            'parent_id' => $fatherMulti->id,
            'class_id' => $classA->id,
        ]);

        // A later class change must not move an already final attendance
        // result away from the roster class captured at closure.
        $multiStudentA->update(['class_id' => $classB->id]);

        $statistics = app(GuardianAttendanceReportService::class)
            ->shareableClassStatistics($event->fresh());

        $this->assertNotNull($statistics);
        $this->assertSame(6, $statistics['summary']['total']);

        $classAStats = collect($statistics['classes'])->firstWhere('id', $classA->id);
        $this->assertSame(5, $classAStats['summary']['total']);
        $this->assertSame(3, $classAStats['guardians']['father']['total']);
        $this->assertSame(1, $classAStats['guardians']['father']['counts'][Attendance::STATUS_HADIR_FISIK]);
        $this->assertSame(1, $classAStats['guardians']['father']['counts'][Attendance::STATUS_IZIN]);
        $this->assertSame(1, $classAStats['guardians']['father']['counts'][Attendance::STATUS_ALPHA]);
        $this->assertEquals(33.3, $classAStats['guardians']['father']['percentages'][Attendance::STATUS_ALPHA]);
        $this->assertSame(2, $classAStats['guardians']['mother']['total']);
        $this->assertSame(1, $classAStats['guardians']['mother']['counts'][Attendance::STATUS_HADIR_ONLINE]);
        $this->assertSame(1, $classAStats['guardians']['mother']['counts']['perlu_validasi']);
        $this->assertSame(0, $classAStats['guardians']['mother']['counts'][Attendance::STATUS_ALPHA]);

        $classBStats = collect($statistics['classes'])->firstWhere('id', $classB->id);
        $this->assertSame(1, $classBStats['summary']['total']);
        $this->assertSame(1, $classBStats['guardians']['father']['counts'][Attendance::STATUS_HADIR_FISIK]);
        $this->assertSame(100.0, $classBStats['guardians']['father']['percentages'][Attendance::STATUS_HADIR_FISIK]);

        $snapshot = AttendanceRosterSnapshot::where('kajian_event_id', $event->id)
            ->where('parent_id', $fatherMulti->id)
            ->firstOrFail();
        $this->assertSame($classA->id, $snapshot->class_id);
    }

    public function test_share_statistics_are_unavailable_for_an_open_event(): void
    {
        $year = AcademicYear::active();
        $event = KajianEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Kajian Masih Dibuka',
            'date' => today(),
            'time_start' => '08:00',
            'time_end' => '09:00',
            'status' => 'open',
            'category' => 'kajian',
        ]);

        $this->assertNull(app(GuardianAttendanceReportService::class)->shareableClassStatistics($event));

        Livewire::test(\App\Livewire\Admin\ReportIndex::class)
            ->set('academicYearId', (string) $year->id)
            ->set('kajianId', (string) $event->id)
            ->assertSee('Statistik gambar belum final');
    }

    private function attendance(
        KajianEvent $event,
        ParentModel $parent,
        Student $student,
        string $status,
        string $validationStatus = Attendance::VALIDATION_APPROVED
    ): void {
        Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'status' => $status,
            'method' => Attendance::METHOD_MANUAL,
            'validation_status' => $validationStatus,
        ]);
    }

    private function makeGuardian(ClassRoom $class, string $type, string $name, string $suffix): array
    {
        $user = User::factory()->create([
            'name' => $type === 'father' ? 'Bapak '.$name : 'Ibu '.$name,
            'username' => 'wali-'.$suffix,
            'email' => 'wali-'.$suffix.'@example.test',
        ]);
        $parent = ParentModel::create([
            'user_id' => $user->id,
            'type' => $type,
            'qr_code_string' => 'P-'.$suffix,
        ]);
        $student = $this->makeStudent($class, 'Santri '.$name, $suffix);
        $parent->students()->attach($student->id, [
            'relationship' => 'biological',
            'is_primary_contact' => true,
        ]);

        return [$parent, $student];
    }

    private function makeStudent(ClassRoom $class, string $name, string $suffix): Student
    {
        return Student::create([
            'nis' => 'NIS-'.$suffix,
            'name' => $name,
            'class_id' => $class->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
    }
}
