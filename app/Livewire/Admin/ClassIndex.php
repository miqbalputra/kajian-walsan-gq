<?php

namespace App\Livewire\Admin;

use App\Models\ClassRoom;
use App\Models\User;
use App\Services\ClassTeacherAssignmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class ClassIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public $showModal = false;
    public $showDeleteModal = false;
    public $editMode = false;
    public $classId = null;

    public $name = '';
    public $level = '';
    public $capacity = '';
    public $teacher_id = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:50',
        'level' => 'nullable|string|max:20',
        'capacity' => 'nullable|integer|min:1',
        'teacher_id' => 'nullable|exists:users,id',
        'is_active' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'level', 'capacity', 'teacher_id', 'is_active', 'editMode', 'classId']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $class = ClassRoom::findOrFail($id);
        $this->classId = $id;
        $this->name = $class->name;
        $this->level = $class->level;
        $this->capacity = $class->capacity;
        $this->teacher_id = $class->teacher_id;
        $this->is_active = $class->is_active;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'level' => $this->level,
            'capacity' => $this->capacity ?: null,
            'is_active' => $this->is_active,
        ];

        $nameQuery = ClassRoom::where('name', $this->name);
        if ($this->editMode && $this->classId) {
            $nameQuery->where('id', '!=', $this->classId);
        }

        if ($nameQuery->exists()) {
            $this->addError('name', 'Nama kelas sudah digunakan.');

            return;
        }

        try {
            DB::transaction(function () use ($data) {
                $class = $this->editMode ? ClassRoom::findOrFail($this->classId) : new ClassRoom;
                $class->fill($data)->save();
                $teacher = $this->is_active && $this->teacher_id ? User::findOrFail($this->teacher_id) : null;
                app(ClassTeacherAssignmentService::class)->assignToClass($class, $teacher);
            });
        } catch (ValidationException $exception) {
            $this->addError('teacher_id', collect($exception->errors())->flatten()->first());
            return;
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => $this->editMode ? 'Kelas berhasil diperbarui!' : 'Kelas berhasil ditambahkan!']);

        $this->showModal = false;
        $this->reset(['name', 'level', 'capacity', 'is_active', 'editMode', 'classId']);
    }

    public function confirmDelete($id)
    {
        $this->classId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $class = ClassRoom::findOrFail($this->classId);
        // Kelas dirujuk enrollment dan histori kegiatan. Nonaktifkan agar
        // label kelas lama tetap dapat ditelusuri.
        $class->update(['is_active' => false, 'teacher_id' => null]);
        $this->showDeleteModal = false;
        $this->classId = null;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kelas dinonaktifkan. Histori siswa tetap tersimpan.']);
    }

    public function render()
    {
        $classes = ClassRoom::with('teacher')
            ->withCount('students')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate($this->perPage);

        $teachers = User::where('is_active', true)
            ->whereDoesntHave('parentProfile', fn ($query) => $query->whereNotNull('teacher_archived_at'))
            ->where(function ($query) {
                $query->whereHas('parentProfile', fn ($query) => $query->activeTeachers())
                    ->orWhere(function ($query) {
                        $query->whereDoesntHave('parentProfile')
                            ->whereHas('role', fn ($query) => $query->whereIn('name', ['guru', 'wali_kelas']));
                    });
            })->with(['managedClass' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('name')->get();

        return view('livewire.admin.class-index', [
            'classes' => $classes,
            'teachers' => $teachers,
        ])->layout('components.layouts.admin', ['title' => 'Manajemen Kelas']);
    }
}
