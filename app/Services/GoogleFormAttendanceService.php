<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\GoogleFormSubmission;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoogleFormAttendanceService
{
    /**
     * Store and process one Google Form response. The response ID is the
     * idempotency key, so Apps Script retries are safe.
     */
    public function receive(array $payload, ?string $formId = null): array
    {
        $payload['event_date'] = Carbon::parse($payload['event_date'])->toDateString();

        return DB::transaction(function () use ($payload, $formId): array {
            $submission = GoogleFormSubmission::query()
                ->where('response_id', $payload['response_id'])
                ->lockForUpdate()
                ->first();

            if (! $submission) {
                $submission = GoogleFormSubmission::create([
                    'response_id' => $payload['response_id'],
                    'form_id' => $formId,
                    'event_date' => $payload['event_date'],
                    'requested_status' => $payload['status'],
                    'processing_status' => GoogleFormSubmission::STATUS_RECEIVED,
                    'payload' => $payload,
                    'submitted_at' => $payload['submitted_at'] ?? null,
                    'received_at' => now(),
                ]);
            } elseif (! in_array($submission->processing_status, [
                GoogleFormSubmission::STATUS_PROCESSED,
                GoogleFormSubmission::STATUS_DUPLICATE,
            ], true)) {
                $submission->update([
                    'form_id' => $formId ?: $submission->form_id,
                    'event_date' => $payload['event_date'],
                    'requested_status' => $payload['status'],
                    'processing_status' => GoogleFormSubmission::STATUS_RECEIVED,
                    'error_code' => null,
                    'error_message' => null,
                    'payload' => $payload,
                    'submitted_at' => $payload['submitted_at'] ?? $submission->submitted_at,
                    'received_at' => now(),
                    'processed_at' => null,
                ]);
            } else {
                return $this->resultFor($submission, 'duplicate_response');
            }

            return $this->processLocked($submission->fresh());
        }, 2);
    }

    public function retry(GoogleFormSubmission $submission): array
    {
        return DB::transaction(function () use ($submission): array {
            $locked = GoogleFormSubmission::query()->lockForUpdate()->findOrFail($submission->id);

            if (! $locked->isRetryable()) {
                return $this->resultFor($locked, 'not_retryable');
            }

            $locked->update([
                'processing_status' => GoogleFormSubmission::STATUS_RECEIVED,
                'error_code' => null,
                'error_message' => null,
                'processed_at' => null,
            ]);

            return $this->processLocked($locked->fresh());
        }, 2);
    }

    /** @return array{status: string, code: string, message: string, submission_id: int, attendance_id: int|null} */
    private function processLocked(GoogleFormSubmission $submission): array
    {
        $payload = $submission->payload ?? [];
        $eventDate = $payload['event_date'] ?? $submission->event_date?->toDateString();

        $events = KajianEvent::query()
            ->whereDate('date', $eventDate)
            ->where('category', 'kajian')
            ->orderByDesc('id')
            ->get();

        if ($events->count() === 0) {
            return $this->markUnresolved($submission, 'event_not_found', 'Kajian pada tanggal tersebut belum tersedia.');
        }

        if ($events->count() > 1) {
            return $this->markUnresolved($submission, 'multiple_events', 'Terdapat lebih dari satu kajian pada tanggal tersebut.');
        }

        $event = $events->first();

        if (! $event->isOpen()) {
            return $this->markUnresolved(
                $submission,
                'event_closed',
                'Presensi untuk kajian ini sudah ditutup.'
            );
        }

        $student = $this->resolveMustawaOneStudent($payload);

        if (! $student) {
            return $this->markUnresolved($submission, 'student_not_found', 'Ananda tidak ditemukan sebagai santri Mustawa 1 aktif.');
        }

        $parentResult = $this->resolveParent($student, $payload);

        if ($parentResult['matches']->count() === 0) {
            return $this->markUnresolved($submission, 'parent_not_found', 'Data wali tidak cocok dengan relasi ananda.');
        }

        if ($parentResult['matches']->count() > 1) {
            return $this->markUnresolved($submission, 'parent_ambiguous', 'Terdapat lebih dari satu wali yang cocok.');
        }

        /** @var ParentModel $parent */
        $parent = $parentResult['matches']->first();
        $event->loadMissing('targetClasses');

        if (! $event->targetsParent($parent)) {
            return $this->markUnresolved($submission, 'event_not_targeted', 'Wali tidak termasuk sasaran kelas pada kajian tersebut.');
        }

        $studentEnrollment = StudentEnrollment::ensureForEvent($student, $event);
        $existingAttendance = Attendance::withTrashed()
            ->where('kajian_event_id', $event->id)
            ->where('parent_id', $parent->id)
            ->lockForUpdate()
            ->first();

        if ($existingAttendance && ! $existingAttendance->trashed()) {
            $submission->update([
                'kajian_event_id' => $event->id,
                'student_id' => $student->id,
                'parent_id' => $parent->id,
                'attendance_id' => $existingAttendance->id,
                'processing_status' => GoogleFormSubmission::STATUS_DUPLICATE,
                'error_code' => 'attendance_exists',
                'error_message' => 'Presensi wali untuk kajian ini sudah tercatat.',
                'processed_at' => now(),
            ]);

            return $this->resultFor($submission->fresh(), 'duplicate_attendance');
        }

        $attributes = [
            'kajian_event_id' => $event->id,
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'student_enrollment_id' => $studentEnrollment?->id,
            'status' => $payload['status'],
            'method' => Attendance::METHOD_GOOGLE_FORM,
            'proof_file' => null,
            'notes' => $payload['notes'] ?? null,
            'validation_status' => Attendance::VALIDATION_PENDING,
            'validated_by' => null,
            'validated_at' => null,
            'rejection_reason' => null,
            'scanned_at' => null,
            'scan_location' => null,
            'device_info' => 'Google Form Mustawa 1',
        ];

        if ($existingAttendance) {
            $existingAttendance->restore();
            $existingAttendance->fill($attributes);
            $existingAttendance->save();
            $attendance = $existingAttendance;
        } else {
            $attendance = Attendance::create($attributes);
        }

        $submission->update([
            'kajian_event_id' => $event->id,
            'attendance_id' => $attendance->id,
            'student_id' => $student->id,
            'parent_id' => $parent->id,
            'processing_status' => GoogleFormSubmission::STATUS_PROCESSED,
            'error_code' => null,
            'error_message' => null,
            'processed_at' => now(),
        ]);

        return $this->resultFor($submission->fresh(), 'processed');
    }

    private function resolveMustawaOneStudent(array $payload): ?Student
    {
        $student = Student::query()
            ->with(['classRoom', 'parents.user'])
            ->active()
            ->where('nis', (string) ($payload['student_reference'] ?? ''))
            ->whereHas('classRoom', fn ($query) => $query->where('level', '1'))
            ->first();

        if (! $student) {
            return null;
        }

        $submittedName = $this->normalizeName((string) ($payload['student_name'] ?? ''));

        return $submittedName !== '' && $submittedName !== $this->normalizeName($student->name)
            ? null
            : $student;
    }

    /** @return array{matches: \Illuminate\Support\Collection} */
    private function resolveParent(Student $student, array $payload): array
    {
        $type = (string) ($payload['parent_type'] ?? '');
        $name = $this->normalizeName((string) ($payload['parent_name'] ?? ''));
        $phone = $this->normalizePhone((string) ($payload['parent_phone'] ?? ''));

        $matches = $student->parents
            ->filter(fn (ParentModel $parent) => $parent->type === $type)
            ->filter(function (ParentModel $parent) use ($name, $phone): bool {
                return $this->normalizeName((string) $parent->user?->name) === $name
                    && $this->normalizePhone((string) $parent->user?->phone) === $phone;
            })
            ->values();

        return ['matches' => $matches];
    }

    private function markUnresolved(GoogleFormSubmission $submission, string $code, string $message): array
    {
        $submission->update([
            'processing_status' => GoogleFormSubmission::STATUS_UNRESOLVED,
            'error_code' => $code,
            'error_message' => $message,
            'processed_at' => now(),
        ]);

        return $this->resultFor($submission->fresh(), $code);
    }

    private function resultFor(GoogleFormSubmission $submission, string $code): array
    {
        return [
            'status' => $submission->processing_status,
            'code' => $code,
            'message' => $submission->error_message ?: 'Respons Google Form diterima.',
            'submission_id' => $submission->id,
            'attendance_id' => $submission->attendance_id,
        ];
    }

    private function normalizeName(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->value();
    }

    private function normalizePhone(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?: '';

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }
}
