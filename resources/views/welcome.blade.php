<x-layouts.app title="Selamat Datang" :force-light="true">
    <div class="relative min-h-screen overflow-hidden bg-slate-50 font-sans selection:bg-primary-500 selection:text-white"
        x-data="{ showInfo: false, showGuide: false }">

        <!-- Animated Background (Matching login page) -->
        <div class="fixed inset-0 w-full h-full bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-700">
            <!-- Decorative Blobs - Simplified for performance -->
            <div class="absolute top-[10%] right-[5%] w-[400px] h-[400px] rounded-full bg-primary-400/20 blur-3xl">
            </div>
            <div class="absolute bottom-[10%] left-[5%] w-[350px] h-[350px] rounded-full bg-secondary-400/15 blur-3xl">
            </div>

            <!-- Pattern Overlay -->
            <div class="absolute inset-0 opacity-[0.03]"
                style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')">
            </div>
        </div>

        <!-- Header -->
        <header class="relative z-20 flex items-center justify-between px-6 py-6 max-w-7xl mx-auto">
            <div class="flex items-center gap-3 group cursor-default">
                <div class="relative">
                    <div
                        class="relative w-10 h-10 bg-white/15 border border-white/20 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-symbols-rounded text-white text-2xl">mosque</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="text-white font-bold text-xl tracking-tight leading-none">Wali Santri</span>
                    <span class="text-white/70 text-[10px] font-medium uppercase tracking-wider">Presensi Kajian</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-5 py-2.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 backdrop-blur-md transition-all text-white text-sm font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-5 py-2.5 rounded-full bg-white text-primary-700 hover:bg-gray-50 transition-all text-sm font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Masuk
                    </a>
                @endauth
                <button @click="showInfo = !showInfo"
                    class="p-2.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 backdrop-blur-md transition-all text-white shadow-lg hover:rotate-12">
                    <span class="material-symbols-rounded text-xl">info</span>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 w-full max-w-xl mx-auto px-6 pb-12 pt-4 flex flex-col items-center">

            <!-- Hero Section -->
            <div class="text-center mb-8 relative">
                <div class="inline-block relative mb-4">
                    <div
                        class="relative w-24 h-24 bg-gradient-to-tr from-white/15 to-white/5 border border-white/20 rounded-[2rem] flex items-center justify-center shadow-2xl">
                        <span class="material-symbols-rounded text-6xl text-white drop-shadow-md">qr_code_scanner</span>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-white mb-3 tracking-tight drop-shadow-sm">
                    Kajian Rutin
                </h1>
                <p class="text-lg text-white/80 font-medium max-w-sm mx-auto leading-relaxed">
                    Sistem Presensi & Informasi Kehadiran Wali Santri
                </p>
            </div>

            <!-- Current/Next Kajian Info Card -->
            @if($currentKajian)
                <div class="w-full mb-8 transform hover:scale-[1.02] transition-transform duration-300">
                    <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden border border-white/40">
                        <!-- Decorative Header Background -->
                        <div class="absolute top-0 inset-x-0 h-32 bg-gradient-to-b from-gray-50 to-white opacity-50"></div>

                        <!-- Status Badge Strip -->
                        <div class="relative px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                @php
                                    $badgeClasses = match ($timeStatus) {
                                        'ongoing' => 'bg-green-100 text-green-700 border-green-200',
                                        'soon' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'today' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'tomorrow' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200'
                                    };

                                    $icon = match ($timeStatus) {
                                        'ongoing' => 'graphic_eq',
                                        'soon' => 'timer',
                                        'today' => 'today',
                                        'tomorrow' => 'event',
                                        default => 'calendar_month'
                                    };

                                    $staticStatusText = match ($timeStatus) {
                                        'ongoing' => 'Sedang Berlangsung',
                                        'today' => 'Hari Ini',
                                        'tomorrow' => 'Besok',
                                        default => 'Kajian Berikutnya'
                                    };
                                @endphp

                                @if($timeStatus === 'soon' && isset($eventTimestamp))
                                    {{-- Live Countdown Timer --}}
                                    <div x-data="countdownTimer({{ $eventTimestamp }})"
                                        class="px-3 py-1.5 rounded-full border transition-all duration-300"
                                        :class="isFinished ? 'bg-green-100 text-green-700 border-green-200' : 'bg-amber-100 text-amber-700 border-amber-200'">
                                        <div class="flex items-center gap-2">
                                            <template x-if="isFinished">
                                                <span class="relative flex h-2.5 w-2.5">
                                                    <span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                                                    <span
                                                        class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                                </span>
                                            </template>
                                            <template x-if="!isFinished">
                                                <span class="material-symbols-rounded text-lg">timer</span>
                                            </template>
                                            <span class="text-xs font-bold uppercase tracking-wide" x-text="displayText"></span>
                                        </div>
                                    </div>
                                @else
                                    {{-- Static Status Badge --}}
                                    <div class="px-3 py-1.5 rounded-full border {{ $badgeClasses }} flex items-center gap-2">
                                        @if($timeStatus === 'ongoing')
                                            <span class="relative flex h-2.5 w-2.5">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                            </span>
                                        @else
                                            <span class="material-symbols-rounded text-lg">{{ $icon }}</span>
                                        @endif
                                        <span class="text-xs font-bold uppercase tracking-wide">{{ $staticStatusText }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($currentKajian->status === 'open')
                                <div
                                    class="px-2.5 py-1 rounded-lg bg-primary-50 text-primary-600 text-[10px] font-bold uppercase border border-primary-100 tracking-wider">
                                    Terbuka
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="relative p-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 leading-tight">
                                {{ $currentKajian->title }}
                            </h3>

                            <div class="grid gap-4">
                                @if($currentKajian->speaker)
                                    <div
                                        class="flex items-start gap-4 p-3 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-md transition-all group">
                                        <div
                                            class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-gray-100 group-hover:border-primary-100 group-hover:bg-primary-50 transition-colors">
                                            <span class="material-symbols-rounded text-primary-500 text-xl">person</span>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-0.5">
                                                Pemateri</p>
                                            <p class="text-gray-900 font-semibold text-sm">{{ $currentKajian->speaker }}</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 gap-4">
                                    <div
                                        class="flex flex-col p-3 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-md transition-all group">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span
                                                class="material-symbols-rounded text-secondary-500 text-lg">calendar_today</span>
                                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Tanggal
                                            </p>
                                        </div>
                                        <p class="text-gray-900 font-semibold text-sm pl-7">
                                            {{ $currentKajian->formatted_date }}
                                        </p>
                                    </div>

                                    <div
                                        class="flex flex-col p-3 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-md transition-all group">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="material-symbols-rounded text-accent-500 text-lg">schedule</span>
                                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Waktu
                                            </p>
                                        </div>
                                        <p class="text-gray-900 font-semibold text-sm pl-7">{{ $currentKajian->time_range }}
                                        </p>
                                    </div>
                                </div>

                                @if($currentKajian->location)
                                    <div class="flex items-start gap-3 px-3 py-2 text-gray-500 text-sm">
                                        <span class="material-symbols-rounded text-lg mt-0.5">location_on</span>
                                        <span>{{ $currentKajian->location }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($timeStatus === 'ongoing' && $currentKajian->attendance_count > 0)
                                <div
                                    class="mt-6 flex items-center justify-center py-3 bg-green-50 rounded-xl border border-green-100">
                                    <div class="flex items-center gap-2 text-green-700">
                                        <span class="material-symbols-rounded">groups</span>
                                        <span class="font-bold">{{ $currentKajian->attendance_count }}</span>
                                        <span class="text-sm font-medium">wali santri sudah hadir</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="w-full mb-8">
                    <div class="bg-white rounded-3xl shadow-xl p-8 text-center border-2 border-dashed border-gray-200">
                        <div
                            class="inline-flex w-16 h-16 bg-gray-50 rounded-2xl items-center justify-center mb-4 shadow-inner">
                            <span class="material-symbols-rounded text-gray-300 text-3xl">event_busy</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Jadwal</h3>
                        <p class="text-gray-500 text-sm">Jadwal kajian berikutnya belum tersedia.</p>
                    </div>
                </div>
            @endif

            <!-- Attendance Guide Prompt -->
            <button @click="showGuide = true"
                class="w-full mb-8 group relative overflow-hidden bg-gradient-to-r from-primary-600 to-secondary-600 p-1 rounded-3xl shadow-xl transition-all duration-300 hover:shadow-primary-500/20 hover:-translate-y-1">
                <div class="bg-white dark:bg-slate-900 group-hover:bg-transparent rounded-[1.35rem] p-5 flex items-center justify-between transition-colors duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-all">
                            <span class="material-symbols-rounded text-2xl font-bold">menu_book</span>
                        </div>
                        <div class="text-left">
                            <h3 class="font-black text-gray-900 group-hover:text-white text-base leading-tight">Panduan Alur Presensi</h3>
                            <p class="text-gray-500 group-hover:text-white/80 text-[10px] uppercase font-bold tracking-widest mt-0.5">Wajib dibaca Wali Santri</p>
                        </div>
                    </div>
                    <span class="material-symbols-rounded text-primary-300 group-hover:text-white transition-colors">arrow_forward_ios</span>
                </div>
            </button>

            <!-- Action Buttons Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full mb-8">
                <!-- Login Panitia Button -->
                <a href="{{ route('login') }}"
                    class="group relative overflow-hidden bg-white hover:bg-primary-50 p-6 rounded-3xl shadow-xl transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                    <div class="relative z-10 flex flex-col items-center text-center gap-3">
                        <div
                            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center shadow-lg shadow-primary-500/30 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-rounded text-3xl">admin_panel_settings</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-primary-700">Masuk Akun
                            </h3>
                            <p class="text-gray-500 text-xs">Panitia / Admin</p>
                        </div>
                    </div>
                    <!-- Hover Effect -->
                    <div
                        class="absolute inset-0 border-2 border-transparent group-hover:border-primary-200 rounded-3xl transition-colors">
                    </div>
                </a>

                <!-- Login Wali Santri Button -->
                <a href="{{ route('login') }}"
                    class="group relative overflow-hidden bg-white hover:bg-secondary-50 p-6 rounded-3xl shadow-xl transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                    <div class="relative z-10 flex flex-col items-center text-center gap-3">
                        <div
                            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-secondary-500 to-secondary-600 text-white flex items-center justify-center shadow-lg shadow-secondary-500/30 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-rounded text-3xl">family_restroom</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-secondary-700">Masuk Akun
                            </h3>
                            <p class="text-gray-500 text-xs">Wali Santri</p>
                        </div>
                    </div>
                    <!-- Hover Effect -->
                    <div
                        class="absolute inset-0 border-2 border-transparent group-hover:border-secondary-200 rounded-3xl transition-colors">
                    </div>
                </a>
            </div>

            <!-- Last Stats Mini Card -->
            @if($lastKajian)
                <div class="w-full">
                    <div
                        class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white">
                                <span class="material-symbols-rounded text-xl">history</span>
                            </div>
                            <div>
                                <p class="text-white/60 text-[10px] font-bold uppercase tracking-wider">Kajian Terakhir</p>
                                <p class="text-white font-medium text-sm truncate max-w-[150px] sm:max-w-xs">
                                    {{ $lastKajian->title }}
                                </p>
                            </div>
                        </div>
                        @if($lastKajian->attendance_count > 0)
                            <div class="text-right">
                                <span
                                    class="block text-2xl font-bold text-white leading-none">{{ $lastKajian->attendance_count }}</span>
                                <span class="text-white/60 text-[10px]">Hadir</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </main>

        <!-- Rights Footer -->
        <footer class="relative z-10 py-6 text-center">
            <p class="text-white/60 text-xs font-medium">
                &copy; {{ date('Y') }} by <span
                    class="text-white/80 hover:text-white transition-colors cursor-pointer">Kelompok Tahfidz Griya
                    Qur'an "Tunas Ilmu"</span>
            </p>
        </footer>

        <!-- Info Modal -->
        <div x-cloak x-show="showInfo" x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/70"
            @click.self="showInfo = false">

            <div class="flex min-h-full items-center justify-center p-4">
                <div x-cloak x-show="showInfo" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden relative border border-white/50 my-8"
                    @click.stop>

                    <!-- Premium Header -->
                    <div class="relative h-40 bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-600">
                        <!-- Elements that need clipping -->
                        <div class="absolute inset-0 overflow-hidden">
                            <!-- Geometric Pattern Overlay -->
                            <div class="absolute inset-0 opacity-20"
                                style="background-image: url('data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' viewBox=\'0 0 100 100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M50 0 L100 50 L50 100 L0 50 Z\' fill=\'none\' stroke=\'white\' stroke-width=\'1\'/%3E%3C/svg%3E'); background-size: 40px 40px;">
                            </div>

                            <!-- Decorative Light Effects - Simplified -->
                            <div class="absolute -top-20 -right-20 w-48 h-48 bg-white/20 rounded-full blur-2xl"></div>
                            <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-primary-400/30 rounded-full blur-2xl">
                            </div>
                        </div>

                        <!-- Close Button -->
                        <button @click="showInfo = false"
                            class="absolute top-6 right-6 z-20 w-10 h-10 bg-black/20 hover:bg-black/30 rounded-2xl text-white transition-colors flex items-center justify-center">
                            <span class="material-symbols-rounded">close</span>
                        </button>

                        <!-- Icon Assembly -->
                        <div class="absolute left-1/2 bottom-0 -translate-x-1/2 translate-y-1/2 z-[11]">
                            <div class="w-24 h-24 p-2 bg-white rounded-[2rem] shadow-2xl">
                                <div
                                    class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-600 rounded-[1.5rem] flex items-center justify-center text-white">
                                    <span class="material-symbols-rounded text-5xl">mosque</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative px-8 pt-16 pb-10 text-center">
                        <!-- App Title -->
                        <div class="mb-8">
                            <h3 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Kajian Walsan</h3>
                            <div class="flex items-center justify-center gap-2">
                                <div class="h-1 w-8 bg-primary-500 rounded-full"></div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em]">Pusat Kendali
                                    Presensi</p>
                                <div class="h-1 w-8 bg-primary-500 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Description Box -->
                        <div
                            class="p-5 bg-slate-50 rounded-[2rem] border border-slate-100 mb-8 relative group overflow-hidden">
                            <div class="absolute top-0 right-0 p-2 opacity-5">
                                <span class="material-symbols-rounded text-4xl">info</span>
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed relative z-10 font-medium">
                                Solusi digital terintegrasi untuk pengelolaan data kehadiran & pemantauan kegiatan
                                kajian rutin wali santri di lingkungan pesantren secara modern, akurat, dan transparan.
                            </p>
                        </div>

                        <!-- Feature Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Feature 1 -->
                            <div
                                class="group p-4 bg-white border border-slate-100 shadow-sm rounded-3xl hover:border-primary-100 hover:shadow-xl hover:shadow-primary-500/10 transition-all duration-300 transform hover:-translate-y-1">
                                <div
                                    class="w-12 h-12 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-600 mb-3 mx-auto group-hover:scale-110 group-hover:bg-primary-500 group-hover:text-white transition-all duration-300">
                                    <span class="material-symbols-rounded text-2xl">qr_code_2</span>
                                </div>
                                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">QR
                                    Presensi</span>
                            </div>

                            <!-- Feature 2 -->
                            <div
                                class="group p-4 bg-white border border-slate-100 shadow-sm rounded-3xl hover:border-secondary-100 hover:shadow-xl hover:shadow-secondary-500/10 transition-all duration-300 transform hover:-translate-y-1">
                                <div
                                    class="w-12 h-12 bg-secondary-50 rounded-2xl flex items-center justify-center text-secondary-600 mb-3 mx-auto group-hover:scale-110 group-hover:bg-secondary-500 group-hover:text-white transition-all duration-300">
                                    <span class="material-symbols-rounded text-2xl">insert_chart</span>
                                </div>
                                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Realtime
                                    Stat</span>
                            </div>

                            <!-- Feature 3 -->
                            <div
                                class="group p-4 bg-white border border-slate-100 shadow-sm rounded-3xl hover:border-amber-100 hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 transform hover:-translate-y-1">
                                <div
                                    class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mb-3 mx-auto group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                                    <span class="material-symbols-rounded text-2xl">calendar_month</span>
                                </div>
                                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Jadwal
                                    Kajian</span>
                            </div>

                            <!-- Feature 4 -->
                            <div
                                class="group p-4 bg-white border border-slate-100 shadow-sm rounded-3xl hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 transform hover:-translate-y-1">
                                <div
                                    class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-3 mx-auto group-hover:scale-110 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                                    <span class="material-symbols-rounded text-2xl">verified_user</span>
                                </div>
                                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">ID
                                    Terpadu</span>
                            </div>
                        </div>

                        <!-- Footer Info -->
                        <div class="mt-10 pt-8 border-t border-slate-100 relative">
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 bg-white">
                                <span
                                    class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300">Identity</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Developed by
                            </p>
                            <p
                                class="text-sm font-black bg-gradient-to-r from-primary-600 to-secondary-500 bg-clip-text text-transparent">
                                Muhammad Iqbal Putra</p>

                            <!-- Contact Links -->
                            <div class="flex items-center justify-center gap-3 mt-4">
                                <a href="https://wa.me/6281390292177" target="_blank"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-500 hover:text-white transition-all duration-300 transform hover:-translate-y-0.5 shadow-sm border border-emerald-100/50">
                                    <span class="material-symbols-rounded text-lg">chat</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">WhatsApp</span>
                                </a>
                                <a href="mailto:iqbalmarketist@gmail.com"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-500 hover:text-white transition-all duration-300 transform hover:-translate-y-0.5 shadow-sm border border-blue-100/50">
                                    <span class="material-symbols-rounded text-lg">mail</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Email</span>
                                </a>
                            </div>

                            <p class="text-[9px] text-slate-300 mt-6 font-medium">&copy; {{ date('Y') }} • Kajian Walsan
                                Digital Ecosystem v2.0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Countdown Timer Alpine.js Component --}}
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('countdownTimer', (targetTimestamp) => ({
                    targetTime: Number(targetTimestamp),
                    displayText: 'Memuat...',
                    isFinished: false,
                    intervalId: null,

                    init() {
                        // Alpine.js automatic lifecycle hook
                        this.updateCountdown();
                        this.intervalId = setInterval(() => {
                            this.updateCountdown();
                        }, 1000);
                    },

                    updateCountdown() {
                        const now = Date.now();
                        const diff = this.targetTime - now;

                        if (diff <= 0) {
                            this.isFinished = true;
                            this.displayText = 'Sedang Berlangsung';
                            if (this.intervalId) {
                                clearInterval(this.intervalId);
                            }
                            return;
                        }

                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                        // Format the display text with Indonesian labels
                        if (hours > 0) {
                            this.displayText = `Dimulai dalam ${hours} jam ${minutes} menit ${seconds} detik`;
                        } else if (minutes > 0) {
                            this.displayText = `Dimulai dalam ${minutes} menit ${seconds} detik`;
                        } else {
                            this.displayText = `Dimulai dalam ${seconds} detik`;
                        }
                    },

                    destroy() {
                        if (this.intervalId) {
                            clearInterval(this.intervalId);
                        }
                    }
                }));
            });
        </script>
        <script>
            function copySocializationText() {
                const text = `*PANDUAN PRESENSI KAJIAN WALI SANTRI*

Assalamu'alaikum Warahmatullahi Wabarakatuh, Ayah/Bunda Wali Santri sekalian. Berikut alur presensi kajian kita:

1️⃣ *HADIR LANGSUNG (FISIK)*
   - Tunjukkan QR Code di aplikasi atau Kartu Identitas cetak ke petugas.
   - Tanpa HP/Kartu? Cukup sebutkan Nama Ortu & Nama Ananda ke petugas.

2️⃣ *HADIR ONLINE (STREAMING)*
   - Klik "Hadir Online" di dashboard aplikasi.
   - Upload Foto Catatan Kajian tulisan tangan (Bapak dan Ibu mengumpulkan catatan masing-masing).

3️⃣ *IZIN (TIDAK HADIR TOTAL)*
   - Klik "Izin" di dashboard aplikasi.
   - Upload surat pernyataan/keterangan berhalangan.

Mohon diperhatikan agar absensi Ananda tetap terjaga. Syukran Jazakumullah Khairan.`;
                
                navigator.clipboard.writeText(text).then(() => {
                    alert('Teks sosialisasi berhasil disalin! Silakan tempel di WhatsApp.');
                });
            }
        </script>

        <!-- Attendance Guide Modal -->
        <div x-cloak x-show="showGuide" x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-[110] overflow-y-auto bg-slate-950/90 backdrop-blur-md"
            @click.self="showGuide = false">

            <div class="flex min-h-screen items-center justify-center p-4 sm:p-6 lg:p-8">
                <div x-cloak x-show="showGuide" x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-12 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-12 scale-95"
                    class="bg-white dark:bg-slate-900 rounded-[3rem] shadow-2xl w-full max-w-2xl overflow-hidden relative border border-white/20 dark:border-slate-800"
                    @click.stop>

                    <!-- Close Button -->
                    <button @click="showGuide = false" 
                        class="fixed sm:absolute top-6 right-6 w-12 h-12 bg-white/10 hover:bg-white/20 backdrop-blur-xl rounded-full flex items-center justify-center text-white transition-all z-20 border border-white/20 group">
                        <span class="material-symbols-rounded group-hover:rotate-90 transition-transform duration-300">close</span>
                    </button>

                    <!-- Header Section -->
                    <div class="relative h-64 sm:h-72 bg-slate-900 border-b border-slate-100 dark:border-slate-800 overflow-hidden">
                        <!-- Decorative Shapes -->
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary-500/20 rounded-full blur-[80px]"></div>
                        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-secondary-500/20 rounded-full blur-[80px]"></div>
                        
                        <img src="/api/files/C:/Users/LENOVO/.gemini/antigravity/brain/82638b85-fafd-433c-97f2-a5759e773c4a/attendance_flow_guide_1776301777931.png" 
                            class="w-full h-full object-cover opacity-50 contrast-125 brightness-75 scale-110" alt="Attendance Flow">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-white dark:from-slate-900 via-white/20 dark:via-slate-900/40 to-transparent"></div>
                        
                        <div class="absolute bottom-10 left-10 right-10">
                            <span class="inline-block px-3 py-1 bg-primary-500/10 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400 text-[10px] font-black uppercase tracking-[0.3em] rounded-full border border-primary-500/20 mb-4">Official Guide</span>
                            <h3 class="text-4xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-none">Alur Presensi</h3>
                            <p class="text-slate-600 dark:text-slate-400 text-base font-medium mt-3">Panduan lengkap kehadiran bagi Wali Santri</p>
                        </div>
                    </div>

                    <div class="px-8 sm:px-10 py-10 space-y-6 max-h-[60vh] overflow-y-auto overscroll-contain">
                        <!-- Step 1: Hadir Langsung -->
                        <div class="group bg-slate-50 dark:bg-slate-800/50 rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 hover:border-primary-500/30 dark:hover:border-primary-500/30 transition-all duration-500">
                            <div class="flex flex-col sm:flex-row gap-6">
                                <div class="w-16 h-16 bg-white dark:bg-slate-800 text-primary-600 dark:text-primary-400 rounded-2xl flex-shrink-0 flex items-center justify-center border border-slate-100 dark:border-slate-700 shadow-sm group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                                    <span class="material-symbols-rounded text-3xl font-bold fill-1">qr_code_scanner</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span class="text-xs font-black text-primary-500/60 uppercase tracking-widest leading-none">Bagian Satu</span>
                                        <div class="h-px flex-1 bg-primary-500/10"></div>
                                    </div>
                                    <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Hadir Langsung (Fisik)</h4>
                                    <ul class="space-y-4">
                                        <li class="flex items-start gap-4 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                            <div class="w-1.5 h-1.5 rounded-full bg-primary-500 mt-2 shrink-0"></div>
                                            <span>Tunjukkan <strong class="text-slate-900 dark:text-white">QR Code</strong> di aplikasi ini ke petugas atau gunakan <strong class="text-slate-900 dark:text-white">Kartu Identitas</strong> fisik.</span>
                                        </li>
                                        <li class="flex items-start gap-4 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                            <div class="w-1.5 h-1.5 rounded-full bg-primary-500 mt-2 shrink-0"></div>
                                            <span>Jika lupa membawa HP/Kartu, sebutkan <strong class="text-slate-900 dark:text-white">Nama Ortu & Nama Ananda</strong> kepada petugas di lokasi.</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Hadir Online -->
                        <div class="group bg-slate-50 dark:bg-slate-800/50 rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 hover:border-secondary-500/30 dark:hover:border-secondary-500/30 transition-all duration-500">
                            <div class="flex flex-col sm:flex-row gap-6">
                                <div class="w-16 h-16 bg-white dark:bg-slate-800 text-secondary-600 dark:text-secondary-400 rounded-2xl flex-shrink-0 flex items-center justify-center border border-slate-100 dark:border-slate-700 shadow-sm group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500">
                                    <span class="material-symbols-rounded text-3xl font-bold fill-1">videocam</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span class="text-xs font-black text-secondary-500/60 uppercase tracking-widest leading-none">Bagian Dua</span>
                                        <div class="h-px flex-1 bg-secondary-500/10"></div>
                                    </div>
                                    <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Hadir Online (Streaming)</h4>
                                    <ul class="space-y-4">
                                        <li class="flex items-start gap-4 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                            <div class="w-1.5 h-1.5 rounded-full bg-secondary-500 mt-2 shrink-0"></div>
                                            <span>Klik tombol <strong class="text-slate-900 dark:text-white">"Hadir Online"</strong> di dashboard saat kajian sedang berlangsung.</span>
                                        </li>
                                        <li class="flex items-start gap-4 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                            <div class="w-1.5 h-1.5 rounded-full bg-secondary-500 mt-2 shrink-0"></div>
                                            <span>Upload <strong class="text-slate-900 dark:text-white">Foto Catatan Kajian</strong> hasil tulisan tangan sendiri (Bapak & Ibu masing-masing).</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Izin -->
                        <div class="group bg-slate-50 dark:bg-slate-800/50 rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 hover:border-amber-500/30 dark:hover:border-amber-500/30 transition-all duration-500">
                            <div class="flex flex-col sm:flex-row gap-6">
                                <div class="w-16 h-16 bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 rounded-2xl flex-shrink-0 flex items-center justify-center border border-slate-100 dark:border-slate-700 shadow-sm group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                                    <span class="material-symbols-rounded text-3xl font-bold fill-1">event_busy</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span class="text-xs font-black text-amber-500/60 uppercase tracking-widest leading-none">Bagian Tiga</span>
                                        <div class="h-px flex-1 bg-amber-500/10"></div>
                                    </div>
                                    <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Izin (Berhalangan)</h4>
                                    <ul class="space-y-4">
                                        <li class="flex items-start gap-4 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0"></div>
                                            <span>Klik tombol <strong class="text-slate-900 dark:text-white">"Izin"</strong> jika sama sekali tidak bisa hadir fisik maupun online.</span>
                                        </li>
                                        <li class="flex items-start gap-4 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0"></div>
                                            <span>Upload <strong class="text-slate-900 dark:text-white">Surat Pernyataan</strong> atau dokumen pendukung keterangan berhalangan.</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp Action Section -->
                        <div class="relative rounded-[2.5rem] p-8 sm:p-10 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/50 text-center overflow-hidden">
                            <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-100 dark:bg-emerald-900/30 rounded-full blur-3xl"></div>
                            
                            <p class="relative z-10 text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.4em] mb-6">Sosialisasi Grup WhatsApp</p>
                            <h5 class="relative z-10 text-lg font-bold text-emerald-900 dark:text-emerald-100 mb-8 px-4 sm:px-8">Bantu Wali Santri lain memahami alur presensi dengan menyalin teks berikut</h5>
                            
                            <button onclick="copySocializationText()" 
                                class="relative z-10 w-full py-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black flex items-center justify-center gap-4 transition-all active:scale-95 shadow-2xl shadow-emerald-500/30 group">
                                <span class="material-symbols-rounded group-hover:animate-bounce">content_copy</span>
                                <span>SALIN TEKS SOSIALISASI</span>
                            </button>
                            
                            <p class="relative z-10 text-emerald-600/60 dark:text-emerald-400/40 text-[10px] mt-6 font-bold uppercase tracking-widest">Klik tombol di atas untuk menyalin otomatis</p>
                        </div>
                    </div>
                    
                    <!-- Bottom Gradient Fade (Mobile Safety) -->
                    <div class="absolute bottom-0 inset-x-0 h-10 bg-gradient-to-t from-white dark:from-slate-900 pointer-events-none"></div>
                </div>
            </div>
        </div>
</x-layouts.app>