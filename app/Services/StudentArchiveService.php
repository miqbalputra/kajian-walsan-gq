<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\StudentExitRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentArchiveService
{
    public function __construct(private readonly ParentArchiveService $parentArchiveService)
    {
    }

    public function archive(Student $student, array $data, ?User $actor = null): StudentExitRecord
    {
        return DB::transaction(function () use ($student, $data, $actor) {
            $student = Student::with('classRoom')->lockForUpdate()->findOrFail($student->id);

            if ($student->student_status === 'graduated') {
                throw new \DomainException('Santri alumni/lulus tidak dapat diarsipkan sebagai pindah atau keluar.');
            }

            $existing = $student->exitRecords()->open()->latest('id')->first();
            if ($existing) {
                return $existing;
            }

            $exitType = $data['exit_type'] ?? 'withdrawn';
            if (! in_array($exitType, ['transferred', 'withdrawn'], true)) {
                throw new \InvalidArgumentException('Jenis arsip santri tidak valid.');
            }

            $academicYearId = $data['academic_year_id'] ?? AcademicYear::active()?->id;
            $record = $student->exitRecords()->create([
                'exit_type' => $exitType,
                'academic_year_id' => $academicYearId,
                'from_class_id' => $student->class_id,
                'from_class_name' => $student->classRoom?->name,
                'effective_date' => $data['effective_date'] ?? today()->toDateString(),
                'reason' => $data['reason'] ?? null,
                'destination' => $data['destination'] ?? null,
                'notes' => $data['notes'] ?? null,
                'evidence_path' => $data['evidence_path'] ?? null,
                'is_legacy' => false,
                'archived_by' => $actor?->id,
                'archived_at' => now(),
            ]);

            $student->update([
                'class_id' => null,
                'student_status' => $exitType,
                'is_active' => false,
            ]);

            $student->enrollments()
                ->whereNull('ended_at')
                ->whereIn('status', ['enrolled', 'retained'])
                ->update([
                    'status' => 'withdrawn',
                    'ended_at' => ($data['effective_date'] ?? today()->toDateString()),
                    'notes' => $data['notes'] ?? null,
                ]);

            $this->parentArchiveService->syncForStudentParents($student->id, $actor);

            return $record->fresh(['student', 'academicYear', 'fromClass']);
        });
    }

    public function restore(Student $student, int $classId, ?int $academicYearId = null, ?User $actor = null, ?string $notes = null): Student
    {
        return DB::transaction(function () use ($student, $classId, $academicYearId, $actor, $notes) {
            $student = Student::lockForUpdate()->findOrFail($student->id);
            if ($student->student_status === 'graduated') {
                throw new \DomainException('Santri alumni/lulus tidak dapat dipulihkan sebagai santri aktif.');
            }

            $class = ClassRoom::where('is_active', true)->findOrFail($classId);
            $academicYear = $academicYearId
                ? AcademicYear::findOrFail($academicYearId)
                : AcademicYear::active();
            if (! $academicYear) {
                throw new \DomainException('Tahun ajaran aktif belum tersedia.');
            }

            $openRecord = $student->exitRecords()->open()->latest('id')->first();
            if ($student->student_status === 'active' && $student->is_active && ! $openRecord) {
                return $student->fresh(['classRoom']);
            }

            $enrollment = $student->enrollments()->where('academic_year_id', $academicYear->id)->first();
            $enrollmentData = [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'class_level' => $class->level,
                'status' => 'enrolled',
                'started_at' => $academicYear->start_date,
                'ended_at' => null,
                'notes' => $notes ?: 'Dipulihkan dari arsip oleh admin.',
            ];
            if ($enrollment) {
                $enrollment->update($enrollmentData);
            } else {
                $student->enrollments()->create(array_merge($enrollmentData, [
                    'academic_year_id' => $academicYear->id,
                ]));
            }

            $student->update([
                'class_id' => $class->id,
                'student_status' => 'active',
                'is_active' => true,
            ]);

            if ($openRecord) {
                $openRecord->update([
                    'restored_at' => now(),
                    'restored_by' => $actor?->id,
                    'restored_academic_year_id' => $academicYear->id,
                    'restored_class_id' => $class->id,
                    'restore_notes' => $notes ?: 'Dipulihkan dari arsip oleh admin.',
                ]);
            }

            $this->parentArchiveService->syncForStudentParents($student->id, $actor);

            return $student->fresh(['classRoom']);
        });
    }
}
