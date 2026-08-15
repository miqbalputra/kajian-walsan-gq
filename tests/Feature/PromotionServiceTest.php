<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\PromotionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PromotionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_preserves_source_enrollment_and_activates_target_year(): void
    {
        $sourceYear = AcademicYear::active();
        $targetYear = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => false,
        ]);
        $sourceClass = ClassRoom::create(['name' => 'Kelas 5A', 'level' => '5', 'is_active' => true]);
        $targetClass = ClassRoom::create(['name' => 'Kelas 6A', 'level' => '6', 'is_active' => true]);
        $student = Student::create([
            'nis' => 'PROMO-5001',
            'name' => 'Santri Promosi',
            'class_id' => $sourceClass->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);

        $batch = app(PromotionService::class)->apply(
            $sourceYear,
            $targetYear,
            [$sourceClass->id => $targetClass->id],
            [],
        );

        $this->assertSame('applied', $batch->status);
        $this->assertTrue(AcademicYear::find($targetYear->id)->is_active);
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'class_id' => $targetClass->id,
            'student_status' => 'active',
            'is_active' => 1,
        ]);
        $this->assertDatabaseHas('student_enrollments', [
            'student_id' => $student->id,
            'academic_year_id' => $sourceYear->id,
            'class_id' => $sourceClass->id,
            'status' => 'enrolled',
        ]);
        $this->assertDatabaseHas('student_enrollments', [
            'student_id' => $student->id,
            'academic_year_id' => $targetYear->id,
            'class_id' => $targetClass->id,
            'status' => 'enrolled',
        ]);
    }

    public function test_historical_source_uses_enrollment_class_not_current_student_class(): void
    {
        $sourceYear = AcademicYear::create([
            'name' => '2023/2024',
            'start_date' => '2023-07-01',
            'end_date' => '2024-06-30',
            'is_active' => false,
        ]);
        $targetYear = AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-07-01',
            'end_date' => '2025-06-30',
            'is_active' => false,
        ]);
        $sourceClass = ClassRoom::create(['name' => 'Kelas 5 Historis', 'level' => '5', 'is_active' => true]);
        $targetClass = ClassRoom::create(['name' => 'Kelas 6 Historis', 'level' => '6', 'is_active' => true]);
        $currentClass = ClassRoom::create(['name' => 'Kelas 2 Sekarang', 'level' => '2', 'is_active' => true]);
        $student = Student::create([
            'nis' => 'PROMO-HIST-1',
            'name' => 'Santri Histori',
            'class_id' => $currentClass->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
        StudentEnrollment::create([
            'student_id' => $student->id,
            'academic_year_id' => $sourceYear->id,
            'class_id' => $sourceClass->id,
            'class_name' => $sourceClass->name,
            'class_level' => $sourceClass->level,
            'status' => 'enrolled',
            'started_at' => $sourceYear->start_date,
        ]);

        app(PromotionService::class)->apply(
            $sourceYear,
            $targetYear,
            [$sourceClass->id => $targetClass->id],
            [],
        );

        $this->assertDatabaseHas('student_enrollments', [
            'student_id' => $student->id,
            'academic_year_id' => $targetYear->id,
            'class_id' => $targetClass->id,
        ]);
        $this->assertDatabaseHas('promotion_changes', [
            'student_id' => $student->id,
            'before_class_id' => $sourceClass->id,
            'after_class_id' => $targetClass->id,
        ]);
    }

    public function test_graduating_last_child_archives_parent_login_without_removing_relation(): void
    {
        $sourceYear = AcademicYear::active();
        $targetYear = AcademicYear::create([
            'name' => '2027/2028',
            'start_date' => '2027-07-01',
            'end_date' => '2028-06-30',
            'is_active' => false,
        ]);
        $sourceClass = ClassRoom::create(['name' => 'Mustawa 6 Ikhwan', 'level' => '6', 'is_active' => true]);
        $student = Student::create([
            'nis' => 'PROMO-6001',
            'name' => 'Santri Lulus',
            'class_id' => $sourceClass->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
        $user = User::create([
            'name' => 'Ayah Santri Lulus',
            'username' => 'bpk-promo-6001',
            'email' => 'bpk-promo-6001@example.test',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'wali_santri')->value('id'),
            'is_active' => true,
        ]);
        $parent = ParentModel::create(['user_id' => $user->id, 'type' => 'father']);
        $parent->students()->attach($student->id, ['relationship' => 'biological']);

        app(PromotionService::class)->apply($sourceYear, $targetYear, [$sourceClass->id => null], []);

        $this->assertDatabaseHas('students', ['id' => $student->id, 'student_status' => 'graduated', 'is_active' => 0]);
        $this->assertDatabaseHas('parent_student', ['parent_id' => $parent->id, 'student_id' => $student->id]);
        $this->assertFalse($parent->fresh()->user->is_active);
        $this->assertDatabaseHas('parent_archive_records', ['parent_id' => $parent->id, 'login_disabled' => 1]);
    }
}
