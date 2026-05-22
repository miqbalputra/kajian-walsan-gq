<?php

namespace App\Livewire\Kepsek;

use App\Models\KajianEvent;
use App\Services\GuardianAttendanceReportService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.kepsek')]
#[Title('Presensi Wali Santri')]
class GuardianAttendanceReport extends Component
{
    use WithPagination;

    public string $kajianId = '';
    public string $statusFilter = '';
    public string $search = '';
    public string $fromDate = '';
    public string $toDate = '';
    public int $perPage = 20;

    public function mount(): void
    {
        $this->fromDate = now()->startOfMonth()->toDateString();
        $this->toDate = now()->toDateString();
        $this->kajianId = (string) (KajianEvent::whereDate('date', '<=', today())
            ->orderByDesc('date')
            ->orderByDesc('time_start')
            ->first()?->id ?? '');
    }

    public function updatingKajianId(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFromDate(): void
    {
        $this->resetPage();
    }

    public function updatingToDate(): void
    {
        $this->resetPage();
    }

    public function getKajiansProperty()
    {
        return KajianEvent::orderByDesc('date')
            ->orderByDesc('time_start')
            ->get();
    }

    public function getSelectedKajianProperty(): ?KajianEvent
    {
        return $this->kajianId ? KajianEvent::find($this->kajianId) : null;
    }

    public function getRowsProperty(): LengthAwarePaginator
    {
        $service = app(GuardianAttendanceReportService::class);

        $rows = $this->selectedKajian
            ? $service->rowsForEvent($this->selectedKajian)
            : $service->attendanceRows($this->fromDate, $this->toDate);

        $rows = $service->filterRows($rows, $this->statusFilter ?: null, $this->search ?: null);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $rows->forPage($page, $this->perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $rows->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getSummaryProperty(): array
    {
        $service = app(GuardianAttendanceReportService::class);

        $rows = $this->selectedKajian
            ? $service->rowsForEvent($this->selectedKajian)
            : $service->attendanceRows($this->fromDate, $this->toDate);

        return $service->summary(
            $service->filterRows($rows, $this->statusFilter ?: null, $this->search ?: null)
        );
    }

    public function getPerformanceRowsProperty()
    {
        return app(GuardianAttendanceReportService::class)
            ->performance($this->fromDate, $this->toDate, $this->search ?: null);
    }

    public function render()
    {
        return view('livewire.kepsek.guardian-attendance-report', [
            'rows' => $this->rows,
            'kajians' => $this->kajians,
            'performanceRows' => $this->performanceRows,
        ]);
    }
}
