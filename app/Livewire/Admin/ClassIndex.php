<?php

namespace App\Livewire\Admin;

use App\Models\ClassRoom;
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
            'teacher_id' => $this->teacher_id ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editMode) {
            $class = ClassRoom::findOrFail($this->classId);
            $class->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Kelas berhasil diperbarui!']);
        } else {
            if (ClassRoom::where('name', $this->name)->exists()) {
                $this->addError('name', 'Nama kelas sudah digunakan.');
                return;
            }
            ClassRoom::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Kelas berhasil ditambahkan!']);
        }

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
        $class->delete();
        $this->showDeleteModal = false;
        $this->classId = null;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kelas berhasil dihapus!']);
    }

    public function render()
    {
        $classes = ClassRoom::with(['teacher', 'students'])
            ->withCount('students')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate($this->perPage);

        $teachers = \App\Models\User::whereHas('role', function ($q) {
            $q->where('name', 'wali_kelas');
        })->get();

        return view('livewire.admin.class-index', [
            'classes' => $classes,
            'teachers' => $teachers,
        ])->layout('components.layouts.admin', ['title' => 'Manajemen Kelas']);
    }
}
