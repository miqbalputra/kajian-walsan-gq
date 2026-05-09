<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pengumuman Push</h1>
            <p class="text-gray-500 dark:text-gray-400">Kirim notifikasi ke wali santri yang sudah install PWA dan mengizinkan notifikasi.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <form wire:submit="send" class="lg:col-span-1 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 space-y-5">
            <div>
                <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Judul</label>
                <input id="title" type="text" wire:model="title"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-950 border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-white"
                    placeholder="Contoh: Kajian Besok Pagi">
                @error('title')
                    <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="body" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Isi Pesan</label>
                <textarea id="body" wire:model="body" rows="5"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-950 border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-white resize-none"
                    placeholder="Tulis pesan singkat yang akan muncul di HP wali santri."></textarea>
                @error('body')
                    <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="url" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Halaman Saat Diklik</label>
                <input id="url" type="text" wire:model="url"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-950 border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-white"
                    placeholder="/wali-santri/schedule">
                @error('url')
                    <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold flex items-center justify-center gap-2 transition-colors disabled:opacity-70"
                wire:loading.attr="disabled">
                <span class="material-symbols-rounded" wire:loading.remove>campaign</span>
                <span class="material-symbols-rounded animate-spin" wire:loading>progress_activity</span>
                <span wire:loading.remove>Kirim Pengumuman</span>
                <span wire:loading>Mengirim...</span>
            </button>
        </form>

        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800">
                <h2 class="font-bold text-gray-900 dark:text-white">Riwayat Pengumuman</h2>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-slate-800">
                @forelse($announcements as $announcement)
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $announcement->title }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $announcement->body }}</p>
                                <p class="text-xs text-gray-400 mt-3">
                                    {{ $announcement->created_at->translatedFormat('d M Y H:i') }}
                                    @if($announcement->sender)
                                        oleh {{ $announcement->sender->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-sm font-bold text-primary-600">{{ $announcement->sent_count }} terkirim</div>
                                <div class="text-xs text-gray-400">{{ $announcement->failed_count }} gagal</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <span class="material-symbols-rounded text-5xl text-gray-300">notifications_off</span>
                        <p class="mt-2 text-gray-500">Belum ada pengumuman.</p>
                    </div>
                @endforelse
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</div>
