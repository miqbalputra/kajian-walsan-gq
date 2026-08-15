<?php

namespace App\Services;

use App\Models\ParentModel;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ParentDataExportService
{
    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F766E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $parentSheet = $spreadsheet->getActiveSheet();
        $parentSheet->setTitle('Wali');
        $this->writeHeader($parentSheet, [
            'Parent ID', 'User ID', 'Nama', 'Tipe', 'Username', 'Email', 'Telepon', 'NIK',
            'Pekerjaan', 'Alamat', 'Status Wali', 'Akun Aktif', 'Jumlah Anak Aktif', 'Jumlah Anak Alumni', 'Jumlah Anak Pindah', 'Jumlah Anak Keluar',
            'QR Utama', 'QR Alias', 'Dibuat',
        ], $headerStyle);

        $parentRow = 2;
        ParentModel::with(['user', 'students', 'qrCodes'])
            ->whereIn('type', ['father', 'mother'])
            ->orderBy('id')
            ->chunkById(200, function ($parents) use ($parentSheet, &$parentRow) {
                foreach ($parents as $parent) {
                    $activeChildren = $parent->students->filter(fn ($student) => ($student->student_status ?? 'active') === 'active' && $student->is_active)->count();
                    $alumniChildren = $parent->students->filter(fn ($student) => ($student->student_status ?? null) === 'graduated')->count();
                    $transferredChildren = $parent->students->where('student_status', 'transferred')->count();
                    $withdrawnChildren = $parent->students->where('student_status', 'withdrawn')->count();
                    $aliases = $parent->qrCodes
                        ->where('kind', '!=', 'canonical')
                        ->where('is_active', true)
                        ->whereNull('revoked_at')
                        ->pluck('code')
                        ->implode(' | ');

                    $parentSheet->fromArray([[
                        $parent->id,
                        $parent->user_id,
                        $parent->user?->name,
                        $parent->type_display,
                        $parent->user?->username,
                        $parent->user?->email,
                        $parent->user?->phone,
                        $parent->nik,
                        $parent->occupation,
                        $parent->address,
                        $activeChildren > 0 ? 'Aktif' : 'Arsip',
                        $parent->user?->is_active ? 'Aktif' : 'Tidak Aktif',
                        $activeChildren,
                        $alumniChildren,
                        $transferredChildren,
                        $withdrawnChildren,
                        $parent->qr_code_string,
                        $aliases,
                        optional($parent->created_at)->format('Y-m-d H:i:s'),
                    ]], null, 'A'.$parentRow++);
                }
            });

        $studentSheet = $spreadsheet->createSheet();
        $studentSheet->setTitle('Anak');
        $this->writeHeader($studentSheet, [
            'Student ID', 'NIS', 'Nama', 'Status', 'Kelas Saat Ini', 'Jenis Kelamin',
            'Tempat Lahir', 'Tanggal Lahir', 'Alamat', 'Mulai Lulus', 'Tahun Lulus',
            'Jenis Keluar', 'Tanggal Keluar', 'Alasan Keluar', 'Tujuan Keluar', 'Dibuat Arsip', 'Dipulihkan',
            'Histori Tahun/Kelas',
        ], $headerStyle);

        $studentRow = 2;
        Student::with(['classRoom', 'enrollments.academicYear', 'graduationAcademicYear', 'exitRecords'])
            ->orderBy('id')
            ->chunkById(200, function ($students) use ($studentSheet, &$studentRow) {
                foreach ($students as $student) {
                    $history = $student->enrollments
                        ->sortBy(fn ($enrollment) => $enrollment->academicYear?->name)
                        ->map(fn ($enrollment) => ($enrollment->academicYear?->name ?? '-').' / '.($enrollment->class_name ?? '-').' ['.$enrollment->status.']')
                        ->implode(' | ');
                    $exit = $student->exitRecords->sortByDesc('id')->first();

                    $studentSheet->fromArray([[
                        $student->id,
                        $student->nis,
                        $student->name,
                        $student->student_status ?? ($student->is_active ? 'active' : 'withdrawn'),
                        $student->classRoom?->name,
                        $student->gender,
                        $student->birth_place,
                        optional($student->birth_date)->format('Y-m-d'),
                        $student->address,
                        optional($student->graduated_at)->format('Y-m-d H:i:s'),
                        $student->graduationAcademicYear?->name,
                        $exit?->exit_type,
                        optional($exit?->effective_date)->format('Y-m-d'),
                        $exit?->reason,
                        $exit?->destination,
                        optional($exit?->archived_at)->format('Y-m-d H:i:s'),
                        optional($exit?->restored_at)->format('Y-m-d H:i:s'),
                        $history,
                    ]], null, 'A'.$studentRow++);
                }
            });

        $relationSheet = $spreadsheet->createSheet();
        $relationSheet->setTitle('Relasi');
        $this->writeHeader($relationSheet, [
            'Parent ID', 'Nama Wali', 'Tipe Wali', 'Student ID', 'NIS', 'Nama Anak',
            'Status Anak', 'Kelas Saat Ini', 'Jenis Keluar', 'Tanggal Keluar', 'Alasan Keluar', 'Tujuan Keluar', 'Hubungan', 'Primary Contact',
            'Tahun Ajaran/Kelas', 'QR Alias Anak',
        ], $headerStyle);

        $relationRow = 2;
        ParentModel::with(['user', 'students.classRoom', 'students.enrollments.academicYear', 'students.exitRecords', 'qrCodes'])
            ->whereIn('type', ['father', 'mother'])
            ->orderBy('id')
            ->chunkById(200, function ($parents) use ($relationSheet, &$relationRow) {
                foreach ($parents as $parent) {
                    foreach ($parent->students as $student) {
                        $history = $student->enrollments
                            ->sortBy(fn ($enrollment) => $enrollment->academicYear?->name)
                            ->map(fn ($enrollment) => ($enrollment->academicYear?->name ?? '-').' / '.($enrollment->class_name ?? '-'))
                            ->implode(' | ');
                        $aliases = $parent->qrCodes
                            ->where('source_student_id', $student->id)
                            ->where('is_active', true)
                            ->whereNull('revoked_at')
                            ->pluck('code')
                            ->implode(' | ');
                        $exit = $student->exitRecords->sortByDesc('id')->first();
                        $pivot = $student->pivot;

                        $relationSheet->fromArray([[
                            $parent->id,
                            $parent->user?->name,
                            $parent->type_display,
                            $student->id,
                            $student->nis,
                            $student->name,
                            $student->student_status ?? ($student->is_active ? 'active' : 'withdrawn'),
                            $student->classRoom?->name,
                            $exit?->exit_type,
                            optional($exit?->effective_date)->format('Y-m-d'),
                            $exit?->reason,
                            $exit?->destination,
                            $pivot?->relationship,
                            $pivot?->is_primary_contact ? 'Ya' : 'Tidak',
                            $history,
                            $aliases,
                        ]], null, 'A'.$relationRow++);
                    }
                }
            });

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $sheet->freezePane('A2');
            $sheet->getDefaultRowDimension()->setRowHeight(18);
            $highestColumn = $sheet->getHighestColumn();
            foreach (range(1, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn)) as $columnIndex) {
                $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex))->setAutoSize(true);
            }
        }

        $fileName = 'data-lengkap-wali-santri-'.now()->format('Y-m-d-His').'.xlsx';
        $directory = storage_path('app/public');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $path = $directory.DIRECTORY_SEPARATOR.$fileName;
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function writeHeader($sheet, array $headers, array $style): void
    {
        $sheet->fromArray($headers, null, 'A1');
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray($style);
        $sheet->getRowDimension(1)->setRowHeight(32);
    }
}
