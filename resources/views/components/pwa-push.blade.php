{{-- Script untuk menangani Web Push Notification --}}
<script>
    // Utility to convert Base64URL to Uint8Array for VAPID
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    async function initPushNotification(silent = false) {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            console.warn('[Push] Browser tidak mendukung push notification');
            if (!silent) alert('Browser Anda tidak mendukung fitur Notifikasi/Pengingat.');
            return;
        }

        try {
            let permission = Notification.permission;
            if (permission === 'default' && !silent) {
                permission = await Notification.requestPermission();
            }

            if (permission !== 'granted') {
                console.warn('[Push] Izin ditolak');
                if (!silent) alert('Izin notifikasi ditolak. Anda tidak akan mendapatkan pengingat kajian.');
                return;
            }

            console.log('[Push] Izin diberikan, memastikan Service Worker aktif...');
            
            // Register explicitly to avoid hanging on .ready if it failed previously
            const registration = await navigator.serviceWorker.register('/sw.js');
            await navigator.serviceWorker.ready;

            const vapidPublicKey = '{{ config('webpush.vapid_public_key') }}';
            if (!vapidPublicKey) {
                console.error('[Push] VAPID Public Key belum disetting di .env');
                alert('Sistem push belum dikonfigurasi oleh server (VAPID Kosong).');
                return;
            }

            let subscription = await registration.pushManager.getSubscription();
            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
                });
            }

            // Send to our server
            const subData = subscription.toJSON();
            const response = await fetch('/push-subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    endpoint: subData.endpoint,
                    keys: subData.keys
                })
            });

            if (response.ok) {
                console.log('[Push] Berhasil berlangganan!');
                if (!silent) alert('Pendaftaran notifikasi berhasil! Anda akan menerima pengingat kajian.');
            } else {
                console.error('[Push] Gagal simpan ke server');
                if (!silent) alert('Gagal menyimpan langganan ke server. Error Code: ' + response.status);
            }

        } catch (e) {
            console.error('[Push] Error init:', e);
            if (!silent) alert('Gagal mengaktifkan notifikasi: ' + e.message);
        }
    }

    if ('Notification' in window && Notification.permission === 'granted') {
        window.addEventListener('load', () => initPushNotification(true));
    }

    // Fungsi test notification (local)
    window.testLocalNotification = async () => {
        try {
            if (Notification.permission === 'default') {
                await Notification.requestPermission();
            }

            if (Notification.permission === 'granted') {
                const registration = await navigator.serviceWorker.register('/sw.js');
                await navigator.serviceWorker.ready;
                registration.showNotification('Kajian Walsan', {
                    body: 'Tes notifikasi pengingat kajian berhasil!',
                    icon: '/icons/icon-192x192.png',
                    badge: '/icons/icon-96x96.png',
                    vibrate: [100, 50, 100],
                    data: {
                        url: '/wali-santri'
                    }
                });
            } else {
                alert('Anda memblokir izin notifikasi browser.');
            }
        } catch (e) {
            alert('Test Error: ' + e.message);
        }
    };
</script>
