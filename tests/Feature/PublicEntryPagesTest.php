<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\KajianEvent;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicEntryPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 10, 10)->setTime(8, 0));
    }

    public function test_guests_can_enter_without_a_schedule_or_install_banner(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Jadwal kajian berikutnya belum tersedia.')
            ->assertSee('Masuk sebagai wali santri')
            ->assertSee(route('login'), false)
            ->assertDontSee(route('qr.login'), false)
            ->assertDontSee('kajian-pwa-install-middleware', false)
            ->assertSee("serviceWorker.register('/sw.js')", false);
    }

    public function test_authenticated_visitors_are_directed_to_their_account(): void
    {
        $user = User::factory()->create(['role_id' => Role::where('name', 'wali_santri')->value('id')]);

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertSee('Buka akun saya')
            ->assertSee(route('dashboard'), false)
            ->assertDontSee('Masuk sebagai wali santri')
            ->assertDontSee('Masuk sebagai petugas');
    }

    #[DataProvider('scheduleStatuses')]
    public function test_schedule_labels_follow_attendance_status_not_just_the_clock(string $date, string $status, string $label): void
    {
        $this->event(['date' => $date, 'status' => $status, 'time_start' => '07:30']);
        $response = $this->get('/')->assertOk()->assertSee($label);

        if ($label !== 'Presensi dibuka') {
            $response->assertDontSee('Presensi dibuka');
        }
        $response->assertDontSee('Sedang Berlangsung');
    }

    public static function scheduleStatuses(): array
    {
        return [
            'today before admin opens' => ['2026-10-10', 'draft', 'Hari ini'],
            'today open' => ['2026-10-10', 'open', 'Presensi dibuka'],
            'today closed' => ['2026-10-10', 'closed', 'Kajian selesai'],
            'past event accepting uploads' => ['2026-10-09', 'open', 'Presensi dibuka'],
            'tomorrow' => ['2026-10-11', 'draft', 'Besok'],
            'future' => ['2026-10-12', 'draft', 'Kajian berikutnya'],
            'future open is not active yet' => ['2026-10-12', 'open', 'Kajian berikutnya'],
        ];
    }

    public function test_countdown_does_not_change_the_admin_controlled_attendance_status(): void
    {
        $event = $this->event(['time_start' => '09:00', 'status' => 'draft']);
        $this->get('/')
            ->assertOk()
            ->assertSee('Dimulai dalam 60 menit')
            ->assertSee('Waktu mulai kajian telah tiba')
            ->assertDontSee('Presensi dibuka');

        $this->assertSame('draft', $event->fresh()->status);
    }

    public function test_long_schedule_details_are_preserved(): void
    {
        $title = 'Kajian keluarga: ' . str_repeat('Belajar bersama ananda ', 6);
        $location = 'Aula utama ' . str_repeat('Griya Quran Tunas Ilmu ', 5);
        $this->event(['title' => $title, 'location' => $location, 'speaker' => 'Ustadz Pemateri Kajian']);
        $this->get('/')->assertOk()->assertSee($title)->assertSee($location)->assertSee('Ustadz Pemateri Kajian');
    }

    #[DataProvider('supportNumbers')]
    public function test_support_links_use_configured_numbers_without_linking_a_placeholder(string $phone, ?string $expected): void
    {
        Setting::set('admin_whatsapp', $phone);
        $response = $this->get('/')->assertOk();

        if ($expected) {
            $response->assertSee('https://wa.me/' . $expected, false)->assertSee('Hubungi admin');
        } else {
            $response->assertDontSee('Hubungi admin')->assertSee('Silakan hubungi petugas kajian atau wali kelas ananda.');
        }
        $response->assertDontSee('https://wa.me/6281234567890', false);
    }

    public static function supportNumbers(): array
    {
        return [
            'international' => ['6281390001122', '6281390001122'],
            'local' => ['081390001122', '6281390001122'],
            'placeholder' => ['6281234567890', null],
            'local placeholder' => ['081234567890', null],
            'empty' => ['', null],
            'invalid' => ['nomor belum tersedia', null],
        ];
    }

    public function test_login_keeps_username_after_failure_without_retaining_password(): void
    {
        $this->user();
        $this->from('/login')->post('/login', ['username' => 'wali-uji', 'password' => 'incorrect-test-password'])
            ->assertRedirect('/login')->assertSessionHasErrors('username');

        $this->get('/login')->assertOk()
            ->assertSee('value="wali-uji"', false)
            ->assertSee('id="username-error"', false)
            ->assertDontSee('incorrect-test-password')
            ->assertDontSee('autofocus', false)
            ->assertDontSee('kajian-pwa-install-middleware', false);
    }

    #[DataProvider('loginIdentifiers')]
    public function test_both_existing_login_identifiers_still_work(string $identifier): void
    {
        $user = $this->user();
        $this->post('/login', ['username' => $identifier, 'password' => 'valid-test-password'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public static function loginIdentifiers(): array
    {
        return [['wali-uji'], ['wali-uji@example.test']];
    }

    public function test_google_failure_is_shown_with_the_google_option(): void
    {
        $errors = (new ViewErrorBag)->put('default', new MessageBag(['google' => 'Email Google ini belum terdaftar.']));
        $this->withSession(['errors' => $errors])->get('/login')
            ->assertOk()
            ->assertSeeInOrder(['Email Google ini belum terdaftar.', 'Masuk dengan Google'])
            ->assertSee(route('google.redirect'), false)
            ->assertSee(route('password.request'), false)
            ->assertDontSee(route('qr.login'), false)
            ->assertSee("serviceWorker.register('/sw.js')", false);
    }

    private function event(array $attributes = []): KajianEvent
    {
        return KajianEvent::create(array_merge([
            'academic_year_id' => AcademicYear::active()->id,
            'title' => 'Kajian Wali Santri',
            'date' => today(),
            'time_start' => '09:00',
            'time_end' => '11:00',
            'location' => 'Masjid Manarul Ilmu',
            'status' => 'draft',
        ], $attributes));
    }

    private function user(): User
    {
        return User::factory()->create([
            'username' => 'wali-uji',
            'email' => 'wali-uji@example.test',
            'password' => 'valid-test-password',
            'role_id' => Role::where('name', 'wali_santri')->value('id'),
            'is_active' => true,
        ]);
    }
}
