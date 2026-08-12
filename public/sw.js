/* ============================================================
 * LeadHub Service Worker
 *
 * Responsibilities:
 *   1. Web-push notifications (existing behaviour — unchanged).
 *   2. Offline shell cache for the /mobile/* mobile-first entry
 *      points so reps can open the capture form on a dead-signal
 *      trade-show floor.
 *   3. Background-sync queue: lead captures submitted while
 *      offline are persisted to IndexedDB and replayed when the
 *      device reconnects.
 *
 * Intentionally minimal so the entire script runs on any shared
 * host with no build step — plain JS, no imports.
 * ============================================================ */

/* ============================================================
 * 1. WEB PUSH  (original behaviour)
 * ============================================================ */

self.addEventListener('push', function (event) {
    if (!event.data) return;

    let data = {};
    try {
        data = event.data.json();
    } catch (e) {
        data = { title: 'LeadHub', body: event.data.text(), url: '/' };
    }

    const title   = data.title   || 'LeadHub';
    const options = {
        body:  data.body  || '',
        icon:  data.icon  || '/icon-192.png',
        badge: data.badge || '/icon-192.png',
        data:  { url: data.url || '/admin' },
        tag:   data.tag   || 'leadhub-notification',
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.url) || '/admin';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

/* ============================================================
 * 2. OFFLINE SHELL CACHE  (mobile capture / scan)
 * ============================================================ */

const CACHE_VERSION = 'lh-v1';
const SHELL_URLS = [
    '/mobile/capture',
    '/mobile/scan',
    '/mobile/offline',
    '/favicon.svg',
    '/site.webmanifest',
];

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_VERSION)
            .then(function (cache) { return cache.addAll(SHELL_URLS); })
            .catch(function () { /* best-effort */ })
    );
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys.filter(function (k) { return k !== CACHE_VERSION; })
                    .map(function (k) { return caches.delete(k); })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', function (event) {
    var req = event.request;
    if (req.method !== 'GET') return;

    var url;
    try { url = new URL(req.url); } catch (_) { return; }
    if (url.origin !== self.location.origin) return;

    // Only intercept the stripped-down /mobile/* shells.  Leave
    // /admin/* alone — Filament relies on CSRF + live polling +
    // up-to-the-second data that a naive cache-first would break.
    var interceptable =
        url.pathname.indexOf('/mobile/') === 0 ||
        url.pathname === '/favicon.svg' ||
        url.pathname === '/site.webmanifest';

    if (!interceptable) return;

    event.respondWith(
        caches.match(req).then(function (cached) {
            if (cached) return cached;
            return fetch(req).then(function (resp) {
                if (resp && resp.ok) {
                    var copy = resp.clone();
                    caches.open(CACHE_VERSION)
                        .then(function (c) { c.put(req, copy); })
                        .catch(function () {});
                }
                return resp;
            }).catch(function () { return caches.match('/mobile/offline'); });
        })
    );
});

/* ============================================================
 * 3. BACKGROUND SYNC  (queued lead captures)
 * ============================================================ */

self.addEventListener('sync', function (event) {
    if (event.tag === 'lh-lead-sync') {
        event.waitUntil(flushQueue());
    }
});

self.addEventListener('message', function (event) {
    if (event.data && event.data.type === 'lh-flush') {
        flushQueue();
    }
});

function flushQueue() {
    return openDb().then(function (db) {
        var tx = db.transaction('queue', 'readonly');
        var store = tx.objectStore('queue');
        return reqPromise(store.getAll()).then(function (items) {
            db.close();
            return items;
        });
    }).then(function (items) {
        var chain = Promise.resolve();
        items.forEach(function (item) {
            chain = chain.then(function () {
                return fetch(item.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-LeadHub-Queued': '1'
                    },
                    body: JSON.stringify(item.payload)
                }).then(function (resp) {
                    if (resp && resp.ok) return removeItem(item.id);
                }).catch(function () { /* still offline */ });
            });
        });
        return chain;
    });
}

function openDb() {
    return new Promise(function (resolve, reject) {
        var req = indexedDB.open('lh-queue', 1);
        req.onupgradeneeded = function () {
            var db = req.result;
            if (!db.objectStoreNames.contains('queue')) {
                db.createObjectStore('queue', { keyPath: 'id', autoIncrement: true });
            }
        };
        req.onsuccess = function () { resolve(req.result); };
        req.onerror   = function () { reject(req.error);   };
    });
}

function reqPromise(req) {
    return new Promise(function (resolve, reject) {
        req.onsuccess = function () { resolve(req.result); };
        req.onerror   = function () { reject(req.error);   };
    });
}

function removeItem(id) {
    return openDb().then(function (db) {
        return new Promise(function (resolve) {
            var tx = db.transaction('queue', 'readwrite');
            tx.objectStore('queue').delete(id);
            tx.oncomplete = function () { db.close(); resolve(); };
        });
    });
}
