<x-layouts.app title="Reset Password">
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

        <div class="relative z-10 w-full max-w-md" x-data="{ showPassword: false, showPasswordConfirm: false }">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-xl rounded-3xl mb-4 border border-white/20 shadow-2xl">
                    <span class="material-symbols-rounded text-5xl text-white">password</span>
                </div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Reset Password</h1>
                <p class="text-white/70 text-sm mt-2 font-medium tracking-wide">Buat password baru untuk akun Anda</p>
            </div>

            <!-- Glassmorphism Card -->
            <div
                class="bg-white/95 backdrop-blur-2xl rounded-[2.5rem] p-8 shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-white/50">

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <!-- Hidden Token -->
                    <input type="hidden" name="token" value="{{ $token ?? $request->route('token') }}">

                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email"
                            class="block text-[13px] font-bold text-gray-700 mb-2 ml-1 uppercase tracking-wider">Email</label>
                        <div class="group relative">
                            <span
                                class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors text-xl">mail</span>
                            <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}"
                                class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-gray-900 font-medium"
                                placeholder="Masukkan email Anda" required autofocus>
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-2 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <label for="password"
                            class="block text-[13px] font-bold text-gray-700 mb-2 ml-1 uppercase tracking-wider">Password
                            Baru</label>
                        <div class="group relative">
                            <span
                                class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors text-xl">lock</span>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                                class="w-full pl-12 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-gray-900 font-medium"
                                placeholder="Masukkan password baru" required>
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

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation"
                            class="block text-[13px] font-bold text-gray-700 mb-2 ml-1 uppercase tracking-wider">Konfirmasi
                            Password</label>
                        <div class="group relative">
                            <span
                                class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors text-xl">lock_clock</span>
                            <input :type="showPasswordConfirm ? 'text' : 'password'" id="password_confirmation"
                                name="password_confirmation"
                                class="w-full pl-12 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none text-gray-900 font-medium"
                                placeholder="Ulangi password baru" required>
                            <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <span class="material-symbols-rounded text-xl"
                                    x-text="showPasswordConfirm ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-2 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-4 px-6 rounded-2xl font-bold text-lg
                               hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-500/30
                               active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-3 shadow-xl shadow-primary-600/20">
                        <span class="material-symbols-rounded">check_circle</span>
                        Reset Password
                    </button>
                </form>

                <!-- Back to Login -->
                <div class="mt-8 text-center">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 text-gray-500 hover:text-primary-600 font-semibold transition-colors group">
                        <span
                            class="material-symbols-rounded text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        Kembali ke Login
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-white/50 text-xs">
                    &copy; {{ date('Y') }} Kelompok Tahfidz Griya Qur'an "Tunas Ilmu"
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
