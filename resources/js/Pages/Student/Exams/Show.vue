<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
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
} from '@heroicons/vue/24/outline';

const props = defineProps({ exam: Object });
const { t, locale } = useI18n();

const exam = ref(props.exam);
const answers = ref({});
const currentIndex = ref(0);
const revealed = ref(false);
let pollInterval;

function poll() {
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

function submitExam() {
    router.post(`/exams/${exam.value.id}/complete`, { answers: answers.value });
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
                        <h2 class="text-base sm:text-lg font-bold leading-relaxed mb-6">
                            {{ currentQuestion.question }}
                        </h2>

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
                        <h2 class="text-base font-bold leading-relaxed mb-4">
                            {{ q.question }}
                        </h2>

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
