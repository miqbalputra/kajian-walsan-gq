<?php

namespace App\Jobs;

use App\Services\TelegramReviewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTelegramReview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 45;

    public array $backoff = [10, 60, 300];

    public function __construct(public int $requestId)
    {
    }

    public function handle(TelegramReviewService $telegram): void
    {
        $telegram->sendReview($this->requestId);
    }

    public function failed(\Throwable $exception): void
    {
        app(TelegramReviewService::class)->markDeliveryFailed($this->requestId, $exception);
    }
}
