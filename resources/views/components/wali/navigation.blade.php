@props(['desktop' => false])
<nav class="wali-nav {{ $desktop ? 'wali-nav-desktop' : 'wali-nav-mobile' }}" aria-label="Menu wali santri">
    @foreach (['dashboard' => ['home', 'Beranda'], 'schedule' => ['calendar_month', 'Jadwal'], 'profile' => ['person', 'Profil']] as $page => [$icon, $label])
        <a href="{{ route('wali-santri.' . $page) }}" @if(request()->routeIs('wali-santri.' . $page)) aria-current="page" @endif>
            <span class="material-symbols-rounded" aria-hidden="true">{{ $icon }}</span><span>{{ $label }}</span>
        </a>
    @endforeach
</nav>
