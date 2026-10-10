@props(['title', 'forceLight' => false, 'surface' => 'default'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ $surface === 'public' ? '#166534' : '#10B981' }}">
    <meta name="description" content="{{ $surface === 'public' ? 'Lihat jadwal kajian dan kelola kehadiran wali santri Griya Qur’an Tunas Ilmu.' : 'Sistem Presensi Wali Santri dengan Scan QR Code' }}">

    {{-- PWA Meta --}}
    @include('components.pwa-meta')

    <title>{{ $title ?? 'Beranda' }} - Presensi Wali Santri</title>

    <!-- Favicon -->
    @if ($surface === 'public')
        <link rel="icon" type="image/svg+xml" href="{{ asset('icons/icon.svg') }}">
    @else
        <link rel="icon" type="image/svg+xml"
            href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📋</text></svg>">
    @endif

    <!-- Preconnect for faster font loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL@24,400,1&display=swap" rel="stylesheet">

    <!-- Critical CSS inline for faster first paint -->
    <style>
        /* Prevent FOUT (Flash of Unstyled Text) */
        .material-symbols-rounded {
            font-family: 'Material Symbols Rounded', sans-serif;
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        /* Prevent Alpine.js flash of unstyled content */
        [x-cloak] { display: none !important; }
    </style>

    <!-- Dark Mode Initializer -->
    <script>
        const forceLight = {{ ($forceLight || $surface === 'public') ? 'true' : 'false' }};
        if (!forceLight && (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches))) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles
</head>

<body
    class="min-h-screen {{ $surface === 'public' ? 'public-page' : ($forceLight ? 'bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-700' : 'bg-gray-50 dark:bg-slate-950') }} font-sans transition-colors duration-300">
    <!-- Main Content -->
    @if ($surface === 'public')
        <a href="#main-content" class="public-skip-link">Lewati ke isi utama</a>
        <div class="public-shell">{{ $slot }}</div>
    @else
        <main>{{ $slot }}</main>
    @endif

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Page Scripts -->
    @stack('scripts')
</body>

</html>
