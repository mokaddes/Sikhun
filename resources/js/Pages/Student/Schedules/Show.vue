<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { ArrowPathIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ schedule: Object });
const { t, locale } = useI18n();
const schedule = ref(props.schedule);
const retrying = ref(false);
let pollInterval;

function poll() {
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(async () => {
        const { data } = await axios.get(`/schedules/${schedule.value.id}/status`);
        if (data.status !== 'generating') {
            schedule.value = { ...schedule.value, status: data.status, schedule_data: data.schedule_data };
            clearInterval(pollInterval);
        }
    }, 2500);
}

onMounted(() => { if (schedule.value.status === 'generating') poll(); });
watch(() => props.schedule, (value) => {
    schedule.value = value;
    if (value.status === 'generating') poll();
    else clearInterval(pollInterval);
});
onBeforeUnmount(() => clearInterval(pollInterval));

function toggleDone(day) {
    router.post(`/schedules/${schedule.value.id}/progress`, { date: day.date }, { preserveScroll: true });
}

function retryGeneration() {
    retrying.value = true;
    router.post(`/schedules/${schedule.value.id}/retry`, {}, {
        onFinish: () => { retrying.value = false; },
    });
}
</script>

<template>
    <Head :title="t('schedules.title')" />
    <StudentLayout>
        <div v-if="schedule.status === 'generating'" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-12 text-center text-[var(--text-muted)]">
            {{ t('schedules.generating') }}
        </div>
        <div v-else-if="schedule.status === 'failed'" class="mx-auto max-w-lg rounded-xl border border-rose-500/30 bg-rose-500/10 p-6 text-center">
            <p class="font-semibold text-rose-600 dark:text-rose-400">{{ locale === 'en' ? 'Study plan generation failed' : 'স্টাডি রুটিন তৈরি করা যায়নি' }}</p>
            <p class="mt-2 text-sm text-[var(--text-muted)]">{{ locale === 'en' ? 'Your schedule settings are saved. Try generating the plan again.' : 'আপনার রুটিনের সেটিংস সংরক্ষিত আছে। আবার তৈরির চেষ্টা করুন।' }}</p>
            <button type="button" :disabled="retrying" class="btn btn-primary mt-5 inline-flex items-center gap-2 disabled:opacity-60" @click="retryGeneration">
                <ArrowPathIcon class="h-4 w-4" :class="retrying ? 'animate-spin' : ''" />
                {{ retrying ? (locale === 'en' ? 'Retrying…' : 'আবার চেষ্টা হচ্ছে…') : (locale === 'en' ? 'Retry generation' : 'আবার তৈরি করুন') }}
            </button>
        </div>
        <div v-else>
            <div class="flex items-center justify-between mb-6">
                <h1 class="font-heading text-xl font-bold">{{ t('schedules.exam_date') }}: {{ new Date(schedule.exam_date).toLocaleDateString() }}</h1>
                <a :href="`/schedules/${schedule.id}/pdf`" class="text-sm text-[var(--primary)] hover:underline">{{ t('schedules.download_pdf') }}</a>
            </div>

            <div class="space-y-2 max-w-2xl">
                <div v-for="day in schedule.schedule_data" :key="day.date"
                    class="flex items-center justify-between rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4"
                    :class="day.completed ? 'opacity-50' : ''">
                    <div>
                        <div class="text-xs text-[var(--text-muted)]">{{ day.date }}</div>
                        <div class="font-medium text-sm" :class="day.completed ? 'line-through' : ''">{{ day.subject }} — {{ day.topic }}</div>
                        <div class="text-xs text-[var(--text-muted)]">{{ day.hours }}h · {{ day.tip }}</div>
                    </div>
                    <input type="checkbox" :checked="day.completed" @change="toggleDone(day)" class="w-5 h-5 rounded" />
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
