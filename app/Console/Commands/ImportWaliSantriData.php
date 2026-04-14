<?php

namespace App\Console\Commands;

use App\Models\ClassRoom;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportWaliSantriData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:wali-santri
                            {file? : Path to the Excel file (default: data_kajian_walsan.xls.xlsx in project root)}
                            {--dry-run : Run without actually inserting data}
                            {--export-credentials : Export credentials to CSV after import}';

    /**
     * The console command description.
     */
    protected $description = 'Import wali santri data from Excel file into the database';

    /**
     * Mustawa level mapping for sorting (higher = older student).
     */
    private array $mustawaLevelMap = [
        'Mustawa 1 Ikhwan' => 1,
        'Mustawa 1 Akhwat' => 1,
        'Mustawa 2 Ikhwan' => 2,
        'Mustawa 2 Akhwat' => 2,
        'Mustawa 3 Ikhwan' => 3,
        'Mustawa 3 Akhwat' => 3,
        'Mustawa 4 Ikhwan' => 4,
        'Mustawa 4 Akhwat' => 4,
        'Mustawa 5 Ikhwan' => 5,
        'Mustawa 5 Akhwat' => 5,
        'Mustawa 6 Ikhwan' => 6,
        'Mustawa 6 Akhwat' => 6,
    ];

    /**
     * Column index mapping — simplified header names mapped to Excel column index (0-based).
     */
    private array $colMap = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file')
            ?? base_path('data_kajian_walsan.xls.xlsx');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return self::FAILURE;
        }

        $isDryRun = $this->option('dry-run');
        $exportCredentials = $this->option('export-credentials');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE — no data will be written to the database.');
        }

        $this->info("📂 Reading Excel file: {$filePath}");

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $highestCol = $sheet->getHighestColumn();

            $this->info("   Rows: {$highestRow} | Columns: {$highestCol}");

            // Build column mapping from header row
            $this->buildColumnMap($sheet);

            // Parse all student rows
            $rows = [];
            for ($r = 2; $r <= $highestRow; $r++) {
                $row = $this->parseRow($sheet, $r);
                if ($row && !empty($row['nis'])) {
                    $rows[] = $row;
                }
            }

            $this->info("📊 Parsed {$highestRow} rows, found " . count($rows) . " valid student records.");

            // Group by parent (deduplicate families with multiple children)
            $families = $this->groupByFamily($rows);
            $this->info("👨‍👩‍👧‍👦 Found " . count($families) . " unique families.");

            $multiChildFamilies = collect($families)->filter(fn($f) => count($f['children']) > 1);
            if ($multiChildFamilies->isNotEmpty()) {
                $this->info("   └─ {$multiChildFamilies->count()} families have multiple children.");
            }

            if ($isDryRun) {
                $this->displayDryRunSummary($families);
                return self::SUCCESS;
            }

            // Perform import within a transaction
            $credentials = [];

            DB::beginTransaction();
            try {
                // Step 1: Create classes
                $classMap = $this->createClasses();

                // Step 2: Create students
                $studentMap = $this->createStudents($rows, $classMap);

                // Step 3: Create parent users + parent records + link to students
                $credentials = $this->createParentsAndUsers($families, $studentMap);

                DB::commit();
                $this->newLine();
                $this->info('✅ Import completed successfully!');
                $this->displaySummary($credentials);

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("❌ Import failed: {$e->getMessage()}");
                Log::error('Import wali santri failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return self::FAILURE;
            }

            // Export credentials if requested
            if ($exportCredentials && !empty($credentials)) {
                $this->exportCredentialsToCsv($credentials);
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error reading file: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    /**
     * Build column mapping from header row.
     */
    private function buildColumnMap($sheet): void
    {
        $headerRow = $sheet->rangeToArray('A1:AF1', null, true, true, false)[0];

        $mapping = [
            'nama'              => 'Nama Lengkap Ananda',
            'kelas'             => 'Kelas',
            'nis'               => 'NIS',
            'jenis_kelamin'     => 'Jenis Kelamin',
            'tempat_lahir'      => 'Tempat Lahir',
            'tanggal_lahir'     => 'Tanggal Lahir',
            'alamat'            => 'Alamat Lengkap',
            'nama_ayah'         => 'Nama Lengkap Ayah',
            'nik_ayah'          => 'NIK AYAH',
            'hp_ayah'           => 'Nomor HP Ayah',
            'pekerjaan_ayah'    => 'Pekerjaan Ayah',
            'email_ayah'        => 'Email Ayah',
            'nama_ibu'          => 'Nama Lengkap Ibu',
            'nik_ibu'           => 'NIK IBU',
            'hp_ibu'            => 'Nomor HP Ibu',
            'pekerjaan_ibu'     => 'Pekerjaan Ibu',
            'email_ibu'         => 'Email Ibu',
            'nama_wali'         => 'Nama Lengkap Wali',
        ];

        foreach ($mapping as $key => $search) {
            foreach ($headerRow as $colIdx => $header) {
                if ($header === null) continue;
                // Match by first line of the header (headers may be multiline)
                $firstLine = explode("\r\n", $header)[0];
                $firstLine = explode("\n", $firstLine)[0];
                $firstLine = trim($firstLine);
                if (str_contains($firstLine, $search)) {
                    $this->colMap[$key] = $colIdx;
                    break;
                }
            }
        }

        $this->info("   Column mapping established for " . count($this->colMap) . " fields.");
    }

    /**
     * Parse a single row of data.
     */
    private function parseRow($sheet, int $rowNum): ?array
    {
        $getValue = function (string $key) use ($sheet, $rowNum): ?string {
            if (!isset($this->colMap[$key])) return null;
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($this->colMap[$key] + 1);
            $cell = $sheet->getCell("{$colLetter}{$rowNum}");
            $value = $cell->getValue();
            if ($value === null || $value === '') return null;
            return trim((string) $value);
        };

        $nis = $getValue('nis');
        if (!$nis) return null;

        // Parse tanggal lahir (Excel serial date)
        $tanggalLahir = null;
        if (isset($this->colMap['tanggal_lahir'])) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($this->colMap['tanggal_lahir'] + 1);
            $cell = $sheet->getCell("{$colLetter}{$rowNum}");
            $rawDate = $cell->getValue();
            if ($rawDate && is_numeric($rawDate)) {
                try {
                    $dateObj = ExcelDate::excelToDateTimeObject((int) $rawDate);
                    $tanggalLahir = $dateObj->format('Y-m-d');
                } catch (\Exception $e) {
                    // Skip invalid date
                }
            }
        }

        // Clean gender
        $jenisKelamin = $getValue('jenis_kelamin');
        $gender = null;
        if ($jenisKelamin) {
            $gender = str_contains(strtolower($jenisKelamin), 'laki') ? 'L' : 'P';
        }

        // Clean email — invalidate obvious non-emails
        $emailAyah = $this->cleanEmail($getValue('email_ayah'));
        $emailIbu = $this->cleanEmail($getValue('email_ibu'));

        // Clean phone numbers
        $hpAyah = $this->cleanPhone($getValue('hp_ayah'));
        $hpIbu = $this->cleanPhone($getValue('hp_ibu'));

        return [
            'nis'            => $nis,
            'nama'           => $this->cleanName($getValue('nama')),
            'kelas'          => trim($getValue('kelas') ?? ''),
            'gender'         => $gender,
            'tempat_lahir'   => $this->cleanName($getValue('tempat_lahir')),
            'tanggal_lahir'  => $tanggalLahir,
            'alamat'         => $getValue('alamat'),
            'nama_ayah'      => $this->cleanName($getValue('nama_ayah')),
            'nik_ayah'       => $getValue('nik_ayah'),
            'hp_ayah'        => $hpAyah,
            'pekerjaan_ayah' => $getValue('pekerjaan_ayah'),
            'email_ayah'     => $emailAyah,
            'nama_ibu'       => $this->cleanName($getValue('nama_ibu')),
            'nik_ibu'        => $getValue('nik_ibu'),
            'hp_ibu'         => $hpIbu,
            'pekerjaan_ibu'  => $getValue('pekerjaan_ibu'),
            'email_ibu'      => $emailIbu,
            'nama_wali'      => $this->cleanName($getValue('nama_wali')),
        ];
    }

    /**
     * Group rows by family (same father + mother name).
     */
    private function groupByFamily(array $rows): array
    {
        $families = [];

        foreach ($rows as $row) {
            // Create family key from normalized parent names
            $fatherKey = mb_strtolower(trim($row['nama_ayah'] ?? ''));
            $motherKey = mb_strtolower(trim($row['nama_ibu'] ?? ''));
            $familyKey = "{$fatherKey}|||{$motherKey}";

            if (!isset($families[$familyKey])) {
                $families[$familyKey] = [
                    'nama_ayah'      => $row['nama_ayah'],
                    'nik_ayah'       => $row['nik_ayah'],
                    'hp_ayah'        => $row['hp_ayah'],
                    'pekerjaan_ayah' => $row['pekerjaan_ayah'],
                    'email_ayah'     => $row['email_ayah'],
                    'nama_ibu'       => $row['nama_ibu'],
                    'nik_ibu'        => $row['nik_ibu'],
                    'hp_ibu'         => $row['hp_ibu'],
                    'pekerjaan_ibu'  => $row['pekerjaan_ibu'],
                    'email_ibu'      => $row['email_ibu'],
                    'alamat'         => $row['alamat'],
                    'children'       => [],
                ];
            }

            $families[$familyKey]['children'][] = [
                'nis'   => $row['nis'],
                'nama'  => $row['nama'],
                'kelas' => $row['kelas'],
            ];

            // Update parent data if current data is more complete
            if (empty($families[$familyKey]['email_ayah']) && !empty($row['email_ayah'])) {
                $families[$familyKey]['email_ayah'] = $row['email_ayah'];
            }
            if (empty($families[$familyKey]['email_ibu']) && !empty($row['email_ibu'])) {
                $families[$familyKey]['email_ibu'] = $row['email_ibu'];
            }
            if (empty($families[$familyKey]['hp_ayah']) && !empty($row['hp_ayah'])) {
                $families[$familyKey]['hp_ayah'] = $row['hp_ayah'];
            }
            if (empty($families[$familyKey]['hp_ibu']) && !empty($row['hp_ibu'])) {
                $families[$familyKey]['hp_ibu'] = $row['hp_ibu'];
            }
        }

        // Sort children within each family by Mustawa level (highest first = oldest)
        foreach ($families as &$family) {
            usort($family['children'], function ($a, $b) {
                $levelA = $this->mustawaLevelMap[$a['kelas']] ?? 0;
                $levelB = $this->mustawaLevelMap[$b['kelas']] ?? 0;
                return $levelB <=> $levelA; // Descending — highest mustawa first
            });
        }

        return $families;
    }

    /**
     * Create classes from the data.
     */
    private function createClasses(): array
    {
        $this->info('📚 Creating classes...');
        $classMap = [];

        foreach ($this->mustawaLevelMap as $name => $level) {
            $class = ClassRoom::firstOrCreate(
                ['name' => $name],
                [
                    'level'    => (string) $level,
                    'capacity' => 25,
                    'is_active' => true,
                ]
            );
            $classMap[$name] = $class->id;
            $this->line("   ✓ {$name} (ID: {$class->id})");
        }

        return $classMap;
    }

    /**
     * Create student records.
     */
    private function createStudents(array $rows, array $classMap): array
    {
        $this->info('👦 Creating students...');
        $studentMap = []; // NIS => student_id
        $bar = $this->output->createProgressBar(count($rows));

        foreach ($rows as $row) {
            $classId = $classMap[$row['kelas']] ?? null;

            $student = Student::firstOrCreate(
                ['nis' => $row['nis']],
                [
                    'name'        => $row['nama'],
                    'class_id'    => $classId,
                    'gender'      => $row['gender'],
                    'birth_date'  => $row['tanggal_lahir'],
                    'birth_place' => $row['tempat_lahir'],
                    'address'     => $row['alamat'],
                    'guardian_name' => $row['nama_wali'],
                    'is_active'   => true,
                ]
            );

            $studentMap[$row['nis']] = $student->id;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("   Created " . count($studentMap) . " students.");

        return $studentMap;
    }

    /**
     * Create parent user accounts, parent records, and link to students.
     */
    private function createParentsAndUsers(array $families, array $studentMap): array
    {
        $this->info('👨‍👩‍👧 Creating parent accounts...');
        $waliSantriRole = Role::where('name', 'wali_santri')->first();
        if (!$waliSantriRole) {
            throw new \RuntimeException('Role "wali_santri" not found. Please run migrations first.');
        }

        $credentials = [];
        $bar = $this->output->createProgressBar(count($families) * 2);

        foreach ($families as $family) {
            // Determine the oldest child's NIS for username
            $oldestChild = $family['children'][0]; // Already sorted descending by mustawa level
            $nisForUsername = $oldestChild['nis'];

            // Collect all child student IDs for this family
            $childStudentIds = [];
            foreach ($family['children'] as $child) {
                if (isset($studentMap[$child['nis']])) {
                    $childStudentIds[] = $studentMap[$child['nis']];
                }
            }

            // === Create Father account ===
            if (!empty($family['nama_ayah'])) {
                $usernameAyah = 'A' . $nisForUsername;
                $emailAyah = $family['email_ayah'] ?: $usernameAyah . '@kajian.griyaquran.web.id';

                // Check for duplicate email — append unique suffix if needed
                $emailAyah = $this->ensureUniqueEmail($emailAyah);

                $userAyah = User::create([
                    'name'     => $family['nama_ayah'],
                    'username' => $usernameAyah,
                    'email'    => $emailAyah,
                    'password' => $usernameAyah, // Will be hashed by cast
                    'role_id'  => $waliSantriRole->id,
                    'phone'    => $family['hp_ayah'],
                    'is_active' => true,
                ]);

                $parentAyah = ParentModel::create([
                    'user_id'    => $userAyah->id,
                    'type'       => 'father',
                    'nik'        => $family['nik_ayah'],
                    'occupation' => $family['pekerjaan_ayah'],
                    'address'    => $family['alamat'],
                ]);

                // Link father to all children
                foreach ($childStudentIds as $studentId) {
                    $parentAyah->students()->attach($studentId, [
                        'relationship'     => 'biological',
                        'is_primary_contact' => true,
                    ]);
                }

                $childNames = collect($family['children'])->pluck('nama')->implode(', ');
                $credentials[] = [
                    'type'        => 'Ayah',
                    'nama'        => $family['nama_ayah'],
                    'username'    => $usernameAyah,
                    'password'    => $usernameAyah,
                    'email'       => $emailAyah,
                    'phone'       => $family['hp_ayah'],
                    'anak'        => $childNames,
                    'nis_anak'    => collect($family['children'])->pluck('nis')->implode(', '),
                ];
            }
            $bar->advance();

            // === Create Mother account ===
            if (!empty($family['nama_ibu'])) {
                $usernameIbu = 'B' . $nisForUsername;
                $emailIbu = $family['email_ibu'] ?: $usernameIbu . '@kajian.griyaquran.web.id';

                // Check for duplicate email
                $emailIbu = $this->ensureUniqueEmail($emailIbu);

                $userIbu = User::create([
                    'name'     => $family['nama_ibu'],
                    'username' => $usernameIbu,
                    'email'    => $emailIbu,
                    'password' => $usernameIbu, // Will be hashed by cast
                    'role_id'  => $waliSantriRole->id,
                    'phone'    => $family['hp_ibu'],
                    'is_active' => true,
                ]);

                $parentIbu = ParentModel::create([
                    'user_id'    => $userIbu->id,
                    'type'       => 'mother',
                    'nik'        => $family['nik_ibu'],
                    'occupation' => $family['pekerjaan_ibu'],
                    'address'    => $family['alamat'],
                ]);

                // Link mother to all children
                foreach ($childStudentIds as $studentId) {
                    $parentIbu->students()->attach($studentId, [
                        'relationship'     => 'biological',
                        'is_primary_contact' => false,
                    ]);
                }

                $childNames = collect($family['children'])->pluck('nama')->implode(', ');
                $credentials[] = [
                    'type'        => 'Ibu',
                    'nama'        => $family['nama_ibu'],
                    'username'    => $usernameIbu,
                    'password'    => $usernameIbu,
                    'email'       => $emailIbu,
                    'phone'       => $family['hp_ibu'],
                    'anak'        => $childNames,
                    'nis_anak'    => collect($family['children'])->pluck('nis')->implode(', '),
                ];
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $credentials;
    }

    /**
     * Display dry run summary.
     */
    private function displayDryRunSummary(array $families): void
    {
        $this->newLine();
        $this->info('=== DRY RUN SUMMARY ===');

        $totalStudents = 0;
        $totalUsers = 0;

        $tableRows = [];
        foreach ($families as $family) {
            $oldestChild = $family['children'][0];
            $nisForUsername = $oldestChild['nis'];
            $childNames = collect($family['children'])->map(fn($c) => "{$c['nama']} ({$c['kelas']})")->implode("\n");
            $totalStudents += count($family['children']);

            if (!empty($family['nama_ayah'])) {
                $tableRows[] = [
                    'A' . $nisForUsername,
                    $family['nama_ayah'],
                    'Ayah',
                    $childNames,
                ];
                $totalUsers++;
            }
            if (!empty($family['nama_ibu'])) {
                $tableRows[] = [
                    'B' . $nisForUsername,
                    $family['nama_ibu'],
                    'Ibu',
                    $childNames,
                ];
                $totalUsers++;
            }
        }

        $this->table(['Username', 'Nama', 'Type', 'Anak'], array_slice($tableRows, 0, 20));

        if (count($tableRows) > 20) {
            $this->line("   ... and " . (count($tableRows) - 20) . " more accounts.");
        }

        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("   Classes: 12");
        $this->info("   Students: {$totalStudents}");
        $this->info("   User Accounts: {$totalUsers}");
        $this->info("   Families: " . count($families));
    }

    /**
     * Display final summary.
     */
    private function displaySummary(array $credentials): void
    {
        $fatherCount = collect($credentials)->where('type', 'Ayah')->count();
        $motherCount = collect($credentials)->where('type', 'Ibu')->count();

        $this->newLine();
        $this->info('📊 Import Summary:');
        $this->info("   Classes created: 12");
        $this->info("   Students created: " . Student::count());
        $this->info("   Father accounts: {$fatherCount}");
        $this->info("   Mother accounts: {$motherCount}");
        $this->info("   Total user accounts: " . ($fatherCount + $motherCount));
        $this->info("   Parent-Student relations: " . DB::table('parent_student')->count());
        $this->newLine();
        $this->warn("💡 Use --export-credentials flag to export login credentials to CSV.");
    }

    /**
     * Export credentials to CSV file.
     */
    private function exportCredentialsToCsv(array $credentials): void
    {
        $filename = 'credentials_wali_santri_' . now()->format('Y-m-d_His') . '.csv';
        $filepath = base_path($filename);

        $fp = fopen($filepath, 'w');

        // UTF-8 BOM for Excel compatibility
        fwrite($fp, "\xEF\xBB\xBF");

        // Header
        fputcsv($fp, ['Type', 'Nama', 'Username', 'Password', 'Email', 'No HP', 'Nama Anak', 'NIS Anak']);

        foreach ($credentials as $cred) {
            fputcsv($fp, [
                $cred['type'],
                $cred['nama'],
                $cred['username'],
                $cred['password'],
                $cred['email'],
                $cred['phone'] ?? '',
                $cred['anak'],
                $cred['nis_anak'],
            ]);
        }

        fclose($fp);

        $this->newLine();
        $this->info("📄 Credentials exported to: {$filepath}");
    }

    /**
     * Clean a name string — trim whitespace, normalize.
     */
    private function cleanName(?string $name): ?string
    {
        if (!$name || $name === '-' || $name === '--') return null;
        // Remove extra whitespace
        return preg_replace('/\s+/', ' ', trim($name));
    }

    /**
     * Clean and validate an email address.
     */
    private function cleanEmail(?string $email): ?string
    {
        if (!$email || $email === '-' || $email === '--') return null;
        $email = trim(strtolower($email));

        // Reject obviously invalid emails
        if (!str_contains($email, '@') || !str_contains($email, '.')) {
            return null;
        }

        // Fix common typos
        $email = str_replace('@gamil.com', '@gmail.com', $email);

        return $email;
    }

    /**
     * Clean phone number to standard format.
     */
    private function cleanPhone(?string $phone): ?string
    {
        if (!$phone || $phone === '-' || $phone === '--') return null;
        // Remove spaces, dashes
        $phone = preg_replace('/[\s\-]/', '', trim($phone));
        // Normalize +62 to 0
        if (str_starts_with($phone, '+62')) {
            $phone = '0' . substr($phone, 3);
        } elseif (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }
        return $phone;
    }

    /**
     * Ensure email is unique in the users table, appending suffix if needed.
     */
    private function ensureUniqueEmail(string $email): string
    {
        $originalEmail = $email;
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $parts = explode('@', $originalEmail);
            $email = $parts[0] . $counter . '@' . ($parts[1] ?? 'kajian.griyaquran.web.id');
            $counter++;
        }

        return $email;
    }
}
