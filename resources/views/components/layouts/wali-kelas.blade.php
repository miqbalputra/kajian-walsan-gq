<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#10B981">

    {{-- PWA Meta --}}
    @include('components.pwa-meta')

    <title>{{ $title ?? 'Wali Kelas' }} - Kajian Walsan</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL@24,400,0..1&display=swap" rel="stylesheet">

    <!-- Dark Mode Initializer -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-300" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 shadow-xl transform transition-transform duration-300 lg:translate-x-0 lg:static lg:shadow-none lg:border-r border-gray-200 dark:border-slate-800">
            <!-- Logo -->
            <div
                class="h-16 flex items-center gap-3 px-6 border-b border-gray-100 dark:border-slate-800 transition-colors">
                <div
                    class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center shadow-lg shadow-primary-500/20">
                    <span class="material-symbols-rounded text-white text-xl">school</span>
                </div>
                <div>
                    <h1 class="font-bold text-gray-900 dark:text-white leading-tight">Kajian Walsan</h1>
                    <p class="text-[10px] text-primary-600 dark:text-primary-400 font-bold uppercase tracking-wider">
                        Wali Kelas</p>
                </div>
            </div>

            <!-- Profile Mini -->
            <div class="p-4 mx-4 my-4 bg-gray-50 dark:bg-slate-800/50 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center">
                        <span class="material-symbols-rounded text-primary-600">person</span>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-gray-500 truncate">
                            {{ auth()->user()->managedClass->name ?? 'Belum ada kelas' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-1">
                <a href="{{ route('wali-kelas.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('wali-kelas.dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded transition-colors">dashboard</span>
                    Dashboard
                </a>

                <a href="{{ route('wali-kelas.reports') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('wali-kelas.reports') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded transition-colors">assessment</span>
                    Laporan Presensi
                </a>

                <div class="pt-4 mt-4 border-t border-gray-100 dark:border-slate-800 transition-colors">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <span class="material-symbols-rounded">logout</span>
                            Keluar
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
            <!-- Top Header -->
            <header
                class="h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-gray-100 dark:border-slate-800 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-40">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 text-gray-600 dark:text-gray-400 border border-gray-100 dark:border-slate-800 transition-colors">
                        <span class="material-symbols-rounded">menu</span>
                    </button>
                    <h2 class="font-bold text-gray-900 dark:text-white">{{ $title ?? 'Dashboard' }}</h2>
                </div>

                <div class="flex items-center gap-4">
                    <x-theme-toggle />
                    <div class="flex flex-col items-end">
                        <span
                            class="text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-widest leading-none mb-1">Tahun
                            Ajaran</span>
                        <span
                            class="text-xs font-semibold text-gray-700 dark:text-gray-300 leading-none">2025/2026</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"></div>

    @livewireScripts

    <!-- Toast Notifications -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (data) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: data[0].type || 'success',
                    title: data[0].message
                });
            });
        });
    </script>

    @stack('scripts')

    {{-- PWA Install Banner & Service Worker --}}
    @include('components.pwa-install')
</body>

</html>