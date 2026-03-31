<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Kelas</h1>
            <p class="text-gray-500">Kelola data kelas/tingkat</p>
        </div>
        <button wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-500 text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors shadow-lg shadow-primary-500/25">
            <span class="material-symbols-rounded">add</span>
            Tambah Kelas
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
            <span class="material-symbols-rounded">check_circle</span>
            {{ session('message') }}
        </div>
    @endif

    <!-- Search -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="relative max-w-md">
            <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama kelas..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
    </div>

    <!-- Class Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($classes as $class)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-primary-600 text-2xl">meeting_room</span>
                    </div>
                    <span
                        class="px-2 py-1 rounded-lg text-xs font-medium {{ $class->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $class->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $class->name }}</h3>

                <div class="flex items-center gap-4 text-sm text-gray-500 mb-2">
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-rounded text-lg">school</span>
                        <span>{{ $class->students_count }} siswa</span>
                    </div>
                    @if($class->capacity)
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-rounded text-lg">groups</span>
                            <span>Max {{ $class->capacity }}</span>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 rounded-xl p-3 mb-4">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="material-symbols-rounded text-primary-500 text-lg">person</span>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold leading-none mb-1">Wali Kelas</p>
                            <p class="font-semibold text-gray-700 leading-none truncate">
                                {{ $class->teacher->name ?? 'Belum ditentukan' }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button wire:click="openEditModal({{ $class->id }})"
                        class="flex-1 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors flex items-center justify-center gap-1">
                        <span class="material-symbols-rounded text-lg">edit</span>
                        Edit
                    </button>
                    <button wire:click="confirmDelete({{ $class->id }})"
                        class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                        <span class="material-symbols-rounded text-lg">delete</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <span class="material-symbols-rounded text-5xl text-gray-300">meeting_room</span>
                <p class="mt-2 text-gray-500">Belum ada data kelas</p>
            </div>
        @endforelse
    </div>

    @if($classes->hasPages())
        <div class="mt-6">
            {{ $classes->links() }}
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showModal', false)"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">{{ $editMode ? 'Edit Kelas' : 'Tambah Kelas' }}</h3>
                        <button wire:click="$set('showModal', false)" class="p-2 hover:bg-gray-100 rounded-full">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>

                    <form wire:submit="save">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model="name"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="Contoh: Kelas 1A">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Level/Tingkat</label>
                                    <input type="text" wire:model="level"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="Contoh: 1">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas</label>
                                    <input type="number" wire:model="capacity"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="30">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas</label>
                                <select wire:model="teacher_id"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="">Pilih Wali Kelas</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                                @error('teacher_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="is_active"
                                        class="w-4 h-4 rounded text-primary-500 focus:ring-primary-500">
                                    <span class="text-sm text-gray-700">Kelas Aktif</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button type="button" wire:click="$set('showModal', false)"
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors">
                                {{ $editMode ? 'Simpan' : 'Tambah' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50" wire:click="$set('showDeleteModal', false)"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 z-10 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-rounded text-red-600 text-3xl">delete</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Kelas?</h3>
                    <p class="text-gray-500 mb-6">Data kelas akan dihapus permanen.</p>
                    <div class="flex gap-3">
                        <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                        <button wire:click="delete"
                            class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl font-medium hover:bg-red-600">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>