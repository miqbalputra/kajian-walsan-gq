<?php

namespace Tests\Feature;

use App\Livewire\WaliSantri\Dashboard;
use App\Livewire\WaliSantri\KajianSchedule;
use App\Livewire\WaliSantri\Profile;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceProofReviewService;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WaliPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 10, 12)->setTime(8, 0));
        Http::fake();
        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_all_wali_pages_keep_navigation_install_access_and_browser_zoom(): void
    {
        [$user] = $this->guardian();
        foreach (['wali-santri.dashboard', 'wali-santri.schedule', 'wali-santri.profile'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk()
                ->assertSee('Pasang aplikasi')->assertSee('Gunakan mode gelap')
                ->assertSee(route('wali-santri.dashboard'), false)->assertSee(route('wali-santri.schedule'), false)->assertSee(route('wali-santri.profile'), false)
                ->assertSee("serviceWorker.register('/sw.js')", false)->assertDontSee('kajian-pwa-install-middleware', false)
                ->assertDontSee('user-scalable=no', false)->assertDontSee('maximum-scale=1.0', false);
        }
    }

    public function test_dashboard_prioritizes_attendance_and_retains_both_alternative_actions(): void
    {
        [$user] = $this->guardian();
        $event = $this->event();
        Livewire::actingAs($user)->test(Dashboard::class)
            ->assertSeeInOrder([$event->title, 'Tampilkan QR Code Saya', 'Hadir Online', 'Izin', 'Riwayat Kehadiran', 'Aktifkan Notifikasi'])
            ->call('openOnlineAttendanceModal')->assertSet('showOnlineModal', true)->assertSee('Foto Bukti Online')
            ->set('notes', 'Catatan contoh')->call('cancelOnlineAttendanceSelection')->assertSet('showOnlineModal', false)->assertSet('notes', '')
            ->call('openIzinModal')->assertSet('showIzinModal', true)->call('submitIzin')->assertHasErrors(['izinDocument', 'notes'])
            ->call('cancelIzinSelection')->assertSet('showIzinModal', false);
    }

    public function test_future_schedule_is_visible_without_opening_attendance_and_hidden_online_policy_is_retained(): void
    {
        [$user] = $this->guardian();
        $event = $this->event(['date' => today()->addDays(2)]);
        Livewire::actingAs($user)->test(Dashboard::class)->assertSee('Presensi belum dibuka')->assertSee($event->title)->assertDontSee('Tampilkan QR Code Saya');
        $event->update(['date' => today(), 'category' => 'rapor']);
        Livewire::actingAs($user)->test(Dashboard::class)->assertSee('Tampilkan QR Code Saya')->assertSee('Izin')->assertDontSee('Hadir Online');
    }

    #[DataProvider('pendingReviewStates')]
    public function test_pending_review_does_not_look_like_approved_attendance(?string $aiStatus, string $label): void
    {
        [$user, $parent] = $this->guardian();
        $attendance = $this->attendance($parent, $this->event(), ['ai_validation_status' => $aiStatus]);
        Livewire::actingAs($user)->test(Dashboard::class)->assertSee($label)->assertSee('Ganti Foto Bukti')->assertSee('Batalkan Kiriman')
            ->assertDontSee('Kehadiran sudah tercatat')->call('openReuploadModal', $attendance->id, true)->assertSet('reuploadIsPendingReplace', true)
            ->call('cancelReuploadSelection')->assertSet('showReuploadModal', false);
    }

    public static function pendingReviewStates(): array
    {
        return [[null, 'Menunggu pemeriksaan'], ['queued', 'Sedang diperiksa otomatis'], ['failed', 'Perlu pemeriksaan admin']];
    }

    public function test_rejection_is_shown_once_with_reason_and_approved_review_blocks_replacement(): void
    {
        [$user, $parent] = $this->guardian();
        $attendance = $this->attendance($parent, $this->event(), ['validation_status' => 'rejected', 'rejection_reason' => 'Tulisan belum terbaca']);
        $component = Livewire::actingAs($user)->test(Dashboard::class)->assertSee('Tulisan belum terbaca')->assertSee('Upload Ulang Bukti')->assertDontSee('Bukti kajian lain perlu diperbaiki');
        $this->assertSame(1, substr_count($component->html(), 'Tulisan belum terbaca'));
        $attendance->update(['validation_status' => 'approved', 'validated_by' => $user->id]);
        Livewire::actingAs($user)->test(Dashboard::class)->assertSee('Kehadiran sudah tercatat')->assertSee('Lihat Bukti Terkirim')->assertDontSee('Ganti Foto Bukti')->assertDontSee('Batalkan Kiriman');
    }

    public function test_uploads_still_save_online_and_izin_using_existing_fields(): void
    {
        [$user, $parent] = $this->guardian();
        $event = $this->event();
        $this->mock(CloudinaryService::class, fn ($mock) => $mock->shouldReceive('upload')->andReturn(['url' => 'https://example.test/proof.jpg', 'is_cloud' => true]));
        $this->mock(AttendanceProofReviewService::class, fn ($mock) => $mock->shouldReceive('queue')->andReturn(false));
        Livewire::actingAs($user)->test(Dashboard::class)->call('openOnlineAttendanceModal')->set('proofPhoto', $this->photo())->call('submitOnlineAttendance')->assertHasNoErrors()->assertSet('showOnlineModal', false);
        $attendance = Attendance::firstOrFail();
        $this->assertSame('hadir_online', $attendance->status);
        Livewire::actingAs($user)->test(Dashboard::class)->call('cancelSubmittedAttendance', $attendance->id)->assertSee('Tampilkan QR Code Saya');
        Livewire::actingAs($user)->test(Dashboard::class)->call('openIzinModal')->set('izinDocument', $this->photo())->set('notes', 'Berhalangan hadir')->call('submitIzin')->assertHasNoErrors()->assertSet('showIzinModal', false);
        $this->assertDatabaseHas('attendances', ['parent_id' => $parent->id, 'kajian_event_id' => $event->id, 'status' => 'izin', 'notes' => 'Berhalangan hadir', 'deleted_at' => null]);
    }

    public function test_teacher_and_guardian_teacher_actions_are_preserved(): void
    {
        [$user, $parent] = $this->guardian('guru', false);
        $this->event();
        Livewire::actingAs($user)->test(Dashboard::class)->assertSee('Upload Hadir Langsung')->assertSee('Upload Menyimak Online')->assertSee('Upload Izin')->assertDontSee('Tampilkan QR Code Saya');
        $parent->update(['type' => 'father', 'is_teacher' => true]);
        $child = Student::create(['name' => 'Ananda Guru', 'nis' => 'TEST-GURU', 'is_active' => true, 'student_status' => 'active', 'class_id' => ClassRoom::create(['name' => 'Kelas Guru', 'level' => '1', 'is_active' => true])->id]);
        $parent->students()->attach($child);
        $this->attendance($parent, KajianEvent::first(), ['status' => 'hadir_fisik', 'method' => 'scan_qr', 'proof_file' => null]);
        Livewire::actingAs($user)->test(Dashboard::class)->assertSee('Upload Catatan Kajian');
    }

    #[DataProvider('replaceableStatuses')]
    public function test_replacement_and_rejected_reupload_preserve_previous_proof(string $status): void
    {
        [$user, $parent] = $this->guardian();
        $attendance = $this->attendance($parent, $this->event(), ['validation_status' => $status, 'rejection_reason' => 'Foto tidak terbaca']);
        $this->mock(CloudinaryService::class, fn ($mock) => $mock->shouldReceive('upload')->once()->andReturn(['url' => 'https://example.test/new-proof.jpg', 'is_cloud' => true]));
        $this->mock(AttendanceProofReviewService::class, fn ($mock) => $mock->shouldReceive('queue')->andReturn(false));
        Livewire::actingAs($user)->test(Dashboard::class)->call('openReuploadModal', $attendance->id, $status === 'pending')
            ->call('reuploadProof')->assertHasErrors('reuploadFile')->assertSet('showReuploadModal', true)
            ->set('reuploadFile', $this->photo())->call('reuploadProof')->assertHasNoErrors()->assertSet('showReuploadModal', false)
            ->assertSee('Menunggu pemeriksaan');
        $this->assertDatabaseHas('attendances', ['id' => $attendance->id, 'proof_file' => 'https://example.test/new-proof.jpg', 'validation_status' => 'pending', 'rejection_reason' => null]);
        $this->assertDatabaseHas('attendance_proof_histories', ['attendance_id' => $attendance->id, 'proof_file' => 'https://example.test/proof.jpg', 'source' => 'reupload']);
    }

    public static function replaceableStatuses(): array
    {
        return [['pending'], ['rejected']];
    }

    public function test_survey_retains_both_ratings_comment_validation_and_saved_feedback(): void
    {
        [$user, $parent] = $this->guardian();
        $event = $this->event(['date' => today()->subDay(), 'status' => 'closed']);
        $this->attendance($parent, $event, ['validation_status' => 'approved']);
        Livewire::actingAs($user)->test(Dashboard::class)->assertSee('Isi Survey')->call('openFeedbackModal', $event->id)
            ->call('submitFeedback')->assertHasErrors(['ratingMateri', 'ratingOperasional'])->assertSet('showFeedbackModal', true)
            ->set('ratingMateri', 5)->set('ratingOperasional', 4)->set('feedbackComment', str_repeat('x', 1001))->call('submitFeedback')->assertHasErrors('feedbackComment')
            ->set('feedbackComment', 'Materi mudah dipahami.')->call('submitFeedback')->assertHasNoErrors()->assertSet('showFeedbackModal', false)->assertDontSee('Isi Survey');
        $this->assertDatabaseHas('kajian_feedback', ['kajian_event_id' => $event->id, 'user_id' => $user->id, 'rating' => 4.5, 'comment' => 'Materi mudah dipahami.']);
    }

    public function test_calendar_changes_month_and_keeps_every_event_on_a_shared_date(): void
    {
        [$user] = $this->guardian();
        $first = $this->event(['title' => 'Kajian Pertama']);
        $second = $this->event(['title' => 'Kajian Kedua']);
        Livewire::actingAs($user)->test(KajianSchedule::class)->call('showDay', today()->toDateString())->assertSet('showDayPicker', true)->assertSee('Kajian Pertama')->assertSee('Kajian Kedua')
            ->call('showDetail', $second->id)->assertSet('showDayPicker', false)->assertSet('showEventDetail', true)->assertSet('selectedEvent.id', $second->id)
            ->set('showEventDetail', false)->call('nextMonth')->assertSet('currentMonth', 11)->call('prevMonth')->assertSet('currentMonth', 10);
        $second->delete();
        Livewire::actingAs($user)->test(KajianSchedule::class)->call('showDay', today()->toDateString())->assertSet('showDayPicker', false)->assertSet('selectedEvent.id', $first->id);
    }

    public function test_schedule_detail_does_not_claim_future_open_event_is_active_and_preserves_long_text(): void
    {
        [$user] = $this->guardian();
        $event = $this->event(['date' => today()->addDays(2), 'title' => str_repeat('Judul kajian panjang ', 8), 'location' => str_repeat('Tempat kajian panjang ', 6)]);
        Livewire::actingAs($user)->test(KajianSchedule::class)->assertSee('<p>Rabu, 14 Oktober 2026</p>', false)
            ->call('showDetail', $event->id)->assertSee('Presensi belum dibuka')->assertSee($event->title)->assertSee($event->location)->assertDontSee('Presensi dibuka');
        $event->update(['status' => 'closed']);
        Livewire::actingAs($user)->test(KajianSchedule::class)->call('showDetail', $event->id)->assertSee('Presensi ditutup');
    }

    public function test_profile_avatar_contact_validation_and_card_download_are_preserved(): void
    {
        [$user] = $this->guardian();
        Livewire::actingAs($user)->test(Profile::class)->call('selectAvatar', 'menu_book', 'bg-emerald-500')->assertSet('avatar', 'icon:menu_book|bg-emerald-500')
            ->set('phone', 'tidak valid')->call('updateProfile')->assertHasErrors('phone')->set('phone', '081390001122')->set('name', 'Wali Uji Diperbarui')->call('updateProfile')->assertHasNoErrors()
            ->call('showCard')->assertSet('showCardModal', true)->assertSee('Download Kartu');
        $this->assertSame('icon:menu_book|bg-emerald-500', $user->fresh()->avatar);
        $this->actingAs($user)->get(route('wali-santri.kartu.download'))->assertOk();
    }

    public function test_password_validation_success_and_login_again_flow_are_preserved(): void
    {
        [$user] = $this->guardian();
        $component = Livewire::actingAs($user)->test(Profile::class)->set('current_password', 'wrong')->set('new_password', 'PasswordUjiBaru123!')->set('new_password_confirmation', 'PasswordUjiBaru123!')->call('updatePassword')->assertHasErrors('current_password');
        $component->set('current_password', 'PasswordUji123!')->call('updatePassword')->assertHasNoErrors()->assertSet('showPasswordSuccessModal', true)->assertSee('Update password')->call('logoutAfterPasswordChange')->assertRedirect(route('login'));
        Http::assertSentCount(1);
    }

    private function guardian(string $role = 'wali_santri', bool $withChild = true): array
    {
        $user = User::factory()->create(['name' => 'Wali Uji', 'username' => 'wali-uji', 'email' => 'wali@example.test', 'password' => 'PasswordUji123!', 'phone' => '081390001122', 'role_id' => Role::where('name', $role)->value('id')]);
        // Existing teacher enum migration is MariaDB-only. Emulate that fixture
        // in SQLite without changing migrations or production schema.
        $sqliteTeacher = $role === 'guru' && DB::connection()->getDriverName() === 'sqlite';
        if ($sqliteTeacher) {
            DB::statement('PRAGMA ignore_check_constraints = ON');
        }
        try {
            $parent = ParentModel::create(['user_id' => $user->id, 'type' => $role === 'guru' ? 'teacher' : 'father', 'qr_code_string' => 'UI-TEST-'.$user->id]);
        } finally {
            if ($sqliteTeacher) {
                DB::statement('PRAGMA ignore_check_constraints = OFF');
            }
        }
        if ($withChild) {
            $class = ClassRoom::create(['name' => 'Kelas Uji', 'level' => '1', 'is_active' => true]);
            $child = Student::create(['name' => 'Ananda Uji', 'nis' => 'UI-TEST-S', 'class_id' => $class->id, 'is_active' => true, 'student_status' => 'active']);
            $parent->students()->attach($child);
        }

        return [$user, $parent];
    }

    private function event(array $attributes = []): KajianEvent
    {
        return KajianEvent::create(array_merge(['academic_year_id' => AcademicYear::active()->id, 'title' => 'Kajian Wali Santri', 'date' => today(), 'time_start' => '09:00', 'time_end' => '11:00', 'location' => 'Masjid Manarul Ilmu', 'speaker' => 'Ustadz Pemateri', 'status' => 'open', 'category' => 'kajian'], $attributes));
    }

    private function attendance(ParentModel $parent, KajianEvent $event, array $attributes = []): Attendance
    {
        return Attendance::create(array_merge(['parent_id' => $parent->id, 'kajian_event_id' => $event->id, 'status' => 'hadir_online', 'method' => 'upload', 'validation_status' => 'pending', 'proof_file' => 'https://example.test/proof.jpg'], $attributes));
    }

    private function photo(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('proof.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScLbtAAAAABJRU5ErkJggg=='));
    }
}
