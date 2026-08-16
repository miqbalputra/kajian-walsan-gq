<?php

namespace App\Services;

use App\Models\AttendanceRosterSnapshot;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\StudentEnrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceFinalizationService
{
    /**
     * Freeze the intended guardian roster before marking attendance as closed.
     * Alpha remains a derived status; it is never fabricated as an attendance
     * record, so later corrections do not collide with the unique attendance key.
     */
    public function close(KajianEvent $event, ?int $closedBy = null): KajianEvent
    {
        return DB::transaction(function () use ($event, $closedBy): KajianEvent {
            $event = KajianEvent::query()
                ->with(['targetClasses', 'academicYear'])
                ->lockForUpdate()
                ->findOrFail($event->id);

            $now = now();
            $this->replaceRosterSnapshot($event, $this->currentParticipants($event), $now, true);

            $event->update([
                'status' => 'closed',
                'closed_at' => $now,
                'closed_by' => $closedBy,
            ]);

            return $event->fresh(['targetClasses']);
        });
    }

    public function reopen(KajianEvent $event): KajianEvent
    {
        return DB::transaction(function () use ($event): KajianEvent {
            $event = KajianEvent::query()->lockForUpdate()->findOrFail($event->id);

            AttendanceRosterSnapshot::where('kajian_event_id', $event->id)->delete();
            $event->update([
                'status' => 'open',
                'closed_at' => null,
                'closed_by' => null,
            ]);

            return $event->fresh(['targetClasses']);
        });
    }

    /**
     * Reconstruct the roster for an old event that was closed before roster
     * snapshots existed. This deliberately does not alter the event's close
     * date or its existing attendance records.
     *
     * The result is only as complete as the enrolment and family links that
     * still exist today. New events always use close(), which takes an exact
     * snapshot at the moment an administrator closes attendance.
     */
    public function backfillLegacyClosedEvent(KajianEvent $event): int
    {
        return DB::transaction(function () use ($event): int {
            $event = KajianEvent::query()
                ->with(['targetClasses', 'academicYear'])
                ->lockForUpdate()
                ->findOrFail($event->id);

            if ($event->status !== 'closed') {
                throw new \LogicException('Hanya presensi yang sudah ditutup yang dapat dibackfill.');
            }

            if ($event->attendanceRosterSnapshots()->exists()) {
                return 0;
            }

            return $this->replaceRosterSnapshot($event, $this->legacyParticipants($event), now(), false);
        });
    }

    /**
     * Estimate the old target roster using the event's academic-year
     * enrolments. It includes guardians with an attendance record even when
     * a historical family/enrolment relation can no longer be reconstructed.
     */
    public function legacyParticipantCount(KajianEvent $event): int
    {
        $event->loadMissing(['targetClasses', 'academicYear']);

        return $this->legacyParticipants($event)->count();
    }

    /**
     * @return Collection<int, array{guardian: ParentModel, student: \App\Models\Student|null, enrollment: StudentEnrollment|null}>
     */
    protected function currentParticipants(KajianEvent $event, bool $ensureEnrollment = true): Collection
    {
        return ParentModel::guardians()
            ->targetedByEvent($event)
            ->with(['students.classRoom'])
            ->orderBy('id')
            ->get()
            ->map(function (ParentModel $guardian) use ($event, $ensureEnrollment): array {
                $student = $event->targetedStudentsForParent($guardian)->first();

                return [
                    'guardian' => $guardian,
                    'student' => $student,
                    'enrollment' => $ensureEnrollment
                        ? StudentEnrollment::ensureForEvent($student, $event)
                        : StudentEnrollment::forStudentAndYear($student?->id, $event->academic_year_id),
                ];
            });
    }

    /**
     * @return Collection<int, array{guardian: ParentModel, student: \App\Models\Student|null, enrollment: StudentEnrollment|null}>
     */
    protected function legacyParticipants(KajianEvent $event): Collection
    {
        if (! $event->academic_year_id) {
            return $this->currentParticipants($event, false);
        }

        $targetClassIds = $event->targetClassIds()->map(fn ($id) => (int) $id)->all();
        $forEventYear = function ($query) use ($event, $targetClassIds): void {
            $query->where('academic_year_id', $event->academic_year_id)
                ->when($targetClassIds !== [], fn ($query) => $query->whereIn('class_id', $targetClassIds));
        };

        $historicalParticipants = ParentModel::guardians()
            ->whereHas('students.enrollments', $forEventYear)
            ->with([
                'students' => function ($query) use ($forEventYear): void {
                    $query->with([
                        'classRoom',
                        'enrollments' => $forEventYear,
                    ])->whereHas('enrollments', $forEventYear);
                },
            ])
            ->orderBy('id')
            ->get()
            ->map(function (ParentModel $guardian): array {
                $student = $guardian->students->first();

                return [
                    'guardian' => $guardian,
                    'student' => $student,
                    'enrollment' => $student?->enrollments->first(),
                ];
            })
            ->keyBy(fn (array $participant) => $participant['guardian']->id);

        // A person with a stored attendance is known to have been a target.
        // Preserve that evidence even if an old family or enrolment relation
        // was later removed from the operational data.
        $recordedParticipants = ParentModel::guardians()
            ->whereIn('id', $event->attendances()->pluck('parent_id')->filter()->unique())
            ->with(['students.classRoom', 'students.enrollments' => $forEventYear])
            ->orderBy('id')
            ->get()
            ->map(function (ParentModel $guardian): array {
                $student = $guardian->students->first();

                return [
                    'guardian' => $guardian,
                    'student' => $student,
                    'enrollment' => $student?->enrollments->first(),
                ];
            })
            ->keyBy(fn (array $participant) => $participant['guardian']->id);

        $participants = $historicalParticipants
            ->union($recordedParticipants)
            ->sortKeys()
            ->values();

        // Student-enrollment history was introduced after some of the legacy
        // events. When it is entirely unavailable, use the current eligible
        // roster as the best remaining reconstruction, still without writing
        // anything during preview/backfill.
        return $participants->isNotEmpty()
            ? $participants
            : $this->currentParticipants($event, false);
    }

    /**
     * @param Collection<int, array{guardian: ParentModel, student: \App\Models\Student|null, enrollment: StudentEnrollment|null}> $participants
     */
    protected function replaceRosterSnapshot(
        KajianEvent $event,
        Collection $participants,
        \DateTimeInterface $now,
        bool $replaceExisting
    ): int {
        $rows = $participants->map(function (array $participant) use ($event, $now): array {
            /** @var ParentModel $guardian */
            $guardian = $participant['guardian'];
            $student = $participant['student'];
            $enrollment = $participant['enrollment'];

            return [
                'kajian_event_id' => $event->id,
                'parent_id' => $guardian->id,
                'student_id' => $student?->id,
                'student_enrollment_id' => $enrollment?->id,
                'class_id' => $enrollment?->class_id ?? $student?->class_id,
                'student_name' => $student?->name,
                'class_name' => $enrollment?->class_name ?? $student?->classRoom?->name,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        if ($replaceExisting) {
            AttendanceRosterSnapshot::where('kajian_event_id', $event->id)->delete();
        }

        if ($rows !== []) {
            AttendanceRosterSnapshot::insert($rows);
        }

        return count($rows);
    }
}
