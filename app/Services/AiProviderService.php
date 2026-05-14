<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiProviderService
{
    public function configured(): bool
    {
        return (bool) Setting::get('ai_enabled', false)
            && filled(Setting::get('ai_endpoint_url'))
            && filled($this->apiKey())
            && filled(Setting::get('ai_model'));
    }

    public function apiKey(): ?string
    {
        $encrypted = Setting::get('ai_api_key_encrypted');

        if (! $encrypted) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable $exception) {
            Log::warning('[AI] Failed to decrypt API key', ['error' => $exception->getMessage()]);
            return null;
        }
    }

    public function listModels(?string $endpoint = null, ?string $apiKey = null): array
    {
        $endpoint = $endpoint ?: Setting::get('ai_endpoint_url');
        $apiKey = $apiKey ?: $this->apiKey();

        if (! $endpoint || ! $apiKey) {
            return [];
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(30)
            ->get($this->modelsUrl($endpoint));

        if (! $response->successful()) {
            throw new \RuntimeException('Gagal mengambil model AI: HTTP ' . $response->status());
        }

        return collect($response->json('data', []))
            ->map(fn ($model) => $model['id'] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    public function chat(array $messages, ?string $model = null, float $temperature = 0.2, ?array $responseFormat = null): string
    {
        if (! $this->configured()) {
            throw new \RuntimeException('AI belum dikonfigurasi di Pengaturan.');
        }

        $endpoint = Setting::get('ai_endpoint_url');
        $payload = [
            'model' => $model ?: Setting::get('ai_model'),
            'messages' => $messages,
            'temperature' => $temperature,
        ];

        if ($responseFormat) {
            $payload['response_format'] = $responseFormat;
        }

        $response = Http::withToken($this->apiKey())
            ->acceptJson()
            ->asJson()
            ->timeout(90)
            ->post($this->chatUrl($endpoint), $payload);

        if (! $response->successful()) {
            Log::warning('[AI] Chat request failed', [
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 500),
            ]);

            throw new \RuntimeException('AI gagal merespons: HTTP ' . $response->status());
        }

        return $response->json('choices.0.message.content', '');
    }

    public function reviewAttendance(Attendance $attendance): array
    {
        if (! $this->configured()) {
            return [
                'decision' => 'needs_review',
                'confidence' => 0,
                'reason' => 'AI belum dikonfigurasi.',
            ];
        }

        $attendance->loadMissing(['parent.user', 'parent.students.classRoom', 'kajianEvent']);

        if (! $attendance->proof_file) {
            return [
                'decision' => 'needs_review',
                'confidence' => 0,
                'reason' => 'Tidak ada file bukti untuk dianalisis.',
            ];
        }

        $proofUrl = CloudinaryService::getDisplayUrl($attendance->proof_file);
        $expectedDocument = $attendance->status === Attendance::STATUS_IZIN
            ? 'surat pernyataan izin tidak bisa ikut kajian'
            : 'catatan hasil menyimak kajian online';

        $messages = [
            [
                'role' => 'system',
                'content' => 'Anda adalah pemeriksa administrasi Kajian Walsan. Jawab hanya JSON valid dengan keys: decision, confidence, reason. decision hanya boleh approve, needs_review, atau reject. Jangan mengarang isi gambar jika tidak terlihat.',
            ],
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => implode("\n", [
                            'Tugas: nilai apakah gambar/file ini layak disetujui sebagai bukti presensi.',
                            'Jenis bukti yang diharapkan: ' . $expectedDocument . '.',
                            'Nama wali: ' . ($attendance->parent?->user?->name ?? '-'),
                            'Status presensi: ' . $attendance->status_display,
                            'Kajian: ' . ($attendance->kajianEvent?->title ?? '-'),
                            'Tanggal kajian: ' . optional($attendance->kajianEvent?->date)->format('d/m/Y'),
                            'Catatan user: ' . ($attendance->notes ?: '-'),
                            '',
                            'Kriteria approve untuk catatan kajian: terlihat seperti catatan kajian, berisi poin materi atau ringkasan yang relevan, bukan gambar kosong/random.',
                            'Kriteria approve untuk izin: terlihat seperti surat/pernyataan izin atau keterangan izin yang wajar, bukan gambar kosong/random.',
                            'Jika tulisan kurang jelas tetapi bentuk dokumen masih masuk akal, gunakan needs_review. Jangan auto-reject kecuali jelas tidak sesuai.',
                            'Confidence 0-100.',
                        ]),
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => ['url' => $proofUrl],
                    ],
                ],
            ],
        ];

        $content = $this->chat($messages, temperature: 0.1, responseFormat: ['type' => 'json_object']);
        $result = json_decode($content, true);

        if (! is_array($result)) {
            $result = [
                'decision' => 'needs_review',
                'confidence' => 0,
                'reason' => 'AI memberi respons tidak terstruktur: ' . Str::limit($content, 180),
            ];
        }

        return [
            'decision' => in_array($result['decision'] ?? '', ['approve', 'needs_review', 'reject'], true)
                ? $result['decision']
                : 'needs_review',
            'confidence' => max(0, min(100, (int) ($result['confidence'] ?? 0))),
            'reason' => Str::limit((string) ($result['reason'] ?? 'Tidak ada alasan dari AI.'), 1000, ''),
            'raw' => $result,
        ];
    }

    public function autoReviewAttendance(Attendance $attendance): array
    {
        if (! $this->configured()) {
            return [
                'decision' => 'needs_review',
                'confidence' => 0,
                'reason' => 'AI belum dikonfigurasi.',
            ];
        }

        $result = $this->reviewAttendance($attendance);
        $threshold = (int) Setting::get('ai_auto_approve_min_confidence', 80);
        $model = Setting::get('ai_model');

        $updates = [
            'ai_validation_status' => $result['decision'],
            'ai_validation_confidence' => $result['confidence'],
            'ai_validation_reason' => $result['reason'],
            'ai_validation_model' => $model,
            'ai_validation_payload' => $result['raw'] ?? $result,
            'ai_validated_at' => now(),
        ];

        if (($result['decision'] ?? '') === 'approve' && ($result['confidence'] ?? 0) >= $threshold) {
            $updates['validation_status'] = Attendance::VALIDATION_APPROVED;
            $updates['validated_by'] = null;
            $updates['validated_at'] = now();
            $updates['rejection_reason'] = null;
        }

        $attendance->update($updates);

        return $result;
    }

    public function chatWithDatabase(string $question): string
    {
        $context = $this->buildDatabaseContext($question);

        return $this->chat([
            [
                'role' => 'system',
                'content' => 'Anda adalah asisten admin Kajian Walsan. Jawab dalam Bahasa Indonesia. Gunakan hanya konteks data yang diberikan. Jika user meminta foto/file, berikan link bukti yang tersedia. Jangan mengklaim melihat gambar kecuali isi gambar disediakan.',
            ],
            [
                'role' => 'user',
                'content' => "Pertanyaan admin:\n{$question}\n\nKonteks database saat ini:\n{$context}",
            ],
        ], temperature: 0.2);
    }

    protected function buildDatabaseContext(string $question): string
    {
        $includeProofs = Str::contains(Str::lower($question), ['foto', 'bukti', 'catatan', 'surat', 'gambar', 'file']);

        $attendanceSummary = Attendance::query()
            ->selectRaw('status, validation_status, count(*) as total')
            ->groupBy('status', 'validation_status')
            ->get()
            ->map(fn ($row) => "{$row->status}/{$row->validation_status}: {$row->total}")
            ->implode('; ');

        $recentUploads = Attendance::with(['parent.user', 'parent.students.classRoom', 'kajianEvent'])
            ->whereNotNull('proof_file')
            ->orderByDesc('created_at')
            ->limit($includeProofs ? 30 : 12)
            ->get()
            ->map(function (Attendance $attendance) {
                $student = $attendance->parent?->students?->first();
                return [
                    'id' => $attendance->id,
                    'wali' => $attendance->parent?->user?->name,
                    'anak' => $student?->name,
                    'kelas' => $student?->classRoom?->name,
                    'kajian' => $attendance->kajianEvent?->title,
                    'tanggal' => optional($attendance->kajianEvent?->date)->format('d/m/Y'),
                    'status' => $attendance->status,
                    'validasi' => $attendance->validation_status,
                    'catatan' => $attendance->notes,
                    'ai' => trim(($attendance->ai_validation_status ?? '-') . ' ' . ($attendance->ai_validation_confidence ? "({$attendance->ai_validation_confidence}%)" : '')),
                    'bukti_url' => CloudinaryService::getDisplayUrl($attendance->proof_file),
                ];
            })
            ->values()
            ->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return implode("\n", [
            'Jumlah user: ' . User::count(),
            'Jumlah wali/guru parent profile: ' . ParentModel::count(),
            'Jumlah santri: ' . Student::count(),
            'Jumlah kajian: ' . KajianEvent::count(),
            'Ringkasan presensi: ' . ($attendanceSummary ?: '-'),
            'Upload bukti terbaru JSON: ' . $recentUploads,
        ]);
    }

    protected function chatUrl(string $endpoint): string
    {
        $endpoint = rtrim($endpoint, '/');

        if (Str::endsWith($endpoint, '/chat/completions')) {
            return $endpoint;
        }

        return $endpoint . '/chat/completions';
    }

    protected function modelsUrl(string $endpoint): string
    {
        $endpoint = rtrim($endpoint, '/');

        if (Str::endsWith($endpoint, '/chat/completions')) {
            return Str::beforeLast($endpoint, '/chat/completions') . '/models';
        }

        return $endpoint . '/models';
    }
}
