<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#fafaf7">
    @include('components.pwa-meta')
    <title>{{ $title ?? 'Wali kelas' }} — Presensi Wali Santri</title>
    <script>
        try { document.documentElement.classList.toggle('dark', localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && matchMedia('(prefers-color-scheme: dark)').matches)); } catch (_) {}
    </script>
    @vite(['resources/css/app.css', 'resources/css/wali-fonts.css'])
    @livewireStyles
</head>
<body class="wc-page">
    <a class="wc-skip" href="#wc-content">Langsung ke isi halaman</a>
    <header class="wc-header">
        <div class="wc-shell wc-header__inner">
            <a href="{{ route('wali-kelas.dashboard') }}" class="wc-brand">
                <span class="wc-brand__mark material-symbols-rounded" aria-hidden="true">school</span>
                <span><strong>Ruang wali kelas</strong><small>Presensi Wali Santri</small></span>
            </a>
            <nav class="wc-nav" aria-label="Menu utama">
                @unless(auth()->user()->isAdmin())
                    <a href="{{ route('wali-santri.dashboard') }}">Presensi Saya</a>
                @endunless
                <a href="{{ route('wali-kelas.dashboard') }}" @if(!request()->routeIs('wali-kelas.reports')) aria-current="page" @endif>Kelas saya</a>
                <a href="{{ route('wali-kelas.reports') }}" @if(request()->routeIs('wali-kelas.reports')) aria-current="page" @endif>Laporan presensi</a>
            </nav>
            <div class="wc-header__tools">
                <span class="wc-header__name">{{ auth()->user()->name }}</span>
                <x-theme-toggle />
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="wc-icon-button" aria-label="Keluar dari akun" title="Keluar"><span class="material-symbols-rounded" aria-hidden="true">logout</span></button>
                </form>
            </div>
        </div>
    </header>
    <main id="wc-content" class="wc-shell wc-main" tabindex="-1">{{ $slot }}</main>
    @livewireScripts
    <script data-kajian-pwa-bootstrap>
        if ('serviceWorker' in navigator) navigator.serviceWorker.register('/sw.js').catch(() => {});
    </script>
    @stack('scripts')
</body>
</html>
