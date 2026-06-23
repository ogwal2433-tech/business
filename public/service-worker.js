self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open('smartbiz-cache').then(function (cache) {
      return cache.addAll([
        '/',
        '/css/app.css',
        '/js/app.js',
        '/images/icons/icon-192x192.png',
        '/images/icons/icon-512x512.png',
        // Add other important files here
      ]);
    })
  );
});

self.addEventListener('fetch', function (event) {
  event.respondWith(
    caches.match(event.request).then(function (response) {
      return response || fetch(event.request);
    })
  );
});
