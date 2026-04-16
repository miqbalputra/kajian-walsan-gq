<div class="pb-24 bg-gray-50 dark:bg-slate-950 transition-colors duration-500">
    <!-- Header Section -->
    <div
        class="bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-700 pt-12 pb-24 px-6 rounded-b-[3rem] shadow-xl relative overflow-hidden transition-all duration-500">
        <!-- Decorative Background (Cross Pattern from Welcome) -->
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')">
        </div>

        <div class="relative z-10 flex flex-col items-center">
            <div class="absolute top-0 right-0 flex items-center gap-2">
                <x-theme-toggle />
                <button onclick="document.getElementById('logout-form').submit()"
                    class="p-2 bg-white/10 hover:bg-red-500/20 hover:text-red-200 rounded-full transition-all text-white border border-white/10"
                    title="Keluar">
                    <span class="material-symbols-rounded">logout</span>
                </button>
            </div>
            @php
                $avatarParts = explode('|', str_replace('icon:', '', $avatar));
                $currentIcon = $avatarParts[0] ?? 'mosque';
                $currentColor = $avatarParts[1] ?? 'bg-indigo-500';
            @endphp

            <div
                class="w-32 h-32 rounded-[2.5rem] bg-white dark:bg-slate-800 p-1.5 shadow-2xl relative transition-colors">
                <div
                    class="w-full h-full rounded-[2.2rem] flex items-center justify-center relative {{ $currentColor }} text-white transition-all duration-500">
                    <span class="material-symbols-rounded text-6xl fill-1">{{ $currentIcon }}</span>
                </div>
            </div>

            <h2 class="text-2xl font-black text-white mt-6">{{ $name }}</h2>
            <p class="text-primary-100 dark:text-slate-400 text-sm font-medium mt-1 transition-colors">@ {{ $username }}
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-6 -mt-12 space-y-6 relative z-10">
        <!-- Avatar Picker Card -->
        <div
            class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/60 dark:shadow-none p-8 border border-slate-100 dark:border-slate-800 transition-all">
            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                <span class="w-8 h-8 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-rounded text-lg">category</span>
                </span>
                Pilih Karakter
            </h3>

            <div class="grid grid-cols-4 gap-4">
                @foreach($avatarPresets as $preset)
                    @php
                        $isSelected = "icon:{$preset['icon']}|{$preset['color']}" === $avatar;
                    @endphp
                    <button type="button" wire:click="selectAvatar('{{ $preset['icon'] }}', '{{ $preset['color'] }}')"
                        class="aspect-square rounded-2xl flex items-center justify-center transition-all relative {{ $preset['color'] }} 
                                                   {{ $isSelected ? 'scale-110 shadow-lg ring-4 ring-white' : 'opacity-40 hover:opacity-100 hover:scale-105' }}">

                        <span class="material-symbols-rounded text-2xl text-white {{ $isSelected ? 'fill-1' : '' }}">
                            {{ $preset['icon'] }}
                        </span>

                        @if($isSelected)
                            <div
                                class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-white text-emerald-500 rounded-full flex items-center justify-center shadow-lg border-2 border-emerald-500 scale-90">
                                <span class="material-symbols-rounded text-xs font-black">check</span>
                            </div>
                        @endif
                    </button>
                @endforeach
            </div>
            <p class="text-[10px] text-slate-400 mt-6 italic text-center">Pilih ikon benda yang paling sesuai untuk
                identitas profil Anda.</p>
        </div>

        <!-- Profile Info Card -->
        <div
            class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/60 dark:shadow-none p-8 border border-slate-100 dark:border-slate-800 transition-all">
            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-8 flex items-center gap-3">
                <span
                    class="w-8 h-8 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-rounded text-lg">manage_accounts</span>
                </span>
                Informasi Kontak
            </h3>

            @if (session()->has('message'))
                <div
                    class="mb-6 p-4 bg-emerald-50 text-emerald-600 rounded-2xl text-sm font-bold flex items-center gap-3 border border-emerald-100 italic">
                    <span class="material-symbols-rounded">check_circle</span>
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit="updateProfile" class="space-y-6">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Nama
                        Lengkap</label>
                    <input type="text" wire:model="name"
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-sm font-bold text-slate-700">
                    @error('name') <span class="text-red-500 text-xs font-bold mt-2 px-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label
                        class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Email</label>
                    <input type="email" wire:model="email"
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-sm font-bold text-slate-700">
                    @error('email') <span class="text-red-500 text-xs font-bold mt-2 px-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Nomor
                        WhatsApp</label>
                    <div class="relative">
                        <span
                            class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">+62</span>
                        <input type="text" wire:model="phone"
                            class="w-full pl-16 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-sm font-bold text-slate-700">
                    </div>
                    @error('phone') <span class="text-red-500 text-xs font-bold mt-2 px-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-4 bg-primary-600 text-white rounded-2xl font-black shadow-xl shadow-primary-500/20 hover:bg-primary-700 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                        <span wire:loading wire:target="updateProfile"
                            class="material-symbols-rounded animate-spin">progress_activity</span>
                        <span wire:loading.remove wire:target="updateProfile">Simpan Perubahan</span>
                        <span wire:loading wire:target="updateProfile">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Card -->
        <div
            class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/60 dark:shadow-none p-8 border border-slate-100 dark:border-slate-800 transition-all">
            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-8 flex items-center gap-3">
                <span
                    class="w-8 h-8 bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-rounded text-lg">shield_lock</span>
                </span>
                Keamanan Akun
            </h3>

            @if (session()->has('password-message'))
                <div
                    class="mb-6 p-4 bg-emerald-50 text-emerald-600 rounded-2xl text-sm font-bold flex items-center gap-3 border border-emerald-100 italic">
                    <span class="material-symbols-rounded">check_circle</span>
                    {{ session('password-message') }}
                </div>
            @endif

            <form wire:submit="updatePassword" class="space-y-6">
                {{-- Hidden username for browser password manager --}}
                <input type="text" name="username" value="{{ $username }}" autocomplete="username" class="hidden" style="display:none;">

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Password
                        Saat Ini</label>
                    <input type="password" wire:model="current_password" autocomplete="current-password"
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-sm font-bold text-slate-700">
                    @error('current_password') <span
                    class="text-red-500 text-xs font-bold mt-2 px-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Password
                        Baru</label>
                    <input type="password" wire:model="new_password" autocomplete="new-password"
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-sm font-bold text-slate-700">
                    @error('new_password') <span
                    class="text-red-500 text-xs font-bold mt-2 px-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label
                        class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Konfirmasi
                        Password Baru</label>
                    <input type="password" wire:model="new_password_confirmation" autocomplete="new-password"
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-sm font-bold text-slate-700">
                </div>

                <div class="pt-4 pb-4">
                    <button type="submit"
                        class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black shadow-xl shadow-slate-900/10 hover:bg-black transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                        <span wire:loading wire:target="updatePassword"
                            class="material-symbols-rounded animate-spin">progress_activity</span>
                        <span wire:loading.remove wire:target="updatePassword">Ganti Password</span>
                        <span wire:loading wire:target="updatePassword">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>



        @if($parentData)
        {{-- Kartu Identitas Card --}}
        <div
            class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/60 dark:shadow-none p-8 border border-slate-100 dark:border-slate-800 transition-all">
            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-4 flex items-center gap-3">
                <span
                    class="w-8 h-8 bg-teal-50 dark:bg-teal-950/30 text-teal-600 dark:text-teal-400 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-rounded text-lg">badge</span>
                </span>
                Kartu Identitas
            </h3>
            <p class="text-xs text-slate-400 dark:text-slate-500 mb-6 font-medium">Kartu identitas Anda sebagai Wali Santri dengan QR Code unik.</p>

            <button wire:click="showCard"
                class="w-full py-4 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-2xl font-black shadow-xl shadow-teal-500/20 hover:opacity-90 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                <span wire:loading wire:target="showCard"
                    class="material-symbols-rounded animate-spin">progress_activity</span>
                <span wire:loading.remove wire:target="showCard" class="material-symbols-rounded">qr_code_2</span>
                <span wire:loading.remove wire:target="showCard">Lihat &amp; Cetak Kartu</span>
                <span wire:loading wire:target="showCard">Memuat Kartu...</span>
            </button>
        </div>
        @endif

        {{-- Logout Card --}}
        <div
            class="bg-red-50 dark:bg-red-950/10 rounded-[2.5rem] p-8 border border-red-100 dark:border-red-900/30 mb-6 transition-colors">
            <h3 class="text-lg font-black text-red-900 dark:text-red-400 mb-2 flex items-center gap-3">
                <span
                    class="w-8 h-8 bg-white dark:bg-slate-800 text-red-600 rounded-xl flex items-center justify-center shadow-sm">
                    <span class="material-symbols-rounded text-lg">logout</span>
                </span>
                Keluar Aplikasi
            </h3>
            <p class="text-red-600/70 text-xs font-medium mb-6 px-1 italic">Pastikan Anda sudah menyimpan semua
                perubahan sebelum keluar.</p>

            <button onclick="document.getElementById('logout-form').submit()"
                class="w-full py-4 bg-white text-red-600 border border-red-200 rounded-2xl font-black shadow-lg shadow-red-500/5 hover:bg-red-600 hover:text-white transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                <span class="material-symbols-rounded">logout</span>
                Keluar Sekarang
            </button>
        </div>

        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>
    </div>

    {{-- ID Card Modal --}}
    @if($showCardModal && $parentData)
        @php
            $isMother = $parentData->type === 'mother';
            $bgHeader = $isMother ? 'from-rose-500 to-pink-500' : 'from-emerald-600 to-teal-500';
            $textColor = $isMother ? 'text-rose-600' : 'text-teal-600';
            $borderColor = $isMother ? 'border-rose-100' : 'border-teal-100';
            $accentBar = $isMother ? 'from-rose-500 via-pink-400 to-rose-500' : 'from-emerald-600 via-teal-400 to-emerald-600';
            $badgeBg = $isMother ? 'bg-rose-50 border-rose-100' : 'bg-emerald-50 border-emerald-100';
            $badgeText = $isMother ? 'text-rose-700' : 'text-emerald-700';
        @endphp
        <div class="fixed inset-0 z-[70] overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
                    wire:click="$set('showCardModal', false)"></div>

                <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-8 z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white">Kartu Identitas</h3>
                            <p class="text-sm text-gray-500 dark:text-slate-400">Ukuran standar KTP Indonesia</p>
                        </div>
                        <button wire:click="$set('showCardModal', false)"
                            class="p-2 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                            <span class="material-symbols-rounded text-2xl text-gray-700 dark:text-slate-300">close</span>
                        </button>
                    </div>

                    {{-- ID Card - KTP Size: 85.6mm x 53.98mm --}}
                    <div id="id-card-ws"
                        class="relative p-6 flex items-center justify-center bg-gray-50 dark:bg-slate-950 rounded-2xl border-2 border-dashed border-gray-200 dark:border-slate-700 overflow-hidden">
                        <div id="id-card-element-ws"
                            class="relative overflow-hidden rounded-xl shadow-2xl bg-white border border-gray-200"
                            style="width: 323.4px; height: 204px; min-width: 323.4px; min-height: 204px; font-family: 'Inter', sans-serif;">

                            {{-- Header Bar --}}
                            <div class="h-8 bg-gradient-to-r {{ $bgHeader }} flex items-center px-4 justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-rounded text-white text-base">mosque</span>
                                    <p class="text-white font-black text-[9px] tracking-tight uppercase">Kajian Walsan</p>
                                </div>
                                <p class="text-white/80 font-bold text-[7px] uppercase tracking-tighter">ID Wali Santri</p>
                            </div>

                            {{-- Background Pattern --}}
                            <div class="absolute inset-0 top-8 pointer-events-none opacity-[0.03]"
                                style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M10 0 L20 10 L10 20 L0 10 Z\' fill=\'none\' stroke=\'black\' stroke-width=\'0.5\'/%3E%3C/svg%3E');">
                            </div>

                            {{-- Card Body --}}
                            <div class="p-4 flex gap-4 h-[calc(100%-32px)]">
                                {{-- Left: Data Info --}}
                                <div class="flex-1 flex flex-col justify-between py-1 min-w-0">
                                    <div class="space-y-0.5">
                                        <p class="text-[6px] font-bold {{ $textColor }} uppercase tracking-widest leading-none">
                                            Nama Lengkap</p>
                                        <p class="text-[10px] font-black text-slate-800 leading-tight uppercase truncate" id="card-ws-name">
                                            {{ $parentData->user?->name }}
                                        </p>
                                    </div>

                                    <div class="flex gap-4">
                                        <div>
                                            <p class="text-[6px] font-bold {{ $textColor }} uppercase tracking-widest leading-none">
                                                Peran</p>
                                            <p class="text-[8px] font-bold text-slate-700 leading-none">
                                                {{ strtoupper($parentData->type_display) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-0.5 min-h-0 overflow-hidden">
                                        <p class="text-[6px] font-bold {{ $textColor }} uppercase tracking-widest leading-none">
                                            Wali Dari / NIS</p>
                                        <div class="space-y-0.5">
                                            @foreach($parentData->students->take(3) as $student)
                                                <p class="text-[8px] font-medium text-slate-700 truncate leading-none mt-0.5">•
                                                    {{ $student->name }} <span
                                                        class="text-slate-400 text-[6px]">({{ $student->nis }})</span>
                                                </p>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Right: QR Code --}}
                                <div class="flex-shrink-0 flex flex-col items-center justify-center">
                                    <div
                                        class="w-20 h-20 bg-white border {{ $borderColor }} rounded-lg p-1 shadow-sm flex items-center justify-center overflow-hidden qrcode-svg-container-ws">
                                        {!! $qrCodeSvg !!}
                                    </div>
                                    <p class="text-[5px] font-mono text-slate-400 mt-1.5 tracking-tighter">
                                        {{ $parentData->qr_code_string }}
                                    </p>

                                    <div
                                        class="mt-2 flex items-center gap-1 px-1.5 py-0.5 {{ $badgeBg }} rounded-full border">
                                        <span
                                            class="material-symbols-rounded {{ $isMother ? 'text-rose-600' : 'text-emerald-600' }} text-[8px]">verified</span>
                                        <span class="text-[5px] font-bold {{ $badgeText }} uppercase">Valid</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Text --}}
                            <div class="absolute bottom-1.5 left-0 right-4 text-right">
                                <p class="text-[5px] font-bold text-slate-400 opacity-60 uppercase tracking-tighter">
                                    Kelompok Tahfidz Griya Qur'an "Tunas Ilmu"
                                </p>
                            </div>

                            {{-- Footer Accent --}}
                            <div
                                class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r {{ $accentBar }}">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button wire:click="$set('showCardModal', false)"
                            class="flex-1 px-4 py-3 border border-gray-200 dark:border-slate-700 rounded-xl font-semibold text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                            Tutup
                        </button>
                        <a href="{{ route('wali-santri.kartu.download') }}"
                            target="_blank"
                            class="flex-1 px-4 py-3 bg-gradient-to-r {{ $isMother ? 'from-rose-500 to-pink-600' : 'from-emerald-600 to-teal-600' }} text-white rounded-xl font-semibold hover:opacity-90 transition-all inline-flex items-center justify-center gap-2 shadow-lg {{ $isMother ? 'shadow-rose-500/25' : 'shadow-emerald-500/25' }}">
                            <span class="material-symbols-rounded text-xl">download</span>
                            Download Kartu
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .qrcode-svg-container-ws svg {
                width: 100% !important;
                height: 100% !important;
                display: block;
            }
        </style>


    @endif


    {{-- Bottom Navigation --}}
    <nav
        class="fixed bottom-0 inset-x-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-t border-slate-100 dark:border-slate-800 px-6 py-3 flex items-center justify-around z-[60] transition-colors">
        <a href="{{ route('wali-santri.dashboard') }}"
            class="flex flex-col items-center gap-1 {{ request()->routeIs('wali-santri.dashboard') ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400' }}">
            <span
                class="material-symbols-rounded {{ request()->routeIs('wali-santri.dashboard') ? 'fill-1' : '' }}">home</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Home</span>
        </a>
        <a href="{{ route('wali-santri.schedule') }}"
            class="flex flex-col items-center gap-1 {{ request()->routeIs('wali-santri.schedule') ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400' }}">
            <span
                class="material-symbols-rounded {{ request()->routeIs('wali-santri.schedule') ? 'fill-1' : '' }}">calendar_month</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Jadwal</span>
        </a>
        <a href="{{ route('wali-santri.profile') }}"
            class="flex flex-col items-center gap-1 {{ request()->routeIs('wali-santri.profile') ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400' }}">
            <span
                class="material-symbols-rounded {{ request()->routeIs('wali-santri.profile') ? 'fill-1' : '' }}">person</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Profil</span>
        </a>
    </nav>
</div>