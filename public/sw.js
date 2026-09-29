/**
 * ASystem PWA Service Worker
 * Version: 1.0.0
 * Provides offline caching, desktop standalone app support, and network-first navigation
 */

const CACHE_NAME = 'asystem-pwa-v1.0.0';
const OFFLINE_URL = '/offline.html';

const PRECACHE_ASSETS = [
    OFFLINE_URL,
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/icon.svg',
    '/icons/favicon-32x32.png',
    '/favicon.ico'
];

// Install Event: Pre-cache essential offline shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[PWA SW] Pre-cache partial warning:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// Activate Event: Clean up outdated caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        console.log('[PWA SW] Removing old cache:', key);
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch Event: Handle requests with appropriate strategies
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // 1. Only handle GET requests. Pass POST/PUT/DELETE through to network
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // 2. Ignore non-HTTP/HTTPS and sensitive administrative scripts
    if (!url.protocol.startsWith('http')) {
        return;
    }
    if (url.pathname.includes('deploy.php') || url.pathname.includes('cron_') || url.pathname.includes('/api/health')) {
        return;
    }

    // 3. Navigation Requests (HTML Pages): Network-First with Offline Fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    // Cache successful navigation responses for dynamic offline support
                    if (networkResponse && networkResponse.status === 200) {
                        const copy = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, copy).catch(() => {});
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // Try to get cached page first
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Fallback to offline.html
                    const offlinePage = await caches.match(OFFLINE_URL);
                    return offlinePage || new Response('Anda sedang offline.', {
                        headers: { 'Content-Type': 'text/plain; charset=utf-8' }
                    });
                })
        );
        return;
    }

    // 4. Static Assets (CSS, JS, Fonts, Images, Icons): Stale-While-Revalidate
    const isStaticAsset = (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font' ||
        url.pathname.startsWith('/icons/') ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com') ||
        url.hostname.includes('cdnjs.cloudflare.com') ||
        url.hostname.includes('cdn.jsdelivr.net') ||
        url.hostname.includes('cdn.tailwindcss.com')
    );

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                const fetchPromise = fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const copy = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, copy).catch(() => {});
                        });
                    }
                    return networkResponse;
                }).catch(() => null);

                // Return cached version immediately if available, otherwise wait for network
                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // 5. Default: Pass through to network
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});
