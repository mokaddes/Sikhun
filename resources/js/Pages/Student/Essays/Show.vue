<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ essay: Object });
const { t } = useI18n();
const essay = ref(props.essay);
let pollInterval;

function poll() {
    pollInterval = setInterval(async () => {
        const { data } = await axios.get(`/essays/${essay.value.id}/status`);
        if (data.status !== 'grading') {
            essay.value = { ...essay.value, status: data.status, result: data.result };
            clearInterval(pollInterval);
        }
    }, 2500);
}

onMounted(() => { if (essay.value.status === 'grading') poll(); });
onBeforeUnmount(() => clearInterval(pollInterval));
</script>

<template>
    <Head :title="t('essays.title')" />
    <StudentLayout>
        <div v-if="essay.status === 'grading'" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-12 text-center text-[var(--text-muted)]">
            {{ t('essays.grading') }}
        </div>
        <div v-else-if="essay.status === 'failed'" class="rounded-xl border border-[var(--accent)]/30 bg-[var(--accent)]/10 p-6 text-[var(--accent)]">
            Grading failed — please try submitting again.
        </div>
        <div v-else class="max-w-2xl space-y-5">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('essays.total_score') }}</div>
                <div class="text-3xl font-heading font-extrabold">{{ essay.result.total_score }} / {{ essay.result.max_score }}</div>
            </div>

            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 space-y-4">
                <div v-for="(item, key) in essay.result.breakdown" :key="key">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="capitalize font-medium">{{ key }}</span>
                        <span>{{ item.score }} / {{ item.max }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-[var(--surface2)] overflow-hidden mb-1">
                        <div class="h-full bg-[var(--primary)]" :style="{ width: `${(item.score / item.max) * 100}%` }"></div>
                    </div>
                    <p class="text-xs text-[var(--text-muted)]">{{ item.feedback }}</p>
                </div>
            </div>

            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <p class="mb-4">{{ essay.result.overall_feedback }}</p>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="font-semibold text-[var(--secondary)] mb-1">{{ t('essays.strengths') }}</div>
                        <ul class="text-[var(--text-muted)] space-y-1">
                            <li v-for="s in essay.result.strengths" :key="s">✓ {{ s }}</li>
                        </ul>
                    </div>
                    <div>
                        <div class="font-semibold text-[var(--accent)] mb-1">{{ t('essays.improvements') }}</div>
                        <ul class="text-[var(--text-muted)] space-y-1">
                            <li v-for="s in essay.result.improvements" :key="s">→ {{ s }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
