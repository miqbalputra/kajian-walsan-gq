<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\ParentQrCode;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\ParentQrCodeService;
use App\Services\StudentArchiveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentArchiveServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_archiving_preserves_relations_attendance_and_qr_while_archiving_parent_when_last_child_leaves(): void
    {
        $admin = $this->makeUser('admin');
        $class = ClassRoom::create(['name' => 'Mustawa 1 Arsip', 'level' => '1', 'is_active' => true]);
        $student = Student::create([
            'nis' => 'ARCH-001',
            'name' => 'Santri Arsip',
            'class_id' => $class->id,
            'gender' => 'L',
            'is_active' => true,
            'student_status' => 'active',
        ]);
        $parent = $this->makeParent('Ayah Arsip');
        $parent->students()->attach($student->id, ['relationship' => 'biological', 'is_primary_contact' => true]);
        app(ParentQrCodeService::class)->syncForParent($parent->fresh());
        $canonicalQr = $parent->fresh()->qr_code_string;
        $alias = ParentQrCode::where('parent_id', $parent->id)->where('kind', 'child_alias')->firstOrFail();

        $event = KajianEvent::create([
            'academic_year_id' => AcademicYear::active()->id,
            'title' => 'Kajian Arsip',
            'date' => '2026-08-15',
            'time_start' => '08:00',
            'time_end' => '09:00',
            'status' => 'closed',
        ]);
        $attendance = Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'status' => 'hadir_fisik',
            'method' => 'scan_qr',
            'validation_status' => 'approved',
        ]);

        $record = app(StudentArchiveService::class)->archive($student, [
            'exit_type' => 'transferred',
            'effective_date' => '2026-08-15',
            'reason' => 'Pindah domisili',
            'destination' => 'Sekolah Baru',
        ], $admin);

        $this->assertSame('transferred', $record->exit_type);
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'student_status' => 'transferred',
            'is_active' => 0,
            'class_id' => null,
        ]);
        $this->assertDatabaseHas('student_exit_records', [
            'student_id' => $student->id,
            'exit_type' => 'transferred',
            'destination' => 'Sekolah Baru',
        ]);
        $this->assertDatabaseHas('parent_student', ['parent_id' => $parent->id, 'student_id' => $student->id]);
        $this->assertDatabaseHas('attendances', ['id' => $attendance->id, 'student_id' => $student->id]);
        $this->assertDatabaseHas('parent_qr_codes', ['parent_id' => $parent->id, 'code' => $alias->code]);
        $this->assertSame($canonicalQr, $parent->fresh()->qr_code_string);
        $this->assertFalse($parent->fresh()->user->is_active);
        $this->assertDatabaseHas('parent_archive_records', [
            'parent_id' => $parent->id,
            'login_disabled' => 1,
        ]);
        $this->assertSame(0, ParentModel::guardians()->withActiveChild()->count());
        $this->assertSame(1, ParentModel::archivedGuardians()->count());
    }

    public function test_parent_with_active_sibling_remains_active_and_restore_is_idempotent(): void
    {
        $admin = $this->makeUser('admin-sibling');
        $class = ClassRoom::create(['name' => 'Mustawa 2 Arsip', 'level' => '2', 'is_active' => true]);
        $first = Student::create(['nis' => 'ARCH-002', 'name' => 'Anak Pertama', 'class_id' => $class->id, 'gender' => 'L', 'is_active' => true, 'student_status' => 'active']);
        $second = Student::create(['nis' => 'ARCH-003', 'name' => 'Anak Kedua', 'class_id' => $class->id, 'gender' => 'P', 'is_active' => true, 'student_status' => 'active']);
        $parent = $this->makeParent('Ayah Saudara');
        $parent->students()->attach([$first->id => ['relationship' => 'biological'], $second->id => ['relationship' => 'biological']]);

        $service = app(StudentArchiveService::class);
        $service->archive($first, ['exit_type' => 'withdrawn', 'effective_date' => '2026-08-15'], $admin);

        $this->assertTrue($parent->fresh()->user->is_active);
        $this->assertDatabaseMissing('parent_archive_records', ['parent_id' => $parent->id]);
        $this->assertSame(1, ParentModel::guardians()->withActiveChild()->count());

        $service->archive($second, ['exit_type' => 'withdrawn', 'effective_date' => '2026-08-16'], $admin);
        $this->assertFalse($parent->fresh()->user->is_active);
        $this->assertDatabaseHas('parent_archive_records', ['parent_id' => $parent->id]);

        $service->restore($first, $class->id, AcademicYear::active()->id, $admin, 'Kembali bersekolah');
        $service->restore($first, $class->id, AcademicYear::active()->id, $admin, 'Pemulihan ulang tidak boleh menggandakan enrollment');

        $this->assertTrue($parent->fresh()->user->is_active);
        $this->assertDatabaseHas('students', ['id' => $first->id, 'student_status' => 'active', 'is_active' => 1, 'class_id' => $class->id]);
        $this->assertSame(1, StudentEnrollment::where('student_id', $first->id)->where('academic_year_id', AcademicYear::active()->id)->count());
        $this->assertDatabaseHas('student_exit_records', ['student_id' => $first->id]);
    }

    public function test_teacher_wali_is_not_auto_disabled_when_last_child_is_archived(): void
    {
        $admin = $this->makeUser('admin-teacher-wali');
        $class = ClassRoom::create(['name' => 'Mustawa 3 Arsip', 'level' => '3', 'is_active' => true]);
        $student = Student::create(['nis' => 'ARCH-004', 'name' => 'Anak Guru', 'class_id' => $class->id, 'gender' => 'L', 'is_active' => true, 'student_status' => 'active']);
        $parent = $this->makeParent('Guru dan Wali', true);
        $parent->students()->attach($student->id, ['relationship' => 'biological']);

        app(StudentArchiveService::class)->archive($student, ['exit_type' => 'withdrawn'], $admin);

        $this->assertTrue($parent->fresh()->user->is_active);
        $this->assertDatabaseMissing('parent_archive_records', ['parent_id' => $parent->id]);
    }

    private function makeUser(string $suffix): User
    {
        return User::create([
            'name' => 'User '.$suffix,
            'username' => $suffix,
            'email' => $suffix.'@example.test',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'admin')->value('id'),
            'is_active' => true,
        ]);
    }

    private function makeParent(string $name, bool $isTeacher = false): ParentModel
    {
        $suffix = strtolower(str_replace(' ', '-', $name));
        $user = User::create([
            'name' => $name,
            'username' => $suffix,
            'email' => $suffix.'@example.test',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'wali_santri')->value('id'),
            'is_active' => true,
        ]);

        return ParentModel::create([
            'user_id' => $user->id,
            'type' => 'father',
            'is_teacher' => $isTeacher,
        ]);
    }
}
