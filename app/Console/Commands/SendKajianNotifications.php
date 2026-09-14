<?php

namespace App\Console\Commands;

use App\Jobs\SendKajianNotification;
use App\Models\KajianEvent;
use App\Models\KajianNotificationDispatch;
use App\Services\KajianNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendKajianNotifications extends Command
{
    protected $signature = 'kajian:send-notifications';

    protected $description = 'Jadwalkan notifikasi push otomatis untuk kajian wali santri.';

    public function handle(KajianNotificationService $service): int
    {
        $now = now(config('app.timezone'));
        $events = KajianEvent::query()
            ->where('category', 'kajian')
            ->whereIn('status', ['draft', 'open', 'ongoing'])
            ->whereBetween('date', [
                $now->copy()->toDateString(),
                $now->copy()->addDay()->toDateString(),
            ])
            ->with('targetClasses')
            ->get();

        $scheduled = 0;

        foreach ($events as $event) {
            foreach ($service->dueNotificationTypes($event, $now) as $due) {
                $dispatch = DB::transaction(function () use ($event, $due): ?KajianNotificationDispatch {
                    $existing = KajianNotificationDispatch::query()
                        ->where('kajian_event_id', $event->id)
                        ->where('notification_type', $due['type'])
                        ->lockForUpdate()
                        ->first();

                    if ($existing) {
                        $retryable = $existing->status === KajianNotificationDispatch::STATUS_FAILED
                            && $existing->updated_at?->lte(now()->subMinutes(10));

                        if (! $retryable) {
                            return null;
                        }

                        $existing->update([
                            'status' => KajianNotificationDispatch::STATUS_QUEUED,
                            'scheduled_for' => $due['scheduled_for'],
                            'last_error' => null,
                        ]);

                        return $existing->fresh();
                    }

                    return KajianNotificationDispatch::create([
                        'kajian_event_id' => $event->id,
                        'notification_type' => $due['type'],
                        'scheduled_for' => $due['scheduled_for'],
                        'status' => KajianNotificationDispatch::STATUS_QUEUED,
                    ]);
                });

                if (! $dispatch) {
                    continue;
                }

                SendKajianNotification::dispatch($dispatch->id);
                $scheduled++;
            }
        }

        $this->line("{$scheduled} notifikasi kajian dijadwalkan.");

        return self::SUCCESS;
    }
}
