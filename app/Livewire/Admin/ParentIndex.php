<?php

namespace App\Livewire\Admin;

use App\Imports\ParentsImport;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\ParentDataExportService;
use App\Services\ParentArchiveService;
use App\Services\ParentLoginAliasService;
use App\Services\ParentQrCodeService;
use App\Services\StudentFamilyImportService;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ParentIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

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

    public $showStudentFamilyImportModal = false;

    public $showCredentialsModal = false;

    public $showLinkChildModal = false;

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

    public $linkParent = null;

    public $linkChildNis = '';

    public $linkChildName = '';

    public $linkChildClassId = '';

    public $linkChildGender = '';

    public $linkChildBirthDate = '';

    public $linkChildAddress = '';

    public $linkChildRelationship = 'biological';

    public $linkChildPrimaryContact = false;

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

    public $studentFamilyImportFile;

    public $studentFamilyImportStoredPath = null;

    public $studentFamilyImportPreview = [];

    public $studentFamilyImportResult = [];

    public $studentFamilyImportCredentials = [];

    protected function rules()
    {
        $allowedTypes = match (true) {
            $this->isTeacherMode() && $this->editMode => 'father,mother,teacher',
            $this->isTeacherMode() => 'teacher',
            default => 'father,mother',
        };

        $phoneRules = ['nullable', 'string', 'max:20'];
        if (! $this->hasUnchangedStoredPhone()) {
            // New or edited phone numbers must use the Indonesian format.
            // Legacy imports may contain a descriptive value such as
            // "Hp bersama istri"; retaining that untouched value must not
            // prevent an admin from updating unrelated account credentials.
            $phoneRules[] = 'regex:/^(\+62|62|0)?[0-9]{8,13}$/';
        }

        return [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'nik' => 'nullable|string|max:20',
            'phone' => $phoneRules,
            'type' => 'required|in:'.$allowedTypes,
            'is_teacher' => 'boolean',
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'is_single_parent' => 'boolean',
            'selectedChildren' => 'array',
        ];
    }

    protected function hasUnchangedStoredPhone(): bool
    {
        if (! $this->editMode || ! $this->parentId) {
            return false;
        }

        $storedPhone = ParentModel::query()
            ->whereKey($this->parentId)
            ->join('users', 'parents.user_id', '=', 'users.id')
            ->value('users.phone');

        return (string) $this->phone === (string) $storedPhone;
    }

    protected function messages(): array
    {
        return [
            'phone.regex' => 'Nomor telepon harus format Indonesia, contoh 081234567890 atau 6281234567890.',
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
        if (! in_array($this->typeFilter, ['', 'father', 'mother', 'teacher'], true)) {
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
            DB::transaction(function () {
                // Update existing
                $parent = ParentModel::with('user')->findOrFail($this->parentId);
                $user = $parent->user;

                $user->update([
                    'name' => $this->name,
                    'username' => $this->username,
                    'email' => $this->email,
                    'phone' => $this->phone,
                ]);

                if ($this->password) {
                    $user->update(['password' => Hash::make($this->password)]);
                    // Wali can sign in with a child login alias as well as
                    // the canonical username. Keep every login path on the
                    // same password after an admin edit.
                    app(ParentLoginAliasService::class)->syncPassword($user, $this->password);
                }

                $parent->update([
                    'nik' => $this->nik,
                    'type' => $this->type,
                    'is_teacher' => $this->type === 'teacher' || (bool) $this->is_teacher,
                    'occupation' => $this->occupation,
                    'address' => $this->address,
                    'is_single_parent' => $this->is_single_parent,
                ]);

                // Sync children first, then reconcile aliases without replacing
                // the permanent parent-owned QR.
                // Editing a parent must not detach historical children. New
                // links are additive; old relations remain available for
                // attendance history and alumni reporting.
                $parent->students()->syncWithoutDetaching($this->selectedChildren);
                $parent->refresh()->syncQrCode();
                app(ParentArchiveService::class)->syncForParent($parent->fresh(), auth()->user());
            });

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Data orang tua berhasil diperbarui!']);
        } else {
            DB::transaction(function () {
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

                // Attach children first, then create aliases without replacing
                // the permanent parent-owned QR.
                if (! empty($this->selectedChildren)) {
                    foreach ($this->selectedChildren as $studentId) {
                        $parent->students()->syncWithoutDetaching([
                            $studentId => [
                                'relationship' => 'biological',
                                'is_primary_contact' => $this->type === 'father',
                            ],
                        ]);
                    }
                }

                $parent->refresh()->syncQrCode();
            });

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

    public function openLinkChildModal(int $parentId): void
    {
        $this->linkParent = ParentModel::with(['user', 'students'])->findOrFail($parentId);
        $this->linkChildNis = '';
        $this->linkChildName = '';
        $this->linkChildClassId = ClassRoom::where('level', '1')->where('is_active', true)->orderBy('name')->value('id') ?? '';
        $this->linkChildGender = '';
        $this->linkChildBirthDate = '';
        $this->linkChildAddress = '';
        $this->linkChildRelationship = 'biological';
        $this->linkChildPrimaryContact = false;
        $this->resetValidation();
        $this->showLinkChildModal = true;
    }

    public function saveLinkedChild(): void
    {
        $this->validate([
            'linkChildNis' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9]+$/'],
            'linkChildName' => ['nullable', 'string', 'max:100'],
            'linkChildClassId' => ['required', 'exists:classes,id'],
            'linkChildGender' => ['nullable', 'in:L,P'],
            'linkChildBirthDate' => ['nullable', 'date'],
            'linkChildAddress' => ['nullable', 'string'],
            'linkChildRelationship' => ['required', 'in:biological,guardian,step'],
            'linkChildPrimaryContact' => ['boolean'],
        ]);

        $class = ClassRoom::findOrFail($this->linkChildClassId);
        if ((string) $class->level !== '1') {
            $this->addError('linkChildClassId', 'Fitur ini khusus untuk anak baru kelas 1.');

            return;
        }

        try {
            DB::transaction(function () use ($class) {
            $student = Student::where('nis', trim($this->linkChildNis))->first();

            if ($student && ($student->student_status ?? null) === 'graduated') {
                throw new \RuntimeException('NIS tersebut sudah berstatus alumni. Periksa kembali data siswa sebelum menyambungkan.');
            }

            if (! $student) {
                if (blank($this->linkChildName)) {
                    throw new \RuntimeException('Nama siswa wajib diisi untuk NIS baru.');
                }

                $student = Student::create([
                    'nis' => trim($this->linkChildNis),
                    'name' => trim($this->linkChildName),
                    'class_id' => $class->id,
                    'gender' => $this->linkChildGender ?: null,
                    'birth_date' => $this->linkChildBirthDate ?: null,
                    'address' => $this->linkChildAddress ?: null,
                    'student_status' => 'active',
                    'is_active' => true,
                ]);
            } else {
                $student->update([
                    'class_id' => $student->class_id ?: $class->id,
                    'student_status' => $student->student_status ?: 'active',
                    'is_active' => true,
                ]);
            }

            $student->load('classRoom');
            $studentClass = $student->classRoom ?: $class;

            $this->linkParent->students()->syncWithoutDetaching([
                $student->id => [
                    'relationship' => $this->linkChildRelationship,
                    'is_primary_contact' => (bool) $this->linkChildPrimaryContact,
                ],
            ]);

            $targetYear = \App\Models\AcademicYear::active();
            if ($targetYear) {
                StudentEnrollment::updateOrCreate(
                    ['student_id' => $student->id, 'academic_year_id' => $targetYear->id],
                    [
                        'class_id' => $student->class_id,
                        'class_name' => $studentClass->name,
                        'class_level' => $studentClass->level,
                        'status' => 'enrolled',
                        'started_at' => $targetYear->start_date,
                    ]
                );
            }

            $linkedParent = $this->linkParent->fresh('students');
            app(ParentQrCodeService::class)->syncForParent($linkedParent);
            app(ParentLoginAliasService::class)->syncForParent($linkedParent);
            app(ParentArchiveService::class)->syncForParent($linkedParent, auth()->user());
            });
        } catch (\Throwable $exception) {
            $this->addError('linkChildNis', $exception->getMessage());

            return;
        }

        $this->showLinkChildModal = false;
        $this->linkParent = null;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Anak berhasil disambungkan. QR utama orang tua tetap sama dan alias anak baru sudah aktif.']);
    }

    public function downloadFullData(ParentDataExportService $exporter)
    {
        return $exporter->download();
    }

    public function delete(ParentArchiveService $archiveService)
    {
        $parent = ParentModel::with(['user', 'attendances', 'students'])->findOrFail($this->parentId);

        if ($parent->isGuardian() && $parent->hasActiveChildren()) {
            $this->showDeleteModal = false;
            $this->addError('parent', 'Wali yang masih memiliki anak aktif tidak dapat diarsipkan. Arsipkan anaknya terlebih dahulu jika memang sudah pindah/keluar.');

            return;
        }

        // Parent records are identity and audit data. Never delete them from
        // the UI; reconcile archive state while preserving all relations.
        if ($parent->isGuardian()) {
            $archiveService->syncForParent($parent, auth()->user());
        } else {
            $parent->user?->update(['is_active' => false]);
        }

        $this->showDeleteModal = false;
        $this->parentId = null;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Akun orang tua diarsipkan tanpa menghapus relasi, QR, dan histori.']);
    }

    public function showCard($id)
    {
        $this->cardParent = ParentModel::with('user', 'students.classRoom')->findOrFail($id);

        if ($this->cardParent->isPureTeacher()) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Guru murni tidak menggunakan kartu QR.']);

            return;
        }

        $this->cardParent->syncQrCode();

        // Generate QR Code using bacon-qr-code
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd
        );
        $writer = new Writer($renderer);
        $this->qrCodeSvg = $writer->writeString($this->cardParent->qr_code_string);

        $this->showCardModal = true;
    }

    public function openManualAttendanceModal($id)
    {
        $this->manualParent = ParentModel::with('user', 'students.classRoom')->findOrFail($id);
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

        $event = KajianEvent::with('targetClasses')->findOrFail($this->manualKajianEventId);
        $parent = ParentModel::with('user', 'students.classRoom')->findOrFail($this->parentId);

        if (! $event->isOpen()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Presensi kajian ini sudah ditutup. Buka kembali presensi sebelum menambah data.']);

            return;
        }

        if (! $event->targetsParent($parent)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Wali santri ini tidak termasuk kelas sasaran kegiatan tersebut.']);

            return;
        }

        if (! in_array($this->manualStatus, $event->policy['statuses'] ?? [], true)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Status presensi tidak tersedia untuk aturan kegiatan ini.']);

            return;
        }

        // Check for existing attendance
        $existing = Attendance::where('parent_id', $this->parentId)
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

        Attendance::create([
            'parent_id' => $this->parentId,
            'kajian_event_id' => $this->manualKajianEventId,
            'student_id' => $event->targetedStudentsForParent($parent)->first()?->id,
            'student_enrollment_id' => StudentEnrollment::ensureForEvent(
                $event->targetedStudentsForParent($parent)->first(),
                $event
            )?->id,
            'status' => $this->manualStatus,
            'method' => 'manual',
            'proof_file' => $proofPath,
            'notes' => $this->manualNotes,
            'validation_status' => $this->manualParent?->isTeacher() ? 'pending' : 'approved',
            'validated_by' => $this->manualParent?->isTeacher() ? null : auth()->id(),
            'validated_at' => $this->manualParent?->isTeacher() ? null : now(),
        ]);

        // Update kajian attendance count
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
            ->sortByDesc(fn ($attendance) => $attendance->kajianEvent->date)
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
        if (! $this->batchPrintClassId) {
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
            new SvgImageBackEnd
        );
        $writer = new Writer($renderer);

        $this->batchPrintParents = $parents->map(function ($parent) use ($writer) {
            return [
                'id' => $parent->id,
                'name' => $parent->user->name,
                'type' => $parent->type_display,
                'qr_code' => $parent->qr_code_string,
                'qr_svg' => $writer->writeString($parent->qr_code_string),
                'children' => $parent->students->map(fn ($s) => [
                    'name' => $s->name,
                    'class' => $s->classRoom?->name ?? '-',
                    'nis' => $s->nis,
                ])->toArray(),
            ];
        })->toArray();
    }

    public function downloadTemplate()
    {
        // Create Excel template using PhpSpreadsheet
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Orang Tua');

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8B5CF6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
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
            $instructionSheet->setCellValue('A'.($row + 1), $data[0]);
        }
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionSheet->getColumnDimension('A')->setWidth(60);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        // Save to temp file
        $fileName = 'template_import_orang_tua.xlsx';
        $tempPath = storage_path('app/public/'.$fileName);

        // Ensure directory exists
        if (! file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
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
            $import = new ParentsImport;
            Excel::import($import, $this->importFile->getRealPath());

            $this->showImportModal = false;
            $this->importFile = null;

            $summary = $import->getSummary();
            $this->dispatch('notify', ['type' => 'success', 'message' => $summary]);

            // Show credentials if any
            if (! empty($import->credentials)) {
                $this->importedCredentials = $import->credentials;
                $this->showCredentialsModal = true;
            }

            // Show errors if any
            if (! empty($import->errors)) {
                foreach (array_slice($import->errors, 0, 5) as $error) {
                    $this->dispatch('notify', ['type' => 'warning', 'message' => $error]);
                }
            }

            $this->resetPage();
        } catch (ValidationException $e) {
            $failures = $e->failures();
            foreach (array_slice($failures, 0, 3) as $failure) {
                $this->addError('importFile', "Baris {$failure->row()}: ".implode(', ', $failure->errors()));
            }
        } catch (\Exception $e) {
            $this->addError('importFile', 'Gagal import: '.$e->getMessage());
        }
    }

    public function openStudentFamilyImportModal(): void
    {
        $this->reset([
            'studentFamilyImportFile',
            'studentFamilyImportStoredPath',
            'studentFamilyImportPreview',
            'studentFamilyImportResult',
            'studentFamilyImportCredentials',
        ]);
        $this->resetValidation();
        $this->showStudentFamilyImportModal = true;
    }

    public function previewStudentFamilyImport(StudentFamilyImportService $importer): void
    {
        $this->validate([
            'studentFamilyImportFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        // Laravel's local disk uses storage/app/private as its root. Keep the
        // stored path and the reader on the same disk instead of rebuilding a
        // path under storage/app, which would make the upload appear missing.
        $storedPath = $this->studentFamilyImportFile->store('imports', 'local');
        $this->studentFamilyImportStoredPath = $storedPath;
        $preview = $importer->preview(Storage::disk('local')->path($storedPath));
        unset($preview['rows']);
        $this->studentFamilyImportPreview = $preview;
        $this->studentFamilyImportResult = [];
        $this->studentFamilyImportCredentials = [];
    }

    public function confirmStudentFamilyImport(StudentFamilyImportService $importer): void
    {
        if (! $this->studentFamilyImportStoredPath) {
            $this->addError('studentFamilyImportFile', 'Silakan upload dan preview file terlebih dahulu.');

            return;
        }

        if (! empty($this->studentFamilyImportPreview['errors'])) {
            $this->addError('studentFamilyImportFile', 'Perbaiki error pada preview sebelum import.');

            return;
        }

        try {
            $result = $importer->import(Storage::disk('local')->path($this->studentFamilyImportStoredPath));
            $this->studentFamilyImportResult = $result;
            $this->studentFamilyImportCredentials = $result['credentials'] ?? [];
            $this->studentFamilyImportPreview = [];
            $this->studentFamilyImportFile = null;
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Import selesai: {$result['created_students']} siswa baru, {$result['linked_relations']} relasi diproses.",
            ]);
            $this->resetPage();
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('studentFamilyImportFile', 'Import dibatalkan: '.$exception->getMessage());
        }
    }

    public function closeStudentFamilyImportModal(): void
    {
        if ($this->studentFamilyImportStoredPath) {
            Storage::disk('local')->delete($this->studentFamilyImportStoredPath);
        }

        $this->showStudentFamilyImportModal = false;
        $this->reset([
            'studentFamilyImportFile',
            'studentFamilyImportStoredPath',
            'studentFamilyImportPreview',
            'studentFamilyImportResult',
            'studentFamilyImportCredentials',
        ]);
    }

    public function render()
    {
        $query = ParentModel::with(['user', 'students.classRoom', 'qrCodes.sourceStudent'])
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(function ($query) use ($search) {
                    $query->where('qr_code_string', 'like', $search)
                        ->orWhereHas('qrCodes', fn ($qrQuery) => $qrQuery->where('code', 'like', $search))
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', $search)
                                ->orWhere('username', 'like', $search)
                                ->orWhere('email', 'like', $search)
                                ->orWhere('phone', 'like', $search);
                        })
                        ->orWhereHas('students', function ($studentQuery) use ($search) {
                            $studentQuery->where('name', 'like', $search)->orWhere('nis', 'like', $search);
                        });
                });
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
