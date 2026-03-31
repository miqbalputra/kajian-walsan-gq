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
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Password
                        Saat Ini</label>
                    <input type="password" wire:model="current_password"
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-sm font-bold text-slate-700">
                    @error('current_password') <span
                    class="text-red-500 text-xs font-bold mt-2 px-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Password
                        Baru</label>
                    <input type="password" wire:model="new_password"
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-sm font-bold text-slate-700">
                    @error('new_password') <span
                    class="text-red-500 text-xs font-bold mt-2 px-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label
                        class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Konfirmasi
                        Password Baru</label>
                    <input type="password" wire:model="new_password_confirmation"
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

        <!-- Google Account Card -->
        <div
            class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/60 dark:shadow-none p-8 border border-slate-100 dark:border-slate-800 transition-all">
            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                <span class="w-8 h-8 bg-blue-50 dark:bg-blue-950/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                </span>
                Login dengan Google
            </h3>
            <p class="text-slate-400 text-xs font-medium mb-6 px-1 italic">
                Hubungkan akun Google agar bisa login lebih mudah tanpa perlu ingat password.
            </p>

            @if(session()->has('google-success'))
                <div class="mb-4 p-4 bg-emerald-50 text-emerald-600 rounded-2xl text-sm font-bold flex items-center gap-3 border border-emerald-100 italic">
                    <span class="material-symbols-rounded">check_circle</span>
                    {{ session('google-success') }}
                </div>
            @endif
            @if(session()->has('google-error'))
                <div class="mb-4 p-4 bg-red-50 text-red-600 rounded-2xl text-sm font-bold flex items-center gap-3 border border-red-100 italic">
                    <span class="material-symbols-rounded">error</span>
                    {{ session('google-error') }}
                </div>
            @endif

            @if(auth()->user()->hasGoogleLinked())
                {{-- Sudah terhubung --}}
                <div class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/20 rounded-2xl border border-emerald-100 dark:border-emerald-900/30 mb-4">
                    <span class="material-symbols-rounded text-emerald-500">verified</span>
                    <div class="flex-1">
                        <p class="text-sm font-black text-emerald-700 dark:text-emerald-400">Akun Google Terhubung</p>
                        <p class="text-xs text-emerald-600/70 dark:text-emerald-500 font-medium">Anda bisa login menggunakan akun Google</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('google.unlink') }}">
                    @csrf
                    <button type="submit"
                        class="w-full py-3.5 border-2 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-2xl font-black text-sm hover:border-red-300 hover:text-red-500 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span class="material-symbols-rounded text-base">link_off</span>
                        Lepaskan Akun Google
                    </button>
                </form>
            @else
                {{-- Belum terhubung --}}
                @if(config('services.google.client_id'))
                <a href="{{ route('google.link') }}"
                    class="w-full flex items-center justify-center gap-3 py-3.5 px-6 border-2 border-gray-200 dark:border-slate-700 rounded-2xl font-bold text-gray-700 dark:text-slate-300 text-sm
                           hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-950/20 active:scale-[0.98] transition-all duration-200">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Hubungkan Akun Google
                </a>
                @else
                <p class="text-center text-xs text-slate-400 italic py-2">Fitur login Google belum dikonfigurasi.</p>
                @endif
            @endif
        </div>

        <!-- Logout Card -->
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

    <!-- Bottom Navigation -->
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