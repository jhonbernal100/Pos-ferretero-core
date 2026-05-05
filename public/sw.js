const CACHE = 'pos-ferretero-v1';

const URLS = [
    '/ventas/crear',
    '/ventas',
];

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE).then(cache => cache.addAll(URLS))
    );
});

self.addEventListener('fetch', e => {
    e.respondWith(
        fetch(e.request).catch(() =>
            caches.match(e.request)
        )
    );
});