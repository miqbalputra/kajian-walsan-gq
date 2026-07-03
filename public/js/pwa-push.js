(function () {
    const PUSH_SCRIPT_VERSION = '20260519-push-v4';

    window.kajianPushScriptVersion = PUSH_SCRIPT_VERSION;

    function getMetaContent(name) {
        return document.querySelector(`meta[name="${name}"]`)?.getAttribute('content') || '';
    }

    function isLikelyInAppBrowser() {
        return /FBAN|FBAV|Instagram|Line|MicroMessenger|TikTok|Telegram|; wv\)|\bwv\b/i.test(navigator.userAgent);
    }

    function waitWithTimeout(promise, timeoutMs, message) {
        return Promise.race([
            promise,
            new Promise((_, reject) => setTimeout(() => reject(new Error(message)), timeoutMs)),
        ]);
    }

    window.urlBase64ToUint8Array = function (base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    };

    window.initPushNotification = async function (silent = false) {
        if (!('Notification' in window) || !('serviceWorker' in navigator) || !('PushManager' in window)) {
            const message = isLikelyInAppBrowser()
                ? 'Browser di dalam aplikasi seperti Telegram/WhatsApp biasanya tidak mendukung notifikasi. Buka aplikasi ini lewat Chrome, Edge, atau Safari lalu coba lagi.'
                : 'Browser Anda tidak mendukung fitur Notifikasi/Pengingat.';

            console.warn('[Push] Browser tidak mendukung push notification');
            if (!silent) alert(message);

            return { ok: false, permission: 'unsupported', message };
        }

        try {
            if (!window.isSecureContext) {
                const message = 'Notifikasi hanya bisa aktif di HTTPS atau localhost. Pastikan alamat aplikasi memakai https://.';

                if (!silent) alert(message);

                return { ok: false, permission: Notification.permission, message };
            }

            let permission = Notification.permission;

            if (permission === 'default' && !silent) {
                permission = await Notification.requestPermission();
            }

            if (permission !== 'granted') {
                const message = permission === 'denied'
                    ? 'Izin notifikasi diblokir. Buka pengaturan browser/situs, izinkan notifikasi untuk aplikasi ini, lalu coba lagi.'
                    : 'Izin notifikasi belum diberikan. Anda belum akan menerima pengingat kajian.';

                console.warn('[Push] Izin tidak diberikan:', permission);
                if (!silent) alert(message);

                return { ok: false, permission, message };
            }

            const vapidPublicKey = getMetaContent('webpush-vapid-public-key');

            if (!vapidPublicKey) {
                const message = 'Sistem push belum dikonfigurasi oleh server (VAPID kosong). Jalankan config:clear setelah mengisi .env.';

                console.error('[Push] VAPID Public Key belum disetting di server');
                if (!silent) alert(message);

                return { ok: false, permission, message };
            }

            console.log('[Push] Izin diberikan, memastikan Service Worker aktif...');

            const registration = await navigator.serviceWorker.register('/sw.js');

            if (typeof registration.update === 'function') {
                registration.update().catch((error) => {
                    console.warn('[Push] Gagal memeriksa update Service Worker:', error);
                });
            }

            await waitWithTimeout(
                navigator.serviceWorker.ready,
                10000,
                'Service Worker belum aktif. Tutup lalu buka ulang aplikasi, kemudian tekan Aktifkan lagi.'
            );

            let subscription = await registration.pushManager.getSubscription();

            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: window.urlBase64ToUint8Array(vapidPublicKey),
                });
            }

            const subData = subscription.toJSON();
            const response = await fetch('/push-subscription', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getMetaContent('csrf-token'),
                },
                body: JSON.stringify({
                    endpoint: subData.endpoint,
                    keys: subData.keys,
                }),
            });

            if (response.ok) {
                const message = 'Pendaftaran notifikasi berhasil! Anda akan menerima pengingat kajian.';

                console.log('[Push] Berhasil berlangganan!');
                if (!silent) alert(message);

                return { ok: true, permission, message };
            }

            const errorText = await response.text().catch(() => '');
            const message = 'Gagal menyimpan langganan ke server. Error Code: ' + response.status;

            console.error('[Push] Gagal simpan ke server:', response.status, errorText);
            if (!silent) alert(message);

            return { ok: false, permission, message };
        } catch (e) {
            const message = isLikelyInAppBrowser()
                ? 'Gagal mengaktifkan notifikasi. Jika Anda membuka dari Telegram/WhatsApp, buka link ini di Chrome/Edge/Safari atau install aplikasinya ke layar utama.'
                : 'Gagal mengaktifkan notifikasi: ' + e.message;

            console.error('[Push] Error init:', e);
            if (!silent) alert(message);

            return { ok: false, permission: 'error', message };
        }
    };

    window.testLocalNotification = async function () {
        try {
            if (Notification.permission === 'default') {
                await Notification.requestPermission();
            }

            if (Notification.permission !== 'granted') {
                alert('Anda memblokir izin notifikasi browser.');
                return;
            }

            const registration = await navigator.serviceWorker.register('/sw.js');
            await navigator.serviceWorker.ready;

            registration.showNotification('Presensi Wali Santri', {
                body: 'Tes notifikasi pengingat kajian berhasil!',
                icon: '/icons/icon-192x192.png',
                badge: '/icons/icon-96x96.png',
                vibrate: [100, 50, 100],
                data: {
                    url: '/wali-santri',
                },
            });
        } catch (e) {
            alert('Test Error: ' + e.message);
        }
    };

    if ('Notification' in window && Notification.permission === 'granted') {
        window.addEventListener('load', () => window.initPushNotification(true));
    }
})();
