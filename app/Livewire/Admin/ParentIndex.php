<?php

namespace App\Livewire\Admin;

use App\Imports\ParentsImport;
use App\Models\ClassRoom;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\KajianEvent;
use App\Models\Attendance;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ParentIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $typeFilter = '';
    public $classFilter = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'classFilter' => ['except' => ''],
    ];

    // Modal state
    public $showModal = false;
    public $showDeleteModal = false;
    public $showCardModal = false;
    public $showBatchPrintModal = false;
    public $showImportModal = false;
    public $showCredentialsModal = false;
    public $editMode = false;
    public $parentId = null;
    public $showManualAttendanceModal = false;
    public $showHistoryModal = false;

    // Form fields
    public $name = '';
    public $username = '';
    public $email = '';
    public $password = '';
    public $phone = '';
    public $nik = '';
    public $type = 'father';
    public $is_teacher = false;
    public $occupation = '';
    public $address = '';
    public $is_single_parent = false;
    public $selectedChildren = [];

    // Manual Attendance Form
    public $manualKajianEventId = '';
    public $manualStatus = 'hadir_fisik';
    public $manualProofFile = null;
    public $manualNotes = '';
    public $manualParent = null;

    // Attendance History
    public $historyParent = null;
    public $historyAttendances = [];
    public $historySummary = [];

    // Card data
    public $cardParent = null;
    public $qrCodeSvg = '';

    // Batch print data
    public $batchPrintClassId = '';
    public $batchPrintParents = [];

    // Import
    public $importFile;
    public $importedCredentials = [];

    protected function rules()
    {
        $allowedTypes = match (true) {
            $this->isTeacherMode() && $this->editMode => 'father,mother,teacher',
            $this->isTeacherMode() => 'teacher',
            default => 'father,mother',
        };

        return [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'nik' => 'nullable|string|max:20',
            // Phone validation: Indonesian format (optional +62/62/0 prefix, 8-13 digits)
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^(\+62|62|0)?[0-9]{8,13}$/'],
            'type' => 'required|in:' . $allowedTypes,
            'is_teacher' => 'boolean',
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'is_single_parent' => 'boolean',
            'selectedChildren' => 'array',
        ];
    }

    public function mount()
    {
        $this->normalizeTypeFilter();
    }

    protected function isTeacherMode(): bool
    {
        return $this->typeFilter === 'teacher';
    }

    protected function normalizeTypeFilter(): void
    {
        if (!in_array($this->typeFilter, ['', 'father', 'mother', 'teacher'], true)) {
            $this->typeFilter = '';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->normalizeTypeFilter();
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'username', 'email', 'password', 'phone', 'nik', 'type', 'is_teacher', 'occupation', 'address', 'is_single_parent', 'selectedChildren', 'editMode', 'parentId']);
        $this->type = $this->typeFilter === 'teacher' ? 'teacher' : 'father';
        $this->is_teacher = $this->type === 'teacher';
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $parent = ParentModel::with('user', 'students')->findOrFail($id);
        $this->parentId = $id;
        $this->name = $parent->user->name;
        $this->username = $parent->user->username;
        $this->email = $parent->user->email;
        $this->phone = $parent->user->phone;
        $this->nik = $parent->nik;
        $this->type = $parent->type;
        $this->is_teacher = $parent->isTeacher();
        $this->occupation = $parent->occupation;
        $this->address = $parent->address;
        $this->is_single_parent = $parent->is_single_parent;
        $this->selectedChildren = $parent->students->pluck('id')->toArray();
        $this->password = '';
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->isTeacherMode()) {
            if (! $this->editMode) {
                $this->type = 'teacher';
            }

            $this->is_teacher = true;
        }

        $this->validate();

        // Validate username uniqueness
        $usernameQuery = User::where('username', $this->username);
        // Validate email uniqueness
        $emailQuery = User::where('email', $this->email);

        if ($this->editMode) {
            $parent = ParentModel::findOrFail($this->parentId);
            $usernameQuery->where('id', '!=', $parent->user_id);
            $emailQuery->where('id', '!=', $parent->user_id);
        }

        if ($usernameQuery->exists()) {
            $this->addError('username', 'Username sudah digunakan.');
            return;
        }

        if ($emailQuery->exists()) {
            $this->addError('email', 'Email sudah digunakan.');
            return;
        }

        if ($this->editMode) {
            // Update existing
            $parent = ParentModel::findOrFail($this->parentId);
            $user = $parent->user;

            $user->update([
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }

            $parent->update([
                'nik' => $this->nik,
                'type' => $this->type,
                'is_teacher' => $this->type === 'teacher' || (bool) $this->is_teacher,
                'occupation' => $this->occupation,
                'address' => $this->address,
                'is_single_parent' => $this->is_single_parent,
            ]);

            // Sync children
            $parent->students()->sync($this->selectedChildren);

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Data orang tua berhasil diperbarui!']);
        } else {
            // Create new
            $roleName = $this->type === 'teacher' ? 'guru' : 'wali_santri';
            $targetRole = Role::where('name', $roleName)->first();

            $user = User::create([
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'password' => Hash::make($this->password ?: 'password'),
                'phone' => $this->phone,
                'role_id' => $targetRole?->id,
                'is_active' => true,
            ]);

            $parent = ParentModel::create([
                'user_id' => $user->id,
                'nik' => $this->nik,
                'type' => $this->type,
                'is_teacher' => $this->type === 'teacher' || (bool) $this->is_teacher,
                'occupation' => $this->occupation,
                'address' => $this->address,
                'is_single_parent' => $this->is_single_parent,
            ]);

            // Attach children
            if (!empty($this->selectedChildren)) {
                $parent->students()->attach($this->selectedChildren, [
                    'relationship' => 'biological',
                    'is_primary_contact' => $this->type === 'father',
                ]);
            }

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Orang tua berhasil ditambahkan!']);
        }

        $this->showModal = false;
        $this->reset(['name', 'username', 'email', 'password', 'phone', 'nik', 'type', 'is_teacher', 'occupation', 'address', 'is_single_parent', 'selectedChildren', 'editMode', 'parentId']);
    }

    public function confirmDelete($id)
    {
        $this->parentId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $parent = ParentModel::with(['user', 'attendances'])->findOrFail($this->parentId);

        // PROTEKSI: Blokir hapus jika punya riwayat presensi
        $attendanceCount = $parent->attendances()->count();
        if ($attendanceCount > 0) {
            $this->showDeleteModal = false;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Tidak bisa dihapus! {$parent->user->name} memiliki {$attendanceCount} riwayat presensi. Gunakan fitur Nonaktifkan saja."
            ]);
            return;
        }

        // Delete user (cascade will handle parent record)
        $parent->user->delete();

        $this->showDeleteModal = false;
        $this->parentId = null;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Data orang tua berhasil dihapus!']);
    }

    public function showCard($id)
    {
        $this->cardParent = ParentModel::with('user', 'students.classRoom')->findOrFail($id);

        // Generate QR Code using bacon-qr-code
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $this->qrCodeSvg = $writer->writeString($this->cardParent->qr_code_string);

        $this->showCardModal = true;
    }

    public function openManualAttendanceModal($id)
    {
        $this->manualParent = ParentModel::with('user')->findOrFail($id);
        $this->parentId = $id;
        $this->manualKajianEventId = KajianEvent::orderBy('date', 'desc')->first()?->id ?? '';
        $this->manualStatus = 'hadir_fisik';
        $this->manualProofFile = null;
        $this->manualNotes = '';
        $this->showManualAttendanceModal = true;
    }

    public function saveManualAttendance()
    {
        $this->validate([
            'manualKajianEventId' => 'required|exists:kajian_events,id',
            'manualStatus' => 'required|in:hadir_fisik,hadir_online,izin',
            'manualProofFile' => $this->manualStatus !== 'hadir_fisik' ? 'required|file|image|max:2048' : 'nullable',
            'manualNotes' => 'nullable|string|max:500',
        ], [
            'manualProofFile.required' => $this->manualStatus === 'hadir_online' ? 'Catatan kajian wajib diupload.' : 'Surat pernyataan izin wajib diupload.',
        ]);

        // Check for existing attendance
        $existing = \App\Models\Attendance::where('parent_id', $this->parentId)
            ->where('kajian_event_id', $this->manualKajianEventId)
            ->first();

        if ($existing) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Orang tua ini sudah memiliki riwayat presensi untuk kajian tersebut.']);
            return;
        }

        $proofPath = null;
        if ($this->manualProofFile) {
            $folder = $this->manualStatus === 'hadir_online' ? 'attendance_notes' : 'attendance_permissions';
            $proofPath = $this->manualProofFile->store($folder, 'public');
        }

        \App\Models\Attendance::create([
            'parent_id' => $this->parentId,
            'kajian_event_id' => $this->manualKajianEventId,
            'status' => $this->manualStatus,
            'method' => 'manual',
            'proof_file' => $proofPath,
            'notes' => $this->manualNotes,
            'validation_status' => $this->manualParent?->isTeacher() ? 'pending' : 'approved',
            'validated_by' => $this->manualParent?->isTeacher() ? null : auth()->id(),
            'validated_at' => $this->manualParent?->isTeacher() ? null : now(),
        ]);

        // Update kajian attendance count
        $event = KajianEvent::find($this->manualKajianEventId);
        $event->updateAttendanceCount();

        $this->showManualAttendanceModal = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Presensi manual berhasil disimpan!']);
    }

    public function showHistory($id)
    {
        $this->historyParent = ParentModel::with(['user', 'students.classRoom'])->findOrFail($id);
        
        // Get all attendances for this parent, ordered by kajian date
        $this->historyAttendances = Attendance::with('kajianEvent')
            ->where('parent_id', $id)
            ->whereHas('kajianEvent') // Ensure event still exists
            ->get()
            ->sortByDesc(fn($attendance) => $attendance->kajianEvent->date)
            ->values()
            ->toArray();

        // Calculate summary
        $summary = [
            'total' => count($this->historyAttendances),
            'hadir_fisik' => 0,
            'hadir_online' => 0,
            'izin' => 0,
            'alpha' => 0,
        ];

        foreach ($this->historyAttendances as $attendance) {
            if (isset($summary[$attendance['status']])) {
                $summary[$attendance['status']]++;
            }
        }

        $this->historySummary = $summary;
        $this->showHistoryModal = true;
    }

    public function openBatchPrintModal()
    {
        $this->batchPrintClassId = '';
        $this->batchPrintParents = [];
        $this->showBatchPrintModal = true;
    }

    public function loadParentsByClass()
    {
        if (!$this->batchPrintClassId) {
            $this->batchPrintParents = [];
            return;
        }

        // Get all parents who have children in the selected class
        $parents = ParentModel::with(['user', 'students.classRoom'])
            ->whereIn('type', ['father', 'mother'])
            ->whereHas('students', function ($query) {
                $query->where('class_id', $this->batchPrintClassId);
            })
            ->get();

        $renderer = new ImageRenderer(
            new RendererStyle(250),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        $this->batchPrintParents = $parents->map(function ($parent) use ($writer) {
            return [
                'id' => $parent->id,
                'name' => $parent->user->name,
                'type' => $parent->type_display,
                'qr_code' => $parent->qr_code_string,
                'qr_svg' => $writer->writeString($parent->qr_code_string),
                'children' => $parent->students->map(fn($s) => [
                    'name' => $s->name,
                    'class' => $s->classRoom?->name ?? '-',
                    'nis' => $s->nis
                ])->toArray()
            ];
        })->toArray();
    }

    public function downloadTemplate()
    {
        // Create Excel template using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Orang Tua');

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '8B5CF6']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];

        // Headers
        $headers = ['Nama', 'Tipe (Ayah/Ibu)', 'Email', 'Telepon', 'NIK', 'Pekerjaan', 'Alamat', 'Single Parent (Ya/Tidak)', 'NIS Anak'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['Budi Santoso', 'Ayah', 'budi@email.com', '081234567890', '3201011234567890', 'Wiraswasta', 'Jl. Merdeka No. 1', 'Tidak', '12345'],
            ['Siti Aminah', 'Ibu', 'siti@email.com', '081987654321', '3201019876543210', 'Ibu Rumah Tangga', 'Jl. Merdeka No. 1', 'Tidak', '12345'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Set column widths
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE ORANG TUA'],
            [''],
            ['Kolom yang wajib diisi:'],
            ['- Nama: Nama lengkap orang tua'],
            [''],
            ['Format Tipe:'],
            ['- Ayah, Father, Bapak untuk Ayah'],
            ['- Ibu, Mother untuk Ibu'],
            [''],
            ['NIS Anak:'],
            ['- Masukkan NIS siswa untuk menghubungkan orang tua dengan anak'],
            ['- NIS harus sudah ada di database siswa'],
            [''],
            ['Catatan:'],
            ['- Username & password akan digenerate otomatis'],
            ['- Password default: walsan + 4 digit terakhir telepon'],
            ['- Jika email kosong, akan digenerate dari nama'],
        ];
        foreach ($instructions as $row => $data) {
            $instructionSheet->setCellValue('A' . ($row + 1), $data[0]);
        }
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionSheet->getColumnDimension('A')->setWidth(60);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        // Save to temp file
        $fileName = 'template_import_orang_tua.xlsx';
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
            $import = new ParentsImport();
            Excel::import($import, $this->importFile->getRealPath());

            $this->showImportModal = false;
            $this->importFile = null;

            $summary = $import->getSummary();
            $this->dispatch('notify', ['type' => 'success', 'message' => $summary]);

            // Show credentials if any
            if (!empty($import->credentials)) {
                $this->importedCredentials = $import->credentials;
                $this->showCredentialsModal = true;
            }

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

    public function render()
    {
        $query = ParentModel::with(['user', 'students.classRoom'])
            ->whereHas('user', function ($query) {
                if ($this->search) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                }
            });

        if ($this->isTeacherMode()) {
            $query->where(function ($query) {
                $query->where('type', 'teacher')
                    ->orWhere('is_teacher', true);
            });
        } else {
            $query->whereIn('type', ['father', 'mother']);

            if (in_array($this->typeFilter, ['father', 'mother'], true)) {
                $query->where('type', $this->typeFilter);
            }
        }

        $query->when($this->classFilter, function ($query) {
                $query->whereHas('students', function ($q) {
                    $q->where('class_id', $this->classFilter);
                });
            })
            ->orderBy('created_at', 'desc');

        // Handle "all" option
        if ($this->perPage === 'all') {
            $parents = $query->get();
        } else {
            $parents = $query->paginate((int) $this->perPage);
        }

        $allStudents = Student::where('is_active', true)->orderBy('name')->get();
        $allClasses = ClassRoom::orderBy('name')->get();
        $allKajianEvents = KajianEvent::orderBy('date', 'desc')->take(10)->get();

        return view('livewire.admin.parent-index', [
            'parents' => $parents,
            'allStudents' => $allStudents,
            'allClasses' => $allClasses,
            'allKajianEvents' => $allKajianEvents,
            'isTeacherMode' => $this->isTeacherMode(),
        ])->layout('components.layouts.admin', ['title' => 'Manajemen Orang Tua']);
    }
}
