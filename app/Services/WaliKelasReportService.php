<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceRosterSnapshot;
use App\Models\AttendanceRosterSnapshotStudent;
use App\Models\ClassRoom;
use App\Models\GuardianFollowUpCase;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** All reads are scoped to the teacher's current roster, including historical reads. */
class WaliKelasReportService
{
    public function managedClass(bool $lock = false): ?ClassRoom
    {
        $user = auth()->user();
        abort_unless($user && ($user->isWaliKelas() || $user->isAdmin()), 403);

        return ClassRoom::where('teacher_id', $user->id)->when($lock, fn ($q) => $q->lockForUpdate())->first();
    }

    public function authorizedStudent(int $id, bool $lock = false): Student
    {
        $class = $this->managedClass($lock);
        abort_unless($class, 404);

        return Student::active()->where('class_id', $class->id)
            ->when($lock, fn ($q) => $q->lockForUpdate())->findOrFail($id);
    }

    public function dataset(?int $studentId = null): array
    {
        $class = $this->managedClass();
        $activeYear = AcademicYear::active();
        if ($studentId !== null) {
            $this->authorizedStudent($studentId);
        }
        $students = $class ? Student::active()->where('class_id', $class->id)
            ->when($studentId, fn ($q) => $q->whereKey($studentId))
            ->with(['parents.user', 'classRoom', 'enrollments.classRoom'])->orderBy('name')->get() : collect();
        $ids = $students->pluck('id')->all();
        if ($students->isEmpty()) {
            return ['class' => $class, 'activeYear' => $activeYear, 'students' => $students, 'events' => collect(), 'rows' => collect(), 'cases' => collect(), 'attention' => collect()];
        }

        $children = AttendanceRosterSnapshotStudent::whereIn('student_id', $ids)->with('rosterSnapshot')->get();
        $primary = AttendanceRosterSnapshot::whereIn('student_id', $ids)->get();
        $direct = Attendance::whereIn('student_id', $ids)->with('studentEnrollment')->get();
        $knownEventIds = $children->pluck('rosterSnapshot.kajian_event_id')->merge($primary->pluck('kajian_event_id'))->merge($direct->pluck('kajian_event_id'))->filter()->unique();
        $activeYearId = $activeYear?->id;
        $yearIds = $students->flatMap(fn ($student) => $student->enrollments->pluck('academic_year_id'))->push($activeYearId)->filter()->unique();
        $classIds = $students->flatMap(fn ($student) => $student->enrollments->pluck('class_id'))->push($class->id)->filter()->unique();
        $events = KajianEvent::with(['academicYear', 'targetClasses'])
            ->where(function ($q) use ($knownEventIds, $yearIds, $classIds) {
                $q->whereIn('id', $knownEventIds)->orWhere(function ($q) use ($yearIds, $classIds) {
                    $q->whereIn('academic_year_id', $yearIds)->where(function ($q) use ($classIds) {
                        $q->whereDoesntHave('targetClasses')->orWhereHas('targetClasses', fn ($q) => $q->whereIn('classes.id', $classIds));
                    });
                });
            })->orderByDesc('date')->orderByDesc('time_start')->orderByDesc('id')->get();
        // Presence of any frozen roster makes the snapshot authoritative for that event.
        $frozen = AttendanceRosterSnapshot::whereIn('kajian_event_id', $events->modelKeys())->distinct()->pluck('kajian_event_id')->flip();
        $parentIds = $students->flatMap(fn ($student) => $student->parents->modelKeys())
            ->merge($children->pluck('parent_id'))->merge($primary->pluck('parent_id'))->merge($direct->pluck('parent_id'))->unique();
        $parents = ParentModel::whereIn('id', $parentIds)->whereIn('type', ['father', 'mother'])->with('user')->get()->keyBy('id');
        $attendances = Attendance::whereIn('kajian_event_id', $events->modelKeys())->whereIn('parent_id', $parentIds)
            ->get()->keyBy(fn ($row) => $row->kajian_event_id.':'.$row->parent_id);
        $childGroups = $children->groupBy(fn ($row) => $row->rosterSnapshot->kajian_event_id.':'.$row->student_id);
        $primaryGroups = $primary->groupBy(fn ($row) => $row->kajian_event_id.':'.$row->student_id);
        $directGroups = $direct->groupBy(fn ($row) => $row->kajian_event_id.':'.$row->student_id);
        $statusService = app(GuardianAttendanceReportService::class);
        $rows = collect();

        foreach ($students as $student) {
            foreach ($events as $event) {
                $key = $event->id.':'.$student->id;
                $enrollment = $student->enrollments->firstWhere('academic_year_id', $event->academic_year_id);
                $targetClass = $enrollment?->class_id ?? ((int) $event->academic_year_id === (int) $activeYearId ? $class->id : null);
                $eligible = $targetClass && ($event->targetClasses->isEmpty() || $event->targetClasses->contains('id', $targetClass))
                    && (! $enrollment?->started_at || $enrollment->started_at->startOfDay()->lte($event->date))
                    && (! $enrollment?->ended_at || $enrollment->ended_at->endOfDay()->gte($event->date));
                $state = $statusService->eventState($event);
                $participants = collect();
                foreach ($childGroups->get($key, collect()) as $snapshot) {
                    $participants->put($snapshot->parent_id, ['confirmed' => true, 'snapshot' => $snapshot]);
                }
                foreach ($primaryGroups->get($key, collect()) as $snapshot) {
                    if (! $participants->has($snapshot->parent_id)) {
                        $participants->put($snapshot->parent_id, ['confirmed' => true, 'snapshot' => $snapshot]);
                    }
                }
                foreach ($directGroups->get($key, collect()) as $attendance) {
                    if (! $participants->has($attendance->parent_id)) {
                        $participants->put($attendance->parent_id, ['confirmed' => true, 'snapshot' => null]);
                    }
                }
                if ($eligible && ! ($state === 'ended' && $frozen->has($event->id))) {
                    foreach ($student->parents->whereIn('type', ['father', 'mother']) as $parent) {
                        if (! $participants->has($parent->id)) {
                            $familyKnown = ! $parent->pivot->created_at || $parent->pivot->created_at->lte($event->date->copy()->endOfDay());
                            $participants->put($parent->id, ['confirmed' => $state !== 'ended' || ($enrollment && $familyKnown), 'snapshot' => null]);
                        }
                    }
                }
                foreach ($participants as $parentId => $context) {
                    $parent = $parents->get($parentId);
                    if (! $parent) {
                        continue;
                    }
                    $attendance = $attendances->get($event->id.':'.$parentId);
                    $derived = $context['confirmed'] ? $statusService->deriveStatus($attendance, $state)
                        : ['status' => 'incomplete', 'label' => 'Data belum lengkap', 'reason' => 'Sasaran peserta atau hubungan keluarga pada kajian lama belum dapat dipastikan.'];
                    if ($derived['status'] === 'alpha' && $state !== 'ended') {
                        $derived = ['status' => 'not_started', 'label' => $state === 'not_started' ? 'Belum mulai' : 'Presensi dibuka', 'reason' => 'Alfa baru ditentukan setelah presensi ditutup.'];
                    }
                    $snapshot = $context['snapshot'];
                    $rows->push([
                        'key' => $student->id.':'.$parentId.':'.$event->id,
                        'student_id' => $student->id, 'student_name' => $student->name, 'nis' => $student->nis,
                        'parent_id' => $parentId, 'parent_name' => $snapshot?->parent_name ?: ($parent->user?->name ?? '-'),
                        'parent_type' => $snapshot?->parent_type ?: $parent->type,
                        'event_id' => $event->id, 'event_title' => $event->title, 'date' => $event->date->format('Y-m-d'),
                        'academic_year_id' => $event->academic_year_id, 'academic_year' => $event->academicYear?->name ?? '-',
                        'class_name' => $snapshot?->class_name ?? $enrollment?->class_name ?? ($eligible ? $class->name : 'Data belum lengkap'),
                        'status' => $derived['status'], 'label' => $derived['label'], 'reason' => $derived['reason'], 'event_state' => $state,
                        'method' => $attendance?->method_display ?? '-', 'validation' => $attendance?->validation_display ?? '-',
                        'recorded_at' => $attendance?->scanned_at?->format('d/m/Y H:i') ?? $attendance?->created_at?->format('d/m/Y H:i') ?? '-',
                        'notes' => $attendance?->notes,
                    ]);
                }
            }
        }

        $eventOrder = array_flip($events->modelKeys());
        $rows = $rows->sortBy(fn ($row) => [$eventOrder[$row['event_id']], mb_strtolower($row['student_name']), $row['parent_type'] === 'father' ? 0 : 1, $row['parent_id']])->values();

        $cases = GuardianFollowUpCase::whereIn('student_id', $ids)
            ->with(['parent.user', 'kajianEvent', 'latestEntry'])
            ->when($studentId, fn ($q) => $q->with('entries'))->get()->keyBy(fn ($case) => $case->student_id.':'.$case->parent_id.':'.$case->kajian_event_id);
        $attention = $rows->where('status', 'alpha')->where('event_state', 'ended')->keyBy('key');
        foreach ($cases as $key => $case) {
            if ($case->status === 'completed') {
                $attention->forget($key);
            } elseif (! $attention->has($key)) {
                $student = $students->firstWhere('id', $case->student_id);
                $attention->put($key, ['key' => $key, 'student_id' => $student->id, 'student_name' => $student->name,
                    'parent_id' => $case->parent_id, 'parent_name' => $case->parent?->user?->name ?? '-',
                    'parent_type' => $case->parent?->type, 'event_id' => $case->kajian_event_id,
                    'event_title' => $case->kajianEvent?->title ?? 'Kajian diarsipkan', 'date' => $case->kajianEvent?->date?->format('Y-m-d') ?? $case->created_at->format('Y-m-d')]);
            }
        }
        $attention = $attention->map(function ($row) use ($cases) {
            $case = $cases->get($row['key']);

            return $row + ['case' => $case, 'follow_up_status' => $case?->status ?? 'open',
                'overdue' => $case?->next_follow_up_at?->lt(today()) ?? false];
        })->sortBy(fn ($row) => [! $row['overdue'], $row['overdue'] ? $row['case']->next_follow_up_at->format('Y-m-d') : $row['date'], $row['date'], $row['key']])->values();

        return compact('class', 'activeYear', 'students', 'events', 'rows', 'cases', 'attention');
    }

    public function filter(Collection $rows, string $year = '', string $event = '', string $status = '', string $search = ''): Collection
    {
        $needle = mb_strtolower(trim($search));

        return $rows->when($year !== '', fn ($rows) => $rows->where('academic_year_id', (int) $year))
            ->when($event !== '', fn ($rows) => $rows->where('event_id', (int) $event))
            ->when($status !== '', fn ($rows) => $rows->filter(fn ($row) => $status === 'present' ? in_array($row['status'], ['hadir_fisik', 'hadir_online'], true) : $row['status'] === $status))
            ->when($needle !== '', fn ($rows) => $rows->filter(fn ($row) => str_contains(mb_strtolower($row['student_name'].' '.$row['nis'].' '.$row['parent_name']), $needle)))->values();
    }

    public function summary(Collection $rows): array
    {
        $unique = $rows->whereNotIn('status', ['incomplete'])->unique(fn ($row) => $row['parent_id'].':'.$row['event_id']);
        $counts = $unique->countBy('status');
        $present = ($counts['hadir_fisik'] ?? 0) + ($counts['hadir_online'] ?? 0);

        return ['total' => $unique->count(), 'present' => $present, 'rate' => $unique->count() ? round($present * 100 / $unique->count(), 1) : null,
            'physical' => $counts['hadir_fisik'] ?? 0, 'online' => $counts['hadir_online'] ?? 0,
            'izin' => $counts['izin'] ?? 0, 'alpha' => $counts['alpha'] ?? 0,
            'pending' => $counts['pending'] ?? 0, 'rejected' => $counts['rejected'] ?? 0, 'incomplete' => $rows->where('status', 'incomplete')->count()];
    }

    public function saveFollowUp(int $studentId, int $parentId, int $eventId, array $values): GuardianFollowUpCase
    {
        return DB::transaction(function () use ($studentId, $parentId, $eventId, $values) {
            $student = $this->authorizedStudent($studentId, true);
            $data = $this->dataset($studentId);
            $key = $studentId.':'.$parentId.':'.$eventId;
            $existing = $data['cases']->get($key);
            $row = $data['rows']->firstWhere('key', $key);
            abort_unless($existing || ($row && $row['status'] === 'alpha' && $row['event_state'] === 'ended'), 404);
            $case = GuardianFollowUpCase::firstOrCreate(['student_id' => $studentId, 'parent_id' => $parentId, 'kajian_event_id' => $eventId],
                ['class_id' => $student->class_id]);
            $case->update(['status' => $values['status'], 'next_follow_up_at' => $values['status'] === 'completed' ? null : ($values['next_follow_up_at'] ?: null)]);
            $case->entries()->create([
                'author_id' => auth()->id(), 'author_name' => auth()->user()->name,
                'contacted_at' => $values['contacted_at'], 'channel' => $values['channel'], 'result' => $values['result'],
                'next_action' => $values['next_action'] ?: null, 'status_after' => $values['status'], 'next_follow_up_at' => $case->next_follow_up_at,
            ]);

            return $case;
        });
    }
}
