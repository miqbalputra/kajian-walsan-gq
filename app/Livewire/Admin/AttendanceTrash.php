<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceTrash extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function restore($id)
    {
        $attendance = Attendance::onlyTrashed()->findOrFail($id);
        $attendance->restore();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Presensi berhasil dikembalikan!']);
    }

    public function forceDelete($id)
    {
        $attendance = Attendance::onlyTrashed()->findOrFail($id);
        $attendance->forceDelete();

        $this->dispatch('notify', ['type' => 'warning', 'message' => 'Presensi dihapus permanen.']);
    }

    public function render()
    {
        $attendances = Attendance::onlyTrashed()
            ->with(['parent.user', 'kajianEvent'])
            ->when($this->search, function ($query) {
                $query->whereHas('parent.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('deleted_at')
            ->paginate($this->perPage);

        return view('livewire.admin.attendance-trash', [
            'attendances' => $attendances
        ])->layout('components.layouts.admin', ['title' => 'Sampah Presensi']);
    }
}
