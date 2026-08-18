<?php

namespace App\Livewire\Admin;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Services\GuardianAttendanceReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportIndex extends Component
{
    use WithPagination;

    // Filters
    public $academicYearId = '';
    public $kajianId = '';
    public $classId = '';
    public $shareClassKey = '';
    public $status = '';
    public $perPage = 20;

    public function mount()
    {
        // Default to active academic year
        $activeYear = AcademicYear::where('is_active', true)->first();
        $this->academicYearId = $activeYear?->id ?? '';
    }

    public function updatingAcademicYearId()
    {
        $this->reset('kajianId');
        $this->resetPage();
    }

    public function updatingKajianId()
    {
        $this->shareClassKey = '';
        $this->resetPage();
    }

    public function updatingClassId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    /**
     * One row is produced for every guardian targeted by an event. Before the
     * event is closed, a missing submission is displayed as progress; after it
     * is closed, the same row becomes Alpha. This prevents reports from
     * silently dropping guardians who never created an Attendance record.
     */
    private function getReportRows(): Collection
    {
        $events = KajianEvent::query()
            ->with(['targetClasses', 'academicYear'])
            ->when($this->academicYearId, function ($query) {
                $query->where('academic_year_id', $this->academicYearId);
            })
            ->when($this->kajianId, function ($query) {
                $query->whereKey($this->kajianId);
            })
            ->orderByDesc('date')
            ->orderByDesc('time_start')
            ->get();

        $service = app(GuardianAttendanceReportService::class);

        return $events
            ->flatMap(fn (KajianEvent $event) => $service->rowsForEvent($event))
            ->when($this->classId, fn (Collection $rows) => $rows->where('class_id', (int) $this->classId))
            ->when($this->status, fn (Collection $rows) => $rows->where('derived_status', $this->status))
            ->sortByDesc(fn (array $row) => ($row['kajian_date'] ?? '').' '.($row['submitted_at'] ?? ''))
            ->values();
    }

    public function getRowsProperty(): LengthAwarePaginator
    {
        $rows = $this->getReportRows();
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $rows->forPage($page, $this->perPage)->values(),
            $rows->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Export to CSV (Excel compatible)
     */
    public function exportExcel(): StreamedResponse
    {
        $rows = $this->getReportRows();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-presensi-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, [
                'No',
                'Tanggal',
                'Nama Kajian',
                'Nama Orang Tua',
                'Tipe',
                'Nama Anak',
                'Kelas',
                'Status',
                'Metode',
                'Validasi',
                'Jam Scan',
            ], ';');

            // Data rows
            foreach ($rows as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row['kajian_date'] ?? '-',
                    $row['kajian_title'] ?? '-',
                    $row['guardian_name'] ?? '-',
                    $row['guardian_type'] ?? '-',
                    $row['children'] ?? '-',
                    $row['class_name'] ?? '-',
                    $this->getStatusLabel($row['derived_status'] ?? null),
                    $this->getMethodLabel($row['method'] ?? null),
                    $this->getValidationLabel($row['validation_status'] ?? null),
                    $row['scanned_at'] ?? '-',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF
     */
    public function exportPdf()
    {
        $attendances = $this->getReportRows();

        $data = [
            'attendances' => $attendances,
            'filters' => [
                'academicYear' => $this->academicYearId ? AcademicYear::find($this->academicYearId)?->name : 'Semua',
                'kajian' => $this->kajianId ? KajianEvent::find($this->kajianId)?->title : 'Semua',
                'class' => $this->classId ? ClassRoom::find($this->classId)?->name : 'Semua',
                'status' => $this->status ? $this->getStatusLabel($this->status) : 'Semua',
            ],
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
            'rowsAreRoster' => true,
        ];

        $pdf = Pdf::loadView('reports.attendance-pdf', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print ($pdf->output()),
            'laporan-presensi-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    private function getStatusLabel($status): string
    {
        return match ($status) {
            'hadir_fisik' => 'Hadir Fisik',
            'hadir_online' => 'Hadir Online',
            'izin' => 'Izin',
            'alpha' => 'Alpha',
            'pending' => 'Menunggu Validasi',
            'rejected' => 'Ditolak',
            'not_started' => 'Presensi Dibuka',
            default => $status ?? '-',
        };
    }

    private function getMethodLabel($method): string
    {
        return match ($method) {
            'scan_qr' => 'Scan QR',
            'manual' => 'Input Manual',
            'upload' => 'Upload Bukti',
            'google_form' => 'Google Form M1',
            'public_form' => 'Form Publik M1',
            default => $method ?? '-',
        };
    }

    private function getValidationLabel($status): string
    {
        return match ($status) {
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $status ?? '-',
        };
    }

    public function getKajiansProperty()
    {
        if (!$this->academicYearId) {
            return collect();
        }

        return KajianEvent::where('academic_year_id', $this->academicYearId)
            ->orderByDesc('date')
            ->get();
    }

    public function getSelectedKajianProperty(): ?KajianEvent
    {
        return $this->kajianId
            ? KajianEvent::with('targetClasses')->find($this->kajianId)
            : null;
    }

    public function getShareStatisticsProperty(): ?array
    {
        return app(GuardianAttendanceReportService::class)
            ->shareableClassStatistics($this->selectedKajian);
    }

    public function getShareStatisticsStateProperty(): string
    {
        if (! $this->selectedKajian) {
            return 'select_event';
        }

        if ($this->selectedKajian->status !== 'closed') {
            return 'attendance_open';
        }

        return $this->shareStatistics ? 'ready' : 'snapshot_missing';
    }

    public function getSummaryProperty()
    {
        $rows = $this->getReportRows();

        return [
            'total' => $rows->count(),
            'hadir_fisik' => $rows->where('derived_status', 'hadir_fisik')->count(),
            'hadir_online' => $rows->where('derived_status', 'hadir_online')->count(),
            'izin' => $rows->where('derived_status', 'izin')->count(),
            'alpha' => $rows->where('derived_status', 'alpha')->count(),
        ];
    }

    public function render()
    {
        $academicYears = AcademicYear::orderByDesc('name')->get();
        $classes = ClassRoom::where('is_active', true)->orderBy('name')->get();
        $selectedKajian = $this->selectedKajian;
        $shareStatistics = $this->shareStatistics;
        $shareStatisticsState = $this->shareStatisticsState;

        return view('livewire.admin.report-index', [
            'rows' => $this->rows,
            'academicYears' => $academicYears,
            'classes' => $classes,
            'selectedKajian' => $selectedKajian,
            'shareStatistics' => $shareStatistics,
            'shareStatisticsState' => $shareStatisticsState,
        ])->layout('components.layouts.admin', ['title' => 'Laporan Kehadiran']);
    }
}
