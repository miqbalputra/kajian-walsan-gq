<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#10B981">

    {{-- PWA Meta --}}
    @include('components.pwa-meta')

    <title>{{ $title ?? 'Panitia' }} - Kajian Walsan</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📋</text></svg>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        rel="stylesheet">

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

<body class="min-h-screen bg-gray-100 dark:bg-slate-950 transition-colors duration-300">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header
            class="bg-gradient-to-r from-primary-500 to-primary-600 dark:from-slate-900 dark:to-slate-950 text-white sticky top-0 z-40 shadow-lg dark:shadow-none transition-all">
            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span
                        class="material-symbols-rounded text-2xl transition-transform group-hover:rotate-12">qr_code_scanner</span>
                    <div>
                        <h1 class="font-bold text-lg">Scanner Presensi</h1>
                        <p class="text-white/80 dark:text-gray-400 text-xs transition-colors">{{ auth()->user()->name }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-theme-toggle />

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-full hover:bg-white/10 transition-colors">
                            <span class="material-symbols-rounded">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    @stack('scripts')

    {{-- PWA Install Banner & Service Worker --}}
    @include('components.pwa-install')
</body>

</html>