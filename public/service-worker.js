/**
 * LeadHub service worker.
 *
 * Strategy:
 *   - Static assets (JS/CSS/images): cache-first, fallback network
 *   - HTML / API: network-first, fallback cache (so users see fresh
 *     data when online; degraded read-only access when offline)
 *   - Bumping CACHE_VERSION evicts the old cache on activate
 *
 * Tightly scoped — does NOT cache /admin pages because Filament 4
 * is auth-gated + Livewire-driven; offline UX for those pages would
 * be confusing.  Caching focused on landing-page + form-page assets
 * so a tenant's public lead-capture forms keep working when their
 * lead's connection drops.
 */

const CACHE_VERSION = 'leadhub-v1';
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/favicon.ico',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_VERSION)
            .then((cache) => cache.addAll(STATIC_ASSETS).catch(() => {}))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((k) => k !== CACHE_VERSION).map((k) => caches.delete(k))
        )).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Only handle GETs — never cache mutations
    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    // Don't cache cross-origin (CDN, analytics, etc.)
    if (url.origin !== self.location.origin) return;

    // Skip auth-gated panels — they need fresh data and mostly use
    // Livewire's own state machine
    if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/super-admin')) {
        return;
    }

    // Skip API endpoints — would mask real connectivity issues
    if (url.pathname.startsWith('/api/')) {
        return;
    }

    // Static assets → cache-first
    if (/\.(js|css|png|jpg|jpeg|gif|svg|ico|woff2?|ttf|eot)$/.test(url.pathname)) {
        event.respondWith(
            caches.match(request).then((cached) => {
                return cached || fetch(request).then((response) => {
                    if (response && response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_VERSION).then((cache) => cache.put(request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }

    // HTML / public pages → network-first
    event.respondWith(
        fetch(request)
            .then((response) => {
                if (response && response.ok && response.type === 'basic') {
                    const clone = response.clone();
                    caches.open(CACHE_VERSION).then((cache) => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => caches.match(request).then((cached) => cached || caches.match('/')))
    );
});

// Receive web-push notifications dispatched from App\Jobs\SendBrowserPush
// (PushSubscription model already exists — this is the front-end half)
self.addEventListener('push', (event) => {
    if (!event.data) return;

    let payload = {};
    try {
        payload = event.data.json();
    } catch {
        payload = { title: 'LeadHub', body: event.data.text() };
    }

    const title = payload.title || 'LeadHub';
    const options = {
        body:  payload.body || '',
        icon:  payload.icon  || '/icons/icon-192.png',
        badge: payload.badge || '/icons/icon-192.png',
        data:  { url: payload.url || '/admin' },
        tag:   payload.tag || 'leadhub',
        requireInteraction: payload.requireInteraction || false,
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/admin';
    event.waitUntil(
        self.clients.matchAll({ type: 'window' }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow(targetUrl);
            }
        })
    );
});
