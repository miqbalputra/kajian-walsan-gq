@if($paginator->hasPages())
    <nav class="wc-pager" aria-label="Halaman {{ $paginator->getPageName() === 'attentionPage' ? 'tindak lanjut' : 'data' }}">
        <p>Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}</p>
        <div>
            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" @disabled($paginator->onFirstPage()) wire:loading.attr="disabled" aria-label="Halaman sebelumnya">← Sebelumnya</button>
            <span>{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" @disabled(!$paginator->hasMorePages()) wire:loading.attr="disabled" aria-label="Halaman berikutnya">Berikutnya →</button>
        </div>
    </nav>
@endif
