<?php

namespace App\Livewire\Admin;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\ParentModel;
use App\Models\Student;
use App\Services\ParentArchiveService;
use App\Services\StudentArchiveService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ArchiveIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $tab = 'students';
    public string $search = '';
    public string $statusFilter = 'archived';
    public $perPage = 15;

    public bool $showArchiveModal = false;
    public bool $showRestoreModal = false;
    public bool $showStudentDetail = false;
    public bool $showParentDetail = false;

    public ?int $selectedStudentId = null;
    public ?int $selectedParentId = null;
    public ?Student $selectedStudent = null;
    public ?ParentModel $selectedParent = null;

    public string $exitType = 'withdrawn';
    public string $effectiveDate = '';
    public string $reason = '';
    public string $destination = '';
    public string $notes = '';
    public $evidenceFile = null;

    public string $restoreClassId = '';
    public string $restoreAcademicYearId = '';
    public string $restoreNotes = '';

    protected $queryString = [
        'tab' => ['except' => 'students'],
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'archived'],
    ];

    public function mount(): void
    {
        $this->effectiveDate = today()->toDateString();
        if (! in_array($this->tab, ['students', 'parents'], true)) {
            $this->tab = 'students';
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTab(): void
    {
        $this->resetPage();
        $this->statusFilter = 'archived';
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openArchiveModal(int $studentId): void
    {
        $this->selectedStudentId = $studentId;
        $this->exitType = 'withdrawn';
        $this->effectiveDate = today()->toDateString();
        $this->reason = '';
        $this->destination = '';
        $this->notes = '';
        $this->evidenceFile = null;
        $this->resetValidation();
        $this->showArchiveModal = true;
    }

    public function archiveStudent(StudentArchiveService $archiveService): void
    {
        $this->validate([
            'exitType' => ['required', 'in:transferred,withdrawn'],
            'effectiveDate' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:500'],
            'destination' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'evidenceFile' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $storedPath = null;
        try {
            if ($this->evidenceFile) {
                $storedPath = $this->evidenceFile->store('student-archive-evidence', 'local');
            }

            $archiveService->archive(
                Student::findOrFail($this->selectedStudentId),
                [
                    'exit_type' => $this->exitType,
                    'effective_date' => $this->effectiveDate,
                    'reason' => $this->reason ?: null,
                    'destination' => $this->destination ?: null,
                    'notes' => $this->notes ?: null,
                    'evidence_path' => $storedPath,
                ],
                auth()->user(),
            );

            $this->showArchiveModal = false;
            $this->reset(['evidenceFile', 'selectedStudentId', 'reason', 'destination', 'notes']);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Santri berhasil diarsipkan. Data, relasi, presensi, dan QR tetap tersimpan.']);
        } catch (\Throwable $exception) {
            if ($storedPath) {
                Storage::disk('local')->delete($storedPath);
            }

            report($exception);
            $this->addError('archive', $exception->getMessage());
        }
    }

    public function openRestoreModal(int $studentId): void
    {
        $this->selectedStudentId = $studentId;
        $this->restoreAcademicYearId = (string) (AcademicYear::active()?->id ?? '');
        $this->restoreClassId = '';
        $this->restoreNotes = '';
        $this->resetValidation();
        $this->showRestoreModal = true;
    }

    public function restoreStudent(StudentArchiveService $archiveService): void
    {
        $this->validate([
            'restoreClassId' => ['required', 'integer', 'exists:classes,id'],
            'restoreAcademicYearId' => ['required', 'integer', 'exists:academic_years,id'],
            'restoreNotes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $archiveService->restore(
                Student::findOrFail($this->selectedStudentId),
                (int) $this->restoreClassId,
                (int) $this->restoreAcademicYearId,
                auth()->user(),
                $this->restoreNotes ?: null,
            );

            $this->showRestoreModal = false;
            $this->selectedStudentId = null;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Santri berhasil dipulihkan sebagai santri aktif.']);
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('restore', $exception->getMessage());
        }
    }

    public function showStudent(int $studentId): void
    {
        $this->selectedStudent = Student::with([
            'classRoom',
            'parents.user',
            'parents.students.classRoom',
            'enrollments.academicYear',
            'exitRecords.academicYear',
            'exitRecords.fromClass',
            'exitRecords.restoredClass',
            'exitRecords.restoredAcademicYear',
        ])->findOrFail($studentId);
        $this->showStudentDetail = true;
    }

    public function showParent(int $parentId): void
    {
        $this->selectedParent = ParentModel::with([
            'user',
            'students.classRoom',
            'students.exitRecords',
            'students.enrollments.academicYear',
            'qrCodes.sourceStudent',
            'archiveRecords.archivedBy',
            'archiveRecords.restoredBy',
        ])->findOrFail($parentId);
        $this->showParentDetail = true;
    }

    public function activateParent(int $parentId, ParentArchiveService $archiveService): void
    {
        $parent = ParentModel::with('students')->findOrFail($parentId);
        if (! $parent->hasActiveChildren()) {
            $this->addError('parent', 'Akun wali hanya dapat diaktifkan kembali setelah ada anak yang dipulihkan atau aktif.');

            return;
        }

        $archiveService->restoreLogin($parent, auth()->user(), 'Diaktifkan kembali karena memiliki anak aktif.');
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Akun wali diaktifkan kembali.']);
    }

    public function render()
    {
        $students = null;
        $parents = null;

        if ($this->tab === 'students') {
            $query = Student::with([
                'classRoom',
                'parents.user',
                'exitRecords' => fn ($exitQuery) => $exitQuery->latest('id'),
            ])->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('nis', 'like', $search)
                        ->orWhereHas('parents.user', fn ($userQuery) => $userQuery
                            ->where('name', 'like', $search)
                            ->orWhere('username', 'like', $search)
                            ->orWhere('phone', 'like', $search));
                });
            });

            $this->applyStudentStatusFilter($query);
            $students = $this->perPage === 'all' ? $query->orderBy('name')->get() : $query->orderBy('name')->paginate((int) $this->perPage);
        } else {
            $query = ParentModel::with([
                'user',
                'students.classRoom',
                'students.exitRecords',
                'qrCodes.sourceStudent',
                'archiveRecords' => fn ($archiveQuery) => $archiveQuery->latest('id'),
            ])->guardians()->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where(function ($query) use ($search) {
                    $query->where('qr_code_string', 'like', $search)
                        ->orWhereHas('qrCodes', fn ($qrQuery) => $qrQuery->where('code', 'like', $search))
                        ->orWhereHas('user', fn ($userQuery) => $userQuery
                            ->where('name', 'like', $search)
                            ->orWhere('username', 'like', $search)
                            ->orWhere('email', 'like', $search)
                            ->orWhere('phone', 'like', $search))
                        ->orWhereHas('students', fn ($studentQuery) => $studentQuery
                            ->where('name', 'like', $search)
                            ->orWhere('nis', 'like', $search));
                });
            });

            if ($this->statusFilter === 'active') {
                $query->withActiveChild();
            } elseif ($this->statusFilter === 'archived') {
                $query->whereDoesntHave('students', fn ($studentQuery) => $studentQuery->active());
            }

            $parents = $this->perPage === 'all' ? $query->latest('id')->get() : $query->latest('id')->paginate((int) $this->perPage);
        }

        return view('livewire.admin.archive-index', [
            'students' => $students,
            'parents' => $parents,
            'classes' => ClassRoom::where('is_active', true)->orderBy('name')->get(),
            'academicYears' => AcademicYear::orderByDesc('name')->get(),
            'stats' => [
                'activeStudents' => Student::active()->count(),
                'graduatedStudents' => Student::where('student_status', 'graduated')->count(),
                'transferredStudents' => Student::where('student_status', 'transferred')->count(),
                'withdrawnStudents' => Student::where('student_status', 'withdrawn')->count(),
                'activeParents' => ParentModel::guardians()->withActiveChild()->count(),
                'archivedParents' => ParentModel::archivedGuardians()->count(),
            ],
        ])->layout('components.layouts.admin', ['title' => 'Arsip Santri & Wali']);
    }

    private function applyStudentStatusFilter($query): void
    {
        match ($this->statusFilter) {
            'active' => $query->where(function ($query) {
                $query->whereNull('student_status')->orWhere('student_status', 'active');
            })->where('is_active', true),
            'graduated' => $query->where('student_status', 'graduated'),
            'transferred' => $query->where('student_status', 'transferred'),
            'withdrawn' => $query->where('student_status', 'withdrawn'),
            'archived' => $query->where(function ($query) {
                $query->whereIn('student_status', ['graduated', 'transferred', 'withdrawn'])
                    ->orWhere(function ($query) {
                        $query->where('is_active', false)->whereNull('student_status');
                    });
            }),
            default => null,
        };
    }
}
