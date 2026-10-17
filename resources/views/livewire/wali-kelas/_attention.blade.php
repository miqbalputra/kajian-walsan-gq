<section class="wc-panel wc-attention" aria-labelledby="attention-title">
    <div class="wc-section-heading"><div><p class="wc-eyebrow">Pendampingan wali santri</p><h2 id="attention-title">Perlu ditindaklanjuti <span class="wc-count">{{ $attention->count() }}</span></h2></div></div>
    <p class="wc-muted wc-help">Mencakup semua tahun ajaran. Satu kali alfa menjadi kasus tersendiri dan tetap di sini sampai ditandai selesai.</p>
    <div class="wc-attention-list">
        @forelse($attentionPage as $item)
            <article class="wc-attention-item" wire:key="attention-{{ $item['key'] }}">
                <div><strong>{{ $item['student_name'] }}</strong><p>{{ $item['parent_type'] === 'father' ? 'Ayah' : 'Ibu' }} · {{ $item['parent_name'] }}</p><small>{{ \Carbon\Carbon::parse($item['date'])->translatedFormat('d M Y') }} · {{ $item['event_title'] }}</small></div>
                <div class="wc-attention-item__action">
                    <x-wali-kelas.status :status="$item['overdue'] ? 'alpha' : $item['follow_up_status']" :label="$item['overdue'] ? 'Lewat tenggat' : \App\Models\GuardianFollowUpCase::STATUSES[$item['follow_up_status']]" />
                    @if($item['case']?->next_follow_up_at)<small>Tenggat {{ $item['case']->next_follow_up_at->translatedFormat('d M Y') }}</small>@endif
                    <a class="wc-button wc-button--small" href="{{ $this->studentUrl($item['student_id']).'&tab=followups' }}">Buka tindak lanjut <span aria-hidden="true">→</span></a>
                </div>
            </article>
        @empty
            <div class="wc-empty wc-empty--compact"><strong>Tidak ada tindak lanjut tertunda</strong><p>Kasus alfa yang belum diselesaikan akan muncul di sini.</p></div>
        @endforelse
    </div>
    <div class="wc-pagination">{{ $attentionPage->links('livewire.wali-kelas._pagination') }}</div>
</section>
