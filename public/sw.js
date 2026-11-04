const CACHE_NAME = 'absensi-pwa-v2';

const STATIC_ASSETS = [
    './manifest.json',
    './icons/icon-192x192.png',
    './icons/icon-512x512.png',
    './icons/apple-touch-icon.png',
    './logo.png',
    './logo-navbar.png',
    './logo-login.png',
    './success.mpeg',
    './failed.mpeg',
    './css/filament-theme-emerald.css'
];

// Install Event: Pre-cache static assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS).catch(err => {
                console.warn('PWA: Failed to cache some static assets during install:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// Activate Event: Cleanup old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// Push Event: Menampilkan notifikasi sistem / push
self.addEventListener('push', event => {
    let data = { title: 'Notifikasi Absaka', body: 'Ada pemberitahuan baru.', url: '/admin' };
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }
    const options = {
        body: data.body,
        icon: './icons/icon-192x192.png',
        badge: './icons/icon-192x192.png',
        vibrate: [200, 100, 200],
        data: { url: data.url || '/admin' }
    };
    event.waitUntil(
        self.registration.showNotification(data.title || 'Notifikasi Absaka', options)
    );
});

// Notification Click Event: Fokus atau buka jendela browser ke URL tujuan
self.addEventListener('notificationclick', event => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/admin';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            for (let client of windowClients) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// Fetch Event: Network-first for web pages, Cache-first for static media
self.addEventListener('fetch', event => {
    const request = event.request;

    // Skip non-GET requests (e.g. Livewire POST updates)
    if (request.method !== 'GET') {
        return;
    }

    // Static assets (images, audio, css, fonts)
    if (
        request.destination === 'image' ||
        request.destination === 'audio' ||
        request.destination === 'style' ||
        request.destination === 'font'
    ) {
        event.respondWith(
            caches.match(request).then(cachedResponse => {
                if (cachedResponse) {
                    fetch(request).then(networkResponse => {
                        if (networkResponse && networkResponse.status === 200) {
                            caches.open(CACHE_NAME).then(cache => cache.put(request, networkResponse));
                        }
                    }).catch(() => {});
                    return cachedResponse;
                }
                return fetch(request).then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(request, responseClone));
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // HTML Navigation requests: Network first with cache fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).then(networkResponse => {
                if (networkResponse && networkResponse.status === 200) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, responseClone));
                }
                return networkResponse;
            }).catch(() => {
                return caches.match(request);
            })
        );
    }
});
