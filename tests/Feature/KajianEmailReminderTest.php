<?php

namespace Tests\Feature;

use App\Jobs\SendKajianNotification;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\KajianNotificationDispatch;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class KajianEmailReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_email_dispatch_is_only_due_at_2030_and_is_idempotently_queued(): void
    {
        Queue::fake();
        $event = $this->makeEvent('2026-09-14', '18:00', '21:30');

        Carbon::setTestNow(Carbon::create(2026, 9, 14, 20, 29, 0, config('app.timezone')));
        Artisan::call('kajian:send-notifications');

        $this->assertDatabaseMissing('kajian_notification_dispatches', [
            'kajian_event_id' => $event->id,
            'notification_type' => KajianNotificationDispatch::TYPE_EMAIL_MISSING_ATTENDANCE,
        ]);

        Carbon::setTestNow(Carbon::create(2026, 9, 14, 20, 30, 0, config('app.timezone')));
        Artisan::call('kajian:send-notifications');
        Artisan::call('kajian:send-notifications');

        $this->assertDatabaseHas('kajian_notification_dispatches', [
            'kajian_event_id' => $event->id,
            'notification_type' => KajianNotificationDispatch::TYPE_EMAIL_MISSING_ATTENDANCE,
            'status' => KajianNotificationDispatch::STATUS_QUEUED,
        ]);
        $this->assertDatabaseCount('kajian_notification_dispatches', 1);
        Queue::assertPushed(SendKajianNotification::class, 1);
    }

    public function test_email_payload_contains_only_targeted_guardians_without_attendance(): void
    {
        Config::set([
            'services.n8n_email.enabled' => true,
            'services.n8n_email.webhook_url' => 'https://n8n.test/webhook/kajian-email',
            'services.n8n_email.webhook_secret' => 'test-secret',
        ]);
        Http::fake([
            'https://n8n.test/*' => Http::response(['accepted' => true], 202),
        ]);

        $event = $this->makeEvent('2026-09-14', '18:00', '19:00');
        $targetClass = ClassRoom::create([
            'name' => 'Kelas Email Sasaran',
            'level' => '1',
            'is_active' => true,
        ]);
        $event->targetClasses()->attach($targetClass->id);

        $missing = $this->makeGuardian($targetClass, 'missing');
        $attended = $this->makeGuardian($targetClass, 'attended');
        $outsideClass = ClassRoom::create([
            'name' => 'Kelas Email Di Luar',
            'level' => '2',
            'is_active' => true,
        ]);
        $outside = $this->makeGuardian($outsideClass, 'outside');

        Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $attended->id,
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'method' => Attendance::METHOD_UPLOAD,
            'validation_status' => Attendance::VALIDATION_PENDING,
        ]);

        $dispatch = KajianNotificationDispatch::create([
            'kajian_event_id' => $event->id,
            'notification_type' => KajianNotificationDispatch::TYPE_EMAIL_MISSING_ATTENDANCE,
            'scheduled_for' => now(),
        ]);

        SendKajianNotification::dispatchSync($dispatch->id);

        Http::assertSent(function ($request) use ($dispatch, $event, $missing, $outside): bool {
            $data = $request->data();
            $emails = collect($data['recipients'])->pluck('email')->all();

            return $request->url() === 'https://n8n.test/webhook/kajian-email'
                && $request->hasHeader('X-N8N-Webhook-Secret', 'test-secret')
                && $data['dispatch_id'] === $dispatch->id
                && $data['idempotency_key'] === 'kajian-email:'.$dispatch->id
                && $data['event']['id'] === $event->id
                && $data['subject'] === 'Pengingat - Anda Belum Presensi Kajian Wali Santri'
                && str_contains($data['text'], 'Mohon segera melakukan presensi')
                && $data['login_url'] === 'https://kajian.griyaquran.web.id/login'
                && $emails === [$missing->user->email]
                && ! in_array($outside->user->email, $emails, true);
        });

        $this->assertDatabaseHas('kajian_notification_dispatches', [
            'id' => $dispatch->id,
            'status' => KajianNotificationDispatch::STATUS_SENT,
            'total_count' => 1,
            'sent_count' => 1,
            'failed_count' => 0,
        ]);
    }

    public function test_disabled_email_webhook_is_skipped_without_an_application_error(): void
    {
        Config::set([
            'services.n8n_email.enabled' => false,
            'services.n8n_email.webhook_url' => '',
        ]);
        Http::fake();

        $event = $this->makeEvent('2026-09-14', '18:00', '19:00');
        $class = ClassRoom::create([
            'name' => 'Kelas Email Disabled',
            'level' => '1',
            'is_active' => true,
        ]);
        $this->makeGuardian($class, 'disabled');
        $dispatch = KajianNotificationDispatch::create([
            'kajian_event_id' => $event->id,
            'notification_type' => KajianNotificationDispatch::TYPE_EMAIL_MISSING_ATTENDANCE,
            'scheduled_for' => now(),
        ]);

        SendKajianNotification::dispatchSync($dispatch->id);

        $this->assertDatabaseHas('kajian_notification_dispatches', [
            'id' => $dispatch->id,
            'status' => KajianNotificationDispatch::STATUS_SKIPPED,
            'total_count' => 1,
            'sent_count' => 0,
            'failed_count' => 0,
        ]);
        Http::assertNothingSent();
    }

    public function test_n8n_http_error_marks_dispatch_failed_for_queue_retry(): void
    {
        Config::set([
            'services.n8n_email.enabled' => true,
            'services.n8n_email.webhook_url' => 'https://n8n.test/webhook/kajian-email',
            'services.n8n_email.webhook_secret' => 'test-secret',
        ]);
        Http::fake([
            'https://n8n.test/*' => Http::response(['error' => 'temporary'], 500),
        ]);

        $event = $this->makeEvent('2026-09-14', '18:00', '19:00');
        $class = ClassRoom::create([
            'name' => 'Kelas Email Error',
            'level' => '1',
            'is_active' => true,
        ]);
        $this->makeGuardian($class, 'error');
        $dispatch = KajianNotificationDispatch::create([
            'kajian_event_id' => $event->id,
            'notification_type' => KajianNotificationDispatch::TYPE_EMAIL_MISSING_ATTENDANCE,
            'scheduled_for' => now(),
        ]);

        try {
            SendKajianNotification::dispatchSync($dispatch->id);
            $this->fail('Expected the n8n webhook error to be rethrown for queue retry.');
        } catch (\Throwable $exception) {
            $this->assertStringContainsString('HTTP 500', $exception->getMessage());
        }

        $this->assertDatabaseHas('kajian_notification_dispatches', [
            'id' => $dispatch->id,
            'status' => KajianNotificationDispatch::STATUS_FAILED,
            'attempts' => 1,
        ]);
    }

    private function makeEvent(string $date, string $start, string $end, string $status = 'open', string $category = 'kajian'): KajianEvent
    {
        return KajianEvent::create([
            'academic_year_id' => AcademicYear::active()->id,
            'title' => 'Kajian Email Test',
            'date' => $date,
            'time_start' => $start,
            'time_end' => $end,
            'status' => $status,
            'category' => $category,
        ]);
    }

    private function makeGuardian(ClassRoom $class, string $suffix): ParentModel
    {
        $role = Role::where('name', 'wali_santri')->firstOrFail();
        $user = User::factory()->create([
            'username' => 'email-'.$suffix,
            'email' => 'email-'.$suffix.'@example.test',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
        $parent = ParentModel::create([
            'user_id' => $user->id,
            'type' => 'father',
            'qr_code_string' => 'P-EMAIL-'.$suffix,
        ]);
        $student = Student::create([
            'nis' => 'EMAIL-'.$suffix,
            'name' => 'Santri Email '.$suffix,
            'class_id' => $class->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
        $parent->students()->attach($student->id);

        return $parent;
    }
}
