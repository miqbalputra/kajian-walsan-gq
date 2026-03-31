<?php

namespace App\Livewire\Admin;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use Barryvdh\DomPDF\Facade\Pdf;
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
     * Build the base query with eager loading and filters
     */
    private function getFilteredQuery()
    {
        return Attendance::query()
            ->with([
                'parent.user',
                'parent.students.classRoom',
                'kajianEvent.academicYear',
                'student.classRoom',
            ])
            ->when($this->academicYearId, function ($query) {
                $query->whereHas('kajianEvent', function ($q) {
                    $q->where('academic_year_id', $this->academicYearId);
                });
            })
            ->when($this->kajianId, function ($query) {
                $query->where('kajian_event_id', $this->kajianId);
            })
            ->when($this->classId, function ($query) {
                $query->whereHas('parent.students', function ($q) {
                    $q->where('class_id', $this->classId);
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderByDesc('created_at');
    }

    /**
     * Export to CSV (Excel compatible)
     */
    public function exportExcel(): StreamedResponse
    {
        $attendances = $this->getFilteredQuery()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-presensi-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($attendances) {
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
            foreach ($attendances as $index => $attendance) {
                $childName = $attendance->parent?->students->first()?->name ?? '-';
                $className = $attendance->parent?->students->first()?->classRoom?->name ?? '-';

                fputcsv($file, [
                    $index + 1,
                    $attendance->kajianEvent?->date?->format('d/m/Y') ?? '-',
                    $attendance->kajianEvent?->title ?? '-',
                    $attendance->parent?->user?->name ?? '-',
                    $attendance->parent?->type_display ?? '-',
                    $childName,
                    $className,
                    $this->getStatusLabel($attendance->status),
                    $this->getMethodLabel($attendance->method),
                    $this->getValidationLabel($attendance->validation_status),
                    $attendance->scan_time?->format('H:i') ?? '-',
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
        $attendances = $this->getFilteredQuery()->get();

        $data = [
            'attendances' => $attendances,
            'filters' => [
                'academicYear' => $this->academicYearId ? AcademicYear::find($this->academicYearId)?->name : 'Semua',
                'kajian' => $this->kajianId ? KajianEvent::find($this->kajianId)?->title : 'Semua',
                'class' => $this->classId ? ClassRoom::find($this->classId)?->name : 'Semua',
                'status' => $this->status ? $this->getStatusLabel($this->status) : 'Semua',
            ],
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
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
            default => $status ?? '-',
        };
    }

    private function getMethodLabel($method): string
    {
        return match ($method) {
            'scan_qr' => 'Scan QR',
            'manual' => 'Input Manual',
            'upload' => 'Upload Bukti',
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

    public function getSummaryProperty()
    {
        $query = $this->getFilteredQuery();

        return [
            'total' => (clone $query)->count(),
            'hadir_fisik' => (clone $query)->where('status', 'hadir_fisik')->count(),
            'hadir_online' => (clone $query)->where('status', 'hadir_online')->count(),
            'izin' => (clone $query)->where('status', 'izin')->count(),
            'alpha' => (clone $query)->where('status', 'alpha')->count(),
        ];
    }

    public function render()
    {
        $attendances = $this->getFilteredQuery()->paginate($this->perPage);
        $academicYears = AcademicYear::orderByDesc('name')->get();
        $classes = ClassRoom::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.report-index', [
            'attendances' => $attendances,
            'academicYears' => $academicYears,
            'classes' => $classes,
        ])->layout('components.layouts.admin', ['title' => 'Laporan Kehadiran']);
    }
}
