<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    SparklesIcon,
    ArrowLeftIcon,
    CalendarDaysIcon,
    ClockIcon,
} from '@heroicons/vue/24/outline';

const { t, locale } = useI18n();

const form = useForm({
    exam_date: '',
    subjects: '',
    weak_subjects: '',
    hours_per_day: 4,
    style: 'balanced',
    include_weekends: true,
});

const hoursPresets = [2, 3, 4, 6, 8];

const styles = [
    { id: 'balanced', labelEn: 'Balanced', labelBn: 'ভারসাম্যপূর্ণ', descEn: 'Mix of tough and light topics', descBn: 'কঠিন ও সহজ বিষয়ের সঠিক সমন্বয়' },
    { id: 'intensive', labelEn: 'Intensive', labelBn: 'নিবিড় প্রস্তুতি', descEn: 'High focus on weak topics first', descBn: 'দুর্বল বিষয়ে বেশি সময় ও গভীর অনুশীলন' },
    { id: 'relaxed', labelEn: 'Relaxed', labelBn: 'স্বাচ্ছন্দ্যময়', descEn: 'Evenly paced with steady breaks', descBn: 'সহজ গতিতে ধীরে ধীরে প্রস্তুতি' },
];

function submit() {
    form.transform((data) => ({
        ...data,
        subjects: data.subjects.split(',').map((s) => s.trim()).filter(Boolean),
        weak_subjects: data.weak_subjects.split(',').map((s) => s.trim()).filter(Boolean),
    })).post('/schedules');
}
</script>

<template>
    <Head :title="t('schedules.new_schedule')" />
    <StudentLayout>
        <div class="mx-auto max-w-2xl pb-12">
            <!-- Back Button -->
            <div class="mb-6 flex items-center justify-between">
                <Link
                    href="/schedules"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--text)] transition-colors"
                >
                    <ArrowLeftIcon class="h-4 w-4" />
                    <span>{{ locale === 'en' ? 'Back to Schedules' : 'সকল রুটিনে ফিরে যান' }}</span>
                </Link>
            </div>

            <!-- Header Card -->
            <div class="mb-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 flex items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-[var(--secondary)]/10 px-3 py-1 text-xs font-semibold text-[var(--secondary)] mb-2">
                        <SparklesIcon class="h-3.5 w-3.5" />
                        {{ locale === 'en' ? 'AI Routine Planner' : 'এআই রুটিন প্ল্যানার' }}
                    </div>
                    <h1 class="font-heading text-2xl font-black tracking-tight sm:text-3xl">
                        {{ t('schedules.new_schedule') }}
                    </h1>
                    <p class="mt-1 text-sm text-[var(--text-muted)]">
                        {{ locale === 'en'
                            ? 'Create an intelligent day-by-day revision schedule ahead of your exam date.'
                            : 'পরীক্ষার লক্ষ্যমাত্রা এবং দৈনিক পড়ার সময় নির্ধারণ করে নিখুঁত প্রস্তুতি রুটিন পান।' }}
                    </p>
                </div>
                <div class="hidden sm:grid h-14 w-14 place-items-center rounded-2xl bg-[var(--surface2)] text-[var(--secondary)] shrink-0">
                    <CalendarDaysIcon class="h-8 w-8" />
                </div>
            </div>

            <!-- Form Card (Centered) -->
            <form @submit.prevent="submit" class="space-y-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                <!-- Target Exam Date -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('schedules.exam_date') }}
                    </label>
                    <input
                        v-model="form.exam_date"
                        type="date"
                        required
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    />
                    <p v-if="form.errors.exam_date" class="mt-1 text-xs text-rose-500">{{ form.errors.exam_date }}</p>
                </div>

                <!-- Subjects -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('schedules.subjects') }}
                        <span class="text-xs font-normal text-[var(--text-muted)] ml-1">
                            ({{ locale === 'en' ? 'Comma-separated' : 'কমা দিয়ে আলাদা করুন' }})
                        </span>
                    </label>
                    <input
                        v-model="form.subjects"
                        type="text"
                        required
                        :placeholder="locale === 'en' ? 'e.g. Physics, Chemistry, Higher Math, Biology' : 'যেমন: পদার্থবিজ্ঞান, রসায়ন, উচ্চতর গণিত, জীববিজ্ঞান'"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    />
                    <p v-if="form.errors.subjects" class="mt-1 text-xs text-rose-500">{{ form.errors.subjects }}</p>
                </div>

                <!-- Weak Subjects -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('schedules.weak_subjects') }}
                        <span class="text-xs font-normal text-[var(--text-muted)] ml-1">
                            ({{ locale === 'en' ? 'Optional, gets higher revision frequency' : 'ঐচ্ছিক, এগুলোতে বেশি গুরুত্ব দেওয়া হবে' }})
                        </span>
                    </label>
                    <input
                        v-model="form.weak_subjects"
                        type="text"
                        :placeholder="locale === 'en' ? 'e.g. Higher Math, Organic Chemistry' : 'যেমন: উচ্চতর গণিত, জৈব রসায়ন'"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    />
                </div>

                <!-- Daily Study Hours -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('schedules.hours_per_day') }}
                    </label>
                    <div class="flex items-center gap-2">
                        <button
                            v-for="h in hoursPresets"
                            :key="h"
                            type="button"
                            class="flex-1 rounded-xl py-2.5 text-xs font-bold border transition-all"
                            :class="form.hours_per_day === h
                                ? 'border-[var(--primary)] bg-[var(--primary)] text-white shadow-sm'
                                : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                            @click="form.hours_per_day = h"
                        >
                            {{ h }} {{ locale === 'en' ? 'hrs' : 'ঘণ্টা' }}
                        </button>
                    </div>
                </div>

                <!-- Style Selection Cards -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('schedules.style') }}
                    </label>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <button
                            v-for="sty in styles"
                            :key="sty.id"
                            type="button"
                            class="flex flex-col items-start rounded-xl border p-3.5 text-left transition-all"
                            :class="form.style === sty.id
                                ? 'border-[var(--secondary)] bg-[var(--secondary)]/8 ring-1 ring-[var(--secondary)]'
                                : 'border-[var(--border)] bg-[var(--surface2)]/40 hover:bg-[var(--surface2)]'"
                            @click="form.style = sty.id"
                        >
                            <span class="font-heading text-xs font-bold" :class="form.style === sty.id ? 'text-[var(--secondary)]' : 'text-[var(--text)]'">
                                {{ locale === 'en' ? sty.labelEn : sty.labelBn }}
                            </span>
                            <span class="mt-1 text-[11px] text-[var(--text-muted)] line-clamp-2">
                                {{ locale === 'en' ? sty.descEn : sty.descBn }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Include Weekends Toggle -->
                <div class="rounded-xl bg-[var(--surface2)]/60 p-4 border border-[var(--border)]">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input
                            v-model="form.include_weekends"
                            type="checkbox"
                            class="h-4 w-4 rounded text-[var(--primary)] focus:ring-[var(--primary)]"
                        />
                        <span class="text-sm font-medium text-[var(--text)]">
                            {{ t('schedules.include_weekends') }}
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn btn-primary w-full justify-center gap-2 rounded-xl py-3.5 text-base font-bold shadow-lg shadow-[var(--primary)]/20 transition-all hover:scale-[1.01] active:scale-[0.99] disabled:opacity-60"
                    >
                        <SparklesIcon class="h-5 w-5" />
                        <span>{{ form.processing ? t('schedules.generating') : t('schedules.generate') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </StudentLayout>
</template>
