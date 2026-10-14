<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\KajianEvent;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class PanitiaScannerUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_scanner_page_loads_its_local_qr_bundle_without_external_assets_or_zoom_lock(): void
    {
        $this->actingAs($this->panitia())->get(route('panitia.scanner'))
            ->assertOk()
            ->assertSee(Vite::asset('resources/css/panitia.css'), false)
            ->assertSee(Vite::asset('resources/css/wali-fonts.css'), false)
            ->assertSee(Vite::asset('resources/js/panitia-scanner.js'), false)
            ->assertDontSee(Vite::asset('resources/js/app.js'), false)
            ->assertDontSee('fonts.googleapis.com', false)
            ->assertDontSee('fonts.gstatic.com', false)
            ->assertDontSee('sweetalert', false)
            ->assertDontSee('maximum-scale=1', false)
            ->assertDontSee('user-scalable=no', false)
            ->assertSee('Belum ada kajian aktif')
            ->assertSee('Lihat jadwal kajian');
    }

    public function test_schedule_page_does_not_download_the_qr_scanner_bundle(): void
    {
        $this->actingAs($this->panitia())->get(route('panitia.jadwal'))
            ->assertOk()
            ->assertDontSee(Vite::asset('resources/js/panitia-scanner.js'), false)
            ->assertDontSee(Vite::asset('resources/js/app.js'), false)
            ->assertDontSee('fonts.googleapis.com', false)
            ->assertDontSee('cdn.jsdelivr.net', false);
    }

    public function test_active_event_keeps_camera_controls_manual_entry_count_and_recent_attendance(): void
    {
        $year = AcademicYear::where('is_active', true)->firstOrFail();
        KajianEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Kajian Wali Santri',
            'date' => today()->toDateString(),
            'time_start' => '08:00:00',
            'time_end' => '10:00:00',
            'location' => 'Aula Pesantren',
            'status' => 'open',
        ]);

        $this->actingAs($this->panitia())->get(route('panitia.scanner'))
            ->assertOk()
            ->assertSee('Scan QR presensi')
            ->assertSee('Arahkan kamera ke kartu wali')
            ->assertSee('Input manual')
            ->assertSee('Presensi terakhir')
            ->assertSee('Ketik minimal 2 huruf untuk mencari wali atau santri.');

        $this->assertStringContainsString('fps: 8', file_get_contents(resource_path('js/panitia-scanner.js')));
    }

    private function panitia(): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', 'panitia')->value('id'),
            'username' => 'panitia-ui',
        ]);
    }
}
