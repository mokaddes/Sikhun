<script setup>
import { computed, ref } from 'vue';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
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
    TrophyIcon,
    ExclamationTriangleIcon,
    ClockIcon,
    BuildingLibraryIcon,
    SparklesIcon,
    XMarkIcon,
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
    onboarding: Object,
    pendingOrder: Object,
});
const { t, locale, money, n } = useI18n();
const student = usePage().props.auth?.student;

const isAdmission = computed(() => {
    return student?.education_type === 'admission' || student?.type === 'admission';
});

const showVerifyModal = ref(false);
const verifyForm = useForm({
    transaction_id: props.pendingOrder?.gateway_transaction_id || '',
});

function submitVerify() {
    if (!props.pendingOrder) return;
    verifyForm.post(`/wallet/orders/${props.pendingOrder.id}/verify`, {
        onSuccess: () => {
            showVerifyModal.value = false;
        },
    });
}

function badge(rank) {
    if (rank === 1) return '🥇';
    if (rank === 2) return '🥈';
    if (rank === 3) return '🥉';
    return '';
}

const statCards = computed(() => [
    { label: t('dashboard.wallet_balance'), value: `৳${money(props.stats?.wallet_balance ?? 0, 2)}`, icon: BanknotesIcon, tint: 'bg-[var(--primary)]/10 text-[var(--primary)]' },
    { label: t('dashboard.books_owned'), value: n(props.stats?.books_owned ?? 0), icon: BookOpenIcon, tint: 'bg-[var(--secondary)]/10 text-[var(--secondary)]' },
    { label: t('dashboard.exams_taken'), value: n(props.stats?.exams_taken ?? 0), icon: ClipboardDocumentCheckIcon, tint: 'bg-[var(--tertiary)]/10 text-[var(--tertiary)]' },
    { label: t('dashboard.courses_enrolled'), value: n(props.stats?.courses_enrolled ?? 0), icon: AcademicCapIcon, tint: 'bg-[var(--accent)]/10 text-[var(--accent)]' },
]);

const onboardingSteps = computed(() => [
    t('onboarding.steps.goal'),
    t('onboarding.steps.class'),
    t('onboarding.steps.book'),
    t('onboarding.steps.question'),
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
        <!-- Hero with embedded stats and subscription status -->
        <section class="sheen anim-rise relative mb-8 overflow-hidden rounded-3xl border border-[var(--border)] bg-gradient-to-br from-[var(--secondary)]/12 via-[var(--surface)] to-[var(--primary)]/10 p-6 sm:p-8 shadow-sm">
            <div class="hero-glow pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full"></div>
            <div class="relative grid gap-6 lg:grid-cols-12 lg:items-center">
                <!-- Col 1: Welcome & Details -->
                <div class="min-w-0 lg:col-span-5">
                    <span class="eyebrow">{{ locale === 'en' ? 'Student overview' : 'শিক্ষার্থী ওভারভিউ' }}</span>
                    <h1 class="mt-2 font-heading text-2xl font-extrabold leading-tight sm:text-3xl text-[var(--text)]">
                        {{ t('dashboard.welcome') }}, {{ student?.name?.split(' ')[0] }} <span class="inline-block">👋</span>
                    </h1>
                    <p class="mt-1.5 text-sm text-[var(--text-muted)] sm:text-base leading-relaxed">{{ t('dashboard.subtitle') }}</p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--surface)]/80 px-3 py-1 text-xs font-medium text-[var(--text-muted)] shadow-xs">
                            <CalendarDaysIcon class="h-3.5 w-3.5 text-[var(--secondary)]" /> {{ today }}
                        </span>
                        <span v-if="myRank" class="inline-flex items-center gap-1.5 rounded-full border border-[var(--border)] bg-[var(--surface)]/80 px-3 py-1 text-xs font-medium text-[var(--text-muted)] shadow-xs">
                            <FireIcon class="h-3.5 w-3.5 text-[var(--primary)]" /> #{{ myRank.rank }}
                        </span>
                    </div>
                </div>

                <!-- Col 2: 4 Embedded Metric Stats (2x2 grid) -->
                <div class="grid grid-cols-2 gap-3 lg:col-span-4">
                    <div
                        v-for="card in statCards"
                        :key="card.label"
                        class="group rounded-2xl border border-[var(--border)] bg-[var(--surface)]/75 p-3.5 backdrop-blur-md transition-all hover:border-[var(--primary)]/30 hover:bg-[var(--surface)]/95 hover:shadow-sm"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span class="truncate text-xs font-medium text-[var(--text-muted)]">{{ card.label }}</span>
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg" :class="card.tint">
                                <component :is="card.icon" class="h-3.5 w-3.5" />
                            </span>
                        </div>
                        <div class="mt-2 font-heading text-lg font-extrabold sm:text-xl text-[var(--text)]">{{ card.value }}</div>
                    </div>
                </div>

                <!-- Col 3: Plan Status / Upgrade CTA -->
                <div class="flex flex-col justify-center lg:col-span-3 lg:items-end">
                    <div v-if="activeSubscription" class="w-full rounded-2xl border border-[var(--secondary)]/30 bg-[var(--surface)]/85 p-4 text-sm backdrop-blur shadow-xs">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ locale === 'en' ? 'Current Plan' : 'বর্তমান প্ল্যান' }}</div>
                        <div class="mt-1 flex items-center gap-2 font-heading text-base font-bold text-[var(--text)]">
                            <span class="grid h-2.5 w-2.5 place-items-center rounded-full bg-[var(--secondary)] ring-4 ring-[var(--secondary)]/20"></span>
                            {{ activeSubscription.plan.name }}
                        </div>
                        <div class="mt-1 text-xs text-[var(--text-muted)]">
                            {{ locale === 'en' ? 'Active until' : 'মেয়াদ' }} {{ new Date(activeSubscription.expires_at).toLocaleDateString() }}
                        </div>
                        <Link href="/plans" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-[var(--primary)] hover:underline">
                            {{ locale === 'en' ? 'Manage plan' : 'প্ল্যান পরিবর্তন' }} →
                        </Link>
                    </div>
                    <div v-else class="w-full rounded-2xl border border-dashed border-[var(--primary)]/40 bg-[var(--surface)]/60 p-4 text-center backdrop-blur">
                        <p class="text-xs text-[var(--text-muted)] mb-2.5">{{ locale === 'en' ? 'Unlock unlimited AI, books & exams' : 'আনলিমিটেড AI, বই ও পরীক্ষা আনলক করুন' }}</p>
                        <Link href="/plans" class="btn btn-primary w-full justify-center text-xs py-2">
                            {{ locale === 'en' ? 'Upgrade to Premium' : 'প্রিমিয়ামে আপগ্রেড' }}
                            <ArrowRightIcon class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <section v-if="!onboarding?.completed" class="anim-rise mb-6 rounded-2xl border border-[var(--primary)]/25 bg-[var(--primary)]/5 p-5">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h2 class="font-heading font-bold">{{ t('onboarding.checklist_title') }}</h2>
                    <p class="mt-1 text-sm text-[var(--text-muted)]">{{ t('onboarding.checklist_hint') }}</p>
                </div>
                <Link href="/onboarding" class="btn btn-primary shrink-0">{{ t('onboarding.resume') }} <ArrowRightIcon class="h-4 w-4" /></Link>
            </div>
            <ol class="mt-4 grid gap-2 sm:grid-cols-2">
                <li v-for="(step, index) in onboardingSteps" :key="step" class="flex items-center gap-2 text-sm" :class="(onboarding?.progress?.completed_steps ?? []).includes(index + 1) ? 'text-[var(--secondary)]' : 'text-[var(--text-muted)]'">
                    <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-[var(--surface)] text-xs font-bold">{{ (onboarding?.progress?.completed_steps ?? []).includes(index + 1) ? '✓' : index + 1 }}</span>
                    {{ step }}
                </li>
            </ol>
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

        <!-- Pending Order Alert Banner -->
        <section v-if="pendingOrder" class="anim-rise mb-6 overflow-hidden rounded-2xl border border-amber-500/40 bg-gradient-to-r from-amber-500/15 via-amber-500/10 to-[var(--surface)] p-5 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-start gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-amber-500/20 text-xl text-amber-600 dark:text-amber-400">
                        ⏳
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-bold text-[var(--text)]">
                                {{ locale === 'en' ? 'Payment Pending' : 'পেমেন্ট অপেক্ষমাণ রয়েছে' }}
                            </span>
                            <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[11px] font-bold text-amber-700 dark:text-amber-300">
                                #{{ pendingOrder.order_number }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-[var(--text)] leading-relaxed">
                            {{ locale === 'en'
                                ? `You have a ৳${money(pendingOrder.amount, 0)} payment pending. Verify your transaction ID to complete payment or contact support.`
                                : `আপনার একটি ৳${money(pendingOrder.amount, 0)} পেমেন্ট অপেক্ষমাণ রয়েছে। পেমেন্ট সম্পন্ন করতে ট্রানজ্যাকশন আইডি যাচাই করুন অথবা সহায়তা পেতে যোগাযোগ করুন।`
                            }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <button
                        @click="showVerifyModal = true"
                        class="btn btn-primary btn-sm rounded-xl px-4 py-2 text-xs font-bold shadow-xs"
                    >
                        {{ locale === 'en' ? 'Verify TrxID' : 'ট্রানজ্যাকশন আইডি যাচাই' }}
                    </button>
                    <Link
                        href="/support/tickets"
                        class="btn btn-secondary btn-sm rounded-xl px-3.5 py-2 text-xs font-semibold"
                    >
                        {{ locale === 'en' ? 'Contact Support' : 'সহায়তা নিন' }}
                    </Link>
                    <Link
                        href="/wallet"
                        class="text-xs font-semibold text-[var(--text-muted)] hover:text-[var(--primary)] px-2"
                    >
                        {{ locale === 'en' ? 'Wallet' : 'ওয়ালেট' }} →
                    </Link>
                </div>
            </div>
        </section>

        <!-- Educative Study Cockpit: 4 Main Pillars for ALL Students -->
        <section class="anim-rise mb-6 rounded-3xl border border-cyan-500/30 bg-gradient-to-r from-cyan-500/10 via-blue-500/5 to-[var(--surface)] p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-cyan-500/15 border border-cyan-500/30 px-3 py-1 text-xs font-bold text-cyan-600 dark:text-cyan-300">
                        <span>🚀</span>
                        <span>{{ isAdmission ? (locale === 'en' ? 'Admission 2026 Test Hub' : 'বিশ্ববিদ্যালয় ভর্তি পরীক্ষা প্রস্তুতি ২০২৬') : (locale === 'en' ? 'Smart Study Cockpit' : 'স্মার্ট স্টাডি ও এক্সাম হাব') }}</span>
                    </div>
                    <h2 class="mt-2 font-heading text-xl font-extrabold sm:text-2xl text-[var(--text)]">
                        {{ locale === 'en' ? 'Your Personal Learning Command Center' : 'আপনার পার্সোনাল স্টাডি ও প্রস্তুতি হাব' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-[var(--text-muted)] mt-1">
                        {{ locale === 'en' ? 'Practice question banks, take live timed exams, review mistakes with AI, and track official university notices.' : 'বিগত বছরের প্রশ্নব্যাংক, লাইভ মডেল টেস্ট, ভুল প্রশ্নের খাতা রিভিশন ও ভর্তি সার্কুলারে দ্রুত প্রবেশ করুন।' }}
                    </p>
                </div>
                <div v-if="props.stats?.unresolved_mistakes > 0" class="shrink-0">
                    <Link
                        href="/mistakes"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs font-bold hover:bg-rose-500/20 transition"
                    >
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <span>{{ props.stats?.unresolved_mistakes }}টি ভুল প্রশ্ন সংশোধনের বাকি</span>
                        <span>→</span>
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <!-- Card 1: Question Bank -->
                <Link
                    href="/question-bank"
                    class="group flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)]/95 p-4 transition-all hover:-translate-y-1 hover:border-amber-500/40 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-amber-500/15 text-amber-500 text-xl group-hover:scale-110 transition-transform">
                            🏛️
                        </span>
                        <span class="rounded-full bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 text-[10px] font-bold text-amber-400">২০,০০০+ প্রশ্ন</span>
                    </div>
                    <div class="mt-3">
                        <h3 class="font-heading font-bold text-sm text-[var(--text)] group-hover:text-amber-500 transition-colors">
                            বিগত প্রশ্নব্যাংক (DU/RU/GST/BUET/BCS)
                        </h3>
                        <p class="text-[11px] text-[var(--text-muted)] mt-1 leading-relaxed">বিশ্ববিদ্যালয় ভর্তি, মেডিকেল ও বিসিএস বিগত প্রশ্নের সমাধান ও AI ব্যাখ্যা</p>
                    </div>
                </Link>

                <!-- Card 2: Live Exam -->
                <Link
                    href="/exams/create"
                    class="group flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)]/95 p-4 transition-all hover:-translate-y-1 hover:border-blue-500/40 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-blue-500/15 text-blue-500 text-xl group-hover:scale-110 transition-transform">
                            ⏱️
                        </span>
                        <span class="rounded-full bg-blue-500/10 border border-blue-500/20 px-2 py-0.5 text-[10px] font-bold text-blue-400">টাইমড এক্সাম</span>
                    </div>
                    <div class="mt-3">
                        <h3 class="font-heading font-bold text-sm text-[var(--text)] group-hover:text-blue-500 transition-colors">
                            লাইভ মডেল টেস্ট ও কুইজ
                        </h3>
                        <p class="text-[11px] text-[var(--text-muted)] mt-1 leading-relaxed">নেগেটিভ মার্কিং সহ রিয়েল-টাইম গতি ও পারফর্মেন্স যাচাই করুন</p>
                    </div>
                </Link>

                <!-- Card 3: Mistake Review -->
                <Link
                    href="/mistakes"
                    class="group flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)]/95 p-4 transition-all hover:-translate-y-1 hover:border-rose-500/40 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-rose-500/15 text-rose-500 text-xl group-hover:scale-110 transition-transform">
                            💡
                        </span>
                        <span class="rounded-full bg-rose-500/10 border border-rose-500/20 px-2 py-0.5 text-[10px] font-bold text-rose-400">
                            {{ props.stats?.unresolved_mistakes ? `${props.stats.unresolved_mistakes}টি বাকি` : 'রিভিশন বুক' }}
                        </span>
                    </div>
                    <div class="mt-3">
                        <h3 class="font-heading font-bold text-sm text-[var(--text)] group-hover:text-rose-500 transition-colors">
                            আমার ভুল প্রশ্নসমূহ (Mistake Review)
                        </h3>
                        <p class="text-[11px] text-[var(--text-muted)] mt-1 leading-relaxed">ভুল হওয়া প্রশ্নগুলো আলাদা করে নিয়ে পুনরায় প্র্যাকটিস করুন</p>
                    </div>
                </Link>

                <!-- Card 4: Book Library & Reader -->
                <Link
                    href="/library"
                    class="group flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)]/95 p-4 transition-all hover:-translate-y-1 hover:border-cyan-500/40 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-cyan-500/15 text-cyan-500 text-xl group-hover:scale-110 transition-transform">
                            📖
                        </span>
                        <span class="rounded-full bg-cyan-500/10 border border-cyan-500/20 px-2 py-0.5 text-[10px] font-bold text-cyan-400">AI রিডার</span>
                    </div>
                    <div class="mt-3">
                        <h3 class="font-heading font-bold text-sm text-[var(--text)] group-hover:text-cyan-500 transition-colors">
                            ডিজিটাল বই লাইব্রেরি ও নোটস
                        </h3>
                        <p class="text-[11px] text-[var(--text-muted)] mt-1 leading-relaxed">বইয়ের চ্যাপ্টার পড়ুন ও AI টিউটরের সাথে তাৎক্ষণিক চ্যাট করুন</p>
                    </div>
                </Link>
            </div>
        </section>

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

        <!-- TrxID Verification Modal -->
        <div
            v-if="showVerifyModal && pendingOrder"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
        >
            <div class="card w-full max-w-md rounded-3xl p-6 sm:p-7 shadow-2xl anim-rise">
                <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
                    <div class="flex items-center gap-2">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400">
                            💳
                        </span>
                        <div>
                            <h3 class="font-heading font-extrabold text-lg text-[var(--text)]">
                                {{ locale === 'en' ? 'Verify Payment TrxID' : 'ট্রানজ্যাকশন আইডি যাচাই' }}
                            </h3>
                            <p class="text-xs text-[var(--text-muted)]">
                                {{ locale === 'en' ? `Order #${pendingOrder.order_number} · ৳${money(pendingOrder.amount, 0)}` : `অর্ডার #${pendingOrder.order_number} · ৳${money(pendingOrder.amount, 0)}` }}
                            </p>
                        </div>
                    </div>
                    <button
                        @click="showVerifyModal = false"
                        class="rounded-lg p-1 text-[var(--text-muted)] hover:bg-[var(--surface2)]"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitVerify" class="mt-4 space-y-4">
                    <div>
                        <label class="label text-xs">
                            {{ locale === 'en' ? 'bKash / Nagad / Rocket Transaction ID (TrxID)' : 'বিকাশ / নগদ / রকেট ট্রানজ্যাকশন আইডি (TrxID)' }}
                        </label>
                        <input
                            v-model="verifyForm.transaction_id"
                            type="text"
                            placeholder="e.g. BL92XK49QA"
                            class="input text-sm py-2 font-mono"
                            required
                        />
                        <p v-if="verifyForm.errors.transaction_id" class="mt-1 text-xs text-rose-500">
                            {{ verifyForm.errors.transaction_id }}
                        </p>
                        <p class="mt-1.5 text-[11px] text-[var(--text-muted)]">
                            {{ locale === 'en'
                                ? 'Enter the transaction ID received from your payment SMS/app.'
                                : 'পেমেন্ট সম্পন্ন হওয়ার পর প্রাপ্ত এসএমএস অথবা অ্যাপের ট্রানজ্যাকশন আইডি দিন।'
                            }}
                        </p>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2">
                        <button
                            type="button"
                            @click="showVerifyModal = false"
                            class="btn btn-secondary text-xs px-4 py-2"
                        >
                            {{ locale === 'en' ? 'Cancel' : 'বাতিল' }}
                        </button>
                        <button
                            type="submit"
                            :disabled="verifyForm.processing"
                            class="btn btn-primary text-xs px-5 py-2 font-bold"
                        >
                            {{ verifyForm.processing ? (locale === 'en' ? 'Verifying...' : 'যাচাই হচ্ছে...') : (locale === 'en' ? 'Submit TrxID' : 'যাচাই করুন') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </StudentLayout>
</template>
