<?php

namespace Tests\Feature;

use App\Jobs\SendKajianNotification;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\KajianEvent;
use App\Models\KajianNotificationDispatch;
use App\Models\ParentModel;
use App\Models\PushSubscription;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\KajianNotificationService;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class KajianNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        try {
            Mockery::close();
        } finally {
            parent::tearDown();
        }
    }

    public function test_scheduler_queues_each_due_stage_only_once(): void
    {
        Queue::fake();
        Carbon::setTestNow(Carbon::create(2026, 9, 14, 9, 30, 0, config('app.timezone')));
        $event = $this->makeEvent('2026-09-14', '10:00', '19:00');

        Artisan::call('kajian:send-notifications');
        Artisan::call('kajian:send-notifications');

        $this->assertDatabaseHas('kajian_notification_dispatches', [
            'kajian_event_id' => $event->id,
            'notification_type' => KajianNotificationDispatch::TYPE_BEFORE_START,
            'status' => KajianNotificationDispatch::STATUS_QUEUED,
        ]);
        $this->assertDatabaseCount('kajian_notification_dispatches', 1);
        Queue::assertPushed(SendKajianNotification::class, 1);
    }

    public function test_scheduler_queues_after_end_and_late_warning_at_nine_pm(): void
    {
        Queue::fake();
        Carbon::setTestNow(Carbon::create(2026, 9, 14, 21, 0, 0, config('app.timezone')));
        $event = $this->makeEvent('2026-09-14', '18:00', '19:00');

        Artisan::call('kajian:send-notifications');

        $this->assertDatabaseHas('kajian_notification_dispatches', [
            'kajian_event_id' => $event->id,
            'notification_type' => KajianNotificationDispatch::TYPE_AFTER_END,
        ]);
        $this->assertDatabaseHas('kajian_notification_dispatches', [
            'kajian_event_id' => $event->id,
            'notification_type' => KajianNotificationDispatch::TYPE_LATE,
        ]);
        Queue::assertPushed(SendKajianNotification::class, 2);
    }

    public function test_after_end_messages_are_grouped_by_incomplete_action(): void
    {
        $event = $this->makeEvent(today()->toDateString(), '18:00', '19:00');
        $class = ClassRoom::create(['name' => 'Kelas Push', 'level' => '1', 'is_active' => true]);
        $event->targetClasses()->attach($class->id);

        $physical = $this->makeGuardian($class, 'fisik');
        $online = $this->makeGuardian($class, 'online');
        $izin = $this->makeGuardian($class, 'izin');
        $complete = $this->makeGuardian($class, 'complete');
        $missing = $this->makeGuardian($class, 'missing');
        $outsideClass = ClassRoom::create(['name' => 'Kelas Di Luar Sasaran', 'level' => '2', 'is_active' => true]);
        $outside = $this->makeGuardian($outsideClass, 'outside');

        foreach ([$physical, $online, $izin, $complete, $missing, $outside] as $index => $guardian) {
            PushSubscription::create([
                'user_id' => $guardian->user_id,
                'endpoint' => 'https://push.test/'.$index,
                'p256dh' => 'key-'.$index,
                'auth' => 'auth-'.$index,
            ]);
        }

        Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $physical->id,
            'status' => Attendance::STATUS_HADIR_FISIK,
            'method' => Attendance::METHOD_SCAN_QR,
            'validation_status' => Attendance::VALIDATION_APPROVED,
        ]);
        Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $online->id,
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'method' => Attendance::METHOD_UPLOAD,
            'validation_status' => Attendance::VALIDATION_REJECTED,
            'proof_file' => 'old.jpg',
        ]);
        Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $izin->id,
            'status' => Attendance::STATUS_IZIN,
            'method' => Attendance::METHOD_UPLOAD,
            'validation_status' => Attendance::VALIDATION_PENDING,
            'proof_file' => null,
        ]);
        Attendance::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $complete->id,
            'status' => Attendance::STATUS_HADIR_ONLINE,
            'method' => Attendance::METHOD_UPLOAD,
            'validation_status' => Attendance::VALIDATION_PENDING,
            'proof_file' => 'note.jpg',
        ]);

        $messages = [];
        $webPush = Mockery::mock(WebPushService::class);
        $webPush->shouldReceive('sendToSubscriptions')->times(4)->andReturnUsing(
            function ($subscriptions, string $title, string $body, string $url) use (&$messages): array {
                $messages[] = [
                    'user_ids' => $subscriptions->pluck('user_id')->sort()->values()->all(),
                    'title' => $title,
                    'body' => $body,
                    'url' => $url,
                ];

                return [
                    'sent' => $subscriptions->count(),
                    'failed' => 0,
                    'total' => $subscriptions->count(),
                ];
            }
        );
        $this->app->instance(WebPushService::class, $webPush);

        $dispatch = KajianNotificationDispatch::create([
            'kajian_event_id' => $event->id,
            'notification_type' => KajianNotificationDispatch::TYPE_AFTER_END,
            'scheduled_for' => now(),
        ]);

        $result = app(KajianNotificationService::class)->sendDispatch($dispatch);

        $this->assertSame(4, $result['total']);
        $this->assertCount(4, $messages);
        $this->assertTrue(collect($messages)->contains('body', 'Anda tercatat hadir langsung. Jangan lupa kirim catatan kajian ke wali kelas.'));
        $this->assertTrue(collect($messages)->contains('body', 'Upload catatan kajian Anda.'));
        $this->assertTrue(collect($messages)->contains('body', 'Upload surat pernyataan izin Anda.'));
        $this->assertTrue(collect($messages)->contains('body', 'Presensi belum tercatat. Silakan isi sekarang.'));
        $this->assertFalse(collect($messages)->contains('user_ids', [$complete->user_id]));
    }

    public function test_closed_and_non_kajian_events_are_not_scheduled(): void
    {
        Queue::fake();
        Carbon::setTestNow(Carbon::create(2026, 9, 14, 9, 30, 0, config('app.timezone')));
        $closed = $this->makeEvent('2026-09-14', '10:00', '19:00', 'closed');
        $other = $this->makeEvent('2026-09-14', '10:00', '19:00', 'open', 'rapor');

        Artisan::call('kajian:send-notifications');

        $this->assertDatabaseMissing('kajian_notification_dispatches', ['kajian_event_id' => $closed->id]);
        $this->assertDatabaseMissing('kajian_notification_dispatches', ['kajian_event_id' => $other->id]);
        Queue::assertNothingPushed();
    }

    private function makeEvent(string $date, string $start, string $end, string $status = 'open', string $category = 'kajian'): KajianEvent
    {
        return KajianEvent::create([
            'academic_year_id' => AcademicYear::active()->id,
            'title' => 'Kajian Push Test',
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
            'username' => 'push-'.$suffix,
            'email' => 'push-'.$suffix.'@example.test',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
        $parent = ParentModel::create([
            'user_id' => $user->id,
            'type' => 'father',
            'qr_code_string' => 'P-PUSH-'.$suffix,
        ]);
        $student = Student::create([
            'nis' => 'PUSH-'.$suffix,
            'name' => 'Santri '.$suffix,
            'class_id' => $class->id,
            'is_active' => true,
            'student_status' => 'active',
        ]);
        $parent->students()->attach($student->id);

        return $parent;
    }
}
