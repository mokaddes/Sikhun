<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

defineProps({
    stats: Object,
    continueReading: Object,
    continueCourse: Object,
    recentExams: Array,
    leaderboardTop: Array,
    myRank: Object,
    activeSubscription: Object,
});
const { t, locale } = useI18n();
const student = usePage().props.auth?.student;

function badge(rank) {
    if (rank === 1) return '🥇';
    if (rank === 2) return '🥈';
    if (rank === 3) return '🥉';
    return '';
}
</script>

<template>
    <Head :title="t('nav.dashboard')" />
    <StudentLayout>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-8">
            <div>
                <h1 class="font-heading text-2xl font-extrabold mb-1">{{ t('dashboard.welcome') }}, {{ student?.name }} 👋</h1>
                <p class="text-[var(--text-muted)]">{{ t('dashboard.subtitle') }}</p>
            </div>
            <div v-if="activeSubscription" class="px-4 py-2 rounded-lg bg-[var(--secondary)]/10 border border-[var(--secondary)]/30 text-sm">
                <span class="font-semibold">{{ activeSubscription.plan.name }}</span> ·
                {{ locale === 'en' ? 'until' : 'পর্যন্ত' }} {{ new Date(activeSubscription.expires_at).toLocaleDateString() }}
            </div>
            <Link v-else href="/plans" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ locale === 'en' ? 'Upgrade to Premium →' : 'প্রিমিয়ামে আপগ্রেড করুন →' }}
            </Link>
        </div>

        <!-- Stat cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('dashboard.wallet_balance') }}</div>
                <div class="text-2xl font-heading font-extrabold">৳{{ stats.wallet_balance }}</div>
            </div>
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('dashboard.books_owned') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.books_owned }}</div>
            </div>
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('dashboard.exams_taken') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.exams_taken }}</div>
            </div>
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('dashboard.courses_enrolled') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.courses_enrolled }}</div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Continue learning -->
            <div class="lg:col-span-2 space-y-4">
                <h2 class="font-heading text-lg font-bold">{{ locale === 'en' ? 'Continue Learning' : 'শেখা চালিয়ে যান' }}</h2>

                <Link v-if="continueReading" :href="`/library/${continueReading.id}/read`"
                    class="flex items-center gap-4 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4 hover:shadow-md transition-shadow">
                    <div class="w-14 h-18 rounded-lg bg-[var(--surface2)] flex items-center justify-center text-xl shrink-0 overflow-hidden">
                        <img v-if="continueReading.cover_image_url" :src="continueReading.cover_image_url" class="w-full h-full object-cover" />
                        <span v-else>📘</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs text-[var(--text-muted)] mb-0.5">{{ t('bookshelf.continue_reading') }}</div>
                        <div class="font-medium">{{ continueReading.title }}</div>
                    </div>
                    <span class="text-[var(--primary)]">→</span>
                </Link>

                <Link v-if="continueCourse" :href="`/courses/${continueCourse.course.slug}`"
                    class="flex items-center gap-4 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 rounded-lg bg-[var(--surface2)] flex items-center justify-center text-xl shrink-0 overflow-hidden">
                        <img v-if="continueCourse.course.cover_image_url" :src="continueCourse.course.cover_image_url" class="w-full h-full object-cover" />
                        <span v-else>🎓</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs text-[var(--text-muted)] mb-0.5">{{ locale === 'en' ? 'Continue Course' : 'কোর্স চালিয়ে যান' }}</div>
                        <div class="font-medium mb-1.5">{{ continueCourse.course.title }}</div>
                        <div class="h-1.5 rounded-full bg-[var(--surface2)] overflow-hidden max-w-[160px]">
                            <div class="h-full bg-[var(--primary)]" :style="{ width: `${continueCourse.progress_percentage}%` }"></div>
                        </div>
                    </div>
                    <span class="text-[var(--primary)]">→</span>
                </Link>

                <div v-if="!continueReading && !continueCourse" class="rounded-xl border border-dashed border-[var(--border)] p-8 text-center text-[var(--text-muted)]">
                    <p class="mb-3">{{ locale === 'en' ? "You haven't started reading or a course yet." : 'আপনি এখনো কোনো বই বা কোর্স শুরু করেননি।' }}</p>
                    <Link href="/library" class="text-[var(--primary)] font-medium text-sm">{{ t('nav.library') }} →</Link>
                </div>

                <!-- Recent exam results -->
                <h2 class="font-heading text-lg font-bold pt-4">{{ locale === 'en' ? 'Recent Exams' : 'সাম্প্রতিক পরীক্ষা' }}</h2>
                <div v-if="recentExams.length" class="space-y-2">
                    <Link v-for="exam in recentExams" :key="exam.id" :href="`/exams/${exam.id}/result`"
                        class="flex items-center justify-between rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4 hover:shadow-md transition-shadow">
                        <div>
                            <div class="font-medium text-sm">{{ exam.config?.type?.toUpperCase() }} · {{ exam.total }} {{ locale === 'en' ? 'questions' : 'প্রশ্ন' }}</div>
                            <div class="text-xs text-[var(--text-muted)]">{{ new Date(exam.completed_at).toLocaleDateString() }}</div>
                        </div>
                        <div class="font-heading font-bold">{{ exam.percentage }}%</div>
                    </Link>
                </div>
                <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-6 text-center text-[var(--text-muted)] text-sm">
                    <Link href="/exams/create" class="text-[var(--primary)] font-medium">{{ locale === 'en' ? 'Take your first exam →' : 'প্রথম পরীক্ষা দিন →' }}</Link>
                </div>
            </div>

            <!-- Sidebar: leaderboard snippet + quick actions -->
            <div class="space-y-4">
                <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-heading font-bold">{{ t('nav.leaderboard') }}</h2>
                        <Link href="/leaderboard" class="text-xs text-[var(--primary)]">{{ locale === 'en' ? 'View all' : 'সব দেখুন' }}</Link>
                    </div>
                    <div v-if="leaderboardTop.length" class="space-y-2 mb-3">
                        <div v-for="entry in leaderboardTop" :key="entry.student_id" class="flex items-center justify-between text-sm">
                            <span>{{ badge(entry.rank) }} #{{ entry.rank }} {{ entry.name }}</span>
                            <span class="font-medium">{{ entry.percentage }}%</span>
                        </div>
                    </div>
                    <p v-else class="text-xs text-[var(--text-muted)] mb-3">{{ locale === 'en' ? 'No entries yet this week.' : 'এই সপ্তাহে এখনো কোনো এন্ট্রি নেই।' }}</p>
                    <div v-if="myRank" class="pt-3 border-t border-[var(--border)] text-sm flex items-center justify-between">
                        <span class="text-[var(--text-muted)]">{{ t('leaderboard_page.your_rank') }}</span>
                        <span class="font-semibold">#{{ myRank.rank }}</span>
                    </div>
                </div>

                <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5 space-y-2">
                    <h2 class="font-heading font-bold mb-2">{{ locale === 'en' ? 'Quick Actions' : 'দ্রুত পদক্ষেপ' }}</h2>
                    <Link href="/ai/chat" class="block text-sm px-3 py-2 rounded-lg hover:bg-[var(--surface2)]">🤖 {{ t('nav.ai_chat') }}</Link>
                    <Link href="/exams/create" class="block text-sm px-3 py-2 rounded-lg hover:bg-[var(--surface2)]">📝 {{ t('exams.new_exam') }}</Link>
                    <Link href="/flashcards/create" class="block text-sm px-3 py-2 rounded-lg hover:bg-[var(--surface2)]">🗂️ {{ t('flashcards.new_set') }}</Link>
                    <Link href="/schedules/create" class="block text-sm px-3 py-2 rounded-lg hover:bg-[var(--surface2)]">📅 {{ t('schedules.new_schedule') }}</Link>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
