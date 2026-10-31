<script setup>
import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import ExamTimer from '@/Components/Exam/ExamTimer.vue';
import { useI18n } from '@/i18n';
import {
    SparklesIcon,
    ArrowLeftIcon,
    ArrowRightIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    AcademicCapIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ exam: Object, canExplain: Boolean });
const { t, locale } = useI18n();
const page = usePage();
const csrfToken = computed(() => page.props.csrf_token ?? '');

const exam = ref(props.exam);
const aiExplanations = ref({});
const explanationLoading = ref({});
const explanationErrors = ref({});
const answers = ref({});
const currentIndex = ref(0);
const revealed = ref(false);
const retrying = ref(false);
let pollInterval;

function poll() {
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(async () => {
        try {
            const { data } = await axios.get(`/exams/${exam.value.id}/status`);
            if (data.status !== 'generating') {
                exam.value = data.exam;
                clearInterval(pollInterval);
            }
        } catch (e) {
            console.error(e);
        }
    }, 2500);
}

onMounted(() => {
    if (exam.value.status === 'generating') poll();
});
watch(() => props.exam, (value) => {
    exam.value = value;
    if (value.status === 'generating') poll();
    else clearInterval(pollInterval);
});
onBeforeUnmount(() => clearInterval(pollInterval));

const isPractice = computed(() => exam.value.mode === 'practice');
const questions = computed(() => exam.value.questions || []);
const currentQuestion = computed(() => questions.value[currentIndex.value]);
const progressPct = computed(() => {
    if (!questions.value.length) return 0;
    return Math.round(((currentIndex.value + 1) / questions.value.length) * 100);
});

function selectAnswer(questionId, value) {
    answers.value[questionId] = value;
}

function nextQuestion() {
    revealed.value = false;
    if (currentIndex.value < questions.value.length - 1) {
        currentIndex.value++;
    }
}

function prevQuestion() {
    revealed.value = false;
    if (currentIndex.value > 0) {
        currentIndex.value--;
    }
}

function checkAnswer() {
    revealed.value = true;
}

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
                'Content-Type': 'application/json',
                'Accept': 'text/event-stream',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ student_answer: answers.value[question.id] ?? null }),
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

function submitExam() {
    router.post(`/exams/${exam.value.id}/complete`, { answers: answers.value });
}

function retryGeneration() {
    retrying.value = true;
    router.post(`/exams/${exam.value.id}/retry`, {}, {
        onFinish: () => { retrying.value = false; },
    });
}
</script>

<template>
    <Head :title="exam.title || exam.display_title || t('exams.title')" />
    <StudentLayout>
        <div class="mx-auto max-w-3xl pb-16">
            <!-- Header bar with Back link -->
            <div class="mb-4 flex items-center justify-between">
                <Link
                    href="/exams"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--text-muted)] hover:text-[var(--text)] transition-colors"
                >
                    <ArrowLeftIcon class="h-4 w-4" />
                    <span>{{ locale === 'en' ? 'Back to Exams' : 'সকল পরীক্ষা' }}</span>
                </Link>

                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold uppercase tracking-wider"
                        :class="isPractice ? 'bg-blue-500/10 text-blue-500 border border-blue-500/20' : 'bg-purple-500/10 text-purple-500 border border-purple-500/20'"
                    >
                        {{ isPractice ? (locale === 'en' ? 'Practice' : 'অনুশীলন') : (locale === 'en' ? 'Exam' : 'পরীক্ষা') }}
                    </span>
                    <ExamTimer
                        v-if="!isPractice && exam.config?.duration > 0 && exam.status !== 'generating'"
                        :duration-minutes="exam.config.duration"
                        @expired="submitExam"
                    />
                </div>
            </div>

            <!-- Loading / Generating State -->
            <div
                v-if="exam.status === 'generating'"
                class="flex flex-col items-center justify-center rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-12 text-center shadow-sm"
            >
                <div class="relative mb-6">
                    <div class="h-16 w-16 animate-spin rounded-full border-4 border-[var(--primary)]/20 border-t-[var(--primary)]"></div>
                    <SparklesIcon class="absolute inset-0 m-auto h-7 w-7 text-[var(--primary)] animate-pulse" />
                </div>
                <h2 class="font-heading text-xl font-bold">
                    {{ t('exams.generating') }}
                </h2>
                <p class="mt-2 max-w-md text-sm text-[var(--text-muted)]">
                    {{ locale === 'en'
                        ? 'Our AI is crafting balanced questions tailored to your topic. This usually takes 5-10 seconds...'
                        : 'আমাদের এআই আপনার নির্ধারিত বিষয়ে মানসম্মত প্রশ্ন তৈরি করছে। সাধারণত ৫-১০ সেকেন্ড সময় লাগে...' }}
                </p>
            </div>

            <div v-else-if="exam.status === 'failed'" class="rounded-3xl border border-rose-500/30 bg-rose-500/10 p-8 text-center">
                <h2 class="font-heading text-xl font-bold text-rose-600 dark:text-rose-400">
                    {{ locale === 'en' ? 'Question generation failed' : 'প্রশ্ন তৈরি করা যায়নি' }}
                </h2>
                <p class="mt-2 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Your exam settings are saved. Try generating the questions again.' : 'আপনার পরীক্ষার সেটিংস সংরক্ষিত আছে। আবার প্রশ্ন তৈরির চেষ্টা করুন।' }}
                </p>
                <button type="button" :disabled="retrying" class="btn btn-primary mt-5 inline-flex items-center gap-2 disabled:opacity-60" @click="retryGeneration">
                    <ArrowPathIcon class="h-4 w-4" :class="retrying ? 'animate-spin' : ''" />
                    {{ retrying ? (locale === 'en' ? 'Retrying...' : 'আবার চেষ্টা হচ্ছে...') : (locale === 'en' ? 'Retry generation' : 'আবার তৈরি করুন') }}
                </button>
            </div>

            <!-- Main Test Area -->
            <div v-else-if="questions.length">
                <!-- Title & Meta Header -->
                <div class="mb-5 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h1 class="font-heading text-xl font-extrabold sm:text-2xl">
                                {{ exam.title || exam.display_title }}
                            </h1>
                            <p class="mt-0.5 text-xs text-[var(--text-muted)]">
                                {{ exam.config?.type?.toUpperCase() || 'MCQ' }} · {{ exam.total }} {{ t('exams.questions') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-[var(--text-muted)]">
                            <ClockIcon class="h-4 w-4" />
                            <span>{{ exam.config?.duration ? `${exam.config.duration} min` : (locale === 'en' ? 'Untimed' : 'সময়সীমা নেই') }}</span>
                        </div>
                    </div>

                    <!-- Progress bar (Practice Mode) -->
                    <div v-if="isPractice" class="mt-4">
                        <div class="flex items-center justify-between text-xs font-semibold text-[var(--text-muted)] mb-1.5">
                            <span>{{ locale === 'en' ? 'Question' : 'প্রশ্ন' }} {{ currentIndex + 1 }} / {{ questions.length }}</span>
                            <span>{{ progressPct }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-[var(--surface2)]">
                            <div class="h-full bg-[var(--primary)] transition-all duration-300" :style="{ width: `${progressPct}%` }"></div>
                        </div>
                    </div>
                </div>

                <!-- PRACTICE MODE: One question at a time -->
                <div v-if="isPractice && currentQuestion" class="space-y-5">
                    <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                        <div class="text-xs font-bold uppercase tracking-wider text-[var(--primary)] mb-2">
                            {{ locale === 'en' ? 'Question' : 'প্রশ্ন' }} {{ currentIndex + 1 }}
                        </div>
                        <h2 class="text-base sm:text-lg font-bold leading-relaxed mb-3">
                            {{ currentQuestion.question }}
                        </h2>
                        <div v-if="currentQuestion.book_ref" class="mb-5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-[var(--text-muted)]">
                            <span class="font-semibold">{{ locale === 'en' ? 'Source:' : 'সূত্র:' }}</span>
                            <Link
                                v-if="currentQuestion.book_ref.reader_url"
                                :href="currentQuestion.book_ref.reader_url"
                                class="font-bold text-[var(--primary)] underline underline-offset-2"
                            >
                                {{ currentQuestion.book_ref.book || (locale === 'en' ? 'Book' : 'বই') }}
                                <span v-if="currentQuestion.book_ref.chapter"> · {{ locale === 'en' ? 'Chapter' : 'অধ্যায়' }} {{ currentQuestion.book_ref.chapter }}</span>
                                <span v-if="currentQuestion.book_ref.page"> · {{ locale === 'en' ? 'Page' : 'পৃষ্ঠা' }} {{ currentQuestion.book_ref.page }}</span>
                            </Link>
                            <span v-else>
                                {{ currentQuestion.book_ref.book || (locale === 'en' ? 'Book' : 'বই') }}
                                <span v-if="currentQuestion.book_ref.chapter"> · {{ locale === 'en' ? 'Chapter' : 'অধ্যায়' }} {{ currentQuestion.book_ref.chapter }}</span>
                                <span v-if="currentQuestion.book_ref.page"> · {{ locale === 'en' ? 'Page' : 'পৃষ্ঠা' }} {{ currentQuestion.book_ref.page }}</span>
                            </span>
                        </div>

                        <!-- Options for MCQ / True False -->
                        <div v-if="currentQuestion.options && currentQuestion.options.length" class="space-y-3">
                            <button
                                v-for="(opt, idx) in currentQuestion.options"
                                :key="opt"
                                type="button"
                                class="flex w-full items-center gap-3.5 rounded-xl border p-4 text-left text-sm font-medium transition-all"
                                :class="answers[currentQuestion.id] === opt
                                    ? 'border-[var(--primary)] bg-[var(--primary)]/10 ring-1 ring-[var(--primary)] text-[var(--text)] font-semibold'
                                    : 'border-[var(--border)] bg-[var(--surface2)]/40 hover:bg-[var(--surface2)] text-[var(--text)]'"
                                @click="selectAnswer(currentQuestion.id, opt)"
                            >
                                <span
                                    class="grid h-7 w-7 shrink-0 place-items-center rounded-lg text-xs font-bold"
                                    :class="answers[currentQuestion.id] === opt ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface2)] border border-[var(--border)] text-[var(--text-muted)]'"
                                >
                                    {{ String.fromCharCode(65 + idx) }}
                                </span>
                                <span class="flex-1">{{ opt }}</span>
                            </button>
                        </div>

                        <!-- Text Input for Short / Fill Blank -->
                        <div v-else>
                            <input
                                v-model="answers[currentQuestion.id]"
                                type="text"
                                :placeholder="locale === 'en' ? 'Type your answer here...' : 'আপনার উত্তর এখানে লিখুন...'"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                            />
                        </div>

                        <!-- Revealed Answer in Practice Mode -->
                        <div
                            v-if="revealed"
                            class="mt-6 rounded-2xl border p-4 transition-all"
                            :class="answers[currentQuestion.id] === currentQuestion.correct_answer
                                ? 'border-emerald-500/30 bg-emerald-500/10'
                                : 'border-amber-500/30 bg-amber-500/10'"
                        >
                            <div class="flex items-center gap-2 font-bold text-sm" :class="answers[currentQuestion.id] === currentQuestion.correct_answer ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">
                                <CheckCircleIcon v-if="answers[currentQuestion.id] === currentQuestion.correct_answer" class="h-5 w-5" />
                                <span v-else class="font-bold">{{ locale === 'en' ? 'Review Solution' : 'সঠিক সমাধান' }}:</span>
                                <span>{{ t('exams.correct') }}: {{ currentQuestion.correct_answer }}</span>
                            </div>
                            <p v-if="currentQuestion.explanation" class="mt-2 text-xs leading-relaxed text-[var(--text-muted)]">
                                {{ currentQuestion.explanation }}
                            </p>
                            <div v-if="canExplain" class="mt-4 border-t border-[var(--border)]/70 pt-3">
                                <button
                                    type="button"
                                    class="btn btn-secondary rounded-xl px-3.5 py-2 text-xs font-bold"
                                    :disabled="explanationLoading[currentQuestion.id]"
                                    @click="requestExplanation(currentQuestion)"
                                >
                                    {{ explanationLoading[currentQuestion.id]
                                        ? (locale === 'en' ? 'Generating…' : 'ব্যাখ্যা তৈরি হচ্ছে…')
                                        : (locale === 'en' ? 'AI Explanation' : 'AI ব্যাখ্যা') }}
                                </button>
                                <p v-if="aiExplanations[currentQuestion.id]" class="mt-3 whitespace-pre-line text-xs leading-relaxed text-[var(--text-muted)]">
                                    {{ aiExplanations[currentQuestion.id] }}
                                </p>
                                <p v-if="explanationErrors[currentQuestion.id]" class="mt-2 text-xs text-rose-600" role="alert">
                                    {{ explanationErrors[currentQuestion.id] }}
                                </p>
                            </div>
                        </div>

                        <!-- Practice Nav Buttons -->
                        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-[var(--border)]/70 pt-5">
                            <button
                                type="button"
                                :disabled="currentIndex === 0"
                                class="btn btn-secondary text-xs disabled:opacity-40"
                                @click="prevQuestion"
                            >
                                <ArrowLeftIcon class="h-3.5 w-3.5" />
                                <span>{{ locale === 'en' ? 'Previous' : 'আগেরটি' }}</span>
                            </button>

                            <div class="flex items-center gap-2">
                                <button
                                    v-if="!revealed"
                                    type="button"
                                    class="btn btn-primary text-xs"
                                    @click="checkAnswer"
                                >
                                    {{ locale === 'en' ? 'Check Answer' : 'উত্তর যাচাই' }}
                                </button>
                                <button
                                    v-else-if="currentIndex < questions.length - 1"
                                    type="button"
                                    class="btn btn-primary text-xs"
                                    @click="nextQuestion"
                                >
                                    <span>{{ locale === 'en' ? 'Next Question' : 'পরবর্তী প্রশ্ন' }}</span>
                                    <ArrowRightIcon class="h-3.5 w-3.5" />
                                </button>
                                <button
                                    v-else
                                    type="button"
                                    class="btn btn-primary text-xs bg-emerald-600 hover:bg-emerald-700"
                                    @click="submitExam"
                                >
                                    <CheckCircleIcon class="h-4 w-4" />
                                    <span>{{ t('exams.submit_exam') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EXAM MODE: All questions rendered with final submit -->
                <div v-else class="space-y-6">
                    <div
                        v-for="(q, idx) in questions"
                        :key="q.id"
                        class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm"
                    >
                        <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-[var(--primary)] mb-2">
                            <span>{{ locale === 'en' ? 'Question' : 'প্রশ্ন' }} {{ idx + 1 }}</span>
                            <span v-if="answers[q.id]" class="text-emerald-500 font-semibold">{{ locale === 'en' ? 'Answered' : 'উত্তর দেওয়া হয়েছে' }}</span>
                        </div>
                        <h2 class="text-base font-bold leading-relaxed mb-3">
                            {{ q.question }}
                        </h2>
                        <div v-if="q.book_ref" class="mb-4 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-[var(--text-muted)]">
                            <span class="font-semibold">{{ locale === 'en' ? 'Source:' : 'সূত্র:' }}</span>
                            <Link
                                v-if="q.book_ref.reader_url"
                                :href="q.book_ref.reader_url"
                                class="font-bold text-[var(--primary)] underline underline-offset-2"
                            >
                                {{ q.book_ref.book || (locale === 'en' ? 'Book' : 'বই') }}
                                <span v-if="q.book_ref.chapter"> · {{ locale === 'en' ? 'Chapter' : 'অধ্যায়' }} {{ q.book_ref.chapter }}</span>
                                <span v-if="q.book_ref.page"> · {{ locale === 'en' ? 'Page' : 'পৃষ্ঠা' }} {{ q.book_ref.page }}</span>
                            </Link>
                            <span v-else>
                                {{ q.book_ref.book || (locale === 'en' ? 'Book' : 'বই') }}
                                <span v-if="q.book_ref.chapter"> · {{ locale === 'en' ? 'Chapter' : 'অধ্যায়' }} {{ q.book_ref.chapter }}</span>
                                <span v-if="q.book_ref.page"> · {{ locale === 'en' ? 'Page' : 'পৃষ্ঠা' }} {{ q.book_ref.page }}</span>
                            </span>
                        </div>

                        <!-- Options for MCQ -->
                        <div v-if="q.options && q.options.length" class="space-y-2.5">
                            <label
                                v-for="(opt, optIdx) in q.options"
                                :key="opt"
                                class="flex items-center gap-3 rounded-xl border p-3.5 text-sm cursor-pointer transition-all"
                                :class="answers[q.id] === opt
                                    ? 'border-[var(--primary)] bg-[var(--primary)]/10 ring-1 ring-[var(--primary)] font-semibold'
                                    : 'border-[var(--border)] bg-[var(--surface2)]/40 hover:bg-[var(--surface2)]'"
                            >
                                <input
                                    type="radio"
                                    :name="`q-${q.id}`"
                                    :value="opt"
                                    v-model="answers[q.id]"
                                    class="h-4 w-4 text-[var(--primary)] focus:ring-[var(--primary)]"
                                />
                                <span>{{ opt }}</span>
                            </label>
                        </div>

                        <!-- Text field for Short Answer -->
                        <div v-else>
                            <input
                                v-model="answers[q.id]"
                                type="text"
                                :placeholder="locale === 'en' ? 'Type your answer...' : 'আপনার উত্তর লিখুন...'"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- Submit Exam Button -->
                    <div class="sticky bottom-4 z-20 rounded-2xl border border-[var(--border)] bg-[var(--surface)]/95 p-4 shadow-xl backdrop-blur-md">
                        <button
                            type="button"
                            @click="submitExam"
                            class="btn btn-primary w-full justify-center gap-2 rounded-xl py-3.5 text-base font-bold shadow-lg shadow-[var(--primary)]/20"
                        >
                            <CheckCircleIcon class="h-5 w-5" />
                            <span>{{ t('exams.submit_exam') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
