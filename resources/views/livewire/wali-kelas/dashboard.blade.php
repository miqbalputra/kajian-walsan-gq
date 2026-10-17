<div class="wc-stack">
    <section class="wc-heading">
        <div><p class="wc-eyebrow">Kelas saya · {{ auth()->user()->name }}</p><h1>{{ $class->name }}</h1><p class="wc-muted">Kenali kehadiran setiap wali, lalu dampingi yang perlu perhatian.</p></div>
        <div class="wc-actions"><a class="wc-button wc-button--secondary" href="#attention-title">Tindak lanjut ({{ $attention->count() }}) ↓</a><a class="wc-button wc-button--secondary" href="{{ route('wali-kelas.reports') }}">Lihat laporan <span aria-hidden="true">↗</span></a></div>
    </section>
    @include('livewire.wali-kelas._filters')
    <section aria-labelledby="summary-title">
        <div class="wc-section-heading"><div><p class="wc-eyebrow">Ringkasan presensi</p><h2 id="summary-title">{{ $events->firstWhere('id', (int) $kajianId)?->title ?? 'Rekap kajian yang sudah ditutup' }}</h2>
            <p class="wc-muted">{{ $events->firstWhere('id', (int) $kajianId)?->date?->translatedFormat('d F Y') ?? 'Pilih kajian untuk melihat hasil satu pertemuan.' }}
                @if($kajianId && $events->firstWhere('id', (int) $kajianId)?->status !== 'closed') · Presensi belum final @endif
            </p></div><span class="wc-count">{{ $students->count() }} santri aktif</span></div>
        <div class="wc-metrics">
            <div class="wc-metric wc-metric--hero"><span>Kehadiran terverifikasi</span><strong>{{ $summary['rate'] === null ? '—' : $summary['rate'].'%' }}</strong><small>{{ $summary['present'] }} dari {{ $summary['total'] }} wali sasaran{{ $kajianId === '' ? ' × kajian' : '' }}</small></div>
            @foreach([['present', 'Hadir', $summary['present'], 'Langsung + online'], ['izin', 'Izin', $summary['izin'], 'Sudah disetujui'], ['alpha', 'Alfa', $summary['alpha'], 'Setelah kajian ditutup'], ['pending', 'Menunggu validasi', $summary['pending'], 'Belum dihitung hadir'], ['rejected', 'Ditolak', $summary['rejected'], 'Perlu perbaikan bukti']] as [$metricStatus, $metricLabel, $metricCount, $metricHelp])
                <button type="button" class="wc-metric {{ $status === $metricStatus ? 'is-selected' : '' }}" wire:click="selectStatus('{{ $metricStatus }}')" aria-pressed="{{ $status === $metricStatus ? 'true' : 'false' }}"><span>{{ $metricLabel }}</span><strong>{{ $metricCount }}</strong><small>{{ $metricHelp }}</small></button>
            @endforeach
        </div>
        <div class="wc-status-breakdown">
            <button type="button" wire:click="selectStatus('hadir_fisik')" aria-pressed="{{ $status === 'hadir_fisik' ? 'true' : 'false' }}"><strong>{{ $summary['physical'] }}</strong> hadir langsung</button>
            <button type="button" wire:click="selectStatus('hadir_online')" aria-pressed="{{ $status === 'hadir_online' ? 'true' : 'false' }}"><strong>{{ $summary['online'] }}</strong> menyimak online</button>
        </div>
        <p class="wc-help">Ayah dan ibu dihitung terpisah. Wali dengan beberapa anak dalam kelas ini dihitung sekali per kajian. Izin dan proses validasi tidak dihitung hadir.</p>
        @if($summary['incomplete'])<p class="wc-notice">{{ $summary['incomplete'] }} riwayat memiliki data sasaran yang belum lengkap dan tidak dimasukkan ke persentase.</p>@endif
    </section>
    <section class="wc-panel" aria-labelledby="trend-title">
        <div class="wc-section-heading"><div><p class="wc-eyebrow">Enam kajian terakhir yang sudah ditutup</p><h2 id="trend-title">Perjalanan kehadiran kelas</h2></div><span class="wc-muted wc-small">Klik batang untuk melihat kajian</span></div>
        @if($trend->isNotEmpty())
            <div class="wc-trend">
                @foreach($trend as $point)
                    <button type="button" wire:key="trend-{{ $point['event']->id }}" wire:click="selectEvent({{ $point['event']->id }})" class="wc-trend__point {{ $kajianId == $point['event']->id ? 'is-selected' : '' }}" aria-label="{{ $point['event']->title }}, {{ $point['event']->date->translatedFormat('d F Y') }}, kehadiran {{ $point['summary']['rate'] ?? 0 }} persen" aria-pressed="{{ $kajianId == $point['event']->id ? 'true' : 'false' }}">
                        <strong>{{ $point['summary']['rate'] === null ? '—' : $point['summary']['rate'].'%' }}</strong>
                        <span class="wc-trend__track"><span style="height: {{ $point['summary']['rate'] ?? 0 }}%"></span></span><small>{{ $point['event']->date->translatedFormat('d M') }}</small>
                    </button>
                @endforeach
            </div>
        @else
            <div class="wc-empty wc-empty--compact"><strong>Tren akan terlihat setelah kajian ditutup</strong><p>Anda tetap dapat membuka profil santri dan meninjau data yang tersedia.</p></div>
        @endif
    </section>
    <section class="wc-panel" aria-labelledby="roster-title">
        <div class="wc-section-heading"><div><p class="wc-eyebrow">Ayah dan ibu ditampilkan terpisah</p><h2 id="roster-title">Santri & wali kelas ini</h2></div><span class="wc-count">{{ $studentPage->total() }} santri</span></div>
        <div class="wc-roster-heading" aria-hidden="true"><span>Santri</span><span>Ayah</span><span>Ibu</span><span>Riwayat</span></div>
        <div class="wc-roster" role="list">
            @forelse($studentPage as $student)
                <article class="wc-student-row" role="listitem" wire:key="student-{{ $student->id }}">
                    <div class="wc-student-name"><span class="wc-avatar" aria-hidden="true">{{ mb_substr($student->name, 0, 1) }}</span><div><h3>{{ $student->name }}</h3><small>NIS {{ $student->nis }}</small>@if($attention->contains('student_id', $student->id))<span class="wc-attention-dot">Ada tindak lanjut</span>@endif</div></div>
                    @include('livewire.wali-kelas._guardian-cell', ['parentType' => 'father'])
                    @include('livewire.wali-kelas._guardian-cell', ['parentType' => 'mother'])
                    <a class="wc-button wc-button--secondary wc-button--small" href="{{ $this->studentUrl($student->id) }}" aria-label="Buka riwayat {{ $student->name }}">Lihat riwayat <span aria-hidden="true">→</span></a>
                </article>
            @empty
                <div class="wc-empty"><strong>{{ $students->isEmpty() ? 'Belum ada santri aktif di kelas ini' : 'Tidak ada santri yang cocok' }}</strong><p>{{ $students->isEmpty() ? 'Hubungi admin untuk memeriksa penempatan santri.' : 'Ubah nama atau status pencarian, atau reset filter.' }}</p></div>
            @endforelse
        </div>
        <div class="wc-pagination">{{ $studentPage->links('livewire.wali-kelas._pagination') }}</div>
    </section>
    @include('livewire.wali-kelas._attention')
</div>
