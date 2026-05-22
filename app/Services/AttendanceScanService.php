<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AttendanceScanService
{
    public function process(KajianEvent $event, string $qrCode, int $userId, ?string $deviceInfo = null): array
    {
        $deviceInfo = $deviceInfo ? mb_substr($deviceInfo, 0, 255) : null;

        $parent = ParentModel::query()
            ->select(['id', 'user_id', 'type', 'is_teacher'])
            ->with([
                'user:id,name',
                'students:id,name,class_room_id',
                'students.classRoom:id,name',
            ])
            ->where('qr_code_string', trim($qrCode))
            ->first();

        if (!$parent) {
            return [
                'status' => 'error',
                'message' => 'QR Code tidak ditemukan dalam sistem.',
            ];
        }

        if ($parent->isPureTeacher()) {
            return [
                'status' => 'error',
                'message' => 'Guru murni tidak perlu scan QR. Silakan upload catatan kajian dari dashboard.',
            ];
        }

        $students = $parent->students;
        $childDisplayNames = $students
            ->map(fn ($student) => $student->name . ($student->classRoom ? ' (' . $student->classRoom->name . ')' : ''))
            ->values()
            ->all();

        $attendance = Attendance::withTrashed()
            ->where('kajian_event_id', $event->id)
            ->where('parent_id', $parent->id)
            ->first();

        if ($attendance) {
            if ($attendance->trashed()) {
                $attendance->restore();
                $attendance->update([
                    'student_id' => $students->first()?->id,
                    'status' => Attendance::STATUS_HADIR_FISIK,
                    'method' => Attendance::METHOD_SCAN_QR,
                    'validation_status' => $parent->isWaliTeacher() ? Attendance::VALIDATION_PENDING : Attendance::VALIDATION_APPROVED,
                    'validated_by' => $parent->isWaliTeacher() ? null : $userId,
                    'validated_at' => $parent->isWaliTeacher() ? null : now(),
                    'rejection_reason' => null,
                    'scanned_at' => now(),
                    'device_info' => $deviceInfo,
                ]);
            } else {
                return [
                    'status' => 'warning',
                    'message' => $parent->user->name . ' sudah tercatat hadir.',
                    'payload' => [
                        'parentName' => $parent->user->name,
                    ],
                ];
            }
        } else {
            try {
                DB::transaction(function () use ($event, $parent, $students, $userId, $deviceInfo) {
                    Attendance::create([
                        'kajian_event_id' => $event->id,
                        'parent_id' => $parent->id,
                        'student_id' => $students->first()?->id,
                        'status' => Attendance::STATUS_HADIR_FISIK,
                        'method' => Attendance::METHOD_SCAN_QR,
                        'validation_status' => $parent->isWaliTeacher() ? Attendance::VALIDATION_PENDING : Attendance::VALIDATION_APPROVED,
                        'validated_by' => $parent->isWaliTeacher() ? null : $userId,
                        'validated_at' => $parent->isWaliTeacher() ? null : now(),
                        'scanned_at' => now(),
                        'device_info' => $deviceInfo,
                    ]);
                }, 2);
            } catch (QueryException $exception) {
                if ($this->isDuplicateAttendance($exception)) {
                    return [
                        'status' => 'warning',
                        'message' => $parent->user->name . ' sudah tercatat hadir.',
                        'payload' => [
                            'parentName' => $parent->user->name,
                        ],
                    ];
                }

                throw $exception;
            }
        }

        $childNameDisplay = count($childDisplayNames) > 0
            ? (count($childDisplayNames) . ' Santri: ' . implode(', ', $childDisplayNames))
            : 'Tidak ada data santri';

        $parentType = match ($parent->type) {
            'father' => 'Bapak',
            'mother' => 'Ibu',
            'teacher' => 'Ustadz/ah',
            default => 'Peserta',
        };

        $message = $parent->isWaliTeacher()
            ? "Selamat Datang, {$parentType} {$parent->user->name}. Berhasil mencatat, mohon ingatkan untuk upload catatan kajian di dashboard."
            : "Selamat Datang, {$parentType} {$parent->user->name}. Berhasil mencatat presensi untuk " . ($students->count() ?: 1) . ' santri.';

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
