<x-layouts.app title="Login" :force-light="true">
    <div
        class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-700 overflow-hidden">
        <!-- Modern Decorative Background -->
        <div class="absolute inset-0 z-0">
            <div
                class="absolute top-[10%] right-[5%] w-96 h-96 bg-primary-400/30 rounded-full blur-[120px] animate-pulse">
            </div>
            <div class="absolute bottom-[10%] left-[5%] w-80 h-80 bg-secondary-400/20 rounded-full blur-[100px] animate-pulse"
                style="animation-delay: 2s"></div>
        </div>

        <div class="relative z-10 w-full max-w-md" x-data="{ showQrLogin: false, loading: false }">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-xl rounded-3xl mb-4 border border-white/20 shadow-2xl">
                    <span class="material-symbols-rounded text-5xl text-white">mosque</span>
                </div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Presensi Kajian</h1>
                <p class="text-white/70 text-sm mt-2 font-medium tracking-wide">Sistem Informasi Kehadiran</p>
            </div>

            <!-- Glassmorphism Login Card -->
            <div
                class="bg-white/95 backdrop-blur-2xl rounded-[2.5rem] p-8 shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-white/50">
                <!-- Tab Buttons -->
                <div class="flex mb-8 bg-gray-100/80 p-1.5 rounded-2xl shadow-inner">
                    <button @click="showQrLogin = false"
                        :class="!showQrLogin ? 'bg-white shadow-md text-primary-600' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2">
                        <span class="material-symbols-rounded text-lg">login</span>
                        Akun
                    </button>
                    <button @click="showQrLogin = true"
                        :class="showQrLogin ? 'bg-white shadow-md text-primary-600' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2">
                        <span class="material-symbols-rounded text-lg">qr_code_scanner</span>
                        QR Code
                    </button>
                </div>

                <!-- Email/Password Login Form -->
                <div x-show="!showQrLogin" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <form method="POST" action="{{ route('login') }}" @submit="loading = true">
                        @csrf

                        <!-- Username or Email -->
                        <div class="mb-5">
                            <label for="username"
                                class="block text-[13px] font-bold text-gray-700 mb-2 ml-1 uppercase tracking-wider">Username
                                atau Email</label>
                            <div class="group relative">
                                <span
                                    class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors text-xl">alternate_email</span>
                                <input type="text" id="username" name="username" value="{{ old('username') }}"
                                    class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-gray-900 font-medium"
                                    placeholder="Username atau alamat email" required autofocus>
                            </div>
                            @error('username')
                                <p class="text-red-500 text-xs mt-2 ml-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-6">
                            <label for="password"
                                class="block text-[13px] font-bold text-gray-700 mb-2 ml-1 uppercase tracking-wider">Kata
                                Sandi</label>
                            <div class="group relative" x-data="{ showPassword: false }">
                                <span
                                    class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors text-xl">lock</span>
                                <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                                    class="w-full pl-12 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-gray-900 font-medium"
                                    placeholder="••••••••" required>
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <span class="material-symbols-rounded text-xl"
                                        x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-2 ml-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between mb-8">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <div class="relative flex items-center">
                                    <input type="checkbox" name="remember"
                                        class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-gray-300 checked:bg-primary-500 checked:border-primary-500 transition-all">
                                    <span
                                        class="material-symbols-rounded absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-sm pointer-events-none">check</span>
                                </div>
                                <span
                                    class="text-sm text-gray-600 font-medium group-hover:text-gray-900 transition-colors">Ingat
                                    saya</span>
                            </label>
                            @php
                                $waNumber = \App\Models\Setting::get('admin_whatsapp', config('kajian.admin_whatsapp', '6281234567890'));
                                $waMessage = urlencode("Assalamu'alaikum Admin,\n\nSaya lupa password akun saya.\n\nNama: \nUsername: \nNama Santri: \n\nMohon bantuannya untuk reset password. Terima kasih.");
                            @endphp
                            <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" target="_blank"
                                class="text-sm text-primary-600 hover:text-primary-700 font-bold inline-flex items-center gap-1">
                                <span class="material-symbols-rounded text-base">support_agent</span>
                                Lupa password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-4 px-6 rounded-2xl font-bold text-lg
                                   hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-500/30
                                   active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-3 shadow-xl shadow-primary-600/20"
                            :disabled="loading">
                            <span class="material-symbols-rounded" x-show="!loading">login</span>
                            <span x-show="loading">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                            <span x-text="loading ? 'Memproses...' : 'Masuk sekarang'"></span>
                        </button>

                        <!-- Divider -->
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-400 font-medium">atau masuk dengan</span>
                            </div>
                        </div>

                        <!-- Google Login Button -->
                        @if(config('services.google.client_id'))
                        <a href="{{ route('google.redirect') }}"
                            class="w-full flex items-center justify-center gap-3 py-3.5 px-6 border-2 border-gray-200 rounded-2xl font-bold text-gray-700 text-sm
                                   hover:border-gray-300 hover:bg-gray-50 active:scale-[0.98] transition-all duration-200 shadow-sm">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Masuk dengan Google
                        </a>
                        @endif

                        @error('google')
                            <p class="text-red-500 text-xs mt-3 ml-1 font-medium text-center">{{ $message }}</p>
                        @enderror
                    </form>
                </div>

                <!-- QR Code Login Section -->
                <div x-show="showQrLogin" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0" class="text-center">
                    <div class="py-6">
                        <div class="relative inline-flex mb-6">
                            <div
                                class="absolute inset-0 bg-secondary-400 rounded-3xl blur-2xl opacity-20 animate-pulse">
                            </div>
                            <div
                                class="relative inline-flex items-center justify-center w-36 h-36 bg-gray-50 border-2 border-dashed border-gray-200 rounded-[2rem] text-gray-400">
                                <span class="material-symbols-rounded text-6xl">qr_code_scanner</span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Scan QR Code Anda</h3>
                        <p class="text-gray-500 text-sm mb-8 px-4 leading-relaxed font-medium">
                            Gunakan Kartu Wali Santri atau QR Code digital Anda untuk masuk secara instan.
                        </p>
                        <a href="{{ route('qr.login') }}"
                            class="inline-flex items-center justify-center gap-3 bg-secondary-600 text-white py-4 px-10 rounded-2xl font-bold text-lg
                                  hover:bg-secondary-700 active:scale-[0.98] transition-all shadow-xl shadow-secondary-600/20">
                            <span class="material-symbols-rounded">photo_camera</span>
                            Buka Kamera
                        </a>
                    </div>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-10">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 text-white/80 hover:text-white font-bold text-sm transition-colors group">
                    <div
                        class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                        <span class="material-symbols-rounded text-lg">arrow_back</span>
                    </div>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>