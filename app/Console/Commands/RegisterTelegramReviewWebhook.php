<?php

namespace App\Console\Commands;

use App\Services\TelegramReviewService;
use Illuminate\Console\Command;

class RegisterTelegramReviewWebhook extends Command
{
    protected $signature = 'telegram:review-webhook {--drop-pending-updates : Hapus callback/message Telegram lama yang belum dikirim}';

    protected $description = 'Daftarkan webhook bot Telegram untuk persetujuan bukti kajian';

    public function handle(TelegramReviewService $telegram): int
    {
        try {
            $telegram->registerWebhook((bool) $this->option('drop-pending-updates'));
            $this->info('Webhook Telegram review berhasil didaftarkan.');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error('Webhook Telegram gagal didaftarkan: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
