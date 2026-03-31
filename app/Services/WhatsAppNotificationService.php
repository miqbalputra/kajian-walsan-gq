<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    protected string $webhookUrl;
    protected bool $enabled;

    public function __construct()
    {
        $this->webhookUrl = config('services.n8n.webhook_url', '');
        $this->enabled = config('services.n8n.enabled', false);
    }

    /**
     * Kirim notifikasi saat presensi di-approve.
     */
    public function sendApprovalNotification(Attendance $attendance): bool
    {
        $parent = $attendance->parent;
        $user = $parent?->user;
        $event = $attendance->kajianEvent;

        if (!$user || !$event) {
            Log::warning('[WhatsApp] Cannot send approval notification: missing user or event data');
            return false;
        }

        $phone = $this->formatPhoneNumber($user->phone);
        if (!$phone) {
            Log::warning('[WhatsApp] Cannot send notification: no phone number', ['user_id' => $user->id]);
            return false;
        }

        $statusText = $attendance->status === 'hadir_online' ? 'Hadir Online' : 'Izin';

        $message = "✅ *Presensi Disetujui*\n\n"
            . "Assalamu'alaikum {$user->name},\n\n"
            . "Presensi kajian Anda telah *disetujui* oleh admin.\n\n"
            . "📋 *Detail:*\n"
            . "• Kajian: {$event->title}\n"
            . "• Tanggal: {$event->date->translatedFormat('l, d F Y')}\n"
            . "• Status: {$statusText}\n"
            . "• Validasi: ✅ Disetujui\n\n"
            . "Jazakumullahu khairan atas partisipasinya.\n\n"
            . "— _Kajian Walsan_";

        return $this->sendToN8n($phone, $message, 'approval', $attendance->id);
    }

    /**
     * Kirim notifikasi saat presensi di-reject.
     */
    public function sendRejectionNotification(Attendance $attendance): bool
    {
        $parent = $attendance->parent;
        $user = $parent?->user;
        $event = $attendance->kajianEvent;

        if (!$user || !$event) {
            Log::warning('[WhatsApp] Cannot send rejection notification: missing user or event data');
            return false;
        }

        $phone = $this->formatPhoneNumber($user->phone);
        if (!$phone) {
            Log::warning('[WhatsApp] Cannot send notification: no phone number', ['user_id' => $user->id]);
            return false;
        }

        $statusText = $attendance->status === 'hadir_online' ? 'Hadir Online' : 'Izin';
        $reason = $attendance->rejection_reason ?? 'Tidak ada keterangan';

        $message = "❌ *Presensi Ditolak*\n\n"
            . "Assalamu'alaikum {$user->name},\n\n"
            . "Mohon maaf, presensi kajian Anda *ditolak* oleh admin.\n\n"
            . "📋 *Detail:*\n"
            . "• Kajian: {$event->title}\n"
            . "• Tanggal: {$event->date->translatedFormat('l, d F Y')}\n"
            . "• Status: {$statusText}\n"
            . "• Validasi: ❌ Ditolak\n\n"
            . "📝 *Alasan Penolakan:*\n"
            . "{$reason}\n\n"
            . "Silakan upload ulang bukti yang sesuai melalui aplikasi.\n\n"
            . "— _Kajian Walsan_";

        return $this->sendToN8n($phone, $message, 'rejection', $attendance->id);
    }

    /**
     * Kirim data ke n8n webhook.
     */
    protected function sendToN8n(string $phone, string $message, string $type, int $attendanceId): bool
    {
        if (!$this->enabled || empty($this->webhookUrl)) {
            Log::info('[WhatsApp] Notification disabled or no webhook URL configured', [
                'type' => $type,
                'phone' => $phone,
                'attendance_id' => $attendanceId,
            ]);
            return false;
        }

        try {
            $payload = [
                'phone' => $phone,
                'message' => $message,
                'type' => $type,
                'attendance_id' => $attendanceId,
                'timestamp' => now()->toISOString(),
            ];

            Log::info('[WhatsApp] Sending notification to n8n', [
                'type' => $type,
                'phone' => $phone,
                'attendance_id' => $attendanceId,
            ]);

            $response = Http::timeout(10)
                ->post($this->webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('[WhatsApp] Notification sent successfully', [
                    'type' => $type,
                    'attendance_id' => $attendanceId,
                ]);
                return true;
            }

            Log::error('[WhatsApp] n8n webhook returned error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            // Jangan sampai error WhatsApp mengganggu proses approve/reject
            Log::error('[WhatsApp] Exception sending notification', [
                'message' => $e->getMessage(),
                'type' => $type,
                'attendance_id' => $attendanceId,
            ]);
            return false;
        }
    }

    /**
     * Format nomor telepon ke format internasional (62xxx).
     */
    protected function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Hapus spasi, strip, tanda kurung
        $phone = preg_replace('/[\s\-\(\)\+]/', '', $phone);

        // Ubah 08xxx ke 628xxx
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }

        // Ubah 8xxx ke 628xxx
        if (str_starts_with($phone, '8') && strlen($phone) >= 10) {
            $phone = '62' . $phone;
        }

        // Pastikan dimulai dengan 62
        if (!str_starts_with($phone, '62')) {
            return null;
        }

        return $phone;
    }
}
