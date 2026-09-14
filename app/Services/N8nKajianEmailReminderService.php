<?php

namespace App\Services;

use App\Models\KajianEvent;
use App\Models\KajianNotificationDispatch;
use App\Models\ParentModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class N8nKajianEmailReminderService
{
    /**
     * Send the 20.30 missing-attendance reminder to n8n.
     *
     * n8n receives one event payload and is responsible for splitting the
     * recipient list into individual email messages.
     *
     * @return array{status: string, total: int, sent: int, failed: int, error: ?string}
     */
    public function send(KajianNotificationDispatch $dispatch, KajianEvent $event): array
    {
        $recipients = $this->recipients($event);
        $total = $recipients->count();

        if ($total === 0) {
            return $this->result(KajianNotificationDispatch::STATUS_SENT, 0, 0, 0);
        }

        $enabled = (bool) config('services.n8n_email.enabled', false);
        $webhookUrl = (string) config('services.n8n_email.webhook_url', '');

        if (! $enabled || blank($webhookUrl)) {
            Log::info('[Kajian Email] n8n email webhook is disabled or not configured', [
                'dispatch_id' => $dispatch->id,
                'recipient_count' => $total,
            ]);

            return $this->result(KajianNotificationDispatch::STATUS_SKIPPED, $total, 0, 0);
        }

        $payload = $this->payload($dispatch, $event, $recipients->all());
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $secret = (string) config('services.n8n_email.webhook_secret', '');
        if ($secret !== '') {
            $headers['X-N8N-Webhook-Secret'] = $secret;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders($headers)
                ->post($webhookUrl, $payload);
        } catch (\Throwable $exception) {
            Log::error('[Kajian Email] n8n webhook request failed', [
                'dispatch_id' => $dispatch->id,
                'recipient_count' => $total,
                'error' => $exception->getMessage(),
            ]);

            throw new RuntimeException('Webhook email n8n gagal dihubungi.', 0, $exception);
        }

        if (! $response->successful()) {
            $error = sprintf(
                'Webhook email n8n mengembalikan HTTP %s: %s',
                $response->status(),
                Str::limit($response->body(), 500)
            );

            Log::error('[Kajian Email] n8n webhook returned an error', [
                'dispatch_id' => $dispatch->id,
                'recipient_count' => $total,
                'status' => $response->status(),
            ]);

            throw new RuntimeException($error);
        }

        Log::info('[Kajian Email] reminder accepted by n8n', [
            'dispatch_id' => $dispatch->id,
            'recipient_count' => $total,
        ]);

        return $this->result(
            KajianNotificationDispatch::STATUS_SENT,
            $total,
            $total,
            0
        );
    }

    /**
     * @return Collection<int, array{user_id: int, name: string, email: string}>
     */
    private function recipients(KajianEvent $event)
    {
        return ParentModel::query()
            ->guardians()
            ->targetedByEvent($event)
            ->whereDoesntHave('attendances', function ($query) use ($event): void {
                $query->where('kajian_event_id', $event->id);
            })
            ->whereHas('user', function ($query): void {
                $query->where('is_active', true)
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->whereHas('role', fn ($role) => $role->where('name', 'wali_santri'));
            })
            ->with('user')
            ->orderBy('id')
            ->get()
            ->unique('user_id')
            ->map(fn (ParentModel $parent): array => [
                'user_id' => (int) $parent->user_id,
                'name' => (string) $parent->user->name,
                'email' => (string) $parent->user->email,
            ])
            ->values();
    }

    /**
     * @param  list<array{user_id: int, name: string, email: string}>  $recipients
     * @return array<string, mixed>
     */
    private function payload(KajianNotificationDispatch $dispatch, KajianEvent $event, array $recipients): array
    {
        $subject = 'Pengingat - Anda Belum Presensi Kajian Wali Santri';
        $loginUrl = 'https://kajian.griyaquran.web.id/login';
        $text = implode("\n\n", [
            'Bapak/Ibu Orang Tua/Wali Santri yang kami hormati,',
            'Mohon segera melakukan presensi pada Agenda Kajian Wali Santri hari ini.',
            'Jika menyimak online, upload catatan hasil kajian ke aplikasi.',
            'Jika izin tidak bisa hadir, upload surat pernyataan izin ke aplikasi disertai keterangan.',
            "Link: {$loginUrl}",
            'Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan Jazakumullahu khairan.',
        ]);

        $escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $html = implode('', [
            '<p>Bapak/Ibu Orang Tua/Wali Santri yang kami hormati,</p>',
            '<p>Mohon segera melakukan presensi pada Agenda Kajian Wali Santri hari ini.</p>',
            '<p>Jika menyimak online, upload catatan hasil kajian ke aplikasi.</p>',
            '<p>Jika izin tidak bisa hadir, upload surat pernyataan izin ke aplikasi disertai keterangan.</p>',
            '<p>Link: <a href="', $escape($loginUrl), '">', $escape($loginUrl), '</a></p>',
            '<p>Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan Jazakumullahu khairan.</p>',
        ]);

        return [
            'channel' => 'email',
            'notification_type' => KajianNotificationDispatch::TYPE_EMAIL_MISSING_ATTENDANCE,
            'dispatch_id' => $dispatch->id,
            'idempotency_key' => 'kajian-email:'.$dispatch->id,
            'timestamp' => now(config('app.timezone'))->toISOString(),
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'date' => $event->date->toDateString(),
                'time_start' => Carbon::parse($event->time_start)->format('H:i'),
                'time_end' => Carbon::parse($event->time_end)->format('H:i'),
            ],
            'subject' => $subject,
            'text' => $text,
            'html' => $html,
            'login_url' => $loginUrl,
            'recipients' => $recipients,
        ];
    }

    /**
     * @return array{status: string, total: int, sent: int, failed: int, error: ?string}
     */
    private function result(string $status, int $total, int $sent, int $failed, ?string $error = null): array
    {
        return compact('status', 'total', 'sent', 'failed', 'error');
    }
}
