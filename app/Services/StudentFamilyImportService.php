<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentFamilyImportService
{
    public function __construct(
        private readonly ParentLoginAliasService $loginAliases,
        private readonly ParentQrCodeService $qrCodes,
        private readonly ParentArchiveService $parentArchives,
    ) {}

    /**
     * Read and validate an import workbook without changing the database.
     */
    public function preview(string $path): array
    {
        $rows = $this->readRows($path);
        $errors = [];
        $warnings = [];
        $seenNis = [];
        $newParents = [];
        $matchedParents = [];
        $existingStudents = 0;

        foreach ($rows as $row) {
            $nis = $this->nis($row['nis'] ?? '');
            $rowNo = $row['_row'];

            if ($nis === '' || blank($row['nama'] ?? null)) {
                $errors[] = "Baris {$rowNo}: NIS dan nama anak wajib diisi.";
                continue;
            }

            if (isset($seenNis[$nis])) {
                $errors[] = "Baris {$rowNo}: NIS {$nis} muncul lebih dari sekali.";
            }
            $seenNis[$nis] = true;

            $year = $this->academicYear($row['tahun_ajaran'] ?? null);
            if (! $year) {
                $errors[] = "Baris {$rowNo}: Tahun ajaran tidak ditemukan atau belum aktif.";
            }

            $class = $this->classRoom($row['kelas'] ?? null);
            if (! $class) {
                $errors[] = "Baris {$rowNo}: Kelas ".($row['kelas'] ?? '-')." tidak ditemukan.";
            }

            $student = Student::where('nis', $nis)->first();
            if ($student) {
                $existingStudents++;
                if (($student->student_status ?? 'active') === 'graduated') {
                    $errors[] = "Baris {$rowNo}: NIS {$nis} sudah berstatus alumni.";
                }
                if ($student->name && ! $this->sameName($student->name, $row['nama'])) {
                    $errors[] = "Baris {$rowNo}: NIS {$nis} sudah dipakai oleh siswa ".($student->name).".";
                }
            }

            foreach (['father' => 'ayah', 'mother' => 'ibu'] as $type => $prefix) {
                $parentId = $this->integerOrNull($row["parent_id_{$prefix}"] ?? null);
                if ($parentId) {
                    $parent = ParentModel::with('user')->find($parentId);
                    if (! $parent || $parent->type !== $type) {
                        $errors[] = "Baris {$rowNo}: Parent ID {$parentId} bukan profil ".($type === 'father' ? 'ayah' : 'ibu')." yang valid.";
                    } else {
                        $matchedParents[$parent->id] = true;
                    }
                } elseif (filled($row["nama_{$prefix}"] ?? null)) {
                    $key = $this->familyKey($row);
                    $newParents[$key.'|'.$type] = true;
                }
            }
        }

        return [
            'total_rows' => count($rows),
            'new_students' => max(0, count($seenNis) - $existingStudents),
            'existing_students' => $existingStudents,
            'matched_parents' => count($matchedParents),
            'new_parents' => count($newParents),
            'errors' => array_values(array_unique($errors)),
            'warnings' => array_values(array_unique($warnings)),
            'rows' => $rows,
        ];
    }

    /**
     * Import students, enrollment, parent records, login aliases and QR
     * aliases atomically. A failure rolls back the entire batch.
     */
    public function import(string $path): array
    {
        $preview = $this->preview($path);
        if ($preview['errors'] !== []) {
            throw new \RuntimeException(implode(' ', $preview['errors']));
        }

        $rows = $preview['rows'];
        $familyNis = $this->familyCanonicalNis($rows);
        $families = [];
        $touchedParents = [];
        $credentials = [];
        $createdStudents = 0;
        $updatedStudents = 0;
        $createdParents = 0;
        $linkedRelations = 0;
        $warnings = [];

        DB::transaction(function () use (
            $rows,
            $familyNis,
            &$families,
            &$touchedParents,
            &$credentials,
            &$createdStudents,
            &$updatedStudents,
            &$createdParents,
            &$linkedRelations,
            &$warnings,
        ): void {
            $role = Role::where('name', 'wali_santri')->first();
            if (! $role) {
                throw new \RuntimeException('Role wali_santri belum tersedia.');
            }

            foreach ($rows as $row) {
                $nis = $this->nis($row['nis']);
                $class = $this->classRoom($row['kelas']);
                $year = $this->academicYear($row['tahun_ajaran'] ?? null);

                if (! $class || ! $year) {
                    throw new \RuntimeException("Konfigurasi kelas/tahun ajaran untuk NIS {$nis} tidak valid.");
                }

                $student = Student::where('nis', $nis)->first();
                if ($student) {
                    $student->update([
                        'name' => trim((string) $row['nama']),
                        'class_id' => $class->id,
                        'gender' => $this->gender($row['jenis_kelamin'] ?? null),
                        'birth_date' => $this->date($row['tanggal_lahir'] ?? null),
                        'birth_place' => $this->nullable($row['tempat_lahir'] ?? null),
                        'address' => $this->nullable($row['alamat'] ?? null),
                        'student_status' => 'active',
                        'is_active' => true,
                    ]);
                    $updatedStudents++;
                } else {
                    $student = Student::create([
                        'nis' => $nis,
                        'name' => trim((string) $row['nama']),
                        'class_id' => $class->id,
                        'gender' => $this->gender($row['jenis_kelamin'] ?? null),
                        'birth_date' => $this->date($row['tanggal_lahir'] ?? null),
                        'birth_place' => $this->nullable($row['tempat_lahir'] ?? null),
                        'address' => $this->nullable($row['alamat'] ?? null),
                        'student_status' => 'active',
                        'is_active' => true,
                    ]);
                    $createdStudents++;
                }

                StudentEnrollment::updateOrCreate(
                    ['student_id' => $student->id, 'academic_year_id' => $year->id],
                    [
                        'class_id' => $class->id,
                        'class_name' => $class->name,
                        'class_level' => $class->level,
                        'status' => 'enrolled',
                        'started_at' => $year->start_date,
                    ],
                );

                foreach (['father' => 'ayah', 'mother' => 'ibu'] as $type => $prefix) {
                    $parent = $this->resolveParent(
                        $row,
                        $type,
                        $prefix,
                        $familyNis[$this->familyKey($row)] ?? $nis,
                        $families,
                        $credentials,
                        $createdParents,
                        $warnings,
                    );

                    if (! $parent) {
                        continue;
                    }

                    $parent->students()->syncWithoutDetaching([
                        $student->id => [
                            'relationship' => $this->relationship($row["hubungan_{$prefix}"] ?? null),
                            'is_primary_contact' => $this->boolean($row["primary_contact_{$prefix}"] ?? null, $type === 'father'),
                        ],
                    ]);

                    $touchedParents[$parent->id] = $parent->fresh(['user', 'students']);
                    $linkedRelations++;
                }
            }

            foreach ($touchedParents as $id => $parent) {
                $aliasCredentials = $this->loginAliases->syncForParent($parent);
                foreach ($aliasCredentials as $aliasCredential) {
                    if ($aliasCredential['created']) {
                        $credentials[] = [
                            'type' => 'login_alias',
                            'nama' => $parent->user?->name,
                            'parent_id' => $parent->id,
                            'username' => $aliasCredential['username'],
                            'password' => $aliasCredential['password'],
                            'student_id' => $aliasCredential['student_id'],
                        ];
                    }
                }

                $parent->syncQrCode();
                $this->parentArchives->syncForParent($parent, null);
            }
        });

        return [
            'created_students' => $createdStudents,
            'updated_students' => $updatedStudents,
            'created_parents' => $createdParents,
            'linked_relations' => $linkedRelations,
            'credentials' => $credentials,
            'warnings' => array_values(array_unique($warnings)),
        ];
    }

    private function resolveParent(
        array $row,
        string $type,
        string $prefix,
        string $canonicalNis,
        array &$families,
        array &$credentials,
        int &$createdParents,
        array &$warnings,
    ): ?ParentModel {
        $parentId = $this->integerOrNull($row["parent_id_{$prefix}"] ?? null);
        if ($parentId) {
            $parent = ParentModel::with('user')->findOrFail($parentId);
            if ($parent->type !== $type) {
                throw new \RuntimeException("Parent ID {$parentId} tidak sesuai tipe.");
            }

            return $parent;
        }

        $familyKey = $this->familyKey($row);
        $cacheKey = $familyKey.'|'.$type;
        if (isset($families[$cacheKey])) {
            return $families[$cacheKey];
        }

        $name = trim((string) ($row["nama_{$prefix}"] ?? ''));
        if ($name === '') {
            return null;
        }

        $username = ($type === 'father' ? 'BPK' : 'IBU').$canonicalNis;
        $existingUser = User::whereRaw('LOWER(username) = ?', [Str::lower($username)])->first();
        if ($existingUser) {
            $existingParent = ParentModel::where('user_id', $existingUser->id)->first();
            if (! $existingParent || $existingParent->type !== $type) {
                throw new \RuntimeException("Username {$username} sudah dipakai akun yang tidak sesuai.");
            }
            $families[$cacheKey] = $existingParent->load('user');

            return $families[$cacheKey];
        }

        $email = $this->nullable($row["email_{$prefix}"] ?? null);
        $sourceEmail = $email;
        if ($email && User::whereRaw('LOWER(email) = ?', [Str::lower($email)])->exists()) {
            // A family may intentionally provide one shared email for both
            // parents. The users.email column is unique, so keep the first
            // account on the supplied email and give the next new account a
            // deterministic internal address instead of aborting the batch.
            $email = $this->uniqueEmail($username.'@kajian.griyaquran.web.id');
            $warnings[] = "Email {$sourceEmail} dipakai lebih dari satu akun atau sudah terdaftar; {$name} dibuat dengan email unik {$email}. Username/password tetap mengikuti format NIS.";
        }

        $email = $email ?: $this->uniqueEmail($username.'@kajian.griyaquran.web.id');
        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $username,
            'role_id' => Role::where('name', 'wali_santri')->value('id'),
            'phone' => $this->nullable($row["telepon_{$prefix}"] ?? null),
            'is_active' => true,
        ]);

        $parent = ParentModel::create([
            'user_id' => $user->id,
            'type' => $type,
            'nik' => $this->nullable($row["nik_{$prefix}"] ?? null),
            'occupation' => $this->nullable($row["pekerjaan_{$prefix}"] ?? null),
            'address' => $this->nullable($row["alamat_{$prefix}"] ?? ($row['alamat'] ?? null)),
        ]);

        $families[$cacheKey] = $parent->load('user');
        $createdParents++;
        $credentials[] = [
            'type' => $type === 'father' ? 'Ayah' : 'Ibu',
            'nama' => $name,
            'parent_id' => $parent->id,
            'username' => $username,
            'password' => $username,
            'email' => $email,
            'student_id' => null,
        ];

        return $families[$cacheKey];
    }

    private function readRows(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $rawRows = $sheet->toArray(null, true, true, false);
        $headers = array_map(fn ($header) => $this->header((string) $header), array_shift($rawRows) ?: []);
        $rows = [];

        foreach ($rawRows as $index => $values) {
            $row = ['_row' => $index + 2];
            foreach ($headers as $column => $header) {
                if ($header !== '') {
                    $row[$header] = $values[$column] ?? null;
                }
            }
            if (count(array_filter($row, fn ($value, $key) => $key !== '_row' && filled($value), ARRAY_FILTER_USE_BOTH)) > 0) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function familyCanonicalNis(array $rows): array
    {
        $families = [];
        foreach ($rows as $row) {
            $key = $this->familyKey($row);
            $nis = $this->nis($row['nis'] ?? '');
            $level = $this->classLevel($row['kelas'] ?? null);
            $families[$key][] = [$level ?? 99, $nis];
        }

        return collect($families)->mapWithKeys(function (array $values, string $key) {
            usort($values, fn ($a, $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);

            return [$key => $values[0][1]];
        })->all();
    }

    private function familyKey(array $row): string
    {
        if (filled($row['family_key'] ?? null)) {
            return trim((string) $row['family_key']);
        }

        return Str::lower(implode('|', [
            $this->compact($row['nama_ayah'] ?? ''),
            $this->compact($row['telepon_ayah'] ?? ''),
            $this->compact($row['nama_ibu'] ?? ''),
            $this->compact($row['telepon_ibu'] ?? ''),
        ]));
    }

    private function academicYear(?string $name): ?AcademicYear
    {
        $name = trim((string) $name);

        return $name !== ''
            ? AcademicYear::where('name', $name)->first()
            : AcademicYear::active();
    }

    private function classRoom(?string $name): ?ClassRoom
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        return ClassRoom::where('name', $name)->first()
            ?? ClassRoom::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first();
    }

    private function classLevel(?string $name): ?int
    {
        if (preg_match('/Mustawa\s+(\d+)/i', (string) $name, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function gender(?string $gender): ?string
    {
        $gender = Str::lower(trim((string) $gender));

        return in_array($gender, ['l', 'laki-laki', 'laki', 'putra', 'male'], true)
            ? 'L'
            : (in_array($gender, ['p', 'perempuan', 'putri', 'female'], true) ? 'P' : null);
    }

    private function date(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        try {
            return is_numeric($value)
                ? ExcelDate::excelToDateTimeObject($value)->format('Y-m-d')
                : \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function relationship(?string $relationship): string
    {
        $relationship = Str::lower(trim((string) $relationship));

        return in_array($relationship, ['guardian', 'wali'], true)
            ? 'guardian'
            : (in_array($relationship, ['step', 'tiri'], true) ? 'step' : 'biological');
    }

    private function boolean(mixed $value, bool $default): bool
    {
        if (blank($value)) {
            return $default;
        }

        return in_array(Str::lower(trim((string) $value)), ['1', 'ya', 'yes', 'true'], true);
    }

    private function nis(mixed $value): string
    {
        return trim((string) $value);
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' || in_array(Str::lower($value), ['-', 'tidak ada', 'tidak'], true) ? null : $value;
    }

    private function compact(mixed $value): string
    {
        return preg_replace('/[^a-z0-9]/', '', Str::lower(trim((string) $value))) ?: '';
    }

    private function sameName(?string $left, ?string $right): bool
    {
        return $this->compact($left) === $this->compact($right);
    }

    private function integerOrNull(mixed $value): ?int
    {
        return filled($value) && (int) $value > 0 ? (int) $value : null;
    }

    private function header(string $value): string
    {
        return Str::of($value)->lower()->ascii()->replaceMatches('/[^a-z0-9]+/', '_')->trim('_')->toString();
    }

    private function uniqueEmail(string $email): string
    {
        $base = Str::before($email, '@');
        $domain = Str::after($email, '@');
        $candidate = $email;
        $counter = 1;

        while (User::whereRaw('LOWER(email) = ?', [Str::lower($candidate)])->exists()) {
            $candidate = $base.$counter.'@'.$domain;
            $counter++;
        }

        return $candidate;
    }
}
