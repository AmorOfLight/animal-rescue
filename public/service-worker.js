const CACHE_NAME = 'animal-shelter-v1';
const ASSETS = [
  '/',
  '/index.html',
  '/styles.css',
  '/app.js',
  '/api/adopt.php',
  '/api/surrender.php',
  '/api/contact.php',
  '/api/donate.php',
  '/icons/an-01-mjE276BopBTy1xjp.png',
  '/icons/ani logo.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(ASSETS))
  );
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => response || fetch(event.request))
  );
});