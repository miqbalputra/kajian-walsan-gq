<?php

namespace App\Services;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

class DatabaseBackupService
{
    private const EXCEL_MAX_ROWS = 1048576;

    /**
     * Create a restorable MariaDB SQL dump containing schema and all data.
     *
     * @return array{path: string, name: string}
     */
    public function createSqlBackup(): array
    {
        $connection = DB::connection();
        $config = $connection->getConfig();

        if (! in_array($connection->getDriverName(), ['mariadb', 'mysql'], true)) {
            throw new RuntimeException('Backup SQL penuh hanya tersedia untuk koneksi MariaDB/MySQL.');
        }

        $binary = $this->findDumpBinary();
        $backup = $this->makeTemporaryBackupFile('sql');

        try {
            $process = Process::fromShellCommandline(
                $this->buildSqlDumpCommand($binary, $config, $backup['path']),
                null,
                ['MYSQL_PWD' => (string) ($config['password'] ?? '')],
            );
            $process->setTimeout(null);
            $process->run();

            if (! $process->isSuccessful() || ! is_file($backup['path']) || filesize($backup['path']) === 0) {
                throw new RuntimeException('Backup SQL gagal dibuat. Pastikan mariadb-dump tersedia dan koneksi database dapat diakses.');
            }

            return $backup;
        } catch (Throwable $exception) {
            @unlink($backup['path']);

            throw $exception;
        }
    }

    /**
     * Create an XLSX audit copy containing every table, column, and row.
     *
     * @return array{path: string, name: string}
     */
    public function createXlsxBackup(): array
    {
        $backup = $this->makeTemporaryBackupFile('xlsx');
        $spreadsheet = new Spreadsheet;

        try {
            DB::connection()->transaction(function () use ($spreadsheet): void {
                $this->populateSpreadsheet($spreadsheet, DB::connection());
            });

            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save($backup['path']);

            if (! is_file($backup['path']) || filesize($backup['path']) === 0) {
                throw new RuntimeException('Backup XLSX gagal dibuat.');
            }

            return $backup;
        } catch (Throwable $exception) {
            @unlink($backup['path']);

            throw $exception;
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    /**
     * Kept protected so the command can be inspected in automated tests
     * without exposing a command construction API to the application.
     *
     * @param  array<string, mixed>  $config
     */
    protected function buildSqlDumpCommand(string $binary, array $config, string $outputPath): string
    {
        $arguments = [
            $binary,
            '--single-transaction',
            '--routines',
            '--triggers',
            '--events',
            '--add-drop-table',
            '--hex-blob',
            '--default-character-set='.(string) ($config['charset'] ?? 'utf8mb4'),
        ];

        if (filled($config['unix_socket'] ?? null)) {
            $arguments[] = '--socket='.(string) $config['unix_socket'];
        } else {
            $arguments[] = '--host='.(string) ($config['host'] ?? '127.0.0.1');
            $arguments[] = '--port='.(string) ($config['port'] ?? '3306');
        }

        $arguments[] = '--user='.(string) ($config['username'] ?? 'root');
        $arguments[] = '--databases';
        $arguments[] = (string) ($config['database'] ?? '');

        return implode(' ', array_map(
            static fn (string $argument): string => escapeshellarg($argument),
            $arguments,
        )).' > '.escapeshellarg($outputPath);
    }

    private function findDumpBinary(): string
    {
        $finder = new ExecutableFinder;

        foreach (['mariadb-dump', 'mysqldump'] as $candidate) {
            $path = $finder->find($candidate);

            if ($path !== null) {
                return $path;
            }
        }

        throw new RuntimeException('mariadb-dump tidak tersedia di server. Pastikan image aplikasi memasang mariadb-client.');
    }

    private function populateSpreadsheet(Spreadsheet $spreadsheet, ConnectionInterface $connection): void
    {
        $metadata = $spreadsheet->getActiveSheet();
        $metadata->setTitle('Metadata');
        $metadata->fromArray([
            ['Backup database lengkap'],
            ['Dibuat pada', now()->toDateTimeString()],
            ['Driver database', $connection->getDriverName()],
            ['Keterangan', 'Setiap tabel diekspor pada worksheet terpisah. Backup SQL adalah berkas pemulihan yang mempertahankan tipe nilai, NULL, BLOB, skema, indeks, trigger, routine, dan event.'],
            [],
            ['Tabel', 'Worksheet', 'Jumlah baris', 'Kolom'],
        ], null, 'A1');
        $metadata->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $this->styleHeader($metadata, 6, 4);
        $metadata->freezePane('A7');
        $metadata->getColumnDimension('A')->setWidth(30);
        $metadata->getColumnDimension('B')->setWidth(30);
        $metadata->getColumnDimension('C')->setWidth(16);
        $metadata->getColumnDimension('D')->setWidth(70);

        $usedSheetNames = ['Metadata'];
        $metadataRow = 7;
        $tables = $connection->getSchemaBuilder()->getTables();
        usort($tables, static fn (array $left, array $right): int => $left['name'] <=> $right['name']);

        foreach ($tables as $table) {
            $tableName = $table['name'];
            $columns = $connection->getSchemaBuilder()->getColumnListing($tableName);
            $rowCount = $connection->table($tableName)->count();
            $worksheetNames = $this->writeTableWorksheets($spreadsheet, $connection, $tableName, $columns, $usedSheetNames);

            $metadata->fromArray([[
                $tableName,
                implode(', ', $worksheetNames),
                $rowCount,
                implode(', ', $columns),
            ]], null, 'A'.$metadataRow++);
        }
    }

    /**
     * @param  list<string>  $columns
     * @param  list<string>  $usedSheetNames
     * @return list<string>
     */
    private function writeTableWorksheets(Spreadsheet $spreadsheet, ConnectionInterface $connection, string $tableName, array $columns, array &$usedSheetNames): array
    {
        $worksheetNames = [];
        $part = 1;
        $sheet = $this->createTableWorksheet($spreadsheet, $this->worksheetName($tableName, $part, $usedSheetNames), $columns);
        $worksheetNames[] = $sheet->getTitle();
        $row = 2;

        foreach ($connection->table($tableName)->cursor() as $record) {
            if ($row > self::EXCEL_MAX_ROWS) {
                $part++;
                $sheet = $this->createTableWorksheet($spreadsheet, $this->worksheetName($tableName, $part, $usedSheetNames), $columns);
                $worksheetNames[] = $sheet->getTitle();
                $row = 2;
            }

            foreach ($columns as $columnIndex => $column) {
                $value = $record->{$column} ?? null;
                $sheet->setCellValueExplicitByColumnAndRow(
                    $columnIndex + 1,
                    $row,
                    $this->spreadsheetValue($value),
                    DataType::TYPE_STRING,
                );
            }

            $row++;
        }

        return $worksheetNames;
    }

    /** @param list<string> $columns */
    private function createTableWorksheet(Spreadsheet $spreadsheet, string $name, array $columns): Worksheet
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($name);
        $sheet->fromArray($columns, null, 'A1');
        $this->styleHeader($sheet, 1, count($columns));
        $sheet->freezePane('A2');

        foreach ($columns as $index => $column) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index + 1))->setWidth(min(max(strlen($column) + 4, 14), 36));
        }

        return $sheet;
    }

    private function styleHeader(Worksheet $sheet, int $row, int $columnCount): void
    {
        $lastColumn = Coordinate::stringFromColumnIndex(max($columnCount, 1));
        $sheet->getStyle('A'.$row.':'.$lastColumn.$row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F766E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(28);
    }

    private function spreadsheetValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_scalar($value)) {
            $value = (string) $value;

            return mb_check_encoding($value, 'UTF-8')
                ? $value
                : 'base64:'.base64_encode($value);
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    /** @param list<string> $usedSheetNames */
    private function worksheetName(string $tableName, int $part, array &$usedSheetNames): string
    {
        $suffix = $part === 1 ? '' : '_'.$part;
        $base = preg_replace('/[\\\\\/*?:\[\]]/', '_', $tableName) ?: 'table';
        $base = mb_substr($base, 0, 31 - strlen($suffix));
        $name = $base.$suffix;
        $counter = 2;

        while (in_array($name, $usedSheetNames, true)) {
            $counterSuffix = '_'.$counter;
            $name = mb_substr($base, 0, 31 - strlen($counterSuffix)).$counterSuffix;
            $counter++;
        }

        $usedSheetNames[] = $name;

        return $name;
    }

    /** @return array{path: string, name: string} */
    private function makeTemporaryBackupFile(string $extension): array
    {
        $directory = storage_path('app/backups');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Direktori sementara backup tidak dapat dibuat.');
        }

        $timestamp = now()->format('Y-m-d-His');
        $name = 'backup-penuh-'.$timestamp.'.'.$extension;
        $path = $directory.DIRECTORY_SEPARATOR.$name;

        if (file_exists($path)) {
            $name = 'backup-penuh-'.$timestamp.'-'.bin2hex(random_bytes(3)).'.'.$extension;
            $path = $directory.DIRECTORY_SEPARATOR.$name;
        }

        return compact('path', 'name');
    }
}
