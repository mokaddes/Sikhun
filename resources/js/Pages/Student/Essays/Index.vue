<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';
import {
    PencilSquareIcon,
    PlusIcon,
    ArrowRightIcon,
    SparklesIcon,
    ClockIcon,
    CheckBadgeIcon,
} from '@heroicons/vue/24/outline';

defineProps({ essays: Object });
const { t, locale } = useI18n();

const formatRubric = (type) => {
    switch (type) {
        case 'hsc_bangla': return locale.value === 'en' ? 'HSC Bengali Essay' : 'এইচএসসি বাংলা রচনা';
        case 'hsc_english': return locale.value === 'en' ? 'HSC English Essay' : 'এইচএসসি ইংরেজি রচনা';
        case 'general': return locale.value === 'en' ? 'General Writing' : 'সাধারণ রচনা / অনুচ্ছেদ';
        default: return type.replace('_', ' ').toUpperCase();
    }
};
</script>

<template>
    <Head :title="t('essays.title')" />
    <StudentLayout>
        <!-- Header -->
        <header class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--accent)]/10 px-3 py-1 text-xs font-semibold text-[var(--accent)]">
                    <SparklesIcon class="h-3.5 w-3.5" />
                    {{ locale === 'en' ? 'AI Writing Evaluation' : 'এআই রচনা ও অনুবাদ মূল্যায়ন' }}
                </span>
                <h1 class="mt-2 font-heading text-2xl font-black tracking-tight sm:text-3xl">
                    {{ t('essays.title') }}
                </h1>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en'
                        ? 'Submit essays or answers for AI-assisted grammatical and rubric-based grading.'
                        : 'আপনার লিখিত রচনা বা উত্তর জমা দিয়ে এআই দ্বারা বিস্তারিত নম্বর ও গঠনমূলক ফিডব্যাক পান।' }}
                </p>
            </div>
            <Link
                href="/essays/create"
                class="btn btn-primary inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-bold shadow-md shadow-[var(--primary)]/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
            >
                <PlusIcon class="h-5 w-5" />
                <span>{{ t('essays.new_essay') }}</span>
            </Link>
        </header>

        <!-- Essays Grid Cards -->
        <div v-if="essays.data && essays.data.length" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="e in essays.data"
                :key="e.id"
                class="group relative flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 transition-all duration-200 hover:-translate-y-1 hover:border-[var(--accent)]/40 hover:shadow-lg hover:shadow-[var(--shadow-color)]"
            >
                <div>
                    <!-- Top badges -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="inline-flex items-center rounded-lg bg-[var(--accent)]/10 px-2.5 py-1 text-xs font-bold text-[var(--accent)] border border-[var(--accent)]/20">
                            {{ formatRubric(e.grading_type) }}
                        </span>
                        <span
                            class="rounded-lg px-2.5 py-0.5 text-[11px] font-semibold"
                            :class="e.status === 'graded' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-500 border border-amber-500/20'"
                        >
                            {{ e.status === 'graded' ? (locale === 'en' ? 'Graded' : 'মূল্যায়িত') : (locale === 'en' ? 'In Review' : 'পর্যালোচনাধীন') }}
                        </span>
                    </div>

                    <!-- Text Snippet -->
                    <p class="text-sm font-medium text-[var(--text)] line-clamp-3 leading-relaxed mb-3">
                        {{ e.essay_text }}
                    </p>

                    <div class="flex items-center gap-1.5 text-xs text-[var(--text-muted)]">
                        <ClockIcon class="h-3.5 w-3.5" />
                        <span>{{ new Date(e.created_at).toLocaleDateString() }}</span>
                    </div>
                </div>

                <!-- Score and Action -->
                <div class="mt-5 border-t border-[var(--border)]/70 pt-4 flex items-center justify-between gap-3">
                    <div v-if="e.result">
                        <span class="text-xs text-[var(--text-muted)] block font-semibold">{{ locale === 'en' ? 'Score' : 'প্রাপ্ত নম্বর' }}</span>
                        <span class="font-heading text-lg font-black text-emerald-600 dark:text-emerald-400">
                            {{ e.result.total_score }} <span class="text-xs text-[var(--text-muted)] font-normal">/ {{ e.result.max_score }}</span>
                        </span>
                    </div>
                    <div v-else>
                        <span class="text-xs text-[var(--text-muted)] italic">{{ locale === 'en' ? 'Grading...' : 'মূল্যায়ন চলছে...' }}</span>
                    </div>

                    <Link
                        :href="`/essays/${e.id}`"
                        class="btn btn-secondary inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold"
                    >
                        <span>{{ locale === 'en' ? 'View Review' : 'রিভিউ দেখুন' }}</span>
                        <ArrowRightIcon class="h-3.5 w-3.5" />
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
                <PencilSquareIcon class="h-8 w-8 opacity-60" />
            </div>
            <h3 class="font-heading text-lg font-bold">
                {{ t('essays.no_essays') }}
            </h3>
            <p class="mt-1 max-w-sm text-sm text-[var(--text-muted)]">
                {{ locale === 'en'
                    ? 'Submit an essay to receive AI-powered grading, feedback, and improvement tips.'
                    : 'এখনো কোনো রচনা জমা দেননি। নির্ভুল মূল্যায়ন ও পরামর্শ পেতে এখনই নতুন রচনা জমা দিন।' }}
            </p>
            <Link href="/essays/create" class="btn btn-primary mt-5 inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-md">
                <PlusIcon class="h-4 w-4" />
                {{ t('essays.new_essay') }}
            </Link>
        </div>

        <div class="mt-8" v-if="essays.links && essays.links.length > 3">
            <Pagination :links="essays.links" />
        </div>
    </StudentLayout>
</template>
