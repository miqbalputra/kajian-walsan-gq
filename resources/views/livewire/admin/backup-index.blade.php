<div class="max-w-5xl" x-data="{ generating: null }">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                <span class="material-symbols-rounded text-white text-2xl">backup</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Backup Data</h1>
                <p class="text-gray-500 text-sm">Unduh salinan lengkap database untuk pemulihan dan pemeriksaan data.</p>
            </div>
        </div>
    </div>

    @if(session('backupError'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
            <span class="material-symbols-rounded text-red-600 mt-0.5">error</span>
            <p class="text-red-700 text-sm font-medium">{{ session('backupError') }}</p>
        </div>
    @endif

    <div class="mb-6 p-5 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3">
        <span class="material-symbols-rounded text-amber-600 mt-0.5">warning</span>
        <div>
            <p class="font-bold text-amber-900">Berkas backup bersifat rahasia</p>
            <p class="text-sm text-amber-800 mt-1">Backup berisi seluruh tabel dan data, termasuk arsip, sesi, hash password, token, serta pengaturan terenkripsi. Simpan hanya di lokasi yang aman.</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="w-11 h-11 bg-slate-100 text-slate-700 rounded-xl flex items-center justify-center mb-4">
                <span class="material-symbols-rounded">database</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Backup SQL penuh</h2>
            <p class="text-sm text-gray-500 mt-2">Untuk pemulihan database. Memuat skema, semua tabel dan data, indeks, trigger, routine, serta event MariaDB.</p>
            <p class="text-xs text-gray-500 mt-3">Gunakan berkas ini saat perlu restore database.</p>
            <a href="{{ route('admin.backups.sql') }}" @click="generating = 'sql'"
                :class="generating === 'sql' ? 'opacity-70 pointer-events-none' : ''"
                class="mt-6 inline-flex items-center justify-center gap-2 w-full px-4 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-black transition-colors">
                <span class="material-symbols-rounded" x-show="generating !== 'sql'">download</span>
                <span class="material-symbols-rounded animate-spin" x-show="generating === 'sql'" x-cloak>progress_activity</span>
                <span x-text="generating === 'sql' ? 'Menyiapkan backup...' : 'Unduh SQL penuh'"></span>
            </a>
        </section>

        <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="w-11 h-11 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center mb-4">
                <span class="material-symbols-rounded">table_view</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Backup XLSX lengkap</h2>
            <p class="text-sm text-gray-500 mt-2">Untuk audit dan pemeriksaan di Excel. Setiap tabel database dibuat sebagai worksheet dengan seluruh kolom dan baris.</p>
            <p class="text-xs text-gray-500 mt-3">Sheet Metadata mencatat tabel, kolom, jumlah baris, dan waktu ekspor.</p>
            <a href="{{ route('admin.backups.xlsx') }}" @click="generating = 'xlsx'"
                :class="generating === 'xlsx' ? 'opacity-70 pointer-events-none' : ''"
                class="mt-6 inline-flex items-center justify-center gap-2 w-full px-4 py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition-colors">
                <span class="material-symbols-rounded" x-show="generating !== 'xlsx'">download</span>
                <span class="material-symbols-rounded animate-spin" x-show="generating === 'xlsx'" x-cloak>progress_activity</span>
                <span x-text="generating === 'xlsx' ? 'Menyiapkan backup...' : 'Unduh XLSX lengkap'"></span>
            </a>
        </section>
    </div>
</div>
