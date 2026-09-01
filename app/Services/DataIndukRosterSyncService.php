<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

final class DataIndukRosterSyncService
{
    public function __construct(private readonly DataIndukClient $client) {}

    /**
     * Build or refresh Kajian's local read model from the active master roster.
     * Attendance and any record not present in the roster are deliberately not
     * deleted here: they are Kajian-owned transaction/history data.
     *
     * @return array{created:int,updated:int,unchanged:int,conflicts:array<int,string>,unmapped_class_ids:array<int,string>}
     */
    public function sync(?string $unitId = null): array
    {
        $roster = $this->client->activeStudentRoster($unitId);

        return DB::transaction(function () use ($roster): array {
            $result = [
                'created' => 0,
                'updated' => 0,
                'unchanged' => 0,
                'conflicts' => [],
                'unmapped_class_ids' => [],
            ];

            foreach ($roster as $remote) {
                $masterId = trim((string) ($remote['id'] ?? ''));
                $nis = trim((string) ($remote['nis'] ?? ''));
                $name = trim((string) ($remote['fullName'] ?? ''));

                if ($masterId === '' || $nis === '' || $name === '') {
                    $result['conflicts'][] = 'Data roster tanpa ID master, NIS, atau nama dilewati.';

                    continue;
                }

                $byMasterId = Student::query()->where('data_induk_id', $masterId)->first();
                $byNis = Student::query()->where('nis', $nis)->first();

                if ($byMasterId && $byNis && $byMasterId->id !== $byNis->id) {
                    $result['conflicts'][] = "NIS {$nis} sudah terkait ke siswa Kajian lain.";

                    continue;
                }

                $student = $byMasterId ?? $byNis;
                $classId = $this->localClassId($remote['classId'] ?? null, $result['unmapped_class_ids']);
                $attributes = [
                    'data_induk_id' => $masterId,
                    'nis' => $nis,
                    'name' => $name,
                    'gender' => in_array($remote['gender'] ?? null, ['L', 'P'], true) ? $remote['gender'] : null,
                    'is_active' => true,
                    'student_status' => 'active',
                ];

                // An unmapped class must not erase a legitimate local class.
                if ($classId !== null) {
                    $attributes['class_id'] = $classId;
                }

                if (! $student) {
                    Student::create($attributes);
                    $result['created']++;

                    continue;
                }

                $changes = array_filter($attributes, fn (mixed $value, string $field): bool => $student->getAttribute($field) !== $value, ARRAY_FILTER_USE_BOTH);
                if ($changes === []) {
                    $result['unchanged']++;

                    continue;
                }

                $student->update($changes);
                $result['updated']++;
            }

            $result['unmapped_class_ids'] = array_values(array_unique($result['unmapped_class_ids']));

            return $result;
        });
    }

    /** @param array<int, string> $unmappedClassIds */
    private function localClassId(mixed $masterClassId, array &$unmappedClassIds): ?int
    {
        $masterClassId = trim((string) $masterClassId);
        if ($masterClassId === '') {
            return null;
        }

        $localClassId = config('data-induk.class_map.'.$masterClassId);
        if (! $localClassId || ! ClassRoom::query()->whereKey($localClassId)->exists()) {
            $unmappedClassIds[] = $masterClassId;

            return null;
        }

        return (int) $localClassId;
    }
}
