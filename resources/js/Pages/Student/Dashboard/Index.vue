<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    ChatBubbleOvalLeftEllipsisIcon,
    ClipboardDocumentCheckIcon,
    RectangleStackIcon,
    CalendarDaysIcon,
    BanknotesIcon,
    BookOpenIcon,
    AcademicCapIcon,
    ArrowRightIcon,
    FireIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: Object,
    continueReading: Object,
    continueCourse: Object,
    recentExams: Array,
    leaderboardTop: Array,
    myRank: Object,
    activeSubscription: Object,
    access: Object,
});
const { t, locale } = useI18n();
const student = usePage().props.auth?.student;

function badge(rank) {
    if (rank === 1) return '🥇';
    if (rank === 2) return '🥈';
    if (rank === 3) return '🥉';
    return '';
}

const statCards = computed(() => [
    { label: t('dashboard.wallet_balance'), value: `৳${props.stats?.wallet_balance ?? 0}`, icon: BanknotesIcon, tint: 'bg-[var(--primary)]/10 text-[var(--primary)]' },
    { label: t('dashboard.books_owned'), value: props.stats?.books_owned ?? 0, icon: BookOpenIcon, tint: 'bg-[var(--secondary)]/10 text-[var(--secondary)]' },
    { label: t('dashboard.exams_taken'), value: props.stats?.exams_taken ?? 0, icon: ClipboardDocumentCheckIcon, tint: 'bg-[var(--tertiary)]/10 text-[var(--tertiary)]' },
    { label: t('dashboard.courses_enrolled'), value: props.stats?.courses_enrolled ?? 0, icon: AcademicCapIcon, tint: 'bg-[var(--accent)]/10 text-[var(--accent)]' },
]);

const quickActions = [
    { label: 'ai_chat.title', href: '/ai/chat', icon: ChatBubbleOvalLeftEllipsisIcon },
    { label: 'exams.new_exam', href: '/exams/create', icon: ClipboardDocumentCheckIcon },
    { label: 'flashcards.new_set', href: '/flashcards/create', icon: RectangleStackIcon },
    { label: 'schedules.new_schedule', href: '/schedules/create', icon: CalendarDaysIcon },
];

const today = computed(() => new Date().toLocaleDateString(locale.value === 'bn' ? 'bn-BD' : 'en-GB', {
    weekday: 'long', day: 'numeric', month: 'long',
}));
</script>

<template>
    <Head :title="t('nav.dashboard')" />
    <StudentLayout>
        <!-- Hero -->
        <section class="sheen anim-rise relative mb-6 overflow-hidden rounded-3xl border border-[var(--border)] bg-gradient-to-br from-[var(--secondary)]/12 via-[var(--surface)] to-[var(--primary)]/10 p-6 sm:p-8">
            <div class="hero-glow pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full"></div>
            <div class="relative flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div class="min-w-0">
                    <span class="eyebrow">{{ locale === 'en' ? 'Student overview' : 'শিক্ষার্থী ওভারভিউ' }}</span>
                    <h1 class="mt-2 font-heading text-2xl font-extrabold leading-tight sm:text-3xl">
                        {{ t('dashboard.welcome') }}, {{ student?.name?.split(' ')[0] }} <span class="inline-block">👋</span>
                    </h1>
                    <p class="mt-1.5 text-sm text-[var(--text-muted)] sm:text-base">{{ t('dashboard.subtitle') }}</p>
                    <p class="mt-3 inline-flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--surface)]/70 px-3 py-1 text-xs font-medium text-[var(--text-muted)]">
                        <CalendarDaysIcon class="h-3.5 w-3.5" /> {{ today }}
                    </p>
                </div>

                <div class="shrink-0">
                    <div v-if="activeSubscription" class="rounded-2xl border border-[var(--secondary)]/30 bg-[var(--surface)]/80 px-4 py-3 text-sm backdrop-blur">
                        <div class="flex items-center gap-2 font-semibold">
                            <span class="grid h-2 w-2 place-items-center rounded-full bg-[var(--secondary)]"></span>
                            {{ activeSubscription.plan.name }}
                        </div>
                        <div class="mt-0.5 text-xs text-[var(--text-muted)]">
                            {{ locale === 'en' ? 'Active until' : 'সক্রিয় পর্যন্ত' }} {{ new Date(activeSubscription.expires_at).toLocaleDateString() }}
                        </div>
                    </div>
                    <Link v-else href="/plans" class="btn btn-primary">
                        {{ locale === 'en' ? 'Upgrade to Premium' : 'প্রিমিয়ামে আপগ্রেড করুন' }}
                        <ArrowRightIcon class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Free access banner -->
        <div v-if="access?.has_access" class="anim-rise mb-6 flex flex-col gap-4 rounded-2xl border border-[var(--primary)]/30 bg-gradient-to-r from-[var(--primary)]/10 to-[var(--secondary)]/10 p-5 md:flex-row md:items-center">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-[var(--primary)]/15 text-lg">🎉</span>
            <div class="flex-1">
                <div class="font-heading font-bold">{{ locale === 'en' ? 'Full Access Unlocked' : 'সম্পূর্ণ অ্যাক্সেস আনলক হয়েছে' }}</div>
                <div class="text-sm text-[var(--text-muted)]">
                    <span v-if="access.campaign">{{ access.campaign.title }} — {{ locale === 'en' ? 'free campaign until' : 'ফ্রি ক্যাম্পেইন' }} {{ new Date(access.campaign.ends_at).toLocaleDateString() }}</span>
                    <span v-else-if="access.coupon">Coupon "{{ access.coupon.name }}" — {{ locale === 'en' ? 'until' : 'পর্যন্ত' }} {{ access.coupon.ends_at ? new Date(access.coupon.ends_at).toLocaleDateString() : locale === 'en' ? 'indefinitely' : 'আনলিমিটেড' }}</span>
                </div>
            </div>
            <Link href="/access" class="btn btn-outline btn-sm shrink-0">{{ locale === 'en' ? 'View access' : 'অ্যাক্সেস দেখুন' }} →</Link>
        </div>

        <!-- Stat cards -->
        <div class="stagger mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div v-for="card in statCards" :key="card.label" class="card card-hover rounded-2xl p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm text-[var(--text-muted)]">{{ card.label }}</div>
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl" :class="card.tint">
                        <component :is="card.icon" class="h-[1.15rem] w-[1.15rem]" />
                    </span>
                </div>
                <div class="mt-3 font-heading text-2xl font-extrabold">{{ card.value }}</div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Continue learning -->
            <div class="space-y-4 lg:col-span-2">
                <h2 class="font-heading text-lg font-bold">{{ locale === 'en' ? 'Continue Learning' : 'শেখা চালিয়ে যান' }}</h2>

                <div class="space-y-3">
                    <Link v-if="continueReading" :href="`/library/${continueReading.id}/read`" class="card card-hover group flex items-center gap-4 rounded-2xl p-4">
                        <div class="flex h-[4.5rem] w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-[var(--surface2)] text-xl">
                            <img v-if="continueReading.cover_image_url" :src="continueReading.cover_image_url" class="h-full w-full object-cover" />
                            <span v-else>📘</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs text-[var(--text-muted)]">{{ t('bookshelf.continue_reading') }}</div>
                            <div class="truncate font-medium">{{ continueReading.title }}</div>
                        </div>
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[var(--surface2)] text-[var(--primary)] transition-transform group-hover:translate-x-1">
                            <ArrowRightIcon class="h-4 w-4" />
                        </span>
                    </Link>

                    <Link v-if="continueCourse" :href="`/courses/${continueCourse.course.slug}`" class="card card-hover group flex items-center gap-4 rounded-2xl p-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-[var(--surface2)] text-xl">
                            <img v-if="continueCourse.course.cover_image_url" :src="continueCourse.course.cover_image_url" class="h-full w-full object-cover" />
                            <span v-else>🎓</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs text-[var(--text-muted)]">{{ locale === 'en' ? 'Continue Course' : 'কোর্স চালিয়ে যান' }}</div>
                            <div class="mb-1.5 truncate font-medium">{{ continueCourse.course.title }}</div>
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 max-w-[200px] flex-1 overflow-hidden rounded-full bg-[var(--surface2)]">
                                    <div class="h-full rounded-full bg-gradient-to-r from-[var(--secondary)] to-[var(--primary)]" :style="{ width: `${continueCourse.progress_percentage}%` }"></div>
                                </div>
                                <span class="text-xs font-semibold text-[var(--text-muted)]">{{ continueCourse.progress_percentage }}%</span>
                            </div>
                        </div>
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[var(--surface2)] text-[var(--primary)] transition-transform group-hover:translate-x-1">
                            <ArrowRightIcon class="h-4 w-4" />
                        </span>
                    </Link>

                    <div v-if="!continueReading && !continueCourse" class="card rounded-2xl border-dashed p-8 text-center text-[var(--text-muted)]">
                        <p class="mb-3">{{ locale === 'en' ? "You haven't started reading or a course yet." : 'আপনি এখনো কোনো বই বা কোর্স শুরু করেননি।' }}</p>
                        <Link href="/library" class="font-medium text-[var(--primary)]">{{ t('nav.library') }} →</Link>
                    </div>
                </div>

                <!-- Recent exam results -->
                <h2 class="pt-4 font-heading text-lg font-bold">{{ locale === 'en' ? 'Recent Exams' : 'সাম্প্রতিক পরীক্ষা' }}</h2>
                <div v-if="recentExams.length" class="space-y-2">
                    <Link v-for="exam in recentExams" :key="exam.id" :href="`/exams/${exam.id}/result`" class="card card-hover flex items-center justify-between gap-3 rounded-2xl p-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[var(--secondary)]/10 text-[var(--secondary)]">
                                <ClipboardDocumentCheckIcon class="h-4 w-4" />
                            </span>
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-[var(--text)]">{{ exam.title || exam.display_title || (exam.config?.type?.toUpperCase() + ' Exam') }}</div>
                                <div class="text-xs text-[var(--text-muted)]">{{ exam.total }} {{ locale === 'en' ? 'questions' : 'প্রশ্ন' }} · {{ new Date(exam.completed_at).toLocaleDateString() }}</div>
                            </div>
                        </div>
                        <span class="font-heading text-lg font-bold" :class="exam.percentage >= 70 ? 'text-[var(--secondary)]' : exam.percentage >= 40 ? 'text-[var(--primary)]' : 'text-[var(--accent)]'">{{ exam.percentage }}%</span>
                    </Link>
                </div>
                <div v-else class="card rounded-2xl border-dashed p-6 text-center text-sm text-[var(--text-muted)]">
                    <Link href="/exams/create" class="font-medium text-[var(--primary)]">{{ locale === 'en' ? 'Take your first exam →' : 'প্রথম পরীক্ষা দিন →' }}</Link>
                </div>
            </div>

            <!-- Sidebar: leaderboard snippet + quick actions -->
            <div class="space-y-4">
                <div class="card anim-rise rounded-2xl p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="flex items-center gap-2 font-heading font-bold">
                            <TrophyIcon class="h-4.5 w-4.5 text-[var(--primary)]" /> {{ t('nav.leaderboard') }}
                        </h2>
                        <Link href="/leaderboard" class="text-xs font-medium text-[var(--primary)]">{{ locale === 'en' ? 'View all' : 'সব দেখুন' }}</Link>
                    </div>
                    <div v-if="leaderboardTop.length" class="mb-3 space-y-1">
                        <div v-for="entry in leaderboardTop" :key="entry.student_id" class="flex items-center justify-between rounded-lg px-2 py-1.5 text-sm" :class="entry.rank <= 3 ? 'bg-[var(--surface2)]/60' : ''">
                            <span class="flex min-w-0 items-center gap-2">
                                <span class="w-6 shrink-0 text-center">{{ badge(entry.rank) || `#${entry.rank}` }}</span>
                                <span class="truncate">{{ entry.name }}</span>
                            </span>
                            <span class="font-medium">{{ entry.percentage }}%</span>
                        </div>
                    </div>
                    <p v-else class="mb-3 text-xs text-[var(--text-muted)]">{{ locale === 'en' ? 'No entries yet this week.' : 'এই সপ্তাহে এখনো কোনো এন্ট্রি নেই।' }}</p>
                    <div v-if="myRank" class="flex items-center justify-between border-t border-[var(--border)] pt-3 text-sm">
                        <span class="flex items-center gap-1.5 text-[var(--text-muted)]"><FireIcon class="h-4 w-4 text-[var(--primary)]" /> {{ t('leaderboard_page.your_rank') }}</span>
                        <span class="font-semibold">#{{ myRank.rank }}</span>
                    </div>
                </div>

                <div class="card anim-rise rounded-2xl p-5">
                    <h2 class="mb-3 font-heading font-bold">{{ locale === 'en' ? 'Quick Actions' : 'দ্রুত পদক্ষেপ' }}</h2>
                    <div class="grid grid-cols-2 gap-2">
                        <Link v-for="action in quickActions" :key="action.href" :href="action.href" class="flex flex-col items-start gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 p-3 text-xs font-medium transition-all hover:-translate-y-0.5 hover:border-[var(--primary)]/40 hover:shadow-sm">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-[var(--surface)] text-[var(--secondary)]">
                                <component :is="action.icon" class="h-4 w-4" />
                            </span>
                            <span class="leading-tight">{{ t(action.label) }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
