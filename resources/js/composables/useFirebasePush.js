import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { useNotificationStore } from '@/Stores/notifications';

const TOKEN_STORAGE_KEY = 'sikhun_fcm_token';
const OPT_OUT_KEY = 'sikhun_fcm_opt_out';
const AUTO_PROMPT_SESSION_KEY = 'sikhun_fcm_auto_prompted';

// Shared singleton state across components (banner, profile settings, foreground toast)
const permission = ref(typeof window !== 'undefined' && 'Notification' in window ? Notification.permission : 'default');
const isLoading = ref(false);
const error = ref('');
const registeredToken = ref(typeof window !== 'undefined' ? (localStorage.getItem(TOKEN_STORAGE_KEY) || '') : '');
const isOptedOut = ref(typeof window !== 'undefined' ? localStorage.getItem(OPT_OUT_KEY) === '1' : false);
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

function formatPushError(err, locale = 'bn') {
    const msg = String(err?.message || err || '');
    if (msg.toLowerCase().includes('push service not available') || err?.name === 'AbortError') {
        return locale === 'en'
            ? 'Push service is unavailable in this browser (e.g. Incognito mode, Brave with Google Push disabled, or an embedded browser). Please use standard Chrome, Edge, or Firefox.'
            : 'এই ব্রাউজারে পুশ সার্ভিস সাপোর্ট করছে না (যেমন: Incognito মোড, Brave ব্রাউজারে Google Push বন্ধ থাকা বা এমবেডেড ব্রাউজার)। অনুগ্রহ করে সাধারণ Chrome, Edge বা Firefox ব্যবহার করুন।';
    }
    return msg || (locale === 'en' ? 'Could not enable push notifications.' : 'পুশ নোটিফিকেশন চালু করা যায়নি।');
}

export function useFirebasePush() {
    const page = usePage();

    const fcmConfig = computed(() => page.props?.site?.fcm || {});
    const locale = computed(() => page.props?.locale || 'bn');
    const isConfigured = computed(() => Boolean(fcmConfig.value?.configured));
    const isSupported = computed(() => isBrowserPushCapable());
    const isSubscribed = computed(() => !isOptedOut.value && permission.value === 'granted' && Boolean(registeredToken.value));

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
            if (isOptedOut.value) return;

            const title = payload?.notification?.title || payload?.data?.title || 'Sikhun.com';
            const body = payload?.notification?.body || payload?.data?.body || '';
            const url = payload?.fcmOptions?.link || payload?.data?.url || '/dashboard';
            const notifId = payload?.data?.notification_id ? Number(payload.data.notification_id) : null;

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
     * Requests browser permission and registers the device's FCM token with the backend.
     * Clears any previous opt-out flag so notifications are active again.
     */
    async function enablePush({ silent = false } = {}) {
        if (!silent) error.value = '';
        if (!isBrowserPushCapable()) {
            if (!silent) error.value = formatPushError('Push notifications are not supported in this browser.', locale.value);
            return false;
        }
        if (!isConfigured.value) {
            if (!silent) error.value = 'Firebase Push Notifications are not configured yet.';
            return false;
        }

        isOptedOut.value = false;
        localStorage.removeItem(OPT_OUT_KEY);

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
            if (!silent) {
                console.error('Firebase push enable failed:', err);
                error.value = formatPushError(err, locale.value);
            } else {
                console.warn('Silent Firebase push auto-enable skipped:', err?.message || err);
            }
            return false;
        } finally {
            isLoading.value = false;
        }
    }

    /**
     * Default-enabled flow for new users:
     * - If the user explicitly disabled push (isOptedOut), do nothing.
     * - If permission is already 'granted', silently sync token & attach foreground listener.
     * - If permission is 'default' (new user), automatically trigger the browser permission prompt once per session.
     */
    async function autoEnableForNewUser() {
        if (!isBrowserPushCapable() || !isConfigured.value) return;
        if (isOptedOut.value) return;

        permission.value = Notification.permission;

        if (permission.value === 'granted') {
            try {
                await fetchAndSyncToken();
            } catch (err) {
                console.warn('Silent FCM token sync skipped:', err?.message || err);
            }
            return;
        }

        if (permission.value === 'default' && sessionStorage.getItem(AUTO_PROMPT_SESSION_KEY) !== '1') {
            sessionStorage.setItem(AUTO_PROMPT_SESSION_KEY, '1');
            await enablePush({ silent: true });
        }
    }

    async function syncIfGranted() {
        if (!isBrowserPushCapable() || !isConfigured.value || isOptedOut.value) return;
        permission.value = Notification.permission;
        if (permission.value !== 'granted') return;

        try {
            await fetchAndSyncToken();
        } catch (err) {
            console.warn('Silent FCM token sync skipped:', err?.message || err);
        }
    }

    /**
     * Unsubscribes this browser device from FCM push notifications and marks user opted-out
     * so it stays disabled until they manually re-enable it.
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
            isOptedOut.value = true;
            localStorage.setItem(OPT_OUT_KEY, '1');
            return true;
        } catch (err) {
            console.error('Firebase push disable failed:', err);
            error.value = formatPushError(err, locale.value);
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
        isOptedOut,
        isLoading,
        error,
        registeredToken,
        foregroundNotice,
        enablePush,
        disablePush,
        autoEnableForNewUser,
        syncIfGranted,
        dismissForegroundNotice,
    };
}
