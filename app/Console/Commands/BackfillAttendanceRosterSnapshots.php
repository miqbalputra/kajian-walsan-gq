<?php

namespace App\Console\Commands;

use App\Models\KajianEvent;
use App\Services\AttendanceFinalizationService;
use Illuminate\Console\Command;

class BackfillAttendanceRosterSnapshots extends Command
{
    /**
     * The command is intentionally preview-first. Historical rosters can only
     * be reconstructed from the family and enrolment records that remain.
     */
    protected $signature = 'attendance:backfill-roster-snapshots
                            {--event=* : ID kegiatan tertentu; dapat diulang beberapa kali}
                            {--apply : Simpan snapshot hasil rekonstruksi}';

    protected $description = 'Pratinjau atau simpan snapshot sasaran untuk presensi lama yang sudah ditutup';

    public function handle(AttendanceFinalizationService $finalization): int
    {
        $eventIds = collect($this->option('event'))
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $events = KajianEvent::query()
            ->with('targetClasses')
            ->where('status', 'closed')
            ->when($eventIds->isNotEmpty(), fn ($query) => $query->whereIn('id', $eventIds))
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->filter(function (KajianEvent $event): bool {
                $snapshots = $event->attendanceRosterSnapshots();

                return ! $snapshots->exists()
                    || $snapshots->whereDoesntHave('students')->exists();
            })
            ->values();

        if ($eventIds->isNotEmpty() && $events->count() !== $eventIds->count()) {
            $this->warn('Sebagian ID dilewati karena tidak ditemukan, belum ditutup, atau snapshot anaknya sudah lengkap.');
        }

        if ($events->isEmpty()) {
            $this->info('Tidak ada presensi lama tertutup yang perlu dibuatkan snapshot.');

            return self::SUCCESS;
        }

        $preview = $events->map(function (KajianEvent $event) use ($finalization): array {
            return [
                'ID' => $event->id,
                'Tanggal' => $event->date?->format('d/m/Y') ?? '-',
                'Kajian' => str($event->title)->limit(42),
                'Sasaran rekonstruksi' => $finalization->legacyParticipantCount($event),
            ];
        });

        $this->table(['ID', 'Tanggal', 'Kajian', 'Sasaran rekonstruksi'], $preview->all());
        $this->newLine();
        $this->warn('Jumlah ini direkonstruksi dari data keluarga dan enrolmen yang masih tersedia; data lama tidak dihapus atau diubah.');

        if (! $this->option('apply')) {
            $this->comment('Pratinjau saja. Jalankan kembali dengan --apply setelah angka sasaran diperiksa.');

            return self::SUCCESS;
        }

        $createdEvents = 0;
        $createdRows = 0;

        foreach ($events as $event) {
            $rows = $finalization->backfillLegacyClosedEvent($event);
            if ($rows > 0) {
                $createdEvents++;
                $createdRows += $rows;
            }
        }

        $this->info("Snapshot berhasil dibuat untuk {$createdEvents} kegiatan ({$createdRows} sasaran). Data presensi lama tetap utuh.");

        return self::SUCCESS;
    }
}
