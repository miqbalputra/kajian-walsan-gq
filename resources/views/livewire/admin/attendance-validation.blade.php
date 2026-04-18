<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 transition-all">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Validasi Presensi</h1>
            <p class="text-gray-500 dark:text-gray-400">Review dan validasi presensi online/izin wali santri</p>
        </div>
    </div>

    <!-- Filters -->
    <div
        class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-4 mb-6 transition-colors">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative group">
                    <span
                        class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 group-focus-within:text-primary-500 transition-colors">search</span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama orang tua..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white">
                </div>
            </div>
            <div class="sm:w-48">
                <select wire:model.live="statusFilter"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white">
                    <option value="pending">⏳ Pending</option>
                    <option value="approved">✅ Disetujui</option>
                    <option value="rejected">❌ Ditolak</option>
                    <option value="">Semua Status</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div
        class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead
                    class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800 transition-colors">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                            Wali Santri</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                            Kajian</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                            Waktu Kirim</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                            Bukti</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 transition-colors">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $attendance->parent?->user?->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Anak:
                                        {{ $attendance->parent?->students->first()?->name ?? '-' }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900 dark:text-gray-200">
                                    {{ Str::limit($attendance->kajianEvent?->title, 30) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $attendance->kajianEvent?->date?->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-lg text-xs font-medium 
                                                        @if($attendance->status === 'hadir_online') bg-blue-100 text-blue-700
                                                        @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ $attendance->status === 'hadir_online' ? 'Online' : 'Izin' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $attendance->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="viewProof({{ $attendance->id }})"
                                    class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    <span class="material-symbols-rounded text-lg">visibility</span>
                                    Lihat File
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if($attendance->validation_status === 'pending')
                                        <button wire:click="approve({{ $attendance->id }})"
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Setujui">
                                            <span class="material-symbols-rounded">check_circle</span>
                                        </button>
                                        <button wire:click="openRejectModal({{ $attendance->id }})"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Tolak">
                                            <span class="material-symbols-rounded">cancel</span>
                                        </button>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-xs font-medium {{ $attendance->validation_status === 'approved' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $attendance->validation_status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                            </span>
                                            <button wire:click="revertToPending({{ $attendance->id }})"
                                                wire:confirm="Kembalikan status ke Pending? Data validasi sebelumnya akan dihapus."
                                                class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-all"
                                                title="Ubah Status (Kembalikan ke Pending)">
                                                <span class="material-symbols-rounded text-sm">history</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-rounded text-5xl text-gray-300">verified_user</span>
                                <p class="mt-2">Tidak ada pengajuan yang perlu divalidasi</p>
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

    <!-- Proof Modal -->
    @if($showProofModal && $selectedAttendance)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/60 transition-opacity" wire:click="$set('showProofModal', false)"></div>

                <div
                    class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full p-6 z-10 max-h-[90vh] overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Bukti Kesantrian / Izin</h3>
                            <p class="text-sm text-gray-500">{{ $selectedAttendance->parent?->user?->name }} -
                                {{ $selectedAttendance->kajianEvent?->title }}
                            </p>
                        </div>
                        <button wire:click="$set('showProofModal', false)" class="p-2 hover:bg-gray-100 rounded-full">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>

                    <div class="flex-1 overflow-auto bg-gray-50 rounded-xl border border-gray-200 p-2 min-h-[400px]">
                        @php
                            $proofUrl = \App\Services\CloudinaryService::getDisplayUrl($selectedAttendance->proof_file);
                            $extension = pathinfo($selectedAttendance->proof_file, PATHINFO_EXTENSION);
                            // Cloudinary URLs: detect type from URL or extension
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])
                                || str_contains($proofUrl, '/image/upload/');
                            $isPdf = strtolower($extension) === 'pdf'
                                || str_contains($proofUrl, '.pdf');
                        @endphp

                        @if($isImage)
                            <img src="{{ $proofUrl }}" class="max-w-full h-auto mx-auto rounded-lg shadow-sm" alt="Bukti">
                        @elseif($isPdf)
                            <iframe src="{{ $proofUrl }}" class="w-full h-full min-h-[600px] rounded-lg"
                                frameborder="0"></iframe>
                        @else
                            <div class="flex flex-col items-center justify-center h-full gap-4 text-gray-500">
                                <span class="material-symbols-rounded text-6xl">file_present</span>
                                <p>File tidak dapat dipratinjau langsung.</p>
                                <a href="{{ $proofUrl }}" target="_blank"
                                    class="px-4 py-2 bg-primary-500 text-white rounded-xl font-medium">Download File</a>
                            </div>
                        @endif
                    </div>

                    @if($selectedAttendance->notes)
                        <div class="mt-4 p-4 bg-primary-50 rounded-xl border border-primary-100">
                            <p class="text-xs font-bold text-primary-700 uppercase tracking-wider mb-1">Catatan Wali Santri:</p>
                            <p class="text-gray-700">{{ $selectedAttendance->notes }}</p>
                        </div>
                    @endif

                    @if($selectedAttendance->validation_status === 'pending')
                        <div class="mt-6 flex gap-3">
                            <button wire:click="openRejectModal({{ $selectedAttendance->id }})"
                                class="flex-1 px-4 py-3 border border-red-200 text-red-600 rounded-xl font-medium hover:bg-red-50 transition-colors">
                                Tolak Pengajuan
                            </button>
                            <button wire:click="approve({{ $selectedAttendance->id }})"
                                class="flex-1 px-4 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors shadow-lg shadow-green-600/20">
                                Setujui Presensi
                            </button>
                        </div>
                    @else
                        <div class="mt-6">
                            <button wire:click="revertToPending({{ $selectedAttendance->id }})"
                                wire:confirm="Kembalikan status ke Pending? Data validasi sebelumnya akan dihapus."
                                class="w-full px-4 py-3 border border-gray-200 text-gray-600 rounded-xl font-medium hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded">history</span>
                                Ubah Status (Kembalikan ke Pending)
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if($showRejectModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showRejectModal', false)"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 z-10">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Tolak Presensi</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span
                                class="text-red-500">*</span></label>
                        <textarea wire:model="rejectionReason" rows="3"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Contoh: Foto bukti tidak jelas atau tidak sesuai."></textarea>
                        @error('rejectionReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="$set('showRejectModal', false)"
                            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button wire:click="reject"
                            class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors">
                            Konfirmasi Tolak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>