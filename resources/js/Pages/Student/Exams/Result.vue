<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ exam: Object });
const { t } = useI18n();

function isCorrect(q) {
    const given = props.exam.answers?.[q.id];
    return given && String(given).toLowerCase().trim() === String(q.correct_answer ?? '').toLowerCase().trim();
}
</script>

<template>
    <Head :title="t('exams.your_score')" />
    <StudentLayout>
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 mb-6 flex items-center justify-between max-w-2xl">
            <div>
                <div class="text-sm text-[var(--text-muted)]">{{ t('exams.your_score') }}</div>
                <div class="text-3xl font-heading font-extrabold">{{ exam.score }} / {{ exam.total }} <span class="text-lg text-[var(--text-muted)]">({{ exam.percentage }}%)</span></div>
            </div>
            <a :href="`/exams/${exam.id}/pdf`" class="px-4 py-2 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-sm font-medium">
                {{ t('exams.download_pdf') }}
            </a>
        </div>

        <div class="space-y-4 max-w-2xl">
            <div v-for="(q, i) in exam.questions" :key="q.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div class="text-xs text-[var(--text-muted)] mb-2">Q{{ i + 1 }}</div>
                <div class="font-medium mb-3">{{ q.question }}</div>
                <div class="text-sm mb-1">
                    Your answer:
                    <span :class="isCorrect(q) ? 'text-[var(--secondary)]' : 'text-[var(--accent)]'">
                        {{ exam.answers?.[q.id] ?? '—' }}
                    </span>
                </div>
                <div class="text-sm text-[var(--secondary)] mb-2">Correct: {{ q.correct_answer }}</div>
                <div v-if="q.explanation" class="text-sm text-[var(--text-muted)]">{{ q.explanation }}</div>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <Link href="/exams/create" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold">{{ t('exams.retake') }}</Link>
            <Link href="/exams" class="px-5 py-2.5 rounded-lg border border-[var(--border)] text-sm font-semibold">{{ t('exams.history') }}</Link>
        </div>
    </StudentLayout>
</template>
