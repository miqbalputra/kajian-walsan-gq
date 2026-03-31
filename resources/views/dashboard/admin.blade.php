<x-layouts.app title="Dashboard Admin">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white border-b sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-rounded text-2xl text-primary-600">mosque</span>
                    <div>
                        <h1 class="font-bold text-gray-900 text-lg">Kajian Walsan</h1>
                        <p class="text-gray-500 text-xs">{{ auth()->user()->role?->display_name ?? 'Admin' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-full hover:bg-gray-100 text-gray-600">
                            <span class="material-symbols-rounded">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-rounded text-primary-600">groups</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\ParentModel::count() }}</p>
                            <p class="text-gray-500 text-xs">Wali Santri</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-rounded text-secondary-600">school</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Student::count() }}</p>
                            <p class="text-gray-500 text-xs">Santri</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-rounded text-accent-600">event</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\KajianEvent::count() }}</p>
                            <p class="text-gray-500 text-xs">Kajian</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-rounded text-green-600">how_to_reg</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Attendance::count() }}</p>
                            <p class="text-gray-500 text-xs">Presensi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access -->
            <h3 class="font-semibold text-gray-900 mb-3">Menu Cepat</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="#" class="card card-hover text-center py-6">
                    <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-rounded text-3xl text-primary-600">add_circle</span>
                    </div>
                    <h4 class="font-semibold text-gray-900">Buat Kajian</h4>
                </a>
                <a href="#" class="card card-hover text-center py-6">
                    <div class="w-14 h-14 bg-secondary-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-rounded text-3xl text-secondary-600">qr_code_scanner</span>
                    </div>
                    <h4 class="font-semibold text-gray-900">Scan Presensi</h4>
                </a>
                <a href="#" class="card card-hover text-center py-6">
                    <div class="w-14 h-14 bg-accent-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-rounded text-3xl text-accent-600">bar_chart</span>
                    </div>
                    <h4 class="font-semibold text-gray-900">Laporan</h4>
                </a>
                <a href="#" class="card card-hover text-center py-6">
                    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-rounded text-3xl text-purple-600">settings</span>
                    </div>
                    <h4 class="font-semibold text-gray-900">Pengaturan</h4>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>