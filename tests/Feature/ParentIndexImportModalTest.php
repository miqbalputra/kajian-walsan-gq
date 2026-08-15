<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use App\Livewire\Admin\ParentIndex;
use Tests\TestCase;

class ParentIndexImportModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_combined_import_modal(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin-test',
            'email' => 'admin-test@example.test',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'admin')->value('id'),
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(ParentIndex::class)
            ->call('openStudentFamilyImportModal')
            ->assertSet('showStudentFamilyImportModal', true);
    }
}
