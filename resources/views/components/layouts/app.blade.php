@props(['title', 'forceLight' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#10B981">
    <meta name="description" content="Sistem Presensi Kajian Rutin Wali Santri dengan Scan QR Code">

    {{-- PWA Meta --}}
    @include('components.pwa-meta')

    <title>{{ $title ?? 'Beranda' }} - Presensi Kajian Wali Santri</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📋</text></svg>">

    <!-- Preconnect for faster font loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts: Inter (optimized with subset) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"
        media="print" onload="this.media='all'">

    <!-- Material Symbols - Optimized: Only load FILL variant with limited weights -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL@24,400,1&display=swap"
        rel="stylesheet" media="print" onload="this.media='all'">

    <!-- Fallback for browsers with JS disabled -->
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL@24,400,1&display=swap"
            rel="stylesheet">
    </noscript>

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
        const forceLight = {{ $forceLight ? 'true' : 'false' }};
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
    class="min-h-screen bg-gray-50 {{ $forceLight ? '' : 'dark:bg-slate-950' }} font-sans transition-colors duration-300">
    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Page Scripts -->
    @stack('scripts')

    {{-- PWA Install Banner & Service Worker --}}
    @include('components.pwa-install')
</body>

</html>