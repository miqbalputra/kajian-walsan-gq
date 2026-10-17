<?php

namespace App\Livewire\WaliKelas;

use App\Livewire\WaliKelas\Concerns\WithAttendanceData;
use App\Models\AcademicYear;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithAttendanceData, WithPagination;

    public function mount(): void
    {
        $this->reportService()->managedClass();
        if (! request()->has('tahun')) {
            $this->academicYearId = (string) (AcademicYear::active()?->id ?? '');
        }
        if (! request()->has('kajian')) {
            $this->kajianId = (string) ($this->dataset['events']->first(fn ($event) => $event->status === 'closed'
                && $event->date->lte(today()) && ($this->academicYearId === '' || $event->academic_year_id == $this->academicYearId)
                && $this->dataset['rows']->contains('event_id', $event->id))?->id ?? '');
        }
    }

    #[Computed]
    public function dataset(): array
    {
        return $this->reportService()->dataset();
    }

    public function render()
    {
        $data = $this->dataset;
        if (! $data['class']) {
            return view('livewire.wali-kelas.no-class')->layout('components.layouts.wali-kelas', ['title' => 'Kelas saya']);
        }
        $baseRows = $this->reportService()->filter($data['rows'], $this->academicYearId, $this->kajianId);
        if ($this->kajianId === '') {
            $baseRows = $baseRows->where('event_state', 'ended');
        }
        $filtered = $this->reportService()->filter($baseRows, status: $this->status, search: $this->search);
        $students = $data['students']->filter(function ($student) use ($filtered) {
            $needle = mb_strtolower(trim($this->search));
            $text = $student->name.' '.$student->nis.' '.$student->parents->pluck('user.name')->join(' ');

            return ($this->status === '' || $filtered->contains('student_id', $student->id))
                && ($needle === '' || str_contains(mb_strtolower($text), $needle) || $filtered->contains('student_id', $student->id));
        })->values();
        $trend = $data['events']->filter(fn ($event) => $event->status === 'closed' && $event->date->lte(today())
            && ($this->academicYearId === '' || $event->academic_year_id == $this->academicYearId)
            && $data['rows']->contains('event_id', $event->id))->take(6)->reverse()->values()
            ->map(fn ($event) => ['event' => $event, 'summary' => $this->reportService()->summary($data['rows']->where('event_id', $event->id))]);

        return view('livewire.wali-kelas.dashboard', $data + [
            'studentPage' => $this->paginateRows($students), 'displayRows' => $baseRows,
            'summary' => $this->reportService()->summary($baseRows), 'trend' => $trend,
            'attentionPage' => $this->paginateRows($data['attention'], 8, 'attentionPage'),
        ])->layout('components.layouts.wali-kelas', ['title' => 'Dashboard wali kelas']);
    }
}
