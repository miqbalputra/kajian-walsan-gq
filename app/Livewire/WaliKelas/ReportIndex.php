<?php

namespace App\Livewire\WaliKelas;

use App\Livewire\WaliKelas\Concerns\WithAttendanceData;
use App\Models\AcademicYear;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ReportIndex extends Component
{
    use WithAttendanceData, WithPagination;

    public function mount(): void
    {
        $this->reportService()->managedClass();
        if (! request()->has('tahun')) {
            $this->academicYearId = (string) (AcademicYear::active()?->id ?? '');
        }
    }

    #[Computed]
    public function dataset(): array
    {
        return $this->reportService()->dataset();
    }

    public function exportExcel()
    {
        abort_unless($this->reportService()->managedClass(), 404);
        $rows = $this->filteredRows();

        return response()->streamDownload(function () use ($rows) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Tanggal', 'Tahun ajaran', 'Kajian', 'Santri', 'NIS', 'Kelas saat kajian', 'Wali', 'Hubungan', 'Status', 'Metode', 'Validasi', 'Waktu', 'Catatan presensi'], ';', '"', '');
            foreach ($rows as $row) {
                $cells = [$row['date'], $row['academic_year'], $row['event_title'], $row['student_name'], $row['nis'], $row['class_name'], $row['parent_name'], $row['parent_type'] === 'father' ? 'Ayah' : 'Ibu', $row['label'], $row['method'], $row['validation'], $row['recorded_at'], $row['notes']];
                $cells = array_map(fn ($cell) => preg_match('/^[\s]*[=+@-]/u', (string) $cell) ? "'".$cell : $cell, $cells);
                fputcsv($file, $cells, ';', '"', '');
            }
            fclose($file);
        }, 'laporan-presensi-kelas-'.today()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf()
    {
        abort_unless($this->reportService()->managedClass(), 404);
        $rows = $this->filteredRows();
        $pdf = Pdf::loadView('reports.wali-kelas-pdf', ['rows' => $rows, 'class' => $this->dataset['class']])->setPaper('a4', 'landscape');

        return response()->streamDownload(fn () => print ($pdf->output()), 'laporan-presensi-kelas-'.today()->format('Y-m-d').'.pdf');
    }

    public function render()
    {
        $data = $this->dataset;
        if (! $data['class']) {
            return view('livewire.wali-kelas.no-class')->layout('components.layouts.wali-kelas', ['title' => 'Laporan presensi']);
        }

        return view('livewire.wali-kelas.report-index', $data + ['rowPage' => $this->paginateRows($this->filteredRows(), 20)])
            ->layout('components.layouts.wali-kelas', ['title' => 'Laporan presensi']);
    }
}
