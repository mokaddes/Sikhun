<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { TrophyIcon, BellIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ student: Object, preferences: Array });
const { t, locale } = useI18n();

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
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-gradient-to-br from-[var(--secondary)] to-[var(--primary)] text-xl font-bold text-white">
                {{ student.name?.charAt(0) }}
            </span>
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
        </div>
    </StudentLayout>
</template>
