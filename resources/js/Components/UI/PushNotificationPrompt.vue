<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from '@/i18n';
import { BellAlertIcon, XMarkIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const { t, locale } = useI18n();

const isSupported = ref(false);
const permission = ref('default');
const isDismissed = ref(false);
const isLoading = ref(false);
const showToast = ref(false);
const toastMessage = ref('');

const DISMISS_KEY = 'sikhun_push_prompt_dismissed_until';

onMounted(() => {
    if (typeof window === 'undefined') return;

    isSupported.value = 'Notification' in window && 'serviceWorker' in navigator;
    if (!isSupported.value) return;

    permission.value = Notification.permission;

    // If already granted, ensure SW is registered
    if (permission.value === 'granted') {
        registerServiceWorker();
        return;
    }

    // Check if dismissed recently (7 days)
    const dismissedUntil = localStorage.getItem(DISMISS_KEY);
    if (dismissedUntil && Date.now() < parseInt(dismissedUntil, 10)) {
        isDismissed.value = true;
    }
});

async function registerServiceWorker() {
    try {
        const reg = await navigator.serviceWorker.register('/sw.js');
        return reg;
    } catch (e) {
        console.warn('SW registration failed:', e);
        return null;
    }
}

async function requestNotificationPermission() {
    if (!isSupported.value) return;

    isLoading.value = true;
    try {
        const result = await Notification.requestPermission();
        permission.value = result;

        if (result === 'granted') {
            const reg = await registerServiceWorker();

            let subscriptionData = {
                browser: navigator.userAgent,
                granted_at: new Date().toISOString(),
            };

            if (reg && reg.pushManager) {
                try {
                    const existingSub = await reg.pushManager.getSubscription();
                    if (existingSub) {
                        subscriptionData = existingSub.toJSON();
                    }
                } catch (subErr) {
                    // Push manager might require VAPID key for new subscription
                }
            }

            // Sync with backend
            try {
                await axios.post('/notifications/push-subscription', {
                    subscription: subscriptionData,
                });
            } catch (apiErr) {
                console.warn('Could not save push subscription to server', apiErr);
            }

            toastMessage.value = locale.value === 'en'
                ? 'Push notifications enabled successfully!'
                : 'পুশ নোটিফিকেশন সফলভাবে চালু করা হয়েছে!';
            showToast.value = true;
            setTimeout(() => {
                showToast.value = false;
            }, 4500);
        } else if (result === 'denied') {
            // Dismiss for 14 days
            localStorage.setItem(DISMISS_KEY, (Date.now() + 14 * 24 * 60 * 60 * 1000).toString());
        }
    } catch (err) {
        console.error('Notification permission error:', err);
    } finally {
        isLoading.value = false;
    }
}

function dismissPrompt() {
    isDismissed.value = true;
    // Dismiss for 7 days
    localStorage.setItem(DISMISS_KEY, (Date.now() + 7 * 24 * 60 * 60 * 1000).toString());
}
</script>

<template>
    <!-- Feedback Toast -->
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform translate-y-3 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform translate-y-3 opacity-0"
    >
        <div
            v-if="showToast"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-xl"
        >
            <CheckCircleIcon class="h-5 w-5 shrink-0 text-white" />
            <span>{{ toastMessage }}</span>
        </div>
    </Transition>

    <!-- Push Notification Prompt Banner -->
    <div
        v-if="isSupported && permission === 'default' && !isDismissed"
        class="anim-rise mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-[var(--secondary)]/30 bg-gradient-to-r from-[var(--secondary)]/10 via-[var(--surface)] to-[var(--primary)]/10 p-4 shadow-sm"
    >
        <div class="flex items-center gap-3">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[var(--secondary)]/20 text-xl text-[var(--secondary)]">
                <BellAlertIcon class="h-5 w-5" />
            </span>
            <div>
                <p class="font-heading text-sm font-bold text-[var(--text)]">
                    {{ locale === 'en' ? 'Turn on Push Notifications' : 'Push Notification চালু করুন' }}
                </p>
                <p class="mt-0.5 text-xs text-[var(--text-muted)]">
                    {{ locale === 'en'
                        ? 'Get instant reminders for upcoming exams, scheduled study sessions, and job circulars.'
                        : 'পরীক্ষার রিমাইন্ডার এবং নতুন সার্কুলারের ইনস্ট্যান্ট নোটিফিকেশন পেতে নোটিফিকেশন চালু রাখুন।' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                :disabled="isLoading"
                class="btn btn-primary btn-sm rounded-xl px-4 font-bold shadow-sm"
                @click="requestNotificationPermission"
            >
                <span v-if="isLoading">{{ locale === 'en' ? 'Enabling...' : 'চালু হচ্ছে...' }}</span>
                <span v-else>{{ locale === 'en' ? 'Enable Notifications' : 'চালু করুন' }}</span>
            </button>

            <button
                type="button"
                class="btn btn-ghost btn-sm rounded-xl p-1.5 text-[var(--text-muted)] hover:text-[var(--text)]"
                :title="locale === 'en' ? 'Later' : 'পরে'"
                @click="dismissPrompt"
            >
                <XMarkIcon class="h-4 w-4" />
            </button>
        </div>
    </div>
</template>
