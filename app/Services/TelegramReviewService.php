<?php

namespace App\Services;

use App\Jobs\SendTelegramReview;
use App\Jobs\SyncTelegramReviewMessage;
use App\Models\Attendance;
use App\Models\TelegramReviewRequest;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class TelegramReviewService
{
    private const CALLBACK_PREFIX = 'tr';

    /** @var array<string, string> */
    private const REJECTION_REASONS = [
        'b' => 'Foto bukti buram atau kualitasnya tidak memadai.',
        't' => 'Catatan atau surat tidak dapat dibaca dengan jelas.',
        's' => 'Bukti yang dikirim tidak sesuai dengan pengajuan presensi.',
    ];

    public function enabled(): bool
    {
        return (bool) config('services.telegram_review.enabled', false)
            && filled(config('services.telegram_review.bot_token'))
            && filled(config('services.telegram_review.chat_id'));
    }

    public function validWebhookSecret(?string $provided): bool
    {
        $expected = (string) config('services.telegram_review.webhook_secret', '');

        return $expected !== ''
            && is_string($provided)
            && hash_equals($expected, $provided);
    }

    /**
     * Queue exactly one active message for the current proof. The observer
     * invokes this after the attendance transaction has committed.
     */
    public function queueForAttendance(Attendance $attendance): ?TelegramReviewRequest
    {
        if (! $attendance->requiresTelegramReview() || ! $attendance->isPending()) {
            return null;
        }

        if (! $this->enabled()) {
            logger()->warning('[Telegram Review] Feature enabled but Telegram configuration is incomplete', [
                'attendance_id' => $attendance->id,
            ]);

            return null;
        }

        [$request, $expired] = DB::transaction(function () use ($attendance): array {
            $requests = TelegramReviewRequest::query()
                ->where('attendance_id', $attendance->id)
                ->whereIn('delivery_status', [
                    TelegramReviewRequest::DELIVERY_QUEUED,
                    TelegramReviewRequest::DELIVERY_SENT,
                    TelegramReviewRequest::DELIVERY_FAILED,
                ])
                ->lockForUpdate()
                ->get();

            $expired = $requests
                ->filter(fn (TelegramReviewRequest $request) => $request->proof_file !== $attendance->proof_file)
                ->values();

            foreach ($expired as $oldRequest) {
                $oldRequest->update(['delivery_status' => TelegramReviewRequest::DELIVERY_EXPIRED]);
            }

            $current = $requests->first(fn (TelegramReviewRequest $request) => $request->proof_file === $attendance->proof_file);

            if ($current) {
                return [$current, $expired];
            }

            return [TelegramReviewRequest::create([
                'token' => (string) Str::ulid(),
                'attendance_id' => $attendance->id,
                'proof_file' => $attendance->proof_file,
                'chat_id' => (string) config('services.telegram_review.chat_id'),
                'delivery_status' => TelegramReviewRequest::DELIVERY_QUEUED,
            ]), $expired];
        }, 2);

        foreach ($expired as $oldRequest) {
            $this->syncMessage($oldRequest->fresh());
        }

        if ($request->delivery_status === TelegramReviewRequest::DELIVERY_QUEUED) {
            SendTelegramReview::dispatch($request->id);
        }

        return $request;
    }

    public function sendReview(int $requestId): void
    {
        $request = TelegramReviewRequest::with(['attendance.parent.user', 'attendance.student', 'attendance.kajianEvent'])
            ->find($requestId);

        if (! $request || $request->delivery_status !== TelegramReviewRequest::DELIVERY_QUEUED) {
            return;
        }

        $attendance = $request->attendance;
        if (! $attendance || ! $attendance->isPending() || $attendance->proof_file !== $request->proof_file) {
            $request->update(['delivery_status' => TelegramReviewRequest::DELIVERY_EXPIRED]);
            return;
        }

        if (! $this->enabled()) {
            throw new RuntimeException('Konfigurasi Telegram review belum lengkap.');
        }

        $request->increment('delivery_attempts');

        $result = $this->call('sendPhoto', [
            'chat_id' => $request->chat_id,
            'photo' => CloudinaryService::getDisplayUrl($request->proof_file),
            'caption' => $this->caption($request, 'pending'),
            'reply_markup' => json_encode($this->mainKeyboard($request), JSON_THROW_ON_ERROR),
        ]);

        $request->update([
            'delivery_status' => TelegramReviewRequest::DELIVERY_SENT,
            'message_id' => data_get($result, 'message_id'),
            'sent_at' => now(),
            'last_error' => null,
        ]);
    }

    public function markDeliveryFailed(int $requestId, \Throwable $exception): void
    {
        TelegramReviewRequest::query()
            ->whereKey($requestId)
            ->where('delivery_status', TelegramReviewRequest::DELIVERY_QUEUED)
            ->update([
                'delivery_status' => TelegramReviewRequest::DELIVERY_FAILED,
                'last_error' => Str::limit($exception->getMessage(), 2000, ''),
            ]);
    }

    /**
     * Resolve outstanding Telegram messages when the normal web screen makes
     * a decision, too. The attendance observer calls this after commit.
     */
    public function resolveForAttendance(Attendance $attendance): void
    {
        if (! $attendance->requiresTelegramReview() || $attendance->isPending()) {
            return;
        }

        $requests = TelegramReviewRequest::query()
            ->where('attendance_id', $attendance->id)
            ->where('proof_file', $attendance->proof_file)
            ->whereIn('delivery_status', [
                TelegramReviewRequest::DELIVERY_QUEUED,
                TelegramReviewRequest::DELIVERY_SENT,
                TelegramReviewRequest::DELIVERY_FAILED,
            ])
            ->get();

        foreach ($requests as $request) {
            $request->update([
                'delivery_status' => TelegramReviewRequest::DELIVERY_RESOLVED,
                'decision' => $attendance->validation_status,
                'rejection_reason' => $attendance->rejection_reason,
                'reviewed_by_user_id' => $attendance->validated_by,
                'reviewed_at' => $attendance->validated_at ?: now(),
            ]);
            $this->syncMessage($request->fresh());
        }
    }

    /** @return array{ok: bool, text: string} */
    public function handleCallback(string $data, User $reviewer, string $telegramUserId, string $chatId): array
    {
        if (! preg_match('/^'.self::CALLBACK_PREFIX.':([0-9A-HJKMNP-TV-Z]{26}):(a|r|b|t|s)$/', $data, $matches)) {
            return ['ok' => false, 'text' => 'Tombol tidak dikenali atau sudah kedaluwarsa.'];
        }

        [, $token, $action] = $matches;

        if ($action === 'r') {
            return $this->showRejectReasons($token, $chatId);
        }

        $decision = $action === 'a'
            ? TelegramReviewRequest::DECISION_APPROVED
            : TelegramReviewRequest::DECISION_REJECTED;
        $reason = self::REJECTION_REASONS[$action] ?? null;

        $result = DB::transaction(function () use ($token, $reviewer, $telegramUserId, $chatId, $decision, $reason): array {
            $request = TelegramReviewRequest::query()
                ->where('token', $token)
                ->lockForUpdate()
                ->first();

            if (! $request
                || $request->delivery_status !== TelegramReviewRequest::DELIVERY_SENT
                || $request->chat_id !== $chatId) {
                return ['ok' => false, 'text' => 'Pengajuan ini sudah tidak aktif.'];
            }

            $attendance = Attendance::query()->lockForUpdate()->find($request->attendance_id);

            if (! $attendance
                || ! $attendance->requiresTelegramReview()
                || ! $attendance->isPending()
                || $attendance->proof_file !== $request->proof_file) {
                $request->update(['delivery_status' => TelegramReviewRequest::DELIVERY_EXPIRED]);

                return ['ok' => false, 'text' => 'Bukti ini sudah diganti atau sudah diputuskan.'];
            }

            app(AttendanceDecisionService::class)->apply(
                $attendance,
                $reviewer,
                $decision,
                $reason,
            );

            $request->update([
                'delivery_status' => TelegramReviewRequest::DELIVERY_RESOLVED,
                'decision' => $decision,
                'rejection_reason' => $reason,
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_by_telegram_id' => $telegramUserId,
                'reviewed_at' => now(),
            ]);

            return ['ok' => true, 'request_id' => $request->id, 'attendance_id' => $attendance->id];
        }, 2);

        if (! $result['ok']) {
            return $result;
        }

        $attendance = Attendance::findOrFail($result['attendance_id']);
        app(AttendanceDecisionService::class)->notifyGuardian($attendance);
        $this->syncMessage(TelegramReviewRequest::find($result['request_id']));

        return [
            'ok' => true,
            'text' => $decision === TelegramReviewRequest::DECISION_APPROVED
                ? 'Presensi disetujui.'
                : 'Presensi ditolak.',
        ];
    }

    /** @return array{ok: bool, text: string} */
    private function showRejectReasons(string $token, string $chatId): array
    {
        $request = TelegramReviewRequest::query()
            ->where('token', $token)
            ->where('delivery_status', TelegramReviewRequest::DELIVERY_SENT)
            ->first();

        if (! $request || $request->chat_id !== $chatId) {
            return ['ok' => false, 'text' => 'Pengajuan ini sudah tidak aktif.'];
        }

        try {
            $this->call('editMessageReplyMarkup', [
                'chat_id' => $request->chat_id,
                'message_id' => $request->message_id,
                'reply_markup' => json_encode($this->reasonKeyboard($request), JSON_THROW_ON_ERROR),
            ]);
        } catch (\Throwable $exception) {
            logger()->warning('[Telegram Review] Could not show rejection reasons', [
                'request_id' => $request->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return ['ok' => true, 'text' => 'Pilih alasan penolakan.'];
    }

    public function syncMessage(?TelegramReviewRequest $request): void
    {
        if (! $request || ! $request->message_id || ! $this->enabled()) {
            return;
        }

        SyncTelegramReviewMessage::dispatch($request->id);
    }

    public function updateMessage(int $requestId): void
    {
        $request = TelegramReviewRequest::with(['attendance.parent.user', 'attendance.student', 'attendance.kajianEvent'])
            ->find($requestId);

        if (! $request || ! $request->message_id || ! $this->enabled()) {
            return;
        }

        $state = match ($request->delivery_status) {
            TelegramReviewRequest::DELIVERY_RESOLVED => $request->decision,
            TelegramReviewRequest::DELIVERY_EXPIRED => 'expired',
            default => 'pending',
        };

        try {
            $this->call('editMessageCaption', [
                'chat_id' => $request->chat_id,
                'message_id' => $request->message_id,
                'caption' => $this->caption($request, $state),
                'reply_markup' => json_encode(['inline_keyboard' => []], JSON_THROW_ON_ERROR),
            ]);
        } catch (RuntimeException $exception) {
            // Telegram returns 400 when the requested caption is already in
            // place; that is a harmless result for concurrent updates.
            if (! str_contains($exception->getMessage(), 'message is not modified')) {
                throw $exception;
            }
        }
    }

    public function replyWithTelegramId(string $chatId, string $telegramUserId): void
    {
        if (! $this->enabled()) {
            return;
        }

        $this->call('sendMessage', [
            'chat_id' => $chatId,
            'text' => "Telegram ID Anda: {$telegramUserId}\nMasukkan angka ini pada akun admin di Manajemen User.",
        ]);
    }

    public function answerCallback(string $callbackId, string $text, bool $showAlert = false): void
    {
        if (! $this->enabled()) {
            return;
        }

        $this->call('answerCallbackQuery', [
            'callback_query_id' => $callbackId,
            'text' => Str::limit($text, 180, '…'),
            'show_alert' => $showAlert ? 'true' : 'false',
        ]);
    }

    public function registerWebhook(bool $dropPendingUpdates = false): void
    {
        if (! $this->enabled() || blank(config('services.telegram_review.webhook_secret'))) {
            throw new RuntimeException('Konfigurasi Telegram review belum lengkap.');
        }

        $this->call('setWebhook', [
            'url' => route('telegram.review.webhook'),
            'secret_token' => config('services.telegram_review.webhook_secret'),
            'allowed_updates' => json_encode(['callback_query', 'message'], JSON_THROW_ON_ERROR),
            'drop_pending_updates' => $dropPendingUpdates ? 'true' : 'false',
        ]);
    }

    /** @return array<string, mixed> */
    private function mainKeyboard(TelegramReviewRequest $request): array
    {
        return [
            'inline_keyboard' => [[
                ['text' => '✅ Setujui', 'callback_data' => self::CALLBACK_PREFIX.':'.$request->token.':a'],
                ['text' => '❌ Tolak', 'callback_data' => self::CALLBACK_PREFIX.':'.$request->token.':r'],
            ]],
        ];
    }

    /** @return array<string, mixed> */
    private function reasonKeyboard(TelegramReviewRequest $request): array
    {
        return [
            'inline_keyboard' => [
                [['text' => '📷 Foto buram', 'callback_data' => self::CALLBACK_PREFIX.':'.$request->token.':b']],
                [['text' => '📝 Tidak terbaca', 'callback_data' => self::CALLBACK_PREFIX.':'.$request->token.':t']],
                [['text' => '🚫 Bukti tidak sesuai', 'callback_data' => self::CALLBACK_PREFIX.':'.$request->token.':s']],
            ],
        ];
    }

    private function caption(TelegramReviewRequest $request, string $state): string
    {
        $attendance = $request->attendance;
        $guardian = $attendance?->parent?->user?->name ?? '-';
        $student = $attendance?->student?->name ?? '-';
        $event = $attendance?->kajianEvent;
        $kind = $attendance?->status === Attendance::STATUS_IZIN ? 'Izin' : 'Hadir Online';

        $status = match ($state) {
            TelegramReviewRequest::DECISION_APPROVED => '✅ DISETUJUI',
            TelegramReviewRequest::DECISION_REJECTED => '❌ DITOLAK',
            'expired' => '↩️ BUKTI SUDAH DIGANTI',
            default => '⏳ MENUNGGU KEPUTUSAN',
        };

        $lines = [
            $status,
            '',
            "Jenis: {$kind}",
            "Wali: {$guardian}",
            "Ananda: {$student}",
            'Kajian: '.($event?->title ?? '-'),
            'Tanggal: '.($event?->date?->translatedFormat('d M Y') ?? '-'),
        ];

        if (filled($attendance?->notes)) {
            $lines[] = 'Keterangan: '.Str::limit((string) $attendance->notes, 350, '…');
        }

        if ($state === TelegramReviewRequest::DECISION_REJECTED && $request->rejection_reason) {
            $lines[] = 'Alasan: '.$request->rejection_reason;
        }

        return Str::limit(implode("\n", $lines), 1000, '…');
    }

    /** @return array<string, mixed> */
    private function call(string $method, array $payload): array
    {
        $response = Http::asForm()
            ->timeout(15)
            ->post($this->endpoint($method), $payload);

        if (! $response->successful() || ! $response->json('ok', false)) {
            throw new RuntimeException($this->responseError($response));
        }

        return (array) $response->json('result', []);
    }

    private function endpoint(string $method): string
    {
        return 'https://api.telegram.org/bot'.config('services.telegram_review.bot_token').'/'.$method;
    }

    private function responseError(Response $response): string
    {
        return Str::limit((string) ($response->json('description') ?: $response->body() ?: 'Telegram API request failed'), 1000, '');
    }
}
