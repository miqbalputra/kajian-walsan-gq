<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#10B981">

    @include('components.pwa-meta')

    <title>{{ $title ?? 'Kepala Sekolah' }} - Kajian Walsan</title>

    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>%F0%9F%93%8B</text></svg>">

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL@24,400,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100 dark:bg-slate-950 transition-colors duration-300" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 shadow-xl transform transition-transform duration-300 lg:translate-x-0 lg:static lg:shadow-none lg:border-r border-gray-200 dark:border-slate-800">
            <div class="h-16 flex items-center gap-3 px-6 border-b border-gray-200 dark:border-slate-800">
                <span class="material-symbols-rounded text-primary-600 text-3xl">mosque</span>
                <div>
                    <h1 class="font-bold text-gray-900 dark:text-white">Kajian Walsan</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kepala Sekolah</p>
                </div>
            </div>

            <nav class="p-4 space-y-1">
                <a href="{{ route('kepsek.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('kepsek.dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">dashboard</span>
                    Dashboard
                </a>
                <a href="{{ route('kepsek.teacher-attendance.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors {{ request()->routeIs('kepsek.teacher-attendance.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">co_present</span>
                    Presensi Guru
                </a>

                <div class="pt-4 mt-4 border-t border-gray-200 dark:border-slate-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                            <span class="material-symbols-rounded">logout</span>
                            Keluar
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-h-screen min-w-0">
            <header
                class="h-16 bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-40 transition-colors">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 text-gray-600 dark:text-gray-400">
                    <span class="material-symbols-rounded">menu</span>
                </button>

                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title ?? 'Dashboard' }}</h2>

                <div class="flex items-center gap-4">
                    <x-theme-toggle />
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->role?->display_name }}</p>
                        </div>
                        <div
                            class="w-10 h-10 bg-primary-100 dark:bg-slate-800 rounded-full flex items-center justify-center border border-primary-200 dark:border-slate-700">
                            <span class="material-symbols-rounded text-primary-600 dark:text-primary-400">person</span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 min-w-0 overflow-x-hidden p-4 lg:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    @livewireScripts
    @stack('scripts')
</body>

</html>
