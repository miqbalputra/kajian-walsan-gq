<?php

namespace App\Livewire\Admin;

use App\Models\AcademicYear;
use App\Models\KajianEvent;
use Livewire\Component;
use Livewire\WithPagination;

class KajianIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    // Modal state
    public $showModal = false;
    public $showDeleteModal = false;
    public $editMode = false;
    public $kajianId = null;

    // Form fields
    public $title = '';
    public $description = '';
    public $speaker = '';
    public $location = '';
    public $date = '';
    public $time_start = '';
    public $time_end = '';
    public $status = 'draft';
    public $academic_year_id = '';

    protected $rules = [
        'title' => 'required|string|max:200',
        'description' => 'nullable|string',
        'speaker' => 'nullable|string|max:100',
        'location' => 'nullable|string|max:200',
        'date' => 'required|date',
        'time_start' => 'required',
        'time_end' => 'required',
        'status' => 'required|in:draft,open,ongoing,closed',
        'academic_year_id' => 'required|exists:academic_years,id',
    ];

    public function mount()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $this->academic_year_id = $activeYear?->id ?? '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['title', 'description', 'speaker', 'location', 'date', 'time_start', 'time_end', 'status', 'editMode', 'kajianId']);
        $this->status = 'draft';
        $this->date = now()->format('Y-m-d');
        $this->time_start = '08:00';
        $this->time_end = '10:00';

        $activeYear = AcademicYear::where('is_active', true)->first();
        $this->academic_year_id = $activeYear?->id ?? '';

        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $kajian = KajianEvent::findOrFail($id);
        $this->kajianId = $id;
        $this->title = $kajian->title;
        $this->description = $kajian->description;
        $this->speaker = $kajian->speaker;
        $this->location = $kajian->location;
        $this->date = $kajian->date->format('Y-m-d');
        $this->time_start = $kajian->time_start;
        $this->time_end = $kajian->time_end;
        $this->status = $kajian->status;
        $this->academic_year_id = $kajian->academic_year_id;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'speaker' => $this->speaker,
            'location' => $this->location,
            'date' => $this->date,
            'time_start' => $this->time_start,
            'time_end' => $this->time_end,
            'status' => $this->status,
            'academic_year_id' => $this->academic_year_id,
        ];

        if ($this->editMode) {
            $kajian = KajianEvent::findOrFail($this->kajianId);
            $kajian->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Kajian berhasil diperbarui!']);
        } else {
            $data['created_by'] = auth()->id();
            KajianEvent::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Kajian berhasil ditambahkan!']);
        }

        $this->showModal = false;
        $this->reset(['title', 'description', 'speaker', 'location', 'date', 'time_start', 'time_end', 'status', 'editMode', 'kajianId']);
    }

    public function toggleStatus($id)
    {
        $kajian = KajianEvent::findOrFail($id);

        // Toggle between 'open' and 'closed'
        $newStatus = $kajian->status === 'open' ? 'closed' : 'open';
        $kajian->update(['status' => $newStatus]);

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Status kajian diubah menjadi ' . ucfirst($newStatus) . '!']);
    }

    public function confirmDelete($id)
    {
        $this->kajianId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $kajian = KajianEvent::findOrFail($this->kajianId);
        $kajian->delete();
        $this->showDeleteModal = false;
        $this->kajianId = null;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kajian berhasil dihapus!']);
    }

    public function sendReminder($id)
    {
        $kajian = KajianEvent::findOrFail($id);
        $service = new \App\Services\WebPushService();

        $title = "Kajian Rutin: " . $kajian->title;
        $body = "Akan dimulai pada " . $kajian->date->translatedFormat('d M Y') . " jam " . \Carbon\Carbon::parse($kajian->time_start)->format('H:i') . ". " .
                ($kajian->speaker ? "Pemateri: " . $kajian->speaker . ". " : "") .
                "Klik untuk melihat detail atau untuk konfirmasi kehadiran online.";

        $result = $service->sendToAllWali($title, $body, '/wali-santri/schedule');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Pengingat dikirim ke {$result['sent']} perangkat (Gagal: {$result['failed']})"
        ]);
    }

    public function render()
    {
        $kajians = KajianEvent::with('academicYear')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('speaker', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('date', 'desc')
            ->paginate($this->perPage);

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('livewire.admin.kajian-index', [
            'kajians' => $kajians,
            'academicYears' => $academicYears,
        ])->layout('components.layouts.admin', ['title' => 'Manajemen Kajian']);
    }
}
