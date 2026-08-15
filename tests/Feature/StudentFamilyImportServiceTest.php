<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\UserLoginAlias;
use App\Services\StudentFamilyImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentFamilyImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_does_not_change_database_and_import_links_old_parent_and_creates_new_parent(): void
    {
        $role = Role::where('name', 'wali_santri')->firstOrFail();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);
        $class = ClassRoom::create([
            'name' => 'Mustawa 1 Ikhwan',
            'level' => '1',
            'is_active' => true,
        ]);

        $oldUser = User::create([
            'name' => 'Bapak Lama',
            'username' => 'BPK-OLD',
            'email' => 'bapak-lama@example.test',
            'password' => Hash::make('password-lama'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);
        $oldParent = ParentModel::create([
            'user_id' => $oldUser->id,
            'type' => 'father',
            'nik' => '3200000000000001',
        ]);
        $oldStudent = Student::create([
            'nis' => 'OLD-001',
            'name' => 'Kakak Lama',
            'class_id' => $class->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
        $oldParent->students()->attach($oldStudent->id);

        $path = $this->makeCsv([
            'source_registration_id', 'nis', 'nama', 'kelas', 'tahun_ajaran',
            'jenis_kelamin', 'family_key', 'parent_id_ayah', 'nama_ayah',
            'email_ibu', 'nama_ibu', 'hubungan_ibu', 'primary_contact_ibu',
        ], [[
            '59', '0214470333', 'Abdullah Izzan', 'Mustawa 1 Ikhwan', '2026/2027',
            'L', 'family-abdullah-izzan', (string) $oldParent->id, 'Bapak Lama',
            'ibu-baru@example.test', 'Ibu Baru', 'biological', 'false',
        ]]);

        $preview = app(StudentFamilyImportService::class)->preview($path);

        $this->assertSame(1, $preview['total_rows']);
        $this->assertSame(1, $preview['new_students']);
        $this->assertSame(1, $preview['matched_parents']);
        $this->assertSame(1, $preview['new_parents']);
        $this->assertSame([], $preview['errors']);
        $this->assertDatabaseMissing('students', ['nis' => '0214470333']);
        $this->assertDatabaseMissing('users', ['username' => 'IBU0214470333']);

        $result = app(StudentFamilyImportService::class)->import($path);

        $this->assertSame(1, $result['created_students']);
        $this->assertSame(1, $result['created_parents']);
        $this->assertSame(2, $result['linked_relations']);
        $this->assertDatabaseHas('students', [
            'nis' => '0214470333',
            'name' => 'Abdullah Izzan',
            'class_id' => $class->id,
            'student_status' => 'active',
        ]);
        $newStudent = Student::where('nis', '0214470333')->firstOrFail();

        $this->assertDatabaseHas('parent_student', [
            'parent_id' => $oldParent->id,
            'student_id' => $newStudent->id,
        ]);
        $newMother = ParentModel::where('type', 'mother')
            ->whereHas('user', fn ($query) => $query->where('username', 'IBU0214470333'))
            ->firstOrFail();
        $this->assertDatabaseHas('parent_student', [
            'parent_id' => $newMother->id,
            'student_id' => $newStudent->id,
        ]);

        $alias = UserLoginAlias::where('username', 'BPK0214470333')->firstOrFail();
        $this->assertSame($oldUser->id, $alias->user_id);
        $this->assertTrue(Hash::check('BPK0214470333', $alias->getRawOriginal('password')));
        $this->assertDatabaseHas('parent_qr_codes', [
            'parent_id' => $oldParent->id,
            'code' => 'A0214470333',
            'source_student_id' => $newStudent->id,
            'is_active' => 1,
        ]);

        $this->post('/login', [
            'username' => 'BPK0214470333',
            'password' => 'BPK0214470333',
        ])->assertRedirect();
        $this->assertAuthenticatedAs($oldUser->fresh());
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, array<int, string>> $rows
     */
    private function makeCsv(array $headers, array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'student-family-import-');
        $handle = fopen($path, 'wb');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return $path;
    }
}
