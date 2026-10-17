<div class="wc-stack" x-data="{ followTrigger: null }"
    @follow-up-opened.window="$nextTick(() => $refs.followResult?.focus())"
    @follow-up-closed.window="$nextTick(() => (followTrigger?.isConnected ? followTrigger : $refs.followTab)?.focus())">
    <a class="wc-back" href="{{ $this->backUrl() }}">← Kembali ke {{ $returnSource === 'reports' ? 'laporan presensi' : 'kelas saya' }}</a>
    <section class="wc-heading">
        <div><p class="wc-eyebrow">{{ $class->name }} · Profil santri</p><h1>{{ $student->name }}</h1><p class="wc-muted">NIS {{ $student->nis }} · Riwayat tahun dan kelas sebelumnya tersedia di profil ini.</p></div>
        <span class="wc-count">{{ $attention->count() }} tindak lanjut aktif</span>
    </section>
    <nav class="wc-tabs" aria-label="Bagian profil santri">
        @foreach(['summary' => 'Ringkasan', 'history' => 'Riwayat presensi', 'followups' => 'Tindak lanjut'] as $tabKey => $tabLabel)
            <button type="button" wire:click="selectTab('{{ $tabKey }}')" @if($tabKey === 'followups') x-ref="followTab" @endif aria-current="{{ $tab === $tabKey ? 'page' : 'false' }}">{{ $tabLabel }}</button>
        @endforeach
    </nav>
    <p class="wc-loading" wire:loading.delay role="status">Memperbarui data…</p>
    @if(session()->has('follow_up_saved'))<p class="wc-success" role="status">{{ session('follow_up_saved') }}</p>@endif

    @if(!in_array($tab, ['history', 'followups'], true))
        <section class="wc-panel">
            <div class="wc-section-heading"><div><p class="wc-eyebrow">Seluruh riwayat kajian yang sudah ditutup</p><h2>Kehadiran keluarga</h2></div></div>
            <div class="wc-profile-metrics">
                <div><strong>{{ $summary['rate'] === null ? '—' : $summary['rate'].'%' }}</strong><span>Kehadiran terverifikasi</span></div>
                <div><strong>{{ $summary['present'] }}</strong><span>Hadir langsung / online</span></div><div><strong>{{ $summary['izin'] }}</strong><span>Izin disetujui</span></div><div><strong>{{ $summary['alpha'] }}</strong><span>Alfa</span></div>
            </div>
            <p class="wc-help">Dihitung per wali per kajian sasaran. Menunggu validasi, ditolak, dan izin tidak dihitung sebagai hadir.</p>
            @if($summary['incomplete'])<p class="wc-notice">{{ $summary['incomplete'] }} riwayat belum lengkap, sehingga tidak dihitung dalam persentase.</p>@endif
        </section>
        <section class="wc-contact-grid" aria-label="Kontak wali santri">
            @foreach(['father' => 'Ayah', 'mother' => 'Ibu'] as $type => $typeLabel)
                <article class="wc-panel"><p class="wc-eyebrow">{{ $typeLabel }}</p>
                    @forelse($student->parents->where('type', $type) as $parent)
                        @php
                            $phone = preg_replace('/\D/', '', $parent->user?->phone ?? '');
                            if (str_starts_with($phone, '0')) $phone = '62'.substr($phone, 1);
                            $parentRows = $rows->where('parent_id', $parent->id)->where('event_state', 'ended');
                            $parentSummary = app(\App\Services\WaliKelasReportService::class)->summary($parentRows);
                        @endphp
                        <h2>{{ $parent->user?->name ?? 'Nama belum tersedia' }}</h2><p class="wc-muted">{{ $parent->user?->phone ?: 'Nomor kontak belum tersedia' }}</p>
                        <div class="wc-contact-summary"><strong>{{ $parentSummary['present'] }} / {{ $parentSummary['total'] }}</strong><span>kajian tercatat hadir</span></div>
                        @if(preg_match('/^\d{8,15}$/', $phone))<a href="https://wa.me/{{ $phone }}" target="_blank" rel="noopener noreferrer" class="wc-button wc-button--secondary">Buka WhatsApp <span aria-hidden="true">↗</span></a>@endif
                    @empty
                        <h2>Belum terdaftar</h2><p class="wc-muted">Hubungi admin untuk melengkapi hubungan wali dan santri.</p>
                    @endforelse
                </article>
            @endforeach
        </section>
        <section class="wc-panel wc-heading"><div><h2>Lanjutkan pendampingan</h2><p class="wc-muted">{{ $attention->count() ? 'Ada '.$attention->count().' kasus yang belum selesai dari seluruh periode.' : 'Tidak ada tindak lanjut tertunda untuk santri ini.' }}</p></div><button type="button" class="wc-button" wire:click="selectTab('followups')">Lihat tindak lanjut →</button></section>
    @elseif($tab === 'history')
        @include('livewire.wali-kelas._filters')
        <section class="wc-panel"><div class="wc-section-heading"><div><p class="wc-eyebrow">Terbaru hingga terlama</p><h2>Perjalanan presensi ayah & ibu</h2></div><span class="wc-count">{{ $historyPage->total() }} kajian</span></div>
            <div class="wc-timeline">
                @forelse($historyPage as $eventRows)
                    @php
                        $first = $eventRows->first();
                    @endphp
                    <article class="wc-timeline-item" wire:key="history-{{ $first['event_id'] }}">
                        <div class="wc-timeline-item__heading"><div><time datetime="{{ $first['date'] }}">{{ \Carbon\Carbon::parse($first['date'])->translatedFormat('d F Y') }}</time><h3>{{ $first['event_title'] }}</h3><p class="wc-muted">{{ $first['academic_year'] }} · {{ $first['class_name'] }}</p></div></div>
                        <div class="wc-history-parents">
                            @foreach(['father' => 'Ayah', 'mother' => 'Ibu'] as $type => $typeLabel)
                                <div><p class="wc-eyebrow">{{ $typeLabel }}</p>
                                    @forelse($eventRows->where('parent_type', $type) as $row)
                                        <strong>{{ $row['parent_name'] }}</strong><x-wali-kelas.status :status="$row['status']" :label="$row['label']" />
                                        <p class="wc-small wc-muted">{{ $row['reason'] }}</p>
                                        <dl class="wc-history-meta"><div><dt>Metode</dt><dd>{{ $row['method'] }}</dd></div><div><dt>Validasi</dt><dd>{{ $row['validation'] }}</dd></div><div><dt>Dicatat</dt><dd>{{ $row['recorded_at'] }}</dd></div></dl>
                                        @if($row['notes'])<p class="wc-presence-note"><strong>Catatan presensi</strong><span>{{ $row['notes'] }}</span></p>@endif
                                    @empty
                                        <span class="wc-muted">Tidak ada catatan sesuai filter atau wali tidak menjadi sasaran.</span>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>
                    </article>
                @empty
                    <div class="wc-empty"><strong>Belum ada riwayat sesuai filter</strong><p>Pilih semua tahun ajaran untuk melihat seluruh perjalanan presensi yang tersedia.</p></div>
                @endforelse
            </div><div class="wc-pagination">{{ $historyPage->links('livewire.wali-kelas._pagination') }}</div>
        </section>
    @else
        @if($followParentId && $followEventId)
            @php
                $editingKey = $studentId.':'.$followParentId.':'.$followEventId;
                $editingRow = $rows->firstWhere('key', $editingKey);
                $editingCase = $cases->get($editingKey);
            @endphp
            <section class="wc-panel wc-editor" aria-labelledby="follow-editor-title">
                <div class="wc-section-heading"><div><p class="wc-eyebrow">Tambah aktivitas pendampingan</p><h2 id="follow-editor-title">{{ $editingRow['parent_name'] ?? $editingCase?->parent?->user?->name ?? 'Wali santri' }}</h2><p class="wc-muted">{{ $editingRow['event_title'] ?? $editingCase?->kajianEvent?->title }}</p></div><button type="button" class="wc-link-button" wire:click="closeFollowUp">Batal</button></div>
                <form wire:submit="saveFollowUp" class="wc-form">
                    @if($errors->any())<div class="wc-error" role="alert">Catatan belum disimpan. Periksa isian yang ditandai di bawah.</div>@endif
                    <div class="wc-form__grid">
                        <label>Tanggal & waktu komunikasi<input type="datetime-local" wire:model="contactedAt" required>@error('contactedAt')<span class="wc-field-error">{{ $message }}</span>@enderror</label>
                        <label>Saluran komunikasi<select wire:model="channel">@foreach($channels as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select>@error('channel')<span class="wc-field-error">{{ $message }}</span>@enderror</label>
                    </div>
                    <label>Hasil komunikasi <span class="wc-muted">(wajib)</span><textarea wire:model="result" x-ref="followResult" rows="4" maxlength="2000" placeholder="Contoh: Ayah sudah dihubungi, berhalangan karena tugas kerja." required aria-invalid="{{ $errors->has('result') ? 'true' : 'false' }}"></textarea>@error('result')<span class="wc-field-error">{{ $message }}</span>@enderror</label>
                    <label>Rencana berikutnya<textarea wire:model="nextAction" rows="2" maxlength="2000" placeholder="Apa yang akan dilakukan setelah komunikasi ini?"></textarea>@error('nextAction')<span class="wc-field-error">{{ $message }}</span>@enderror</label>
                    <div class="wc-form__grid">
                        <label>Status kasus<select wire:model.live="followStatus">@foreach($caseStatuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select>@error('followStatus')<span class="wc-field-error">{{ $message }}</span>@enderror</label>
                        @if($followStatus !== 'completed')<label>Tenggat berikutnya <span class="wc-muted">(opsional)</span><input type="date" wire:model="nextFollowUpAt">@error('nextFollowUpAt')<span class="wc-field-error">{{ $message }}</span>@enderror</label>@endif
                    </div>
                    <div class="wc-actions"><button type="submit" class="wc-button" wire:loading.attr="disabled" wire:target="saveFollowUp"><span wire:loading.remove wire:target="saveFollowUp">Simpan catatan</span><span wire:loading wire:target="saveFollowUp">Menyimpan…</span></button><p class="wc-help">Catatan ditambahkan ke riwayat aktivitas dan tidak mengubah status presensi.</p></div>
                </form>
            </section>
        @endif
        <section class="wc-panel"><div class="wc-section-heading"><div><p class="wc-eyebrow">Semua tahun ajaran</p><h2>Kasus yang belum selesai <span class="wc-count">{{ $attention->count() }}</span></h2></div></div>
            <div class="wc-attention-list">
                @forelse($attention as $item)
                    <article class="wc-attention-item" wire:key="profile-attention-{{ $item['key'] }}"><div><strong>{{ $item['parent_type'] === 'father' ? 'Ayah' : 'Ibu' }} · {{ $item['parent_name'] }}</strong><p>{{ $item['event_title'] }}</p><small>{{ \Carbon\Carbon::parse($item['date'])->translatedFormat('d M Y') }}</small></div><div class="wc-attention-item__action"><x-wali-kelas.status :status="$item['overdue'] ? 'alpha' : $item['follow_up_status']" :label="$item['overdue'] ? 'Lewat tenggat' : $caseStatuses[$item['follow_up_status']]" />@if($item['case']?->next_follow_up_at)<small>Tenggat {{ $item['case']->next_follow_up_at->translatedFormat('d M Y') }}</small>@endif<button type="button" class="wc-button wc-button--small" @click="followTrigger = $el" wire:click="openFollowUp({{ $item['parent_id'] }}, {{ $item['event_id'] }})">Catat tindak lanjut</button></div></article>
                @empty
                    <div class="wc-empty wc-empty--compact"><strong>Semua tindak lanjut sudah tertangani</strong><p>Kasus baru akan muncul bila ada alfa pada kajian sasaran yang sudah ditutup.</p></div>
                @endforelse
            </div>
        </section>
        <section class="wc-panel"><div class="wc-section-heading"><div><p class="wc-eyebrow">Catatan tetap tersimpan saat wali kelas berganti</p><h2>Riwayat aktivitas pendampingan</h2></div></div>
            @forelse($cases->sortByDesc('updated_at') as $case)
                <article class="wc-case-history" wire:key="case-history-{{ $case->id }}">
                    <div class="wc-section-heading"><div><h3>{{ $case->parent?->user?->name ?? 'Wali santri' }}</h3><p class="wc-muted">{{ $case->kajianEvent?->title ?? 'Kajian diarsipkan' }} · {{ $case->kajianEvent?->date?->translatedFormat('d M Y') }}</p></div><x-wali-kelas.status :status="$case->status" :label="$caseStatuses[$case->status]" /></div>
                    @foreach($case->entries as $entry)
                        <div class="wc-activity"><div class="wc-activity__meta"><strong>{{ $entry->author_name }}</strong><span>{{ $entry->contacted_at->translatedFormat('d M Y H:i') }} · {{ $channels[$entry->channel] ?? $entry->channel }}</span><x-wali-kelas.status :status="$entry->status_after" :label="$caseStatuses[$entry->status_after]" /></div><p>{{ $entry->result }}</p>@if($entry->next_action)<p><strong>Rencana berikutnya:</strong> {{ $entry->next_action }}</p>@endif @if($entry->next_follow_up_at)<small>Tenggat {{ $entry->next_follow_up_at->translatedFormat('d M Y') }}</small>@endif</div>
                    @endforeach
                    <button type="button" class="wc-link-button" @click="followTrigger = $el" wire:click="openFollowUp({{ $case->parent_id }}, {{ $case->kajian_event_id }})">Tambah aktivitas / ubah status</button>
                </article>
            @empty
                <div class="wc-empty wc-empty--compact"><strong>Belum ada catatan pendampingan</strong><p>Mulai dengan tombol Catat tindak lanjut pada salah satu kasus di atas.</p></div>
            @endforelse
        </section>
    @endif
</div>
