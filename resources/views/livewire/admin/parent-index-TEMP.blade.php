<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Orang Tua</h1>
            <p class="text-gray-500">Kelola data wali santri dan generate kartu ID</p>
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
                Tambah Orang Tua
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
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama, username, atau email..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
            <div class="sm:w-40">
                <select wire:model.live="typeFilter"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Tipe</option>
                    <option value="father">Ayah</option>
                    <option value="mother">Ibu</option>
                </select>
            </div>
            <div class="sm:w-40">
                <select wire:model.live="classFilter"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua Kelas</option>
                    @foreach($allClasses as $class)
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
            <button wire:click="openBatchPrintModal"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-secondary-500 text-white rounded-xl font-semibold hover:bg-secondary-600 transition-colors shadow-lg shadow-secondary-500/25">
                <span class="material-symbols-rounded">print</span>
                Cetak Kartu
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Orang Tua</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Anak</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">QR
                            Code</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($parents as $parent)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-{{ $parent->type === 'father' ? 'blue' : 'pink' }}-100 rounded-full flex items-center justify-center">
                                        <span
                                            class="material-symbols-rounded text-{{ $parent->type === 'father' ? 'blue' : 'pink' }}-600">{{ $parent->type === 'father' ? 'man' : 'woman' }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $parent->user?->name }}</p>
                                        <p class="text-xs text-primary-600 font-semibold mb-1">@
                                            {{ $parent->user?->username }}
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm text-gray-500">{{ $parent->user?->email }}</p>
                                            @if($phone = $parent->user?->phone)
                                                @php
                                                    $waUrl = 'https://wa.me/' . preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $phone));
                                                @endphp
                                                <a href="{{ $waUrl }}" target="_blank"
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200 transition-colors"
                                                    title="Chat via WhatsApp">
                                                    <span class="material-symbols-rounded text-[14px] mr-1">chat</span> Chat
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="px-2 py-1 rounded-lg text-xs font-medium {{ $parent->type === 'father' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                        {{ $parent->type_display }}
                                    </span>
                                    @if($parent->is_single_parent)
                                        <span
                                            class="px-2 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs font-medium inline-flex items-center gap-1">
                                            <span class="material-symbols-rounded text-[14px]">person</span>
                                            Single Parent
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($parent->students as $student)
                                        <span
                                            class="px-2 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs">{{ $student->name }}
                                            <span class="text-gray-400">({{ $student->classRoom?->name ?? '-' }})</span></span>
                                    @empty
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-600">
                                {{ Str::limit($parent->qr_code_string, 15) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="showCard({{ $parent->id }})"
                                        class="p-2 text-gray-600 hover:text-secondary-600 hover:bg-secondary-50 rounded-lg transition-colors"
                                        title="Generate Kartu">
                                        <span class="material-symbols-rounded text-xl">qr_code_2</span>
                                    </button>
                                    <button wire:click="openEditModal({{ $parent->id }})"
                                        class="p-2 text-gray-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                                        <span class="material-symbols-rounded text-xl">edit</span>
                                    </button>
                                    <button wire:click="confirmDelete({{ $parent->id }})"
                                        class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <span class="material-symbols-rounded text-xl">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-rounded text-5xl text-gray-300">group</span>
                                <p class="mt-2">Belum ada data orang tua</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($parents instanceof \Illuminate\Pagination\LengthAwarePaginator && $parents->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $parents->links() }}
            </div>
        @endif
    </div>
    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showModal', false)"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 z-10 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">{{ $editMode ? 'Edit Orang Tua' : 'Tambah Orang Tua' }}
                        </h3>
                        <button wire:click="$set('showModal', false)" class="p-2 hover:bg-gray-100 rounded-full">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>

                    <form wire:submit="save">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model="name"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Username <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" wire:model="username"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="cth: fulan123">
                                    @error('username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" wire:model="email"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="nama@email.com">
                                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password
                                    {{ $editMode ? '(kosongkan jika tidak diubah)' : '' }}</label>
                                <input type="password" wire:model="password"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="{{ $editMode ? '••••••••' : '' }}">
                                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                                    <input type="text" wire:model="phone"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model="type"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <option value="father">Ayah</option>
                                        <option value="mother">Ibu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" wire:model="is_single_parent"
                                        class="w-5 h-5 rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                                    <div>
                                        <p class="font-semibold text-gray-900">Orang Tua Tunggal (Single Parent)</p>
                                        <p class="text-sm text-gray-500">Centang jika ini adalah rumah tangga orang tua
                                            tunggal.</p>
                                    </div>
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                                <input type="text" wire:model="occupation"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                <textarea wire:model="address" rows="2"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Anak (Pilih siswa)</label>
                                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-xl p-3 space-y-2">
                                    @foreach($allStudents as $student)
                                        <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg">
                                            <input type="checkbox" wire:model="selectedChildren" value="{{ $student->id }}"
                                                class="w-4 h-4 rounded text-primary-500 focus:ring-primary-500">
                                            <span class="text-sm text-gray-700">{{ $student->name }} <span
                                                    class="text-gray-400">({{ $student->classRoom?->name ?? '-' }})</span></span>
                                        </label>
                                    @endforeach
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
                                {{ $editMode ? 'Simpan Perubahan' : 'Tambah' }}
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
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Orang Tua?</h3>
                    <p class="text-gray-500 mb-6">Akun dan data orang tua akan dihapus permanen.</p>

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

    <!-- ID Card Modal - KTP Size -->
    @if($showCardModal && $cardParent)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
                    wire:click="$set('showCardModal', false)"></div>

                <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8 z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900">Kartu Identitas</h3>
                            <p class="text-sm text-gray-500">Ukuran standar KTP Indonesia</p>
                        </div>
                        <button wire:click="$set('showCardModal', false)"
                            class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                            <span class="material-symbols-rounded text-2xl">close</span>
                        </button>
                    </div>

                    <!-- ID Card - KTP Size: 85.6mm x 53.98mm -->
                    <div id="id-card"
                        class="relative p-6 flex items-center justify-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 overflow-hidden">
                        <div id="id-card-element"
                            class="relative overflow-hidden rounded-xl shadow-2xl bg-white border border-gray-200"
                            style="width: 323.4px; height: 204px; min-width: 323.4px; min-height: 204px; font-family: 'Inter', sans-serif;">

                            <!-- Header Bar -->
                            <div
                                class="h-8 bg-gradient-to-r from-emerald-600 to-teal-500 flex items-center px-4 justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-rounded text-white text-base">mosque</span>
                                    <p class="text-white font-black text-[9px] tracking-tight uppercase">Presensi Wali Santri</p>
                                </div>
                                <p class="text-white/80 font-bold text-[7px] uppercase tracking-tighter">ID Wali Santri</p>
                            </div>

                            <!-- Background Pattern Container -->
                            <div class="absolute inset-0 top-8 pointer-events-none opacity-[0.03]"
                                style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M10 0 L20 10 L10 20 L0 10 Z\' fill=\'none\' stroke=\'black\' stroke-width=\'0.5\'/%3E%3C/svg%3E');">
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 flex gap-4 h-[calc(100%-32px)]">
                                <!-- Left: Data Info -->
                                <div class="flex-1 flex flex-col justify-between py-1 min-w-0">
                                    <div class="space-y-0.5">
                                        <p
                                            class="text-[6px] font-bold text-teal-600 uppercase tracking-widest leading-none">
                                            Nama Lengkap</p>
                                        <p class="text-[10px] font-black text-slate-800 leading-tight uppercase truncate">
                                            {{ $cardParent->user?->name }}</p>
                                    </div>

                                    <div class="flex gap-4">
                                        <div>
                                            <p
                                                class="text-[6px] font-bold text-teal-600 uppercase tracking-widest leading-none">
                                                Peran</p>
                                            <p class="text-[8px] font-bold text-slate-700 leading-none">
                                                {{ strtoupper($cardParent->type_display) }}</p>
                                        </div>
                                    </div>

                                    <div class="space-y-0.5 min-h-0 overflow-hidden">
                                        <p
                                            class="text-[6px] font-bold text-teal-600 uppercase tracking-widest leading-none">
                                            Wali Dari / NIS</p>
                                        <div class="space-y-0.5">
                                            @foreach($cardParent->students->take(3) as $student)
                                                <p class="text-[8px] font-medium text-slate-700 truncate leading-none mt-0.5">•
                                                    {{ $student->name }} <span
                                                        class="text-slate-400 text-[6px]">({{ $student->nis }})</span></p>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: QR Code -->
                                <div class="flex-shrink-0 flex flex-col items-center justify-center">
                                    <div
                                        class="w-20 h-20 bg-white border border-teal-100 rounded-lg p-1 shadow-sm flex items-center justify-center overflow-hidden qrcode-svg-container">
                                        {!! $qrCodeSvg !!}
                                    </div>
                                    <p class="text-[5px] font-mono text-slate-400 mt-1.5 tracking-tighter">
                                        {{ $cardParent->qr_code_string }}</p>

                                    <div
                                        class="mt-2 flex items-center gap-1 px-1.5 py-0.5 bg-emerald-50 rounded-full border border-emerald-100">
                                        <span class="material-symbols-rounded text-emerald-600 text-[8px]">verified</span>
                                        <span class="text-[5px] font-bold text-emerald-700 uppercase">Valid</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Accent -->
                            <div
                                class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-600 via-teal-400 to-emerald-600">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button wire:click="$set('showCardModal', false)"
                            class="flex-1 px-4 py-3 border border-gray-200 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            Tutup
                        </button>
                        <button onclick="printSingleCard()"
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl font-semibold hover:from-emerald-700 hover:to-teal-700 transition-all inline-flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/25">
                            <span class="material-symbols-rounded text-xl">print</span>
                            Cetak Kartu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .qrcode-svg-container svg {
                width: 100% !important;
                height: 100% !important;
                display: block;
            }
        </style>





    @endif

    <!-- Import Modal -->
    @if($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showImportModal', false)"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-rounded text-violet-600">upload_file</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Import Data Orang Tua</h3>
                                <p class="text-sm text-gray-500">Dari file Excel atau CSV</p>
                            </div>
                        </div>
                        <button wire:click="$set('showImportModal', false)" class="p-2 hover:bg-gray-100 rounded-full">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>

                    <!-- Info Card -->
                    <div class="bg-gradient-to-br from-violet-50 to-purple-50 border border-violet-100 rounded-xl p-4 mb-5">
                        <h4 class="font-bold text-violet-800 mb-2 flex items-center gap-2">
                            <span class="material-symbols-rounded text-violet-600">lightbulb</span>
                            Panduan Import
                        </h4>
                        <ul class="text-sm text-violet-700 space-y-1.5">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-rounded text-base mt-0.5">check_circle</span>
                                Format: <strong>.xlsx</strong>, <strong>.xls</strong>, <strong>.csv</strong>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-rounded text-base mt-0.5">check_circle</span>
                                Kolom wajib: <strong>Nama</strong>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-rounded text-base mt-0.5">check_circle</span>
                                Username & password akan digenerate otomatis
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-rounded text-base mt-0.5">check_circle</span>
                                Masukkan NIS anak untuk menghubungkan data
                            </li>
                        </ul>
                    </div>

                    <!-- Download Template -->
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl p-4 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                <span class="material-symbols-rounded text-violet-600">description</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Template Import</p>
                                <p class="text-xs text-gray-500">Download template Excel</p>
                            </div>
                        </div>
                        <button wire:click="downloadTemplate"
                            class="px-4 py-2 bg-violet-500 text-white rounded-lg font-medium text-sm hover:bg-violet-600 transition-colors flex items-center gap-2">
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
                                :class="dragover ? 'border-violet-400 bg-violet-50' : 'border-gray-200 bg-white'"
                                class="relative border-2 border-dashed rounded-xl p-6 transition-colors cursor-pointer hover:border-violet-300 hover:bg-violet-50/50">
                                <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv,.txt" x-ref="fileInput"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                                <div class="text-center" wire:loading.remove wire:target="importFile">
                                    @if($importFile)
                                        <div
                                            class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <span class="material-symbols-rounded text-green-600 text-2xl">task_alt</span>
                                        </div>
                                        <p class="font-medium text-gray-800">{{ $importFile->getClientOriginalName() }}</p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ number_format($importFile->getSize() / 1024, 1) }} KB
                                        </p>
                                    @else
                                        <div
                                            class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <span class="material-symbols-rounded text-gray-400 text-2xl">cloud_upload</span>
                                        </div>
                                        <p class="font-medium text-gray-700">Drag & drop file di sini</p>
                                        <p class="text-sm text-gray-500 mt-1">atau <span
                                                class="text-violet-600 font-medium">klik untuk pilih file</span></p>
                                    @endif
                                </div>

                                <div class="text-center" wire:loading wire:target="importFile">
                                    <div
                                        class="w-14 h-14 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <span
                                            class="material-symbols-rounded text-violet-600 text-2xl animate-spin">progress_activity</span>
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
                                class="flex-1 px-4 py-3 bg-violet-500 text-white rounded-xl font-medium hover:bg-violet-600 transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
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

    <!-- Credentials Modal -->
    @if($showCredentialsModal && !empty($importedCredentials))
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showCredentialsModal', false)">
                </div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 z-10 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-rounded text-green-600">key</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Akun Berhasil Dibuat</h3>
                                <p class="text-sm text-gray-500">Simpan kredensial ini untuk diberikan ke wali santri</p>
                            </div>
                        </div>
                        <button wire:click="$set('showCredentialsModal', false)" class="p-2 hover:bg-gray-100 rounded-full">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-yellow-600">warning</span>
                            <div>
                                <p class="font-medium text-yellow-800">Penting!</p>
                                <p class="text-sm text-yellow-700">Password hanya ditampilkan sekali. Pastikan Anda menyalin
                                    atau mencetak daftar ini.</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Username</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Password</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($importedCredentials as $cred)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $cred['name'] }}</td>
                                        <td class="px-4 py-3 font-mono text-primary-600">{{ $cred['username'] }}</td>
                                        <td class="px-4 py-3 font-mono text-gray-600">{{ $cred['password'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button wire:click="$set('showCredentialsModal', false)"
                            class="flex-1 px-4 py-3 border border-gray-200 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Tutup
                        </button>
                        <button onclick="window.print()"
                            class="flex-1 px-4 py-3 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-rounded">print</span>
                            Cetak Daftar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Batch Print Modal -->
    @if($showBatchPrintModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-start justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
                    wire:click="$set('showBatchPrintModal', false)"></div>

                <div
                    class="relative bg-white rounded-3xl shadow-2xl max-w-5xl w-full p-8 z-10 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900">Cetak Kartu Per Kelas</h3>
                            <p class="text-sm text-gray-500">Cetak semua kartu wali santri dalam satu kelas ke kertas F4</p>
                        </div>
                        <button wire:click="$set('showBatchPrintModal', false)"
                            class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                            <span class="material-symbols-rounded text-2xl">close</span>
                        </button>
                    </div>

                    <!-- Class Selection -->
                    <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kelas</label>
                                <select wire:model.live="batchPrintClassId" wire:change="loadParentsByClass"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent text-lg">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($allClasses as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(!empty($batchPrintParents))
                                <div
                                    class="flex items-center gap-2 text-sm text-gray-600 bg-white px-4 py-3 rounded-xl border border-gray-200">
                                    <span class="material-symbols-rounded text-primary-500">badge</span>
                                    <span><strong>{{ count($batchPrintParents) }}</strong> kartu akan dicetak</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(!empty($batchPrintParents))
                        <!-- Print Preview Info -->
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-rounded text-blue-600">info</span>
                                <div class="text-sm text-blue-700">
                                    <p class="font-semibold mb-1">Panduan Cetak:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Ukuran kertas: <strong>F4 (215.9mm x 330.2mm)</strong></li>
                                        <li>Kartu berukuran standar KTP (85.6mm x 53.98mm)</li>
                                        <li>Setiap halaman memuat <strong>10 kartu</strong> (2 kolom x 5 baris)</li>
                                        <li>Gunting sesuai garis potong setelah mencetak</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Grid -->
                        <div class="mb-6">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Preview Kartu
                                ({{ count($batchPrintParents) }} Kartu)</h4>
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[500px] overflow-y-auto p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 shadow-inner">
                                @foreach($batchPrintParents as $parent)
                                    <div class="relative overflow-hidden rounded-xl shadow-lg border border-slate-200 bg-white mx-auto flex flex-col"
                                        style="width: 323.4px; height: 204px; font-family: 'Inter', sans-serif;">
                                        <!-- Card Content Mockup -->
                                        <div
                                            class="h-8 bg-gradient-to-r from-emerald-600 to-teal-500 flex items-center px-3 justify-between">
                                            <div class="flex items-center gap-1.5">
                                                <span class="material-symbols-rounded text-white text-[10px]">mosque</span>
                                                <p class="text-white font-black text-[8px] tracking-tight uppercase">Presensi Wali Santri
                                                </p>
                                            </div>
                                        </div>
                                        <div class="p-3 flex gap-3 flex-1 overflow-hidden">
                                            <div class="flex-1 flex flex-col justify-between py-1 min-w-0">
                                                <div class="space-y-0.5">
                                                    <p class="text-[5px] font-bold text-teal-600 uppercase">Nama Lengkap</p>
                                                    <p
                                                        class="text-[9px] font-black text-slate-800 leading-tight uppercase truncate">
                                                        {{ $parent['name'] }}</p>
                                                </div>
                                                <div class="flex gap-3">
                                                    <div>
                                                        <p class="text-[5px] font-bold text-teal-600 uppercase">Peran</p>
                                                        <p class="text-[7px] font-bold text-slate-700">
                                                            {{ strtoupper($parent['type']) }}</p>
                                                    </div>
                                                </div>
                                                <div class="space-y-0.5">
                                                    <p class="text-[5px] font-bold text-teal-600 uppercase">Wali Dari / NIS</p>
                                                    @foreach(array_slice($parent['children'], 0, 2) as $child)
                                                        <p class="text-[7px] font-medium text-slate-700 truncate">• {{ $child['name'] }}
                                                            <span class="text-[5px] opacity-70">({{ $child['nis'] }})</span></p>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div
                                                class="w-16 h-16 bg-white border border-teal-50 rounded p-0.5 self-center qrcode-svg-container">
                                                {!! $parent['qr_svg'] !!}
                                            </div>
                                        </div>
                                        <div class="h-1 bg-emerald-500"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button wire:click="$set('showBatchPrintModal', false)"
                                class="flex-1 px-4 py-3 border border-gray-200 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button onclick="printBatchCards()"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl font-semibold hover:from-emerald-700 hover:to-teal-700 transition-all inline-flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/25">
                                <span class="material-symbols-rounded text-xl">print</span>
                                Cetak {{ count($batchPrintParents) }} Kartu
                            </button>
                        </div>
                    @else
                        <div class="text-center py-20 bg-emerald-50/30 rounded-3xl border-2 border-dashed border-emerald-100">
                            <div
                                class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <span class="material-symbols-rounded text-4xl text-emerald-500">badge</span>
                            </div>
                            <h4 class="text-xl font-bold text-slate-900">Belum Ada Data</h4>
                            <p class="text-slate-500 max-w-xs mx-auto mt-2">Pilih kelas di atas untuk memuat pratinjau kartu
                                orang tua.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hidden Print Area for Batch Print -->
        @if(!empty($batchPrintParents))
            <div id="batch-print-area" class="hidden print:block">
                @foreach(array_chunk($batchPrintParents, 10) as $pageIndex => $pageCards)
                    <div class="batch-print-page"
                        style="page-break-after: always; width: 215.9mm; height: 330.2mm; padding: 15mm 10mm; box-sizing: border-box;">
                        <div
                            style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10mm 8mm; justify-items: center; align-content: start;">
                            @foreach($pageCards as $card)
                                <div class="batch-card"
                                    style="width: 85.6mm; height: 53.98mm; background-color: #ffffff; border: 0.1mm solid #e5e7eb; border-radius: 2mm; overflow: hidden; position: relative; -webkit-print-color-adjust: exact; print-color-adjust: exact; font-family: 'Inter', sans-serif;">

                                    <!-- Header Bar -->
                                    <div
                                        style="height: 10mm; background: linear-gradient(to right, #059669, #14b8a6); display: flex; align-items: center; padding: 0 4mm; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 2mm;">
                                            <span style="color: white; font-size: 10pt;">⛪</span>
                                            <p
                                                style="color: white; font-weight: 900; font-size: 8pt; margin: 0; text-transform: uppercase; letter-spacing: 0.5mm;">
                                                Presensi Wali Santri</p>
                                        </div>
                                        <p
                                            style="color: rgba(255,255,255,0.8); font-weight: bold; font-size: 6pt; margin: 0; text-transform: uppercase;">
                                            ID Wali Santri</p>
                                    </div>

                                    <!-- Body -->
                                    <div style="padding: 3mm 4mm; display: flex; gap: 4mm; height: 43.98mm;">
                                        <!-- Left Info -->
                                        <div
                                            style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; padding-bottom: 2mm;">
                                            <div>
                                                <p
                                                    style="font-size: 6pt; font-weight: bold; color: #059669; margin: 0; text-transform: uppercase;">
                                                    Nama Lengkap</p>
                                                <p
                                                    style="font-size: 9pt; font-weight: 900; color: #1e293b; margin: 0.5mm 0; text-transform: uppercase; line-height: 1.2;">
                                                    {{ $card['name'] }}</p>
                                            </div>

                                            <div style="display: flex; gap: 4mm;">
                                                <div>
                                                    <p
                                                        style="font-size: 6pt; font-weight: bold; color: #059669; margin: 0; text-transform: uppercase;">
                                                        Peran</p>
                                                    <p
                                                        style="font-size: 7pt; font-weight: bold; color: #334155; margin: 0; text-transform: uppercase;">
                                                        {{ $card['type'] }}</p>
                                                </div>
                                            </div>

                                            <div>
                                                <p
                                                    style="font-size: 6pt; font-weight: bold; color: #059669; margin: 0; text-transform: uppercase;">
                                                    Wali Dari / NIS</p>
                                                <div style="margin-top: 0.5mm;">
                                                    @foreach(array_slice($card['children'], 0, 3) as $child)
                                                        <p style="font-size: 7pt; font-weight: 500; color: #475569; margin: 0.5mm 0;">•
                                                            {{ $child['name'] }} <span
                                                                style="color: #94a3b8; font-size: 6pt;">({{ $child['nis'] }})</span></p>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right QR -->
                                        <div
                                            style="flex-shrink: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                            <div
                                                style="width: 24mm; height: 24mm; border: 0.5mm solid #ccfbf1; border-radius: 1.5mm; padding: 1mm; display: flex; align-items: center; justify-content: center;">
                                                <div style="width: 100%; height: 100%;">
                                                    {!! $card['qr_svg'] !!}
                                                </div>
                                            </div>
                                            <p
                                                style="font-family: monospace; font-size: 5pt; color: #94a3b8; margin-top: 2mm; letter-spacing: -0.2mm;">
                                                {{ $card['qr_code'] }}</p>
                                        </div>
                                    </div>

                                    <!-- Bottom Accent -->
                                    <div
                                        style="position: absolute; bottom: 0; left: 0; right: 0; height: 1mm; background: linear-gradient(to right, #059669, #2dd4bf, #059669);">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    <script>
        function printSingleCard() {
            const cardElement = document.getElementById('id-card-element');
            if (!cardElement) {
                alert('Elemen kartu tidak ditemukan!');
                return;
            }

            const printWindow = window.open('', '_blank');
            if (!printWindow) {
                alert('Pop-up terblokir! Mohon izinkan pop-up untuk mencetak kartu.');
                return;
            }

            const title = document.querySelector('#id-card-element p.text-slate-800')?.innerText || 'Wali Santri';

            printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Cetak Kartu - ${title}</title>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { 
                        font-family: 'Inter', sans-serif; 
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        background: white;
                    }
                    @page { 
                        size: 85.6mm 53.98mm; 
                        margin: 0; 
                    }
                    #id-card-element {
                        width: 85.6mm !important;
                        height: 53.98mm !important;
                        position: relative;
                        overflow: hidden;
                        background: white;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    svg { width: 100% !important; height: 100% !important; display: block; }
                    .material-symbols-rounded { font-family: 'Material Symbols Rounded'; }
                </style>
                <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
                <script src="https://cdn.tailwindcss.com"></script>
                <script>
                    tailwind.config = {
                        theme: {
                            extend: {
                                colors: {
                                    emerald: { 600: '#059669' },
                                    teal: { 500: '#14b8a6' }
                                }
                            }
                        }
                    }
                </script>
            </head>

            <body>
                <div id="id-card-element" class="relative overflow-hidden shadow-none border border-gray-100">
                    \${cardElement.innerHTML}
                </div>
            </body>

            </html>
            `);

    printWindow.document.close();

    // Wait for fonts and Tailwind to load
    setTimeout(() => {
    printWindow.print();
    printWindow.onafterprint = function() {
    printWindow.close();
    };
    }, 800);
    }

    function printBatchCards() {
    const printArea = document.getElementById('batch-print-area');
    if (!printArea) {
    alert('Data cetak tidak tersedia!');
    return;
    }

    const printWindow = window.open('', '_blank');
    if (!printWindow) {
    alert('Pop-up terblokir! Mohon izinkan pop-up untuk mencetak kartu.');
    return;
    }

    printWindow.document.write(`
    <!DOCTYPE html>
    <html>

    <head>
        <title>Cetak Kartu - Kelas</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
            rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
            }

            @page {
                size: 215.9mm 330.2mm;
                margin: 8mm;
            }

            @media print {
                body {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .batch-print-page {
                    page-break-after: always;
                }

                .batch-print-page:last-child {
                    page-break-after: avoid;
                }
            }

            .batch-print-page {
                width: 199.9mm;
                padding: 0;
            }

            .batch-print-page>div {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10mm 8mm;
                justify-items: center;
            }

            svg {
                width: 100% !important;
                height: 100% !important;
                display: block;
            }
        </style>
    </head>

    <body>
        ${printArea.innerHTML}
    </body>

    </html>
    `);

    printWindow.document.close();
    setTimeout(() => {
    printWindow.print();
    printWindow.onafterprint = function() {
    printWindow.close();
    };
    }, 800);
    }
    </script>
</div>
