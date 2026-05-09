@props(['title', 'forceLight' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#10B981">

    {{-- PWA Meta --}}
    @include('components.pwa-meta')

    <title>{{ $title ?? 'Beranda' }} - Presensi Kajian Wali Santri</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🕌</text></svg>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL@24,400,0..1&display=swap" rel="stylesheet">

    <!-- Dark Mode Initializer -->
    <script>
        const forceLight = {{ $forceLight ? 'true' : 'false' }};
        if (!forceLight && (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches))) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body
    class="min-h-screen bg-gray-100 {{ $forceLight ? '' : 'dark:bg-slate-950' }} font-sans transition-colors duration-300">
    {{ $slot }}

    @livewireScripts
    @stack('scripts')

    <!-- Global Password Warning Modal -->
    @if(auth()->check() && auth()->user()->hasRole('wali_santri') && (\Illuminate\Support\Facades\Hash::check(auth()->user()->username, auth()->user()->password) || \Illuminate\Support\Facades\Hash::check('PasswordSementara123', auth()->user()->password)) && !request()->routeIs('wali-santri.profile'))
        <div x-data="{ showWarning: true }" 
             x-show="showWarning" 
             style="display: none;"
             class="fixed inset-0 z-[80] overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-slate-900/60 dark:bg-black/80 backdrop-blur-sm"></div>

                <div class="relative bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl w-full max-w-sm p-8 z-10 text-center transition-all"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 scale-95">
                     
                    <div class="w-20 h-20 bg-amber-50 dark:bg-amber-950/30 text-amber-500 dark:text-amber-400 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-amber-100 dark:border-amber-900/50 shadow-inner">
                        <span class="material-symbols-rounded text-4xl">shield_person</span>
                    </div>
                    
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">Akun Belum Aman!</h3>
                    <p class="text-slate-500 dark:text-gray-400 text-sm leading-relaxed mb-8">
                        Demi privasi dan keamanan data anak Anda, mohon segera ganti password bawaan Anda dengan password baru yang lebih kuat.
                    </p>

                    <div class="flex flex-col gap-3">
                        <a href="{{ route('wali-santri.profile') }}" 
                           class="w-full py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-bold flex items-center justify-center gap-2 shadow-xl shadow-amber-500/20 transition-all active:scale-95">
                            <span class="material-symbols-rounded">key</span>
                            Ganti Password Sekarang
                        </a>
                        <button @click="showWarning = false"
                           class="w-full py-3.5 border-2 border-slate-100 dark:border-slate-800 text-slate-400 dark:text-gray-500 rounded-2xl font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all mt-1">
                            Nanti Saja
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @include('components.pwa-push')
</body>

</html>
