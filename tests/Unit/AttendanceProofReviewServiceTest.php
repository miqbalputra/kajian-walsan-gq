<?php

namespace Tests\Unit;

use App\Models\Attendance;
use App\Services\AiProviderService;
use App\Services\AttendanceProofReviewService;
use App\Services\OcrGuardService;
use ReflectionMethod;
use Tests\TestCase;

class AttendanceProofReviewServiceTest extends TestCase
{
    public function test_clear_note_is_approved_by_ocr_signal(): void
    {
        $result = $this->decide(new Attendance(['status' => Attendance::STATUS_HADIR_ONLINE]), [
            'document_signal' => 'strong',
            'text_chars' => 90,
            'text_boxes' => 5,
            'ocr_confidence' => 88,
        ]);

        $this->assertSame('approve', $result['decision']);
    }

    public function test_handwritten_or_unreadable_note_remains_pending(): void
    {
        $result = $this->decide(new Attendance(['status' => Attendance::STATUS_HADIR_ONLINE]), [
            'document_signal' => 'none',
            'text_chars' => 0,
            'text_boxes' => 0,
            'ocr_confidence' => 0,
        ]);

        $this->assertSame('needs_review', $result['decision']);
    }

    public function test_letter_without_readable_text_is_rejected_only_at_strong_signal(): void
    {
        $result = $this->decide(new Attendance(['status' => Attendance::STATUS_IZIN]), [
            'document_signal' => 'none',
            'text_chars' => 0,
            'text_boxes' => 0,
            'ocr_confidence' => 95,
        ]);

        $this->assertSame('reject', $result['decision']);
    }

    private function decide(Attendance $attendance, array $ocr): array
    {
        $service = new AttendanceProofReviewService(new OcrGuardService, new AiProviderService);
        $method = new ReflectionMethod($service, 'decide');
        $method->setAccessible(true);

        return $method->invoke($service, $attendance, $ocr);
    }
}
