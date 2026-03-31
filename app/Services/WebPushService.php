<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebPushService
{
    protected string $publicKey;
    protected string $privateKey;
    protected string $subject;

    public function __construct()
    {
        $this->publicKey  = config('webpush.vapid_public_key', '');
        $this->privateKey = config('webpush.vapid_private_key', '');
        $this->subject    = config('webpush.subject', 'mailto:admin@kajianwalsan.com');
    }

    /**
     * Kirim notifikasi ke semua wali santri yang berlangganan.
     */
    public function sendToAllWali(string $title, string $body, string $url = '/'): array
    {
        // Ambil semua subscription wali santri
        $subscriptions = PushSubscription::whereHas('user', function ($q) {
            $q->whereHas('role', fn($r) => $r->where('name', 'wali_santri'));
        })->get();

        $sent   = 0;
        $failed = 0;

        foreach ($subscriptions as $sub) {
            /** @var PushSubscription $sub */
            $result = $this->sendNotification($sub, $title, $body, $url);
            $result ? $sent++ : $failed++;
        }

        return ['sent' => $sent, 'failed' => $failed, 'total' => $subscriptions->count()];
    }

    /**
     * Kirim notifikasi ke satu subscription.
     */
    public function sendNotification(PushSubscription $subscription, string $title, string $body, string $url = '/'): bool
    {
        if (empty($this->publicKey) || empty($this->privateKey)) {
            Log::warning('[WebPush] VAPID keys not configured');
            return false;
        }

        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'url'   => $url,
            'icon'  => '/icons/icon-192x192.png',
            'badge' => '/icons/icon-96x96.png',
        ]);

        try {
            // Build VAPID auth headers using JWT
            $headers = $this->buildVapidHeaders($subscription->endpoint);

            $response = Http::withHeaders($headers)
                ->withBody($payload, 'application/json')
                ->post($subscription->endpoint);

            if ($response->status() === 410 || $response->status() === 404) {
                // Gone - subscription expired, hapus dari DB
                $subscription->delete();
                return false;
            }

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('[WebPush] Send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Build VAPID JWT Authorization header.
     */
    protected function buildVapidHeaders(string $endpoint): array
    {
        $endpointParts = parse_url($endpoint);
        $audience = $endpointParts['scheme'] . '://' . $endpointParts['host'];

        $header    = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $now       = time();
        $payload   = base64_encode(json_encode([
            'aud' => $audience,
            'exp' => $now + 43200, // 12 jam
            'sub' => $this->subject,
        ]));

        $unsignedToken = $header . '.' . $payload;

        // Sign with ES256 (using openssl)
        $privateKeyPem = $this->vapidPrivateKeyToPem($this->privateKey);
        $signature     = '';
        openssl_sign($unsignedToken, $signature, $privateKeyPem, OPENSSL_ALGO_SHA256);

        $jwt = $unsignedToken . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return [
            'Authorization'  => 'vapid t=' . $jwt . ', k=' . $this->publicKey,
            'Content-Type'   => 'application/json',
            'Content-Length' => strlen(json_encode(['title' => '', 'body' => ''])),
            'TTL'            => '86400',
        ];
    }

    /**
     * Convert base64url VAPID private key to PEM format.
     */
    protected function vapidPrivateKeyToPem(string $base64urlKey): string
    {
        $key = base64_decode(strtr($base64urlKey, '-_', '+/'));

        // EC private key in DER format (NIST P-256)
        $der = "\x30\x77\x02\x01\x01\x04\x20" . $key .
               "\xa0\x0a\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07" .
               "\xa1\x44\x03\x42\x00" . hex2bin(str_repeat('04', 1)) .
               str_repeat("\x00", 64);

        return "-----BEGIN EC PRIVATE KEY-----\n" .
               chunk_split(base64_encode($der), 64, "\n") .
               "-----END EC PRIVATE KEY-----\n";
    }
}
