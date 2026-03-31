<?php

namespace App\Imports;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    use Importable;

    public int $successCount = 0;
    public int $updateCount = 0;
    public int $skipCount = 0;
    public array $errors = [];

    /**
     * Process the imported collection.
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $rowNumber = $index + 2; // +2 because index starts at 0 and header is row 1

                // Clean and validate NIS
                $nis = trim($row['nis'] ?? '');
                $name = trim($row['nama'] ?? $row['name'] ?? '');

                if (empty($nis) || empty($name)) {
                    $this->skipCount++;
                    $this->errors[] = "Baris {$rowNumber}: NIS atau Nama kosong, dilewati.";
                    continue;
                }

                // Find or determine class
                $classId = null;
                $className = trim($row['kelas'] ?? $row['class'] ?? '');
                if (!empty($className)) {
                    $class = ClassRoom::where('name', $className)
                        ->orWhere('name', 'like', "%{$className}%")
                        ->first();
                    $classId = $class?->id;
                }

                // Parse gender
                $genderRaw = strtolower(trim($row['jenis_kelamin'] ?? $row['gender'] ?? ''));
                $gender = null;
                if (in_array($genderRaw, ['l', 'laki-laki', 'laki', 'male', 'm', 'putra'])) {
                    $gender = 'male';
                } elseif (in_array($genderRaw, ['p', 'perempuan', 'female', 'f', 'putri', 'wanita'])) {
                    $gender = 'female';
                }

                // Parse birth date
                $birthDate = null;
                $birthDateRaw = $row['tanggal_lahir'] ?? $row['birth_date'] ?? null;
                if ($birthDateRaw) {
                    try {
                        if (is_numeric($birthDateRaw)) {
                            // Excel serial date
                            $birthDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($birthDateRaw);
                        } else {
                            $birthDate = \Carbon\Carbon::parse($birthDateRaw);
                        }
                    } catch (\Exception $e) {
                        // Ignore date parsing errors
                    }
                }

                // Check if student already exists
                $existingStudent = Student::where('nis', $nis)->first();

                $studentData = [
                    'name' => $name,
                    'class_id' => $classId,
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                    'birth_place' => trim($row['tempat_lahir'] ?? $row['birth_place'] ?? null),
                    'address' => trim($row['alamat'] ?? $row['address'] ?? null),
                    'guardian_name' => trim($row['nama_wali'] ?? $row['guardian_name'] ?? null),
                    'guardian_phone' => trim($row['telepon_wali'] ?? $row['guardian_phone'] ?? null),
                    'guardian_relationship' => trim($row['hubungan_wali'] ?? $row['guardian_relationship'] ?? null),
                    'is_active' => true,
                ];

                if ($existingStudent) {
                    // Update existing student
                    $existingStudent->update(array_filter($studentData, fn($v) => $v !== null && $v !== ''));
                    $this->updateCount++;
                } else {
                    // Create new student
                    Student::create(array_merge(['nis' => $nis], $studentData));
                    $this->successCount++;
                }
            } catch (\Exception $e) {
                $this->skipCount++;
                $this->errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'nis' => ['nullable'],
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
            $parts[] = "{$this->successCount} siswa baru ditambahkan";
        }
        if ($this->updateCount > 0) {
            $parts[] = "{$this->updateCount} siswa diperbarui";
        }
        if ($this->skipCount > 0) {
            $parts[] = "{$this->skipCount} baris dilewati";
        }

        return implode(', ', $parts) ?: 'Tidak ada data yang diproses';
    }
}
