const CACHE_NAME = 'login-app-cache-v1';
const urlsToCache = [
  '/',
  '/logindr.php',
  '/dashboard.php',
  '/manifest.json',
  '/styles.css',  // Include any other CSS files
  '/scripts.js',  // Include any other JavaScript files
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png'
];

// Install the service worker
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Fetch event
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        // Cache hit - return response
        if (response) {
          return response;
        }
        return fetch(event.request);
      })
  );
});
