<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TelegramReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramReviewWebhookController extends Controller
{
    public function __invoke(Request $request, TelegramReviewService $telegram): JsonResponse
    {
        if (! $telegram->validWebhookSecret($request->header('X-Telegram-Bot-Api-Secret-Token'))) {
            return response()->json(['message' => 'Webhook secret tidak valid.'], 401);
        }

        $update = $request->json()->all();

        if (is_array($update['callback_query'] ?? null)) {
            return $this->handleCallback($update['callback_query'], $telegram);
        }

        if (is_array($update['message'] ?? null)) {
            $this->handleStartCommand($update['message'], $telegram);
        }

        return response()->json(['ok' => true]);
    }

    /** @param array<string, mixed> $callback */
    private function handleCallback(array $callback, TelegramReviewService $telegram): JsonResponse
    {
        $callbackId = (string) ($callback['id'] ?? '');
        $telegramId = (string) data_get($callback, 'from.id', '');
        $reviewer = $this->reviewerFor($telegramId);

        if (! $reviewer) {
            $this->answerCallback($telegram, $callbackId, 'Akun Telegram ini tidak berhak memutuskan.', true);

            return response()->json(['ok' => true]);
        }

        try {
            $result = $telegram->handleCallback(
                (string) ($callback['data'] ?? ''),
                $reviewer,
                $telegramId,
                (string) data_get($callback, 'message.chat.id', ''),
            );
            $this->answerCallback($telegram, $callbackId, $result['text'], ! $result['ok']);
        } catch (\Throwable $exception) {
            Log::error('[Telegram Review] Callback processing failed', [
                'telegram_user_id' => $telegramId,
                'error' => $exception->getMessage(),
            ]);
            $this->answerCallback($telegram, $callbackId, 'Server gagal memproses keputusan. Coba lagi.', true);

            return response()->json(['message' => 'Callback gagal diproses.'], 500);
        }

        return response()->json(['ok' => true]);
    }

    /** @param array<string, mixed> $message */
    private function handleStartCommand(array $message, TelegramReviewService $telegram): void
    {
        if (! preg_match('/^\\/start(?:@\\w+)?(?:\\s|$)/i', (string) ($message['text'] ?? ''))) {
            return;
        }

        if (data_get($message, 'chat.type') !== 'private') {
            return;
        }

        try {
            $telegram->replyWithTelegramId(
                (string) data_get($message, 'chat.id'),
                (string) data_get($message, 'from.id'),
            );
        } catch (\Throwable $exception) {
            Log::warning('[Telegram Review] Could not reply to /start', ['error' => $exception->getMessage()]);
        }
    }

    private function reviewerFor(string $telegramId): ?User
    {
        if ($telegramId === '') {
            return null;
        }

        return User::query()
            ->active()
            ->where('telegram_user_id', $telegramId)
            ->whereHas('role', fn ($query) => $query->where('name', 'admin'))
            ->first();
    }

    private function answerCallback(TelegramReviewService $telegram, string $callbackId, string $text, bool $alert): void
    {
        if ($callbackId === '') {
            return;
        }

        try {
            $telegram->answerCallback($callbackId, $text, $alert);
        } catch (\Throwable $exception) {
            Log::warning('[Telegram Review] Could not answer callback', ['error' => $exception->getMessage()]);
        }
    }
}
