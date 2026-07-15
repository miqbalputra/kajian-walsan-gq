<?php

namespace App\Livewire\Admin;

use App\Imports\StudentsImport;
use App\Models\ClassRoom;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentIndex extends Component
{
    use WithPagination;
    use \Livewire\WithFileUploads;

    public $search = '';
    public $classFilter = '';
    public $perPage = 10;

    // Modal state
    public $showModal = false;
    public $showImportModal = false;
    public $showDeleteModal = false;
    public $editMode = false;
    public $studentId = null;

    // Form fields
    public $nis = '';
    public $name = '';
    public $class_id = '';
    public $gender = '';
    public $birth_date = '';
    public $guardian_name = '';
    public $guardian_phone = '';
    public $guardian_relationship = '';
    public $is_active = true;

    // Import
    public $importFile;

    protected $rules = [
        'nis' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9]+$/'],
        'name' => 'required|string|max:100',
        'class_id' => 'required|exists:classes,id',
        'gender' => 'required|in:L,P',
        'birth_date' => 'nullable|date',
        'guardian_name' => 'nullable|string|max:100',
        // Phone validation: Indonesian format (optional +62/62/0 prefix, 8-13 digits)
        'guardian_phone' => ['nullable', 'string', 'max:20', 'regex:/^(\+62|62|0)?[0-9]{8,13}$/'],
        'guardian_relationship' => 'nullable|string|max:50',
        'is_active' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClassFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['nis', 'name', 'class_id', 'gender', 'birth_date', 'guardian_name', 'guardian_phone', 'guardian_relationship', 'is_active', 'editMode', 'studentId']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $student = Student::findOrFail($id);
        $this->studentId = $id;
        $this->nis = $student->nis;
        $this->name = $student->name;
        $this->class_id = $student->class_id;
        $this->gender = $student->gender;
        $this->birth_date = $student->birth_date?->format('Y-m-d');
        $this->guardian_name = $student->guardian_name;
        $this->guardian_phone = $student->guardian_phone;
        $this->guardian_relationship = $student->guardian_relationship;
        $this->is_active = $student->is_active;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nis' => $this->nis,
            'name' => $this->name,
            'class_id' => $this->class_id,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date ?: null,
            'guardian_name' => $this->guardian_name,
            'guardian_phone' => $this->guardian_phone,
            'guardian_relationship' => $this->guardian_relationship,
            'is_active' => $this->is_active,
        ];

        // Check unique NIS (exclude current student if editing)
        $nisQuery = Student::where('nis', $this->nis);
        if ($this->editMode && $this->studentId) {
            $nisQuery->where('id', '!=', $this->studentId);
        }
        if ($nisQuery->exists()) {
            $this->addError('nis', 'NIS sudah digunakan.');
            return;
        }

        if ($this->editMode) {
            $student = Student::findOrFail($this->studentId);
            $student->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Siswa berhasil diperbarui!']);
        } else {
            Student::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Siswa berhasil ditambahkan!']);
        }

        $this->showModal = false;
        $this->reset(['nis', 'name', 'class_id', 'gender', 'birth_date', 'guardian_name', 'guardian_phone', 'guardian_relationship', 'is_active', 'editMode', 'studentId']);
    }

    public function confirmDelete($id)
    {
        $this->studentId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $student = Student::findOrFail($this->studentId);
        $student->delete();
        $this->showDeleteModal = false;
        $this->studentId = null;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Siswa berhasil dihapus!']);
    }

    /**
     * Export seluruh data identitas santri beserta orang tuanya (ayah & ibu)
     * ke file Excel (.xlsx) native. Satu baris per santri.
     */
    public function exportWaliSantri()
    {
        $students = Student::with(['classRoom', 'parents.user'])
            ->orderBy('name')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Santri & Wali');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrap' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $headers = [
            'NIS', 'Nama Santri', 'Kelas', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Alamat Santri', 'Status Aktif',
            'Nama Ayah', 'NIK Ayah', 'Pekerjaan Ayah', 'No HP Ayah', 'Email Ayah', 'Alamat Ayah',
            'Nama Ibu', 'NIK Ibu', 'Pekerjaan Ibu', 'No HP Ibu', 'Email Ibu', 'Alamat Ibu',
            'Nama Wali (fallback)', 'Telepon Wali (fallback)', 'Hubungan Wali (fallback)',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)); // 23 -> 'W'
        $sheet->getStyle('A1:'.$lastCol.'1')->applyFromArray($headerStyle);

        $rows = [];
        foreach ($students as $student) {
            // Ambil ayah/ibu dari koleksi yang sudah di-eager-load (hindari N+1).
            $father = $student->parents->firstWhere('type', 'father');
            $mother = $student->parents->firstWhere('type', 'mother');

            $rows[] = [
                $student->nis,
                $student->name,
                $student->classRoom?->name ?? '-',
                $student->gender ?? '',
                $student->birth_place ?? '',
                $student->birth_date?->format('Y-m-d') ?? '',
                $student->address ?? '',
                $student->is_active ? 'Aktif' : 'Tidak Aktif',
                // Ayah
                $father?->user?->name ?? '',
                $father?->nik ?? '',
                $father?->occupation ?? '',
                $father?->user?->phone ?? '',
                $father?->user?->email ?? '',
                $father?->address ?? '',
                // Ibu
                $mother?->user?->name ?? '',
                $mother?->nik ?? '',
                $mother?->occupation ?? '',
                $mother?->user?->phone ?? '',
                $mother?->user?->email ?? '',
                $mother?->address ?? '',
                // Wali fallback (kolom teks di tabel students, untuk santri tanpa akun wali)
                $student->guardian_name ?? '',
                $student->guardian_phone ?? '',
                $student->guardian_relationship ?? '',
            ];
        }

        $sheet->fromArray($rows, null, 'A2');

        // Lebar kolom otomatis
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header
        $sheet->freezePane('A2');

        // Simpan ke temp file lalu download
        $fileName = 'data-santri-wali-'.date('Y-m-d').'.xlsx';
        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }
        $tempPath = storage_path('app/public/'.$fileName);

        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function downloadTemplate()
    {
        // Create Excel template using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Siswa');

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];

        // Headers
        $headers = ['NIS', 'Nama', 'Kelas', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Alamat', 'Nama Wali', 'Telepon Wali', 'Hubungan Wali'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['12345', 'Ahmad Fulan', '1A', 'L', 'Jakarta', '2015-05-15', 'Jl. Merdeka No. 1', 'Budi Santoso', '081234567890', 'Paman'],
            ['12346', 'Siti Aisyah', '1B', 'P', 'Bandung', '2015-08-20', 'Jl. Sukajadi No. 10', 'Aminah', '081987654321', 'Bibi'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Set column widths
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE SISWA'],
            [''],
            ['Kolom yang wajib diisi:'],
            ['- NIS: Nomor Induk Siswa (unik untuk setiap siswa)'],
            ['- Nama: Nama lengkap siswa'],
            [''],
            ['Format Jenis Kelamin:'],
            ['- L atau Laki-laki untuk laki-laki'],
            ['- P atau Perempuan untuk perempuan'],
            [''],
            ['Format Tanggal Lahir:'],
            ['- YYYY-MM-DD (contoh: 2015-05-15)'],
            ['- Atau format Excel date'],
            [''],
            ['Catatan:'],
            ['- Jika NIS sudah ada, data siswa akan diperbarui'],
            ['- Kelas akan otomatis dibuat jika belum ada'],
            ['- Email dan password untuk wali akan digenerate otomatis'],
        ];
        foreach ($instructions as $row => $data) {
            $instructionSheet->setCellValue('A' . ($row + 1), $data[0]);
        }
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionSheet->getColumnDimension('A')->setWidth(60);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        // Save to temp file
        $fileName = 'template_import_siswa.xlsx';
        $tempPath = storage_path('app/public/' . $fileName);

        // Ensure directory exists
        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ]);

        try {
            $import = new StudentsImport();
            Excel::import($import, $this->importFile->getRealPath());

            $this->showImportModal = false;
            $this->importFile = null;

            $summary = $import->getSummary();
            $this->dispatch('notify', ['type' => 'success', 'message' => $summary]);

            // Show errors if any
            if (!empty($import->errors)) {
                foreach (array_slice($import->errors, 0, 5) as $error) {
                    $this->dispatch('notify', ['type' => 'warning', 'message' => $error]);
                }
            }

            $this->resetPage();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            foreach (array_slice($failures, 0, 3) as $failure) {
                $this->addError('importFile', "Baris {$failure->row()}: " . implode(', ', $failure->errors()));
            }
        } catch (\Exception $e) {
            $this->addError('importFile', 'Gagal import: ' . $e->getMessage());
        }
    }

    private function createParent($name, $phone, $type, $student, $waliRole)
    {
        // Try to find existing parent by phone/name
        // Simple logic: Create User first
        $username = preg_replace('/[^0-9]/', '', $phone);
        if (empty($username)) {
            $username = strtolower(str_replace(' ', '', $name)) . rand(100, 999);
        }

        // Check if user exists
        $user = \App\Models\User::where('username', $username)->first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => $name,
                'username' => $username,
                'email' => $username . '@example.com', // Dummy email
                'password' => \Illuminate\Support\Facades\Hash::make('bismillah'), // Default password
                'phone' => $phone,
                'role_id' => $waliRole?->id,
                'is_active' => true,
            ]);
        }

        // Create/Find Parent Profile
        $parent = \App\Models\ParentModel::firstOrCreate(
            ['user_id' => $user->id],
            ['type' => $type]
        );

        // Attach to student
        if (!$student->parents()->where('parent_id', $parent->id)->exists()) {
            $student->parents()->attach($parent->id, ['relationship' => $type === 'father' ? 'Ayah' : 'Ibu']);
        }
    }

    public function render()
    {
        $query = Student::with('classRoom', 'parents.students')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nis', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->classFilter, function ($query) {
                $query->where('class_id', $this->classFilter);
            })
            ->orderBy('name');

        // Handle "all" option
        if ($this->perPage === 'all') {
            $students = $query->get();
        } else {
            $students = $query->paginate((int) $this->perPage);
        }

        $classes = ClassRoom::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.student-index', [
            'students' => $students,
            'classes' => $classes,
        ])->layout('components.layouts.admin', ['title' => 'Manajemen Siswa']);
    }
}
