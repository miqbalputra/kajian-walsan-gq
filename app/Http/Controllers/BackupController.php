<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BackupController extends Controller
{
    public function downloadSql(DatabaseBackupService $backup): BinaryFileResponse|RedirectResponse
    {
        try {
            return $this->download($backup->createSqlBackup(), 'application/sql');
        } catch (Throwable $exception) {
            report($exception);

            return $this->failedDownload('Backup SQL belum dapat dibuat. Pastikan server memiliki mariadb-client dan koneksi database aktif.');
        }
    }

    public function downloadXlsx(DatabaseBackupService $backup): BinaryFileResponse|RedirectResponse
    {
        try {
            return $this->download(
                $backup->createXlsxBackup(),
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->failedDownload('Backup XLSX belum dapat dibuat. Silakan coba lagi atau periksa kapasitas server.');
        }
    }

    /** @param array{path: string, name: string} $backup */
    private function download(array $backup, string $contentType): BinaryFileResponse
    {
        return response()->download($backup['path'], $backup['name'], [
            'Content-Type' => $contentType,
            'Cache-Control' => 'no-store, private',
        ])->deleteFileAfterSend(true);
    }

    private function failedDownload(string $message): RedirectResponse
    {
        return redirect()->route('admin.backups.index')->with('backupError', $message);
    }
}
