<?php

namespace App\Livewire\WaliKelas\Concerns;

use App\Livewire\WaliKelas\Dashboard;
use App\Livewire\WaliKelas\ReportIndex;
use App\Services\WaliKelasReportService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

trait WithAttendanceData
{
    #[Url(as: 'tahun', history: true)]
    public string $academicYearId = '';

    #[Url(as: 'kajian', history: true)]
    public string $kajianId = '';

    #[Url(as: 'status', history: true)]
    public string $status = '';

    #[Url(as: 'q')]
    public string $search = '';

    protected function reportService(): WaliKelasReportService
    {
        return app(WaliKelasReportService::class);
    }

    public function updated($property): void
    {
        if (in_array($property, ['academicYearId', 'kajianId', 'status', 'search'], true)) {
            if ($property === 'academicYearId') {
                $this->kajianId = '';
            }
            $this->resetPage();
        }
    }

    public function selectEvent(int $id): void
    {
        abort_unless($this->dataset['events']->contains('id', $id), 404);
        $this->kajianId = (string) $id;
        $this->status = '';
        $this->resetPage();
    }

    public function selectStatus(string $status): void
    {
        abort_unless(in_array($status, ['', 'present', 'hadir_fisik', 'hadir_online', 'izin', 'alpha', 'pending', 'rejected', 'incomplete', 'not_started'], true), 422);
        $this->status = $this->status === $status ? '' : $status;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['academicYearId', 'kajianId', 'search', 'status']);
        $this->resetPage();
    }

    protected function filteredRows(): Collection
    {
        return $this->reportService()->filter($this->dataset['rows'], $this->academicYearId, $this->kajianId, $this->status, $this->search);
    }

    protected function paginateRows(Collection $rows, int $perPage = 12, string $pageName = 'page'): LengthAwarePaginator
    {
        $page = $this->getPage($pageName);

        return new LengthAwarePaginator($rows->forPage($page, $perPage)->values(), $rows->count(), $perPage, $page,
            ['path' => request()->url(), 'query' => request()->query(), 'pageName' => $pageName]);
    }

    public function studentUrl(int $id): string
    {
        $query = array_filter(['tahun' => $this->academicYearId, 'kajian' => $this->kajianId, 'status' => $this->status, 'q' => $this->search, 'page' => $this->getPage()], fn ($value) => $value !== '');
        if ($this instanceof Dashboard) {
            $query['attentionPage'] = $this->getPage('attentionPage');
        }

        return route('wali-kelas.students.show', ['student' => $id, 'kembali' => http_build_query($query),
            'asal' => $this instanceof ReportIndex ? 'reports' : 'dashboard']);
    }
}
