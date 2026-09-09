<?php

namespace App\Console\Commands;

use App\Models\ClassRoom;
use App\Services\DataIndukClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class SyncDataIndukReferences extends Command
{
    protected $signature = 'data-induk:sync-references {--unit= : UUID unit Data Induk}';
    protected $description = 'Sinkronkan referensi kelas Data Induk ke read-model Kajian tanpa menghapus kelas atau transaksi lokal.';

    public function handle(DataIndukClient $client): int
    {
        try { $classes = $client->referenceClasses($this->option('unit')); }
        catch (\Throwable $exception) { $this->error($exception->getMessage()); return self::FAILURE; }

        $created = $updated = $conflicts = 0;
        DB::transaction(function () use ($classes, &$created, &$updated, &$conflicts): void {
            foreach ($classes as $remote) {
                $masterId = trim((string) ($remote['id'] ?? ''));
                $name = trim((string) ($remote['name'] ?? ''));
                if ($masterId === '' || $name === '') { continue; }
                $class = ClassRoom::query()->where('data_induk_id', $masterId)->first();
                $sameName = ClassRoom::query()->where('name', $name)->first();
                if ($class && $sameName && $class->id !== $sameName->id && $sameName->data_induk_id !== $masterId) {
                    DB::table('data_induk_sync_conflicts')->insert(['entity_type' => 'class', 'master_id' => $masterId, 'local_id' => $sameName->id, 'reason' => 'Nama kelas cocok dengan kelas lokal lain.', 'payload' => json_encode($remote), 'created_at' => now(), 'updated_at' => now()]);
                    $conflicts++; continue;
                }
                if (! $class && $sameName && $sameName->data_induk_id) { $conflicts++; continue; }
                $attributes = ['data_induk_id' => $masterId, 'name' => $name, 'level' => (string) ($remote['code'] ?? ''), 'is_active' => empty($remote['archivedAt'])];
                if (! $class && ! $sameName) { ClassRoom::create($attributes); $created++; continue; }
                ($class ?? $sameName)->update($attributes); $updated++;
            }
        });
        $this->info("Kelas Data Induk: {$created} baru, {$updated} diperbarui, {$conflicts} konflik.");
        return self::SUCCESS;
    }
}
