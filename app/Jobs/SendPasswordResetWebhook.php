<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Mengirim payload reset password ke webhook n8n secara asynchronous.
 *
 * Sebelumnya controller memanggil Http::post() secara sinkron dengan
 * timeout 15 detik. Jika webhook n8n lambat, halaman /forgot-password
 * ikut nge-hang sampai 15 detik. Dengan job, response ke user langsung
 * selesai (pesan generik tetap ditampilkan) dan pengiriman webhook
 * terjadi di queue worker dengan retry otomatis.
 */
class SendPasswordResetWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Jumlah percobaan total (1x awal + 2x retry).
     */
    public int $tries = 3;

    /**
     * Batas waktu eksekusi job sebelum dianggap gagal (detik).
     */
    public int $timeout = 30;

    /**
     * Backoff eksponensial antar retry (detik): 10s, 30s, 60s.
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 30, 60];

    public function __construct(
        public User $user,
        public string $resetUrl,
        public string $identifier,
        public int $expiresMinutes = 60,
    ) {
    }

    /**
     * Eksekusi job.
     */
    public function handle(): void
    {
        $webhookUrl = config('services.n8n.password_reset_webhook_url');

        // Kalau webhook belum dikonfigurasi, hapus job dengan tenang
        // (bukan error yang perlu retry) dan catat saja.
        if (! filled($webhookUrl)) {
            Log::info('[ForgotPassword] n8n webhook tidak dikonfigurasi — job dibatalkan.', [
                'user_id' => $this->user->id,
            ]);
            $this->delete();
            return;
        }

        $phone = $this->formatPhoneForWa($this->user->phone);
        $preferredChannel = str_contains($this->identifier, '@')
            ? 'email'
            : ($phone ? 'whatsapp' : 'email');

        try {
            $response = Http::timeout(15)->post($webhookUrl, [
                'secret'            => config('services.n8n.password_reset_secret'),
                'name'              => $this->user->name,
                'username'          => $this->user->username,
                'email'             => $this->user->email,
                'phone'             => $phone,
                'preferred_channel' => $preferredChannel,
                'reset_url'         => $this->resetUrl,
                'expires_minutes'   => $this->expiresMinutes,
                'requested_at'      => now()->toISOString(),
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException(
                    'n8n webhook mengembalikan HTTP ' . $response->status()
                    . ' — body: ' . substr((string) $response->body(), 0, 200)
                );
            }

            Log::info('[ForgotPassword] n8n webhook berhasil dikirim.', [
                'user_id' => $this->user->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('[ForgotPassword] n8n webhook job gagal.', [
                'user_id' => $this->user->id,
                'error'   => $e->getMessage(),
            ]);

            // Lempar ulang agar queue worker mencoba lagi sesuai backoff.
            throw $e;
        }
    }

    /**
     * Normalisasi nomor HP ke format WhatsApp internasional (62xxx).
     * Dipindahkan dari controller agar job self-contained.
     */
    private function formatPhoneForWa(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '08')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return str_starts_with($digits, '62') ? $digits : null;
    }

    /**
     * Job yang gagal total (setelah semua retry) ditangani di sini.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[ForgotPassword] n8n webhook job GAGAL PERMANEN setelah semua retry.', [
            'user_id' => $this->user->id,
            'identifier' => $this->identifier,
            'error' => $exception->getMessage(),
        ]);
    }
}