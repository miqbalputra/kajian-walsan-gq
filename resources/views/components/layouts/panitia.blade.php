<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#FAFAF7">

    {{-- PWA Meta --}}
    @include('components.pwa-meta')

    <title>{{ $title ?? 'Panitia' }} - Presensi Wali Santri</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📋</text></svg>">

    <!-- Dark Mode Initializer -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/css/wali-fonts.css', 'resources/css/panitia.css'])
    @if (request()->routeIs('panitia.scanner'))
        @vite('resources/js/panitia-scanner.js')
    @endif
    @livewireStyles
</head>

<body class="panitia-page">
    <div class="panitia-page__shell">
        <header class="panitia-header">
            <div class="panitia-header__inner">
                <a class="panitia-brand" href="{{ route('panitia.scanner') }}" aria-label="Presensi Wali Santri, halaman pemindai">
                    <span class="panitia-brand__icon"><span class="material-symbols-rounded" aria-hidden="true">qr_code_scanner</span></span>
                    <span class="panitia-brand__copy">
                        <strong>Presensi Wali Santri</strong>
                        <span>{{ auth()->user()->name }}</span>
                    </span>
                </a>
                <div class="panitia-header__tools">
                    <a href="{{ route('panitia.jadwal') }}" class="panitia-header__link" aria-label="Lihat jadwal kajian" title="Jadwal kajian">
                        <span class="material-symbols-rounded" aria-hidden="true">calendar_month</span>
                        <span>Jadwal</span>
                    </a>
                    <x-theme-toggle />
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="panitia-header__logout" aria-label="Keluar dari akun" title="Keluar">
                            <span class="material-symbols-rounded" aria-hidden="true">logout</span>
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
    @stack('scripts')

    {{-- PWA install banner and service worker are injected by middleware. --}}
</body>

</html>
