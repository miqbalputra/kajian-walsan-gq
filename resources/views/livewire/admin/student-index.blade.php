<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Siswa</h1>
            <p class="text-gray-500">Kelola data siswa/santri</p>
        </div>
        <div class="flex gap-3">
            <button wire:click="$set('showImportModal', true)"
                class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                <span class="material-symbols-rounded">upload_file</span>
                Import
            </button>
            <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-500 text-white rounded-xl font-semibold hover:bg-primary-600 transition-colors shadow-lg shadow-primary-500/25">
                <span class="material-symbols-rounded">add</span>
                Tambah Siswa
            </button>
        </div>
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
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIS..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
            <div class="sm:w-40">
                <select wire:model.live="classFilter"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:w-36">
                <select wire:model.live="perPage"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="10">10 / hal</option>
                    <option value="25">25 / hal</option>
                    <option value="50">50 / hal</option>
                    <option value="100">100 / hal</option>
                    <option value="all">Semua</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NIS
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Gender</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 transition-colors">
                    @forelse($students as $student)
                        <tr class="group hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-all duration-300">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-{{ $student->gender === 'L' ? 'blue-100 dark:bg-blue-900/40' : 'pink-100 dark:bg-pink-900/40' }} rounded-full flex items-center justify-center transition-colors">
                                        <span
                                            class="material-symbols-rounded text-{{ $student->gender === 'L' ? 'blue-600 dark:text-blue-400' : 'pink-600 dark:text-pink-400' }} text-xl">{{ $student->gender === 'L' ? 'boy' : 'girl' }}</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white text-base transition-colors">
                                            {{ $student->name }}
                                        </p>
                                        @if($student->siblings->isNotEmpty())
                                            <div class="flex items-center gap-1 mt-1 transition-colors">
                                                <span class="material-symbols-rounded text-[14px] text-primary-500">group</span>
                                                <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">Saudara:
                                                    {{ $student->siblings->pluck('name')->implode(', ') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400 font-mono text-sm">{{ $student->nis }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">
                                {{ $student->classRoom?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-lg text-xs font-medium {{ $student->gender === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                    {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-lg text-xs font-medium {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEditModal({{ $student->id }})"
                                        class="p-2 text-gray-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                                        <span class="material-symbols-rounded text-xl">edit</span>
                                    </button>
                                    <button wire:click="confirmDelete({{ $student->id }})"
                                        class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <span class="material-symbols-rounded text-xl">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-rounded text-5xl text-gray-300">school</span>
                                <p class="mt-2">Belum ada data siswa</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator && $students->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showModal', false)"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">{{ $editMode ? 'Edit Siswa' : 'Tambah Siswa' }}</h3>
                        <button wire:click="$set('showModal', false)" class="p-2 hover:bg-gray-100 rounded-full">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>

                    <form wire:submit="save">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIS <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model="nis"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="Nomor Induk Siswa">
                                @error('nis') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model="name"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="Nama siswa">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span
                                        class="text-red-500">*</span></label>
                                <select wire:model="class_id"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="">Pilih Kelas</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('class_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span
                                        class="text-red-500">*</span></label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="gender" value="L"
                                            class="w-4 h-4 text-primary-500 focus:ring-primary-500">
                                        <span>Laki-laki</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="gender" value="P"
                                            class="w-4 h-4 text-primary-500 focus:ring-primary-500">
                                        <span>Perempuan</span>
                                    </label>
                                </div>
                                @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                <input type="date" wire:model="birth_date"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>

                            <hr class="border-gray-100 my-2">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Informasi Wali (Opsional)
                            </p>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wali</label>
                                    <input type="text" wire:model="guardian_name"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="Nama wali">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Wali</label>
                                    <input type="text" wire:model="guardian_phone"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hubungan Wali</label>
                                <input type="text" wire:model="guardian_relationship"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="Contoh: Paman, Bibi, kakek">
                            </div>

                            <div>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="is_active"
                                        class="w-4 h-4 rounded text-primary-500 focus:ring-primary-500">
                                    <span class="text-sm text-gray-700">Aktif</span>
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
                                {{ $editMode ? 'Simpan Perubahan' : 'Tambah Siswa' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    @endif

        <!-- Import Modal -->
        @if($showImportModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showImportModal', false)">
                    </div>

                    <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                                    <span class="material-symbols-rounded text-primary-600">upload_file</span>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Import Data Siswa</h3>
                                    <p class="text-sm text-gray-500">Dari file Excel atau CSV</p>
                                </div>
                            </div>
                            <button wire:click="$set('showImportModal', false)" class="p-2 hover:bg-gray-100 rounded-full">
                                <span class="material-symbols-rounded">close</span>
                            </button>
                        </div>

                        <!-- Info Card -->
                        <div
                            class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-xl p-4 mb-5">
                            <h4 class="font-bold text-emerald-800 mb-2 flex items-center gap-2">
                                <span class="material-symbols-rounded text-emerald-600">lightbulb</span>
                                Panduan Import
                            </h4>
                            <ul class="text-sm text-emerald-700 space-y-1.5">
                                <li class="flex items-start gap-2">
                                    <span class="material-symbols-rounded text-base mt-0.5">check_circle</span>
                                    Format yang didukung: <strong>.xlsx</strong>, <strong>.xls</strong>,
                                    <strong>.csv</strong>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="material-symbols-rounded text-base mt-0.5">check_circle</span>
                                    Kolom wajib: <strong>NIS</strong> dan <strong>Nama</strong>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="material-symbols-rounded text-base mt-0.5">check_circle</span>
                                    Data yang sudah ada (NIS sama) akan diperbarui
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="material-symbols-rounded text-base mt-0.5">check_circle</span>
                                    Kelas baru akan dibuat otomatis jika belum ada
                                </li>
                            </ul>
                        </div>

                        <!-- Download Template -->
                        <div class="flex items-center justify-between bg-gray-50 rounded-xl p-4 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                    <span class="material-symbols-rounded text-primary-600">description</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Template Import</p>
                                    <p class="text-xs text-gray-500">Download template Excel untuk memulai</p>
                                </div>
                            </div>
                            <button wire:click="downloadTemplate"
                                class="px-4 py-2 bg-primary-500 text-white rounded-lg font-medium text-sm hover:bg-primary-600 transition-colors flex items-center gap-2">
                                <span class="material-symbols-rounded text-lg">download</span>
                                Download
                            </button>
                        </div>

                        <form wire:submit="import">
                            <!-- Upload Area -->
                            <div class="mb-5">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                                <div x-data="{ dragover: false }" x-on:dragover.prevent="dragover = true"
                                    x-on:dragleave.prevent="dragover = false"
                                    x-on:drop.prevent="dragover = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                                    :class="dragover ? 'border-primary-400 bg-primary-50' : 'border-gray-200 bg-white'"
                                    class="relative border-2 border-dashed rounded-xl p-6 transition-colors cursor-pointer hover:border-primary-300 hover:bg-primary-50/50">
                                    <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv,.txt"
                                        x-ref="fileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                                    <div class="text-center" wire:loading.remove wire:target="importFile">
                                        @if($importFile)
                                            <div
                                                class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <span class="material-symbols-rounded text-green-600 text-2xl">task_alt</span>
                                            </div>
                                            <p class="font-medium text-gray-800">{{ $importFile->getClientOriginalName() }}</p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ number_format($importFile->getSize() / 1024, 1) }} KB • Klik untuk ganti file
                                            </p>
                                        @else
                                            <div
                                                class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <span
                                                    class="material-symbols-rounded text-gray-400 text-2xl">cloud_upload</span>
                                            </div>
                                            <p class="font-medium text-gray-700">Drag & drop file di sini</p>
                                            <p class="text-sm text-gray-500 mt-1">atau <span
                                                    class="text-primary-600 font-medium">klik untuk pilih file</span></p>
                                            <p class="text-xs text-gray-400 mt-2">Maksimal 5MB • Format: .xlsx, .xls, .csv</p>
                                        @endif
                                    </div>

                                    <div class="text-center" wire:loading wire:target="importFile">
                                        <div
                                            class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <span
                                                class="material-symbols-rounded text-primary-600 text-2xl animate-spin">progress_activity</span>
                                        </div>
                                        <p class="font-medium text-gray-700">Mengupload file...</p>
                                    </div>
                                </div>
                                @error('importFile')
                                    <div class="flex items-center gap-2 mt-2 text-red-600">
                                        <span class="material-symbols-rounded text-lg">error</span>
                                        <span class="text-sm">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3">
                                <button type="button" wire:click="$set('showImportModal', false)"
                                    class="flex-1 px-4 py-3 border border-gray-200 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" wire:loading.attr="disabled" wire:target="import"
                                    class="flex-1 px-4 py-3 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="import" class="flex items-center gap-2">
                                        <span class="material-symbols-rounded">upload</span>
                                        Import Sekarang
                                    </span>
                                    <span wire:loading wire:target="import" class="flex items-center gap-2">
                                        <span class="material-symbols-rounded animate-spin">progress_activity</span>
                                        Memproses...
                                    </span>
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
                    <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showDeleteModal', false)">
                    </div>

                    <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 z-10 text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-rounded text-red-600 text-3xl">delete</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Siswa?</h3>
                        <p class="text-gray-500 mb-6">Data siswa akan dihapus permanen dan tidak dapat dikembalikan.</p>

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