<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\PromotionBatch;
use App\Models\PromotionChange;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PromotionService
{
    public function suggestClassMapping(Collection $sourceClasses, Collection $targetClasses): array
    {
        $mapping = [];

        foreach ($sourceClasses as $source) {
            $nextLevel = (string) ((int) $source->level + 1);
            $candidates = $targetClasses->filter(fn ($target) => (string) $target->level === $nextLevel);

            if ((int) $source->level >= 6) {
                $mapping[$source->id] = null;
                continue;
            }

            $signature = $this->classSignature($source->name);
            $sameSignature = $candidates->filter(fn ($target) => $this->classSignature($target->name) === $signature);

            $mapping[$source->id] = $sameSignature->count() === 1
                ? $sameSignature->first()->id
                : ($candidates->count() === 1 ? $candidates->first()->id : null);
        }

        return $mapping;
    }

    public function preview(int $sourceYearId, array $classMapping, array $overrides = []): array
    {
        $students = Student::with('classRoom')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('student_status')->orWhere('student_status', 'active');
            })
            ->orderBy('class_id')
            ->orderBy('name')
            ->get();

        $targetClasses = ClassRoom::orderBy('level')->orderBy('name')->get()->keyBy('id');

        $rows = [];
        foreach ($students as $student) {
            $sourceLevel = (int) ($student->classRoom?->level ?? 0);
            $defaultAction = $sourceLevel >= 6 ? 'graduate' : 'promote';
            $defaultTarget = $sourceLevel >= 6 ? null : ($classMapping[$student->class_id] ?? null);

            if ($sourceLevel < 6 && ! $defaultTarget) {
                $defaultAction = 'defer';
            }

            $override = $overrides[$student->id] ?? [];
            $action = $override['action'] ?? $defaultAction;
            $targetClassId = match ($action) {
                'graduate', 'defer' => null,
                'retain' => $student->class_id,
                default => array_key_exists('target_class_id', $override)
                    ? ($override['target_class_id'] ?: null)
                    : $defaultTarget,
            };

            $targetClass = $targetClassId ? $targetClasses->get((int) $targetClassId) : null;

            $rows[] = [
                'student_id' => $student->id,
                'nis' => $student->nis,
                'name' => $student->name,
                'source_class_id' => $student->class_id,
                'source_class_name' => $student->classRoom?->name,
                'source_level' => $student->classRoom?->level,
                'action' => $action,
                'target_class_id' => $targetClass?->id,
                'target_class_name' => $targetClass?->name,
                'is_unmapped' => $sourceLevel < 6 && ! $targetClass,
                'is_graduate' => $action === 'graduate',
            ];
        }

        return [
            'rows' => $rows,
            'summary' => $this->summarize($rows),
            'warnings' => $this->warnings($rows, $targetClasses),
        ];
    }

    public function apply(
        AcademicYear $sourceYear,
        AcademicYear $targetYear,
        array $classMapping,
        array $decisions,
        ?int $adminId = null
    ): PromotionBatch {
        $existing = PromotionBatch::where('source_academic_year_id', $sourceYear->id)
            ->where('target_academic_year_id', $targetYear->id)
            ->whereIn('status', ['draft', 'applied'])
            ->first();

        if ($existing?->status === 'applied') {
            throw ValidationException::withMessages([
                'promotion' => 'Kenaikan untuk pasangan tahun ajaran ini sudah pernah diterapkan.',
            ]);
        }

        $preview = $this->preview($sourceYear->id, $classMapping, $decisions);
        $blocking = collect($preview['rows'])->filter(function (array $row) {
            return $row['action'] === 'defer'
                || ($row['action'] !== 'graduate' && ! $row['target_class_id']);
        });

        if ($blocking->isNotEmpty()) {
            throw ValidationException::withMessages([
                'promotion' => 'Masih ada siswa yang belum memiliki keputusan atau kelas tujuan.',
            ]);
        }

        $targetClassIds = collect($preview['rows'])
            ->pluck('target_class_id')
            ->filter()
            ->unique()
            ->values();

        $capacityErrors = ClassRoom::whereIn('id', $targetClassIds)->get()->filter(function (ClassRoom $class) use ($preview) {
            $incoming = collect($preview['rows'])->where('target_class_id', $class->id)->count();
            return $class->capacity && $incoming > $class->capacity;
        });

        if ($capacityErrors->isNotEmpty()) {
            throw ValidationException::withMessages([
                'promotion' => 'Kapasitas kelas tujuan terlampaui: '.$capacityErrors->pluck('name')->implode(', '),
            ]);
        }

        return DB::transaction(function () use ($sourceYear, $targetYear, $classMapping, $decisions, $preview, $adminId) {
            $batch = PromotionBatch::updateOrCreate(
                [
                    'source_academic_year_id' => $sourceYear->id,
                    'target_academic_year_id' => $targetYear->id,
                ],
                [
                    'initiated_by' => $adminId,
                    'status' => 'draft',
                    'class_mapping' => $classMapping,
                    'summary' => $preview['summary'],
                ]
            );

            $batch->changes()->delete();

            foreach ($preview['rows'] as $row) {
                $student = Student::with('classRoom')->lockForUpdate()->findOrFail($row['student_id']);
                $beforeClass = $student->classRoom;
                $beforeStatus = $student->student_status ?? ($student->is_active ? 'active' : 'withdrawn');
                $beforeIsActive = (bool) $student->is_active;
                $action = $row['action'];

                $sourceEnrollment = StudentEnrollment::where('student_id', $student->id)
                    ->where('academic_year_id', $sourceYear->id)
                    ->first();

                if ($action === 'graduate') {
                    $sourceEnrollment?->update([
                        'status' => 'graduated',
                        'ended_at' => $targetYear->start_date,
                        'notes' => 'Lulus pada proses kenaikan '.$targetYear->name,
                    ]);

                    $student->update([
                        'class_id' => null,
                        'student_status' => 'graduated',
                        'is_active' => false,
                        'graduated_at' => now(),
                        'graduation_academic_year_id' => $sourceYear->id,
                    ]);
                } else {
                    $targetClass = ClassRoom::findOrFail($row['target_class_id']);
                    $sourceEnrollment?->update(['ended_at' => $targetYear->start_date]);

                    StudentEnrollment::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'academic_year_id' => $targetYear->id,
                        ],
                        [
                            'class_id' => $targetClass->id,
                            'class_name' => $targetClass->name,
                            'class_level' => $targetClass->level,
                            'status' => $action === 'retain' ? 'retained' : 'enrolled',
                            'started_at' => $targetYear->start_date,
                            'ended_at' => null,
                            'notes' => $action === 'retain' ? 'Tetap kelas atas keputusan admin.' : null,
                        ]
                    );

                    $student->update([
                        'class_id' => $targetClass->id,
                        'student_status' => 'active',
                        'is_active' => true,
                        'graduated_at' => null,
                        'graduation_academic_year_id' => null,
                    ]);
                }

                $student->refresh()->load('classRoom');
                $batch->changes()->create([
                    'student_id' => $student->id,
                    'before_class_id' => $beforeClass?->id,
                    'after_class_id' => $student->class_id,
                    'before_class_name' => $beforeClass?->name,
                    'after_class_name' => $student->classRoom?->name,
                    'before_status' => $beforeStatus,
                    'after_status' => $student->student_status,
                    'before_is_active' => $beforeIsActive,
                    'after_is_active' => $student->is_active,
                    'action' => $action,
                ]);
            }

            $batch->update([
                'status' => 'applied',
                'applied_at' => now(),
                'summary' => $preview['summary'],
            ]);

            $targetYear->setAsActive();

            return $batch->fresh(['changes']);
        });
    }

    public function rollback(PromotionBatch $batch): PromotionBatch
    {
        if (! $batch->isApplied()) {
            throw ValidationException::withMessages(['promotion' => 'Batch belum berstatus applied.']);
        }

        return DB::transaction(function () use ($batch) {
            foreach ($batch->changes as $change) {
                $student = Student::lockForUpdate()->find($change->student_id);
                if (! $student) {
                    continue;
                }

                $student->update([
                    'class_id' => $change->before_class_id,
                    'student_status' => $change->before_status,
                    'is_active' => $change->before_is_active,
                    'graduated_at' => null,
                    'graduation_academic_year_id' => null,
                ]);

                StudentEnrollment::where('student_id', $student->id)
                    ->where('academic_year_id', $batch->target_academic_year_id)
                    ->update([
                        'status' => 'withdrawn',
                        'ended_at' => now()->toDateString(),
                        'notes' => 'Dibatalkan melalui rollback batch kenaikan #'.$batch->id,
                    ]);

                StudentEnrollment::where('student_id', $student->id)
                    ->where('academic_year_id', $batch->source_academic_year_id)
                    ->update(['ended_at' => null, 'status' => $change->before_status === 'graduated' ? 'graduated' : 'enrolled']);
            }

            $batch->update(['status' => 'rolled_back', 'rolled_back_at' => now()]);

            $batch->sourceAcademicYear?->setAsActive();

            return $batch->fresh();
        });
    }

    private function summarize(array $rows): array
    {
        $collection = collect($rows);

        return [
            'total' => $collection->count(),
            'promoted' => $collection->where('action', 'promote')->count(),
            'retained' => $collection->where('action', 'retain')->count(),
            'moved' => $collection->where('action', 'move')->count(),
            'graduated' => $collection->where('action', 'graduate')->count(),
            'deferred' => $collection->where('action', 'defer')->count(),
            'unmapped' => $collection->where('is_unmapped', true)->count(),
        ];
    }

    private function warnings(array $rows, Collection $targetClasses): array
    {
        $warnings = [];
        foreach (collect($rows)->where('is_unmapped', true) as $row) {
            $warnings[] = $row['source_class_name'].' belum memiliki kelas tujuan untuk '.$row['name'].'.';
        }

        foreach ($targetClasses as $class) {
            $incoming = collect($rows)->where('target_class_id', $class->id)->count();
            if ($class->capacity && $incoming > $class->capacity) {
                $warnings[] = 'Kapasitas '.$class->name.' akan terlampaui.';
            }
        }

        return array_values(array_unique($warnings));
    }

    private function classSignature(string $name): string
    {
        return preg_replace('/[^a-z]+/i', '', preg_replace('/\d+/', '', strtolower($name))) ?: '';
    }
}
