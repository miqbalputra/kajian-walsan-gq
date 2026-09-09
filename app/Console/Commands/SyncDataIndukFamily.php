<?php

namespace App\Console\Commands;

use App\Models\ParentModel;
use App\Models\Student;
use App\Services\DataIndukClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

final class SyncDataIndukFamily extends Command
{
    protected $signature = 'data-induk:sync-family {--unit= : UUID unit Data Induk}';
    protected $description = 'Sinkronkan hubungan keluarga yang seluruh UUID-nya telah dipetakan; kandidat NIK dicatat sebagai konflik dan tidak disatukan otomatis.';

    public function handle(DataIndukClient $client): int
    {
        try {
            $unitId = $this->option('unit');
            $guardians = collect($client->guardians($unitId))->keyBy('id');
            $links = $client->familyLinks($unitId);
            $linked = $conflicts = 0;

            DB::transaction(function () use ($guardians, $links, &$linked, &$conflicts): void {
                foreach ($links as $link) {
                    if (! empty($link['archivedAt'])) {
                        // Kajian preserves an existing pivot for historical
                        // attendance; the local profile simply stops receiving
                        // active relationship updates.
                        continue;
                    }
                    $student = Student::query()->where('data_induk_id', $link['studentId'] ?? null)->first();
                    $parent = ParentModel::query()->where('data_induk_id', $link['guardianId'] ?? null)->first();
                    if (! $parent && isset($guardians[$link['guardianId'] ?? ''])) {
                        $guardian = $guardians[$link['guardianId']];
                        $candidate = filled($guardian['nik'] ?? null)
                            ? ParentModel::query()->where('nik', $guardian['nik'])->first()
                            : null;
                        if ($candidate) {
                            $this->conflict('guardian', (string) $link['guardianId'], $candidate->id, 'NIK wali cocok; persetujuan operator diperlukan sebelum UUID dipetakan.', $guardian);
                            $conflicts++;
                        } else {
                            $this->conflict('guardian', (string) $link['guardianId'], null, 'Wali belum memiliki akun/profil Kajian. Buat atau petakan wali terlebih dahulu.', $guardian);
                            $conflicts++;
                        }
                    }
                    if (! $student || ! $parent) {
                        if (! $student) {
                            $this->conflict('student', (string) ($link['studentId'] ?? ''), null, 'Peserta didik Data Induk belum termapping di Kajian.', $link);
                            $conflicts++;
                        }
                        continue;
                    }
                    DB::table('parent_student')->updateOrInsert(
                        ['parent_id' => $parent->id, 'student_id' => $student->id],
                        [
                            'relationship' => ($link['relationship'] ?? '') === 'guardian' ? 'guardian' : 'biological',
                            'is_primary_contact' => (bool) ($link['isPrimary'] ?? false),
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                    $linked++;
                }
            });

            $this->info("{$linked} hubungan keluarga tersinkronkan; {$conflicts} konflik dicatat.");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    /** @param array<string, mixed> $payload */
    private function conflict(string $entityType, ?string $masterId, ?int $localId, string $reason, array $payload): void
    {
        DB::table('data_induk_sync_conflicts')->updateOrInsert(
            ['entity_type' => $entityType, 'master_id' => $masterId, 'reason' => $reason, 'status' => 'open'],
            ['local_id' => $localId, 'payload' => json_encode($payload), 'updated_at' => now(), 'created_at' => now()]
        );
    }
}
