<?php

namespace App\Console\Commands;

use App\Services\DataIndukClient;
use App\Services\DataIndukRosterSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

final class SyncDataIndukChanges extends Command
{
    protected $signature = 'data-induk:sync-changes {--unit= : UUID unit Data Induk}';
    protected $description = 'Proses feed perubahan Data Induk secara idempoten; cursor maju hanya setelah read-model Kajian berhasil diperbarui.';

    public function handle(DataIndukClient $client, DataIndukRosterSyncService $roster): int
    {
        $unitId = (string) ($this->option('unit') ?: config('data-induk.unit_id'));
        $scope = 'changes:'.($unitId ?: 'all');
        $state = DB::table('data_induk_sync_states')->where('scope', $scope)->first();
        $cursor = $state?->cursor;

        try {
            $batch = $client->changes($cursor, $unitId ?: null);
            $items = $batch['items'];
            if ($items === []) {
                DB::table('data_induk_sync_states')->updateOrInsert(
                    ['scope' => $scope],
                    ['cursor' => $cursor, 'last_success_at' => now(), 'last_error' => null, 'updated_at' => now()]
                );
                $this->info('Tidak ada perubahan Data Induk baru.');

                return self::SUCCESS;
            }

            // Fetching the canonical roster once per committed batch makes an
            // upsert idempotent even if a process stops between event delivery
            // and local persistence.
            $result = $roster->sync($unitId ?: null);
            foreach ($items as $event) {
                if (($event['entityType'] ?? null) === 'student' && ($event['operation'] ?? null) === 'archive') {
                    $roster->archiveMasterStudent((string) ($event['entityId'] ?? ''));
                }
            }

            DB::table('data_induk_sync_states')->updateOrInsert(
                ['scope' => $scope],
                ['cursor' => (string) $batch['nextCursor'], 'last_success_at' => now(), 'last_error' => null, 'updated_at' => now()]
            );
            $this->info("{$result['created']} baru, {$result['updated']} diperbarui, {$result['unchanged']} tetap; cursor {$batch['nextCursor']}. ");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            DB::table('data_induk_sync_states')->updateOrInsert(
                ['scope' => $scope],
                ['cursor' => $cursor, 'last_error' => $exception->getMessage(), 'updated_at' => now()]
            );
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
