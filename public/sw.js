/**
 * Sikhun.com - Web Push Notification Service Worker
 */

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', (event) => {
    let payload = {};

    try {
        if (event.data) {
            payload = event.data.json();
        }
    } catch (e) {
        payload = {
            body: event.data ? event.data.text() : 'আপনার জন্য নতুন নোটিফিকেশন এসেছে।',
        };
    }

    const title = payload.title || 'Sikhun.com - শিখুন';
    const body = payload.body || 'আপনার জন্য নতুন নোটিফিকেশন এসেছে।';
    const icon = payload.icon || '/images/sikhun-mark.svg';
    const badge = payload.badge || '/images/sikhun-mark.svg';
    const url = payload.url || payload.action_url || '/dashboard';
    const tag = payload.tag || 'sikhun-notification-' + Date.now();

    const options = {
        body: body,
        icon: icon,
        badge: badge,
        data: {
            url: url,
            timestamp: Date.now(),
        },
        tag: tag,
        renotify: true,
        vibrate: [200, 100, 200],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const destinationUrl = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            // Check if there's an existing tab with the site open
            for (let i = 0; i < windowClients.length; i++) {
                const client = windowClients[i];
                if ('focus' in client) {
                    if (client.url.includes(destinationUrl)) {
                        return client.focus();
                    }
                }
            }
            // If window with Sikhun is open, navigate and focus
            if (windowClients.length > 0 && 'focus' in windowClients[0] && 'navigate' in windowClients[0]) {
                return windowClients[0].navigate(destinationUrl).then((c) => c.focus());
            }
            // Otherwise open a new window
            if (clients.openWindow) {
                return clients.openWindow(destinationUrl);
            }
        })
    );
});
