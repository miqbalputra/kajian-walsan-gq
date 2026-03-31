<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Seeding database...');

        // Get roles (already seeded in migration)
        $adminRole = Role::where('name', 'admin')->first();
        $panitiaRole = Role::where('name', 'panitia')->first();
        $kepsekRole = Role::where('name', 'kepsek')->first();
        $waliSantriRole = Role::where('name', 'wali_santri')->first();

        // 1. Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@kajianwalsan.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'phone' => '081234567890',
            'is_active' => true,
        ]);
        $this->command->info('✓ Admin created: admin@kajianwalsan.test');

        // 2. Create Panitia User
        $panitia = User::create([
            'name' => 'Panitia Kajian',
            'username' => 'panitia',
            'email' => 'panitia@kajianwalsan.test',
            'password' => Hash::make('password'),
            'role_id' => $panitiaRole->id,
            'phone' => '081234567891',
            'is_active' => true,
        ]);
        $this->command->info('✓ Panitia created: panitia@kajianwalsan.test');

        // 3. Create Kepsek User
        $kepsek = User::create([
            'name' => 'Kepala Sekolah',
            'username' => 'kepsek',
            'email' => 'kepsek@kajianwalsan.test',
            'password' => Hash::make('password'),
            'role_id' => $kepsekRole->id,
            'phone' => '081234567892',
            'is_active' => true,
        ]);
        $this->command->info('✓ Kepsek created: kepsek@kajianwalsan.test');

        // 4. Create Classes
        $classes = [];
        $classNames = ['Kelas 1A', 'Kelas 1B', 'Kelas 2A', 'Kelas 2B', 'Kelas 3A', 'Kelas 3B'];
        foreach ($classNames as $index => $className) {
            $classes[] = ClassRoom::create([
                'name' => $className,
                'level' => (int) (($index / 2) + 1),
                'capacity' => 30,
                'is_active' => true,
            ]);
        }
        $this->command->info('✓ 6 Classes created');

        // 5. Create 20 Students with Parents
        $firstNames = ['Ahmad', 'Muhammad', 'Abdullah', 'Umar', 'Ali', 'Hasan', 'Husein', 'Ibrahim', 'Yusuf', 'Musa', 'Aisyah', 'Fatimah', 'Khadijah', 'Maryam', 'Zainab', 'Hafshah', 'Safiyyah', 'Ruqayyah', 'Asma', 'Sumayya'];
        $lastNames = ['Pratama', 'Santoso', 'Wijaya', 'Hidayat', 'Rahman', 'Hakim', 'Fauzi', 'Syahputra', 'Ramadhan', 'Maulana', 'Putri', 'Nurhaliza', 'Azzahra', 'Rahmawati', 'Salsabila', 'Khoirunnisa', 'Amalia', 'Safitri', 'Maharani', 'Wulandari'];

        $fatherNames = ['Budi', 'Agus', 'Hendra', 'Dedi', 'Eko', 'Fajar', 'Gunawan', 'Hadi', 'Irfan', 'Joko', 'Kurniawan', 'Lukman', 'Mahmud', 'Nasir', 'Omar', 'Purnomo', 'Qomar', 'Rizki', 'Surya', 'Taufik'];
        $motherNames = ['Siti', 'Nur', 'Dewi', 'Ratna', 'Wati', 'Yuni', 'Ani', 'Binti', 'Citra', 'Dian', 'Eka', 'Fitri', 'Gina', 'Hana', 'Indah', 'Jamilah', 'Kartini', 'Lestari', 'Maya', 'Nadia'];

        for ($i = 0; $i < 20; $i++) {
            // Create Student
            $student = Student::create([
                'nis' => '2025' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'name' => $firstNames[$i] . ' ' . $lastNames[$i],
                'class_id' => $classes[$i % 6]->id,
                'gender' => $i < 10 ? 'L' : 'P',
                'birth_date' => now()->subYears(rand(7, 12))->subDays(rand(1, 365)),
                'is_active' => true,
            ]);

            // Create Father
            $fatherUser = User::create([
                'name' => $fatherNames[$i] . ' ' . $lastNames[$i],
                'username' => 'ayah' . ($i + 1),
                'email' => 'ayah' . ($i + 1) . '@kajianwalsan.test',
                'password' => Hash::make('password'),
                'role_id' => $waliSantriRole->id,
                'phone' => '0812' . str_pad($i + 1, 8, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            $father = ParentModel::create([
                'user_id' => $fatherUser->id,
                'type' => 'father',
                'occupation' => ['Wiraswasta', 'PNS', 'Guru', 'Pedagang', 'Karyawan'][rand(0, 4)],
                'address' => 'Jl. Contoh No. ' . ($i + 1) . ', Kota',
            ]);

            // Create Mother
            $motherUser = User::create([
                'name' => $motherNames[$i] . ' ' . $lastNames[$i],
                'username' => 'ibu' . ($i + 1),
                'email' => 'ibu' . ($i + 1) . '@kajianwalsan.test',
                'password' => Hash::make('password'),
                'role_id' => $waliSantriRole->id,
                'phone' => '0813' . str_pad($i + 1, 8, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            $mother = ParentModel::create([
                'user_id' => $motherUser->id,
                'type' => 'mother',
                'occupation' => ['Ibu Rumah Tangga', 'Guru', 'Dokter', 'Perawat', 'Wiraswasta'][rand(0, 4)],
                'address' => 'Jl. Contoh No. ' . ($i + 1) . ', Kota',
            ]);

            // Link parents to student (pivot table)
            $student->parents()->attach([
                $father->id => ['relationship' => 'biological', 'is_primary_contact' => true],
                $mother->id => ['relationship' => 'biological', 'is_primary_contact' => false],
            ]);
        }
        $this->command->info('✓ 20 Students with 40 Parents created');

        // 6. Create siblings example (students 1-2 share parents)
        $firstStudent = Student::find(1);
        $secondStudent = Student::find(2);
        if ($firstStudent && $secondStudent) {
            // Get first student's parents and attach to second student
            $parentIds = $firstStudent->parents()->pluck('parents.id');
            $secondStudent->parents()->syncWithoutDetaching(
                $parentIds->mapWithKeys(fn($id) => [$id => ['relationship' => 'biological']])->toArray()
            );
            $this->command->info('✓ Sibling relationship created (Students 1 & 2)');
        }

        // 7. Ensure Academic Year exists (may already be seeded in migration)
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $academicYear = AcademicYear::create([
                'name' => '2025/2026',
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
                'is_active' => true,
            ]);
        }
        $this->command->info('✓ Academic Year: ' . $academicYear->name);

        // 8. Create Kajian Event for today
        $kajianEvent = KajianEvent::create([
            'academic_year_id' => $academicYear->id,
            'title' => 'Kajian Rutin Bulanan - Januari 2026',
            'description' => 'Kajian rutin bulanan membahas tema "Mendidik Anak di Era Digital".',
            'speaker' => 'Ustadz Ahmad Fauzi, Lc., M.A.',
            'location' => 'Aula Utama Pesantren',
            'date' => now()->toDateString(),
            'time_start' => '08:00:00',
            'time_end' => '10:00:00',
            'status' => 'open',
            'created_by' => $admin->id,
        ]);
        $this->command->info('✓ Kajian Event created for today: ' . $kajianEvent->title);

        $this->command->newLine();
        $this->command->info('============================================');
        $this->command->info('   DATABASE SEEDING COMPLETED SUCCESSFULLY');
        $this->command->info('============================================');
        $this->command->newLine();
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@kajianwalsan.test', 'password'],
                ['Panitia', 'panitia@kajianwalsan.test', 'password'],
                ['Kepsek', 'kepsek@kajianwalsan.test', 'password'],
                ['Wali Santri (Ayah)', 'ayah1@kajianwalsan.test', 'password'],
                ['Wali Santri (Ibu)', 'ibu1@kajianwalsan.test', 'password'],
            ]
        );
    }
}
