<section class="wc-panel wc-filters-shell" aria-label="Filter data presensi" x-data="{ showFilters: window.matchMedia('(min-width: 761px)').matches }">
    <div class="wc-filter-heading"><div><strong>Filter & pencarian</strong><p class="wc-small wc-muted">{{ $events->pluck('academicYear')->push($activeYear)->filter()->firstWhere('id', (int) $academicYearId)?->name ?? 'Semua tahun ajaran' }} · {{ $events->firstWhere('id', (int) $kajianId)?->date?->translatedFormat('d M Y') ?? 'Semua kajian' }}@if($search !== '') · Pencarian: {{ $search }}@endif</p></div><button type="button" class="wc-link-button" @click="showFilters = !showFilters" :aria-expanded="showFilters" aria-controls="wc-filter-fields" x-text="showFilters ? 'Tutup filter' : 'Ubah filter'">Ubah filter</button></div>
    <div class="wc-filters" id="wc-filter-fields" x-show="showFilters" x-cloak>
    <label>Tahun ajaran
        <select wire:model.live="academicYearId">
            <option value="">Semua tahun ajaran</option>
            @foreach($events->pluck('academicYear')->push($activeYear)->filter()->unique('id')->sortByDesc('name') as $year)
                <option value="{{ $year->id }}">{{ $year->name }}</option>
            @endforeach
        </select>
    </label>
    <label>Kajian
        <select wire:model.live="kajianId">
            <option value="">Semua kajian</option>
            @foreach($events->filter(fn ($event) => $academicYearId === '' || $event->academic_year_id == $academicYearId) as $event)
                <option value="{{ $event->id }}">{{ $event->date->translatedFormat('d M Y') }} · {{ $event->title }}</option>
            @endforeach
        </select>
    </label>
    <label>Status presensi
        <select wire:model.live="status">
            <option value="">Semua status</option><option value="present">Hadir langsung / online</option>
            <option value="hadir_fisik">Hadir langsung</option><option value="hadir_online">Menyimak online</option>
            <option value="izin">Izin</option><option value="alpha">Alfa</option><option value="pending">Menunggu validasi</option>
            <option value="rejected">Ditolak</option><option value="incomplete">Data belum lengkap</option><option value="not_started">Belum final</option>
        </select>
    </label>
    <label class="wc-filters__search">Cari santri atau wali
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Nama santri, NIS, atau nama wali" maxlength="150">
    </label>
    <button type="button" class="wc-link-button" wire:click="resetFilters">Reset filter</button>
    <p wire:loading.delay class="wc-loading" role="status">Memperbarui data…</p>
    </div>
</section>
