<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';
import {
    CreditCardIcon,
    CheckIcon,
    SparklesIcon,
    BoltIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    AdjustmentsHorizontalIcon,
    ShieldCheckIcon,
    ArrowRightIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    plans: Array,
    activeSubscription: Object,
});

const { t, locale } = useI18n();
const page = usePage();
const student = computed(() => page.props.auth?.student);

const activeTab = ref('general'); // general | admission | job | custom
const months = ref(1);

// Custom Plan Configurator State
const customMinutes = ref(350);
const customImages = ref(40);
const customBooks = ref(5);
const customReuploads = ref(5);
const customQuizzes = ref(20);

// Custom Plan Pricing (Slightly higher unit rate than fixed bundles, giving flexibility)
const customTotalMonthly = computed(() => {
    const minCost = customMinutes.value * 0.22;
    const imgCost = customImages.value * 1.10;
    const bookCost = customBooks.value * 14.0;
    const reuploadCost = customReuploads.value * 5.0;
    const quizCost = customQuizzes.value * 2.5;
    const baseFee = 35.0;
    return Math.max(149, Math.round(minCost + imgCost + bookCost + reuploadCost + quizCost + baseFee));
});

function isCurrentPlan(plan) {
    return props.activeSubscription?.plan_id === plan.id;
}

function getPlanBadge(plan) {
    const price = Number(plan.price_monthly);
    if (plan.slug === 'pro' || price === 199) {
        return { text: locale.value === 'en' ? 'Most Popular' : 'জনপ্রিয়', color: 'bg-[var(--primary)] text-white' };
    }
    if (plan.slug === 'elite' || price === 399) {
        return { text: locale.value === 'en' ? 'Top Accuracy' : 'সর্বোচ্চ নির্ভুলতা', color: 'bg-gradient-to-r from-amber-500 to-orange-500 text-white' };
    }
    if (plan.slug === 'starter' || price === 99) {
        return { text: locale.value === 'en' ? 'Best Value' : 'বেস্ট ভ্যালু', color: 'bg-emerald-600 text-white' };
    }
    if (plan.is_free || price === 0) {
        return { text: locale.value === 'en' ? 'Free Plan' : 'ফ্রি প্ল্যান', color: 'bg-slate-600 text-white' };
    }
    return null;
}

// Clean General Plan Descriptions (Not HSC/SSC/BUET specific)
function getGeneralPlanDescription(plan) {
    const price = Number(plan.price_monthly);
    if (plan.slug === 'elite' || price >= 350) {
        return locale.value === 'en'
            ? 'Top-tier accuracy and reasoning for advanced research, writing, and intensive studies.'
            : 'গবেষণা, অ্যাডভান্সড স্টাডি ও উচ্চতর বিশ্লেষণে সর্বোচ্চ নির্ভুলতার পাওয়ার প্যাকেজ।';
    }
    if (plan.slug === 'pro' || price >= 150) {
        return locale.value === 'en'
            ? 'Our most popular plan for daily reading, concept notes, and smart problem solving.'
            : 'নিয়মিত অধ্যয়ন, বই পড়া ও বিষয়ভিত্তিক দ্রুত সহায়তার জনপ্রিয় প্যাকেজ।';
    }
    if (plan.slug === 'starter' || price > 0) {
        return locale.value === 'en'
            ? 'Affordable everyday companion for active readers and learners.'
            : 'সাশ্রয়ী মূল্যে পাঠ্যবই পড়া ও নিয়মিত পড়ার সেরা পছন্দ।';
    }
    return locale.value === 'en'
        ? 'Free study assistant with essential AI chat and reader tools.'
        : 'নতুন পাঠকদের জন্য আজীবন ফ্রি স্টাডি অ্যাসিস্ট্যান্ট ও বুক রিডার।';
}

function getPlanPricing(plan) {
    const monthly = Number(plan.price_monthly) || 0;
    if (monthly === 0) {
        return { total: 0, original: 0, discountPercent: 0, savings: 0 };
    }
    const original = monthly * months.value;
    const discountPercent = months.value === 3 ? 10 : months.value === 6 ? 20 : months.value === 12 ? 30 : 0;
    const total = Math.round(original * (100 - discountPercent) / 100);
    const savings = original - total;
    return { total, original, discountPercent, savings };
}

function subscribe(plan, method) {
    if (!student.value) {
        if (plan.is_free || Number(plan.price_monthly) === 0) {
            router.visit(`/register?plan_id=${plan.id}`);
        } else {
            router.visit('/login?redirect=/plans');
        }
        return;
    }
    router.post('/plans/purchase', { plan_id: plan.id, months: months.value, payment_method: method });
}

function subscribeCustom(method) {
    if (!student.value) {
        router.visit('/login?redirect=/plans');
        return;
    }
    router.post('/plans/purchase', {
        is_custom: true,
        months: 1,
        payment_method: method,
        custom_config: {
            minutes: customMinutes.value,
            images: customImages.value,
            books: customBooks.value,
            reuploads: customReuploads.value,
            quizzes: customQuizzes.value,
            total_price: customTotalMonthly.value,
        },
    });
}

function subscribeSpecial(pack, method) {
    if (!student.value) {
        router.visit('/login?redirect=/plans');
        return;
    }
    // Map to custom config with exact special bundle limits
    router.post('/plans/purchase', {
        is_custom: true,
        months: pack.months,
        payment_method: method,
        custom_config: {
            bundle_name: pack.title,
            minutes: pack.minutes,
            images: pack.images,
            books: pack.books,
            reuploads: pack.reuploads,
            quizzes: pack.quizzes,
            total_price: pack.price,
        },
    });
}
</script>

<template>
    <Head :title="t('plans_page.title')" />
    <PublicLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <!-- Header -->
            <header class="anim-rise mb-8 text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] text-xs font-bold mb-3 border border-[var(--primary)]/20">
                    <SparklesIcon class="w-4 h-4 text-amber-500" />
                    {{ locale === 'en' ? 'Sikhun AI Subscriptions' : 'শিখুন এআই সাবস্ক্রিপশন প্ল্যান' }}
                </div>
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-[var(--text)]">
                    {{ t('plans_page.title') }}
                </h1>
                <p class="mt-3 text-sm sm:text-base text-[var(--text-muted)] leading-relaxed">
                    {{ locale === 'en' ? 'Select from General Learning Plans, Admission & Job Bundles, or build your own custom plan.' : 'আপনার লক্ষ্য অনুযায়ী সাধারণ স্টাডি প্ল্যান, ভর্তি ও চাকরি স্পেশাল বান্ডেল বেছে নিন অথবা কাস্টম প্ল্যান তৈরি করুন।' }}
                </p>
            </header>

        <!-- Current Active Plan Banner -->
        <div v-if="activeSubscription" class="mb-6 rounded-2xl border border-[var(--secondary)]/30 bg-[var(--secondary)]/10 px-4 py-3 text-sm flex items-center justify-between flex-wrap gap-2">
            <span>
                {{ t('plans_page.current_plan') }}: <strong class="text-[var(--text)]">{{ activeSubscription.plan.name }}</strong> —
                {{ t('plans_page.active_until') }} {{ new Date(activeSubscription.expires_at).toLocaleDateString() }}
            </span>
            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold">
                <CheckIcon class="w-3.5 h-3.5" /> সক্রিয় প্ল্যান
            </span>
        </div>

        <!-- Main Categories Tab Navigation -->
        <div class="mb-8 flex items-center justify-start sm:justify-center gap-2 p-1.5 rounded-2xl bg-[var(--surface2)] border border-[var(--border)] max-w-2xl mx-auto overflow-x-auto">
            <button
                type="button"
                @click="activeTab = 'general'"
                :class="activeTab === 'general' ? 'bg-[var(--surface)] text-[var(--text)] shadow-xs font-bold border border-[var(--border)]' : 'text-[var(--text-muted)] hover:text-[var(--text)] font-medium'"
                class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all whitespace-nowrap flex items-center gap-1.5"
            >
                <SparklesIcon class="w-4 h-4 text-amber-500" />
                {{ locale === 'en' ? 'General Plans' : 'সাধারণ স্টাডি প্ল্যান' }}
            </button>

            <button
                type="button"
                @click="activeTab = 'admission'"
                :class="activeTab === 'admission' ? 'bg-[var(--surface)] text-[var(--text)] shadow-xs font-bold border border-[var(--border)]' : 'text-[var(--text-muted)] hover:text-[var(--text)] font-medium'"
                class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all whitespace-nowrap flex items-center gap-1.5"
            >
                <AcademicCapIcon class="w-4 h-4 text-emerald-500" />
                {{ locale === 'en' ? 'Admission Bundles' : 'ভর্তি পরীক্ষা বান্ডেল' }}
            </button>

            <button
                type="button"
                @click="activeTab = 'job'"
                :class="activeTab === 'job' ? 'bg-[var(--surface)] text-[var(--text)] shadow-xs font-bold border border-[var(--border)]' : 'text-[var(--text-muted)] hover:text-[var(--text)] font-medium'"
                class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all whitespace-nowrap flex items-center gap-1.5"
            >
                <BriefcaseIcon class="w-4 h-4 text-blue-500" />
                {{ locale === 'en' ? 'Job & BCS Bundles' : 'চাকরি ও বিসিএস বান্ডেল' }}
            </button>

            <button
                type="button"
                @click="activeTab = 'custom'"
                :class="activeTab === 'custom' ? 'bg-[var(--surface)] text-[var(--text)] shadow-xs font-bold border border-[var(--border)]' : 'text-[var(--text-muted)] hover:text-[var(--text)] font-medium'"
                class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all whitespace-nowrap flex items-center gap-1.5"
            >
                <AdjustmentsHorizontalIcon class="w-4 h-4 text-purple-500" />
                {{ locale === 'en' ? 'Custom Plan Builder' : 'কাস্টম প্ল্যান তৈরি' }}
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: GENERAL STUDY PLANS (Free, Starter, Pro, Elite) -->
        <!-- ========================================== -->
        <div v-if="activeTab === 'general'">
            <!-- Duration Selector -->
            <div class="mb-6 rounded-2xl bg-[var(--surface)] border border-[var(--border)] p-4 shadow-xs">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div>
                        <label class="text-sm font-bold text-[var(--text)] flex items-center gap-1.5">
                            <SparklesIcon class="w-4 h-4 text-amber-500" />
                            {{ locale === 'en' ? 'Select Subscription Duration:' : 'মেয়াদ নির্বাচন করুন (মাসিক / কোয়ার্টারলি / বার্ষিক):' }}
                        </label>
                        <p class="text-xs text-[var(--text-muted)] mt-0.5">
                            {{ locale === 'en' ? 'Save up to 30% with multi-month packages!' : '৩ বা ৬ মাসের মেয়াদে ২০% পর্যন্ত অতিরিক্ত ছাড় উপভোগ করুন।' }}
                        </p>
                    </div>
                    <div class="inline-flex rounded-xl bg-[var(--surface2)] p-1 border border-[var(--border)] self-start md:self-auto">
                        <button
                            type="button"
                            @click="months = 1"
                            :class="months === 1 ? 'bg-[var(--primary)] text-white shadow-xs font-bold' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                            class="px-3 py-1.5 rounded-lg text-xs transition-all"
                        >
                            {{ locale === 'en' ? '1 Month' : '১ মাস' }}
                        </button>
                        <button
                            type="button"
                            @click="months = 3"
                            :class="months === 3 ? 'bg-[var(--primary)] text-white shadow-xs font-bold' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                            class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1"
                        >
                            {{ locale === 'en' ? '3 Months' : '৩ মাস' }}
                            <span class="text-[10px] px-1 py-0.5 rounded bg-amber-400 text-amber-950 font-extrabold">-10%</span>
                        </button>
                        <button
                            type="button"
                            @click="months = 6"
                            :class="months === 6 ? 'bg-[var(--primary)] text-white shadow-xs font-bold' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                            class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1"
                        >
                            {{ locale === 'en' ? '6 Months' : '৬ মাস' }}
                            <span class="text-[10px] px-1 py-0.5 rounded bg-rose-500 text-white font-extrabold">-20%</span>
                        </button>
                        <button
                            type="button"
                            @click="months = 12"
                            :class="months === 12 ? 'bg-[var(--primary)] text-white shadow-xs font-bold' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                            class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1"
                        >
                            {{ locale === 'en' ? '12 Months' : '১২ মাস' }}
                            <span class="text-[10px] px-1 py-0.5 rounded bg-emerald-500 text-white font-extrabold">-30%</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 4 Plans Grid -->
            <div class="stagger grid gap-5 grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 items-stretch">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="card card-hover relative flex flex-col rounded-3xl p-5 border transition-all duration-200"
                    :class="[
                        (plan.slug === 'pro' || Number(plan.price_monthly) === 199) ? 'ring-2 ring-[var(--primary)] border-[var(--primary)]/50 shadow-lg shadow-[var(--primary)]/10' : 'border-[var(--border)]',
                        isCurrentPlan(plan) ? 'bg-[var(--primary)]/5 dark:bg-[var(--primary)]/10' : ''
                    ]"
                >
                    <!-- Top Badge -->
                    <span
                        v-if="getPlanBadge(plan)"
                        class="badge absolute -top-3 left-6 shadow-sm text-[11px] font-bold px-3 py-0.5 rounded-full"
                        :class="getPlanBadge(plan).color"
                    >
                        <SparklesIcon v-if="plan.slug === 'pro' || Number(plan.price_monthly) === 199" class="h-3.5 w-3.5 mr-1 inline" />
                        <BoltIcon v-else-if="plan.slug === 'elite' || Number(plan.price_monthly) === 399" class="h-3.5 w-3.5 mr-1 inline" />
                        {{ getPlanBadge(plan).text }}
                    </span>

                    <div class="font-heading text-lg font-bold text-[var(--text)] mt-1">{{ plan.name }}</div>
                    <!-- General Study Description -->
                    <p class="mb-3 mt-1 text-xs text-[var(--text-muted)] line-clamp-2 min-h-8">
                        {{ getGeneralPlanDescription(plan) }}
                    </p>

                    <!-- Pricing Display -->
                    <div class="mb-4">
                        <div class="flex items-baseline gap-1.5 font-heading text-3xl font-extrabold text-[var(--text)]">
                            ৳{{ getPlanPricing(plan).total === 0 ? (locale === 'en' ? '0' : '০') : getPlanPricing(plan).total }}
                            <span class="text-xs font-normal text-[var(--text-muted)]">
                                / {{ months > 1 ? (locale === 'en' ? `${months} months` : `${months} মাস`) : t('plans_page.per_month') }}
                            </span>
                            <span
                                v-if="getPlanPricing(plan).savings > 0"
                                class="line-through text-sm text-[var(--text-muted)]/60 font-semibold"
                            >
                                ৳{{ getPlanPricing(plan).original }}
                            </span>
                        </div>
                        <div v-if="getPlanPricing(plan).savings > 0" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                            {{ locale === 'en'
                                ? `✓ Save ৳${getPlanPricing(plan).savings} total (${getPlanPricing(plan).discountPercent}% off)`
                                : `✓ মোট ৳${getPlanPricing(plan).savings} সাশ্রয় (${getPlanPricing(plan).discountPercent}% ছাড়)`
                            }}
                        </div>
                        <div v-else-if="months > 1 && Number(plan.price_monthly) > 0" class="text-[11px] text-[var(--text-muted)] mt-0.5">
                            (৳{{ Number(plan.price_monthly) }} × {{ months }} {{ locale === 'en' ? 'months' : 'মাস' }})
                        </div>
                    </div>

                    <!-- Plan Highlights / Quotas -->
                    <div class="mb-4 grid grid-cols-2 gap-2 text-xs">
                        <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium border border-[var(--border)]/50">
                            <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">{{ locale === 'en' ? '💬 AI Chat' : '💬 AI চ্যাট' }}</span>
                            <span class="font-bold text-[var(--text)]">{{ plan.ai_chat_minutes }} {{ locale === 'en' ? 'mins/mo' : 'মিনিট/মাস' }}</span>
                        </div>
                        <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium border border-[var(--border)]/50">
                            <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">{{ locale === 'en' ? '📸 Vision Solver' : '📸 ভিশন সলভার' }}</span>
                            <span class="font-bold text-[var(--text)]">{{ plan.ai_vision_limit || 0 }} {{ locale === 'en' ? 'images/mo' : 'টি ছবি/মাস' }}</span>
                        </div>
                        <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium border border-[var(--border)]/50">
                            <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">{{ locale === 'en' ? '📚 Book Slots' : '📚 বই স্লট' }}</span>
                            <span class="font-bold text-[var(--text)]">{{ plan.upload_book_limit || 1 }} {{ locale === 'en' ? 'slots' : 'টি বই' }}</span>
                        </div>
                        <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium border border-[var(--border)]/50">
                            <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">{{ locale === 'en' ? '🔄 Re-uploads' : '🔄 রি-আপলোড' }}</span>
                            <span class="font-bold text-[var(--text)]">{{ plan.monthly_reupload_limit || 1 }} {{ locale === 'en' ? '/mo' : 'বার/মাস' }}</span>
                        </div>
                    </div>

                    <!-- Feature List -->
                    <ul class="mb-5 flex-1 space-y-2 text-xs text-[var(--text-muted)] border-t border-[var(--border)] pt-3">
                        <li v-for="f in plan.features" :key="f" class="flex items-start gap-2">
                            <CheckIcon class="mt-0.5 h-3.5 w-3.5 shrink-0 text-emerald-500 font-bold" />
                            <span class="leading-tight">{{ f }}</span>
                        </li>
                    </ul>

                    <!-- Action Buttons (ZiniPay removed per Image 2 red cross) -->
                    <div class="space-y-2 mt-auto">
                        <template v-if="plan.is_free || Number(plan.price_monthly) === 0">
                            <div v-if="isCurrentPlan(plan)" class="w-full py-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold text-center border border-emerald-500/30">
                                {{ locale === 'en' ? '✓ Current Active Plan' : '✓ বর্তমান সক্রিয় প্ল্যান' }}
                            </div>
                            <button
                                v-else
                                @click="subscribe(plan, 'free')"
                                class="btn btn-outline w-full py-2.5 text-xs font-bold hover:bg-[var(--primary)] hover:text-white hover:border-[var(--primary)] transition-all"
                            >
                                {{ locale === 'en' ? 'Start Free Plan' : 'বিনামূল্যে শুরু করুন' }}
                            </button>
                        </template>

                        <template v-else>
                            <button
                                @click="subscribe(plan, 'mhpay')"
                                class="btn btn-primary w-full shadow-md shadow-pink-500/20 bg-gradient-to-r from-rose-600 to-pink-600 border-0 text-xs py-2.5 font-bold"
                            >
                                {{ locale === 'en' ? 'Subscribe with bKash / Nagad' : 'বিকাশ / নগদ / রকেটে সাবস্ক্রাইব' }}
                            </button>
                            <button
                                v-if="student"
                                @click="subscribe(plan, 'wallet')"
                                class="btn btn-outline w-full text-xs py-2 font-bold"
                            >
                                {{ locale === 'en' ? 'Pay with Wallet' : t('book_show.pay_with_wallet') }}
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: ADMISSION PREPARATION BUNDLES -->
        <!-- ========================================== -->
        <div v-else-if="activeTab === 'admission'" class="space-y-6">
            <div class="p-6 rounded-3xl bg-gradient-to-r from-amber-500/10 via-[var(--surface)] to-[var(--surface)] border border-amber-500/30">
                <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-500 font-mono text-xs font-bold uppercase">
                    TARGET: BUET · Medical · DU · GST
                </span>
                <h2 class="font-heading text-2xl font-extrabold text-[var(--text)] mt-2">
                    {{ locale === 'en' ? 'Special University Admission Preparation Bundles' : 'ভর্তি পরীক্ষা স্পেশাল প্রিপারেশন বান্ডেল' }}
                </h2>
                <p class="text-sm text-[var(--text-muted)] mt-1 max-w-2xl">
                    {{ locale === 'en'
                        ? 'Comprehensive 25-year university admission question bank, negative marking model tests, and step-by-step AI problem solving.'
                        : 'বিগত ২৫ বছরের ভর্তি পরীক্ষার পূর্ণাঙ্গ প্রশ্নব্যাংক, নেগেটিভ মার্কিং মডেল টেস্ট এবং গাণিতিক সমীকরণের স্টেপ-বাই-স্টেপ এআই সমাধান।'
                    }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
                <!-- 3 Months Admission Crash Bundle -->
                <div class="card p-6 rounded-3xl border border-amber-500/40 bg-[var(--surface)] shadow-md relative flex flex-col justify-between">
                    <div>
                        <span class="badge bg-amber-500 text-white text-xs font-bold px-3 py-0.5 rounded-full inline-block mb-3">
                            {{ locale === 'en' ? '3 Months Crash Course' : '৩ মাস স্পেশাল ক্র্যাশ কোর্স' }}
                        </span>
                        <h3 class="font-heading text-xl font-bold text-[var(--text)]">
                            {{ locale === 'en' ? 'Admission Crash Pack (3 Months)' : 'অ্যাডমিশন ক্র্যাশ প্যাক (৩ মাস)' }}
                        </h3>
                        <p class="text-xs text-[var(--text-muted)] mt-1">
                            {{ locale === 'en' ? 'Intensive revision and full-length exam model test package for final preparation.' : 'শেষ মুহূর্তের নিবিড় রিভিশন ও পূর্ণাঙ্গ মডেল টেস্ট প্যাকেজ।' }}
                        </p>

                        <div class="my-4 flex items-baseline gap-2">
                            <span class="font-heading text-3xl font-extrabold text-[var(--text)]">৳৪৫০</span>
                            <span class="text-sm text-[var(--text-muted)] line-through">৳৫০০</span>
                            <span class="text-xs font-bold text-amber-500 bg-amber-500/10 px-2 py-0.5 rounded-full">
                                {{ locale === 'en' ? '10% OFF' : '১০% ছাড়' }}
                            </span>
                        </div>

                        <ul class="space-y-2 text-xs text-[var(--text)] border-t border-[var(--border)] pt-3 mb-6">
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '500 mins top-tier high-reasoning AI chat' : '৫০০ মিনিট টপ-টায়ার হাই-রিজনিং AI চ্যাট' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '120 AI Vision math & formula solvers' : '১২০টি AI Vision গাণিতিক ছবি সমাধান' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? 'Past 25 years BUET, Medical & DU question bank' : 'বিগত ২৫ বছরের বুয়েট, মেডিকেল ও ঢাবি প্রশ্নব্যাংক' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '60 Admission model tests with negative marking' : 'নেগেটিভ মার্কিংসহ ৬০টি অ্যাডমিশন মডেল টেস্ট' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '5 Personal book/note upload slots' : '৫টি নিজস্ব বই/নোট আপলোড স্লট' }}</li>
                        </ul>
                    </div>

                    <div class="space-y-2">
                        <button
                            @click="subscribeSpecial({ title: locale === 'en' ? 'Admission Crash Pack (3 Months)' : 'অ্যাডমিশন ক্র্যাশ প্যাক (৩ মাস)', price: 450, months: 3, minutes: 500, images: 120, books: 5, reuploads: 5, quizzes: 60 }, 'mhpay')"
                            class="btn btn-primary w-full shadow-md shadow-pink-500/20 bg-gradient-to-r from-rose-600 to-pink-600 border-0 text-xs py-2.5 font-bold"
                        >
                            {{ locale === 'en' ? 'Start 3 Months with bKash / Nagad' : 'বিকাশ / নগদ দিয়ে ৩ মাস শুরু করুন' }}
                        </button>
                        <button
                            v-if="student"
                            @click="subscribeSpecial({ title: locale === 'en' ? 'Admission Crash Pack (3 Months)' : 'অ্যাডমিশন ক্র্যাশ প্যাক (৩ মাস)', price: 450, months: 3, minutes: 500, images: 120, books: 5, reuploads: 5, quizzes: 60 }, 'wallet')"
                            class="btn btn-outline w-full text-xs py-2 font-bold"
                        >
                            {{ locale === 'en' ? 'Pay with Wallet (৳450)' : 'ওয়ালেট দিয়ে পরিশোধ (৳৪৫০)' }}
                        </button>
                    </div>
                </div>

                <!-- 6 Months Complete Admission Pass -->
                <div class="card p-6 rounded-3xl border-2 border-[var(--primary)] bg-[var(--surface)] shadow-lg shadow-[var(--primary)]/10 relative flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="badge bg-gradient-to-r from-orange-500 to-amber-500 text-white text-xs font-bold px-3 py-0.5 rounded-full inline-block">
                                {{ locale === 'en' ? '6 Months Complete Success Pass' : '৬ মাস কমপ্লিট সাকসেস পাস' }}
                            </span>
                            <span class="text-xs font-bold text-rose-500 bg-rose-500/10 px-2.5 py-0.5 rounded-full">
                                {{ locale === 'en' ? '20% Mega Savings' : '২০% মেগা সাশ্রয়' }}
                            </span>
                        </div>

                        <h3 class="font-heading text-xl font-bold text-[var(--text)]">
                            {{ locale === 'en' ? 'Admission Success Pass (6 Months)' : 'অ্যাডমিশন সাকসেস পাস (৬ মাস)' }}
                        </h3>
                        <p class="text-xs text-[var(--text-muted)] mt-1">
                            {{ locale === 'en' ? 'Complete end-to-end coverage from post-HSC to university admission.' : 'এইচএসসি পরবর্তী বিশ্ববিদ্যালয় ভর্তি পরীক্ষার শুরু থেকে শেষ পর্যন্ত সম্পূর্ণ কভার্ড।' }}
                        </p>

                        <div class="my-4 flex items-baseline gap-2">
                            <span class="font-heading text-3xl font-extrabold text-[var(--text)]">৳৮৫০</span>
                            <span class="text-sm text-[var(--text-muted)] line-through">৳১,০৫০</span>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                {{ locale === 'en' ? 'Save ৳200' : 'সাশ্রয় ৳২০০' }}
                            </span>
                        </div>

                        <ul class="space-y-2 text-xs text-[var(--text)] border-t border-[var(--border)] pt-3 mb-6">
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '1,200 mins top-tier AI study chat' : '১,২০০ মিনিট টপ-টায়ার AI স্টাডি চ্যাট' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '350 AI Vision formula & math solvers' : '৩৫০টি AI Vision গাণিতিক সমীকরণ সমাধান' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? 'Unlimited negative-marking model tests' : 'আনলিমিটেড নেগেটিভ মার্কিং মডেল টেস্ট' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? 'Chapter-wise weakness tracking & AI recommendations' : 'চ্যাপ্টারভিত্তিক দুর্বলতা ট্র্যাকার ও এআই রিকমেন্ডেশন' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '15 Personal book & question bank upload slots' : '১৫টি নিজস্ব বই ও প্রশ্নব্যাংক আপলোড স্লট' }}</li>
                        </ul>
                    </div>

                    <div class="space-y-2">
                        <button
                            @click="subscribeSpecial({ title: locale === 'en' ? 'Admission Success Pass (6 Months)' : 'অ্যাডমিশন সাকসেস পাস (৬ মাস)', price: 850, months: 6, minutes: 1200, images: 350, books: 15, reuploads: 15, quizzes: 150 }, 'mhpay')"
                            class="btn btn-primary w-full shadow-md shadow-pink-500/20 bg-gradient-to-r from-rose-600 to-pink-600 border-0 text-xs py-2.5 font-bold"
                        >
                            {{ locale === 'en' ? 'Start 6 Months with bKash / Nagad' : 'বিকাশ / নগদ দিয়ে ৬ মাস শুরু করুন' }}
                        </button>
                        <button
                            v-if="student"
                            @click="subscribeSpecial({ title: locale === 'en' ? 'Admission Success Pass (6 Months)' : 'অ্যাডমিশন সাকসেস পাস (৬ মাস)', price: 850, months: 6, minutes: 1200, images: 350, books: 15, reuploads: 15, quizzes: 150 }, 'wallet')"
                            class="btn btn-outline w-full text-xs py-2 font-bold"
                        >
                            {{ locale === 'en' ? 'Pay with Wallet (৳850)' : 'ওয়ালেট দিয়ে পরিশোধ (৳৮৫০)' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: JOB & BCS PREPARATION BUNDLES -->
        <!-- ========================================== -->
        <div v-else-if="activeTab === 'job'" class="space-y-6">
            <div class="p-6 rounded-3xl bg-gradient-to-r from-blue-500/10 via-[var(--surface)] to-[var(--surface)] border border-blue-500/30">
                <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-500 font-mono text-xs font-bold uppercase">
                    TARGET: BCS · Bank Officer · Primary · Govt
                </span>
                <h2 class="font-heading text-2xl font-extrabold text-[var(--text)] mt-2">
                    {{ locale === 'en' ? 'Govt Job & BCS Preparation Bundles' : 'সরকারি চাকরি ও বিসিএস প্রিপারেশন বান্ডেল' }}
                </h2>
                <p class="text-sm text-[var(--text-muted)] mt-1 max-w-2xl">
                    {{ locale === 'en'
                        ? 'Specialized packages for BCS preliminary, written answer evaluation, combined bank recruitment, and ministry exams.'
                        : 'বিসিএস প্রিলিমিনারি, রিটেন খাতা মূল্যায়ন, সমন্বিত ব্যাংকার্স সিলেকশন ও মন্ত্রণালয় নিয়োগের বিশেষায়িত প্রস্তুতি প্যাক।'
                    }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
                <!-- 3 Months Job Crash Pack -->
                <div class="card p-6 rounded-3xl border border-blue-500/40 bg-[var(--surface)] shadow-md relative flex flex-col justify-between">
                    <div>
                        <span class="badge bg-blue-600 text-white text-xs font-bold px-3 py-0.5 rounded-full inline-block mb-3">
                            {{ locale === 'en' ? '3 Months Job Crash Pack' : '৩ মাস জব ক্র্যাশ প্যাক' }}
                        </span>
                        <h3 class="font-heading text-xl font-bold text-[var(--text)]">
                            {{ locale === 'en' ? 'Govt Job Crash Pack (3 Months)' : 'সরকারি চাকরি ক্র্যাশ প্যাক (৩ মাস)' }}
                        </h3>
                        <p class="text-xs text-[var(--text-muted)] mt-1">
                            {{ locale === 'en' ? 'Fast-track revision package for BCS and Bank preliminary examinations.' : 'বিসিএস ও ব্যাংক প্রিলির দ্রুততম রিভিশন প্যাকেজ।' }}
                        </p>

                        <div class="my-4 flex items-baseline gap-2">
                            <span class="font-heading text-3xl font-extrabold text-[var(--text)]">৳৫০০</span>
                            <span class="text-sm text-[var(--text-muted)] line-through">৳৫৬০</span>
                            <span class="text-xs font-bold text-blue-500 bg-blue-500/10 px-2 py-0.5 rounded-full">
                                {{ locale === 'en' ? '10% OFF' : '১০% ছাড়' }}
                            </span>
                        </div>

                        <ul class="space-y-2 text-xs text-[var(--text)] border-t border-[var(--border)] pt-3 mb-6">
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '500 mins AI chat & general knowledge assistant' : '৫০০ মিনিট এআই চ্যাট ও সাধারণ জ্ঞান সহকারী' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '100 Math & analytical solvers' : '১০০টি গণিত ও এনালিটিক্যাল সমাধান' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? 'Past 20 years BCS & PSC question banks' : 'বিগত ২০ বছরের বিসিএস ও পিএসসি প্রশ্নব্যাংক' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? 'Recent current affairs & general knowledge quizzes' : 'সাম্প্রতিক কারেন্ট অ্যাফেয়ার্স ও সাধারণ জ্ঞান কুইজ' }}</li>
                        </ul>
                    </div>

                    <div class="space-y-2">
                        <button
                            @click="subscribeSpecial({ title: locale === 'en' ? 'Govt Job Crash Pack (3 Months)' : 'জব ক্র্যাশ প্যাক (৩ মাস)', price: 500, months: 3, minutes: 500, images: 100, books: 5, reuploads: 5, quizzes: 80 }, 'mhpay')"
                            class="btn btn-primary w-full shadow-md shadow-pink-500/20 bg-gradient-to-r from-rose-600 to-pink-600 border-0 text-xs py-2.5 font-bold"
                        >
                            {{ locale === 'en' ? 'Start 3 Months with bKash / Nagad' : 'বিকাশ / নগদ দিয়ে ৩ মাস শুরু করুন' }}
                        </button>
                        <button
                            v-if="student"
                            @click="subscribeSpecial({ title: locale === 'en' ? 'Govt Job Crash Pack (3 Months)' : 'জব ক্র্যাশ প্যাক (৩ মাস)', price: 500, months: 3, minutes: 500, images: 100, books: 5, reuploads: 5, quizzes: 80 }, 'wallet')"
                            class="btn btn-outline w-full text-xs py-2 font-bold"
                        >
                            {{ locale === 'en' ? 'Pay with Wallet (৳500)' : 'ওয়ালেট দিয়ে পরিশোধ (৳৫০০)' }}
                        </button>
                    </div>
                </div>

                <!-- 6 Months Complete BCS & Bank Pass -->
                <div class="card p-6 rounded-3xl border-2 border-emerald-500 bg-[var(--surface)] shadow-lg relative flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="badge bg-emerald-600 text-white text-xs font-bold px-3 py-0.5 rounded-full inline-block">
                                {{ locale === 'en' ? '6 Months Complete BCS & Bank Pass' : '৬ মাস কমপ্লিট বিসিএস ও ব্যাংক পাস' }}
                            </span>
                            <span class="text-xs font-bold text-rose-500 bg-rose-500/10 px-2.5 py-0.5 rounded-full">
                                {{ locale === 'en' ? '20% Mega Savings' : '২০% মেগা সাশ্রয়' }}
                            </span>
                        </div>

                        <h3 class="font-heading text-xl font-bold text-[var(--text)]">
                            {{ locale === 'en' ? 'BCS & Bank Complete Pass (6 Months)' : 'বিসিএস ও ব্যাংক কমপ্লিট পাস (৬ মাস)' }}
                        </h3>
                        <p class="text-xs text-[var(--text-muted)] mt-1">
                            {{ locale === 'en' ? 'Comprehensive preparation from preliminary to written essays, translations, and AI grading.' : 'প্রিলিমিনারি থেকে লিখিত পরীক্ষার রচনা ও ইংরেজি অনুবাদ মূল্যায়নসহ সম্পূর্ণ প্যাকেজ।' }}
                        </p>

                        <div class="my-4 flex items-baseline gap-2">
                            <span class="font-heading text-3xl font-extrabold text-[var(--text)]">৳৯৫০</span>
                            <span class="text-sm text-[var(--text-muted)] line-through">৳১,২০০</span>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                {{ locale === 'en' ? 'Save ৳250' : 'সাশ্রয় ৳২৫০' }}
                            </span>
                        </div>

                        <ul class="space-y-2 text-xs text-[var(--text)] border-t border-[var(--border)] pt-3 mb-6">
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '1,500 mins high-reasoning AI chat' : '১,৫০০ মিনিট হাই-রিজনিং AI চ্যাট' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '450 Math & bank analytical solutions' : '৪৫০টি গণিত ও ব্যাংক এনালিটিক্যাল সমাধান' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? 'AI evaluation & feedback for written test scripts' : 'লিখিত পরীক্ষার সৃজনশীল খাতার AI মূল্যায়ন ও ফিডব্যাক' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? 'Unlimited topic-wise BCS mock tests' : 'আনলিমিটেড বিষয়ভিত্তিক বিসিএস মক টেস্ট' }}</li>
                            <li class="flex items-center gap-2"><CheckIcon class="w-4 h-4 text-emerald-500 shrink-0" /> {{ locale === 'en' ? '20 Job preparation guides & book upload slots' : '২০টি চাকরি সহায়ক গাইড ও বই আপলোড স্লট' }}</li>
                        </ul>
                    </div>

                    <div class="space-y-2">
                        <button
                            @click="subscribeSpecial({ title: locale === 'en' ? 'BCS & Bank Complete Pass (6 Months)' : 'বিসিএস ও ব্যাংক কমপ্লিট পাস (৬ মাস)', price: 950, months: 6, minutes: 1500, images: 450, books: 20, reuploads: 20, quizzes: 200 }, 'mhpay')"
                            class="btn btn-primary w-full shadow-md shadow-pink-500/20 bg-gradient-to-r from-rose-600 to-pink-600 border-0 text-xs py-2.5 font-bold"
                        >
                            {{ locale === 'en' ? 'Start 6 Months with bKash / Nagad' : 'বিকাশ / নগদ দিয়ে ৬ মাস শুরু করুন' }}
                        </button>
                        <button
                            v-if="student"
                            @click="subscribeSpecial({ title: locale === 'en' ? 'BCS & Bank Complete Pass (6 Months)' : 'বিসিএস ও ব্যাংক কমপ্লিট পাস (৬ মাস)', price: 950, months: 6, minutes: 1500, images: 450, books: 20, reuploads: 20, quizzes: 200 }, 'wallet')"
                            class="btn btn-outline w-full text-xs py-2 font-bold"
                        >
                            {{ locale === 'en' ? 'Pay with Wallet (৳950)' : 'ওয়ালেট দিয়ে পরিশোধ (৳৯৫০)' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: CUSTOM PLAN BUILDER -->
        <!-- ========================================== -->
        <div v-else-if="activeTab === 'custom'" class="space-y-6">
            <div class="p-6 rounded-3xl bg-gradient-to-r from-purple-500/10 via-[var(--surface)] to-[var(--surface)] border border-purple-500/30">
                <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-500 font-mono text-xs font-bold uppercase">
                    BUILD YOUR OWN PLAN
                </span>
                <h2 class="font-heading text-2xl font-extrabold text-[var(--text)] mt-2">
                    {{ locale === 'en' ? 'Custom Subscription Plan Builder' : 'কাস্টম সাবস্ক্রিপশন প্ল্যান বিল্ডার' }}
                </h2>
                <p class="text-sm text-[var(--text-muted)] mt-1 max-w-2xl">
                    {{ locale === 'en'
                        ? 'Tailor your plan to your exact study habits and resource needs. Transparent per-unit pricing with full control.'
                        : 'প্যাকেজ বান্ডেলের বাইরে আপনার যতটুকু প্রয়োজন ঠিক ততটুকুই বেছে নিন। ফ্লেক্সিবল ব্যবহারের জন্য প্রতি ইউনিটের মূল্য নির্ধারিত।'
                    }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <!-- Sliders Controls (2 cols) -->
                <div class="lg:col-span-2 card p-6 rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-xs space-y-6">
                    <!-- Slider 1: AI Chat Minutes -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-bold text-[var(--text)] flex items-center gap-1.5">
                                {{ locale === 'en' ? '💬 AI Chat Time (Mins / Month)' : '💬 AI চ্যাট সময় (মিনিট / মাস)' }}
                            </label>
                            <span class="font-mono font-extrabold text-sm text-[var(--primary)]">
                                {{ customMinutes }} {{ locale === 'en' ? 'mins' : 'মিনিট' }}
                            </span>
                        </div>
                        <input
                            v-model.number="customMinutes"
                            type="range"
                            min="100"
                            max="2500"
                            step="50"
                            class="w-full accent-[var(--primary)] cursor-pointer"
                        />
                        <div class="flex justify-between text-[10px] text-[var(--text-muted)]">
                            <span>100 {{ locale === 'en' ? 'mins' : 'মিনিট' }}</span>
                            <span>1,250 {{ locale === 'en' ? 'mins' : 'মিনিট' }}</span>
                            <span>2,500 {{ locale === 'en' ? 'mins' : 'মিনিট' }}</span>
                        </div>
                    </div>

                    <!-- Slider 2: Vision Image Solver -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-bold text-[var(--text)] flex items-center gap-1.5">
                                {{ locale === 'en' ? '📸 Vision Image Solver (Images / Month)' : '📸 Vision ছবি সমাধান (টি ছবি / মাস)' }}
                            </label>
                            <span class="font-mono font-extrabold text-sm text-amber-500">
                                {{ customImages }} {{ locale === 'en' ? 'images' : 'টি ছবি' }}
                            </span>
                        </div>
                        <input
                            v-model.number="customImages"
                            type="range"
                            min="10"
                            max="400"
                            step="10"
                            class="w-full accent-amber-500 cursor-pointer"
                        />
                        <div class="flex justify-between text-[10px] text-[var(--text-muted)]">
                            <span>10 {{ locale === 'en' ? 'images' : 'টি ছবি' }}</span>
                            <span>200 {{ locale === 'en' ? 'images' : 'টি ছবি' }}</span>
                            <span>400 {{ locale === 'en' ? 'images' : 'টি ছবি' }}</span>
                        </div>
                    </div>

                    <!-- Slider 3: Book Upload Slots -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-bold text-[var(--text)] flex items-center gap-1.5">
                                {{ locale === 'en' ? '📚 Personal Book Upload Slots' : '📚 পার্সোনাল বই আপলোড স্লট' }}
                            </label>
                            <span class="font-mono font-extrabold text-sm text-emerald-500">
                                {{ customBooks }} {{ locale === 'en' ? 'books' : 'টি বই' }}
                            </span>
                        </div>
                        <input
                            v-model.number="customBooks"
                            type="range"
                            min="1"
                            max="25"
                            step="1"
                            class="w-full accent-emerald-500 cursor-pointer"
                        />
                        <div class="flex justify-between text-[10px] text-[var(--text-muted)]">
                            <span>1 {{ locale === 'en' ? 'book' : 'টি বই' }}</span>
                            <span>12 {{ locale === 'en' ? 'books' : 'টি বই' }}</span>
                            <span>25 {{ locale === 'en' ? 'books' : 'টি বই' }}</span>
                        </div>
                    </div>

                    <!-- Slider 4: Monthly Re-uploads -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-bold text-[var(--text)] flex items-center gap-1.5">
                                {{ locale === 'en' ? '🔄 Monthly Book Re-upload Quota' : '🔄 মাসিক বই রি-আপলোড কোটা' }}
                            </label>
                            <span class="font-mono font-extrabold text-sm text-blue-500">
                                {{ customReuploads }} {{ locale === 'en' ? 'times / mo' : 'বার / মাস' }}
                            </span>
                        </div>
                        <input
                            v-model.number="customReuploads"
                            type="range"
                            min="1"
                            max="20"
                            step="1"
                            class="w-full accent-blue-500 cursor-pointer"
                        />
                        <div class="flex justify-between text-[10px] text-[var(--text-muted)]">
                            <span>1 {{ locale === 'en' ? 'time' : 'বার' }}</span>
                            <span>10 {{ locale === 'en' ? 'times' : 'বার' }}</span>
                            <span>20 {{ locale === 'en' ? 'times' : 'বার' }}</span>
                        </div>
                    </div>

                    <!-- Slider 5: AI Quizzes & Mock Tests -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-bold text-[var(--text)] flex items-center gap-1.5">
                                {{ locale === 'en' ? '📝 Chapter Quizzes & Mock Tests' : '📝 অধ্যায়ভিত্তিক কুইজ ও মডেল টেস্ট' }}
                            </label>
                            <span class="font-mono font-extrabold text-sm text-purple-500">
                                {{ customQuizzes }} {{ locale === 'en' ? 'tests' : 'টি টেস্ট' }}
                            </span>
                        </div>
                        <input
                            v-model.number="customQuizzes"
                            type="range"
                            min="5"
                            max="100"
                            step="5"
                            class="w-full accent-purple-500 cursor-pointer"
                        />
                        <div class="flex justify-between text-[10px] text-[var(--text-muted)]">
                            <span>5 {{ locale === 'en' ? 'tests' : 'টি' }}</span>
                            <span>50 {{ locale === 'en' ? 'tests' : 'টি' }}</span>
                            <span>100 {{ locale === 'en' ? 'tests' : 'টি' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Live Price Calculation & Summary Card (1 col) -->
                <div class="card p-6 rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-md sticky top-6 space-y-4">
                    <h3 class="font-heading text-base font-bold text-[var(--text)] border-b border-[var(--border)] pb-2 flex items-center justify-between">
                        <span>{{ locale === 'en' ? 'Your Custom Estimate' : 'আপনার কাস্টম হিসাব' }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-500 font-bold uppercase">
                            {{ locale === 'en' ? '1 Month Term' : '১ মাস মেয়াদী' }}
                        </span>
                    </h3>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between text-[var(--text-muted)]">
                            <span>{{ locale === 'en' ? `AI Chat (${customMinutes} mins)` : `AI চ্যাট (${customMinutes} মি.)` }}</span>
                            <span class="font-mono font-bold text-[var(--text)]">৳{{ Math.round(customMinutes * 0.22) }}</span>
                        </div>
                        <div class="flex justify-between text-[var(--text-muted)]">
                            <span>{{ locale === 'en' ? `Vision Solver (${customImages} images)` : `Vision ছবি (${customImages}টি)` }}</span>
                            <span class="font-mono font-bold text-[var(--text)]">৳{{ Math.round(customImages * 1.10) }}</span>
                        </div>
                        <div class="flex justify-between text-[var(--text-muted)]">
                            <span>{{ locale === 'en' ? `Book Slots (${customBooks} books)` : `বই স্লট (${customBooks}টি)` }}</span>
                            <span class="font-mono font-bold text-[var(--text)]">৳{{ Math.round(customBooks * 14) }}</span>
                        </div>
                        <div class="flex justify-between text-[var(--text-muted)]">
                            <span>{{ locale === 'en' ? `Re-uploads (${customReuploads} times)` : `রি-আপলোড (${customReuploads} বার)` }}</span>
                            <span class="font-mono font-bold text-[var(--text)]">৳{{ Math.round(customReuploads * 5) }}</span>
                        </div>
                        <div class="flex justify-between text-[var(--text-muted)]">
                            <span>{{ locale === 'en' ? `Quizzes (${customQuizzes} tests)` : `কুইজ (${customQuizzes}টি)` }}</span>
                            <span class="font-mono font-bold text-[var(--text)]">৳{{ Math.round(customQuizzes * 2.5) }}</span>
                        </div>
                        <div class="flex justify-between text-[var(--text-muted)]">
                            <span>{{ locale === 'en' ? 'Platform Base Fee' : 'প্ল্যাটফর্ম বেস ফি' }}</span>
                            <span class="font-mono font-bold text-[var(--text)]">৳৩৫</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[var(--border)]">
                        <div class="text-xs text-[var(--text-muted)] mb-1">
                            {{ locale === 'en' ? 'Total Payable Amount:' : 'সর্বমোট প্রদেয় মূল্য:' }}
                        </div>
                        <div class="font-heading text-3xl font-extrabold text-[var(--text)] flex items-baseline gap-1">
                            ৳{{ customTotalMonthly }}
                            <span class="text-xs font-normal text-[var(--text-muted)]">/ {{ locale === 'en' ? '1 Month' : '১ মাস' }}</span>
                        </div>
                        <p class="text-[11px] text-[var(--text-muted)] mt-1 leading-relaxed">
                            {{ locale === 'en'
                                ? '* Custom plan unit rates are slightly higher than fixed bundles, giving you complete flexibility to tailor to your study routine.'
                                : '* কাস্টম প্ল্যানের প্রতি ইউনিটের রেট প্যাকেজের চেয়ে সামান্য বেশি কিন্তু এটি সম্পূর্ণ আপনার নিজস্ব প্রয়োজন অনুযায়ী তৈরি।'
                            }}
                        </p>
                    </div>

                    <div class="space-y-2 pt-2">
                        <button
                            type="button"
                            @click="subscribeCustom('mhpay')"
                            class="btn btn-primary w-full shadow-md shadow-pink-500/20 bg-gradient-to-r from-rose-600 to-pink-600 border-0 text-xs py-2.5 font-bold"
                        >
                            {{ locale === 'en' ? 'Activate with bKash / Nagad' : 'বিকাশ / নগদ দিয়ে সক্রিয় করুন' }}
                        </button>
                        <button
                            v-if="student"
                            type="button"
                            @click="subscribeCustom('wallet')"
                            class="btn btn-outline w-full text-xs py-2 font-bold"
                        >
                            {{ locale === 'en' ? `Pay with Wallet (৳${customTotalMonthly})` : `ওয়ালেট দিয়ে পরিশোধ (৳${customTotalMonthly})` }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </PublicLayout>
</template>
