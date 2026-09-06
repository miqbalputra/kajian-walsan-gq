<?php

namespace Tests\Feature;

use App\Jobs\SendTelegramReview;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\TelegramReviewRequest;
use App\Models\User;
use App\Services\AiProviderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramReviewFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.telegram_review.enabled' => true,
            'services.telegram_review.bot_token' => 'test-token',
            'services.telegram_review.chat_id' => '-1001234567890',
            'services.telegram_review.webhook_secret' => 'telegram-test-secret',
            'services.n8n.enabled' => false,
        ]);

        Http::fake(fn () => Http::response([
            'ok' => true,
            'result' => ['message_id' => 789],
        ]));
    }

    public function test_new_online_proof_is_pending_and_sent_to_telegram_but_physical_proof_is_not(): void
    {
        Bus::fake();
        [, $guardian, $event] = $this->fixture();

        $online = Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $guardian->id,
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'method' => Attendance::METHOD_UPLOAD,
            'proof_file' => 'attendance-proofs/catatan.jpg',
            'validation_status' => Attendance::VALIDATION_APPROVED,
        ]);

        $this->assertSame(Attendance::VALIDATION_PENDING, $online->fresh()->validation_status);
        $this->assertDatabaseHas('telegram_review_requests', [
            'attendance_id' => $online->id,
            'proof_file' => 'attendance-proofs/catatan.jpg',
            'delivery_status' => TelegramReviewRequest::DELIVERY_QUEUED,
        ]);
        Bus::assertDispatched(SendTelegramReview::class);

        $otherGuardianUser = User::factory()->create(['is_active' => true]);
        $otherGuardian = ParentModel::create([
            'user_id' => $otherGuardianUser->id,
            'type' => 'mother',
            'qr_code_string' => 'P-'.str()->upper(str()->random(20)),
        ]);

        $physical = Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $otherGuardian->id,
            'status' => Attendance::STATUS_HADIR_FISIK,
            'method' => Attendance::METHOD_UPLOAD,
            'proof_file' => 'teacher-attendance-notes/catatan.jpg',
            'validation_status' => Attendance::VALIDATION_APPROVED,
        ]);

        $this->assertSame(Attendance::VALIDATION_APPROVED, $physical->fresh()->validation_status);
        $this->assertDatabaseCount('telegram_review_requests', 1);
    }

    public function test_reupload_expires_old_telegram_request_and_queues_a_new_one(): void
    {
        Bus::fake();
        [, $guardian, $event] = $this->fixture();
        $attendance = Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $guardian->id,
            'status' => Attendance::STATUS_IZIN,
            'method' => Attendance::METHOD_UPLOAD,
            'proof_file' => 'izin-documents/lama.jpg',
            'validation_status' => Attendance::VALIDATION_PENDING,
        ]);

        $old = TelegramReviewRequest::firstOrFail();
        $old->update(['delivery_status' => TelegramReviewRequest::DELIVERY_SENT, 'message_id' => 10]);

        $attendance->update([
            'proof_file' => 'izin-documents/baru.jpg',
            'validation_status' => Attendance::VALIDATION_APPROVED,
        ]);

        $this->assertSame(Attendance::VALIDATION_PENDING, $attendance->fresh()->validation_status);
        $this->assertDatabaseHas('telegram_review_requests', [
            'id' => $old->id,
            'delivery_status' => TelegramReviewRequest::DELIVERY_EXPIRED,
        ]);
        $this->assertDatabaseHas('telegram_review_requests', [
            'attendance_id' => $attendance->id,
            'proof_file' => 'izin-documents/baru.jpg',
            'delivery_status' => TelegramReviewRequest::DELIVERY_QUEUED,
        ]);
    }

    public function test_ai_recommendation_does_not_change_telegram_controlled_proof_status(): void
    {
        [, $guardian, $event] = $this->fixture();
        $attendance = Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $guardian->id,
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'method' => Attendance::METHOD_UPLOAD,
            'proof_file' => 'attendance-proofs/catatan.jpg',
            'validation_status' => Attendance::VALIDATION_PENDING,
        ]);

        $ai = new class extends AiProviderService {
            public function configured(): bool
            {
                return true;
            }

            public function reviewAttendance(Attendance $attendance): array
            {
                return ['decision' => 'approve', 'confidence' => 100, 'reason' => 'Bukti terlihat baik.', 'raw' => []];
            }
        };

        $ai->autoReviewAttendance($attendance);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'validation_status' => Attendance::VALIDATION_PENDING,
            'ai_validation_status' => 'approve',
        ]);
    }

    public function test_linked_admin_can_approve_from_valid_webhook(): void
    {
        [$guardian, $attendance] = $this->pendingAttendanceWithSentRequest();
        $admin = $this->makeAdmin('11223344');

        $response = $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'telegram-test-secret')
            ->postJson(route('telegram.review.webhook'), $this->callbackPayload($attendance->telegramReviewRequests()->firstOrFail()->token, 'a', $admin->telegram_user_id));

        $response->assertOk();
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'validation_status' => Attendance::VALIDATION_APPROVED,
            'validated_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('telegram_review_requests', [
            'attendance_id' => $attendance->id,
            'delivery_status' => TelegramReviewRequest::DELIVERY_RESOLVED,
            'decision' => TelegramReviewRequest::DECISION_APPROVED,
            'reviewed_by_user_id' => $admin->id,
            'reviewed_by_telegram_id' => $admin->telegram_user_id,
        ]);
    }

    public function test_unmapped_user_and_invalid_secret_cannot_change_attendance(): void
    {
        [, $attendance] = $this->pendingAttendanceWithSentRequest();
        $token = $attendance->telegramReviewRequests()->firstOrFail()->token;

        $this->postJson(route('telegram.review.webhook'), $this->callbackPayload($token, 'a', '99887766'))
            ->assertUnauthorized();
        $this->assertSame(Attendance::VALIDATION_PENDING, $attendance->fresh()->validation_status);

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'telegram-test-secret')
            ->postJson(route('telegram.review.webhook'), $this->callbackPayload($token, 'a', '99887766'))
            ->assertOk();
        $this->assertSame(Attendance::VALIDATION_PENDING, $attendance->fresh()->validation_status);
    }

    public function test_reject_flow_requires_a_preset_reason_then_records_it(): void
    {
        [, $attendance] = $this->pendingAttendanceWithSentRequest();
        $admin = $this->makeAdmin('55667788');
        $token = $attendance->telegramReviewRequests()->firstOrFail()->token;

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'telegram-test-secret')
            ->postJson(route('telegram.review.webhook'), $this->callbackPayload($token, 'r', $admin->telegram_user_id))
            ->assertOk();
        $this->assertSame(Attendance::VALIDATION_PENDING, $attendance->fresh()->validation_status);

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'telegram-test-secret')
            ->postJson(route('telegram.review.webhook'), $this->callbackPayload($token, 'b', $admin->telegram_user_id))
            ->assertOk();

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'validation_status' => Attendance::VALIDATION_REJECTED,
            'rejection_reason' => 'Foto bukti buram atau kualitasnya tidak memadai.',
        ]);
    }

    /** @return array{0: ParentModel, 1: Attendance} */
    private function pendingAttendanceWithSentRequest(): array
    {
        [, $guardian, $event] = $this->fixture();
        $attendance = Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $guardian->id,
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'method' => Attendance::METHOD_UPLOAD,
            'proof_file' => 'attendance-proofs/catatan.jpg',
            'validation_status' => Attendance::VALIDATION_PENDING,
        ]);

        $request = TelegramReviewRequest::where('attendance_id', $attendance->id)->firstOrFail();
        $request->update([
            'delivery_status' => TelegramReviewRequest::DELIVERY_SENT,
            'message_id' => 789,
            'sent_at' => now(),
        ]);

        return [$guardian, $attendance->fresh()];
    }

    /** @return array{0: User, 1: ParentModel, 2: KajianEvent} */
    private function fixture(): array
    {
        $guardianUser = User::factory()->create(['is_active' => true]);
        $guardian = ParentModel::create([
            'user_id' => $guardianUser->id,
            'type' => 'father',
            'qr_code_string' => 'P-'.str()->upper(str()->random(20)),
        ]);
        $year = AcademicYear::active();
        $event = KajianEvent::create([
            'academic_year_id' => $year->id,
            'title' => 'Kajian Telegram Test',
            'date' => today(),
            'time_start' => '08:00',
            'time_end' => '10:00',
            'status' => 'open',
            'category' => 'kajian',
        ]);

        return [$guardianUser, $guardian, $event];
    }

    private function makeAdmin(string $telegramId): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', 'admin')->value('id'),
            'telegram_user_id' => $telegramId,
            'is_active' => true,
        ]);
    }

    /** @return array<string, mixed> */
    private function callbackPayload(string $token, string $action, string $telegramId): array
    {
        return [
            'callback_query' => [
                'id' => 'callback-'.str()->random(12),
                'from' => ['id' => $telegramId],
                'message' => ['chat' => ['id' => '-1001234567890']],
                'data' => "tr:{$token}:{$action}",
            ],
        ];
    }
}
