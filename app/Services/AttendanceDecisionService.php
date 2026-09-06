<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AttendanceDecisionService
{
    /**
     * Apply a decision from the normal admin screen. Telegram calls apply()
     * inside its own locked transaction to prevent concurrent button clicks.
     */
    public function decide(Attendance $attendance, User $validator, string $decision, ?string $reason = null): Attendance
    {
        $resolved = DB::transaction(function () use ($attendance, $validator, $decision, $reason): Attendance {
            $locked = Attendance::query()->lockForUpdate()->findOrFail($attendance->id);

            return $this->apply($locked, $validator, $decision, $reason);
        }, 2);

        $this->notifyGuardian($resolved);

        return $resolved;
    }

    /**
     * The caller must already hold a lock for the attendance row when needed.
     */
    public function apply(Attendance $attendance, User $validator, string $decision, ?string $reason = null): Attendance
    {
        if (! in_array($decision, [
            Attendance::VALIDATION_APPROVED,
            Attendance::VALIDATION_REJECTED,
        ], true)) {
            throw new InvalidArgumentException('Keputusan validasi tidak dikenal.');
        }

        $attendance->update([
            'validation_status' => $decision,
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'rejection_reason' => $decision === Attendance::VALIDATION_REJECTED ? $reason : null,
        ]);

        $attendance->loadMissing('kajianEvent');
        $attendance->kajianEvent?->updateAttendanceCount();

        return $attendance->fresh();
    }

    public function notifyGuardian(Attendance $attendance): void
    {
        try {
            $attendance->loadMissing(['parent.user', 'kajianEvent']);
            $notifier = app(WhatsAppNotificationService::class);

            if ($attendance->validation_status === Attendance::VALIDATION_APPROVED) {
                $notifier->sendApprovalNotification($attendance);
            } elseif ($attendance->validation_status === Attendance::VALIDATION_REJECTED) {
                $notifier->sendRejectionNotification($attendance);
            }
        } catch (\Throwable $exception) {
            logger()->warning('[Attendance Decision] WhatsApp notification failed', [
                'attendance_id' => $attendance->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
