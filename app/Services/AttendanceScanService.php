<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceProofHistory;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\StudentEnrollment;
use App\Services\ParentQrCodeService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AttendanceScanService
{
    public function __construct(private ParentQrCodeService $qrCodes)
    {
    }

    public function process(KajianEvent $event, string $qrCode, int $userId, ?string $deviceInfo = null): array
    {
        $parent = $this->qrCodes->resolve(trim($qrCode));

        if (! $parent) {
            return [
                'status' => 'error',
                'message' => 'QR Code tidak ditemukan dalam sistem.',
            ];
        }

        return $this->processParent($event, $parent, $userId, Attendance::METHOD_SCAN_QR, $deviceInfo);
    }

    /**
     * Record a manual check-in through the same guarded path as QR scans.
     * Keeping one write path prevents different validation/enrollment rules.
     */
    public function processManual(KajianEvent $event, ParentModel $parent, int $userId, ?string $deviceInfo = null): array
    {
        return $this->processParent($event, $parent, $userId, Attendance::METHOD_MANUAL, $deviceInfo);
    }

    private function processParent(
        KajianEvent $event,
        ParentModel $parent,
        int $userId,
        string $method,
        ?string $deviceInfo = null
    ): array {
        $deviceInfo = $deviceInfo ? mb_substr($deviceInfo, 0, 255) : null;
        $parent->loadMissing([
            'user:id,name',
            'students:id,name,class_id,student_status,is_active',
            'students.classRoom:id,name',
        ]);

        if ($parent->isPureTeacher()) {
            return [
                'status' => 'error',
                'message' => 'Guru murni tidak perlu scan QR. Silakan upload catatan kajian dari dashboard.',
            ];
        }

        $event->loadMissing('targetClasses');

        if (! $event->targetsParent($parent)) {
            return [
                'status' => 'error',
                'message' => 'Wali santri tidak termasuk kelas sasaran kegiatan ini.',
            ];
        }

        $students = $event->targetedStudentsForParent($parent);
        $studentId = $students->first()?->id;
        $childDisplayNames = $students
            ->map(fn ($student) => $student->name.($student->classRoom ? ' ('.$student->classRoom->name.')' : ''))
            ->values()
            ->all();

        $needsProof = $parent->isWaliTeacher() && ($event->policy['guru_hadir_fisik_requires_proof'] ?? true);

        try {
            $recordResult = DB::transaction(function () use (
                $event,
                $parent,
                $studentId,
                $students,
                $userId,
                $deviceInfo,
                $method,
                $needsProof
            ): array {
                $attendance = Attendance::withTrashed()
                    ->where('kajian_event_id', $event->id)
                    ->where('parent_id', $parent->id)
                    ->lockForUpdate()
                    ->first();

                if ($attendance && ! $attendance->trashed()) {
                    return ['action' => 'duplicate'];
                }

                $studentEnrollmentId = StudentEnrollment::ensureForEvent($students->first(), $event)?->id;
                $attributes = [
                    'student_id' => $studentId,
                    'student_enrollment_id' => $studentEnrollmentId,
                    'status' => Attendance::STATUS_HADIR_FISIK,
                    'method' => $method,
                    'validation_status' => $needsProof ? Attendance::VALIDATION_PENDING : Attendance::VALIDATION_APPROVED,
                    'validated_by' => $needsProof ? null : $userId,
                    'validated_at' => $needsProof ? null : now(),
                    'rejection_reason' => null,
                    'scanned_at' => now(),
                    'device_info' => $deviceInfo,
                ];

                if ($attendance) {
                    if ($attendance->proof_file) {
                        AttendanceProofHistory::firstOrCreate([
                            'attendance_id' => $attendance->id,
                            'proof_file' => $attendance->proof_file,
                            'source' => 'qr_restore',
                        ], ['created_at' => now()]);
                    }

                    $attendance->restore();
                    $attendance->update($attributes + [
                        'proof_file' => null,
                        'notes' => null,
                        'ai_validation_status' => null,
                        'ai_validation_confidence' => null,
                        'ai_validation_reason' => null,
                        'ai_validation_model' => null,
                        'ai_validation_payload' => null,
                        'ai_validated_at' => null,
                    ]);

                    return ['action' => 'restored'];
                }

                Attendance::create([
                    'kajian_event_id' => $event->id,
                    'parent_id' => $parent->id,
                    ...$attributes,
                ]);

                return ['action' => 'created'];
            }, 2);
        } catch (QueryException $exception) {
            if ($this->isDuplicateAttendance($exception)) {
                return [
                    'status' => 'warning',
                    'message' => $parent->user->name.' sudah tercatat hadir.',
                    'payload' => [
                        'parentName' => $parent->user->name,
                    ],
                ];
            }

            throw $exception;
        }

        if (($recordResult['action'] ?? null) === 'duplicate') {
            return [
                'status' => 'warning',
                'message' => $parent->user->name.' sudah tercatat hadir.',
                'payload' => [
                    'parentName' => $parent->user->name,
                ],
            ];
        }

        $childNameDisplay = count($childDisplayNames) > 0
            ? (count($childDisplayNames).' Santri: '.implode(', ', $childDisplayNames))
            : 'Tidak ada data santri';

        $parentType = match ($parent->type) {
            'father' => 'Bapak',
            'mother' => 'Ibu',
            'teacher' => 'Ustadz/ah',
            default => 'Peserta',
        };

        $needsProof = $parent->isWaliTeacher() && ($event->policy['guru_hadir_fisik_requires_proof'] ?? true);
        $event->updateAttendanceCount();
        $message = ($parent->isWaliTeacher() && $needsProof)
            ? "Selamat Datang, {$parentType} {$parent->user->name}. Berhasil mencatat, mohon ingatkan untuk upload catatan kajian di dashboard."
            : "Selamat Datang, {$parentType} {$parent->user->name}. Berhasil mencatat presensi untuk ".($students->count() ?: 1).' santri.';

        return [
            'status' => 'success',
            'message' => $message,
            'payload' => [
                'parentName' => $parent->user->name,
                'parentType' => $parent->type_display,
                'childName' => $childNameDisplay,
                'time' => now()->format('H:i'),
            ],
        ];
    }

    private function isDuplicateAttendance(QueryException $exception): bool
    {
        $errorInfo = $exception->errorInfo;

        return ($errorInfo[0] ?? null) === '23000' || ($errorInfo[1] ?? null) === 1062;
    }
}
