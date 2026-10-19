<?php

namespace App\Livewire\Admin;

use App\Models\TeacherArchive;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class TeacherArchiveIndex extends Component
{
    use WithPagination;

    public string $search = '';

    #[Locked]
    public ?int $selectedArchiveId = null;

    public function mount(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function showDetail(int $archiveId): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->selectedArchiveId = TeacherArchive::findOrFail($archiveId)->id;
    }

    public function closeDetail(): void
    {
        $this->selectedArchiveId = null;
    }

    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        return view('livewire.admin.teacher-archive-index', [
            'archives' => TeacherArchive::with('archivedBy')
                ->when($this->search !== '', fn ($query) => $query->where('teacher_name', 'like', '%'.$this->search.'%'))
                ->latest()->paginate(15),
            'selectedArchive' => $this->selectedArchiveId
                ? TeacherArchive::with('archivedBy')->findOrFail($this->selectedArchiveId) : null,
        ])->layout('components.layouts.admin', ['title' => 'Arsip Guru']);
    }
}
