<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';
import {
    CalculatorIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    CalendarDaysIcon,
    SparklesIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    recentJobs: Array,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');

// Active tab: 'job_age' | 'gpa' | 'nu_cgpa'
const activeTab = ref('job_age');

// ==================== 1. JOB AGE CALCULATOR ====================
const birthDate = ref('2000-01-01');
const targetDate = ref(new Date().toISOString().split('T')[0]);

const ageResult = computed(() => {
    if (!birthDate.value || !targetDate.value) return null;

    const b = new Date(birthDate.value);
    const t = new Date(targetDate.value);
    if (isNaN(b.getTime()) || isNaN(t.getTime()) || b > t) return null;

    let years = t.getFullYear() - b.getFullYear();
    let months = t.getMonth() - b.getMonth();
    let days = t.getDate() - b.getDate();

    if (days < 0) {
        months -= 1;
        const prevMonth = new Date(t.getFullYear(), t.getMonth(), 0);
        days += prevMonth.getDate();
    }
    if (months < 0) {
        years -= 1;
        months += 12;
    }

    const totalDays = Math.floor((t - b) / (1000 * 60 * 60 * 24));

    // BCS & Govt Eligibility
    const isGeneralEligible = years < 30 || (years === 30 && months === 0 && days === 0);
    const isQuotaEligible = years < 32 || (years === 32 && months === 0 && days === 0);

    return {
        years,
        months,
        days,
        totalDays,
        isGeneralEligible,
        isQuotaEligible,
    };
});

// ==================== 2. GPA CALCULATOR (SSC/HSC with 4th Subject) ====================
const examType = ref('hsc'); // 'ssc' | 'hsc'
const stream = ref('science'); // 'science' | 'commerce' | 'humanities'

const GRADE_POINTS = {
    'A+': 5.0,
    'A': 4.0,
    'A-': 3.5,
    'B': 3.0,
    'C': 2.0,
    'D': 1.0,
    'F': 0.0,
};

const subjects = ref([
    { name: 'বাংলা (Bangla)', grade: 'A+' },
    { name: 'ইংরেজি (English)', grade: 'A+' },
    { name: 'তথ্য ও যোগাযোগ প্রযুক্তি (ICT)', grade: 'A+' },
    { name: 'পদার্থবিজ্ঞান (Physics)', grade: 'A+' },
    { name: 'রসায়ন (Chemistry)', grade: 'A+' },
    { name: 'উচ্চতর গণিত (Higher Math)', grade: 'A+' },
]);

const optionalSubject = ref({ name: 'জীববিজ্ঞান (Biology - ৪র্থ বিষয়)', grade: 'A+' });

const gpaResult = computed(() => {
    let hasFail = false;
    let totalPoints = 0;

    for (const sub of subjects.value) {
        const pt = GRADE_POINTS[sub.grade] ?? 0;
        if (sub.grade === 'F') hasFail = true;
        totalPoints += pt;
    }

    const optPt = GRADE_POINTS[optionalSubject.value.grade] ?? 0;
    // 4th subject formula: points above 2.0 added to total
    const bonus = Math.max(0, optPt - 2.0);
    totalPoints += bonus;

    if (hasFail) {
        return { gpa: '0.00', grade: 'F', passed: false, bonus, totalPoints };
    }

    const calculatedGpa = Math.min(5.0, totalPoints / subjects.value.length);
    let letter = 'F';
    if (calculatedGpa >= 5.0) letter = 'A+';
    else if (calculatedGpa >= 4.0) letter = 'A';
    else if (calculatedGpa >= 3.5) letter = 'A-';
    else if (calculatedGpa >= 3.0) letter = 'B';
    else if (calculatedGpa >= 2.0) letter = 'C';
    else if (calculatedGpa >= 1.0) letter = 'D';

    return {
        gpa: calculatedGpa.toFixed(2),
        grade: letter,
        passed: true,
        bonus: bonus.toFixed(1),
        totalPoints: totalPoints.toFixed(2),
    };
});

// ==================== 3. NU HONOURS CGPA CALCULATOR ====================
const nuYears = ref([
    { year: '১ম বর্ষ (1st Year)', gpa: '3.25', credits: 30 },
    { year: '২য় বর্ষ (2nd Year)', gpa: '3.40', credits: 30 },
    { year: '৩য় বর্ষ (3rd Year)', gpa: '3.50', credits: 30 },
    { year: '৪র্থ বর্ষ (4th Year)', gpa: '3.60', credits: 30 },
]);

const nuResult = computed(() => {
    let totalWeightedPoints = 0;
    let totalCredits = 0;

    for (const y of nuYears.value) {
        const g = parseFloat(y.gpa) || 0;
        const c = parseFloat(y.credits) || 0;
        totalWeightedPoints += (g * c);
        totalCredits += c;
    }

    if (totalCredits === 0) return null;
    const cgpa = totalWeightedPoints / totalCredits;

    let divClass = en.value ? 'Third Class' : '৩য় বিভাগ (Third Class)';
    if (cgpa >= 3.00) divClass = en.value ? 'First Class' : '১ম শ্রেণি (First Class)';
    else if (cgpa >= 2.25) divClass = en.value ? 'Second Class' : '২য় শ্রেণি (Second Class)';

    return {
        cgpa: cgpa.toFixed(2),
        totalCredits,
        divClass,
    };
});
</script>

<template>
    <Head :title="en ? 'Student & Job Calculators Hub - Govt Age, GPA & NU CGPA Calculator' : 'স্টুডেন্ট ও চাকরি ক্যালকুলেটর হাব - সরকারি চাকরির বয়স, GPA ও CGPA ক্যালকুলেটর'" />
    <PublicLayout>
        <!-- Top Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-[#0b0f19] dark:via-[#111827] dark:to-[var(--bg)] border-b border-gray-200 dark:border-white/5 py-12 md:py-16">
            <div class="pointer-events-none absolute -top-32 left-1/2 -translate-x-1/2 h-96 w-96 rounded-full bg-amber-500/15 blur-[120px]"></div>

            <div class="max-w-7xl mx-auto px-4 md:px-6 relative z-10">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-600 dark:text-amber-400 text-xs font-bold uppercase tracking-wider mb-4">
                        <SparklesIcon class="w-4 h-4" />
                        {{ en ? '100% Free Online Student & Job Tools (No Login Required)' : '১০০% ফ্রি অনলাইন স্টুডেন্ট টুলস (লগইন ছাড়াই)' }}
                    </span>
                    <h1 class="font-heading text-3xl md:text-5xl font-black text-gray-900 dark:text-white leading-tight">
                        {{ en ? 'Academic & Job' : 'একাডেমিক ও চাকরির' }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-500">{{ en ? 'Calculators Hub' : 'ক্যালকুলেটর হাব' }}</span>
                    </h1>
                    <p class="mt-3 text-sm md:text-base text-gray-600 dark:text-[#a2aabc] leading-relaxed">
                        {{ en ? 'Instant calculation for BCS & Govt job age limits, SSC/HSC GPA with 4th subject bonus, and National University CGPA.' : 'বিসিএস ও সরকারি চাকরির বয়স হিসাব, ৪র্থ বিষয়সহ এসএসসি/এইচএসসি জিপিএ এবং জাতীয় বিশ্ববিদ্যালয়ের সিজিপিএ নির্ভুলভাবে হিসাব করুন এক ক্লিকে।' }}
                    </p>
                </div>

                <!-- Navigation Tabs -->
                <div class="mt-8 flex flex-wrap gap-3">
                    <button
                        @click="activeTab = 'job_age'"
                        class="px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'job_age' ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white border-transparent shadow-lg shadow-orange-500/20' : 'bg-white dark:bg-[#1a2030] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/20'"
                    >
                        <BriefcaseIcon class="w-4 h-4" />
                        <span>{{ en ? 'Govt Job Age Calculator' : 'সরকারি চাকরির বয়স ক্যালকুলেটর' }}</span>
                    </button>
                    <button
                        @click="activeTab = 'gpa'"
                        class="px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'gpa' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-lg shadow-[var(--primary)]/20' : 'bg-white dark:bg-[#1a2030] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/20'"
                    >
                        <AcademicCapIcon class="w-4 h-4" />
                        <span>{{ en ? 'SSC / HSC GPA Calculator' : 'SSC / HSC GPA ক্যালকুলেটর' }}</span>
                    </button>
                    <button
                        @click="activeTab = 'nu_cgpa'"
                        class="px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'nu_cgpa' ? 'bg-emerald-600 text-white border-emerald-500 shadow-lg shadow-emerald-600/20' : 'bg-white dark:bg-[#1a2030] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/20'"
                    >
                        <CalculatorIcon class="w-4 h-4" />
                        <span>{{ en ? 'National University (NU) CGPA' : 'জাতীয় বিশ্ববিদ্যালয় (NU) CGPA' }}</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Main Calculator Area -->
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-10">
            <!-- TAB 1: JOB AGE CALCULATOR -->
            <div v-if="activeTab === 'job_age'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-7 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 md:p-8 shadow-xl space-y-6 text-gray-900 dark:text-white">
                    <div>
                        <h2 class="font-heading text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <BriefcaseIcon class="w-5 h-5 text-amber-500 dark:text-amber-400" />
                            <span>{{ en ? 'Govt Job & BCS Age Calculator' : 'সরকারি চাকরি ও বিসিএস বয়স ক্যালকুলেটর' }}</span>
                        </h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            {{ en ? 'Enter your date of birth and target date from circular to calculate exact years, months, and days.' : 'আপনার জন্মতারিখ এবং সার্কুলারে নির্ধারিত হিসাবের তারিখ দিন। বছর, মাস ও দিন নির্ভুলভাবে বের হয়ে আসবে।' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">{{ en ? 'Your Date of Birth' : 'আপনার জন্মতারিখ (Date of Birth)' }}</label>
                            <input
                                v-model="birthDate"
                                type="date"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-[#202738] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-sm focus:outline-none focus:border-amber-400"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">{{ en ? 'Circular Target Date' : 'সার্কুলারের তারিখ (Target Date)' }}</label>
                            <input
                                v-model="targetDate"
                                type="date"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-[#202738] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-sm focus:outline-none focus:border-amber-400"
                            />
                            <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 block">{{ en ? 'Default: Today\'s date' : 'ডিফল্ট: আজকের তারিখ' }}</span>
                        </div>
                    </div>

                    <!-- Age Result Display -->
                    <div v-if="ageResult" class="p-6 rounded-2xl bg-gray-50 dark:bg-[#202738]/90 border border-amber-500/30 space-y-5">
                        <div>
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider block mb-1">{{ en ? 'Your Current Age' : 'আপনার বর্তমান বয়স' }}</span>
                            <div class="flex items-baseline gap-3 flex-wrap">
                                <div class="text-center bg-white dark:bg-[#171c28] px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/5 shadow-xs">
                                    <span class="font-heading text-3xl font-black text-gray-900 dark:text-white">{{ ageResult.years }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">{{ en ? 'Years' : 'বছর' }}</span>
                                </div>
                                <div class="text-center bg-white dark:bg-[#171c28] px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/5 shadow-xs">
                                    <span class="font-heading text-3xl font-black text-gray-900 dark:text-white">{{ ageResult.months }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">{{ en ? 'Months' : 'মাস' }}</span>
                                </div>
                                <div class="text-center bg-white dark:bg-[#171c28] px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/5 shadow-xs">
                                    <span class="font-heading text-3xl font-black text-gray-900 dark:text-white">{{ ageResult.days }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">{{ en ? 'Days' : 'দিন' }}</span>
                                </div>
                            </div>
                            <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-2 block">({{ en ? 'Total approx' : 'সর্বমোট প্রায়' }} {{ ageResult.totalDays.toLocaleString(en ? 'en-US' : 'bn-BD') }} {{ en ? 'days' : 'দিন' }})</span>
                        </div>

                        <!-- Eligibility Cards -->
                        <div class="space-y-2.5 pt-4 border-t border-gray-200 dark:border-white/5 text-xs">
                            <div
                                class="p-3.5 rounded-xl flex items-center justify-between gap-3 border"
                                :class="ageResult.isGeneralEligible ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/10 border-rose-500/30 text-rose-700 dark:text-rose-300'"
                            >
                                <div>
                                    <strong class="block font-bold">{{ en ? 'General Candidates (Max 30 Years)' : 'সাধারণ কোটা (সর্বোচ্চ ৩০ বছর)' }}</strong>
                                    <span class="text-[11px] opacity-80">{{ ageResult.isGeneralEligible ? (en ? 'You are eligible to apply for general government jobs.' : 'আপনি সাধারণ সকল সরকারি চাকরির আবেদনের যোগ্য।') : (en ? 'General candidate age limit (30 years) exceeded.' : 'সাধারণ প্রার্থীর বয়সসীমা (৩০ বছর) উত্তীর্ণ হয়েছে।') }}</span>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap" :class="ageResult.isGeneralEligible ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'">
                                    {{ ageResult.isGeneralEligible ? (en ? '✓ Eligible' : '✓ যোগ্য') : (en ? '✕ Over-age' : '✕ উত্তীর্ণ') }}
                                </span>
                            </div>

                            <div
                                class="p-3.5 rounded-xl flex items-center justify-between gap-3 border"
                                :class="ageResult.isQuotaEligible ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/10 border-rose-500/30 text-rose-700 dark:text-rose-300'"
                            >
                                <div>
                                    <strong class="block font-bold">{{ en ? 'Freedom Fighter / Disability Quota (Max 32 Years)' : 'মুক্তিযোদ্ধা / প্রতিবন্ধী কোটা (সর্বোচ্চ ৩২ বছর)' }}</strong>
                                    <span class="text-[11px] opacity-80">{{ ageResult.isQuotaEligible ? (en ? 'You are eligible to apply under quota privilege.' : 'আপনি কোটা সুবিধা অনুযায়ী আবেদনের যোগ্য।') : (en ? 'Quota age limit (32 years) exceeded.' : 'কোটার বয়সসীমা (৩২ বছর) উত্তীর্ণ হয়েছে।') }}</span>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap" :class="ageResult.isQuotaEligible ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'">
                                    {{ ageResult.isQuotaEligible ? (en ? '✓ Eligible' : '✓ যোগ্য') : (en ? '✕ Over-age' : '✕ উত্তীর্ণ') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Circular Link & Rules -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 shadow-xl text-gray-900 dark:text-white">
                        <h3 class="font-heading font-bold text-gray-900 dark:text-white text-base mb-3">{{ en ? 'Govt Job Age Guidelines' : 'সরকারি চাকরির বয়সসীমা বিধিমালা' }}</h3>
                        <ul class="space-y-2 text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">•</span>
                                <span><strong>{{ en ? 'General Candidates:' : 'সাধারণ প্রার্থী:' }}</strong> {{ en ? '18 to 30 years.' : '১৮ হতে ৩০ বছর পর্যন্ত।' }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">•</span>
                                <span><strong>{{ en ? 'Freedom Fighter Children:' : 'বীর মুক্তিযোদ্ধা/শহীদ মুক্তিযোদ্ধার সন্তান:' }}</strong> {{ en ? 'Up to 32 years.' : '৩২ বছর পর্যন্ত।' }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">•</span>
                                <span><strong>{{ en ? 'Physically Challenged:' : 'শারীরিক প্রতিবন্ধী প্রার্থী:' }}</strong> {{ en ? 'Up to 32 years.' : '৩২ বছর পর্যন্ত।' }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">•</span>
                                <span><strong>{{ en ? 'BCS Health Cadre:' : 'বিসিএস স্বাস্থ্য ক্যাডার:' }}</strong> {{ en ? 'Up to 32 years.' : '৩২ বছর পর্যন্ত।' }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">•</span>
                                <span><strong>{{ en ? 'Departmental Candidates:' : 'বিভাগীয় প্রার্থী:' }}</strong> {{ en ? 'Eligible for 35 to 40 years as per post rules.' : 'পদ অনুযায়ী ৩৫ থেকে ৪০ বছর পর্যন্ত ছাড় পাওয়া যায়।' }}</span>
                            </li>
                        </ul>

                        <Link
                            href="/jobs"
                            class="mt-6 flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-bold shadow-md shadow-emerald-500/20"
                        >
                            <span>{{ en ? 'Browse Latest Job Circulars' : 'সর্বশেষ চাকরির সার্কুলারগুলো দেখুন' }}</span>
                            <span>→</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- TAB 2: SSC / HSC GPA CALCULATOR -->
            <div v-else-if="activeTab === 'gpa'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-8 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 md:p-8 shadow-xl space-y-6 text-gray-900 dark:text-white">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <h2 class="font-heading text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <AcademicCapIcon class="w-5 h-5 text-[var(--primary)]" />
                                <span>{{ en ? 'SSC & HSC GPA Calculator (with 4th subject)' : 'SSC ও HSC জিপিএ ক্যালকুলেটর (৪র্থ বিষয়সহ)' }}</span>
                            </h2>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ en ? 'Select letter grades for each subject. Bonus points from 4th subject will be calculated automatically.' : 'প্রতিটি বিষয়ের গ্রেড সিলেক্ট করুন। শিক্ষাবোর্ডের সঠিক নিয়মে ৪র্থ বিষয়ের বোনাস পয়েন্ট হিসাব করা হবে।' }}
                            </p>
                        </div>

                        <!-- Exam Selector -->
                        <div class="inline-flex rounded-xl bg-gray-100 dark:bg-[#202738] p-1 border border-gray-200 dark:border-white/5 text-xs font-bold">
                            <button
                                @click="examType = 'ssc'"
                                class="px-3 py-1.5 rounded-lg transition"
                                :class="examType === 'ssc' ? 'bg-[var(--primary)] text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                            >
                                SSC
                            </button>
                            <button
                                @click="examType = 'hsc'"
                                class="px-3 py-1.5 rounded-lg transition"
                                :class="examType === 'hsc' ? 'bg-[var(--primary)] text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                            >
                                HSC
                            </button>
                        </div>
                    </div>

                    <!-- Core Subjects List -->
                    <div class="space-y-3">
                        <div
                            v-for="(sub, i) in subjects"
                            :key="i"
                            class="flex items-center justify-between gap-4 p-3.5 rounded-2xl bg-gray-50 dark:bg-[#202738]/70 border border-gray-200 dark:border-white/5"
                        >
                            <span class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-white">{{ sub.name }}</span>
                            <div class="flex items-center gap-2 shrink-0">
                                <select
                                    v-model="sub.grade"
                                    class="px-3 py-1.5 rounded-xl bg-white dark:bg-[#171c28] border border-gray-300 dark:border-white/10 text-xs font-bold text-gray-900 dark:text-white focus:outline-none focus:border-[var(--primary)]"
                                >
                                    <option value="A+">A+ (5.0)</option>
                                    <option value="A">A (4.0)</option>
                                    <option value="A-">A- (3.5)</option>
                                    <option value="B">B (3.0)</option>
                                    <option value="C">C (2.0)</option>
                                    <option value="D">D (1.0)</option>
                                    <option value="F">F (0.0)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 4th Subject -->
                        <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between gap-4">
                            <div>
                                <span class="text-xs sm:text-sm font-bold text-amber-700 dark:text-amber-300 block">{{ optionalSubject.name }}</span>
                                <span class="text-[10px] text-amber-600 dark:text-amber-200/80">{{ en ? 'Points above 2.0 count as bonus added to total GPA' : 'পয়েন্ট হতে ২.০ বাদ দিয়ে অতিরিক্ত বোনাস যোগ হবে' }}</span>
                            </div>
                            <select
                                v-model="optionalSubject.grade"
                                class="px-3 py-1.5 rounded-xl bg-white dark:bg-[#171c28] border border-amber-500/30 text-xs font-bold text-amber-800 dark:text-amber-300 focus:outline-none"
                            >
                                <option value="A+">A+ (5.0 - {{ en ? '3.0 bonus' : 'বোনাস ৩.০' }})</option>
                                <option value="A">A (4.0 - {{ en ? '2.0 bonus' : 'বোনাস ২.০' }})</option>
                                <option value="A-">A- (3.5 - {{ en ? '1.5 bonus' : 'বোনাস ১.৫' }})</option>
                                <option value="B">B (3.0 - {{ en ? '1.0 bonus' : 'বোনাস ১.০' }})</option>
                                <option value="C">C (2.0 - {{ en ? 'no bonus' : 'বোনাস ০.০' }})</option>
                                <option value="F">F (0.0)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Result Card -->
                <div class="lg:col-span-4 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 shadow-xl space-y-5 sticky top-24 text-gray-900 dark:text-white">
                    <h3 class="font-heading font-bold text-gray-900 dark:text-white text-base">{{ en ? 'Result Summary' : 'আপনার ফলাফল সারাংশ' }}</h3>
                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-[#202738] border border-gray-200 dark:border-white/5 text-center">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">{{ en ? 'Total GPA' : 'মোট জিপিএ (GPA)' }}</span>
                        <div class="font-heading text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-amber-500">
                            {{ gpaResult.gpa }}
                        </div>
                        <span class="inline-block mt-2 px-3 py-0.5 rounded-full text-xs font-extrabold" :class="gpaResult.passed ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/15 text-rose-600 dark:text-rose-400'">
                            {{ en ? 'Grade:' : 'গ্রেড:' }} {{ gpaResult.grade }} ({{ gpaResult.passed ? (en ? 'Passed' : 'উত্তীর্ণ') : (en ? 'Failed' : 'অকৃতকার্য') }})
                        </span>
                    </div>

                    <div class="space-y-2 text-xs text-gray-700 dark:text-gray-300 pt-2 border-t border-gray-200 dark:border-white/5">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">{{ en ? '4th Subject Bonus:' : '৪র্থ বিষয়ের বোনাস পয়েন্ট:' }}</span>
                            <strong class="text-amber-600 dark:text-amber-400">+{{ gpaResult.bonus }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">{{ en ? 'Total Points:' : 'সর্বমোট পয়েন্ট:' }}</span>
                            <strong>{{ gpaResult.totalPoints }}</strong>
                        </div>
                    </div>

                    <Link
                        href="/plans"
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-2xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-xs font-bold shadow-lg shadow-[var(--primary)]/20 transition"
                    >
                        <span>{{ en ? 'Admission Preparation Bundles' : 'অ্যাডমিশন প্রিপারেশন বান্ডেল' }}</span>
                        <span>→</span>
                    </Link>
                </div>
            </div>

            <!-- TAB 3: NU CGPA CALCULATOR -->
            <div v-else-if="activeTab === 'nu_cgpa'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-8 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 md:p-8 shadow-xl space-y-6 text-gray-900 dark:text-white">
                    <div>
                        <h2 class="font-heading text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <CalculatorIcon class="w-5 h-5 text-emerald-500 dark:text-emerald-400" />
                            <span>{{ en ? 'National University (NU) Honours CGPA Calculator' : 'জাতীয় বিশ্ববিদ্যালয় (NU) Honours CGPA ক্যালকুলেটর' }}</span>
                        </h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            {{ en ? 'Enter acquired GPA and credits for each year. 4-year cumulative CGPA and degree division will be computed.' : 'প্রতিটি শিক্ষাবর্ষের জিপিএ এবং ক্রেডিট সংখ্যা দিন। চার বছরের সমন্বিত সিজিপিএ এবং বিভাগ নির্ণয় হবে।' }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(y, i) in nuYears"
                            :key="i"
                            class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-gray-50 dark:bg-[#202738]/70 border border-gray-200 dark:border-white/5 items-center"
                        >
                            <span class="font-bold text-sm text-gray-900 dark:text-white">{{ y.year }}</span>
                            <div>
                                <label class="text-[10px] text-gray-500 dark:text-gray-400 block mb-1">{{ en ? 'Acquired GPA' : 'অর্জিত জিপিএ (GPA)' }}</label>
                                <input
                                    v-model="y.gpa"
                                    type="number"
                                    step="0.01"
                                    min="2.00"
                                    max="4.00"
                                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#171c28] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-sm"
                                />
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-500 dark:text-gray-400 block mb-1">{{ en ? 'Credits' : 'ক্রেডিট (Credits)' }}</label>
                                <input
                                    v-model="y.credits"
                                    type="number"
                                    min="20"
                                    max="40"
                                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#171c28] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-sm"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Result Card -->
                <div class="lg:col-span-4 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 shadow-xl space-y-5 sticky top-24 text-gray-900 dark:text-white">
                    <h3 class="font-heading font-bold text-gray-900 dark:text-white text-base">{{ en ? 'Your Honours CGPA' : 'আপনার অনার্স সিজিপিএ' }}</h3>
                    <div v-if="nuResult" class="p-6 rounded-2xl bg-gray-50 dark:bg-[#202738] border border-gray-200 dark:border-white/5 text-center">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">{{ en ? '4-Year Final CGPA' : '৪ বছরের ফাইনাল CGPA' }}</span>
                        <div class="font-heading text-5xl font-black text-emerald-500 dark:text-emerald-400">
                            {{ nuResult.cgpa }}
                        </div>
                        <span class="inline-block mt-2 px-3 py-0.5 rounded-full text-xs font-extrabold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300">
                            {{ nuResult.divClass }}
                        </span>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-2 block">{{ en ? 'Total Credits:' : 'মোট ক্রেডিট:' }} {{ nuResult.totalCredits }}</span>
                    </div>

                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1 pt-2 border-t border-gray-200 dark:border-white/5">
                        <p><strong>{{ en ? '1st Division:' : '১ম শ্রেণি:' }}</strong> 3.00 - 4.00</p>
                        <p><strong>{{ en ? '2nd Division:' : '২য় শ্রেণি:' }}</strong> 2.25 - 2.99</p>
                        <p><strong>{{ en ? '3rd Division:' : '৩য় শ্রেণি:' }}</strong> 2.00 - 2.24</p>
                    </div>

                    <Link
                        href="/jobs"
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-bold shadow-lg transition"
                    >
                        <span>{{ en ? 'View Graduate Job Circulars' : 'স্নাতক পাসের চাকরির খবর দেখুন' }}</span>
                        <span>→</span>
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
