<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#10B981">

    {{-- PWA Meta --}}
    @include('components.pwa-meta')

    <title>{{ $title ?? 'Admin' }} - Presensi Wali Santri</title>

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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL@24,400,0..1&display=swap" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="admin-ui min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-300" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
    <div class="admin-layout flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            id="admin-sidebar" aria-label="Navigasi admin"
            class="admin-sidebar fixed inset-y-0 left-0 z-50 flex h-dvh max-h-dvh w-[min(18rem,88vw)] flex-col bg-white dark:bg-slate-900 shadow-xl transform transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:max-h-screen lg:w-72 lg:translate-x-0 lg:shadow-none lg:border-r border-gray-200 dark:border-slate-800">
            <!-- Logo -->
            <div class="flex min-h-16 items-center gap-3 border-b border-gray-200 px-5 dark:border-slate-800">
                <span class="material-symbols-rounded text-primary-600 text-3xl">mosque</span>
                <div class="min-w-0 flex-1">
                    <h1 class="font-bold text-gray-900 dark:text-white">Presensi Wali Santri</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Admin Panel</p>
                </div>
                <button type="button" @click="sidebarOpen = false" aria-label="Tutup menu admin"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-gray-500 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:text-gray-300 dark:hover:bg-slate-800 lg:hidden">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>

            <!-- Navigation -->
            <nav aria-label="Menu admin" @click="sidebarOpen = false" class="admin-nav flex-1 space-y-5 overflow-y-auto p-3 sm:p-4">
                <div class="admin-nav-group">
                    <p class="admin-nav-heading">Ringkasan</p>
                <a href="{{ route('admin.dashboard') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">dashboard</span>
                    Dashboard
                </a>
                </div>

                <div class="admin-nav-group">
                    <p class="admin-nav-heading">Data & akademik</p>
                <a href="{{ route('admin.students.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.students.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">school</span>
                    Siswa
                </a>
                <a href="{{ route('admin.parents.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ (request()->routeIs('admin.parents.*') && request()->get('typeFilter') !== 'teacher') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">group</span>
                    Orang Tua
                </a>
                <a href="{{ route('admin.parents.index', ['typeFilter' => 'teacher']) }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ (request()->get('typeFilter') === 'teacher') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">person_book</span>
                    Data Guru
                </a>
                <a href="{{ route('admin.classes.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.classes.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">meeting_room</span>
                    Kelas
                </a>
                <a href="{{ route('admin.promotion.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.promotion.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">upgrade</span>
                    Kenaikan Kelas
                </a>
                <a href="{{ route('admin.archive.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.archive.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">inventory_2</span>
                    Arsip Santri & Wali
                </a>
                </div>

                <div class="admin-nav-group">
                    <p class="admin-nav-heading">Kehadiran & kegiatan</p>
                <a href="{{ route('admin.teacher-attendance.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.teacher-attendance.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">co_present</span>
                    Presensi Guru
                </a>
                <a href="{{ route('admin.kajian.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.kajian.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">event</span>
                    Kajian
                </a>
                <a href="{{ route('admin.validation.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.validation.index') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">verified_user</span>
                    Validasi
                    @php
                        $pendingCount = \App\Models\Attendance::where('validation_status', 'pending')
                            ->whereHas('parent', fn ($query) => $query->where('type', '!=', 'teacher'))
                            ->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.validation.trash') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.validation.trash') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">delete_outline</span>
                    Tempat Sampah
                </a>
                </div>

                <div class="admin-nav-group">
                    <p class="admin-nav-heading">Komunikasi & laporan</p>
                <a href="{{ route('admin.announcements.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.announcements.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">campaign</span>
                    Pengumuman
                </a>
                <a href="{{ route('admin.chat-ai') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.chat-ai') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">smart_toy</span>
                    Chat AI
                </a>
                <a href="{{ route('admin.reports.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">assessment</span>
                    Laporan
                </a>
                <a href="{{ route('admin.surveys.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.surveys.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">thumbs_up_down</span>
                    Hasil Survey
                </a>
                </div>

                <div class="admin-nav-group">
                    <p class="admin-nav-heading">Sistem</p>
                <a href="{{ route('admin.backups.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.backups.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">backup</span>
                    Backup Data
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">manage_accounts</span>
                    Manajemen User
                </a>
                @if(config('pulse.enabled'))
                <a href="{{ url('/pulse') }}" target="_blank"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800">
                    <span class="material-symbols-rounded">monitor_heart</span>
                    Sistem Pulse
                </a>
                @endif
                <a href="{{ route('admin.settings') }}"
                    class="admin-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('admin.settings') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                    <span class="material-symbols-rounded">settings</span>
                    Pengaturan
                </a>
                </div>

                <div class="border-t border-gray-200 pt-4 dark:border-slate-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="admin-nav-link flex w-full items-center gap-3 rounded-xl px-3 py-2.5 font-medium text-red-600 transition-colors hover:bg-red-50 dark:hover:bg-red-950/30">
                            <span class="material-symbols-rounded">logout</span>
                            Keluar
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen min-w-0">
            <!-- Top Header -->
            <header
                class="admin-topbar sticky top-0 z-40 flex min-h-16 items-center justify-between gap-2 border-b border-gray-200 bg-white px-3 transition-colors dark:border-slate-800 dark:bg-slate-900 sm:px-4 lg:px-6">
                <button @click="sidebarOpen = !sidebarOpen"
                    type="button" aria-label="Buka menu admin" aria-controls="admin-sidebar" :aria-expanded="sidebarOpen"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-gray-600 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:text-gray-300 dark:hover:bg-slate-800 lg:hidden">
                    <span class="material-symbols-rounded" aria-hidden="true">menu</span>
                </button>

                <h2 class="min-w-0 flex-1 truncate px-1 text-base font-bold text-gray-900 dark:text-white sm:text-lg">{{ $title ?? 'Dashboard' }}</h2>

                <div class="flex shrink-0 items-center gap-2 sm:gap-4">
                    <x-theme-toggle />
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ auth()->user()->role?->display_name }}
                            </p>
                        </div>
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full border border-primary-200 bg-primary-100 dark:border-slate-700 dark:bg-slate-800 sm:h-10 sm:w-10">
                            <span class="material-symbols-rounded text-primary-600 dark:text-primary-400">person</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="admin-main min-w-0 flex-1 overflow-x-hidden p-3 sm:p-4 lg:p-6 xl:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div x-cloak x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        aria-hidden="true" class="fixed inset-0 z-40 bg-slate-950/55 backdrop-blur-[1px] lg:hidden"></div>

    @livewireScripts

    <!-- Toast Notifications -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (data) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: data[0].type || 'success',
                    title: data[0].message
                });
            });
        });
    </script>

    @stack('scripts')

    {{-- PWA Install Banner & Service Worker --}}
</body>

</html>
