<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Services\AiProviderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Menjalankan AI review presensi secara asynchronous di queue worker.
 *
 * Sebelumnya HermesAgentController memanggil autoReviewAttendance() secara
 * sinkron, yang bisa memblock response API hingga 90 detik (timeout AI
 * provider). Dengan job, response Hermes langsung selesai dan review
 * berjalan di background.
 */
class ReviewAttendanceProof implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(
        public Attendance $attendance,
    ) {
    }

    public function handle(): void
    {
        if (! $this->attendance->proof_file) {
            return;
        }

        try {
            app(AiProviderService::class)->autoReviewAttendance($this->attendance);
        } catch (\Throwable $e) {
            Log::warning('[AI Review Job] Gagal', [
                'attendance_id' => $this->attendance->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[AI Review Job] GAGAL PERMANEN', [
            'attendance_id' => $this->attendance->id,
            'error' => $exception->getMessage(),
        ]);
    }
}