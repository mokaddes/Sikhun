<script setup>
import { onMounted } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import StudentAvatar from '@/Components/UI/StudentAvatar.vue';
import { useI18n } from '@/i18n';
import { useFirebasePush } from '@/composables/useFirebasePush';
import { TrophyIcon, BellIcon, BellAlertIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ student: Object, preferences: Array });
const { t, locale } = useI18n();
const {
    permission,
    isSupported,
    isConfigured,
    isSubscribed,
    isOptedOut,
    isLoading: pushLoading,
    error: pushError,
    enablePush,
    disablePush,
    syncIfGranted,
} = useFirebasePush();

onMounted(() => {
    syncIfGranted();
});

const optOutForm = useForm({ opt_out: props.student.leaderboard_opt_out });
function saveOptOut() {
    optOutForm.put('/profile/leaderboard-opt-out', { preserveScroll: true });
}

const prefForm = useForm({ preferences: JSON.parse(JSON.stringify(props.preferences)) });
function savePreferences() {
    prefForm.put('/profile/notification-preferences', { preserveScroll: true });
}
</script>

<template>
    <Head :title="t('profile_page.notification_settings')" />
    <StudentLayout>
        <header class="anim-rise mb-6 flex items-center gap-4">
            <StudentAvatar :avatar="student.avatar" :name="student.name" size="lg" />
            <div>
                <span class="eyebrow">{{ locale === 'en' ? 'Account' : 'অ্যাকাউন্ট' }}</span>
                <h1 class="mt-0.5 font-heading text-2xl font-extrabold">{{ t('profile_page.notification_settings') }}</h1>
                <p class="text-sm text-[var(--text-muted)]">{{ student.email }}</p>
            </div>
        </header>

        <div class="grid max-w-3xl gap-6 md:grid-cols-2">
            <div class="card anim-rise rounded-2xl p-6">
                <h2 class="mb-4 flex items-center gap-2 font-heading text-lg font-bold"><TrophyIcon class="h-5 w-5 text-[var(--primary)]" /> {{ t('profile_page.leaderboard_settings') }}</h2>
                <label class="mb-5 flex cursor-pointer items-center gap-3 text-sm">
                    <input v-model="optOutForm.opt_out" type="checkbox" class="h-4 w-4 rounded accent-[var(--primary)]" />
                    {{ t('profile_page.opt_out_label') }}
                </label>
                <button @click="saveOptOut" :disabled="optOutForm.processing" class="btn btn-primary btn-sm">
                    {{ t('profile_page.save') }}
                </button>
            </div>

            <div class="card anim-rise rounded-2xl p-6">
                <h2 class="mb-4 flex items-center gap-2 font-heading text-lg font-bold"><BellIcon class="h-5 w-5 text-[var(--secondary)]" /> {{ t('profile_page.notification_settings') }}</h2>
                <div class="mb-4 max-h-72 space-y-1 overflow-y-auto">
                    <div v-for="pref in prefForm.preferences" :key="pref.type" class="flex items-center justify-between gap-3 rounded-lg px-2 py-2 text-sm hover:bg-[var(--surface2)]/60">
                        <label class="flex flex-1 cursor-pointer items-center gap-2.5">
                            <input v-model="pref.is_enabled" type="checkbox" class="h-4 w-4 rounded accent-[var(--primary)]" />
                            {{ pref.label }}
                        </label>
                        <input v-if="pref.is_enabled" v-model="pref.preferred_time" type="time" class="rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2 py-1 text-xs" />
                    </div>
                </div>
                <button @click="savePreferences" :disabled="prefForm.processing" class="btn btn-primary btn-sm">
                    {{ t('profile_page.save') }}
                </button>
            </div>

            <div v-if="isSupported && isConfigured" class="card anim-rise rounded-2xl p-6 md:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="flex items-center gap-2 font-heading text-lg font-bold">
                            <BellAlertIcon class="h-5 w-5 text-[var(--primary)]" />
                            {{ locale === 'en' ? 'Browser Push Notifications' : 'ব্রাউজার পুশ নোটিফিকেশন' }}
                        </h2>
                        <p class="mt-1 text-xs text-[var(--text-muted)]">
                            {{ locale === 'en'
                                ? 'Receive instant exam alerts, study reminders, and announcements directly on this device via Google Firebase.'
                                : 'এই ডিভাইসে সরাসরি পরীক্ষার অ্যালার্ট, স্টাডি রিমাইন্ডার এবং গুরুত্বপূর্ণ ঘোষণা পেতে পুশ নোটিফিকেশন চালু রাখুন।' }}
                        </p>
                        <p v-if="permission === 'denied'" class="mt-2 text-xs text-amber-500">
                            {{ locale === 'en'
                                ? 'Notifications are blocked in your browser settings. Please allow notifications for sikhun.com in your browser address bar.'
                                : 'আপনার ব্রাউজার সেটিংসে নোটিফিকেশন ব্লক করা আছে। অ্যাড্রেস বার থেকে sikhun.com-এর জন্য নোটিফিকেশন Allow করুন।' }}
                        </p>
                        <p v-if="pushError" class="mt-2 text-xs text-red-500">{{ pushError }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            v-if="isSubscribed"
                            class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-500"
                        >
                            {{ locale === 'en' ? 'Active on this device' : 'এই ডিভাইসে চালু আছে' }}
                        </span>
                        <span
                            v-else-if="isOptedOut"
                            class="rounded-full border border-[var(--border)] bg-[var(--surface2)] px-3 py-1 text-xs font-semibold text-[var(--text-muted)]"
                        >
                            {{ locale === 'en' ? 'Disabled' : 'বন্ধ করা আছে' }}
                        </span>
                        <button
                            v-if="isSubscribed"
                            type="button"
                            :disabled="pushLoading"
                            class="btn btn-ghost btn-sm border border-[var(--border)]"
                            @click="disablePush"
                        >
                            {{ pushLoading
                                ? (locale === 'en' ? 'Updating...' : 'আপডেট হচ্ছে...')
                                : (locale === 'en' ? 'Disable' : 'বন্ধ করুন') }}
                        </button>
                        <button
                            v-else
                            type="button"
                            :disabled="pushLoading || permission === 'denied'"
                            class="btn btn-primary btn-sm"
                            @click="enablePush()"
                        >
                            {{ pushLoading
                                ? (locale === 'en' ? 'Enabling...' : 'চালু হচ্ছে...')
                                : (locale === 'en' ? 'Enable Push Notifications' : 'পুশ নোটিফিকেশন চালু করুন') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
