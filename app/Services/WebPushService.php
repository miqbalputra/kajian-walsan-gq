<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected string $publicKey;
    protected string $privateKey;
    protected string $subject;

    public function __construct()
    {
        $this->publicKey = config('webpush.vapid_public_key', '');
        $this->privateKey = config('webpush.vapid_private_key', '');
        $this->subject = config('webpush.subject', 'mailto:admin@kajianwalsan.com');
    }

    /**
     * Kirim notifikasi ke semua wali santri yang sudah subscribe dari PWA/browser.
     */
    public function sendToAllWali(string $title, string $body, string $url = '/wali-santri'): array
    {
        $subscriptions = PushSubscription::whereHas('user.role', function ($query) {
            $query->where('name', 'wali_santri');
        })->get();

        return $this->sendToSubscriptions($subscriptions, $title, $body, $url);
    }

    /**
     * Kirim notifikasi ke kumpulan subscription.
     */
    public function sendToSubscriptions($subscriptions, string $title, string $body, string $url = '/'): array
    {
        if (empty($this->publicKey) || empty($this->privateKey)) {
            Log::warning('[WebPush] VAPID keys not configured');
            return ['sent' => 0, 'failed' => $subscriptions->count(), 'total' => $subscriptions->count()];
        }

        if (!class_exists(WebPush::class) || !class_exists(Subscription::class)) {
            Log::error('[WebPush] minishlink/web-push is not installed. Run composer install/update before sending push notifications.');
            return ['sent' => 0, 'failed' => $subscriptions->count(), 'total' => $subscriptions->count()];
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => $this->subject,
                'publicKey' => $this->publicKey,
                'privateKey' => $this->privateKey,
            ],
        ]);

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => '/icons/icon-192x192.png',
            'badge' => '/icons/icon-96x96.png',
        ]);

        $queued = [];
        foreach ($subscriptions as $subscription) {
            $queued[$subscription->endpoint] = $subscription;

            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->p256dh,
                    'authToken' => $subscription->auth,
                ]),
                $payload
            );
        }

        $sent = 0;
        $failed = 0;

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            $subscription = $queued[$endpoint] ?? null;

            if ($report->isSuccess()) {
                $sent++;
                continue;
            }

            $failed++;

            if ($report->isSubscriptionExpired() && $subscription) {
                $subscription->delete();
            }

            Log::warning('[WebPush] Notification failed', [
                'endpoint' => $endpoint,
                'reason' => $report->getReason(),
            ]);
        }

        return ['sent' => $sent, 'failed' => $failed, 'total' => $subscriptions->count()];
    }
}
