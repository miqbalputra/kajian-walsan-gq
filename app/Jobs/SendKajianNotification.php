<?php

namespace App\Jobs;

use App\Models\KajianNotificationDispatch;
use App\Services\KajianNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendKajianNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 180;

    public function __construct(public int $dispatchId) {}

    public function backoff(): array
    {
        return [60, 300];
    }

    public function handle(KajianNotificationService $service): void
    {
        $dispatch = KajianNotificationDispatch::query()->find($this->dispatchId);

        if (! $dispatch || in_array($dispatch->status, [
            KajianNotificationDispatch::STATUS_SENT,
            KajianNotificationDispatch::STATUS_SKIPPED,
        ], true)) {
            return;
        }

        $dispatch->increment('attempts');

        try {
            $result = $service->sendDispatch($dispatch->fresh());

            $dispatch->update([
                'status' => $result['status'],
                'total_count' => $result['total'],
                'sent_count' => $result['sent'],
                'failed_count' => $result['failed'],
                'last_error' => $result['error'],
                'sent_at' => $result['status'] === KajianNotificationDispatch::STATUS_SENT ? now() : null,
            ]);
        } catch (\Throwable $exception) {
            $dispatch->update([
                'status' => KajianNotificationDispatch::STATUS_FAILED,
                'last_error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(\Throwable $exception): void
    {
        KajianNotificationDispatch::query()
            ->whereKey($this->dispatchId)
            ->update([
                'status' => KajianNotificationDispatch::STATUS_FAILED,
                'last_error' => $exception->getMessage(),
            ]);

        Log::error('[Kajian Notification] Notification job failed permanently', [
            'dispatch_id' => $this->dispatchId,
            'error' => $exception->getMessage(),
        ]);
    }
}
