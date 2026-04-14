self.addEventListener('push', function(event) {
    if (event.data) {
        let data = {
            title: 'Kajian Walsan',
            body: 'Ada pembaruan!',
            icon: '/icons/icon-192x192.png',
            url: '/wali-santri'
        };

        try {
            const parsed = event.data.json();
            data = { ...data, ...parsed };
        } catch (e) {
            data.body = event.data.text();
        }

        const options = {
            body: data.body,
            icon: data.icon,
            badge: '/icons/icon-96x96.png',
            vibrate: [100, 50, 100],
            data: {
                dateOfArrival: Date.now(),
                primaryKey: '2',
                url: data.url
            }
        };

        event.waitUntil(
            self.registration.showNotification(data.title, options)
        );
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/')
    );
});
