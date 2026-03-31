<?php

namespace App\Imports;

use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\Importable;

class ParentsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    use Importable;

    public int $successCount = 0;
    public int $updateCount = 0;
    public int $skipCount = 0;
    public array $errors = [];
    public array $credentials = [];

    /**
     * Process the imported collection.
     */
    public function collection(Collection $rows)
    {
        $waliSantriRole = Role::where('name', 'wali_santri')->first();

        foreach ($rows as $index => $row) {
            try {
                $rowNumber = $index + 2;

                $name = trim($row['nama'] ?? $row['nama_orang_tua'] ?? $row['name'] ?? '');
                $phone = trim($row['telepon'] ?? $row['phone'] ?? $row['no_hp'] ?? '');
                $email = trim($row['email'] ?? '');
                $studentNis = trim($row['nis_anak'] ?? $row['nis_siswa'] ?? $row['student_nis'] ?? '');

                if (empty($name)) {
                    $this->skipCount++;
                    $this->errors[] = "Baris {$rowNumber}: Nama orang tua kosong, dilewati.";
                    continue;
                }

                // Parse parent type
                $typeRaw = strtolower(trim($row['tipe'] ?? $row['type'] ?? $row['jenis'] ?? 'father'));
                $type = 'father'; // default
                if (in_array($typeRaw, ['ibu', 'mother', 'mom', 'wanita', 'perempuan', 'p', 'f'])) {
                    $type = 'mother';
                }

                // Generate username from name or phone
                $baseUsername = Str::slug($name, '');
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                // Generate email if not provided
                if (empty($email)) {
                    $email = $username . '@walsan.local';
                }

                // Check if user with this email exists
                $existingUser = User::where('email', $email)->first();

                // Generate default password
                $defaultPassword = 'walsan' . substr($phone ?: rand(1000, 9999), -4);

                if ($existingUser) {
                    // Check if parent record exists
                    $existingParent = ParentModel::where('user_id', $existingUser->id)->first();
                    if ($existingParent) {
                        // Link to student if provided
                        $this->linkToStudent($existingParent, $studentNis, $type);
                        $this->updateCount++;
                        continue;
                    }
                }

                // Create user
                $user = $existingUser ?? User::create([
                    'name' => $name,
                    'email' => $email,
                    'username' => $username,
                    'password' => Hash::make($defaultPassword),
                    'role_id' => $waliSantriRole?->id,
                ]);

                // Parse is_single_parent
                $singleParentRaw = strtolower(trim($row['single_parent'] ?? $row['is_single_parent'] ?? ''));
                $isSingleParent = in_array($singleParentRaw, ['ya', 'yes', 'y', '1', 'true']);

                // Create parent record
                $parent = ParentModel::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'is_single_parent' => $isSingleParent,
                    'nik' => trim($row['nik'] ?? null),
                    'occupation' => trim($row['pekerjaan'] ?? $row['occupation'] ?? null),
                    'address' => trim($row['alamat'] ?? $row['address'] ?? null),
                ]);

                // Link to student if NIS provided
                $this->linkToStudent($parent, $studentNis, $type);

                // Store credentials for display
                $this->credentials[] = [
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'password' => $defaultPassword,
                ];

                $this->successCount++;
            } catch (\Exception $e) {
                $this->skipCount++;
                $this->errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }
    }

    /**
     * Link parent to student by NIS.
     */
    private function linkToStudent(ParentModel $parent, string $studentNis, string $type): void
    {
        if (empty($studentNis)) {
            return;
        }

        $student = Student::where('nis', $studentNis)->first();
        if (!$student) {
            return;
        }

        // Check if already linked
        if (!$parent->students()->where('students.id', $student->id)->exists()) {
            $parent->students()->attach($student->id, [
                'relationship' => $type === 'father' ? 'Ayah' : 'Ibu',
                'is_primary_contact' => true,
            ]);
        }
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'nama' => ['nullable'],
            'name' => ['nullable'],
        ];
    }

    /**
     * Get summary message.
     */
    public function getSummary(): string
    {
        $parts = [];
        if ($this->successCount > 0) {
            $parts[] = "{$this->successCount} orang tua baru ditambahkan";
        }
        if ($this->updateCount > 0) {
            $parts[] = "{$this->updateCount} orang tua diperbarui";
        }
        if ($this->skipCount > 0) {
            $parts[] = "{$this->skipCount} baris dilewati";
        }

        return implode(', ', $parts) ?: 'Tidak ada data yang diproses';
    }
}
