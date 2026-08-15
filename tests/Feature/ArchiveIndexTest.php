<?php

namespace Tests\Feature;

use App\Livewire\Admin\ArchiveIndex;
use App\Models\ClassRoom;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ArchiveIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_archive_and_restore_from_archive_screen(): void
    {
        $admin = User::create([
            'name' => 'Admin Arsip',
            'username' => 'admin-arsip',
            'email' => 'admin-arsip@example.test',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'admin')->value('id'),
            'is_active' => true,
        ]);
        $class = ClassRoom::create(['name' => 'Mustawa 1 UI', 'level' => '1', 'is_active' => true]);
        $student = Student::create([
            'nis' => 'UI-ARCH-001',
            'name' => 'Santri UI Arsip',
            'class_id' => $class->id,
            'gender' => 'L',
            'student_status' => 'active',
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(ArchiveIndex::class)
            ->set('statusFilter', 'active')
            ->call('openArchiveModal', $student->id)
            ->set('exitType', 'transferred')
            ->set('reason', 'Pindah kota')
            ->call('archiveStudent')
            ->assertSet('showArchiveModal', false);

        $this->assertDatabaseHas('students', ['id' => $student->id, 'student_status' => 'transferred', 'is_active' => 0]);

        Livewire::test(ArchiveIndex::class)
            ->call('openRestoreModal', $student->id)
            ->set('restoreAcademicYearId', (string) \App\Models\AcademicYear::active()->id)
            ->set('restoreClassId', (string) $class->id)
            ->call('restoreStudent')
            ->assertSet('showRestoreModal', false);

        $this->assertDatabaseHas('students', ['id' => $student->id, 'student_status' => 'active', 'is_active' => 1, 'class_id' => $class->id]);
    }
}
