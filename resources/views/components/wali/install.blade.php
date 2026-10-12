<section class="wali-install wali-container" x-data="waliInstall()" @appinstalled.window="update()" aria-labelledby="wali-install-title">
    <div><h2 id="wali-install-title">Pasang aplikasi</h2><p x-show="!installed">Akses Presensi Wali Santri dari layar utama HP. Aplikasi juga tetap dapat digunakan melalui browser.</p><p x-show="installed" x-cloak>Aplikasi sudah terpasang pada perangkat ini.</p></div>
    <button type="button" class="wali-button wali-button-secondary" x-show="!installed" @click="install()" :disabled="busy"><span class="material-symbols-rounded" aria-hidden="true">download</span><span x-text="busy ? 'Memproses…' : 'Pasang aplikasi'">Pasang aplikasi</span></button>
    <div class="wali-install-instructions" x-show="instructions" x-cloak>
        <p><strong>Android/Chrome:</strong> buka menu titik tiga, lalu pilih <strong>Install app</strong> atau <strong>Add to Home screen</strong>.</p>
        <p><strong>iPhone:</strong> buka melalui Safari, tekan Bagikan (Share), lalu pilih <strong>Add to Home Screen</strong>.</p>
    </div>
    <p class="wali-install-instructions" role="status" x-show="message" x-text="message" x-cloak></p>
</section>
