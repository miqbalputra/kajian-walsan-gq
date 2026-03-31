<?php

namespace App\Livewire\WaliKelas;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class ReportIndex extends Component
{
    use WithPagination;

    public $academicYearId = '';
    public $kajianId = '';
    public $search = '';
    public $status = '';
    public $perPage = 15;

    public function mount()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $this->academicYearId = $activeYear?->id ?? '';
    }

    public function resetFilters()
    {
        $this->reset(['kajianId', 'search', 'status']);
        $activeYear = AcademicYear::where('is_active', true)->first();
        $this->academicYearId = $activeYear?->id ?? '';
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingKajianId()
    {
        $this->resetPage();
    }
    public function updatingStatus()
    {
        $this->resetPage();
    }

    private function getFilteredQuery()
    {
        $teacherClass = auth()->user()->managedClass;

        if (!$teacherClass) {
            return Attendance::whereRaw('1=0');
        }

        return Attendance::query()
            ->with(['student', 'parent.user', 'kajianEvent'])
            ->whereHas('student', function ($query) use ($teacherClass) {
                $query->where('class_id', $teacherClass->id);
            })
            ->when($this->academicYearId, function ($query) {
                $query->whereHas('kajianEvent', function ($q) {
                    $q->where('academic_year_id', $this->academicYearId);
                });
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('student', function ($sq) {
                        $sq->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('nis', 'like', '%' . $this->search . '%');
                    })
                        ->orWhereHas('parent.user', function ($pq) {
                            $pq->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->kajianId, function ($query) {
                $query->where('kajian_event_id', $this->kajianId);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderByDesc('created_at');
    }

    public function render()
    {
        $class = auth()->user()->managedClass;

        if (!$class) {
            return view('livewire.wali-kelas.no-class')
                ->layout('components.layouts.wali-kelas', ['title' => 'Laporan']);
        }

        $attendances = $this->getFilteredQuery()->paginate($this->perPage);
        $kajianEvents = KajianEvent::where('academic_year_id', $this->academicYearId)
            ->orderByDesc('date')
            ->get();

        return view('livewire.wali-kelas.report-index', [
            'attendances' => $attendances,
            'kajianEvents' => $kajianEvents,
            'class' => $class
        ])->layout('components.layouts.wali-kelas', ['title' => 'Laporan Kehadiran']);
    }

    /**
     * Export to CSV (Excel compatible)
     */
    public function exportExcel()
    {
        $attendances = $this->getFilteredQuery()->get();
        $class = auth()->user()->managedClass;

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-presensi-' . ($class->name ?? 'kelas') . '-' . now()->format('Y-m-d') . '.csv"',
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
                $childName = $attendance->student?->name ?? $attendance->parent?->students->first()?->name ?? '-';
                $className = $attendance->student?->classRoom?->name ?? $attendance->parent?->students->first()?->classRoom?->name ?? '-';

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
                    $attendance->created_at?->format('H:i') ?? '-',
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
        $class = auth()->user()->managedClass;

        $data = [
            'attendances' => $attendances,
            'filters' => [
                'academicYear' => $this->academicYearId ? AcademicYear::find($this->academicYearId)?->name : 'Semua',
                'kajian' => $this->kajianId ? KajianEvent::find($this->kajianId)?->title : 'Semua',
                'class' => $class->name ?? 'Semua',
                'status' => $this->status ? $this->getStatusLabel($this->status) : 'Semua',
            ],
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.attendance-pdf', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print ($pdf->output()),
            'laporan-presensi-' . ($class->name ?? 'kelas') . '-' . now()->format('Y-m-d') . '.pdf'
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
}
