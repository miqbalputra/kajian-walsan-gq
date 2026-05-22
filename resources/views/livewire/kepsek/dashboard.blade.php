<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Kepala Sekolah</h1>
            <p class="text-gray-500 dark:text-gray-400">Monitoring presensi kajian dan performa guru secara read-only.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('kepsek.teacher-attendance.index') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 transition-colors">
                <span class="material-symbols-rounded">co_present</span>
                Presensi Guru
            </a>
            <a href="{{ route('kepsek.guardian-attendance.index') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-primary-700 border border-primary-100 rounded-xl font-semibold hover:bg-primary-50 transition-colors">
                <span class="material-symbols-rounded">group</span>
                Presensi Wali Santri
            </a>
            <a href="{{ route('kepsek.surveys.index') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-primary-700 border border-primary-100 rounded-xl font-semibold hover:bg-primary-50 transition-colors">
                <span class="material-symbols-rounded">thumbs_up_down</span>
                Hasil Survey
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-slate-800">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalGuru }}</p>
            <p class="text-sm text-gray-500">Total Guru</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-slate-800">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kajianBulanIni }}</p>
            <p class="text-sm text-gray-500">Kajian Bulan Ini</p>
        </div>
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
            <p class="text-2xl font-bold text-amber-700">{{ $pendingUploads }}</p>
            <p class="text-sm text-amber-600">Upload Pending</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 border border-red-100">
            <p class="text-2xl font-bold text-red-700">{{ $summary['alpha'] ?? 0 }}</p>
            <p class="text-sm text-red-600">Alfa Kajian Terakhir</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 mb-6">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">Ringkasan Kajian Terakhir</h2>
                <p class="text-sm text-gray-500">
                    @if($latestKajian)
                        {{ $latestKajian->title }} - {{ $latestKajian->date?->format('d/m/Y') }}
                    @else
                        Belum ada kajian.
                    @endif
                </p>
            </div>
            <span class="material-symbols-rounded text-primary-600">analytics</span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3">
            <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                <p class="text-xl font-bold text-slate-900">{{ $summary['total'] ?? 0 }}</p>
                <p class="text-xs text-slate-500">Total</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-3 border border-emerald-100">
                <p class="text-xl font-bold text-emerald-700">{{ $summary['hadir_fisik'] ?? 0 }}</p>
                <p class="text-xs text-emerald-600">Hadir</p>
            </div>
            <div class="rounded-xl bg-blue-50 p-3 border border-blue-100">
                <p class="text-xl font-bold text-blue-700">{{ $summary['hadir_online'] ?? 0 }}</p>
                <p class="text-xs text-blue-600">Online</p>
            </div>
            <div class="rounded-xl bg-yellow-50 p-3 border border-yellow-100">
                <p class="text-xl font-bold text-yellow-700">{{ $summary['izin'] ?? 0 }}</p>
                <p class="text-xs text-yellow-600">Izin</p>
            </div>
            <div class="rounded-xl bg-amber-50 p-3 border border-amber-100">
                <p class="text-xl font-bold text-amber-700">{{ $summary['pending'] ?? 0 }}</p>
                <p class="text-xs text-amber-600">Pending</p>
            </div>
            <div class="rounded-xl bg-rose-50 p-3 border border-rose-100">
                <p class="text-xl font-bold text-rose-700">{{ $summary['rejected'] ?? 0 }}</p>
                <p class="text-xs text-rose-600">Ditolak</p>
            </div>
            <div class="rounded-xl bg-red-50 p-3 border border-red-100">
                <p class="text-xl font-bold text-red-700">{{ $summary['alpha'] ?? 0 }}</p>
                <p class="text-xs text-red-600">Alfa</p>
            </div>
            <div class="rounded-xl bg-primary-50 p-3 border border-primary-100">
                <p class="text-xl font-bold text-primary-700">{{ $summary['uploaded'] ?? 0 }}</p>
                <p class="text-xs text-primary-600">File Upload</p>
            </div>
        </div>
    </div>

    <div class="bg-primary-50 border border-primary-100 rounded-2xl p-5">
        <div class="flex items-start gap-3">
            <span class="material-symbols-rounded text-primary-700 mt-0.5">visibility</span>
            <div>
                <h3 class="font-bold text-primary-900">Akses Kepala Sekolah Bersifat Pantau</h3>
                <p class="text-sm text-primary-800 mt-1">
                    Kepala Sekolah dapat melihat rekap, detail presensi guru, dan file yang diupload. Tidak ada tombol tambah, edit, hapus, atau validasi data.
                </p>
            </div>
        </div>
    </div>
</div>
