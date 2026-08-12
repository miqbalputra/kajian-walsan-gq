<?php

namespace App\Services;

use App\Jobs\ReviewAttendanceProof;
use App\Models\Attendance;
use App\Models\AttendanceAiReview;
use App\Models\Setting;
use Illuminate\Support\Str;

class AttendanceProofReviewService
{
    public function __construct(
        protected OcrGuardService $ocrGuard,
        protected AiProviderService $externalProvider,
    ) {
    }

    public function configured(): bool
    {
        return $this->ocrGuard->configured() || $this->externalProvider->configured();
    }

    public function queue(Attendance $attendance): void
    {
        $attendance->update([
            'ai_validation_status' => 'queued',
            'ai_validation_confidence' => null,
            'ai_validation_reason' => 'Upload diterima dan menunggu pemeriksaan otomatis.',
            'ai_validation_model' => $this->providerName(),
            'ai_validation_payload' => [
                'provider' => $this->providerName(),
                'status' => 'queued',
            ],
            'ai_validated_at' => null,
        ]);

        ReviewAttendanceProof::dispatch($attendance->fresh());
    }

    public function review(Attendance $attendance): array
    {
        if ($this->ocrGuard->configured()) {
            return $this->reviewWithOcr($attendance);
        }

        if ($this->externalProvider->configured()) {
            return $this->reviewWithExternalProvider($attendance);
        }

        $result = [
            'decision' => 'needs_review',
            'confidence' => 0,
            'reason' => 'Pemeriksaan otomatis belum dikonfigurasi.',
        ];

        $attendance->update([
            'ai_validation_status' => $result['decision'],
            'ai_validation_confidence' => 0,
            'ai_validation_reason' => $result['reason'],
            'ai_validation_model' => null,
            'ai_validation_payload' => $result,
            'ai_validated_at' => now(),
        ]);

        return $result;
    }

    protected function reviewWithExternalProvider(Attendance $attendance): array
    {
        $result = $this->externalProvider->autoReviewAttendance($attendance);

        // Fallback provider tetap dicatat agar seluruh pemeriksaan bukti punya
        // histori yang dapat diaudit, meskipun OCR lokal sedang dinonaktifkan.
        if ($attendance->proof_file) {
            try {
                $hash = $this->ocrGuard->proofHash($attendance);
                AttendanceAiReview::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                        'proof_hash' => $hash,
                        'provider' => 'external',
                    ],
                    [
                        'model' => Setting::get('ai_model'),
                        'status' => 'completed',
                        'decision' => $result['decision'] ?? 'needs_review',
                        'reason_code' => 'external_provider',
                        'confidence' => (int) ($result['confidence'] ?? 0),
                        'reason' => $result['reason'] ?? null,
                        'payload' => $result['raw'] ?? $result,
                        'completed_at' => now(),
                        'error' => null,
                    ]
                );
            } catch (\Throwable $exception) {
                logger()->warning('[AttendanceProofReview] Gagal menyimpan histori provider eksternal', [
                    'attendance_id' => $attendance->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $result;
    }

    protected function reviewWithOcr(Attendance $attendance): array
    {
        $attendance->refresh();

        if (! $attendance->proof_file) {
            return $this->persistUnavailable($attendance, 'File bukti tidak tersedia.');
        }

        $hash = $this->ocrGuard->proofHash($attendance);
        $review = AttendanceAiReview::firstOrCreate(
            [
                'attendance_id' => $attendance->id,
                'proof_hash' => $hash,
                'provider' => 'rapidocr',
            ],
            [
                'model' => config('ocr_guard.model', 'PP-OCRv6-small-id'),
                'status' => 'queued',
                'attempt' => 0,
            ]
        );

        if ($review->status === 'completed') {
            $decision = [
                'decision' => in_array($review->decision, ['approve', 'reject', 'needs_review'], true)
                    ? $review->decision
                    : 'needs_review',
                'confidence' => (int) ($review->confidence ?? 0),
                'reason' => $review->reason ?: 'Pemeriksaan otomatis sudah selesai.',
                'provider' => 'rapidocr',
                'shadow_mode' => (bool) config('ocr_guard.shadow_mode', true),
            ];

            $updates = [
                'ai_validation_status' => $decision['decision'],
                'ai_validation_confidence' => $decision['confidence'],
                'ai_validation_reason' => $decision['reason'],
                'ai_validation_model' => $review->model,
                'ai_validation_payload' => [
                    'provider' => 'rapidocr',
                    'shadow_mode' => $decision['shadow_mode'],
                    'reused_review_id' => $review->id,
                    'ocr' => $review->payload,
                ],
                'ai_validated_at' => now(),
            ];

            if (! $decision['shadow_mode']) {
                $this->applyDecision($updates, $attendance, $decision);
            }

            $attendance->update($updates);

            return $decision;
        }

        $review->update([
            'status' => 'processing',
            'attempt' => ((int) $review->attempt) + 1,
            'started_at' => now(),
            'completed_at' => null,
            'error' => null,
        ]);

        try {
            $ocr = $this->ocrGuard->checkAttendance($attendance);
            $decision = $this->decide($attendance, $ocr);
            $shadow = (bool) config('ocr_guard.shadow_mode', true);

            $reason = $decision['reason'];
            $updates = [
                'ai_validation_status' => $decision['decision'],
                'ai_validation_confidence' => $decision['confidence'],
                'ai_validation_reason' => $reason,
                'ai_validation_model' => $ocr['model'],
                'ai_validation_payload' => [
                    'provider' => 'rapidocr',
                    'shadow_mode' => $shadow,
                    'expected_document' => $this->ocrGuard->expectedDocument($attendance),
                    'ocr' => $ocr['payload'],
                ],
                'ai_validated_at' => now(),
            ];

            if (! $shadow) {
                $this->applyDecision($updates, $attendance, $decision);
            }

            $attendance->update($updates);
            $review->update([
                'model' => $ocr['model'],
                'status' => 'completed',
                'decision' => $decision['decision'],
                'reason_code' => $ocr['reason_code'],
                'confidence' => $decision['confidence'],
                'text_chars' => $ocr['text_chars'],
                'text_boxes' => $ocr['text_boxes'],
                'language' => $ocr['language'],
                'document_signal' => $ocr['document_signal'],
                'reason' => $reason,
                'raw_text_preview' => $ocr['raw_text'],
                'payload' => $ocr['payload'],
                'completed_at' => now(),
            ]);

            return $decision + [
                'provider' => 'rapidocr',
                'shadow_mode' => $shadow,
                'ocr' => $ocr,
            ];
        } catch (\Throwable $exception) {
            $review->update([
                'status' => 'failed',
                'error' => Str::limit($exception->getMessage(), 2000, ''),
                'completed_at' => now(),
            ]);

            $attendance->update([
                'ai_validation_status' => 'failed',
                'ai_validation_confidence' => 0,
                'ai_validation_reason' => 'Pemeriksaan otomatis gagal; menunggu review admin.',
                'ai_validation_model' => config('ocr_guard.model', 'PP-OCRv6-small-id'),
                'ai_validation_payload' => [
                    'provider' => 'rapidocr',
                    'error' => Str::limit($exception->getMessage(), 500, ''),
                ],
                'ai_validated_at' => now(),
            ]);

            throw $exception;
        }
    }

    protected function decide(Attendance $attendance, array $ocr): array
    {
        $expected = $this->ocrGuard->expectedDocument($attendance);
        $minimumChars = $expected === 'permission_letter'
            ? (int) config('ocr_guard.approve_letter_min_chars', 80)
            : (int) config('ocr_guard.approve_note_min_chars', 40);
        $minimumBoxes = (int) config('ocr_guard.approve_min_boxes', 2);
        $minimumConfidence = (int) config('ocr_guard.approve_min_confidence', 70);
        $confidence = (int) $ocr['ocr_confidence'];

        if (in_array($ocr['reason_code'] ?? '', ['invalid_image', 'blank_image', 'low_resolution'], true)) {
            return [
                'decision' => 'reject',
                'confidence' => 100,
                'reason' => 'File gambar tidak valid, terlalu kosong, atau resolusinya terlalu rendah.',
            ];
        }

        $strongDocument = $ocr['document_signal'] === 'strong'
            && $ocr['text_chars'] >= $minimumChars
            && $ocr['text_boxes'] >= $minimumBoxes
            && $confidence >= $minimumConfidence;

        if ($strongDocument) {
            return [
                'decision' => 'approve',
                'confidence' => $confidence,
                'reason' => $expected === 'permission_letter'
                    ? 'Surat izin memiliki struktur dan teks yang cukup jelas.'
                    : 'Catatan kajian memiliki struktur dan teks yang cukup jelas.',
            ];
        }

        // Surat yang mayoritas dicetak dapat ditolak jika benar-benar tidak
        // memiliki teks. Catatan tangan tetap pending agar tidak salah tolak.
        if ($expected === 'permission_letter'
            && $ocr['document_signal'] === 'none'
            && $ocr['text_chars'] === 0
            && $confidence >= (int) config('ocr_guard.reject_min_confidence', 90)) {
            return [
                'decision' => 'reject',
                'confidence' => $confidence,
                'reason' => 'Foto tidak menunjukkan teks surat pernyataan yang dapat dibaca.',
            ];
        }

        return [
            'decision' => 'needs_review',
            'confidence' => $confidence,
            'reason' => $ocr['document_signal'] === 'none'
                ? 'OCR tidak menemukan teks yang cukup; perlu pemeriksaan admin agar catatan tangan tidak salah ditolak.'
                : 'Dokumen terdeteksi, tetapi teks atau kualitas gambar belum cukup untuk auto-approve.',
        ];
    }

    protected function applyDecision(array &$updates, Attendance $attendance, array $decision): void
    {
        if ($decision['decision'] === 'approve') {
            $updates['validation_status'] = Attendance::VALIDATION_APPROVED;
            $updates['validated_by'] = null;
            $updates['validated_at'] = now();
            $updates['rejection_reason'] = null;
        } elseif ($decision['decision'] === 'reject') {
            $updates['validation_status'] = Attendance::VALIDATION_REJECTED;
            $updates['validated_by'] = null;
            $updates['validated_at'] = now();
            $updates['rejection_reason'] = $decision['reason'];
        } else {
            $updates['validation_status'] = Attendance::VALIDATION_PENDING;
            $updates['validated_by'] = null;
            $updates['validated_at'] = null;
        }
    }

    protected function persistUnavailable(Attendance $attendance, string $reason): array
    {
        $result = ['decision' => 'needs_review', 'confidence' => 0, 'reason' => $reason];
        $attendance->update([
            'ai_validation_status' => 'needs_review',
            'ai_validation_confidence' => 0,
            'ai_validation_reason' => $reason,
            'ai_validation_payload' => $result,
            'ai_validated_at' => now(),
        ]);

        return $result;
    }

    protected function providerName(): string
    {
        return $this->ocrGuard->configured() ? 'rapidocr' : ($this->externalProvider->configured() ? 'external' : 'none');
    }
}
