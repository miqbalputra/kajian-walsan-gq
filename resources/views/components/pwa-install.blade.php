{{-- PWA Install Banner & Service Worker Registration --}}
<div id="pwa-install-banner"
    style="position:fixed; bottom:20px; left:50%; transform:translate(-50%, 24px); z-index:9999; width:calc(100% - 32px); max-width:430px; display:none; opacity:0; transition:opacity .35s ease, transform .35s ease;">
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
            <button id="pwa-dismiss-button" type="button" aria-label="Tutup ajakan install"
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
                <button id="pwa-later-button" type="button"
                    class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm text-gray-500 hover:bg-gray-100 transition-colors">
                    Nanti Saja
                </button>
                <button id="pwa-install-button" type="button"
                    class="flex-1 py-2.5 px-4 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 shadow-lg shadow-primary-500/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span class="material-symbols-rounded text-lg">download</span>
                    Install Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Android/Chrome Install Guide Modal --}}
<div id="pwa-browser-guide" class="fixed inset-0 z-[10000] items-end justify-center p-4"
    style="display:none;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-pwa-close-guide></div>
    <div class="relative bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl mb-4">
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
        <button type="button" data-pwa-close-guide
            class="w-full py-3 rounded-2xl bg-primary-500 text-white font-bold text-sm hover:bg-primary-600 transition-colors">
            Mengerti
        </button>
    </div>
</div>

{{-- iOS Install Guide Modal --}}
<div id="pwa-ios-guide" class="fixed inset-0 z-[10000] items-end justify-center p-4"
    style="display:none;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-pwa-close-ios></div>
    <div class="relative bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl mb-4">
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
        <button type="button" data-pwa-close-ios
            class="w-full py-3 rounded-2xl bg-primary-500 text-white font-bold text-sm hover:bg-primary-600 transition-colors">
            Mengerti
        </button>
    </div>
</div>

<script>
    window.pwaDeferredPrompt = null;
    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        window.pwaDeferredPrompt = event;
        window.dispatchEvent(new CustomEvent('pwa-prompt-available'));
    });

    (function () {
        const dismissedKey = 'pwa-install-dismissed-v3';

        function isInstalled() {
            return window.matchMedia('(display-mode: standalone)').matches
                || window.navigator.standalone === true;
        }

        function isIos() {
            return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        }

        function wasDismissedRecently() {
            const dismissed = localStorage.getItem(dismissedKey);
            if (!dismissed) return false;

            const dismissedAt = parseInt(dismissed, 10);
            const twelveHours = 12 * 60 * 60 * 1000;
            return Number.isFinite(dismissedAt) && Date.now() - dismissedAt < twelveHours;
        }

        function showBanner() {
            const banner = document.getElementById('pwa-install-banner');
            if (!banner || isInstalled() || wasDismissedRecently()) return;

            banner.style.display = 'block';
            requestAnimationFrame(() => {
                banner.style.opacity = '1';
                banner.style.transform = 'translate(-50%, 0)';
            });
        }

        function hideBanner() {
            const banner = document.getElementById('pwa-install-banner');
            if (!banner) return;

            banner.style.opacity = '0';
            banner.style.transform = 'translate(-50%, 24px)';
            setTimeout(() => {
                banner.style.display = 'none';
            }, 350);
        }

        function showModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.style.display = 'flex';
        }

        function hideModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.style.display = 'none';
        }

        async function installPwa() {
            if (isIos()) {
                hideBanner();
                showModal('pwa-ios-guide');
                return;
            }

            if (window.pwaDeferredPrompt) {
                window.pwaDeferredPrompt.prompt();
                const choice = await window.pwaDeferredPrompt.userChoice;
                window.pwaDeferredPrompt = null;
                if (choice.outcome === 'accepted') {
                    hideBanner();
                }
                return;
            }

            hideBanner();
            showModal('pwa-browser-guide');
        }

        function initPwaInstall() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('[PWA] Service Worker registered:', registration.scope);
                        setInterval(() => registration.update(), 60 * 60 * 1000);
                    })
                    .catch((error) => {
                        console.log('[PWA] Service Worker registration failed:', error);
                    });
            }

            document.getElementById('pwa-install-button')?.addEventListener('click', installPwa);
            document.getElementById('pwa-dismiss-button')?.addEventListener('click', () => {
                hideBanner();
                localStorage.setItem(dismissedKey, Date.now().toString());
            });
            document.getElementById('pwa-later-button')?.addEventListener('click', () => {
                hideBanner();
                localStorage.setItem(dismissedKey, Date.now().toString());
            });

            document.querySelectorAll('[data-pwa-close-guide]').forEach((element) => {
                element.addEventListener('click', () => hideModal('pwa-browser-guide'));
            });
            document.querySelectorAll('[data-pwa-close-ios]').forEach((element) => {
                element.addEventListener('click', () => hideModal('pwa-ios-guide'));
            });

            setTimeout(showBanner, 1200);
            window.addEventListener('pwa-prompt-available', showBanner);
            window.addEventListener('appinstalled', () => {
                hideBanner();
                window.pwaDeferredPrompt = null;
                localStorage.removeItem(dismissedKey);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPwaInstall);
        } else {
            initPwaInstall();
        }
    })();
</script>
