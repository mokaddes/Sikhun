<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    CalendarDaysIcon,
    PlusIcon,
    ArrowRightIcon,
    SparklesIcon,
    ClockIcon,
    AcademicCapIcon,
} from '@heroicons/vue/24/outline';

defineProps({ schedules: Array });
const { t, locale } = useI18n();

function daysRemaining(dateStr) {
    const diff = Math.ceil((new Date(dateStr) - new Date()) / (1000 * 60 * 60 * 24));
    if (diff < 0) return locale.value === 'en' ? 'Passed' : 'সমাপ্ত';
    if (diff === 0) return locale.value === 'en' ? 'Today' : 'আজ';
    return locale.value === 'en' ? `${diff} days left` : `${diff} দিন বাকি`;
}
</script>

<template>
    <Head :title="t('schedules.title')" />
    <StudentLayout>
        <!-- Header -->
        <header class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--secondary)]/10 px-3 py-1 text-xs font-semibold text-[var(--secondary)]">
                    <SparklesIcon class="h-3.5 w-3.5" />
                    {{ locale === 'en' ? 'Smart Study Routine' : 'স্মার্ট পড়ার রুটিন ও পরিকল্পনা' }}
                </span>
                <h1 class="mt-2 font-heading text-2xl font-black tracking-tight sm:text-3xl">
                    {{ t('schedules.title') }}
                </h1>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en'
                        ? 'AI-generated personalized day-by-day exam preparation routines.'
                        : 'আপনার পরীক্ষার তারিখ ও দুর্বল বিষয়ভিত্তিক কৃত্রিম বুদ্ধিমত্তার নিখুঁত স্টাডি রুটিন।' }}
                </p>
            </div>
            <Link
                href="/schedules/create"
                class="btn btn-primary inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-bold shadow-md shadow-[var(--primary)]/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
            >
                <PlusIcon class="h-5 w-5" />
                <span>{{ t('schedules.new_schedule') }}</span>
            </Link>
        </header>

        <!-- Schedules Grid Cards -->
        <div v-if="schedules && schedules.length" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="s in schedules"
                :key="s.id"
                class="group relative flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 transition-all duration-200 hover:-translate-y-1 hover:border-[var(--secondary)]/40 hover:shadow-lg hover:shadow-[var(--shadow-color)]"
            >
                <div>
                    <!-- Top Date & Status -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[var(--secondary)]/10 px-2.5 py-1 text-xs font-bold text-[var(--secondary)] border border-[var(--secondary)]/20">
                            <CalendarDaysIcon class="h-3.5 w-3.5" />
                            {{ daysRemaining(s.exam_date) }}
                        </span>
                        <span
                            class="rounded-lg px-2.5 py-0.5 text-[11px] font-semibold"
                            :class="s.status === 'active' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-[var(--surface2)] text-[var(--text-muted)]'"
                        >
                            {{ s.status === 'active' ? (locale === 'en' ? 'Active' : 'সক্রিয়') : s.status }}
                        </span>
                    </div>

                    <!-- Target Exam Date -->
                    <h3 class="font-heading text-lg font-bold tracking-tight text-[var(--text)]">
                        {{ t('schedules.exam_date') }}: {{ new Date(s.exam_date).toLocaleDateString() }}
                    </h3>

                    <!-- Daily Study Time & Style -->
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-[var(--text-muted)]">
                        <span class="inline-flex items-center gap-1">
                            <ClockIcon class="h-3.5 w-3.5" />
                            {{ s.config?.hours_per_day || 4 }} {{ locale === 'en' ? 'hours/day' : 'ঘণ্টা/দিন' }}
                        </span>
                        <span>•</span>
                        <span class="capitalize">{{ s.config?.style || 'balanced' }}</span>
                    </div>

                    <!-- Subjects List -->
                    <div v-if="s.config?.subjects" class="mt-3 flex flex-wrap gap-1.5">
                        <span
                            v-for="subj in s.config.subjects"
                            :key="subj"
                            class="rounded-md bg-[var(--surface2)] px-2 py-0.5 text-[11px] font-medium text-[var(--text-muted)] border border-[var(--border)]"
                        >
                            {{ subj }}
                        </span>
                    </div>
                </div>

                <!-- Card Bottom -->
                <div class="mt-6 border-t border-[var(--border)]/70 pt-4">
                    <Link
                        :href="`/schedules/${s.id}`"
                        class="btn btn-secondary w-full justify-center gap-2 rounded-xl py-2.5 text-xs font-bold"
                    >
                        <span>{{ locale === 'en' ? 'View Daily Plan' : 'দৈনিক রুটিন দেখুন' }}</span>
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
                <CalendarDaysIcon class="h-8 w-8 opacity-60" />
            </div>
            <h3 class="font-heading text-lg font-bold">
                {{ t('schedules.no_schedules') }}
            </h3>
            <p class="mt-1 max-w-sm text-sm text-[var(--text-muted)]">
                {{ locale === 'en'
                    ? 'Create a custom study plan tailored to your exam date and study hours.'
                    : 'এখনো কোনো স্টাডি রুটিন নেই। পরীক্ষার প্রস্তুতি সহজ করতে এখনই নতুন রুটিন তৈরি করুন।' }}
            </p>
            <Link href="/schedules/create" class="btn btn-primary mt-5 inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-md">
                <PlusIcon class="h-4 w-4" />
                {{ t('schedules.new_schedule') }}
            </Link>
        </div>
    </StudentLayout>
</template>
