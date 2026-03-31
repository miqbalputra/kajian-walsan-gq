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