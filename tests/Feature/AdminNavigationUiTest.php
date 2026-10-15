<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavigationUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_shell_keeps_all_navigation_targets_and_allows_browser_zoom(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Ringkasan')
            ->assertSee('Data & akademik', false)
            ->assertSee('Kehadiran & kegiatan', false)
            ->assertSee('Komunikasi & laporan', false)
            ->assertSee('Sistem')
            ->assertSee('Dashboard')
            ->assertSee('Siswa')
            ->assertSee('Orang Tua')
            ->assertSee('Data Guru')
            ->assertSee('Kelas')
            ->assertSee('Kenaikan Kelas')
            ->assertSee('Arsip Santri & Wali', false)
            ->assertSee('Presensi Guru')
            ->assertSee('Kajian')
            ->assertSee('Validasi')
            ->assertSee('Tempat Sampah')
            ->assertSee('Pengumuman')
            ->assertSee('Chat AI')
            ->assertSee('Laporan')
            ->assertSee('Hasil Survey')
            ->assertSee('Backup Data')
            ->assertSee('Manajemen User')
            ->assertSee('Pengaturan')
            ->assertSee(route('admin.parents.index', ['typeFilter' => 'teacher']), false)
            ->assertSee('name="viewport" content="width=device-width, initial-scale=1.0"', false)
            ->assertDontSee('user-scalable=no')
            ->assertDontSee('maximum-scale=1.0');

        foreach ([
            'admin.students.index',
            'admin.parents.index',
            'admin.promotion.index',
            'admin.archive.index',
            'admin.teacher-attendance.index',
            'admin.kajian.index',
            'admin.announcements.index',
            'admin.chat-ai',
            'admin.classes.index',
            'admin.reports.index',
            'admin.backups.index',
            'admin.validation.index',
            'admin.validation.trash',
            'admin.users.index',
            'admin.settings',
            'admin.surveys.index',
        ] as $routeName) {
            $response->assertSee(route($routeName), false);
        }
    }

    public function test_admin_dashboard_remains_restricted_to_admin_role(): void
    {
        $this->actingAs($this->userWithRole('panitia'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', $role)->value('id'),
            'is_active' => true,
        ]);
    }
}
