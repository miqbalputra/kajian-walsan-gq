<x-layouts.app title="Dashboard Kepala Sekolah">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-gradient-to-r from-secondary-500 to-secondary-600 text-white sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-rounded text-2xl">mosque</span>
                    <div>
                        <h1 class="font-bold text-lg">Kajian Walsan</h1>
                        <p class="text-white/80 text-xs">Kepala Sekolah</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 rounded-full hover:bg-white/10">
                        <span class="material-symbols-rounded">logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="card mb-6">
                <h2 class="font-bold text-gray-900 text-lg mb-4">Selamat Datang, {{ auth()->user()->name }}</h2>
                <p class="text-gray-500">Dashboard monitoring kehadiran kajian wali santri.</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="card bg-gradient-to-br from-primary-500 to-primary-600 text-white">
                    <p class="text-white/80 text-sm">Total Kehadiran Bulan Ini</p>
                    <p class="text-3xl font-bold">
                        {{ \App\Models\Attendance::whereMonth('created_at', now()->month)->count() }}</p>
                </div>
                <div class="card bg-gradient-to-br from-secondary-500 to-secondary-600 text-white">
                    <p class="text-white/80 text-sm">Kajian Bulan Ini</p>
                    <p class="text-3xl font-bold">
                        {{ \App\Models\KajianEvent::whereMonth('date', now()->month)->count() }}</p>
                </div>
            </div>

            <a href="#" class="card card-hover block">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-accent-100 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-3xl text-accent-600">bar_chart</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">Lihat Laporan Lengkap</h3>
                        <p class="text-gray-500 text-sm">Statistik kehadiran per kelas</p>
                    </div>
                    <span class="material-symbols-rounded text-gray-400">arrow_forward</span>
                </div>
            </a>
        </div>
    </div>
</x-layouts.app>