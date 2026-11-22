<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from '@/i18n';
import { useFirebasePush } from '@/composables/useFirebasePush';
import { BellAlertIcon, XMarkIcon, CheckCircleIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const { locale } = useI18n();
const {
    permission,
    isSupported,
    isConfigured,
    isOptedOut,
    isLoading,
    error,
    foregroundNotice,
    enablePush,
    autoEnableForNewUser,
    dismissForegroundNotice,
} = useFirebasePush();

const isDismissed = ref(false);
const showToast = ref(false);
const toastMessage = ref('');
const toastIsError = ref(false);

const DISMISS_KEY = 'sikhun_push_prompt_dismissed_until';

onMounted(() => {
    if (typeof window === 'undefined') return;
    if (!isSupported.value || !isConfigured.value) return;

    // Default-enabled for new users: auto-prompt & register unless user explicitly disabled it
    if (!isOptedOut.value) {
        autoEnableForNewUser();
    }

    if (permission.value === 'granted' || isOptedOut.value) {
        return;
    }

    const dismissedUntil = localStorage.getItem(DISMISS_KEY);
    if (dismissedUntil && Date.now() < parseInt(dismissedUntil, 10)) {
        isDismissed.value = true;
    }
});

async function handleEnable() {
    const ok = await enablePush();

    if (ok) {
        toastIsError.value = false;
        toastMessage.value = locale.value === 'en'
            ? 'Firebase push notifications enabled!'
            : 'পুশ নোটিফিকেশন সফলভাবে চালু করা হয়েছে!';
        showToast.value = true;
        setTimeout(() => { showToast.value = false; }, 4500);
    } else if (permission.value === 'denied') {
        localStorage.setItem(DISMISS_KEY, (Date.now() + 14 * 24 * 60 * 60 * 1000).toString());
        isDismissed.value = true;
    } else if (error.value) {
        toastIsError.value = true;
        toastMessage.value = error.value;
        showToast.value = true;
        setTimeout(() => { showToast.value = false; }, 5000);
    }
}

function dismissPrompt() {
    isDismissed.value = true;
    localStorage.setItem(DISMISS_KEY, (Date.now() + 7 * 24 * 60 * 60 * 1000).toString());
}
</script>

<template>
    <!-- Foreground Push Notification Toast -->
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform -translate-y-3 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform -translate-y-3 opacity-0"
    >
        <div
            v-if="foregroundNotice"
            class="fixed top-5 right-5 z-50 flex max-w-sm items-start gap-3 rounded-2xl border border-[var(--primary)]/30 bg-[var(--surface)] p-4 shadow-2xl"
        >
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[var(--primary)]/15 text-[var(--primary)]">
                <BellAlertIcon class="h-5 w-5" />
            </span>
            <a :href="foregroundNotice.url || '/dashboard'" class="min-w-0 flex-1">
                <p class="text-sm font-bold text-[var(--text)]">{{ foregroundNotice.title }}</p>
                <p v-if="foregroundNotice.body" class="mt-0.5 line-clamp-2 text-xs text-[var(--text-muted)]">
                    {{ foregroundNotice.body }}
                </p>
            </a>
            <button
                type="button"
                class="rounded-lg p-1 text-[var(--text-muted)] hover:bg-[var(--surface2)] hover:text-[var(--text)]"
                @click="dismissForegroundNotice"
            >
                <XMarkIcon class="h-4 w-4" />
            </button>
        </div>
    </Transition>

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
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl border px-4 py-3 text-sm font-semibold text-white shadow-xl"
            :class="toastIsError ? 'border-red-500/30 bg-red-600' : 'border-emerald-500/30 bg-emerald-600'"
        >
            <ExclamationTriangleIcon v-if="toastIsError" class="h-5 w-5 shrink-0 text-white" />
            <CheckCircleIcon v-else class="h-5 w-5 shrink-0 text-white" />
            <span>{{ toastMessage }}</span>
        </div>
    </Transition>

    <!-- Push Notification Prompt Banner (fallback if browser requires manual click) -->
    <div
        v-if="isSupported && isConfigured && !isOptedOut && permission === 'default' && !isDismissed"
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
                @click="handleEnable"
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
