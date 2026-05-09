{{-- PWA Install Banner & Service Worker Registration --}}
<div id="pwa-install-banner"
    style="position:fixed; bottom:20px; left:50%; transform:translateX(-50%); z-index:9999; width:calc(100% - 32px); max-width:430px;"
    x-data="pwaInstall()" x-init="init()" x-show="showBanner" x-cloak
    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8"
    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-8">

    <div class="bg-white rounded-3xl shadow-2xl border border-emerald-100 overflow-hidden"
        style="box-shadow: 0 25px 60px rgba(0,0,0,0.18), 0 0 0 1px rgba(16,185,129,0.12);">

        <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-5 py-4 flex items-center gap-4">
            <div
                class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/30">
                <img src="/icons/icon-96x96.png" alt="Kajian Walsan" class="w-10 h-10 rounded-lg">
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-white font-bold text-base">Install di HP</h3>
                <p class="text-white/85 text-xs mt-0.5 truncate">Akses cepat dan notifikasi otomatis</p>
            </div>
            <button @click="dismissBanner()" type="button" aria-label="Tutup ajakan install"
                class="p-1.5 rounded-xl hover:bg-white/10 transition-colors text-white/70 hover:text-white flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="px-5 py-4">
            <p class="text-sm font-medium leading-relaxed text-gray-700 mb-4">
                Install di HP untuk akses lebih cepat dan menerima notifikasi pengumuman otomatis.
            </p>

            <div class="grid grid-cols-3 gap-2 text-xs text-gray-600 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-primary-500 text-lg">speed</span>
                    <span>Cepat</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-primary-500 text-lg">home_app_logo</span>
                    <span>Ikon HP</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-primary-500 text-lg">notifications_active</span>
                    <span>Notifikasi</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="dismissBanner()" type="button"
                    class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm text-gray-500 hover:bg-gray-100 transition-colors">
                    Nanti Saja
                </button>
                <button @click="installPwa()" type="button"
                    class="flex-1 py-2.5 px-4 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 shadow-lg shadow-primary-500/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded text-lg">download</span>
                    Install Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Android/Chrome Install Guide Modal --}}
<div x-data="{ showBrowserGuide: false }" @show-browser-install-guide.window="showBrowserGuide = true"
    x-show="showBrowserGuide" x-cloak class="fixed inset-0 z-[10000] flex items-end justify-center p-4"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showBrowserGuide = false"></div>
    <div class="relative bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl mb-4"
        x-transition:enter="transition ease-out duration-300 delay-100"
        x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <img src="/icons/icon-96x96.png" alt="Kajian Walsan" class="w-10 h-10 rounded-lg">
            </div>
            <h3 class="text-lg font-bold text-gray-900">Install Kajian Walsan</h3>
            <p class="text-sm text-gray-500 mt-1">Akses cepat dan notifikasi otomatis dari HP.</p>
        </div>
        <div class="space-y-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-primary-600">1</span>
                </div>
                <p class="text-sm text-gray-700">Tekan menu browser <strong>titik tiga</strong> di kanan atas.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-primary-600">2</span>
                </div>
                <p class="text-sm text-gray-700">Pilih <strong>Install app</strong> atau <strong>Add to Home screen</strong>.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-primary-600">3</span>
                </div>
                <p class="text-sm text-gray-700">Tekan <strong>Install</strong>, lalu buka dari ikon di layar HP.</p>
            </div>
        </div>
        <button @click="showBrowserGuide = false" type="button"
            class="w-full py-3 rounded-2xl bg-primary-500 text-white font-bold text-sm hover:bg-primary-600 transition-colors">
            Mengerti
        </button>
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
            <p class="text-sm text-gray-500 mt-1">Akses cepat dan notifikasi otomatis dari HP.</p>
        </div>
        <div class="space-y-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-primary-600">1</span>
                </div>
                <p class="text-sm text-gray-700">Tekan tombol <strong>Share</strong> di bawah browser Safari.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-primary-600">2</span>
                </div>
                <p class="text-sm text-gray-700">Scroll dan pilih <strong>Add to Home Screen</strong>.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-primary-600">3</span>
                </div>
                <p class="text-sm text-gray-700">Tekan <strong>Add</strong> untuk menginstall.</p>
            </div>
        </div>
        <button @click="showIosGuide = false" type="button"
            class="w-full py-3 rounded-2xl bg-primary-500 text-white font-bold text-sm hover:bg-primary-600 transition-colors">
            Mengerti
        </button>
    </div>
</div>

{{-- Global beforeinstallprompt handler --}}
<script>
    window.pwaDeferredPrompt = null;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        window.pwaDeferredPrompt = e;
        window.dispatchEvent(new CustomEvent('pwa-prompt-available'));
    });
</script>

{{-- Service Worker Registration & PWA Install Logic --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then((registration) => {
                    console.log('[PWA] Service Worker registered:', registration.scope);
                    setInterval(() => {
                        registration.update();
                    }, 60 * 60 * 1000);
                })
                .catch((error) => {
                    console.log('[PWA] Service Worker registration failed:', error);
                });
        });
    }

    function pwaInstall() {
        return {
            showBanner: false,
            isIos: false,
            isInstalled: false,

            init() {
                this.isInstalled = window.matchMedia('(display-mode: standalone)').matches
                    || window.navigator.standalone === true;

                if (this.isInstalled) return;

                const dismissed = localStorage.getItem('pwa-install-dismissed-v2');
                if (dismissed) {
                    const dismissedAt = parseInt(dismissed);
                    const twelveHours = 12 * 60 * 60 * 1000;
                    if (Date.now() - dismissedAt < twelveHours) return;
                }

                this.isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

                setTimeout(() => {
                    this.showBanner = true;
                }, 1200);

                window.addEventListener('pwa-prompt-available', () => {
                    this.showBanner = true;
                });

                window.addEventListener('appinstalled', () => {
                    this.showBanner = false;
                    window.pwaDeferredPrompt = null;
                    localStorage.removeItem('pwa-install-dismissed-v2');
                    console.log('[PWA] App installed successfully');
                });
            },

            async installPwa() {
                if (this.isIos) {
                    window.dispatchEvent(new CustomEvent('show-ios-guide'));
                    this.showBanner = false;
                    return;
                }

                if (window.pwaDeferredPrompt) {
                    window.pwaDeferredPrompt.prompt();
                    const { outcome } = await window.pwaDeferredPrompt.userChoice;
                    console.log('[PWA] Install outcome:', outcome);
                    if (outcome === 'accepted') {
                        this.showBanner = false;
                    }
                    window.pwaDeferredPrompt = null;
                    return;
                }

                window.dispatchEvent(new CustomEvent('show-browser-install-guide'));
                this.showBanner = false;
            },

            dismissBanner() {
                this.showBanner = false;
                localStorage.setItem('pwa-install-dismissed-v2', Date.now().toString());
            }
        };
    }
</script>
