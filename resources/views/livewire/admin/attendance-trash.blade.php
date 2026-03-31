<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sampah Presensi</h1>
            <p class="text-gray-500">Data presensi yang pernah dibatalkan atau dihapus</p>
        </div>
        <a href="{{ route('admin.validation.index') }}"
            class="flex items-center gap-2 text-primary-600 font-medium hover:underline">
            <span class="material-symbols-rounded">arrow_back</span>
            Kembali ke Validasi
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="relative">
            <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama orang tua..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Wali Santri</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Kajian</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status Saat Dihapus</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Waktu Hapus</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $attendance->parent?->user?->name }}</p>
                                <p class="text-xs text-gray-500">Anak:
                                    {{ $attendance->parent?->students->first()?->name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $attendance->kajianEvent?->title }}</p>
                                <p class="text-xs text-gray-500">{{ $attendance->kajianEvent?->date?->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-lg text-xs font-medium 
                                        @if($attendance->status === 'hadir_fisik') bg-green-100 text-green-700
                                        @elseif($attendance->status === 'hadir_online') bg-blue-100 text-blue-700
                                        @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ $attendance->status_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $attendance->deleted_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="restore({{ $attendance->id }})"
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                        title="Kembalikan (Restore)">
                                        <span class="material-symbols-rounded">restore</span>
                                    </button>
                                    <button onclick="confirmForceDelete({{ $attendance->id }})"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus Permanen">
                                        <span class="material-symbols-rounded">delete_forever</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-rounded text-5xl text-gray-300">delete_outline</span>
                                <p class="mt-2">Tidak ada data di tempat sampah</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $attendances->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmForceDelete(id) {
                Swal.fire({
                    title: 'Hapus Permanen?',
                    text: "Data ini tidak akan bisa dikembalikan lagi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.forceDelete(id);
                    }
                })
            }
        </script>
    @endpush
</div>