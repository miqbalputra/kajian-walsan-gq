<?php

namespace App\Console\Commands;

use App\Models\ClassRoom;
use App\Models\ParentModel;
use Illuminate\Console\Command;

final class MapDataIndukLocalRecord extends Command
{
    protected $signature = 'data-induk:map {entity : class atau guardian} {masterId : UUID Data Induk} {localId : ID Kajian}';
    protected $description = 'Tetapkan mapping UUID Data Induk ke kelas atau wali lokal setelah operator memverifikasinya.';

    public function handle(): int
    {
        $entity = (string) $this->argument('entity');
        $masterId = (string) $this->argument('masterId');
        $localId = (int) $this->argument('localId');
        $model = match ($entity) {
            'class' => ClassRoom::query()->find($localId),
            'guardian' => ParentModel::query()->find($localId),
            default => null,
        };
        if (! $model) {
            $this->error('Entity harus class atau guardian, dan ID lokal harus ada.');
            return self::FAILURE;
        }
        $occupied = $model::query()->where('data_induk_id', $masterId)->whereKeyNot($model->getKey())->exists();
        if ($occupied) {
            $this->error('UUID Data Induk tersebut sudah dipetakan ke record lokal lain.');
            return self::FAILURE;
        }
        $model->update(['data_induk_id' => $masterId]);
        $this->info("Mapping {$entity} lokal {$localId} ke {$masterId} tersimpan.");

        return self::SUCCESS;
    }
}
