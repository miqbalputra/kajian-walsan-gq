<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Kajian</h1>
            <p class="text-gray-500">Kelola event kajian dan presensi</p>
        </div>
        <button wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-500 text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors shadow-lg shadow-primary-500/25">
            <span class="material-symbols-rounded">add</span>
            Buat Kajian
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
            <span class="material-symbols-rounded">check_circle</span>
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <span
                        class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul atau pemateri..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
            <div class="sm:w-48">
                <select wire:model.live="statusFilter"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="open">Open</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Kajian Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($kajians as $kajian)
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium 
                                    {{ $kajian->status === 'open' ? 'bg-green-100 text-green-700' : ($kajian->status === 'closed' ? 'bg-gray-100 text-gray-700' : ($kajian->status === 'ongoing' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ ucfirst($kajian->status) }}
                        </span>
                        <div class="flex items-center gap-1">
                            <button wire:click="sendReminder({{ $kajian->id }})" wire:loading.attr="disabled"
                                class="p-1.5 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Kirim Pengingat (Notifikasi Push)">
                                <span wire:loading.remove wire:target="sendReminder({{ $kajian->id }})" class="material-symbols-rounded text-lg">notifications_active</span>
                                <span wire:loading wire:target="sendReminder({{ $kajian->id }})" class="material-symbols-rounded text-lg animate-spin">progress_activity</span>
                            </button>
                            <button wire:click="openEditModal({{ $kajian->id }})"
                                class="p-1.5 text-gray-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                                <span class="material-symbols-rounded text-lg">edit</span>
                            </button>
                            <button wire:click="confirmDelete({{ $kajian->id }})"
                                class="p-1.5 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <span class="material-symbols-rounded text-lg">delete</span>
                            </button>
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $kajian->title }}</h3>

                    <div class="space-y-2 text-sm text-gray-500 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-rounded text-lg">calendar_today</span>
                            <span>{{ $kajian->date->translatedFormat('l, d M Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-rounded text-lg">schedule</span>
                            <span>{{ $kajian->time_range }}</span>
                        </div>
                        @if($kajian->speaker)
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-rounded text-lg">person</span>
                                <span>{{ $kajian->speaker }}</span>
                            </div>
                        @endif
                        @if($kajian->location)
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-rounded text-lg">location_on</span>
                                <span>{{ $kajian->location }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Toggle Status Button -->
                    <button wire:click="toggleStatus({{ $kajian->id }})"
                        class="w-full py-2.5 rounded-xl font-medium text-sm transition-colors flex items-center justify-center gap-2
                                    {{ $kajian->status === 'open' ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                        <span
                            class="material-symbols-rounded text-lg">{{ $kajian->status === 'open' ? 'lock' : 'lock_open' }}</span>
                        {{ $kajian->status === 'open' ? 'Tutup Presensi' : 'Buka Presensi' }}
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <span class="material-symbols-rounded text-5xl text-gray-300">event_busy</span>
                <p class="mt-2 text-gray-500">Belum ada kajian</p>
                <button wire:click="openCreateModal"
                    class="mt-4 px-6 py-2 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors">
                    Buat Kajian Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($kajians->hasPages())
        <div class="mt-6">
            {{ $kajians->links() }}
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showModal', false)"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 z-10 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">{{ $editMode ? 'Edit Kajian' : 'Buat Kajian Baru' }}
                        </h3>
                        <button wire:click="$set('showModal', false)" class="p-2 hover:bg-gray-100 rounded-full">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>

                    <form wire:submit="save">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kajian <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model="title"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="Contoh: Kajian Rutin Bulanan">
                                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea wire:model="description" rows="3"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="Deskripsi singkat kajian"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pemateri</label>
                                    <input type="text" wire:model="speaker"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="Nama ustadz/pemateri">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                                    <input type="text" wire:model="location"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="Contoh: Aula Utama">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span
                                        class="text-red-500">*</span></label>
                                <input type="date" wire:model="date"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai <span
                                            class="text-red-500">*</span></label>
                                    <input type="time" wire:model="time_start"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    @error('time_start') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai <span
                                            class="text-red-500">*</span></label>
                                    <input type="time" wire:model="time_end"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    @error('time_end') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model="academic_year_id"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id') <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select wire:model="status"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <option value="draft">Draft</option>
                                        <option value="open">Open</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button type="button" wire:click="$set('showModal', false)"
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors">
                                {{ $editMode ? 'Simpan Perubahan' : 'Buat Kajian' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showDeleteModal', false)"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 z-10 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-rounded text-red-600 text-3xl">delete</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Kajian?</h3>
                    <p class="text-gray-500 mb-6">Data kajian dan presensi terkait akan dihapus permanen.</p>

                    <div class="flex gap-3">
                        <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button wire:click="delete"
                            class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl font-medium hover:bg-red-600 transition-colors">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>