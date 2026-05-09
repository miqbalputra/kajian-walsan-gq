<x-layouts.app title="Login" :force-light="true">
    {{-- deploy-marker: pwa-inline-install-ff1db75 --}}
    <div
        class="min-h-screen min-h-[100svh] flex items-center justify-center p-4 bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-700 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div
                class="absolute top-[10%] right-[5%] w-96 h-96 bg-primary-400/30 rounded-full blur-[120px] animate-pulse">
            </div>
            <div class="absolute bottom-[10%] left-[5%] w-80 h-80 bg-secondary-400/20 blur-[100px] animate-pulse"
                style="animation-delay: 2s"></div>
        </div>

        <div class="relative z-10 w-full max-w-md" x-data="{ loading: false }">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-xl rounded-3xl mb-4 border border-white/20 shadow-2xl">
                    <span class="material-symbols-rounded text-5xl text-white">mosque</span>
                </div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Presensi Kajian</h1>
                <p class="text-white/70 text-sm mt-2 font-medium tracking-wide">Sistem Informasi Kehadiran</p>
            </div>

            <div
                class="bg-white/95 backdrop-blur-2xl rounded-[2.5rem] p-8 shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-white/50">
                <div class="flex mb-8 bg-gray-100/80 p-1.5 rounded-2xl shadow-inner">
                    <div
                        class="flex-1 py-3 px-4 bg-white shadow-md text-primary-600 rounded-xl font-bold text-sm flex items-center justify-center gap-2">
                        <span class="material-symbols-rounded text-lg">login</span>
                        Masuk Akun
                    </div>
                </div>

                @if ($errors->has('google'))
                    <div class="mb-5 p-3 bg-red-50 border border-red-100 rounded-2xl text-sm text-red-600 font-medium">
                        {{ $errors->first('google') }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-5 p-3 bg-green-50 border border-green-100 rounded-2xl text-sm text-green-700 font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mb-6">
                    @include('components.pwa-inline-install')
                </div>

                <form method="POST" action="{{ route('login') }}" @submit="loading = true">
                    @csrf

                    <div class="mb-5">
                        <label for="username"
                            class="block text-[13px] font-bold text-gray-700 mb-2 ml-1 uppercase tracking-wider">
                            Username atau Email
                        </label>
                        <div class="group relative">
                            <span
                                class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors text-xl">alternate_email</span>
                            <input type="text" id="username" name="username" value="{{ old('username') }}"
                                class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-gray-900 font-medium"
                                placeholder="Username atau email" required autofocus autocomplete="username">
                        </div>
                        @error('username')
                            <p class="text-red-500 text-xs mt-2 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password"
                            class="block text-[13px] font-bold text-gray-700 mb-2 ml-1 uppercase tracking-wider">Kata
                            Sandi</label>
                        <div class="group relative" x-data="{ showPassword: false }">
                            <span
                                class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors text-xl">lock</span>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                                class="w-full pl-12 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-gray-900 font-medium"
                                placeholder="Masukkan kata sandi" required autocomplete="current-password">
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

                    <div class="flex justify-end mb-6">
                        <a href="{{ route('password.request') }}"
                            class="text-sm font-bold text-primary-600 hover:text-primary-700 transition-colors">
                            Lupa password?
                        </a>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-4 px-6 rounded-2xl font-bold text-lg
                               hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-500/30
                               active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-3 shadow-xl shadow-primary-600/20"
                        :disabled="loading">
                        <span class="material-symbols-rounded" x-show="!loading">login</span>
                        <span x-show="loading" style="display:none">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <span x-text="loading ? 'Memproses...' : 'Masuk Akun'">Masuk Akun</span>
                    </button>
                </form>

                <div class="my-6 flex items-center gap-3">
                    <div class="h-px flex-1 bg-gray-200"></div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">atau</span>
                    <div class="h-px flex-1 bg-gray-200"></div>
                </div>

                <a href="{{ route('google.redirect') }}"
                    class="w-full py-3.5 px-6 rounded-2xl border border-gray-200 bg-white text-gray-700 font-bold text-sm
                           hover:bg-gray-50 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-3 shadow-sm">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4"
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853"
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05"
                            d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84z" />
                        <path fill="#EA4335"
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.31 9.14 5.38 12 5.38z" />
                    </svg>
                    Masuk dengan Google
                </a>
                <p class="mt-3 text-center text-xs font-medium leading-relaxed text-gray-500">
                    Untuk login lebih cepat, gunakan akun Google yang emailnya sudah terdaftar.
                </p>
            </div>

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
