<script setup>
import { computed, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    ArrowLeftIcon,
    ArrowDownTrayIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    XCircleIcon,
    TrophyIcon,
    ClockIcon,
    ListBulletIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ exam: Object, canExplain: Boolean });
const { t, locale } = useI18n();
const page = usePage();
const csrfToken = computed(() => page.props.csrf_token ?? '');

const exam = computed(() => props.exam);
const questions = computed(() => exam.value.questions || []);
const aiExplanations = ref({});
const explanationLoading = ref({});
const explanationErrors = ref({});

async function requestExplanation(question) {
    if (explanationLoading.value[question.id]) return;

    explanationLoading.value = { ...explanationLoading.value, [question.id]: true };
    explanationErrors.value = { ...explanationErrors.value, [question.id]: '' };
    aiExplanations.value = { ...aiExplanations.value, [question.id]: '' };

    try {
        const response = await fetch(`/exams/${exam.value.id}/questions/${question.id}/explanation`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.value,
                'Accept': 'text/event-stream',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            explanationErrors.value = {
                ...explanationErrors.value,
                [question.id]: response.status === 403
                    ? (locale.value === 'en' ? 'AI explanations require a paid plan.' : 'AI ব্যাখ্যার জন্য পেইড প্ল্যান প্রয়োজন।')
                    : (locale.value === 'en' ? 'Could not load the explanation.' : 'ব্যাখ্যাটি আনা যায়নি।'),
            };
            return;
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';
        let streamError = '';
        const handleEvent = (event) => {
            if (event.error) {
                streamError = event.error;
                return;
            }
            if (event.content) {
                aiExplanations.value = {
                    ...aiExplanations.value,
                    [question.id]: (aiExplanations.value[question.id] || '') + event.content,
                };
            }
        };

        while (true) {
            const { done, value } = await reader.read();
            if (done) break;
            buffer += decoder.decode(value, { stream: true });
            const events = buffer.split('\n\n');
            buffer = events.pop();
            for (const eventText of events) {
                for (const line of eventText.split('\n')) {
                    if (line.startsWith('data:')) handleEvent(JSON.parse(line.slice(5).trim()));
                }
            }
        }
        if (buffer.trim()) {
            for (const line of buffer.split('\n')) {
                if (line.startsWith('data:')) handleEvent(JSON.parse(line.slice(5).trim()));
            }
        }
        if (streamError) {
            explanationErrors.value = { ...explanationErrors.value, [question.id]: streamError };
        }
    } catch {
        explanationErrors.value = {
            ...explanationErrors.value,
            [question.id]: locale.value === 'en' ? 'Could not load the explanation.' : 'ব্যাখ্যাটি আনা যায়নি।',
        };
    } finally {
        explanationLoading.value = { ...explanationLoading.value, [question.id]: false };
    }
}

function isCorrect(q) {
    const given = exam.value.answers?.[q.id];
    return given && String(given).toLowerCase().trim() === String(q.correct_answer ?? '').toLowerCase().trim();
}

const correctCount = computed(() => {
    return questions.value.filter(q => isCorrect(q)).length;
});

const incorrectCount = computed(() => {
    return Math.max(0, (exam.value.total || questions.value.length) - correctCount.value);
});

const scoreGrade = computed(() => {
    const pct = exam.value.percentage || 0;
    if (pct >= 80) return { label: locale.value === 'en' ? 'Excellent Performance!' : 'অসাধারণ ফলাফল!', color: 'text-emerald-500' };
    if (pct >= 60) return { label: locale.value === 'en' ? 'Good Job, Keep Practicing!' : 'ভালো হয়েছে, নিয়মিত অনুশীলন চালিয়ে যান!', color: 'text-blue-500' };
    if (pct >= 40) return { label: locale.value === 'en' ? 'Needs Improvement' : 'আরও মনোযোগ প্রয়োজন', color: 'text-amber-500' };
    return { label: locale.value === 'en' ? 'Review the Topic & Try Again' : 'পুনরায় পড়ে আবার চেষ্টা করুন', color: 'text-rose-500' };
});
</script>

<template>
    <Head :title="t('exams.your_score')" />
    <StudentLayout>
        <div class="mx-auto max-w-3xl pb-16">
            <!-- Header back navigation -->
            <div class="mb-4 flex items-center justify-between">
                <Link
                    href="/exams"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--text-muted)] hover:text-[var(--text)] transition-colors"
                >
                    <ArrowLeftIcon class="h-4 w-4" />
                    <span>{{ locale === 'en' ? 'Back to Exams' : 'সকল পরীক্ষা' }}</span>
                </Link>

                <div class="flex items-center gap-2">
                    <a
                        :href="`/exams/${exam.id}/pdf`"
                        class="btn btn-secondary inline-flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-xs font-bold"
                    >
                        <ArrowDownTrayIcon class="h-3.5 w-3.5" />
                        <span>{{ t('exams.download_pdf') }}</span>
                    </a>
                </div>
            </div>

            <!-- Score Summary Card -->
            <div class="mb-8 overflow-hidden rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col items-center text-center sm:flex-row sm:items-center sm:justify-between sm:text-left gap-6">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--secondary)]/10 px-3 py-1 text-xs font-bold text-[var(--secondary)] mb-2">
                            <TrophyIcon class="h-3.5 w-3.5" />
                            {{ scoreGrade.label }}
                        </span>
                        <h1 class="font-heading text-2xl font-black tracking-tight sm:text-3xl text-[var(--text)]">
                            {{ exam.title || exam.display_title || t('exams.your_score') }}
                        </h1>
                        <p class="mt-1 text-xs text-[var(--text-muted)]">
                            {{ exam.config?.type?.toUpperCase() || 'MCQ' }} · {{ exam.total }} {{ t('exams.questions') }}
                        </p>
                    </div>

                    <!-- Large Score Circle / Badge -->
                    <div class="flex flex-col items-center rounded-2xl bg-[var(--surface2)] px-6 py-4 border border-[var(--border)]">
                        <span class="font-heading text-4xl font-black tracking-tight" :class="exam.percentage >= 70 ? 'text-emerald-500' : exam.percentage >= 40 ? 'text-amber-500' : 'text-rose-500'">
                            {{ exam.percentage }}%
                        </span>
                        <span class="mt-1 text-xs font-bold text-[var(--text-muted)]">
                            {{ exam.score }} / {{ exam.total }} {{ locale === 'en' ? 'Score' : 'নম্বর' }}
                        </span>
                    </div>
                </div>

                <!-- Sub-metrics Grid -->
                <div class="mt-6 grid grid-cols-3 gap-3 border-t border-[var(--border)] pt-5">
                    <div class="rounded-xl bg-emerald-500/10 p-3 text-center border border-emerald-500/20">
                        <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 block">{{ t('exams.correct') }}</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ correctCount }}</span>
                    </div>
                    <div class="rounded-xl bg-rose-500/10 p-3 text-center border border-rose-500/20">
                        <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 block">{{ t('exams.incorrect') }}</span>
                        <span class="text-xl font-black text-rose-600 dark:text-rose-400">{{ incorrectCount }}</span>
                    </div>
                    <div class="rounded-xl bg-blue-500/10 p-3 text-center border border-blue-500/20">
                        <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 block">{{ locale === 'en' ? 'Total' : 'মোট' }}</span>
                        <span class="text-xl font-black text-blue-600 dark:text-blue-400">{{ exam.total }}</span>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-[var(--border)] pt-5">
                    <Link
                        href="/exams/create"
                        class="btn btn-primary inline-flex items-center gap-1.5 rounded-xl px-5 py-2.5 text-xs font-bold shadow-sm"
                    >
                        <ArrowPathIcon class="h-4 w-4" />
                        <span>{{ t('exams.retake') }}</span>
                    </Link>
                    <Link
                        href="/exams"
                        class="btn btn-secondary inline-flex items-center gap-1.5 rounded-xl px-5 py-2.5 text-xs font-bold"
                    >
                        <ListBulletIcon class="h-4 w-4" />
                        <span>{{ t('exams.history') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Detailed Question Breakdown -->
            <div class="space-y-4">
                <h2 class="font-heading text-lg font-bold tracking-tight mb-2">
                    {{ locale === 'en' ? 'Question-by-Question Review' : 'প্রশ্নভিত্তিক বিস্তারিত মূল্যায়ন' }}
                </h2>

                <div
                    v-for="(q, idx) in questions"
                    :key="q.id"
                    class="rounded-2xl border bg-[var(--surface)] p-5 sm:p-6 transition-all"
                    :class="isCorrect(q) ? 'border-emerald-500/30' : 'border-rose-500/30'"
                >
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">
                            {{ locale === 'en' ? 'Question' : 'প্রশ্ন' }} {{ idx + 1 }}
                        </span>
                        <span
                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold"
                            :class="isCorrect(q) ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'"
                        >
                            <CheckCircleIcon v-if="isCorrect(q)" class="h-4 w-4" />
                            <XCircleIcon v-else class="h-4 w-4" />
                            <span>{{ isCorrect(q) ? t('exams.correct') : t('exams.incorrect') }}</span>
                        </span>
                    </div>

                    <h3 class="text-base font-bold leading-relaxed mb-4 text-[var(--text)]">
                        {{ q.question }}
                    </h3>

                    <div class="space-y-2 rounded-xl bg-[var(--surface2)]/60 p-4 border border-[var(--border)] text-xs">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-[var(--text-muted)]">{{ locale === 'en' ? 'Your Answer:' : 'আপনার উত্তর:' }}</span>
                            <span :class="isCorrect(q) ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-rose-600 dark:text-rose-400 font-bold'">
                                {{ exam.answers?.[q.id] || (locale === 'en' ? 'Not Answered' : 'উত্তর দেওয়া হয়নি') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-[var(--text-muted)]">{{ locale === 'en' ? 'Correct Answer:' : 'সঠিক উত্তর:' }}</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ q.correct_answer }}</span>
                        </div>
                        <div v-if="q.explanation" class="mt-2 border-t border-[var(--border)]/70 pt-2 text-[var(--text-muted)] leading-relaxed">
                            <span class="font-bold text-[var(--text)]">{{ locale === 'en' ? 'Explanation:' : 'ব্যাখ্যা:' }}</span>
                            {{ q.explanation }}
                        </div>
                    </div>

                    <div v-if="q.book_ref" class="mt-4 flex flex-wrap items-center gap-2 text-xs text-[var(--text-muted)]">
                        <span class="font-semibold">{{ locale === 'en' ? 'Book reference:' : 'বইয়ের সূত্র:' }}</span>
                        <Link
                            v-if="q.book_ref.reader_url"
                            :href="q.book_ref.reader_url"
                            class="font-bold text-[var(--primary)] underline underline-offset-2"
                        >
                            {{ q.book_ref.book || q.book_ref.chapter || (locale === 'en' ? 'Book' : 'বই') }}
                            <span v-if="q.book_ref.book && q.book_ref.chapter"> · {{ locale === 'en' ? 'Chapter' : 'অধ্যায়' }} {{ q.book_ref.chapter }}</span>
                            <span v-if="q.book_ref.page"> · {{ locale === 'en' ? 'Page' : 'পৃষ্ঠা' }} {{ q.book_ref.page }}</span>
                        </Link>
                        <span v-else>
                            {{ q.book_ref.book || q.book_ref.chapter || (locale === 'en' ? 'Book' : 'বই') }}
                            <span v-if="q.book_ref.book && q.book_ref.chapter"> · {{ locale === 'en' ? 'Chapter' : 'অধ্যায়' }} {{ q.book_ref.chapter }}</span>
                            <span v-if="q.book_ref.page"> · {{ locale === 'en' ? 'Page' : 'পৃষ্ঠা' }} {{ q.book_ref.page }}</span>
                        </span>
                    </div>

                    <div v-if="canExplain" class="mt-4">
                        <button
                            type="button"
                            class="btn btn-secondary rounded-xl px-3.5 py-2 text-xs font-bold"
                            :disabled="explanationLoading[q.id]"
                            @click="requestExplanation(q)"
                        >
                            {{ explanationLoading[q.id]
                                ? (locale === 'en' ? 'Generating…' : 'ব্যাখ্যা তৈরি হচ্ছে…')
                                : (locale === 'en' ? 'AI Explanation' : 'AI ব্যাখ্যা') }}
                        </button>
                        <p v-if="aiExplanations[q.id]" class="mt-3 whitespace-pre-line rounded-xl bg-[var(--surface2)] p-4 text-sm leading-relaxed text-[var(--text-muted)]">
                            {{ aiExplanations[q.id] }}
                        </p>
                        <p v-if="explanationErrors[q.id]" class="mt-2 text-xs text-rose-600" role="alert">
                            {{ explanationErrors[q.id] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
