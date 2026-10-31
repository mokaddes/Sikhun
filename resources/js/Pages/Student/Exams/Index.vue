<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';
import {
    ClipboardDocumentCheckIcon,
    ClipboardDocumentListIcon,
    PlusIcon,
    ArrowRightIcon,
    ClockIcon,
    CheckCircleIcon,
    ChartBarIcon,
    TrophyIcon,
    MagnifyingGlassIcon,
    SparklesIcon,
    BookOpenIcon,
    TagIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    exams: Object,
    stats: {
        type: Object,
        default: () => ({ total: 0, completed: 0, avg_score: 0, highest_score: 0 }),
    },
    filters: {
        type: Object,
        default: () => ({ search: '', status: 'all' }),
    },
});

const { t, locale } = useI18n();

const search = ref(props.filters.search || '');
const activeStatus = ref(props.filters.status || 'all');
const retryingExamId = ref(null);

const filterTabs = computed(() => [
    { id: 'all', label: locale.value === 'en' ? 'All Exams' : 'সকল পরীক্ষা' },
    { id: 'completed', label: locale.value === 'en' ? 'Completed' : 'সম্পন্ন' },
    { id: 'in_progress', label: locale.value === 'en' ? 'In Progress' : 'চলমান' },
    { id: 'practice', label: locale.value === 'en' ? 'Practice' : 'অনুশীলন' },
    { id: 'exam', label: locale.value === 'en' ? 'Exam Mode' : 'পরীক্ষা মোড' },
]);

function applyFilters(status = activeStatus.value) {
    activeStatus.value = status;
    router.get(
        '/exams',
        {
            search: search.value.trim() || undefined,
            status: status !== 'all' ? status : undefined,
        },
        { preserveState: true, replace: true }
    );
}

function clearSearch() {
    search.value = '';
    applyFilters();
}

function retryExam(exam) {
    retryingExamId.value = exam.id;
    router.post(`/exams/${exam.id}/retry`, {}, {
        onFinish: () => { retryingExamId.value = null; },
    });
}

const statusBadge = (status) => {
    switch (status) {
        case 'completed':
            return {
                bg: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20',
                label: locale.value === 'en' ? 'Completed' : 'সম্পন্ন',
            };
        case 'in_progress':
            return {
                bg: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20',
                label: locale.value === 'en' ? 'In Progress' : 'চলমান',
            };
        case 'generating':
            return {
                bg: 'bg-[var(--primary)]/10 text-[var(--primary)] border border-[var(--primary)]/20 animate-pulse',
                label: locale.value === 'en' ? 'Generating...' : 'তৈরি হচ্ছে...',
            };
        case 'failed':
            return {
                bg: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20',
                label: locale.value === 'en' ? 'Generation failed' : 'তৈরি ব্যর্থ',
            };
        default:
            return {
                bg: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20',
                label: status,
            };
    }
};

const scoreColor = (pct) => {
    if (pct >= 70) return 'text-emerald-500 dark:text-emerald-400 border-emerald-500/30 bg-emerald-500/10';
    if (pct >= 40) return 'text-amber-500 dark:text-amber-400 border-amber-500/30 bg-amber-500/10';
    return 'text-rose-500 dark:text-rose-400 border-rose-500/30 bg-rose-500/10';
};
</script>

<template>
    <Head :title="t('exams.title')" />
    <StudentLayout>
        <!-- Header Section -->
        <header class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--secondary)]/10 px-3 py-1 text-xs font-semibold text-[var(--secondary)]">
                    <SparklesIcon class="h-3.5 w-3.5" />
                    {{ locale === 'en' ? 'AI Assessment & Practice' : 'এআই মূল্যায়ন ও অনুশীলন' }}
                </span>
                <h1 class="mt-2 font-heading text-2xl font-black tracking-tight sm:text-3xl">
                    {{ t('exams.title') }}
                </h1>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en'
                        ? 'Practice chapter tests, assess your learning curve, and excel in your exams.'
                        : 'অধ্যায়ভিত্তিক পরীক্ষা দিন, নিজের অগ্রগতি যাচাই করুন এবং লক্ষ্য পূরণ করুন।' }}
                </p>
            </div>
            <Link
                href="/exams/create"
                class="btn btn-primary inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-bold shadow-md shadow-[var(--primary)]/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
            >
                <PlusIcon class="h-5 w-5" />
                <span>{{ t('exams.new_exam') }}</span>
            </Link>
        </header>

        <!-- Top Metrics Cards -->
        <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="group relative overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 transition-all hover:border-[var(--secondary)]/40 hover:shadow-sm sm:p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                        {{ t('exams.total_exams') }}
                    </span>
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-blue-500/10 text-blue-500">
                        <ClipboardDocumentListIcon class="h-5 w-5" />
                    </span>
                </div>
                <div class="mt-3 font-heading text-2xl font-black tracking-tight sm:text-3xl">
                    {{ stats.total }}
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 transition-all hover:border-emerald-500/40 hover:shadow-sm sm:p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                        {{ t('exams.completed_exams') }}
                    </span>
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-500/10 text-emerald-500">
                        <CheckCircleIcon class="h-5 w-5" />
                    </span>
                </div>
                <div class="mt-3 font-heading text-2xl font-black tracking-tight sm:text-3xl text-emerald-600 dark:text-emerald-400">
                    {{ stats.completed }}
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 transition-all hover:border-amber-500/40 hover:shadow-sm sm:p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                        {{ t('exams.avg_score') }}
                    </span>
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-amber-500/10 text-amber-500">
                        <ChartBarIcon class="h-5 w-5" />
                    </span>
                </div>
                <div class="mt-3 font-heading text-2xl font-black tracking-tight sm:text-3xl">
                    {{ stats.avg_score }}%
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 transition-all hover:border-purple-500/40 hover:shadow-sm sm:p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                        {{ t('exams.highest_score') }}
                    </span>
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-purple-500/10 text-purple-500">
                        <TrophyIcon class="h-5 w-5" />
                    </span>
                </div>
                <div class="mt-3 font-heading text-2xl font-black tracking-tight sm:text-3xl text-purple-600 dark:text-purple-400">
                    {{ stats.highest_score }}%
                </div>
            </div>
        </div>

        <!-- Filter Tabs & Search Bar -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Tabs -->
            <div class="no-scrollbar flex items-center gap-1.5 overflow-x-auto rounded-xl bg-[var(--surface2)]/60 p-1 border border-[var(--border)]">
                <button
                    v-for="tab in filterTabs"
                    :key="tab.id"
                    type="button"
                    class="rounded-lg px-3.5 py-1.5 text-xs font-semibold transition-all whitespace-nowrap"
                    :class="activeStatus === tab.id
                        ? 'bg-[var(--surface)] text-[var(--text)] shadow-sm'
                        : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                    @click="applyFilters(tab.id)"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- Search -->
            <div class="relative w-full sm:w-64">
                <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                <input
                    v-model="search"
                    type="search"
                    :placeholder="locale === 'en' ? 'Search exams...' : 'পরীক্ষা খুঁজুন...'"
                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] py-2 pl-9 pr-3 text-sm focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/15"
                    @keydown.enter="applyFilters()"
                />
            </div>
        </div>

        <!-- Responsive Grid Cards -->
        <div v-if="exams.data && exams.data.length" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="exam in exams.data"
                :key="exam.id"
                class="group relative flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 transition-all duration-200 hover:-translate-y-1 hover:border-[var(--secondary)]/40 hover:shadow-lg hover:shadow-[var(--shadow-color)]"
            >
                <!-- Card Top: Tags & Status -->
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <!-- Mode Badge -->
                            <span
                                class="inline-flex items-center rounded-lg px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider"
                                :class="exam.mode === 'exam'
                                    ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20'
                                    : 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20'"
                            >
                                {{ exam.mode === 'exam' ? (locale === 'en' ? 'Exam' : 'পরীক্ষা') : (locale === 'en' ? 'Practice' : 'অনুশীলন') }}
                            </span>
                            <!-- Question Type -->
                            <span class="inline-flex items-center gap-1 rounded-lg bg-[var(--surface2)] px-2 py-1 text-[11px] font-medium text-[var(--text-muted)] border border-[var(--border)]">
                                <TagIcon class="h-3 w-3" />
                                {{ exam.config?.type?.toUpperCase() || 'MCQ' }}
                            </span>
                        </div>

                        <!-- Status badge -->
                        <span class="rounded-lg px-2.5 py-1 text-[11px] font-semibold" :class="statusBadge(exam.status).bg">
                            {{ statusBadge(exam.status).label }}
                        </span>
                    </div>

                    <!-- Exam Title -->
                    <h3 class="font-heading text-lg font-bold tracking-tight text-[var(--text)] line-clamp-1 group-hover:text-[var(--secondary)] transition-colors">
                        {{ exam.title || exam.display_title }}
                    </h3>

                    <!-- Source Detail (Book or Custom Topic) -->
                    <div class="mt-1.5 flex items-center gap-1.5 text-xs text-[var(--text-muted)] line-clamp-1">
                        <BookOpenIcon class="h-3.5 w-3.5 shrink-0" />
                        <span v-if="exam.source_type === 'book' && exam.book">
                            {{ exam.book.title }}
                        </span>
                        <span v-else-if="exam.source_text">
                            {{ exam.source_text }}
                        </span>
                        <span v-else>
                            {{ locale === 'en' ? 'General Topic' : 'সাধারণ বিষয়' }}
                        </span>
                    </div>
                </div>

                <!-- Card Middle: Key Info & Score -->
                <div class="my-4 border-t border-b border-[var(--border)]/60 py-3">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-xs text-[var(--text-muted)]">
                                <ClipboardDocumentCheckIcon class="h-4 w-4 text-[var(--text-muted)]" />
                                <span class="font-semibold text-[var(--text)]">{{ exam.total }}</span>
                                <span>{{ t('exams.questions') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs text-[var(--text-muted)]">
                                <ClockIcon class="h-4 w-4 text-[var(--text-muted)]" />
                                <span>{{ new Date(exam.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>

                        <!-- Score Pill if completed -->
                        <div v-if="exam.status === 'completed'" class="flex flex-col items-end">
                            <span class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1 text-sm font-black border" :class="scoreColor(exam.percentage)">
                                {{ exam.percentage }}%
                            </span>
                            <span class="text-[11px] text-[var(--text-muted)] mt-0.5 font-medium">
                                {{ exam.score }} / {{ exam.total }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Bottom: Action Link -->
                <div>
                    <Link
                        v-if="exam.status === 'completed'"
                        :href="`/exams/${exam.id}/result`"
                        class="btn btn-secondary w-full justify-center gap-2 rounded-xl py-2.5 text-xs font-bold"
                    >
                        <span>{{ t('exams.view_result') }}</span>
                        <ArrowRightIcon class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" />
                    </Link>
                    <button
                        v-if="exam.status === 'failed'"
                        type="button"
                        :disabled="retryingExamId === exam.id"
                        class="btn btn-primary w-full justify-center gap-2 rounded-xl py-2.5 text-xs font-bold shadow-sm disabled:opacity-60"
                        @click="retryExam(exam)"
                    >
                        <ArrowPathIcon class="h-3.5 w-3.5" :class="retryingExamId === exam.id ? 'animate-spin' : ''" />
                        <span>{{ retryingExamId === exam.id ? (locale === 'en' ? 'Retrying...' : 'আবার চেষ্টা হচ্ছে...') : (locale === 'en' ? 'Retry generation' : 'আবার তৈরি করুন') }}</span>
                    </button>
                    <Link
                        v-else
                        :href="`/exams/${exam.id}`"
                        class="btn btn-primary w-full justify-center gap-2 rounded-xl py-2.5 text-xs font-bold shadow-sm"
                    >
                        <span>{{ t('exams.continue_exam') }}</span>
                        <ArrowRightIcon class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-[var(--border)] bg-[var(--surface)] p-12 text-center"
        >
            <div class="grid h-16 w-16 place-items-center rounded-2xl bg-[var(--surface2)] text-[var(--text-muted)] mb-4">
                <ClipboardDocumentCheckIcon class="h-8 w-8 opacity-60" />
            </div>
            <h3 class="font-heading text-lg font-bold">
                {{ t('exams.no_exams') }}
            </h3>
            <p class="mt-1 max-w-sm text-sm text-[var(--text-muted)]">
                {{ locale === 'en'
                    ? 'You have not taken any exams yet, or no exams match your filter. Create one to test your knowledge!'
                    : 'এখনো কোনো পরীক্ষা গ্রহণ করেননি। জ্ঞান যাচাই করতে এখনই নতুন পরীক্ষা তৈরি করুন!' }}
            </p>
            <Link href="/exams/create" class="btn btn-primary mt-5 inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-md">
                <PlusIcon class="h-4 w-4" />
                {{ t('exams.new_exam') }}
            </Link>
        </div>

        <!-- Pagination -->
        <div class="mt-8" v-if="exams.links && exams.links.length > 3">
            <Pagination :links="exams.links" />
        </div>
    </StudentLayout>
</template>
