<div class="w-full rounded-3xl border border-emerald-100 bg-white/95 p-4 shadow-xl shadow-emerald-950/10">
    <div class="flex items-start gap-3">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-primary-600">
            <span class="material-symbols-rounded text-2xl">install_mobile</span>
        </div>
        <div class="min-w-0 flex-1">
            <h2 class="text-sm font-black text-gray-900">Install aplikasi di HP</h2>
            <p class="mt-1 text-xs font-medium leading-relaxed text-gray-500">
                Akses lebih cepat dan dapatkan notifikasi pengumuman otomatis.
            </p>
        </div>
    </div>

    <button type="button"
        onclick="window.dispatchEvent(new CustomEvent('kajian-pwa-install-clicked'))"
        class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-3 text-sm font-black text-white shadow-lg shadow-primary-600/20 transition active:scale-[0.98]">
        <span class="material-symbols-rounded text-lg">download</span>
        Install Sekarang
    </button>
</div>

<script>
    window.addEventListener('kajian-pwa-install-clicked', async () => {
        const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

        if (isIos) {
            alert('Untuk install di iPhone: tekan tombol Share di Safari, lalu pilih Add to Home Screen.');
            return;
        }

        if (window.pwaDeferredPrompt) {
            window.pwaDeferredPrompt.prompt();
            await window.pwaDeferredPrompt.userChoice;
            window.pwaDeferredPrompt = null;
            return;
        }

        const globalInstallButton = document.getElementById('pwa-install-button');
        if (globalInstallButton) {
            globalInstallButton.click();
            return;
        }

        alert('Untuk install aplikasi: buka menu browser titik tiga, lalu pilih Install app atau Add to Home screen.');
    });
</script>
