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

    public function chatWithDatabase(string $question, array $conversation = []): string
    {
        $context = $this->buildDatabaseContext($question);
        $history = collect($conversation)
            ->filter(fn ($message) => in_array($message['role'] ?? '', ['user', 'assistant'], true))
            ->take(-12)
            ->map(fn ($message) => [
                'role' => $message['role'],
                'content' => Str::limit((string) ($message['content'] ?? ''), 1200, ''),
            ])
            ->values()
            ->all();

        $messages = [
            [
                'role' => 'system',
                'content' => implode(' ', [
                    'Anda adalah asisten admin Kajian Walsan.',
                    'Jawab dalam Bahasa Indonesia.',
                    'Akurasi wajib berbasis DATA_JSON yang diberikan, bukan asumsi.',
                    'Jika data tidak ada di konteks, katakan data tidak tersedia dan minta filter yang lebih spesifik.',
                    'Untuk angka, hitung dari DATA_JSON atau gunakan total eksplisit yang tersedia.',
                    'Jika user meminta foto/file/catatan/surat, tampilkan URL bukti yang tersedia.',
                    'Jangan mengklaim melihat isi gambar kecuali gambar memang dikirim sebagai input visual.',
                ]),
            ],
        ];

        $messages = array_merge($messages, $history);
        $messages[] = [
            'role' => 'user',
            'content' => "Pertanyaan admin:\n{$question}\n\nDATA_JSON dari database saat ini:\n{$context}",
        ];

        return $this->chat($messages, temperature: 0);
    }

    protected function buildDatabaseContext(string $question): string
    {
        $lowerQuestion = Str::lower($question);
        $includeProofs = Str::contains($lowerQuestion, ['foto', 'bukti', 'catatan', 'surat', 'gambar', 'file']);
        $wantsComplete = Str::contains($lowerQuestion, ['lengkap', 'semua', 'detail', '100%', 'seluruh']);
        $attendanceLimit = $wantsComplete ? 500 : ($includeProofs ? 150 : 80);
        $peopleLimit = $wantsComplete ? 400 : 120;
        $eventLimit = $wantsComplete ? 200 : 80;

        $attendanceSummary = Attendance::query()
            ->selectRaw('status, validation_status, count(*) as total')
            ->groupBy('status', 'validation_status')
            ->get()
            ->map(fn ($row) => "{$row->status}/{$row->validation_status}: {$row->total}")
            ->implode('; ');

        $attendances = Attendance::with(['parent.user', 'parent.students.classRoom', 'kajianEvent', 'validator'])
            ->orderByDesc('created_at')
            ->limit($attendanceLimit)
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
                    'waktu_kirim' => optional($attendance->created_at)->format('d/m/Y H:i'),
                    'status' => $attendance->status,
                    'validasi' => $attendance->validation_status,
                    'validator' => $attendance->validator?->name,
                    'catatan' => $attendance->notes,
                    'alasan_ditolak' => $attendance->rejection_reason,
                    'ai' => trim(($attendance->ai_validation_status ?? '-') . ' ' . ($attendance->ai_validation_confidence ? "({$attendance->ai_validation_confidence}%)" : '')),
                    'bukti_url' => CloudinaryService::getDisplayUrl($attendance->proof_file),
                ];
            })
            ->values()
            ->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $parents = ParentModel::with(['user', 'students.classRoom'])
            ->orderBy('id')
            ->limit($peopleLimit)
            ->get()
            ->map(fn (ParentModel $parent) => [
                'id' => $parent->id,
                'nama' => $parent->user?->name,
                'username' => $parent->user?->username,
                'email' => $parent->user?->email,
                'tipe' => $parent->type,
                'tipe_display' => $parent->type_display,
                'qr_code' => $parent->qr_code_string,
                'anak' => $parent->students->map(fn (Student $student) => [
                    'id' => $student->id,
                    'nama' => $student->name,
                    'nis' => $student->nis,
                    'kelas' => $student->classRoom?->name,
                ])->values()->all(),
            ])
            ->values()
            ->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $events = KajianEvent::query()
            ->orderByDesc('date')
            ->orderByDesc('time_start')
            ->limit($eventLimit)
            ->get()
            ->map(fn (KajianEvent $event) => [
                'id' => $event->id,
                'judul' => $event->title,
                'tanggal' => optional($event->date)->format('d/m/Y'),
                'jam_mulai' => optional($event->time_start)->format('H:i'),
                'jam_selesai' => optional($event->time_end)->format('H:i'),
                'pemateri' => $event->speaker,
                'status' => $event->status,
                'jumlah_presensi' => $event->attendance_count,
            ])
            ->values()
            ->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return json_encode([
            'meta' => [
                'generated_at' => now()->format('d/m/Y H:i:s'),
                'question' => $question,
                'limits' => [
                    'attendances_sent' => $attendanceLimit,
                    'parents_sent' => $peopleLimit,
                    'events_sent' => $eventLimit,
                ],
                'totals' => [
                    'users' => User::count(),
                    'parents_and_teachers' => ParentModel::count(),
                    'students' => Student::count(),
                    'kajian_events' => KajianEvent::count(),
                    'attendances' => Attendance::count(),
                ],
                'attendance_summary' => $attendanceSummary ?: '-',
            ],
            'kajian_events' => json_decode($events, true),
            'parents_and_teachers' => json_decode($parents, true),
            'attendances' => json_decode($attendances, true),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
