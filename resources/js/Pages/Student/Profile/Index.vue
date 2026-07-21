<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ student: Object, preferences: Array });
const { t } = useI18n();

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
    <Head :title="t('profile_page.title')" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-1">{{ t('profile_page.title') }}</h1>
        <p class="text-[var(--text-muted)] mb-8">{{ student.name }} · {{ student.email }}</p>

        <div class="grid md:grid-cols-2 gap-6 max-w-3xl">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">{{ t('profile_page.leaderboard_settings') }}</h2>
                <label class="flex items-center gap-3 text-sm mb-4">
                    <input v-model="optOutForm.opt_out" type="checkbox" class="rounded w-4 h-4" />
                    {{ t('profile_page.opt_out_label') }}
                </label>
                <button @click="saveOptOut" :disabled="optOutForm.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold disabled:opacity-60">
                    {{ t('profile_page.save') }}
                </button>
            </div>

            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">{{ t('profile_page.notification_settings') }}</h2>
                <div class="space-y-3 mb-4 max-h-72 overflow-y-auto">
                    <div v-for="(pref, i) in prefForm.preferences" :key="pref.type" class="flex items-center justify-between gap-3 text-sm">
                        <label class="flex items-center gap-2 flex-1">
                            <input v-model="pref.is_enabled" type="checkbox" class="rounded" />
                            {{ pref.label }}
                        </label>
                        <input v-if="pref.is_enabled" v-model="pref.preferred_time" type="time" class="px-2 py-1 rounded border border-[var(--border)] bg-[var(--surface2)] text-xs" />
                    </div>
                </div>
                <button @click="savePreferences" :disabled="prefForm.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold disabled:opacity-60">
                    {{ t('profile_page.save') }}
                </button>
            </div>
        </div>
    </StudentLayout>
</template>
