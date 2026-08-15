<?php

namespace Tests\Feature;

use App\Jobs\ReviewAttendanceProof;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\PublicAttendanceLink;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\PublicMustawaOneAttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicMustawaOneAttendanceFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cloudinary.enabled' => false]);
        Storage::fake('public');
        Bus::fake();
    }

    public function test_only_new_parent_type_is_offered_for_a_mustawa_one_student(): void
    {
        [, $student] = $this->makeFixture();
        $newFather = $this->makeParent('father', 'new-father');
        $oldMother = $this->makeParent('mother', 'old-mother');
        $student->parents()->attach($newFather->id, ['relationship' => 'biological', 'is_primary_contact' => true]);
        $student->parents()->attach($oldMother->id, ['relationship' => 'biological', 'is_primary_contact' => false]);

        $classTwo = ClassRoom::create(['name' => 'Mustawa 2 Akhwat', 'level' => '2', 'is_active' => true]);
        $olderChild = Student::create([
            'nis' => 'M2-ELIGIBILITY-1',
            'name' => 'Kakak Mustawa Dua',
            'class_id' => $classTwo->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
        $oldMother->students()->attach($olderChild->id, ['relationship' => 'biological', 'is_primary_contact' => true]);

        $option = app(PublicMustawaOneAttendanceService::class)->eligibleStudentOptions()
            ->firstWhere('id', $student->id);

        $this->assertNotNull($option);
        $this->assertSame(['father'], $option['parent_types']);

        $link = PublicAttendanceLink::create([
            'audience' => PublicAttendanceLink::AUDIENCE_MUSTAWA_ONE_NEW,
            'token' => str()->random(48),
            'is_active' => true,
        ]);
        $this->get(route('public.mustawa-one-form.show', ['token' => $link->token]))
            ->assertOk()
            ->assertSee($student->name);
    }

    public function test_parent_with_mustawa_two_enrollment_history_is_excluded_even_if_child_is_archived(): void
    {
        [$year, $student] = $this->makeFixture();
        $father = $this->makeParent('father', 'history-father');
        $student->parents()->attach($father->id, ['relationship' => 'biological', 'is_primary_contact' => true]);

        $classTwo = ClassRoom::create(['name' => 'Mustawa 2 Ikhwan', 'level' => '2', 'is_active' => true]);
        $olderChild = Student::create([
            'nis' => 'M2-HISTORY-1',
            'name' => 'Kakak Alumni',
            'class_id' => null,
            'is_active' => false,
            'student_status' => 'graduated',
        ]);
        StudentEnrollment::create([
            'student_id' => $olderChild->id,
            'academic_year_id' => $year->id,
            'class_id' => $classTwo->id,
            'class_name' => $classTwo->name,
            'class_level' => '2',
            'status' => 'graduated',
        ]);
        $father->students()->attach($olderChild->id, ['relationship' => 'biological', 'is_primary_contact' => true]);

        $options = app(PublicMustawaOneAttendanceService::class)->eligibleStudentOptions();

        $this->assertNull($options->firstWhere('id', $student->id));
    }

    public function test_public_form_creates_pending_online_attendance_and_queues_ocr(): void
    {
        [, $student, $event, $link] = $this->makeFixture();
        $father = $this->makeParent('father', 'submit-father');
        $student->parents()->attach($father->id, ['relationship' => 'biological', 'is_primary_contact' => true]);

        $response = $this->post(route('public.mustawa-one-form.store', ['token' => $link->token]), [
            'student_id' => $student->id,
            'parent_type' => 'father',
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'notes' => 'Menyimak dari rumah.',
            'proof_file' => $this->fakeImage('catatan.png'),
        ]);

        $response->assertRedirect(route('public.mustawa-one-form.show', ['token' => $link->token]));
        $this->assertDatabaseHas('attendances', [
            'kajian_event_id' => $event->id,
            'parent_id' => $father->id,
            'student_id' => $student->id,
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'method' => Attendance::METHOD_PUBLIC_FORM,
            'validation_status' => Attendance::VALIDATION_PENDING,
        ]);
        Bus::assertDispatched(ReviewAttendanceProof::class);
    }

    public function test_izin_requires_notes_and_duplicate_pending_submission_is_rejected(): void
    {
        [, $student, , $link] = $this->makeFixture();
        $mother = $this->makeParent('mother', 'duplicate-mother');
        $student->parents()->attach($mother->id, ['relationship' => 'biological', 'is_primary_contact' => true]);

        $payload = [
            'student_id' => $student->id,
            'parent_type' => 'mother',
            'status' => Attendance::STATUS_IZIN,
            'proof_file' => $this->fakeImage('surat-izin.png'),
        ];

        $this->from(route('public.mustawa-one-form.show', ['token' => $link->token]))
            ->post(route('public.mustawa-one-form.store', ['token' => $link->token]), $payload)
            ->assertSessionHasErrors('notes');

        $payload['notes'] = 'Ada keperluan keluarga.';
        $this->post(route('public.mustawa-one-form.store', ['token' => $link->token]), $payload)
            ->assertRedirect();

        $this->from(route('public.mustawa-one-form.show', ['token' => $link->token]))
            ->post(route('public.mustawa-one-form.store', ['token' => $link->token]), [
                ...$payload,
                'proof_file' => $this->fakeImage('surat-izin-ulang.png'),
            ])
            ->assertSessionHasErrors('form');

        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_rejected_public_submission_can_replace_proof_without_duplicate_attendance(): void
    {
        [, $student, $event, $link] = $this->makeFixture();
        $father = $this->makeParent('father', 'reupload-father');
        $student->parents()->attach($father->id, ['relationship' => 'biological', 'is_primary_contact' => true]);

        $attendance = Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $father->id,
            'student_id' => $student->id,
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'method' => Attendance::METHOD_PUBLIC_FORM,
            'proof_file' => 'attendance-proofs/old-proof.jpg',
            'validation_status' => Attendance::VALIDATION_REJECTED,
            'rejection_reason' => 'Foto tidak jelas.',
        ]);

        $this->post(route('public.mustawa-one-form.store', ['token' => $link->token]), [
            'student_id' => $student->id,
            'parent_type' => 'father',
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'proof_file' => $this->fakeImage('catatan-baru.png'),
        ])->assertRedirect();

        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'validation_status' => Attendance::VALIDATION_PENDING,
        ]);
        $this->assertDatabaseHas('attendance_proof_histories', [
            'attendance_id' => $attendance->id,
            'proof_file' => 'attendance-proofs/old-proof.jpg',
            'source' => 'public_form_reupload',
        ]);
    }

    public function test_old_link_and_honeypot_do_not_create_attendance(): void
    {
        [, $student, , $link] = $this->makeFixture();
        $father = $this->makeParent('father', 'honeypot-father');
        $student->parents()->attach($father->id, ['relationship' => 'biological', 'is_primary_contact' => true]);

        $link->update(['is_active' => false, 'revoked_at' => now()]);
        $this->get(route('public.mustawa-one-form.show', ['token' => $link->token]))->assertNotFound();

        $link->update(['is_active' => true, 'revoked_at' => null]);
        $this->post(route('public.mustawa-one-form.store', ['token' => $link->token]), [
            'student_id' => $student->id,
            'parent_type' => 'father',
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'proof_file' => $this->fakeImage('catatan.png'),
            'website' => 'https://spam.example.test',
        ])->assertRedirect();

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_form_displays_unavailable_message_when_no_mustawa_one_event_is_open(): void
    {
        $link = PublicAttendanceLink::create([
            'audience' => PublicAttendanceLink::AUDIENCE_MUSTAWA_ONE_NEW,
            'token' => str()->random(48),
            'is_active' => true,
        ]);

        $this->get(route('public.mustawa-one-form.show', ['token' => $link->token]))
            ->assertOk()
            ->assertSee('Form belum tersedia');
    }

    /** @return array{0: AcademicYear, 1: Student, 2: KajianEvent, 3: PublicAttendanceLink} */
    private function makeFixture(): array
    {
        $year = AcademicYear::active();
        $class = ClassRoom::create(['name' => 'Mustawa 1 Ikhwan', 'level' => '1', 'is_active' => true]);
        $student = Student::create([
            'nis' => 'M1-'.str()->upper(str()->random(8)),
            'name' => 'Santri Mustawa Satu',
            'class_id' => $class->id,
            'is_active' => true,
            'student_status' => 'active',
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
        $link = PublicAttendanceLink::create([
            'audience' => PublicAttendanceLink::AUDIENCE_MUSTAWA_ONE_NEW,
            'token' => str()->random(48),
            'is_active' => true,
        ]);

        return [$year, $student, $event, $link];
    }

    private function makeParent(string $type, string $identity): ParentModel
    {
        $user = User::create([
            'name' => 'Wali '.$identity,
            'username' => 'USR-'.str()->upper(str()->random(10)),
            'email' => $identity.'-'.str()->random(8).'@example.test',
            'password' => Hash::make('password'),
            'phone' => '0812'.random_int(1000000, 9999999),
            'is_active' => true,
        ]);

        return ParentModel::create([
            'user_id' => $user->id,
            'type' => $type,
            'qr_code_string' => 'P-'.str()->upper(str()->random(16)),
        ]);
    }

    private function fakeImage(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScL1JwAAAABJRU5ErkJggg==')
        );
    }
}
