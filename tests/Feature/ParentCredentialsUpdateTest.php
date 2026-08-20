<?php

namespace Tests\Feature;

use App\Livewire\Admin\ParentIndex;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\UserLoginAlias;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ParentCredentialsUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_credentials_without_replacing_a_legacy_invalid_phone_value(): void
    {
        $admin = $this->makeUser('admin', 'admin-test@example.test', 'admin');
        $parentUser = $this->makeUser('ayah-lama', 'ayah-lama@example.test', 'wali_santri', 'Hpbersamaistri');
        $parent = ParentModel::create([
            'user_id' => $parentUser->id,
            'type' => 'father',
        ]);
        $student = Student::create([
            'nis' => '20250001',
            'name' => 'Santri Test',
            'gender' => 'L',
            'is_active' => true,
        ]);
        $parent->students()->attach($student->id, [
            'relationship' => 'biological',
            'is_primary_contact' => true,
        ]);
        $alias = UserLoginAlias::create([
            'user_id' => $parentUser->id,
            'source_student_id' => $student->id,
            'username' => 'BPK20250001',
            'password' => 'password-lama',
            'kind' => 'child_alias',
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(ParentIndex::class)
            ->call('openEditModal', $parent->id)
            ->set('username', 'ayah-baru')
            ->set('password', 'password-baru')
            ->call('save')
            ->assertSet('showModal', false)
            ->assertHasNoErrors();

        $parentUser->refresh();
        $alias->refresh();

        $this->assertSame('ayah-baru', $parentUser->username);
        $this->assertTrue(Hash::check('password-baru', $parentUser->password));
        $this->assertTrue(Hash::check('password-baru', $alias->password));

        Livewire::test(ParentIndex::class)
            ->call('openEditModal', $parent->id)
            ->set('phone', 'nomor-tidak-valid')
            ->call('save')
            ->assertHasErrors(['phone']);
    }

    private function makeUser(string $username, string $email, string $roleName, string $phone = '081234567890'): User
    {
        return User::create([
            'name' => $username,
            'username' => $username,
            'email' => $email,
            'password' => 'password-lama',
            'role_id' => Role::where('name', $roleName)->value('id'),
            'phone' => $phone,
            'is_active' => true,
        ]);
    }
}
