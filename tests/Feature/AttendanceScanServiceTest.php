<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceScanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceScanServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_scan_is_idempotent_and_creates_enrollment_snapshot(): void
    {
        $year = AcademicYear::active();
        $class = ClassRoom::create(['name' => 'Kelas 1A', 'level' => '1', 'is_active' => true]);
        $operator = User::factory()->create();
        $parent = ParentModel::create([
            'user_id' => $operator->id,
            'type' => 'father',
            'qr_code_string' => 'P-TEST-FATHER',
        ]);
        $student = Student::create([
            'nis' => 'S-1001',
            'name' => 'Santri Uji',
            'class_id' => $class->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
        $parent->students()->attach($student->id, [
            'relationship' => 'biological',
            'is_primary_contact' => true,
        ]);
        $parent->syncQrCode();

        $event = KajianEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Kajian Uji',
            'date' => today(),
            'time_start' => '08:00',
            'time_end' => '10:00',
            'status' => 'open',
            'category' => 'kajian',
        ]);

        $service = app(AttendanceScanService::class);
        $alias = 'AS-1001';
        $parent->qrCodes()->where('source_student_id', $student->id)->update(['code' => $alias]);

        $first = $service->process($event, $alias, $operator->id, 'test-agent');
        $second = $service->process($event, $alias, $operator->id, 'test-agent');

        $this->assertSame('success', $first['status']);
        $this->assertSame('warning', $second['status']);
        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseCount('student_enrollments', 1);
        $this->assertDatabaseHas('attendances', [
            'kajian_event_id' => $event->id,
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'method' => Attendance::METHOD_SCAN_QR,
            'validation_status' => Attendance::VALIDATION_APPROVED,
        ]);

        // A cancelled upload must be restorable for a fresh QR scan without
        // carrying the cancelled proof into the new attendance record.
        $attendance = Attendance::firstOrFail();
        $attendance->update(['proof_file' => 'old-proof.jpg', 'notes' => 'old']);
        $attendance->delete();

        $restored = $service->process($event, $alias, $operator->id, 'test-agent');

        $this->assertSame('success', $restored['status']);
        $this->assertDatabaseHas('attendance_proof_histories', [
            'attendance_id' => $attendance->id,
            'proof_file' => 'old-proof.jpg',
            'source' => 'qr_restore',
        ]);
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'proof_file' => null,
        ]);
    }

    public function test_manual_check_in_uses_same_duplicate_guard_as_qr(): void
    {
        $year = AcademicYear::active();
        $class = ClassRoom::create(['name' => 'Kelas 2A', 'level' => '2', 'is_active' => true]);
        $operator = User::factory()->create();
        $parent = ParentModel::create([
            'user_id' => $operator->id,
            'type' => 'mother',
            'qr_code_string' => 'P-TEST-MOTHER',
        ]);
        $student = Student::create([
            'nis' => 'S-2001',
            'name' => 'Santri Manual',
            'class_id' => $class->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
        $parent->students()->attach($student->id);

        $event = KajianEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Kajian Manual Uji',
            'date' => today(),
            'time_start' => '08:00',
            'time_end' => '10:00',
            'status' => 'open',
            'category' => 'kajian',
        ]);

        $service = app(AttendanceScanService::class);
        $first = $service->processManual($event, $parent, $operator->id, 'manual-test');
        $second = $service->processManual($event, $parent, $operator->id, 'manual-test');

        $this->assertSame('success', $first['status']);
        $this->assertSame('warning', $second['status']);
        $this->assertDatabaseHas('attendances', [
            'kajian_event_id' => $event->id,
            'parent_id' => $parent->id,
            'method' => Attendance::METHOD_MANUAL,
        ]);
    }
}
