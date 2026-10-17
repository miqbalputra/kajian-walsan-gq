<div class="wc-stack">
    <section class="wc-heading"><div><p class="wc-eyebrow">{{ $class->name }}</p><h1>Laporan presensi</h1><p class="wc-muted">Hasil presensi dan riwayat santri yang saat ini berada di kelas Anda.</p></div>
        <div class="wc-actions"><button type="button" class="wc-button wc-button--secondary" wire:click="exportExcel" wire:loading.attr="disabled" wire:target="exportExcel">Unduh CSV / Excel</button><button type="button" class="wc-button" wire:click="exportPdf" wire:loading.attr="disabled" wire:target="exportPdf">Unduh PDF</button></div>
    </section>
    @include('livewire.wali-kelas._filters')
    <section class="wc-panel">
        <div class="wc-section-heading"><h2>Riwayat presensi</h2><span class="wc-count">{{ $rowPage->total() }} catatan</span></div>
        <p class="wc-help">Satu baris mewakili seorang wali pada satu kajian untuk santri tersebut. Riwayat kelas sebelumnya tetap ditampilkan dengan nama kelas saat kajian.</p>
        <div class="wc-report-list">
            @forelse($rowPage as $row)
                <article class="wc-report-row" wire:key="report-{{ $row['key'] }}">
                    <div><strong><a href="{{ $this->studentUrl($row['student_id']) }}">{{ $row['student_name'] }}</a></strong><small>NIS {{ $row['nis'] }} · {{ $row['class_name'] }}</small></div>
                    <div><strong>{{ $row['parent_name'] }}</strong><small>{{ $row['parent_type'] === 'father' ? 'Ayah' : 'Ibu' }}</small></div>
                    <div><strong>{{ $row['event_title'] }}</strong><small>{{ \Carbon\Carbon::parse($row['date'])->translatedFormat('d M Y') }} · {{ $row['academic_year'] }}</small></div>
                    <div><x-wali-kelas.status :status="$row['status']" :label="$row['label']" /><small>{{ $row['method'] }} · {{ $row['validation'] }}</small></div>
                </article>
            @empty
                <div class="wc-empty"><strong>Belum ada catatan yang sesuai</strong><p>Coba pilih semua tahun ajaran atau ubah filter. Santri tetap dapat dilihat di halaman Kelas saya.</p></div>
            @endforelse
        </div>
        <div class="wc-pagination">{{ $rowPage->links('livewire.wali-kelas._pagination') }}</div>
    </section>
</div>
