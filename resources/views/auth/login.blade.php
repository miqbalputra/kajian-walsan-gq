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

                        <!-- Forgot Password Assistant -->
                        <div class="flex items-center justify-center mb-8">
                            <p class="text-sm text-gray-600 font-medium inline-flex items-center gap-2">
                                <span class="material-symbols-rounded text-base text-primary-500">support_agent</span>
                                <span class="text-primary-600 font-bold">Chat asisten di bawah jika lupa password</span>
                            </p>
                        </div>

                        <!-- Submit Button -->
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
                            <span x-text="loading ? 'Memproses...' : 'Masuk sekarang'">Masuk sekarang</span>
                        </button>


                    </form>
                </div>

                <!-- QR Code Login Section -->
                <div x-show="showQrLogin" style="display:none" x-transition:enter="transition ease-out duration-300"
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

    @push('scripts')
    <!-- Custom Premium Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />
    <style>
        /* Modern & Premium Chat Styling */
        :root {
            --chat-primary: #0f172a; /* Slate 900 */
            --chat-accent: #3b82f6;  /* Blue 500 */
        }

        .n8n-chat-button, .chat-window-toggle, #n8n-chat-button {
            background-image: url('/img/chatbotv2.png') !important;
            background-size: 70% !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-color: #0f172a !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4) !important;
            border: 2px solid rgba(255, 255, 255, 0.1) !important;
            width: 55px !important; /* Perkecil sedikit agar tidak mengganggu */
            height: 55px !important;
            position: fixed !important;
            bottom: 20px !important;
            right: 20px !important;
            z-index: 9999 !important;
        }

        .n8n-chat-button svg, .chat-window-toggle svg, #n8n-chat-button svg {
            display: none !important;
        }

        .n8n-chat-window {
            font-family: 'Outfit', sans-serif !important;
            border-radius: 20px !important;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15) !important;
            width: 350px !important; /* Set lebar yang pas */
            z-index: 9999 !important;
        }
    </style>

    <script type="module">
        import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

        createChat({
            webhookUrl: 'https://automation.tunasilmu.com/webhook/jkbsabhjabchbc82u9u991',
            webhookConfig: {
                method: 'POST'
            },
            showWelcomeScreen: false,
            title: 'Asisten Wali Santri',
            description: 'Pemulihan Akun Otomatis',
            // Gunakan logo dan avatar resmi n8n
            logo: 'https://kajian.griyaquran.web.id/img/chatbotv2.png',
            initialMessages: [
                'Assalamu\'alaikum Bapak/Ibu! 👋',
                'Saya asisten otomatis. Untuk meriset password, silakan ketikkan **16 digit NIK Anda (Bapak/Ibu)** dan **10 digit NIS anak Anda**.',
                'Contoh:\nNIK: 3303XXXXXXXXXXXX NIS: 0123456789'
            ],
            i18n: {
                en: {
                    title: 'Bantuan Login',
                    subtitle: 'Aktif 24 Jam',
                    getStarted: 'Tulis pesan Anda...',
                    inputPlaceholder: 'Tulis pesan Anda...',
                    chatInputPlaceholder: 'Tulis pesan Anda...',
                }
            },
            style: {
                primaryColor: '#0f172a',
                userMessageColor: '#3b82f6',
                backgroundColor: '#ffffff',
            }
        });
    </script>
    <style>
        /* Modern integration for Kajian Walsan theme */
        :root {
            --chat--color-primary: #10B981; /* Primary Emerald */
            --chat--color-primary-shade-50: #059669;
            --chat--color-primary-shade-100: #047857;
            --chat--color-secondary: #0ea5e9;
            --chat--color-secondary-shade-50: #0284c7;
        }
    </style>
    @endpush
</x-layouts.app>