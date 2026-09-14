<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\KajianEvent;
use App\Models\KajianNotificationDispatch;
use App\Models\ParentModel;
use App\Models\PushSubscription;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class KajianNotificationService
{
    public function __construct(
        private WebPushService $webPush,
        private N8nKajianEmailReminderService $emailReminder,
    ) {}

    /**
     * Return the scheduled notification stages that are due for an event.
     * All calculations intentionally use the application timezone (WIB).
     *
     * @return list<array{type: string, scheduled_for: Carbon}>
     */
    public function dueNotificationTypes(KajianEvent $event, ?Carbon $now = null): array
    {
        $now ??= now(config('app.timezone'));
        $start = $this->eventDateTime($event, $event->time_start, $now);
        $end = $this->eventDateTime($event, $event->time_end, $now);
        $due = [];

        if (in_array($event->status, ['draft', 'open', 'ongoing'], true)
            && $now->greaterThanOrEqualTo($start->copy()->subMinutes(30))
            && $now->lessThan($start)) {
            $due[] = [
                'type' => KajianNotificationDispatch::TYPE_BEFORE_START,
                'scheduled_for' => $start->copy()->subMinutes(30),
            ];
        }

        if (in_array($event->status, ['open', 'ongoing'], true)
            && $now->greaterThanOrEqualTo($end)) {
            $due[] = [
                'type' => KajianNotificationDispatch::TYPE_AFTER_END,
                'scheduled_for' => $end,
            ];
        }

        $twentyOne = $end->copy()->setTime(21, 0);
        $lateAt = $end->greaterThan($twentyOne) ? $end : $twentyOne;

        if (in_array($event->status, ['open', 'ongoing'], true)
            && $now->greaterThanOrEqualTo($lateAt)
            && $now->toDateString() === $event->date->toDateString()) {
            $due[] = [
                'type' => KajianNotificationDispatch::TYPE_LATE,
                'scheduled_for' => $lateAt,
            ];
        }

        $emailReminderAt = $start->copy()->setTime(20, 30);
        if (in_array($event->status, ['draft', 'open', 'ongoing'], true)
            && $now->toDateString() === $event->date->toDateString()
            && $now->greaterThanOrEqualTo($emailReminderAt)) {
            $due[] = [
                'type' => KajianNotificationDispatch::TYPE_EMAIL_MISSING_ATTENDANCE,
                'scheduled_for' => $emailReminderAt,
            ];
        }

        return $due;
    }

    /**
     * Send an admin-triggered reminder using the same copy as the scheduled
     * before-start notification, without consuming the scheduled dispatch log.
     */
    public function sendManualReminder(KajianEvent $event): array
    {
        $message = $this->message($event);

        return $this->webPush->sendToWaliForEvent(
            $event,
            $message['title'],
            $message['body'],
            $message['url']
        );
    }

    /**
     * @return array{status: string, total: int, sent: int, failed: int, error: ?string}
     */
    public function sendDispatch(KajianNotificationDispatch $dispatch): array
    {
        $event = $dispatch->kajianEvent()->with('targetClasses')->first();

        if (! $event || $event->category !== 'kajian' || $event->status === 'closed') {
            return [
                'status' => KajianNotificationDispatch::STATUS_SKIPPED,
                'total' => 0,
                'sent' => 0,
                'failed' => 0,
                'error' => null,
            ];
        }

        if ($dispatch->notification_type === KajianNotificationDispatch::TYPE_EMAIL_MISSING_ATTENDANCE) {
            return $this->emailReminder->send($dispatch, $event);
        }

        $parents = $this->targetParents($event);
        if ($parents->isEmpty()) {
            return [
                'status' => KajianNotificationDispatch::STATUS_SENT,
                'total' => 0,
                'sent' => 0,
                'failed' => 0,
                'error' => null,
            ];
        }

        $attendanceByParent = Attendance::query()
            ->where('kajian_event_id', $event->id)
            ->whereIn('parent_id', $parents->pluck('id'))
            ->get()
            ->keyBy('parent_id');

        $groups = $this->recipientGroups($dispatch->notification_type, $parents, $attendanceByParent);
        $total = 0;
        $sent = 0;
        $failed = 0;

        foreach ($groups as $action => $groupParents) {
            if ($groupParents->isEmpty()) {
                continue;
            }

            $subscriptions = PushSubscription::query()
                ->whereIn('user_id', $groupParents->pluck('user_id')->filter()->unique())
                ->get();

            if ($subscriptions->isEmpty()) {
                continue;
            }

            $message = $this->messageForAction($dispatch->notification_type, $action, $event);
            $result = $this->webPush->sendToSubscriptions(
                $subscriptions,
                $message['title'],
                $message['body'],
                $message['url']
            );

            $total += $result['total'];
            $sent += $result['sent'];
            $failed += $result['failed'];
        }

        return [
            'status' => $total > 0 && $sent === 0 && $failed > 0
                ? KajianNotificationDispatch::STATUS_FAILED
                : KajianNotificationDispatch::STATUS_SENT,
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'error' => $total > 0 && $sent === 0 && $failed > 0
                ? 'Tidak ada subscription yang berhasil menerima notifikasi.'
                : null,
        ];
    }

    /**
     * @return Collection<int, ParentModel>
     */
    private function targetParents(KajianEvent $event): Collection
    {
        return ParentModel::query()
            ->guardians()
            ->targetedByEvent($event)
            ->whereHas('user', function ($query): void {
                $query->where('is_active', true)
                    ->whereHas('role', fn ($role) => $role->where('name', 'wali_santri'));
            })
            ->with('user')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  Collection<int, ParentModel>  $parents
     * @param  Collection<int, Attendance>  $attendanceByParent
     * @return array<string, Collection<int, ParentModel>>
     */
    private function recipientGroups(string $type, Collection $parents, Collection $attendanceByParent): array
    {
        if ($type === KajianNotificationDispatch::TYPE_BEFORE_START) {
            return ['choose' => $parents];
        }

        $groups = [
            'choose' => collect(),
            'physical_note' => collect(),
            'online' => collect(),
            'izin' => collect(),
        ];

        foreach ($parents as $parent) {
            $attendance = $attendanceByParent->get($parent->id);

            if ($type === KajianNotificationDispatch::TYPE_LATE) {
                if (! $attendance || $attendance->status === Attendance::STATUS_ALPHA) {
                    $groups['choose']->push($parent);
                }

                continue;
            }

            if (! $attendance || $attendance->status === Attendance::STATUS_ALPHA) {
                $groups['choose']->push($parent);
            } elseif ($attendance->status === Attendance::STATUS_HADIR_FISIK) {
                $groups['physical_note']->push($parent);
            } elseif ($attendance->status === Attendance::STATUS_HADIR_ONLINE
                && (! $attendance->proof_file || $attendance->validation_status === Attendance::VALIDATION_REJECTED)) {
                $groups['online']->push($parent);
            } elseif ($attendance->status === Attendance::STATUS_IZIN
                && (! $attendance->proof_file || $attendance->validation_status === Attendance::VALIDATION_REJECTED)) {
                $groups['izin']->push($parent);
            }
        }

        return $groups;
    }

    /**
     * @return array{title: string, body: string, url: string}
     */
    private function messageForAction(string $type, string $action, KajianEvent $event): array
    {
        if ($type === KajianNotificationDispatch::TYPE_BEFORE_START) {
            return $this->message($event);
        }

        $baseUrl = '/wali-santri?kajian='.$event->id.'&action=';

        return match ($action) {
            'physical_note' => [
                'title' => 'Pengingat Catatan Kajian',
                'body' => 'Anda tercatat hadir langsung. Jangan lupa kirim catatan kajian ke wali kelas.',
                'url' => $baseUrl.'physical-note',
            ],
            'online' => [
                'title' => 'Presensi Belum Lengkap',
                'body' => 'Upload catatan kajian Anda.',
                'url' => $baseUrl.'online',
            ],
            'izin' => [
                'title' => 'Presensi Belum Lengkap',
                'body' => 'Upload surat pernyataan izin Anda.',
                'url' => $baseUrl.'izin',
            ],
            default => [
                'title' => 'Pengingat Presensi Kajian',
                'body' => $type === KajianNotificationDispatch::TYPE_LATE
                    ? 'Anda belum presensi. Segera isi agar tidak dihitung Alfa.'
                    : 'Presensi belum tercatat. Silakan isi sekarang.',
                'url' => $baseUrl.'choose',
            ],
        };
    }

    /**
     * @return array{title: string, body: string, url: string}
     */
    private function message(KajianEvent $event): array
    {
        $time = Carbon::parse($event->time_start)->format('H:i');

        return [
            'title' => 'Kajian Akan Segera Dimulai',
            'body' => "Kajian akan dimulai pukul {$time}. Jangan lupa presensi.",
            'url' => '/wali-santri?kajian='.$event->id.'&action=choose',
        ];
    }

    private function eventDateTime(KajianEvent $event, mixed $time, Carbon $now): Carbon
    {
        return Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $event->date->format('Y-m-d').' '.Carbon::parse($time)->format('H:i:s'),
            $now->getTimezone()
        );
    }
}
