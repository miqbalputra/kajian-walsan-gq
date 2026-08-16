<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\GoogleFormSubmission;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GoogleFormAttendanceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_mustawa_one_form_response_creates_pending_attendance(): void
    {
        [$event, $student, $parent] = $this->makeMustawaOneFixture();
        $payload = $this->payload($event, $student, $parent, 'google-response-1', 'hadir_online');

        $response = $this->postSigned($payload);

        $response->assertCreated()->assertJsonPath('status', 'processed');
        $this->assertDatabaseHas('google_form_submissions', [
            'response_id' => 'google-response-1',
            'processing_status' => GoogleFormSubmission::STATUS_PROCESSED,
            'student_id' => $student->id,
            'parent_id' => $parent->id,
        ]);
        $this->assertDatabaseHas('attendances', [
            'kajian_event_id' => $event->id,
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'method' => Attendance::METHOD_GOOGLE_FORM,
            'validation_status' => Attendance::VALIDATION_PENDING,
        ]);
    }

    public function test_same_response_and_second_response_do_not_duplicate_attendance(): void
    {
        [$event, $student, $parent] = $this->makeMustawaOneFixture();
        $firstPayload = $this->payload($event, $student, $parent, 'google-response-duplicate-1', 'izin');
        $firstPayload['notes'] = 'Ada keperluan keluarga.';

        $this->postSigned($firstPayload)->assertCreated();
        $this->postSigned($firstPayload)->assertOk()->assertJsonPath('code', 'duplicate_response');

        $secondPayload = $this->payload($event, $student, $parent, 'google-response-duplicate-2', 'hadir_online');
        $this->postSigned($secondPayload)->assertOk()->assertJsonPath('code', 'duplicate_attendance');

        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseCount('google_form_submissions', 2);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        config(['services.google_forms.webhook_secret' => 'test-secret']);

        $response = $this->call(
            'POST',
            '/api/integrations/google-forms/mustawa-1',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_GOOGLE_FORM_SIGNATURE' => 'invalid',
            ],
            json_encode(['response_id' => 'invalid-signature'])
        );

        $response->assertUnauthorized();
        $this->assertDatabaseCount('google_form_submissions', 0);
    }

    public function test_unmatched_parent_is_saved_for_admin_retry_without_creating_attendance(): void
    {
        [$event, $student, $parent] = $this->makeMustawaOneFixture();
        $payload = $this->payload($event, $student, $parent, 'google-response-unresolved', 'izin');
        $payload['notes'] = 'Tidak dapat hadir.';
        $payload['parent_phone'] = '081299999999';

        $response = $this->postSigned($payload);

        $response->assertStatus(202)->assertJsonPath('status', GoogleFormSubmission::STATUS_UNRESOLVED);
        $this->assertDatabaseHas('google_form_submissions', [
            'response_id' => 'google-response-unresolved',
            'processing_status' => GoogleFormSubmission::STATUS_UNRESOLVED,
            'error_code' => 'parent_not_found',
        ]);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_closed_event_response_is_kept_without_creating_attendance(): void
    {
        [$event, $student, $parent] = $this->makeMustawaOneFixture();
        $event->update(['status' => 'closed']);

        $response = $this->postSigned($this->payload($event, $student, $parent, 'google-response-closed', 'hadir_online'));

        $response->assertStatus(202)->assertJsonPath('code', 'event_closed');
        $this->assertDatabaseHas('google_form_submissions', [
            'response_id' => 'google-response-closed',
            'processing_status' => GoogleFormSubmission::STATUS_UNRESOLVED,
            'error_code' => 'event_closed',
        ]);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_options_returns_only_active_mustawa_one_students(): void
    {
        [, $student] = $this->makeMustawaOneFixture();
        $classTwo = ClassRoom::create(['name' => 'Mustawa 2 Ikhwan', 'level' => '2', 'is_active' => true]);
        Student::create([
            'nis' => '0214470999',
            'name' => 'Santri Mustawa Dua',
            'class_id' => $classTwo->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);

        config(['services.google_forms.webhook_secret' => 'test-secret']);
        $response = $this->call(
            'GET',
            '/api/integrations/google-forms/mustawa-1/options',
            [],
            [],
            [],
            [
                'HTTP_X_GOOGLE_FORM_SIGNATURE' => hash_hmac('sha256', '', 'test-secret'),
            ]
        );

        $response->assertOk()
            ->assertJsonPath('students.0.reference', $student->nis)
            ->assertJsonMissing(['reference' => '0214470999']);
    }

    private function makeMustawaOneFixture(): array
    {
        $year = AcademicYear::active();
        $class = ClassRoom::create(['name' => 'Mustawa 1 Ikhwan', 'level' => '1', 'is_active' => true]);
        $user = User::create([
            'name' => 'Bapak Santri Uji',
            'username' => 'BPK-TEST-1',
            'email' => 'bapak-santri-uji@example.test',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'is_active' => true,
        ]);
        $parent = ParentModel::create([
            'user_id' => $user->id,
            'type' => 'father',
            'qr_code_string' => 'P-GOOGLE-FORM-TEST',
        ]);
        $student = Student::create([
            'nis' => '0214470332',
            'name' => 'Abdul Karim Zaidan',
            'class_id' => $class->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
        $parent->students()->attach($student->id, [
            'relationship' => 'biological',
            'is_primary_contact' => true,
        ]);

        $event = KajianEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Kajian Mustawa 1 Uji',
            'date' => today(),
            'time_start' => '08:00',
            'time_end' => '10:00',
            'status' => 'open',
            'category' => 'kajian',
        ]);

        return [$event, $student, $parent];
    }

    private function payload(KajianEvent $event, Student $student, ParentModel $parent, string $responseId, string $status): array
    {
        $parent->load('user');

        return [
            'response_id' => $responseId,
            'form_id' => 'form-test-1',
            'submitted_at' => now()->toISOString(),
            'event_date' => $event->date->toDateString(),
            'status' => $status,
            'student_reference' => $student->nis,
            'student_name' => $student->name,
            'parent_type' => $parent->type,
            'parent_name' => $parent->user->name,
            'parent_phone' => $parent->user->phone,
            'notes' => $status === 'izin' ? 'Keterangan izin.' : '',
        ];
    }

    private function postSigned(array $payload)
    {
        config(['services.google_forms.webhook_secret' => 'test-secret']);
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $this->call(
            'POST',
            '/api/integrations/google-forms/mustawa-1',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_GOOGLE_FORM_SIGNATURE' => hash_hmac('sha256', $body, 'test-secret'),
            ],
            $body
        );
    }
}
