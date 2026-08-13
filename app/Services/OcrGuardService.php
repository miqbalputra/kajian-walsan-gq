<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OcrGuardService
{
    public function configured(): bool
    {
        return (bool) config('ocr_guard.enabled', false)
            && filled(config('ocr_guard.url'));
    }

    /**
     * Lightweight diagnostic used by the admin settings page. It never
     * exposes the guard token and fails fast when the internal service is down.
     */
    public function health(): array
    {
        if (! $this->configured()) {
            return ['status' => 'disabled', 'model' => null];
        }

        try {
            $request = Http::acceptJson()
                ->connectTimeout(1)
                ->timeout(2);

            if (filled(config('ocr_guard.token'))) {
                $request = $request->withToken(config('ocr_guard.token'));
            }

            $response = $request->get(rtrim((string) config('ocr_guard.url'), '/').'/health');

            if (! $response->successful()) {
                return ['status' => 'unavailable', 'model' => null];
            }

            $payload = $response->json();

            return [
                'status' => ($payload['status'] ?? null) === 'ok' ? 'ok' : 'unavailable',
                'model' => is_string($payload['model'] ?? null) ? $payload['model'] : null,
            ];
        } catch (\Throwable) {
            return ['status' => 'unavailable', 'model' => null];
        }
    }

    public function proofHash(Attendance $attendance): string
    {
        return hash('sha256', $this->proofBytes($attendance));
    }

    public function checkAttendance(Attendance $attendance): array
    {
        if (! $attendance->proof_file) {
            throw new \RuntimeException('Tidak ada file bukti untuk dianalisis.');
        }

        $bytes = $this->proofBytes($attendance);
        $filename = $this->proofFilename($attendance->proof_file);
        $request = Http::acceptJson()
            ->asMultipart()
            ->connectTimeout((int) config('ocr_guard.connect_timeout', 10))
            ->timeout((int) config('ocr_guard.timeout', 90));

        if (filled(config('ocr_guard.token'))) {
            $request = $request->withToken(config('ocr_guard.token'));
        }

        $response = $request
            ->attach('image_file', $bytes, $filename)
            ->post(rtrim((string) config('ocr_guard.url'), '/').'/v1/check-document', [
                'expected_document' => $this->expectedDocument($attendance),
                'language' => 'id',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('OCR guard gagal: HTTP '.$response->status());
        }

        return $this->normalizeResponse($response->json());
    }

    public function expectedDocument(Attendance $attendance): string
    {
        return $attendance->status === Attendance::STATUS_IZIN
            ? 'permission_letter'
            : 'kajian_note';
    }

    protected function proofBytes(Attendance $attendance): string
    {
        $path = (string) $attendance->proof_file;

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $response = Http::connectTimeout(10)->timeout(30)->get($path);

            if (! $response->successful()) {
                throw new \RuntimeException('File bukti tidak dapat diambil dari storage.');
            }

            return $response->body();
        }

        $path = preg_replace('#^'.preg_quote(config('filesystems.disks.public.url', ''), '#').'/?#', '', $path) ?: $path;
        $path = preg_replace('#^/?storage/#', '', $path) ?: $path;

        if (! Storage::disk('public')->exists($path)) {
            throw new \RuntimeException('File bukti lokal tidak ditemukan.');
        }

        return Storage::disk('public')->get($path);
    }

    protected function proofFilename(string $path): string
    {
        $name = basename(parse_url($path, PHP_URL_PATH) ?: $path);

        return str_contains($name, '.') ? $name : 'attendance-proof.jpg';
    }

    protected function normalizeResponse(mixed $payload): array
    {
        $payload = is_array($payload) ? $payload : [];

        return [
            'document_signal' => in_array($payload['document_signal'] ?? '', ['strong', 'weak', 'none'], true)
                ? $payload['document_signal']
                : 'none',
            'text_chars' => max(0, (int) ($payload['text_chars'] ?? 0)),
            'text_boxes' => max(0, (int) ($payload['text_boxes'] ?? 0)),
            'ocr_confidence' => max(0, min(100, (int) ($payload['ocr_confidence'] ?? 0))),
            'language' => Str::limit((string) ($payload['language'] ?? 'id'), 20, ''),
            'reason_code' => Str::limit((string) ($payload['reason_code'] ?? 'unknown'), 60, ''),
            'raw_text' => Str::limit((string) ($payload['raw_text'] ?? ''), (int) config('ocr_guard.max_raw_text', 1000), ''),
            'model' => Str::limit((string) ($payload['model'] ?? config('ocr_guard.model', 'rapidocr')), 120, ''),
            'image_width' => max(0, (int) ($payload['image_width'] ?? 0)),
            'image_height' => max(0, (int) ($payload['image_height'] ?? 0)),
            'payload' => $payload,
        ];
    }
}
