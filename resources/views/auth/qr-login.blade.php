<x-layouts.app title="Login dengan QR Code" :force-light="true">
    <div
        class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-secondary-600 via-secondary-700 to-primary-700 overflow-hidden text-gray-900">
        <!-- Modern Decorative Background -->
        <div class="absolute inset-0 z-0">
            <div
                class="absolute top-[15%] left-[10%] w-80 h-80 bg-secondary-400/30 rounded-full blur-[100px] animate-pulse">
            </div>
            <div class="absolute bottom-[15%] right-[10%] w-96 h-96 bg-primary-400/20 rounded-full blur-[120px] animate-pulse"
                style="animation-delay: 1.5s"></div>
        </div>

        <div class="relative z-10 w-full max-w-md" x-data="qrScanner()">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-xl rounded-3xl mb-4 border border-white/20 shadow-2xl">
                    <span class="material-symbols-rounded text-5xl text-white">qr_code_scanner</span>
                </div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Scan QR Code</h1>
                <p class="text-white/70 text-sm mt-2 font-medium tracking-wide">Masuk Instan Ke Sistem</p>
            </div>

            <!-- Glassmorphism Scanner Card -->
            <div
                class="bg-white/95 backdrop-blur-2xl rounded-[2.5rem] p-8 shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-white/50">
                <!-- Camera View Container -->
                <div
                    class="relative rounded-[2rem] overflow-hidden bg-black aspect-square mb-6 shadow-2xl ring-4 ring-gray-100/50">
                    <div id="qr-reader" class="w-full h-full"></div>

                    <!-- Scanning Overlay -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-56 h-56 border-2 border-white/30 rounded-3xl relative">
                            <!-- Animated Corners -->
                            <div
                                class="absolute -top-1 -left-1 w-10 h-10 border-t-4 border-l-4 border-secondary-400 rounded-tl-2xl">
                            </div>
                            <div
                                class="absolute -top-1 -right-1 w-10 h-10 border-t-4 border-r-4 border-secondary-400 rounded-tr-2xl">
                            </div>
                            <div
                                class="absolute -bottom-1 -left-1 w-10 h-10 border-b-4 border-l-4 border-secondary-400 rounded-bl-2xl">
                            </div>
                            <div
                                class="absolute -bottom-1 -right-1 w-10 h-10 border-b-4 border-r-4 border-secondary-400 rounded-br-2xl">
                            </div>

                            <!-- Modern Laser Line Animation -->
                            <div
                                class="absolute inset-x-4 h-1 bg-gradient-to-r from-transparent via-secondary-400 to-transparent top-1/2 -translate-y-1/2 animate-[scan_2s_ease-in-out_infinite] shadow-[0_0_15px_rgba(56,189,248,0.8)]">
                            </div>
                        </div>
                    </div>

                    <!-- Loading Overlay -->
                    <div x-show="loading" x-transition
                        class="absolute inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center">
                        <div class="text-center text-white">
                            <div
                                class="w-16 h-16 border-4 border-secondary-400/30 border-t-secondary-400 rounded-full animate-spin mx-auto mb-4">
                            </div>
                            <p class="font-bold tracking-wide">Memverifikasi...</p>
                        </div>
                    </div>

                    <!-- Success Overlay -->
                    <div x-show="success" x-transition
                        class="absolute inset-0 bg-primary-600/90 backdrop-blur-md flex items-center justify-center">
                        <div class="text-center text-white p-6">
                            <div
                                class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 scale-in animate-[bounce_1s_ease-in-out]">
                                <span class="material-symbols-rounded text-6xl">check_circle</span>
                            </div>
                            <p class="text-xl font-extrabold" x-text="successMessage"></p>
                        </div>
                    </div>

                    <!-- Error Overlay -->
                    <div x-show="error" x-transition
                        class="absolute inset-0 bg-red-600/90 backdrop-blur-md flex items-center justify-center">
                        <div class="text-center text-white p-6">
                            <span
                                class="material-symbols-rounded text-6xl mb-2 animate-[shake_0.5s_ease-in-out]">error</span>
                            <p class="font-bold mb-4" x-text="errorMessage"></p>
                            <button @click="resetScanner()"
                                class="px-6 py-2 bg-white text-red-600 rounded-xl text-sm font-bold shadow-lg hover:bg-gray-100 transition-colors">
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Text -->
                <div class="text-center mb-8" x-show="!loading && !success && !error">
                    <p class="text-gray-900 font-bold mb-1">Arahkan Kamera</p>
                    <p class="text-gray-500 text-sm font-medium">Posisikan QR Code di dalam kotak</p>
                </div>

                <!-- Manual Input Section -->
                <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100">
                    <p class="text-center text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Input
                        Manual Kode</p>
                    <form @submit.prevent="submitManualCode()">
                        <div class="flex gap-2">
                            <input type="text" x-model="manualCode"
                                class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none text-center font-bold tracking-[0.2em] uppercase transition-all"
                                placeholder="WS-XXXX-YYYY">
                            <button type="submit"
                                class="w-14 h-14 bg-primary-600 text-white rounded-2xl flex items-center justify-center hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/20 active:scale-95"
                                :disabled="loading">
                                <span class="material-symbols-rounded font-bold">send</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Back to Login -->
            <div class="text-center mt-10">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-2 text-white/80 hover:text-white font-bold text-sm transition-colors group">
                    <div
                        class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                        <span class="material-symbols-rounded text-lg">arrow_back</span>
                    </div>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>

    <!-- Custom Animations for Scanner -->
    <style>
        @keyframes scan {

            0%,
            100% {
                transform: translateY(-110px);
            }

            50% {
                transform: translateY(110px);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }
    </style>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            function qrScanner() {
                return {
                    loading: false,
                    success: false,
                    error: false,
                    successMessage: '',
                    errorMessage: '',
                    manualCode: '',
                    scanner: null,

                    init() {
                        this.startScanner();
                    },

                    async startScanner() {
                        try {
                            this.scanner = new Html5Qrcode("qr-reader");
                            await this.scanner.start(
                                { facingMode: "environment" },
                                { fps: 10, qrbox: { width: 250, height: 250 } },
                                (decodedText) => this.onScanSuccess(decodedText),
                                (error) => { } // Ignore scan errors
                            );
                        } catch (err) {
                            console.error("Camera error:", err);
                            this.errorMessage = "Tidak dapat mengakses kamera";
                            this.error = true;
                        }
                    },

                    async onScanSuccess(qrCode) {
                        if (this.loading || this.success) return;

                        this.loading = true;
                        await this.processQrCode(qrCode);
                    },

                    async submitManualCode() {
                        if (!this.manualCode.trim()) return;

                        this.loading = true;
                        await this.processQrCode(this.manualCode.trim());
                    },

                    async processQrCode(qrCode) {
                        try {
                            const response = await fetch("{{ route('qr.login.process') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({ qr_code: qrCode }),
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.success = true;
                                this.successMessage = `Selamat datang, ${data.user.name}!`;

                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 1500);
                            } else {
                                this.error = true;
                                this.errorMessage = data.message;
                            }
                        } catch (err) {
                            this.error = true;
                            this.errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                        } finally {
                            this.loading = false;
                        }
                    },

                    resetScanner() {
                        this.error = false;
                        this.errorMessage = '';
                        this.manualCode = '';
                    }
                }
            }
        </script>
    @endpush
</x-layouts.app>