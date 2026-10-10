@props(['login' => false])
<header class="public-header public-container {{ $login ? 'public-header-login' : '' }}">
    <a class="public-brand" href="{{ route('home') }}" aria-label="Presensi Wali Santri — Beranda">
        <img src="{{ asset('icons/icon.svg') }}" width="40" height="40" alt="">
        <span>
            <span class="public-brand-name">{{ config('kajian.name', 'Presensi Wali Santri') }}</span>
            <span class="public-brand-institution">{{ config('kajian.institution') }}</span>
        </span>
    </a>
    <nav aria-label="Navigasi utama">
        @if ($login)
            <a class="public-header-link" href="{{ route('home') }}"><x-public.icon name="arrow-left" /> Beranda</a>
        @else
            <a class="public-header-link" href="#cara-presensi">Panduan</a>
        @endif
    </nav>
</header>
