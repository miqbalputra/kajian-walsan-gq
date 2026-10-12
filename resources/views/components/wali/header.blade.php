@php
    $avatarParts = explode('|', str_replace('icon:', '', auth()->user()->avatar ?? 'icon:mosque|bg-indigo-500'));
@endphp
<header class="wali-header wali-container">
    <a class="wali-brand" href="{{ route('wali-santri.dashboard') }}" aria-label="Presensi Wali Santri — Beranda">
        <img src="{{ asset('icons/icon.svg') }}" width="36" height="36" alt=""><span>Presensi Wali Santri</span>
    </a>
    <x-wali.navigation :desktop="true" />
    <div class="wali-header-tools" x-data="{ avatar: @js(auth()->user()->avatar ?? 'icon:mosque|bg-indigo-500'), name: @js(auth()->user()->name) }" @wali-profile-header-updated.window="avatar = $event.detail.avatar; name = $event.detail.name">
        <span class="wali-header-avatar" :class="avatar.split('|')[1]" :title="name"><span class="material-symbols-rounded" aria-hidden="true" x-text="avatar.split('|')[0].replace('icon:', '')">{{ $avatarParts[0] }}</span></span>
        <span class="wali-header-name" x-text="name">{{ auth()->user()->name }}</span>
        <button type="button" class="wali-icon-button" x-data="waliTheme()" @click="toggle()" :aria-label="dark ? 'Gunakan mode terang' : 'Gunakan mode gelap'" aria-label="Gunakan mode gelap" :aria-pressed="dark">
            <span class="material-symbols-rounded" aria-hidden="true" x-text="dark ? 'light_mode' : 'dark_mode'">dark_mode</span>
        </button>
        <button type="submit" form="wali-logout-form" class="wali-icon-button" aria-label="Keluar akun" title="Keluar akun"><span class="material-symbols-rounded" aria-hidden="true">logout</span></button>
    </div>
</header>
