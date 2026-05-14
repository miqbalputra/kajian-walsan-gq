<div class="h-[calc(100vh-7rem)] flex flex-col">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat AI</h1>
            <p class="text-gray-500 dark:text-gray-400">Tanya AI tentang data presensi, kajian, wali santri, dan bukti upload.</p>
        </div>
        <button type="button" wire:click="clearChat"
            class="px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors inline-flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">mop</span>
            Bersihkan
        </button>
    </div>

    <div class="flex-1 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            @foreach($messages as $index => $message)
                <div wire:key="ai-chat-{{ $index }}" class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-3xl rounded-2xl px-4 py-3 {{ $message['role'] === 'user' ? 'bg-primary-600 text-white' : 'bg-gray-50 dark:bg-slate-800 text-gray-800 dark:text-slate-100 border border-gray-100 dark:border-slate-700' }}">
                        <div class="prose prose-sm max-w-none {{ $message['role'] === 'user' ? 'prose-invert' : 'dark:prose-invert' }}">
                            @php
                                $safeContent = e($message['content']);
                                $linkedContent = preg_replace(
                                    '#(https?://[^\s<]+)#',
                                    '<a href="$1" target="_blank" rel="noopener" class="underline font-semibold">$1</a>',
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
                    AI sedang membaca data...
                </div>
            </div>
        </div>

        <form wire:submit="ask" class="border-t border-gray-100 dark:border-slate-800 p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <textarea wire:model="question" rows="2"
                    class="flex-1 px-4 py-3 bg-gray-50 dark:bg-slate-950 border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-white resize-none"
                    placeholder="Contoh: tampilkan foto catatan kajian terbaru yang masih pending, atau rangkum siapa saja yang izin minggu ini..."></textarea>
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
    </div>
</div>
