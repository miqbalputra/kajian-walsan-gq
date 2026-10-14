<?php

namespace Tests\Feature;

use App\Livewire\WaliSantri\Profile;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class WaliAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_wali_pages_load_local_fonts_and_only_their_required_javascript(): void
    {
        Http::fake();
        $user = User::factory()->create(['role_id' => Role::where('name', 'wali_santri')->value('id'), 'username' => 'wali-aset', 'password' => 'PasswordUjiAset123!']);
        ParentModel::create(['user_id' => $user->id, 'type' => 'father', 'qr_code_string' => 'ASSET-TEST']);

        foreach (['wali-santri.dashboard', 'wali-santri.schedule', 'wali-santri.profile'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk()
                ->assertSee('as="font"', false)->assertSee('wali-symbols', false)->assertSee('inter-latin', false)
                ->assertSee(Vite::asset('resources/js/wali.js'), false)->assertDontSee(Vite::asset('resources/js/app.js'), false)
                ->assertDontSee('fonts.googleapis.com', false)->assertDontSee('fonts.gstatic.com', false);
        }
    }

    public function test_local_font_files_match_their_manifest_and_license_files_exist(): void
    {
        $manifest = json_decode(file_get_contents(resource_path('fonts/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($manifest['assets'] as $asset) {
            $path = resource_path('fonts/'.$asset['file']);
            $this->assertSame('wOF2', file_get_contents($path, false, null, 0, 4));
            $this->assertSame($asset['bytes'], filesize($path));
            $this->assertSame($asset['sha256'], hash_file('sha256', $path));
        }
        $this->assertFileExists(resource_path('fonts/licenses/Inter-OFL.txt'));
        $this->assertFileExists(resource_path('fonts/licenses/Material-Symbols-Apache-2.0.txt'));
    }

    public function test_icon_subset_covers_wali_views_theme_states_and_all_supported_avatars(): void
    {
        $icons = json_decode(file_get_contents(resource_path('fonts/wali-icons.json')), true, flags: JSON_THROW_ON_ERROR);
        $manifest = json_decode(file_get_contents(resource_path('fonts/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame($icons, $manifest['icons']);
        $used = ['light_mode', 'dark_mode'];
        foreach ((new Profile)->avatarPresets as $preset) {
            $used[] = $preset['icon'];
        }
        $views = array_merge(
            glob(resource_path('views/components/wali/*.blade.php')),
            glob(resource_path('views/livewire/wali-santri/*.blade.php')),
            glob(resource_path('views/livewire/panitia/*.blade.php')),
            [resource_path('views/components/layouts/panitia.blade.php')],
        );
        foreach ($views as $view) {
            preg_match_all('/<span[^>]*class="[^"]*material-symbols-rounded[^"]*"[^>]*>\s*([a-z][a-z0-9_]*)\s*<\/span>/', file_get_contents($view), $matches);
            $used = array_merge($used, $matches[1]);
        }
        $used = array_merge($used, ['front_camera', 'rear_camera', 'fullscreen', 'fullscreen_exit', 'play_arrow', 'stop', 'error', 'info']);
        $this->assertEmpty(array_diff(array_unique($used), $icons), 'Update the local icon subset when adding Wali or Panitia icons.');
    }
}
