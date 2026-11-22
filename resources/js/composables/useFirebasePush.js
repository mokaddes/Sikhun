import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { useNotificationStore } from '@/Stores/notifications';

const TOKEN_STORAGE_KEY = 'sikhun_fcm_token';

// Shared singleton state across components (banner, profile settings, foreground toast)
const permission = ref(typeof window !== 'undefined' && 'Notification' in window ? Notification.permission : 'default');
const isLoading = ref(false);
const error = ref('');
const registeredToken = ref(typeof window !== 'undefined' ? (localStorage.getItem(TOKEN_STORAGE_KEY) || '') : '');
const foregroundNotice = ref(null);

let messagingInstance = null;
let foregroundListenerAttached = false;
let foregroundToastTimer = null;

function isBrowserPushCapable() {
    return typeof window !== 'undefined'
        && 'Notification' in window
        && 'serviceWorker' in navigator
        && 'PushManager' in window;
}

export function useFirebasePush() {
    const page = usePage();

    const fcmConfig = computed(() => page.props?.site?.fcm || {});
    const isConfigured = computed(() => Boolean(fcmConfig.value?.configured));
    const isSupported = computed(() => isBrowserPushCapable());
    const isSubscribed = computed(() => permission.value === 'granted' && Boolean(registeredToken.value));

    async function initMessaging() {
        if (messagingInstance) return messagingInstance;
        if (!isConfigured.value || !isBrowserPushCapable()) return null;

        const [{ initializeApp, getApps }, { getMessaging, isSupported: isFcmSupported }] = await Promise.all([
            import('firebase/app'),
            import('firebase/messaging'),
        ]);

        if (!(await isFcmSupported())) {
            return null;
        }

        const cfg = fcmConfig.value;
        const app = getApps().length > 0
            ? getApps()[0]
            : initializeApp({
                apiKey: cfg.api_key,
                authDomain: cfg.auth_domain,
                projectId: cfg.project_id,
                storageBucket: cfg.storage_bucket,
                messagingSenderId: cfg.messaging_sender_id,
                appId: cfg.app_id,
            });

        messagingInstance = getMessaging(app);
        return messagingInstance;
    }

    async function attachForegroundListener() {
        if (foregroundListenerAttached) return;
        const messaging = await initMessaging();
        if (!messaging) return;

        const { onMessage } = await import('firebase/messaging');
        foregroundListenerAttached = true;

        onMessage(messaging, (payload) => {
            const title = payload?.notification?.title || payload?.data?.title || 'Sikhun.com';
            const body = payload?.notification?.body || payload?.data?.body || '';
            const url = payload?.fcmOptions?.link || payload?.data?.url || '/dashboard';
            const notifId = payload?.data?.notification_id ? Number(payload.data.notification_id) : null;

            // Update the notification bell store if not already added via Reverb
            try {
                const store = useNotificationStore();
                const alreadyExists = notifId && store.items.some((n) => Number(n.id) === notifId);
                if (!alreadyExists) {
                    store.items.unshift({
                        id: notifId || Date.now(),
                        title,
                        body,
                        data: payload?.data || {},
                        read_at: null,
                        created_at: new Date().toISOString(),
                    });
                    store.unreadCount++;
                }
            } catch {
                // Store unavailable
            }

            // Show foreground toast
            foregroundNotice.value = { title, body, url };
            if (foregroundToastTimer) clearTimeout(foregroundToastTimer);
            foregroundToastTimer = setTimeout(() => {
                foregroundNotice.value = null;
            }, 6000);
        });
    }

    async function fetchAndSyncToken() {
        const messaging = await initMessaging();
        if (!messaging) return null;

        const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', {
            scope: '/',
        });
        await navigator.serviceWorker.ready;

        const { getToken } = await import('firebase/messaging');
        const token = await getToken(messaging, {
            vapidKey: fcmConfig.value.vapid_key,
            serviceWorkerRegistration: registration,
        });

        if (!token) {
            throw new Error('Could not obtain Firebase Cloud Messaging token.');
        }

        await axios.post('/notifications/push-subscription', { token });
        registeredToken.value = token;
        localStorage.setItem(TOKEN_STORAGE_KEY, token);

        await attachForegroundListener();
        return token;
    }

    /**
     * Requests browser permission (must be called from a user click/gesture)
     * and registers the device's FCM token with the backend.
     */
    async function enablePush() {
        error.value = '';
        if (!isBrowserPushCapable()) {
            error.value = 'Push notifications are not supported in this browser.';
            return false;
        }
        if (!isConfigured.value) {
            error.value = 'Firebase Push Notifications are not configured yet.';
            return false;
        }

        isLoading.value = true;
        try {
            const result = await Notification.requestPermission();
            permission.value = result;

            if (result !== 'granted') {
                return false;
            }

            await fetchAndSyncToken();
            return true;
        } catch (err) {
            console.error('Firebase push enable failed:', err);
            error.value = err?.message || 'Could not enable push notifications.';
            return false;
        } finally {
            isLoading.value = false;
        }
    }

    /**
     * Silently refreshes the FCM token and attaches the foreground listener
     * when the student has already granted browser notification permission.
     */
    async function syncIfGranted() {
        if (!isBrowserPushCapable() || !isConfigured.value) return;
        permission.value = Notification.permission;
        if (permission.value !== 'granted') return;

        try {
            await fetchAndSyncToken();
        } catch (err) {
            console.warn('Silent FCM token sync skipped:', err?.message || err);
        }
    }

    /**
     * Unsubscribes this browser device from FCM push notifications.
     */
    async function disablePush() {
        error.value = '';
        isLoading.value = true;
        try {
            const tokenToRemove = registeredToken.value || localStorage.getItem(TOKEN_STORAGE_KEY) || '';
            if (messagingInstance) {
                try {
                    const { deleteToken } = await import('firebase/messaging');
                    await deleteToken(messagingInstance);
                } catch {
                    // Ignore client-side deleteToken failure
                }
            }
            await axios.delete('/notifications/push-subscription', {
                data: { token: tokenToRemove },
            });
            registeredToken.value = '';
            localStorage.removeItem(TOKEN_STORAGE_KEY);
            return true;
        } catch (err) {
            console.error('Firebase push disable failed:', err);
            error.value = err?.message || 'Could not disable push notifications.';
            return false;
        } finally {
            isLoading.value = false;
        }
    }

    function dismissForegroundNotice() {
        foregroundNotice.value = null;
    }

    return {
        permission,
        isSupported,
        isConfigured,
        isSubscribed,
        isLoading,
        error,
        registeredToken,
        foregroundNotice,
        enablePush,
        disablePush,
        syncIfGranted,
        dismissForegroundNotice,
    };
}
