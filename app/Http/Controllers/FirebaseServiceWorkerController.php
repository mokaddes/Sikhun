<?php

namespace App\Http\Controllers;

use App\Services\PushNotificationService;
use App\Services\SiteSettingService;
use Illuminate\Http\Response;

/**
 * Dynamically serves /firebase-messaging-sw.js using the public Firebase Web SDK
 * configuration stored in Site Settings so admins can configure FCM from the
 * Admin Panel without editing static files or rebuilding assets.
 */
class FirebaseServiceWorkerController extends Controller
{
    public function __invoke(PushNotificationService $push, SiteSettingService $settings): Response
    {
        $config = $push->webConfig();
        $faviconPath = $settings->get('site_favicon');
        $iconUrl = $faviconPath ? asset('storage/'.$faviconPath) : asset('favicon.svg');

        $firebaseConfigJson = json_encode([
            'apiKey' => $config['api_key'],
            'authDomain' => $config['auth_domain'],
            'projectId' => $config['project_id'],
            'storageBucket' => $config['storage_bucket'],
            'messagingSenderId' => $config['messaging_sender_id'],
            'appId' => $config['app_id'],
        ], JSON_UNESCAPED_SLASHES);

        $iconJson = json_encode($iconUrl, JSON_UNESCAPED_SLASHES);

        $js = <<<JS
/* Sikhun.com — Firebase Cloud Messaging Service Worker */
importScripts('https://www.gstatic.com/firebasejs/11.6.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/11.6.0/firebase-messaging-compat.js');

const FIREBASE_CONFIG = {$firebaseConfigJson};
const DEFAULT_ICON = {$iconJson};

self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

if (FIREBASE_CONFIG.apiKey && FIREBASE_CONFIG.projectId && FIREBASE_CONFIG.messagingSenderId && FIREBASE_CONFIG.appId) {
    firebase.initializeApp(FIREBASE_CONFIG);
    const messaging = firebase.messaging();

    messaging.onBackgroundMessage((payload) => {
        // When FCM payload already includes a top-level notification object,
        // Firebase compat SDK displays it automatically in the background.
        // Only manually display for data-only messages to prevent duplicates.
        if (payload.notification && payload.notification.title) {
            return;
        }

        const data = payload.data || {};
        const title = data.title || 'Sikhun.com — শিখুন';
        const options = {
            body: data.body || 'আপনার জন্য নতুন নোটিফিকেশন এসেছে।',
            icon: data.icon || DEFAULT_ICON,
            badge: DEFAULT_ICON,
            data: {
                url: data.url || data.action_url || '/dashboard',
            },
            tag: data.notification_id ? 'sikhun-' + data.notification_id : 'sikhun-push-' + Date.now(),
            renotify: true,
        };

        self.registration.showNotification(title, options);
    });
}

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const fcmLink = event.notification?.data?.FCM_MSG?.fcmOptions?.link
        || event.notification?.data?.FCM_MSG?.data?.url
        || event.notification?.data?.url
        || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            for (const client of windowClients) {
                if ('focus' in client && client.url.includes(self.location.origin)) {
                    if ('navigate' in client) {
                        return client.navigate(fcmLink).then((c) => c ? c.focus() : client.focus());
                    }
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(fcmLink);
            }
        })
    );
});
JS;

        return response($js, 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Service-Worker-Allowed' => '/',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
