<div @class([
    'flex flex-col bg-gray-100 dark:bg-slate-950',
    'fixed inset-0 z-[100] p-4' => $fullscreen,
    'h-[calc(100vh-7rem)]' => ! $fullscreen,
])>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat AI</h1>
            <p class="text-gray-500 dark:text-gray-400">Tanya AI dengan konteks data database yang lebih lengkap dan tersimpan per sesi.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" wire:click="newSession"
                class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-colors inline-flex items-center gap-2">
                <span class="material-symbols-rounded text-lg">add</span>
                Sesi Baru
            </button>
            <button type="button" wire:click="toggleFullscreen"
                class="px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-xl font-bold hover:bg-white dark:hover:bg-slate-800 transition-colors inline-flex items-center gap-2">
                <span class="material-symbols-rounded text-lg">{{ $fullscreen ? 'close_fullscreen' : 'open_in_full' }}</span>
                {{ $fullscreen ? 'Normal' : 'Full Screen' }}
            </button>
        </div>
    </div>

    <div class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-[300px_minmax(0,1fr)] gap-4">
        <aside class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden flex flex-col min-h-[220px]">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-800">
                <p class="font-bold text-gray-900 dark:text-white">Session</p>
                <p class="text-xs text-gray-500 dark:text-slate-400">Klik untuk melanjutkan percakapan lama.</p>
            </div>
            <div class="flex-1 overflow-y-auto p-2 space-y-2">
                @forelse($sessions as $session)
                    <button type="button" wire:click="selectSession({{ $session['id'] }})"
                        class="w-full text-left rounded-xl px-3 py-3 transition-colors border {{ $activeSessionId === $session['id'] ? 'bg-primary-50 border-primary-200 text-primary-700 dark:bg-primary-950/30 dark:border-primary-900 dark:text-primary-300' : 'bg-white dark:bg-slate-900 border-transparent hover:bg-gray-50 dark:hover:bg-slate-800 text-gray-700 dark:text-slate-200' }}">
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-rounded text-lg mt-0.5">forum</span>
                            <div class="min-w-0">
                                <p class="font-bold text-sm truncate">{{ $session['title'] }}</p>
                                <p class="text-[11px] opacity-70 mt-0.5">{{ $session['last_accessed'] ?? '-' }}</p>
                                <p class="text-[11px] opacity-60">{{ $session['messages_count'] }} pesan</p>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        <span class="material-symbols-rounded text-4xl text-gray-300">forum</span>
                        <p class="text-sm mt-2">Belum ada sesi.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden flex flex-col min-h-0">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-black uppercase tracking-widest text-gray-400">Percakapan Aktif</p>
                    <h2 class="font-bold text-gray-900 dark:text-white truncate">{{ $activeSessionTitle }}</h2>
                </div>
                <div class="text-xs text-gray-400 hidden sm:block">Enter kirim, Shift+Enter baris baru</div>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-4">
                @if(count($messages) === 0)
                    <div class="max-w-3xl rounded-2xl px-4 py-3 bg-gray-50 dark:bg-slate-800 text-gray-800 dark:text-slate-100 border border-gray-100 dark:border-slate-700">
                        Assalamu'alaikum. Saya siap bantu membaca data aplikasi secara lebih detail. Coba tanyakan: "tampilkan semua izin yang ditolak beserta link bukti" atau "siapa saja wali yang upload catatan pending hari ini".
                    </div>
                @endif

                @foreach($messages as $index => $message)
                    <div wire:key="ai-chat-{{ $activeSessionId }}-{{ $index }}" class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-4xl rounded-2xl px-4 py-3 {{ $message['role'] === 'user' ? 'bg-primary-600 text-white' : 'bg-gray-50 dark:bg-slate-800 text-gray-800 dark:text-slate-100 border border-gray-100 dark:border-slate-700' }}">
                            <div class="prose prose-sm max-w-none {{ $message['role'] === 'user' ? 'prose-invert' : 'dark:prose-invert' }}">
                                @php
                                    $safeContent = e($message['content']);
                                    $linkedContent = preg_replace(
                                        '#(https?://[^\s<]+)#',
                                        '<a href="$1" target="_blank" rel="noopener" class="underline font-semibold break-all">$1</a>',
                                        $safeContent
                                    );
                                @endphp
                                {!! nl2br($linkedContent) !!}
                            </div>
                        </div>
                    </div>
                @endforeach

                <div wire:loading wire:target="ask" class="flex justify-start">
                    <div class="bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-2xl px-4 py-3 text-gray-500 dark:text-slate-400 inline-flex items-center gap-2">
                        <span class="material-symbols-rounded animate-spin">progress_activity</span>
                        AI sedang membaca data lengkap yang relevan...
                    </div>
                </div>
            </div>

            <form wire:submit="ask" class="border-t border-gray-100 dark:border-slate-800 p-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <textarea wire:model="question" rows="2"
                        x-on:keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $wire.ask($event.target.value); }"
                        class="flex-1 px-4 py-3 bg-gray-50 dark:bg-slate-950 border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-white resize-none"
                        placeholder="Tulis pertanyaan detail tentang presensi, wali, kajian, catatan, surat, atau link bukti..."></textarea>
                    <button type="submit"
                        class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold inline-flex items-center justify-center gap-2 transition-colors disabled:opacity-70"
                        wire:loading.attr="disabled" wire:target="ask">
                        <span class="material-symbols-rounded" wire:loading.remove wire:target="ask">send</span>
                        <span class="material-symbols-rounded animate-spin" wire:loading wire:target="ask">progress_activity</span>
                        Kirim
                    </button>
                </div>
                @error('question')
                    <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                @enderror
            </form>
        </section>
    </div>
</div>
