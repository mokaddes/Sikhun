<script setup>
import { ref, computed } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { renderMath } from '@/Utils/renderMath';
import { useI18n } from '@/i18n';
import axios from 'axios';
import {
    AcademicCapIcon,
    BookOpenIcon,
    SparklesIcon,
    ArrowPathIcon,
    FunnelIcon,
    MagnifyingGlassIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    TrophyIcon,
    ChevronDownIcon,
    LightBulbIcon,
    ArrowRightIcon,
    BuildingLibraryIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    mistakes: Object,
    subjects: Array,
    institutions: Array,
    stats: Object,
    filters: Object,
});

const { t, locale } = useI18n();

const activeStatus = ref(props.filters?.status || 'unresolved');
const selectedSubjectId = ref(props.filters?.subject_id || '');
const selectedInstitution = ref(props.filters?.institution || '');
const searchQuery = ref(props.filters?.search || '');

const practicing = ref(false);
const practiceError = ref('');
const practiceCount = ref(20);
const showPracticeModal = ref(false);

const showResetModal = ref(false);
const resetting = ref(false);

function resetMistakes() {
    if (resetting.value) return;
    resetting.value = true;
    router.post('/mistakes/reset', {
        subject_id: selectedSubjectId.value || undefined,
        institution: selectedInstitution.value || undefined,
    }, {
        preserveScroll: true,
        onFinish: () => {
            resetting.value = false;
            showResetModal.value = false;
        },
    });
}

const openExplanations = ref({});
const loadingExplanation = ref({});

function toggleExplanation(id) {
    openExplanations.value[id] = !openExplanations.value[id];
}

async function requestAiExplanation(mistake) {
    if (!mistake.question_bank_id || loadingExplanation.value[mistake.id]) return;

    loadingExplanation.value[mistake.id] = true;
    try {
        const response = await axios.post(`/question-bank/${mistake.question_bank_id}/ai-explain`);
        if (response.data?.success && response.data?.explanation) {
            mistake.explanation = response.data.explanation;
            openExplanations.value[mistake.id] = true;
        }
    } catch (e) {
        console.error('Failed to load AI explanation:', e);
    } finally {
        loadingExplanation.value[mistake.id] = false;
    }
}

function applyFilters() {
    router.get('/mistakes', {
        status: activeStatus.value,
        subject_id: selectedSubjectId.value || undefined,
        institution: selectedInstitution.value || undefined,
        search: searchQuery.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
}

function setStatus(status) {
    activeStatus.value = status;
    applyFilters();
}

function onSubjectChange() {
    applyFilters();
}

function onInstitutionChange() {
    applyFilters();
}

function onSearch() {
    applyFilters();
}

function resetFilters() {
    activeStatus.value = 'unresolved';
    selectedSubjectId.value = '';
    selectedInstitution.value = '';
    searchQuery.value = '';
    applyFilters();
}

async function startMistakePractice() {
    if (practicing.value) return;
    practicing.value = true;
    practiceError.value = '';

    try {
        const response = await axios.post('/mistakes/practice', {
            subject_id: selectedSubjectId.value || undefined,
            institution: selectedInstitution.value || undefined,
            limit: practiceCount.value,
        });

        if (response.data?.success && response.data?.redirect_url) {
            window.location.href = response.data.redirect_url;
        } else {
            practiceError.value = response.data?.message || 'অনুশীলন শুরু করা যায়নি।';
            practicing.value = false;
        }
    } catch (err) {
        practiceError.value = err.response?.data?.message || 'অনুশীলন সেশন তৈরিতে সমস্যা হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।';
        practicing.value = false;
    }
}

function parseOptions(options) {
    if (!options) return [];
    if (typeof options === 'string') {
        try {
            return JSON.parse(options);
        } catch {
            return [];
        }
    }
    return Array.isArray(options) ? options : [];
}

function getOptionKey(opt, index) {
    if (typeof opt === 'object' && opt !== null) {
        return opt.key || String.fromCharCode(65 + index);
    }
    return String.fromCharCode(65 + index);
}

function getOptionText(opt) {
    if (typeof opt === 'object' && opt !== null) {
        return opt.text || '';
    }
    return String(opt || '');
}

function isSelectedAnswer(mistake, opt, index) {
    const key = getOptionKey(opt, index);
    const text = getOptionText(opt);
    const ans = String(mistake.student_answer || '').trim().toLowerCase();
    return ans && (ans === key.toLowerCase() || ans === text.toLowerCase());
}

function isCorrectAnswer(mistake, opt, index) {
    const key = getOptionKey(opt, index);
    const text = getOptionText(opt);
    const correctOpt = String(mistake.correct_option || '').trim().toLowerCase();
    const correctAns = String(mistake.correct_answer || '').trim().toLowerCase();

    return (correctOpt && key.toLowerCase() === correctOpt) ||
           (correctAns && (text.toLowerCase() === correctAns || key.toLowerCase() === correctAns));
}
</script>

<template>
    <Head :title="locale === 'en' ? 'Mistake Review - Revision Book' : 'আমার ভুলসমূহ (Mistake Review) - রিভিশন বুক'" />
    <StudentLayout>
        <div class="space-y-6 pb-12">
            <!-- Hero / Header Section -->
            <section class="sheen anim-rise relative overflow-hidden rounded-3xl border border-[var(--border)] bg-gradient-to-br from-rose-500/10 via-[var(--surface)] to-[var(--primary)]/10 p-6 sm:p-8 shadow-sm">
                <div class="hero-glow pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full bg-rose-500/15"></div>
                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/15 px-3 py-1 text-xs font-bold text-rose-600 dark:text-rose-400">
                                <SparklesIcon class="h-3.5 w-3.5" />
                                {{ locale === 'en' ? 'Smart Revision Book' : 'স্মার্ট রিভিশন বুক' }}
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full border border-[var(--border)] bg-[var(--surface)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                                💡 {{ locale === 'en' ? 'Learn from mistakes' : 'ভুল থেকে শিক্ষা' }}
                            </span>
                        </div>
                        <h1 class="mt-3 font-heading text-2xl font-black sm:text-3xl lg:text-4xl text-[var(--text)]">
                            {{ locale === 'en' ? 'My Mistakes & Weak Areas' : 'আমার ভুল প্রশ্নসমূহ (Mistake Review)' }}
                        </h1>
                        <p class="mt-2 text-sm text-[var(--text-muted)] sm:text-base leading-relaxed">
                            {{ locale === 'en'
                                ? 'Analyze questions you answered incorrectly in past exams, review explanations, and re-test them repeatedly until mastered.'
                                : 'বিগত পরীক্ষা ও প্রশ্নব্যাংক প্র্যাকটিসের যে প্রশ্নগুলোতে ভুল করেছিলেন, সেগুলো নিয়মিত রিভিশন দিন এবং পুনরায় পরীক্ষা দিয়ে দুর্বলতা দূর করুন।'
                            }}
                        </p>
                    </div>

                    <!-- Quick Practice Action Button -->
                    <div class="shrink-0 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <button
                            @click="showPracticeModal = true"
                            :disabled="practicing || (stats?.total_mistakes === 0)"
                            class="btn btn-primary inline-flex items-center justify-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-bold shadow-md hover:shadow-lg transition-all"
                        >
                            <ArrowPathIcon v-if="practicing" class="h-4 w-4 animate-spin" />
                            <ArrowPathIcon v-else class="h-4 w-4" />
                            <span>{{ locale === 'en' ? 'Re-test Mistake Questions' : 'ভুল প্রশ্নগুলো পুনরায় প্র্যাকটিস করুন' }}</span>
                        </button>

                        <Link
                            href="/question-bank"
                            class="btn btn-secondary inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3.5 text-sm font-semibold"
                        >
                            <AcademicCapIcon class="h-4 w-4" />
                            <span>{{ locale === 'en' ? 'Question Bank' : 'প্রশ্নব্যাংক' }}</span>
                        </Link>

                        <button
                            v-if="(stats?.total_mistakes || 0) > 0"
                            type="button"
                            @click="showResetModal = true"
                            class="inline-flex items-center justify-center gap-1.5 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 transition-all cursor-pointer"
                        >
                            <TrashIcon class="h-4 w-4" />
                            <span>{{ locale === 'en' ? 'Reset Mistakes' : 'ভুল খাতা রিসেট' }}</span>
                        </button>
                    </div>
                </div>

                <!-- 4 Quick Metric Cards -->
                <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)]/80 p-4 backdrop-blur-md">
                        <div class="text-xs font-semibold text-[var(--text-muted)]">{{ locale === 'en' ? 'Total Mistakes' : 'মোট ভুল প্রশ্ন' }}</div>
                        <div class="mt-1 font-heading text-2xl font-extrabold text-rose-600 dark:text-rose-400">
                            {{ stats?.total_mistakes || 0 }}
                        </div>
                    </div>
                    <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)]/80 p-4 backdrop-blur-md">
                        <div class="text-xs font-semibold text-[var(--text-muted)]">{{ locale === 'en' ? 'Unresolved' : 'সংশোধন প্রয়োজন' }}</div>
                        <div class="mt-1 font-heading text-2xl font-extrabold text-amber-600 dark:text-amber-400">
                            {{ stats?.total_mistakes || 0 }}
                        </div>
                    </div>
                    <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)]/80 p-4 backdrop-blur-md">
                        <div class="text-xs font-semibold text-[var(--text-muted)]">{{ locale === 'en' ? 'Mastered / Correct' : 'মাস্টার্ড প্রশ্ন' }}</div>
                        <div class="mt-1 font-heading text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                            {{ stats?.mastered_count || 0 }}
                        </div>
                    </div>
                    <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)]/80 p-4 backdrop-blur-md">
                        <div class="text-xs font-semibold text-[var(--text-muted)]">{{ locale === 'en' ? 'Accuracy Rate' : 'সামগ্রিক নির্ভুলতা' }}</div>
                        <div class="mt-1 font-heading text-2xl font-extrabold text-[var(--primary)]">
                            {{ stats?.accuracy || 0 }}%
                        </div>
                    </div>
                </div>
            </section>

            <!-- Filters & Search Toolbar -->
            <div class="card rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <!-- Status Filter Tabs -->
                    <div class="flex flex-wrap items-center gap-1.5 p-1 rounded-xl bg-[var(--surface2)]/80 border border-[var(--border)]">
                        <button
                            @click="setStatus('unresolved')"
                            class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all"
                            :class="activeStatus === 'unresolved' ? 'bg-[var(--surface)] text-[var(--text)] shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            {{ locale === 'en' ? 'Need Revision' : 'সংশোধন প্রয়োজন' }}
                        </button>
                        <button
                            @click="setStatus('all')"
                            class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all"
                            :class="activeStatus === 'all' ? 'bg-[var(--surface)] text-[var(--text)] shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            {{ locale === 'en' ? 'All Mistakes' : 'সকল ভুল' }}
                        </button>
                        <button
                            @click="setStatus('mastered')"
                            class="rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all"
                            :class="activeStatus === 'mastered' ? 'bg-[var(--surface)] text-[var(--text)] shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            {{ locale === 'en' ? 'Mastered' : 'মাস্টার্ড' }}
                        </button>
                    </div>

                    <!-- Dropdowns: Subject, University & Search -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- University Filter -->
                        <div class="relative min-w-[160px]">
                            <select
                                v-model="selectedInstitution"
                                @change="onInstitutionChange"
                                class="input text-xs py-2 pr-8 font-medium cursor-pointer"
                            >
                                <option value="">{{ locale === 'en' ? 'All Universities' : 'সকল বিশ্ববিদ্যালয় / প্রতিষ্ঠান' }}</option>
                                <option v-for="inst in institutions" :key="inst.institution" :value="inst.institution">
                                    {{ inst.institution }} ({{ inst.mistakes_count }})
                                </option>
                            </select>
                        </div>

                        <!-- Subject Filter -->
                        <div class="relative min-w-[140px]">
                            <select
                                v-model="selectedSubjectId"
                                @change="onSubjectChange"
                                class="input text-xs py-2 pr-8 font-medium cursor-pointer"
                            >
                                <option value="">{{ locale === 'en' ? 'All Subjects' : 'সকল বিষয়' }}</option>
                                <option v-for="subj in subjects" :key="subj.subject_id" :value="subj.subject_id">
                                    {{ subj.subject_name }} ({{ subj.mistakes_count }})
                                </option>
                            </select>
                        </div>

                        <!-- Search Box -->
                        <div class="relative flex-1 sm:w-64">
                            <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                            <input
                                v-model="searchQuery"
                                @keyup.enter="onSearch"
                                type="text"
                                :placeholder="locale === 'en' ? 'Search question text...' : 'প্রশ্ন খুঁজুন...'"
                                class="input text-xs pl-9 pr-3 py-2 w-full"
                            />
                        </div>

                        <button
                            v-if="selectedSubjectId || selectedInstitution || searchQuery || activeStatus !== 'unresolved'"
                            @click="resetFilters"
                            class="text-xs font-bold text-rose-500 hover:underline px-2 py-1"
                        >
                            {{ locale === 'en' ? 'Clear' : 'রিসেট' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mistakes Question List -->
            <div v-if="mistakes?.data?.length" class="space-y-4">
                <article
                    v-for="(mistake, idx) in mistakes.data"
                    :key="mistake.id"
                    class="card rounded-2xl p-5 sm:p-6 transition-all hover:border-[var(--primary)]/40 hover:shadow-sm"
                >
                    <!-- Question Header & Badges -->
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[var(--border)] pb-3 mb-4 text-xs">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-bold text-[var(--text-muted)]">
                                #{{ (mistakes.current_page - 1) * mistakes.per_page + idx + 1 }}
                            </span>

                            <span
                                v-if="mistake.institution || mistake.origin_tag"
                                class="inline-flex items-center gap-1 rounded-md bg-blue-500/10 px-2 py-0.5 font-bold text-blue-600 dark:text-blue-400"
                            >
                                <BuildingLibraryIcon class="h-3 w-3" />
                                {{ mistake.institution || mistake.origin_tag }}
                            </span>

                            <span
                                v-if="mistake.subject_name"
                                class="inline-flex items-center gap-1 rounded-md bg-[var(--primary)]/10 px-2 py-0.5 font-bold text-[var(--primary)]"
                            >
                                <BookOpenIcon class="h-3 w-3" />
                                {{ mistake.subject_name }}
                            </span>

                            <span v-if="mistake.exam_name" class="text-[var(--text-muted)] truncate max-w-[200px]">
                                {{ mistake.exam_name }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span
                                v-if="!mistake.is_correct"
                                class="inline-flex items-center gap-1 rounded-full bg-rose-500/10 px-2.5 py-0.5 text-xs font-bold text-rose-600 dark:text-rose-400"
                            >
                                <XCircleIcon class="h-3.5 w-3.5" />
                                {{ locale === 'en' ? 'Incorrect' : 'ভুল উত্তর হয়েছিল' }}
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-bold text-emerald-600 dark:text-emerald-400"
                            >
                                <CheckCircleIcon class="h-3.5 w-3.5" />
                                {{ locale === 'en' ? 'Mastered' : 'মাস্টার্ড ✓' }}
                            </span>

                            <span class="text-[var(--text-muted)] text-[11px]">
                                {{ new Date(mistake.created_at).toLocaleDateString() }}
                            </span>
                        </div>
                    </div>

                    <!-- Question Body -->
                    <div
                        class="prose prose-sm dark:prose-invert max-w-none text-base font-semibold leading-relaxed text-[var(--text)] mb-4"
                        v-html="renderMath(mistake.question_text)"
                    ></div>

                    <!-- Options Grid -->
                    <div class="grid gap-2 sm:grid-cols-2 mb-4">
                        <div
                            v-for="(opt, optIdx) in parseOptions(mistake.options)"
                            :key="optIdx"
                            class="flex items-start gap-3 rounded-xl border p-3 text-sm transition-all"
                            :class="[
                                isCorrectAnswer(mistake, opt, optIdx)
                                    ? 'border-emerald-500/50 bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 font-semibold ring-1 ring-emerald-500/30'
                                    : isSelectedAnswer(mistake, opt, optIdx)
                                    ? 'border-rose-500/50 bg-rose-500/10 text-rose-800 dark:text-rose-300 font-semibold ring-1 ring-rose-500/30'
                                    : 'border-[var(--border)] bg-[var(--surface2)]/40 text-[var(--text)]'
                            ]"
                        >
                            <span
                                class="grid h-6 w-6 shrink-0 place-items-center rounded-lg text-xs font-bold"
                                :class="[
                                    isCorrectAnswer(mistake, opt, optIdx)
                                        ? 'bg-emerald-500 text-white'
                                        : isSelectedAnswer(mistake, opt, optIdx)
                                        ? 'bg-rose-500 text-white'
                                        : 'bg-[var(--surface)] text-[var(--text-muted)]'
                                ]"
                            >
                                {{ getOptionKey(opt, optIdx) }}
                            </span>

                            <div class="flex-1 min-w-0">
                                <div v-html="renderMath(getOptionText(opt))"></div>
                            </div>

                            <div class="shrink-0 text-xs">
                                <span
                                    v-if="isCorrectAnswer(mistake, opt, optIdx)"
                                    class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold"
                                >
                                    <CheckCircleIcon class="h-4 w-4" />
                                    {{ locale === 'en' ? 'Correct Answer' : 'সঠিক উত্তর' }}
                                </span>
                                <span
                                    v-else-if="isSelectedAnswer(mistake, opt, optIdx)"
                                    class="inline-flex items-center gap-1 text-rose-600 dark:text-rose-400 font-bold"
                                >
                                    <XCircleIcon class="h-4 w-4" />
                                    {{ locale === 'en' ? 'Your Answer' : 'আপনার উত্তর' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Explanation Toggle / Action Area -->
                    <div class="pt-2 flex flex-wrap items-center justify-between gap-3 border-t border-[var(--border)]">
                        <div class="flex items-center gap-2">
                            <button
                                v-if="mistake.explanation"
                                @click="toggleExplanation(mistake.id)"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-[var(--primary)] hover:underline"
                            >
                                <LightBulbIcon class="h-4 w-4" />
                                <span>{{ openExplanations[mistake.id] ? (locale === 'en' ? 'Hide Explanation' : 'ব্যাখ্যা লুকান') : (locale === 'en' ? 'View Explanation' : 'ব্যাখ্যা দেখুন') }}</span>
                                <ChevronDownIcon class="h-3.5 w-3.5 transition-transform" :class="openExplanations[mistake.id] ? 'rotate-180' : ''" />
                            </button>

                            <button
                                v-else-if="mistake.question_bank_id"
                                @click="requestAiExplanation(mistake)"
                                :disabled="loadingExplanation[mistake.id]"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-[var(--secondary)] hover:underline"
                            >
                                <SparklesIcon class="h-4 w-4" :class="loadingExplanation[mistake.id] ? 'animate-spin' : ''" />
                                <span>{{ loadingExplanation[mistake.id] ? (locale === 'en' ? 'Generating AI explanation...' : 'AI ব্যাখ্যা তৈরি হচ্ছে...') : (locale === 'en' ? 'Generate AI Explanation' : 'AI ব্যাখ্যা দেখুন') }}</span>
                            </button>
                        </div>

                        <span v-if="mistake.exam_session_id" class="text-xs text-[var(--text-muted)]">
                            <Link :href="`/exams/${mistake.exam_session_id}/result`" class="hover:text-[var(--primary)] hover:underline">
                                {{ locale === 'en' ? 'Exam Result' : 'পরীক্ষার ফলাফল দেখুন' }} →
                            </Link>
                        </span>
                    </div>

                    <!-- Expanded Explanation Box -->
                    <div
                        v-if="openExplanations[mistake.id] && mistake.explanation"
                        class="mt-3 rounded-xl border border-[var(--primary)]/20 bg-[var(--primary)]/5 p-4 text-sm leading-relaxed"
                    >
                        <div class="mb-1 text-xs font-bold uppercase tracking-wider text-[var(--primary)]">
                            💡 {{ locale === 'en' ? 'Detailed Explanation' : 'প্রশ্নের বিস্তারিত ব্যাখ্যা' }}
                        </div>
                        <div class="prose prose-sm dark:prose-invert max-w-none" v-html="renderMath(mistake.explanation)"></div>
                    </div>
                </article>

                <Pagination :links="mistakes.links" />
            </div>

            <!-- Empty State -->
            <div v-else class="card rounded-3xl border-dashed p-10 text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-emerald-500/10 text-emerald-500 text-3xl mb-4">
                    🎉
                </div>
                <h3 class="font-heading text-xl font-bold text-[var(--text)]">
                    {{ locale === 'en' ? 'No Mistake Questions Found!' : 'কোনো অমীমাংসিত ভুল প্রশ্ন পাওয়া যায়নি!' }}
                </h3>
                <p class="mt-2 text-sm text-[var(--text-muted)] max-w-md mx-auto">
                    {{ locale === 'en'
                        ? 'Great job! You have either resolved all previous mistakes or haven\'t taken any exams yet.'
                        : 'দারুণ অগ্রগতি! আপনি পূর্বের সকল ভুল সমাধান করেছেন অথবা এই ফিল্টারে কোনো ভুল প্রশ্ন নেই। প্রস্তুতি যাচাইয়ে নতুন পরীক্ষা দিন।'
                    }}
                </p>
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <Link href="/question-bank" class="btn btn-primary">
                        <AcademicCapIcon class="h-4 w-4" />
                        <span>{{ locale === 'en' ? 'Practice Question Bank' : 'প্রশ্নব্যাংক অনুশীলন করুন' }}</span>
                    </Link>
                    <Link href="/exams/create" class="btn btn-secondary">
                        <span>{{ locale === 'en' ? 'Start Model Test' : 'মডেল টেস্ট দিন' }}</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Practice Mistake Modal -->
        <div
            v-if="showPracticeModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
        >
            <div class="card w-full max-w-md rounded-3xl p-6 sm:p-7 shadow-2xl anim-rise">
                <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
                    <div class="flex items-center gap-2">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-rose-500/15 text-rose-600 dark:text-rose-400">
                            <ArrowPathIcon class="h-5 w-5" />
                        </span>
                        <div>
                            <h3 class="font-heading font-extrabold text-lg text-[var(--text)]">
                                {{ locale === 'en' ? 'Re-test Mistake Questions' : 'ভুল প্রশ্ন রিভিশন পরীক্ষা' }}
                            </h3>
                            <p class="text-xs text-[var(--text-muted)]">
                                {{ locale === 'en' ? 'Practice your mistakes until 100% mastered' : 'ভুল প্রশ্নগুলো পুনরায় সমাধান করে দুর্বলতা দূর করুন' }}
                            </p>
                        </div>
                    </div>
                    <button
                        @click="showPracticeModal = false"
                        class="rounded-lg p-1 text-[var(--text-muted)] hover:bg-[var(--surface2)]"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-4 space-y-4">
                    <div>
                        <label class="label text-xs">{{ locale === 'en' ? 'University / Institution Scope' : 'বিশ্ববিদ্যালয় / প্রতিষ্ঠান ফিল্টার' }}</label>
                        <select v-model="selectedInstitution" class="input text-xs py-2">
                            <option value="">{{ locale === 'en' ? 'All (General Mistakes)' : 'সকল বিশ্ববিদ্যালয়ের ভুল প্রশ্ন' }}</option>
                            <option v-for="inst in institutions" :key="inst.institution" :value="inst.institution">
                                {{ inst.institution }} ({{ inst.mistakes_count }})
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="label text-xs">{{ locale === 'en' ? 'Subject Scope' : 'বিষয় ফিল্টার' }}</label>
                        <select v-model="selectedSubjectId" class="input text-xs py-2">
                            <option value="">{{ locale === 'en' ? 'All Subjects' : 'সকল বিষয়ের ভুল প্রশ্ন' }}</option>
                            <option v-for="subj in subjects" :key="subj.subject_id" :value="subj.subject_id">
                                {{ subj.subject_name }} ({{ subj.mistakes_count }})
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="label text-xs">{{ locale === 'en' ? 'Number of Questions' : 'প্রশ্নের সংখ্যা' }}</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                v-for="cnt in [10, 20, 30]"
                                :key="cnt"
                                type="button"
                                @click="practiceCount = cnt"
                                class="rounded-xl border py-2 text-xs font-bold transition-all text-center"
                                :class="practiceCount === cnt ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)]'"
                            >
                                {{ cnt }} টি প্রশ্ন
                            </button>
                        </div>
                    </div>

                    <div v-if="practiceError" class="rounded-xl bg-rose-500/10 p-3 text-xs font-semibold text-rose-600 dark:text-rose-400">
                        {{ practiceError }}
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2">
                        <button
                            type="button"
                            @click="showPracticeModal = false"
                            class="btn btn-secondary text-xs px-4 py-2"
                        >
                            {{ locale === 'en' ? 'Cancel' : 'বাতিল' }}
                        </button>

                        <button
                            type="button"
                            @click="startMistakePractice"
                            :disabled="practicing"
                            class="btn btn-primary text-xs px-5 py-2 inline-flex items-center gap-1.5"
                        >
                            <ArrowPathIcon v-if="practicing" class="h-4 w-4 animate-spin" />
                            <span>{{ practicing ? (locale === 'en' ? 'Preparing test...' : 'টেস্ট তৈরি হচ্ছে...') : (locale === 'en' ? 'Start Practice Now' : 'টেস্ট শুরু করুন') }}</span>
                            <ArrowRightIcon v-if="!practicing" class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reset Confirmation Modal -->
            <div
                v-if="showResetModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
                @click.self="showResetModal = false"
            >
                <div class="relative w-full max-w-md rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-2xl space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 shrink-0">
                            <TrashIcon class="h-6 w-6" />
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-base text-[var(--text)]">
                                {{ locale === 'en' ? 'Reset Mistake Questions?' : 'ভুল প্রশ্নের তালিকা রিসেট করবেন?' }}
                            </h3>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                {{ locale === 'en' ? 'This will clear all unresolved mistakes from your revision notebook.' : 'এটি আপনার ভুল খাতার সকল প্রশ্ন সমাধানকৃত হিসেবে চিহ্নিত করে নতুন করে শুরু করবে।' }}
                            </p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-700 dark:text-amber-400">
                        ⚠️ {{ locale === 'en' ? 'This action cannot be undone. Are you sure you want to proceed?' : 'এই কাজটি অপরিবর্তনীয়। আপনি কি নিশ্চিত যে তালিকা রিসেট করতে চান?' }}
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2">
                        <button
                            type="button"
                            @click="showResetModal = false"
                            class="btn btn-secondary text-xs px-4 py-2"
                        >
                            {{ locale === 'en' ? 'Cancel' : 'বাতিল' }}
                        </button>

                        <button
                            type="button"
                            @click="resetMistakes"
                            :disabled="resetting"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white hover:bg-rose-700 disabled:opacity-50 transition"
                        >
                            <ArrowPathIcon v-if="resetting" class="h-3.5 w-3.5 animate-spin" />
                            <span>{{ resetting ? (locale === 'en' ? 'Resetting...' : 'রিসেট হচ্ছে...') : (locale === 'en' ? 'Confirm Reset' : 'হ্যাঁ, রিসেট করুন') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
