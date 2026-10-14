@props(['title', 'forceLight' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="webpush-vapid-public-key" content="{{ config('webpush.vapid_public_key') }}">
    <meta name="pwa-push-script-url" content="{{ asset('js/pwa-push.js') }}?v=20260519-push-v4">
    <meta name="theme-color" content="#166534">

    {{-- PWA Meta --}}
    @include('components.pwa-meta')

    <style>[x-cloak]{display:none!important}</style>
    <title>{{ $title ?? (request()->routeIs('wali-santri.schedule') ? 'Jadwal Kajian' : (request()->routeIs('wali-santri.profile') ? 'Profil Saya' : 'Beranda')) }} - Presensi Wali Santri</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🕌</text></svg>">

    {{-- Local fonts remove the extra DNS/TLS connections on the first visit. --}}
    <link rel="preload" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/fonts/inter-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/fonts/wali-symbols.woff2') }}" as="font" type="font/woff2" crossorigin>

    <!-- Dark Mode Initializer -->
    <script>
        const forceLight = {{ $forceLight ? 'true' : 'false' }};
        if (!forceLight && (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches))) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/css/wali-fonts.css', 'resources/js/wali.js'])
    @include('components.pwa-push')
    @livewireStyles
</head>

<body class="wali-page font-sans">
    <a href="#wali-main" class="wali-skip">Lewati ke isi utama</a>
    <x-wali.header />
    <main id="wali-main" class="wali-main wali-container" tabindex="-1">{{ $slot }}</main>
    <x-wali.install />
    <x-wali.navigation />
    <form id="wali-logout-form" method="POST" action="{{ route('logout') }}" hidden>@csrf</form>
    @livewireScripts
    @stack('scripts')
    @if(auth()->check() && auth()->user()->hasRole('wali_santri') && (\Illuminate\Support\Facades\Hash::check(auth()->user()->username, auth()->user()->password) || \Illuminate\Support\Facades\Hash::check('PasswordSementara123', auth()->user()->password)) && !request()->routeIs('wali-santri.profile'))
        <x-wali.dialog id="wali-password-warning" title="Ganti kata sandi bawaan">
            <p class="wali-dialog-description">Untuk menjaga keamanan akun Bapak/Ibu, ganti kata sandi bawaan melalui halaman Profil.</p>
            <div class="wali-dialog-actions"><button type="button" class="wali-button wali-button-secondary" @click="requestClose()">Nanti Saja</button><a class="wali-button wali-button-primary" href="{{ route('wali-santri.profile') }}">Ganti Password Sekarang</a></div>
        </x-wali.dialog>
    @endif
</body>
</html>
