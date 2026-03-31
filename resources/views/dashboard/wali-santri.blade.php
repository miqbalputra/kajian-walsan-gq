<x-layouts.app title="Dashboard Wali Santri">
    <div class="min-h-screen bg-gray-50" x-data="{ showMenu: false }">
        <!-- Header -->
        <header class="bg-gradient-to-r from-primary-500 to-primary-600 text-white sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-rounded text-2xl">mosque</span>
                    <div>
                        <h1 class="font-bold text-lg">Kajian Walsan</h1>
                        <p class="text-white/80 text-xs">
                            {{ auth()->user()->parentProfile?->type_display ?? 'Wali Santri' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="p-2 rounded-full hover:bg-white/10">
                        <span class="material-symbols-rounded">notifications</span>
                    </button>
                    <button @click="showMenu = !showMenu" class="p-2 rounded-full hover:bg-white/10">
                        <span class="material-symbols-rounded">menu</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Profile Card -->
        <div class="max-w-7xl mx-auto px-4 -mt-4 relative z-10">
            <div class="card bg-white">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                        <span class="material-symbols-rounded text-3xl text-primary-600">person</span>
                    </div>
                    <div class="flex-1">
                        <h2 class="font-bold text-gray-900 text-lg">{{ auth()->user()->name }}</h2>
                        <p class="text-gray-500 text-sm">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('qr.login') }}" class="p-3 bg-gray-100 rounded-xl">
                        <span class="material-symbols-rounded text-gray-600">qr_code_2</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 py-6 space-y-6">
            <!-- Quick Actions -->
            <div class="grid grid-cols-2 gap-4">
                <a href="#" class="card card-hover text-center py-6">
                    <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-rounded text-3xl text-primary-600">qr_code_scanner</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Scan Presensi</h3>
                    <p class="text-gray-500 text-xs mt-1">Absen kajian hari ini</p>
                </a>
                <a href="#" class="card card-hover text-center py-6">
                    <div class="w-14 h-14 bg-secondary-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-rounded text-3xl text-secondary-600">history</span>
                    </div>
                    <h3 class="font-semibold text-gray-900">Riwayat</h3>
                    <p class="text-gray-500 text-xs mt-1">Lihat kehadiran</p>
                </a>
            </div>

            <!-- Next Event -->
            <div class="card bg-gradient-to-r from-primary-500 to-primary-600 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-rounded text-3xl">event</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-white/80 text-xs font-medium uppercase tracking-wide">Kajian Berikutnya</p>
                        <p class="text-white font-semibold text-lg">Kajian Rutin Bulanan</p>
                        <p class="text-white/80 text-sm">Hari ini, {{ now()->translatedFormat('d F Y') }} • 08:00 WIB
                        </p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-white/20">
                    <button
                        class="w-full py-3 bg-white/20 rounded-xl font-semibold inline-flex items-center justify-center gap-2 hover:bg-white/30 transition-colors">
                        <span class="material-symbols-rounded">qr_code_scanner</span>
                        Scan untuk Presensi
                    </button>
                </div>
            </div>

            <!-- Children List -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Anak Saya</h3>
                @php
                    $children = auth()->user()->parentProfile?->students ?? collect();
                @endphp
                @forelse($children as $child)
                    <div class="card mb-3">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-accent-100 rounded-full flex items-center justify-center">
                                <span class="material-symbols-rounded text-2xl text-accent-600">school</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $child->name }}</h4>
                                <p class="text-gray-500 text-sm">{{ $child->classRoom?->name ?? 'Belum ada kelas' }} • NIS:
                                    {{ $child->nis }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card text-center py-8">
                        <span class="material-symbols-rounded text-4xl text-gray-300">child_care</span>
                        <p class="text-gray-500 mt-2">Belum ada data anak</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div x-show="showMenu" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 z-50" @click="showMenu = false">
            <div x-show="showMenu" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full" class="absolute right-0 top-0 h-full w-72 bg-white shadow-xl"
                @click.stop>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="font-bold text-gray-900">Menu</h2>
                        <button @click="showMenu = false" class="p-2 hover:bg-gray-100 rounded-full">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>
                    <nav class="space-y-2">
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100">
                            <span class="material-symbols-rounded text-gray-600">person</span>
                            <span class="text-gray-700">Profil Saya</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100">
                            <span class="material-symbols-rounded text-gray-600">qr_code_2</span>
                            <span class="text-gray-700">QR Code Saya</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100">
                            <span class="material-symbols-rounded text-gray-600">settings</span>
                            <span class="text-gray-700">Pengaturan</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 text-red-600">
                                <span class="material-symbols-rounded">logout</span>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>