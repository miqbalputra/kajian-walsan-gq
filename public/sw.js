const CACHE_NAME = 'kajian-walsan-v2';
const OFFLINE_URL = '/offline.html';

// Static assets to pre-cache (app shell)
const PRECACHE_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// Install event - pre-cache essential assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Pre-caching app shell');
                // Use Promise.allSettled to prevent single file failure from breaking the SW installation
                return Promise.allSettled(
                    PRECACHE_ASSETS.map(url => 
                        cache.add(url).catch(err => console.warn(`[SW] Failed to cache ${url}:`, err))
                    )
                );
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => {
                        console.log('[SW] Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - necessary for PWA install criteria
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests (POST for Livewire, forms, etc.)
    if (request.method !== 'GET') return;

    // Skip Livewire requests - always go to network
    if (url.pathname.startsWith('/livewire')) return;

    // Skip hot module replacement (Vite dev)
    if (url.pathname.includes('/@vite') || url.pathname.includes('/__vite') || url.port === '5173') return;

    // Navigation requests - network first with offline fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Cache successful navigation responses
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                    return response;
                })
                .catch(() => {
                    // Try cache first, then offline page
                    return caches.match(request)
                        .then((cached) => cached || caches.match(OFFLINE_URL));
                })
        );
        return;
    }

    // Static assets (CSS, JS, images, fonts) - stale while revalidate
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.match(/\.(css|js|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|ico|webp)$/)
    ) {
        event.respondWith(
            caches.match(request).then((cached) => {
                const fetchPromise = fetch(request)
                    .then((response) => {
                        if (response && response.status === 200) {
                            const responseClone = response.clone();
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(request, responseClone);
                            });
                        }
                        return response;
                    })
                    .catch(() => cached);
                return cached || fetchPromise;
            })
        );
        return;
    }
});

// Push Notification Event
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

// Notification Click Event
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const urlToOpen = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((windowClients) => {
                for (let i = 0; i < windowClients.length; i++) {
                    const client = windowClients[i];
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});
