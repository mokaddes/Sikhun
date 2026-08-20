<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { ChatBubbleOvalLeftEllipsisIcon, ClipboardDocumentCheckIcon, RectangleStackIcon, CalendarDaysIcon } from '@heroicons/vue/24/outline';

defineProps({
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

const quickActions = [
    { label: 'ai_chat.title', href: '/ai/chat', icon: ChatBubbleOvalLeftEllipsisIcon },
    { label: 'exams.new_exam', href: '/exams/create', icon: ClipboardDocumentCheckIcon },
    { label: 'flashcards.new_set', href: '/flashcards/create', icon: RectangleStackIcon },
    { label: 'schedules.new_schedule', href: '/schedules/create', icon: CalendarDaysIcon },
];
</script>

<template>
    <Head :title="t('nav.dashboard')" />
    <StudentLayout>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-8">
            <div>
                <span class="eyebrow">{{ locale === 'en' ? 'Student Overview' : 'শিক্ষার্থী ওভারভিউ' }}</span>
                <h1 class="font-heading text-2xl font-extrabold mt-1">{{ t('dashboard.welcome') }}, {{ student?.name }} 👋</h1>
                <p class="text-[var(--text-muted)]">{{ t('dashboard.subtitle') }}</p>
            </div>
            <div v-if="activeSubscription" class="px-4 py-2 rounded-full bg-[var(--secondary)]/10 border border-[var(--secondary)]/30 text-sm">
                <span class="font-semibold">{{ activeSubscription.plan.name }}</span> ·
                {{ locale === 'en' ? 'until' : 'পর্যন্ত' }} {{ new Date(activeSubscription.expires_at).toLocaleDateString() }}
            </div>
            <Link v-else href="/plans" class="btn btn-primary text-sm">{{ locale === 'en' ? 'Upgrade to Premium →' : 'প্রিমিয়ামে আপগ্রেড করুন →' }}</Link>
        </div>

        <!-- Free access banner -->
        <div v-if="access?.has_access" class="mb-8 rounded-2xl border bg-gradient-to-r from-[var(--primary)]/10 to-[var(--secondary)]/10 border-[var(--primary)]/30 p-5 flex flex-col md:flex-row md:items-center gap-4 card-hover">
            <span class="sun-disc w-11 h-11 shrink-0 flex items-center justify-center text-white text-lg">🎉</span>
            <div class="flex-1">
                <div class="font-heading font-bold mb-0.5">{{ locale === 'en' ? 'Full Access Unlocked' : 'সম্পূর্ণ অ্যাক্সেস আনলক হয়েছে' }}</div>
                <div class="text-sm text-[var(--text-muted)]">
                    <span v-if="access.campaign">{{ access.campaign.title }} — {{ locale === 'en' ? 'free campaign until' : 'ফ্রি ক্যাম্পেইন' }} {{ new Date(access.campaign.ends_at).toLocaleDateString() }}</span>
                    <span v-else-if="access.coupon">Coupon "{{ access.coupon.name }}" — {{ locale === 'en' ? 'until' : 'পর্যন্ত' }} {{ access.coupon.ends_at ? new Date(access.coupon.ends_at).toLocaleDateString() : locale === 'en' ? 'indefinitely' : 'আনলিমিটেড' }}</span>
                </div>
            </div>
            <Link href="/access" class="btn btn-outline text-sm shrink-0">{{ locale === 'en' ? 'View access' : 'অ্যাক্সেস দেখুন' }} →</Link>
        </div>

        <!-- Stat cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="card card-hover rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('dashboard.wallet_balance') }}</div>
                <div class="text-2xl font-heading font-extrabold text-[var(--primary)]">৳{{ stats.wallet_balance }}</div>
            </div>
            <div class="card card-hover rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('dashboard.books_owned') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.books_owned }}</div>
            </div>
            <div class="card card-hover rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('dashboard.exams_taken') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.exams_taken }}</div>
            </div>
            <div class="card card-hover rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('dashboard.courses_enrolled') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.courses_enrolled }}</div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Continue learning -->
            <div class="lg:col-span-2 space-y-4">
                <h2 class="font-heading text-lg font-bold">{{ locale === 'en' ? 'Continue Learning' : 'শেখা চালিয়ে যান' }}</h2>

                <Link v-if="continueReading" :href="`/library/${continueReading.id}/read`"
                    class="flex items-center gap-4 card card-hover rounded-2xl p-4">
                    <div class="w-14 h-18 rounded-xl bg-[var(--surface2)] flex items-center justify-center text-xl shrink-0 overflow-hidden">
                        <img v-if="continueReading.cover_image_url" :src="continueReading.cover_image_url" class="w-full h-full object-cover" />
                        <span v-else>📘</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs text-[var(--text-muted)] mb-0.5">{{ t('bookshelf.continue_reading') }}</div>
                        <div class="font-medium">{{ continueReading.title }}</div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[var(--primary)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </Link>

                <Link v-if="continueCourse" :href="`/courses/${continueCourse.course.slug}`"
                    class="flex items-center gap-4 card card-hover rounded-2xl p-4">
                    <div class="w-14 h-14 rounded-xl bg-[var(--surface2)] flex items-center justify-center text-xl shrink-0 overflow-hidden">
                        <img v-if="continueCourse.course.cover_image_url" :src="continueCourse.course.cover_image_url" class="w-full h-full object-cover" />
                        <span v-else>🎓</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs text-[var(--text-muted)] mb-0.5">{{ locale === 'en' ? 'Continue Course' : 'কোর্স চালিয়ে যান' }}</div>
                        <div class="font-medium mb-1.5">{{ continueCourse.course.title }}</div>
                        <div class="h-1.5 rounded-full bg-[var(--surface2)] overflow-hidden max-w-[160px]">
                            <div class="h-full rounded-full bg-gradient-to-r from-[var(--secondary)] to-[var(--primary)]" :style="{ width: `${continueCourse.progress_percentage}%` }"></div>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[var(--primary)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </Link>

                <div v-if="!continueReading && !continueCourse" class="card rounded-2xl border-dashed p-8 text-center text-[var(--text-muted)]">
                    <p class="mb-3">{{ locale === 'en' ? "You haven't started reading or a course yet." : 'আপনি এখনো কোনো বই বা কোর্স শুরু করেননি।' }}</p>
                    <Link href="/library" class="text-[var(--primary)] font-medium text-sm">{{ t('nav.library') }} →</Link>
                </div>

                <!-- Recent exam results -->
                <h2 class="font-heading text-lg font-bold pt-4">{{ locale === 'en' ? 'Recent Exams' : 'সাম্প্রতিক পরীক্ষা' }}</h2>
                <div v-if="recentExams.length" class="space-y-2">
                    <Link v-for="exam in recentExams" :key="exam.id" :href="`/exams/${exam.id}/result`"
                        class="flex items-center justify-between card card-hover rounded-2xl p-4">
                        <div>
                            <div class="font-medium text-sm">{{ exam.config?.type?.toUpperCase() }} · {{ exam.total }} {{ locale === 'en' ? 'questions' : 'প্রশ্ন' }}</div>
                            <div class="text-xs text-[var(--text-muted)]">{{ new Date(exam.completed_at).toLocaleDateString() }}</div>
                        </div>
                        <span class="font-heading font-bold text-lg" :class="exam.percentage >= 70 ? 'text-[var(--secondary)]' : exam.percentage >= 40 ? 'text-[var(--primary)]' : 'text-[var(--accent)]'">{{ exam.percentage }}%</span>
                    </Link>
                </div>
                <div v-else class="card rounded-2xl border-dashed p-6 text-center text-[var(--text-muted)] text-sm">
                    <Link href="/exams/create" class="text-[var(--primary)] font-medium">{{ locale === 'en' ? 'Take your first exam →' : 'প্রথম পরীক্ষা দিন →' }}</Link>
                </div>
            </div>

            <!-- Sidebar: leaderboard snippet + quick actions -->
            <div class="space-y-4">
                <div class="card rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-heading font-bold">{{ t('nav.leaderboard') }}</h2>
                        <Link href="/leaderboard" class="text-xs text-[var(--primary)] font-medium">{{ locale === 'en' ? 'View all' : 'সব দেখুন' }}</Link>
                    </div>
                    <div v-if="leaderboardTop.length" class="space-y-2 mb-3">
                        <div v-for="entry in leaderboardTop" :key="entry.student_id" class="flex items-center justify-between text-sm">
                            <span>{{ badge(entry.rank) }} <span class="font-mono text-xs text-[var(--text-muted)]">#{{ entry.rank }}</span> {{ entry.name }}</span>
                            <span class="font-medium">{{ entry.percentage }}%</span>
                        </div>
                    </div>
                    <p v-else class="text-xs text-[var(--text-muted)] mb-3">{{ locale === 'en' ? 'No entries yet this week.' : 'এই সপ্তাহে এখনো কোনো এন্ট্রি নেই।' }}</p>
                    <div v-if="myRank" class="pt-3 border-t border-[var(--border)] text-sm flex items-center justify-between">
                        <span class="text-[var(--text-muted)]">{{ t('leaderboard_page.your_rank') }}</span>
                        <span class="font-semibold">#{{ myRank.rank }}</span>
                    </div>
                </div>

                <div class="card rounded-2xl p-5">
                    <h2 class="font-heading font-bold mb-3">{{ locale === 'en' ? 'Quick Actions' : 'দ্রুত পদক্ষেপ' }}</h2>
                    <div class="space-y-1.5">
                        <Link v-for="action in quickActions" :key="action.href" :href="action.href"
                            class="flex items-center gap-3 text-sm px-3 py-2.5 rounded-xl hover:bg-[var(--surface2)] transition-colors">
                            <span class="w-8 h-8 rounded-lg bg-[var(--surface2)] text-[var(--secondary)] flex items-center justify-center shrink-0">
                                <component :is="action.icon" class="w-4.5 h-4.5" />
                            </span>
                            {{ t(action.label) }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>