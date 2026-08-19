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
        $multiStudentASibling = $this->makeStudent($classA, 'Anak Ketiga Multi', 'SHARE-MULTI-A2');
        $fatherMulti->students()->attach($multiStudentASibling->id, [
            'relationship' => 'biological',
            'is_primary_contact' => false,
        ]);
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
        $this->assertDatabaseCount('attendance_roster_snapshot_students', 8);

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
        $this->assertSame(2, $classBStats['summary']['total']);
        $this->assertSame(1, $classBStats['guardians']['father']['counts'][Attendance::STATUS_HADIR_FISIK]);
        $this->assertSame(1, $classBStats['guardians']['father']['counts'][Attendance::STATUS_IZIN]);
        $this->assertSame(50.0, $classBStats['guardians']['father']['percentages'][Attendance::STATUS_HADIR_FISIK]);
        $this->assertSame(50.0, $classBStats['guardians']['father']['percentages'][Attendance::STATUS_IZIN]);

        $classAStudent = collect($classAStats['students'])->firstWhere('student_id', $multiStudentA->id);
        $this->assertSame($multiStudentA->name, $classAStudent['name']);
        $this->assertSame($multiStudentA->nis, $classAStudent['nis']);
        $this->assertSame('Izin', $classAStudent['parents']['father']['label']);
        $this->assertSame('Bapak Izin Multi', $classAStudent['parents']['father']['name']);
        $this->assertCount(6, $classAStats['students']);
        $this->assertNotNull(collect($classAStats['students'])->firstWhere('student_id', $multiStudentASibling->id));
        $this->assertSame('Belum terdaftar', collect($classAStats['students'])->firstWhere('student_id', $fatherPhysicalStudent->id)['parents']['mother']['label']);
        $this->assertSame('Menunggu Validasi', collect($classAStats['students'])->firstWhere('student_id', $motherPendingStudent->id)['parents']['mother']['label']);
        $this->assertSame('Alfa', collect($classAStats['students'])->firstWhere('student_id', $fatherMissing->students()->first()->id)['parents']['father']['label']);

        $classBStudent = collect($classBStats['students'])->firstWhere('student_id', $multiStudentB->id);
        $this->assertSame($multiStudentB->name, $classBStudent['name']);
        $this->assertSame($multiStudentB->nis, $classBStudent['nis']);
        $this->assertSame('Izin', $classBStudent['parents']['father']['label']);
        $this->assertSame($classB->name, $classBStudent['class_name']);

        $snapshot = AttendanceRosterSnapshot::where('kajian_event_id', $event->id)
            ->where('parent_id', $fatherMulti->id)
            ->firstOrFail();
        $this->assertSame($classA->id, $snapshot->class_id);
        $this->assertDatabaseHas('attendance_roster_snapshot_students', [
            'attendance_roster_snapshot_id' => $snapshot->id,
            'student_id' => $multiStudentA->id,
            'class_id' => $classA->id,
            'student_nis' => $multiStudentA->nis,
        ]);

        Livewire::test(\App\Livewire\Admin\ReportIndex::class)
            ->set('academicYearId', (string) $year->id)
            ->set('kajianId', (string) $event->id)
            ->set('shareClassKey', 'class-'.$classA->id)
            ->assertSee('Detail peserta didik')
            ->assertSee('Nama Anak')
            ->assertSee($multiStudentA->nis)
            ->assertSee('Bapak Izin Multi')
            ->assertSee('Ibu');
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
