@props(['name'])
<svg {{ $attributes->class(['public-icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($name)
        @case('arrow-right')
            <path d="M5 12h14m-6-6 6 6-6 6" />
            @break
        @case('arrow-left')
            <path d="M19 12H5m6-6-6 6 6 6" />
            @break
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 11h18m-13 5h2" />
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" />
            @break
        @case('pin')
            <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z" /><circle cx="12" cy="10" r="2.5" />
            @break
        @case('user')
            <circle cx="12" cy="8" r="4" /><path d="M4 21v-2a8 8 0 0 1 16 0v2" />
            @break
        @case('notebook')
            <rect x="5" y="3" width="15" height="18" rx="2" /><path d="M3 7h4M3 12h4M3 17h4m4-9h5m-5 4h5m-5 4h3" />
            @break
        @case('document')
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" /><path d="M14 2v6h6M8 13h8m-8 4h5" />
            @break
        @case('message')
            <path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5H3l2-5a8.5 8.5 0 1 1 16-3.5Z" /><path d="M8 11h8m-8 4h5" />
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6" />
            @break
        @case('eye')
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" /><circle cx="12" cy="12" r="3" />
            @break
        @case('eye-off')
            <path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.2A11 11 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-3.3 4.2M6.2 6.2A18 18 0 0 0 2 12s3.5 7 10 7a11 11 0 0 0 5.8-1.8" />
            @break
        @case('check')
            <path d="m5 12 4 4L19 6" />
            @break
    @endswitch
</svg>
