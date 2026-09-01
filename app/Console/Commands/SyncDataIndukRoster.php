<?php

namespace App\Console\Commands;

use App\Services\DataIndukRosterSyncService;
use Illuminate\Console\Command;
use Throwable;

class SyncDataIndukRoster extends Command
{
    protected $signature = 'data-induk:sync-roster {--unit= : UUID unit Data Induk, bila berbeda dari konfigurasi}';

    protected $description = 'Tarik roster aktif Data Induk ke read model siswa Kajian tanpa menyentuh transaksi presensi.';

    public function handle(DataIndukRosterSyncService $sync): int
    {
        try {
            $result = $sync->sync($this->option('unit'));
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->table(['Dibuat', 'Diperbarui', 'Tetap', 'Konflik', 'Kelas belum dipetakan'], [[
            $result['created'],
            $result['updated'],
            $result['unchanged'],
            count($result['conflicts']),
            count($result['unmapped_class_ids']),
        ]]);

        foreach ($result['conflicts'] as $conflict) {
            $this->warn($conflict);
        }
        foreach ($result['unmapped_class_ids'] as $classId) {
            $this->warn("Kelas master {$classId} belum ada di DATA_INDUK_CLASS_MAP; kelas lokal siswa tidak diubah.");
        }

        return self::SUCCESS;
    }
}
