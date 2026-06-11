/// <reference types="@sveltejs/kit" />
/// <reference no-default-lib="true"/>
/// <reference lib="esnext" />
/// <reference lib="webworker" />

declare let self: ServiceWorkerGlobalScope;

import { build, files, version } from '$service-worker';

// Satisfy Vite PWA injectManifest requirement
// @ts-ignore
const precacheList = self.__WB_MANIFEST;


const CACHE = `cache-${version}`;
const ASSETS = [
	...build, // the app itself
	...files  // everything in `static`
];

self.addEventListener('install', (event) => {
	async function addFilesToCache() {
		const cache = await caches.open(CACHE);
		await cache.addAll(ASSETS);
	}
	event.waitUntil(addFilesToCache());
});

self.addEventListener('activate', (event) => {
	async function deleteOldCaches() {
		for (const key of await caches.keys()) {
			if (key !== CACHE) await caches.delete(key);
		}
	}
	event.waitUntil(deleteOldCaches());
});

self.addEventListener('fetch', (event) => {
	if (event.request.method !== 'GET') return;
	async function respond() {
		const url = new URL(event.request.url);
		const cache = await caches.open(CACHE);
		if (ASSETS.includes(url.pathname)) {
			return cache.match(event.request) as Promise<Response>;
		}
		try {
			const response = await fetch(event.request);
			if (response.status === 200) {
				cache.put(event.request, response.clone());
			}
			return response;
		} catch {
			return cache.match(event.request) as Promise<Response>;
		}
	}
	event.respondWith(respond());
});

self.addEventListener('push', (event) => {
  const data = event.data?.json() ?? {};
  const title = data.title || 'Plan Trip Tracker';
  const options = {
    body: data.body || 'You have a new notification.',
    icon: data.icon || '/favicon.svg',
    data: data.data || {},
    vibrate: [200, 100, 200]
  };

  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const url = event.notification.data?.url || '/';
  
  event.waitUntil(
    self.clients.matchAll({ type: 'window' }).then((windowClients) => {
      // Check if there is already a window/tab open with the target URL
      for (var i = 0; i < windowClients.length; i++) {
        var client = windowClients[i];
        if (client.url === url && 'focus' in client) {
          return client.focus();
        }
      }
      // If not, open a new window
      if (self.clients.openWindow) {
        return self.clients.openWindow(url);
      }
    })
  );
});
