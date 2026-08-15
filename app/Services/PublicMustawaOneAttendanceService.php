<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceProofHistory;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PublicMustawaOneAttendanceService
{
    /**
     * The form deliberately only uses one unambiguous, open Kajian event.
     * This keeps a permanent public link from accidentally recording a
     * submission against the wrong event.
     */
    public function availability(): array
    {
        $events = KajianEvent::openForAttendance()
            ->where('category', 'kajian')
            ->with('targetClasses')
            ->get()
            ->filter(fn (KajianEvent $event) => $this->targetsMustawaOne($event))
            ->values();

        if ($events->isEmpty()) {
            return [
                'available' => false,
                'code' => 'no_open_event',
                'message' => 'Form belum dapat digunakan karena belum ada kajian Mustawa 1 yang dibuka.',
                'event' => null,
                'allowed_statuses' => [],
            ];
        }

        if ($events->count() !== 1) {
            return [
                'available' => false,
                'code' => 'multiple_open_events',
                'message' => 'Terdapat lebih dari satu kajian Mustawa 1 yang dibuka. Silakan hubungi panitia.',
                'event' => null,
                'allowed_statuses' => [],
            ];
        }

        $event = $events->first();
        $allowedStatuses = collect($event->policy['statuses'] ?? [])
            ->intersect([Attendance::STATUS_HADIR_ONLINE, Attendance::STATUS_IZIN])
            ->values()
            ->all();

        if ($allowedStatuses === []) {
            return [
                'available' => false,
                'code' => 'status_not_allowed',
                'message' => 'Kajian yang dibuka tidak menerima presensi online atau izin melalui form ini.',
                'event' => null,
                'allowed_statuses' => [],
            ];
        }

        return [
            'available' => true,
            'code' => 'available',
            'message' => null,
            'event' => $event,
            'allowed_statuses' => $allowedStatuses,
        ];
    }

    /**
     * @return Collection<int, array{id: int, label: string, parent_types: array<int, string>}>
     */
    public function eligibleStudentOptions(): Collection
    {
        return Student::query()
            ->active()
            ->with([
                'classRoom',
                'parents.students.classRoom',
                'parents.students.enrollments',
            ])
            ->whereHas('classRoom', fn ($query) => $query->where('level', '1'))
            ->orderBy('name')
            ->get()
            ->map(function (Student $student): ?array {
                $parentTypes = $this->eligibleParentTypes($student);

                if ($parentTypes === []) {
                    return null;
                }

                return [
                    'id' => $student->id,
                    'label' => $student->name.' — '.$student->nis,
                    'parent_types' => $parentTypes,
                ];
            })
            ->filter()
            ->values();
    }

    public function submit(array $data, UploadedFile $proof): Attendance
    {
        $availability = $this->availability();

        if (! $availability['available']) {
            throw new PublicMustawaOneFormException($availability['message']);
        }

        /** @var KajianEvent $event */
        $event = $availability['event'];
        $student = $this->findEligibleStudent((int) $data['student_id']);

        if (! $student) {
            throw new PublicMustawaOneFormException('Data Ananda tidak tersedia untuk form ini.');
        }

        $parent = $this->resolveEligibleParent($student, (string) $data['parent_type']);

        if (! $parent) {
            throw new PublicMustawaOneFormException('Pilihan Bapak/Ibu tidak tersedia untuk Ananda tersebut.');
        }

        if (! in_array($data['status'], $availability['allowed_statuses'], true)) {
            throw new PublicMustawaOneFormException('Jenis pengajuan tidak tersedia untuk kajian ini.');
        }

        $this->ensureNoActiveSubmission($event, $parent);

        $folder = $data['status'] === Attendance::STATUS_IZIN
            ? 'izin-documents'
            : 'attendance-proofs';
        $path = app(CloudinaryService::class)->upload($proof, $folder)['url'];

        $attendance = DB::transaction(function () use ($event, $student, $parent, $data, $path): Attendance {
            $existing = Attendance::withTrashed()
                ->where('kajian_event_id', $event->id)
                ->where('parent_id', $parent->id)
                ->lockForUpdate()
                ->first();

            if ($existing && ! $existing->trashed() && $existing->validation_status !== Attendance::VALIDATION_REJECTED) {
                throw new PublicMustawaOneFormException('Pengajuan wali untuk kajian ini sudah tercatat.');
            }

            $attributes = [
                'kajian_event_id' => $event->id,
                'parent_id' => $parent->id,
                'student_id' => $student->id,
                'student_enrollment_id' => StudentEnrollment::ensureForEvent($student, $event)?->id,
                'status' => $data['status'],
                'method' => Attendance::METHOD_PUBLIC_FORM,
                'proof_file' => $path,
                'notes' => $data['notes'] ?? null,
                'validation_status' => Attendance::VALIDATION_PENDING,
                'validated_by' => null,
                'validated_at' => null,
                'rejection_reason' => null,
                'scanned_at' => null,
                'scan_location' => null,
                'device_info' => 'Form Publik Mustawa 1',
            ];

            if (! $existing) {
                return Attendance::create($attributes);
            }

            if ($existing->proof_file && $existing->proof_file !== $path) {
                AttendanceProofHistory::firstOrCreate([
                    'attendance_id' => $existing->id,
                    'proof_file' => $existing->proof_file,
                    'source' => 'public_form_reupload',
                ], ['created_at' => now()]);
            }

            if ($existing->trashed()) {
                $existing->restore();
            }

            $existing->fill($attributes);
            $existing->save();

            return $existing;
        }, 2);

        try {
            app(AttendanceProofReviewService::class)->queue($attendance->fresh());
        } catch (\Throwable $exception) {
            report($exception);
        }

        return $attendance;
    }

    private function findEligibleStudent(int $studentId): ?Student
    {
        $student = Student::query()
            ->active()
            ->with([
                'classRoom',
                'parents.students.classRoom',
                'parents.students.enrollments',
            ])
            ->whereHas('classRoom', fn ($query) => $query->where('level', '1'))
            ->find($studentId);

        return $student && $this->eligibleParentTypes($student) !== [] ? $student : null;
    }

    private function resolveEligibleParent(Student $student, string $type): ?ParentModel
    {
        $matches = $student->parents
            ->filter(fn (ParentModel $parent) => $parent->type === $type)
            ->filter(fn (ParentModel $parent) => $this->isNewGuardian($parent))
            ->values();

        return $matches->count() === 1 ? $matches->first() : null;
    }

    /** @return array<int, string> */
    private function eligibleParentTypes(Student $student): array
    {
        return $student->parents
            ->filter(fn (ParentModel $parent) => in_array($parent->type, ['father', 'mother'], true))
            ->filter(fn (ParentModel $parent) => $this->isNewGuardian($parent))
            ->pluck('type')
            ->unique()
            ->values()
            ->all();
    }

    private function isNewGuardian(ParentModel $parent): bool
    {
        $students = $parent->relationLoaded('students')
            ? $parent->students
            : $parent->students()->with(['classRoom', 'enrollments'])->get();

        return ! $students->contains(function (Student $child): bool {
            $currentLevel = (string) ($child->classRoom?->level ?? '');

            if (in_array($currentLevel, ['2', '3', '4', '5', '6'], true)) {
                return true;
            }

            $enrollments = $child->relationLoaded('enrollments')
                ? $child->enrollments
                : $child->enrollments()->get();

            return $enrollments->contains(fn (StudentEnrollment $enrollment) => in_array((string) $enrollment->class_level, ['2', '3', '4', '5', '6'], true));
        });
    }

    private function targetsMustawaOne(KajianEvent $event): bool
    {
        return $event->targetClasses->isEmpty()
            || $event->targetClasses->contains(fn ($class) => (string) $class->level === '1');
    }

    private function ensureNoActiveSubmission(KajianEvent $event, ParentModel $parent): void
    {
        $existing = Attendance::query()
            ->where('kajian_event_id', $event->id)
            ->where('parent_id', $parent->id)
            ->first();

        if ($existing && $existing->validation_status !== Attendance::VALIDATION_REJECTED) {
            throw new PublicMustawaOneFormException('Pengajuan wali untuk kajian ini sudah tercatat.');
        }
    }
}
