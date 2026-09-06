<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Services\TelegramReviewService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class AttendanceObserver implements ShouldHandleEventsAfterCommit
{
    public function creating(Attendance $attendance): void
    {
        $this->normalizeTelegramReview($attendance, true);
    }

    public function updating(Attendance $attendance): void
    {
        $this->normalizeTelegramReview($attendance, false);
    }

    public function created(Attendance $attendance): void
    {
        if ($attendance->requiresTelegramReview() && $attendance->isPending()) {
            $this->queueTelegramReview($attendance);
        }
    }

    public function updated(Attendance $attendance): void
    {
        $telegram = app(TelegramReviewService::class);

        if ($attendance->wasChanged(['proof_file', 'status'])
            && $attendance->requiresTelegramReview()
            && $attendance->isPending()) {
            $this->queueTelegramReview($attendance);
        }

        if ($attendance->wasChanged('validation_status') && ! $attendance->isPending()) {
            try {
                $telegram->resolveForAttendance($attendance);
            } catch (\Throwable $exception) {
                logger()->warning('[Telegram Review] Could not close review message', [
                    'attendance_id' => $attendance->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }

    private function normalizeTelegramReview(Attendance $attendance, bool $creating): void
    {
        $changedToReviewable = $creating || $attendance->isDirty(['proof_file', 'status']);

        if (! $changedToReviewable || ! $attendance->requiresTelegramReview()) {
            return;
        }

        $attendance->validation_status = Attendance::VALIDATION_PENDING;
        $attendance->validated_by = null;
        $attendance->validated_at = null;
        $attendance->rejection_reason = null;
    }

    private function queueTelegramReview(Attendance $attendance): void
    {
        try {
            app(TelegramReviewService::class)->queueForAttendance($attendance);
        } catch (\Throwable $exception) {
            // Upload bukti tetap sukses dan dapat diputuskan dari layar admin
            // apabila layanan Telegram/queue sedang bermasalah.
            logger()->warning('[Telegram Review] Could not queue proof', [
                'attendance_id' => $attendance->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
