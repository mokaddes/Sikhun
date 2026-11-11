<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import StudentAvatar from '@/Components/UI/StudentAvatar.vue';
import { useI18n } from '@/i18n';
import { TrophyIcon, FireIcon, AcademicCapIcon, SparklesIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    period: String,
    filters: Object,
    studentType: String,
    top: Array,
    myRank: Object,
    optedOut: Boolean,
});

const { t, locale } = useI18n();

const period = ref(props.period);

watch(period, () => {
    router.get('/leaderboard', { period: period.value }, { preserveState: true, replace: true });
});

const currentTypeKey = computed(() => props.studentType || props.filters?.type || 'hsc');

const educationTypeLabels = {
    admission: {
        title: 'বিশ্ববিদ্যালয় ভর্তি প্রস্তুতি',
        short: 'ভর্তি প্রস্তুতি',
        desc: 'DU, RU, BUET, Medical, GST',
        badge: 'Admission',
        color: 'border-blue-500/30 bg-blue-500/10 text-blue-600 dark:text-blue-400',
    },
    job: {
        title: 'বিসিএস ও চাকরি প্রস্তুতি',
        short: 'বিসিএস ও চাকরি',
        desc: 'BCS, Bank, Primary, Govt Exam',
        badge: 'Job Exam',
        color: 'border-emerald-500/30 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    },
    hsc: {
        title: 'উচ্চ মাধ্যমিক (HSC)',
        short: 'HSC',
        desc: 'HSC Science, Arts, Commerce',
        badge: 'HSC',
        color: 'border-purple-500/30 bg-purple-500/10 text-purple-600 dark:text-purple-400',
    },
    ssc: {
        title: 'মাধ্যমিক (SSC)',
        short: 'SSC',
        desc: 'SSC Science, Arts, Commerce',
        badge: 'SSC',
        color: 'border-amber-500/30 bg-amber-500/10 text-amber-600 dark:text-amber-400',
    },
    university: {
        title: 'বিশ্ববিদ্যালয় অ্যাকাডেমিক',
        short: 'বিশ্ববিদ্যালয়',
        desc: 'Undergraduate / Semester Study',
        badge: 'University',
        color: 'border-indigo-500/30 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
    },
};

const activeTypeInfo = computed(() => {
    return educationTypeLabels[currentTypeKey.value] || {
        title: currentTypeKey.value.toUpperCase(),
        short: currentTypeKey.value.toUpperCase(),
        desc: '',
        badge: currentTypeKey.value.toUpperCase(),
        color: 'border-slate-500/30 bg-slate-500/10 text-slate-600 dark:text-slate-400',
    };
});

function badge(rank) {
    if (rank === 1) return '🥇';
    if (rank === 2) return '🥈';
    if (rank === 3) return '🥉';
    if (rank <= 10) return '🔥';
    if (rank <= 100) return '⭐';
    return '';
}

const rowClass = (rank) => rank === 1
    ? 'bg-[var(--primary)]/10'
    : rank <= 3 ? 'bg-[var(--surface2)]/60' : '';
</script>

<template>
    <Head :title="t('leaderboard_page.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6">
            <span class="eyebrow">{{ locale === 'en' ? 'Grow' : 'উন্নতি' }}</span>
            <div class="mt-1 flex flex-wrap items-center justify-between gap-3">
                <h1 class="flex items-center gap-2 font-heading text-2xl font-extrabold sm:text-3xl">
                    <TrophyIcon class="h-7 w-7 text-[var(--primary)]" /> {{ t('leaderboard_page.title') }}
                </h1>
                <div class="flex items-center gap-2">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="activeTypeInfo.color">
                        🎯 {{ activeTypeInfo.short }}
                    </span>
                </div>
            </div>
        </header>

        <!-- Scoped Cohort Banner -->
        <div class="anim-rise mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-[var(--primary)]/30 bg-[var(--primary)]/10 px-4 py-3.5 sm:px-5">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[var(--primary)]/20 text-xl">
                    🎯
                </span>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-heading text-sm sm:text-base font-bold">
                            {{ locale === 'en' ? 'Your Preparation Category:' : 'আপনার প্রস্তুতি ক্যাটাগরি:' }}
                        </span>
                        <span class="font-heading text-sm sm:text-base font-extrabold text-[var(--primary)]">
                            {{ activeTypeInfo.title }}
                        </span>
                        <span class="rounded-full px-2 py-0.5 text-[11px] font-bold" :class="activeTypeInfo.color">
                            {{ activeTypeInfo.badge }}
                        </span>
                    </div>
                    <p class="mt-0.5 text-xs text-[var(--text-muted)]">
                        {{ locale === 'en'
                            ? `You are competing exclusively with other ${activeTypeInfo.badge} students.`
                            : `আপনি শুধুমাত্র আপনার ক্যাটাগরির (${activeTypeInfo.short}) সহপাঠীদের সাথে প্রতিদ্বন্দ্বিতা করছেন।` }}
                    </p>
                </div>
            </div>

            <Link
                href="/profile/settings"
                class="inline-flex items-center gap-1 text-xs font-semibold text-[var(--primary)] hover:underline"
            >
                <span>{{ locale === 'en' ? 'Change Category in Settings →' : 'ক্যাটাগরি পরিবর্তন করুন →' }}</span>
            </Link>
        </div>

        <div v-if="optedOut" class="mb-5 rounded-2xl border border-[var(--accent)]/30 bg-[var(--accent)]/10 px-4 py-3 text-sm">
            {{ t('leaderboard_page.opt_out_notice') }}
        </div>

        <!-- Period Toggle & Stream Badge -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div class="flex rounded-full border border-[var(--border)] bg-[var(--surface)] p-1">
                <button
                    v-for="p in ['weekly', 'monthly', 'all_time']"
                    :key="p"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="period === p ? 'bg-[var(--primary)] text-white shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                    @click="period = p"
                >
                    {{ t(`leaderboard_page.${p}`) }}
                </button>
            </div>

            <span class="text-xs font-medium text-[var(--text-muted)]">
                {{ locale === 'en' ? 'Leaderboard updates automatically after completed exams' : 'পরীক্ষা সম্পন্ন করার সাথে সাথে লিডারবোর্ড আপডেট হয়' }}
            </span>
        </div>

        <!-- My Rank Card -->
        <div v-if="myRank" class="card anim-rise mb-5 flex items-center justify-between rounded-2xl border-[var(--primary)]/30 bg-[var(--primary)]/5 p-4 sm:px-6">
            <span class="flex items-center gap-2 text-sm font-medium">
                <FireIcon class="h-5 w-5 text-[var(--primary)]" />
                <span>{{ t('leaderboard_page.your_rank') }} ({{ activeTypeInfo.short }})</span>
            </span>
            <span class="font-heading text-lg font-extrabold text-[var(--primary)]">
                #{{ myRank.rank }} — {{ myRank.percentage }}%
            </span>
        </div>

        <!-- Top Students Table -->
        <div v-if="top.length" class="card overflow-hidden rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] text-left text-xs uppercase tracking-wide text-[var(--text-muted)]">
                            <th class="px-5 py-3 font-semibold">{{ t('leaderboard_page.rank') }}</th>
                            <th class="px-5 py-3 font-semibold">{{ t('leaderboard_page.student') }}</th>
                            <th class="px-5 py-3 font-semibold">{{ t('common.type') }}</th>
                            <th class="px-5 py-3 text-right font-semibold">{{ t('leaderboard_page.score') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="entry in top"
                            :key="entry.student_id"
                            class="border-b border-[var(--border)] transition-colors last:border-0 hover:bg-[var(--surface2)]/50"
                            :class="rowClass(entry.rank)"
                        >
                            <td class="px-5 py-3 font-semibold">
                                <span class="mr-1.5">{{ badge(entry.rank) }}</span>#{{ entry.rank }}
                            </td>
                            <td class="px-5 py-3 font-medium">
                                <div class="flex items-center gap-2.5">
                                    <StudentAvatar :avatar="entry.avatar" :name="entry.name" size="xs" />
                                    <span>{{ entry.name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs">
                                <span class="rounded-full px-2 py-0.5 font-semibold uppercase tracking-wider" :class="activeTypeInfo.color">
                                    {{ entry.type }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-heading font-bold text-[var(--text)]">
                                {{ entry.percentage }}%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="card flex flex-col items-center gap-3 rounded-2xl border-dashed p-12 text-center text-[var(--text-muted)]">
            <TrophyIcon class="h-10 w-10 opacity-40 text-[var(--primary)]" />
            <p class="font-medium text-base text-[var(--text)]">
                {{ activeTypeInfo.title }} ক্যাটাগরিতে এখনো কোনো এন্ট্রি নেই
            </p>
            <p class="text-xs max-w-md">
                {{ t('leaderboard_page.no_entries') }}
            </p>
            <Link href="/exams" class="btn btn-primary btn-sm rounded-xl mt-2 font-semibold">
                {{ locale === 'en' ? 'Start an Exam Now' : 'একটি পরীক্ষা শুরু করুন' }}
            </Link>
        </div>
    </StudentLayout>
</template>
