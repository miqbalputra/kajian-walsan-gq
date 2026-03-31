{{-- PWA Install Banner & Service Worker Registration --}}
<div id="pwa-install-banner"
    style="display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%); z-index:9999; width:calc(100% - 32px); max-width:420px;"
    x-data="pwaInstall()" x-init="init()" x-show="showBanner" x-cloak
    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8"
    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-8">

    <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden"
        style="box-shadow: 0 25px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);">

        {{-- Header with gradient --}}
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-5 py-4 flex items-center gap-4">
            <div
                class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/30">
                <img src="/icons/icon-96x96.png" alt="Kajian Walsan" class="w-10 h-10 rounded-lg">
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-white font-bold text-base">Install Kajian Walsan</h3>
                <p class="text-white/80 text-xs mt-0.5 truncate">Akses cepat dari layar utama</p>
            </div>
            <button @click="dismissBanner()"
                class="p-1.5 rounded-xl hover:bg-white/10 transition-colors text-white/70 hover:text-white flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-5 py-4">
            <div class="flex items-center gap-3 text-sm text-gray-600 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-primary-500 text-lg">speed</span>
                    <span>Lebih cepat</span>
                </div>
                <span class="text-gray-300">•</span>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-primary-500 text-lg">wifi_off</span>
                    <span>Offline</span>
                </div>
                <span class="text-gray-300">•</span>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-primary-500 text-lg">notifications_active</span>
                    <span>Notifikasi</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="dismissBanner()"
                    class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm text-gray-500 hover:bg-gray-100 transition-colors">
                    Nanti Saja
                </button>
                <button @click="installPwa()"
                    class="flex-1 py-2.5 px-4 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 shadow-lg shadow-primary-500/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded text-lg">download</span>
                    Install
                </button>
            </div>
        </div>
    </div>
</div>

{{-- iOS Install Guide Modal --}}
<div x-data="{ showIosGuide: false }" @show-ios-guide.window="showIosGuide = true" x-show="showIosGuide" x-cloak
    class="fixed inset-0 z-[10000] flex items-end justify-center p-4"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showIosGuide = false"></div>
    <div class="relative bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl mb-4"
        x-transition:enter="transition ease-out duration-300 delay-100"
        x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <img src="/icons/icon-96x96.png" alt="Kajian Walsan" class="w-10 h-10 rounded-lg">
            </div>
            <h3 class="text-lg font-bold text-gray-900">Install di Safari</h3>
            <p class="text-sm text-gray-500 mt-1">Ikuti langkah berikut:</p>
        </div>
        <div class="space-y-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-primary-600">1</span>
                </div>
                <p class="text-sm text-gray-700">Tekan tombol <strong>Share</strong> <svg
                        xmlns="http://www.w3.org/2000/svg" class="inline w-5 h-5 text-blue-500" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                    </svg> di bawah browser</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-primary-600">2</span>
                </div>
                <p class="text-sm text-gray-700">Scroll dan pilih <strong>"Add to Home Screen"</strong></p>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-primary-600">3</span>
                </div>
                <p class="text-sm text-gray-700">Tekan <strong>"Add"</strong> untuk menginstall</p>
            </div>
        </div>
        <button @click="showIosGuide = false"
            class="w-full py-3 rounded-2xl bg-primary-500 text-white font-bold text-sm hover:bg-primary-600 transition-colors">
            Mengerti
        </button>
    </div>
</div>

{{-- Service Worker Registration & PWA Install Logic --}}
<script>
    // Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then((registration) => {
                    console.log('[PWA] Service Worker registered:', registration.scope);

                    // Check for updates periodically
                    setInterval(() => {
                        registration.update();
                    }, 60 * 60 * 1000); // Check every hour
                })
                .catch((error) => {
                    console.log('[PWA] Service Worker registration failed:', error);
                });
        });
    }

    // Alpine.js PWA Install Component
    function pwaInstall() {
        return {
            showBanner: false,
            deferredPrompt: null,
            isIos: false,
            isInstalled: false,

            init() {
                // Check if already installed
                this.isInstalled = window.matchMedia('(display-mode: standalone)').matches
                    || window.navigator.standalone === true;

                if (this.isInstalled) return;

                // Check if user dismissed recently
                const dismissed = localStorage.getItem('pwa-install-dismissed');
                if (dismissed) {
                    const dismissedAt = parseInt(dismissed);
                    const threeDays = 3 * 24 * 60 * 60 * 1000;
                    if (Date.now() - dismissedAt < threeDays) return;
                }

                // Detect iOS
                this.isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

                // Listen for beforeinstallprompt (Chrome, Edge, etc.)
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    // Show banner after a short delay
                    setTimeout(() => {
                        this.showBanner = true;
                        document.getElementById('pwa-install-banner').style.display = 'block';
                    }, 2000);
                });

                // For iOS, show banner after delay
                if (this.isIos) {
                    setTimeout(() => {
                        this.showBanner = true;
                        document.getElementById('pwa-install-banner').style.display = 'block';
                    }, 3000);
                }

                // Listen for successful install
                window.addEventListener('appinstalled', () => {
                    this.showBanner = false;
                    this.deferredPrompt = null;
                    localStorage.removeItem('pwa-install-dismissed');
                    console.log('[PWA] App installed successfully');
                });
            },

            async installPwa() {
                if (this.isIos) {
                    // Show iOS install guide
                    window.dispatchEvent(new CustomEvent('show-ios-guide'));
                    this.showBanner = false;
                    return;
                }

                if (this.deferredPrompt) {
                    this.deferredPrompt.prompt();
                    const { outcome } = await this.deferredPrompt.userChoice;
                    console.log('[PWA] Install outcome:', outcome);
                    if (outcome === 'accepted') {
                        this.showBanner = false;
                    }
                    this.deferredPrompt = null;
                }
            },

            dismissBanner() {
                this.showBanner = false;
                localStorage.setItem('pwa-install-dismissed', Date.now().toString());
            }
        };
    }
</script>