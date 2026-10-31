<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';
import {
    AcademicCapIcon,
    PlayCircleIcon,
    LockClosedIcon,
    CheckBadgeIcon,
    UsersIcon,
    ClockIcon,
    FolderIcon,
    CreditCardIcon,
    WalletIcon,
    UserIcon,
    EnvelopeIcon,
    PhoneIcon,
    ChevronRightIcon,
    ArrowTopRightOnSquareIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    course: Object,
    enrollment: Object,
    isEnrolled: Boolean,
    hasAccess: Boolean,
    walletBalance: { type: Number, default: 0 },
    relatedCourses: { type: Array, default: () => [] },
    seo: Object,
});

const { t, locale, money } = useI18n();
const page = usePage();
const isLoggedIn = computed(() => !!page.props.auth?.student);
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

// Checkout mode tab for unauthenticated visitors: 'guest' | 'auth'
const checkoutTab = ref('guest');

const guestForm = useForm({
    name: '',
    email: '',
    phone: '',
    type: ['ssc', 'hsc', 'university', 'job'].includes(props.course.level) ? props.course.level : 'hsc',
});

const loginRedirectUrl = computed(() => `/login?redirect=${encodeURIComponent(`/courses/${props.course.slug}`)}`);
const registerRedirectUrl = computed(() => `/register?redirect=${encodeURIComponent(`/courses/${props.course.slug}`)}`);

function enrollAuthenticated(method) {
    router.post(`/courses/${props.course.id}/enroll`, { payment_method: method });
}

function submitGuestCheckout() {
    guestForm.post(`/courses/${props.course.id}/guest-enroll`, {
        preserveScroll: true,
    });
}

const totalLessonsCount = computed(() => {
    if (!props.course.sections) return 0;
    return props.course.sections.reduce((acc, s) => acc + (s.lessons?.length || 0), 0);
});

const totalDurationMinutes = computed(() => {
    if (!props.course.sections) return 0;
    return props.course.sections.reduce(
        (acc, s) => acc + (s.lessons || []).reduce((lAcc, l) => lAcc + (Number(l.duration_minutes) || 0), 0),
        0
    );
});

const firstAccessibleLessonUrl = computed(() => {
    if (!props.course.sections?.length) return null;
    for (const section of props.course.sections) {
        if (section.lessons?.length) {
            const lesson = section.lessons[0];
            return `/courses/${props.course.slug}/sections/${section.id}/lessons/${lesson.id}`;
        }
    }
    return null;
});
</script>

<template>
    <Head :title="course.title" />
    <SeoHead v-if="seo" :seo="seo" />

    <PublicLayout>
        <!-- Hero Header -->
        <section class="relative overflow-hidden border-b border-[var(--border)] bg-gradient-to-br from-[#0b0f19] via-[#111827] to-[#1e1b4b] text-white">
            <div class="pointer-events-none absolute -top-24 -right-24 h-80 w-80 rounded-full bg-[var(--primary)]/20 blur-3xl"></div>

            <div class="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
                <!-- Breadcrumb -->
                <div class="mb-5 flex flex-wrap items-center gap-2 text-xs text-slate-300">
                    <Link href="/" class="hover:text-white">{{ locale === 'en' ? 'Home' : 'হোম' }}</Link>
                    <ChevronRightIcon class="h-3.5 w-3.5" />
                    <Link href="/courses" class="hover:text-white">{{ t('nav.courses') }}</Link>
                    <template v-if="course.category">
                        <ChevronRightIcon class="h-3.5 w-3.5" />
                        <Link :href="`/courses?category_id=${course.category.id}`" class="hover:text-white">{{ course.category.name }}</Link>
                    </template>
                    <ChevronRightIcon class="h-3.5 w-3.5" />
                    <span class="truncate text-orange-300">{{ course.title }}</span>
                </div>

                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span v-if="course.category" class="rounded-full bg-[var(--primary)]/20 px-3 py-1 text-xs font-bold text-orange-300">
                            {{ course.category.name }}
                        </span>
                        <span v-if="course.level" class="rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold uppercase">
                            {{ course.level }}
                        </span>
                        <span v-if="isEnrolled" class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-bold text-emerald-300">
                            <CheckBadgeIcon class="h-4 w-4" />
                            {{ locale === 'en' ? 'Enrolled' : 'এনরোলকৃত' }}
                        </span>
                    </div>

                    <h1 class="mt-3 font-heading text-2xl font-extrabold leading-tight sm:text-4xl">
                        {{ course.title }}
                    </h1>

                    <p v-if="course.description" class="mt-4 whitespace-pre-line text-sm leading-relaxed text-slate-300 sm:text-base">
                        {{ course.description }}
                    </p>

                    <!-- Stats & Mentor Row -->
                    <div class="mt-6 flex flex-wrap items-center gap-5 text-xs sm:text-sm text-slate-200">
                        <div v-if="course.mentor" class="flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-500/20 font-bold text-orange-300">
                                {{ course.mentor.name?.charAt(0) || 'M' }}
                            </div>
                            <div>
                                <div class="font-semibold text-white">{{ course.mentor.name }}</div>
                                <div v-if="course.mentor.designation" class="text-xs text-slate-400">{{ course.mentor.designation }}</div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-slate-300">
                            <span class="inline-flex items-center gap-1.5">
                                <UsersIcon class="h-4 w-4 text-orange-400" />
                                {{ course.enrollments_count ?? 0 }} {{ locale === 'en' ? 'Students' : 'জন শিক্ষার্থী' }}
                            </span>
                            <span v-if="totalLessonsCount" class="inline-flex items-center gap-1.5">
                                <PlayCircleIcon class="h-4 w-4 text-emerald-400" />
                                {{ totalLessonsCount }} {{ locale === 'en' ? 'Lessons' : 'টি লেসন' }}
                            </span>
                            <span v-if="totalDurationMinutes" class="inline-flex items-center gap-1.5">
                                <ClockIcon class="h-4 w-4 text-indigo-400" />
                                {{ totalDurationMinutes }} {{ locale === 'en' ? 'mins' : 'মিনিট' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Body + Checkout Sidebar -->
        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
            <!-- Flash Alerts -->
            <div v-if="flashSuccess" class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-600 dark:text-emerald-300">
                {{ flashSuccess }}
            </div>
            <div v-if="flashError" class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm font-medium text-rose-600 dark:text-rose-300">
                {{ flashError }}
            </div>

            <div class="grid gap-8 lg:grid-cols-12">
                <!-- Left Column: Curriculum / Course Content (7 cols) -->
                <div class="space-y-8 lg:col-span-7 xl:col-span-8">
                    <!-- Link-delivered Course Access Box (Visible once enrolled) -->
                    <div
                        v-if="course.delivery_type !== 'video' && isEnrolled"
                        class="rounded-2xl border border-emerald-500/30 bg-emerald-500/5 p-6 sm:p-8"
                    >
                        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-bold text-emerald-500">
                            <CheckBadgeIcon class="h-4 w-4" />
                            <span>{{ locale === 'en' ? 'Course Access Unlocked' : 'কোর্স অ্যাক্সেস চালু হয়েছে' }}</span>
                        </div>

                        <h2 class="mt-3 font-heading text-xl font-bold">
                            {{ course.delivery_type === 'file_download' ? t('courses_page.download_files') : t('courses_page.open_link') }}
                        </h2>

                        <div class="mt-4">
                            <a
                                v-if="course.external_link"
                                :href="course.external_link"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/25 transition hover:bg-[var(--primary-hover)]"
                            >
                                <span>{{ course.delivery_type === 'file_download' ? t('courses_page.download_files') : t('courses_page.open_link') }}</span>
                                <ArrowTopRightOnSquareIcon class="h-4 w-4" />
                            </a>
                        </div>

                        <p v-if="course.link_note" class="mt-4 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4 text-sm whitespace-pre-line text-[var(--text-muted)]">
                            {{ course.link_note }}
                        </p>
                        <p class="mt-3 text-xs text-[var(--text-muted)]">
                            {{ t('courses_page.link_emailed') }}
                        </p>
                    </div>

                    <!-- Video Course Curriculum -->
                    <div v-if="course.delivery_type === 'video'" class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm sm:p-8">
                        <div class="mb-6 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <h2 class="font-heading text-xl font-extrabold">
                                    {{ locale === 'en' ? 'Course Curriculum' : 'কোর্স কারিকুলাম ও লেসনসমূহ' }}
                                </h2>
                                <p class="mt-0.5 text-xs text-[var(--text-muted)]">
                                    {{ (course.sections || []).length }} {{ locale === 'en' ? 'Sections' : 'টি সেকশন' }} · {{ totalLessonsCount }} {{ locale === 'en' ? 'Lessons' : 'টি লেসন' }}
                                </p>
                            </div>
                        </div>

                        <div v-if="course.sections?.length" class="space-y-4">
                            <div
                                v-for="(section, idx) in course.sections"
                                :key="section.id"
                                class="overflow-hidden rounded-xl border border-[var(--border)] bg-[var(--surface2)]/30"
                            >
                                <div class="flex items-center justify-between border-b border-[var(--border)] bg-[var(--surface2)]/70 px-4 py-3.5">
                                    <div class="flex items-center gap-2.5 font-heading text-sm font-bold">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[var(--primary)]/15 text-xs font-extrabold text-[var(--primary)]">
                                            {{ idx + 1 }}
                                        </span>
                                        <span>{{ section.title }}</span>
                                    </div>
                                    <span class="text-xs text-[var(--text-muted)]">
                                        {{ (section.lessons || []).length }} {{ locale === 'en' ? 'lessons' : 'লেসন' }}
                                    </span>
                                </div>

                                <ul class="divide-y divide-[var(--border)]">
                                    <li v-for="lesson in section.lessons" :key="lesson.id">
                                        <Link
                                            v-if="isEnrolled || lesson.is_free_preview"
                                            :href="`/courses/${course.slug}/sections/${section.id}/lessons/${lesson.id}`"
                                            class="flex items-center justify-between gap-3 px-4 py-3 text-sm transition hover:bg-[var(--primary)]/5"
                                        >
                                            <div class="flex items-center gap-3">
                                                <PlayCircleIcon class="h-5 w-5 shrink-0 text-[var(--primary)]" />
                                                <span class="font-medium text-[var(--text)]">{{ lesson.title }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span
                                                    v-if="lesson.is_free_preview && !isEnrolled"
                                                    class="rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-[11px] font-bold text-emerald-500"
                                                >
                                                    {{ t('admin.courses.free_preview') }}
                                                </span>
                                                <span v-if="lesson.duration_minutes" class="text-xs text-[var(--text-muted)]">
                                                    {{ lesson.duration_minutes }} min
                                                </span>
                                            </div>
                                        </Link>

                                        <div
                                            v-else
                                            class="flex items-center justify-between gap-3 px-4 py-3 text-sm text-[var(--text-muted)] opacity-75"
                                        >
                                            <div class="flex items-center gap-3">
                                                <LockClosedIcon class="h-4 w-4 shrink-0" />
                                                <span>{{ lesson.title }}</span>
                                            </div>
                                            <span v-if="lesson.duration_minutes" class="text-xs">
                                                {{ lesson.duration_minutes }} min
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-8 text-center text-sm text-[var(--text-muted)]">
                            {{ locale === 'en' ? 'Curriculum details will be updated soon.' : 'কোর্সের লেসন শীঘ্রই যুক্ত করা হবে।' }}
                        </div>
                    </div>

                    <!-- What You'll Get Card -->
                    <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm sm:p-8">
                        <h3 class="font-heading text-lg font-bold">
                            {{ locale === 'en' ? 'Why Enroll in This Course?' : 'এই কোর্সে যা যা পাচ্ছেন' }}
                        </h3>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div class="flex items-start gap-3 rounded-xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4">
                                <SparklesIcon class="h-5 w-5 shrink-0 text-[var(--primary)]" />
                                <div class="text-xs leading-relaxed">
                                    <div class="font-bold text-[var(--text)]">{{ locale === 'en' ? 'Instant Access' : 'তাৎক্ষণিক অ্যাক্সেস' }}</div>
                                    <div class="mt-0.5 text-[var(--text-muted)]">{{ locale === 'en' ? 'Start learning immediately after enrollment or payment.' : 'পেমেন্ট বা এনরোল করার সাথে সাথেই কোর্স ম্যাটেরিয়াল ও ভিডিও চালু হয়ে যাবে।' }}</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 rounded-xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4">
                                <CheckBadgeIcon class="h-5 w-5 shrink-0 text-emerald-500" />
                                <div class="text-xs leading-relaxed">
                                    <div class="font-bold text-[var(--text)]">{{ locale === 'en' ? 'Certificate of Completion' : 'কোর্স সমাপনী সার্টিফিকেট' }}</div>
                                    <div class="mt-0.5 text-[var(--text-muted)]">{{ locale === 'en' ? 'Earn a verifiable certificate upon completing all lessons.' : 'কোর্সের সব লেসন সম্পন্ন করলে থাকছে ভেরিফায়েবল ডিজিটাল সার্টিফিকেট।' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sticky Checkout Card (Both Guest Checkout & Auth Checkout) (5 cols) -->
                <div class="lg:col-span-5 xl:col-span-4">
                    <div class="sticky top-24 overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-xl">
                        <!-- Course Cover Preview -->
                        <div class="relative aspect-video overflow-hidden bg-[var(--surface2)]">
                            <img
                                v-if="course.cover_image_url"
                                :src="course.cover_image_url"
                                :alt="course.title"
                                width="480"
                                height="270"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-800 to-indigo-950 text-slate-400">
                                <AcademicCapIcon class="h-12 w-12 opacity-50" />
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Price Header -->
                            <div class="flex items-baseline justify-between border-b border-[var(--border)] pb-4">
                                <div>
                                    <span class="text-xs font-medium uppercase tracking-wider text-[var(--text-muted)]">
                                        {{ locale === 'en' ? 'Course Fee' : 'কোর্স ফি' }}
                                    </span>
                                    <div class="mt-0.5 font-heading text-3xl font-extrabold">
                                        <span v-if="+course.price === 0" class="text-emerald-500">{{ t('common.free') }}</span>
                                        <span v-else>৳{{ money(course.price) }}</span>
                                    </div>
                                </div>

                                <span v-if="+course.price === 0" class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-bold text-emerald-500">
                                    {{ locale === 'en' ? '100% Free' : 'সম্পূর্ণ ফ্রি' }}
                                </span>
                                <span v-else class="rounded-full bg-[var(--primary)]/15 px-3 py-1 text-xs font-bold text-[var(--primary)]">
                                    {{ locale === 'en' ? 'Lifetime Access' : 'আজীবন অ্যাক্সেস' }}
                                </span>
                            </div>

                            <!-- CASE 1: ALREADY ENROLLED -->
                            <div v-if="isEnrolled" class="mt-5 space-y-4">
                                <div v-if="enrollment && course.delivery_type === 'video'">
                                    <div class="mb-2 flex items-center justify-between text-xs font-semibold">
                                        <span>{{ t('courses_page.progress') }}</span>
                                        <span class="text-[var(--primary)]">{{ enrollment.progress_percentage ?? 0 }}%</span>
                                    </div>
                                    <div class="h-2.5 overflow-hidden rounded-full bg-[var(--surface2)]">
                                        <div class="h-full rounded-full bg-[var(--primary)] transition-all" :style="{ width: `${enrollment.progress_percentage ?? 0}%` }"></div>
                                    </div>
                                </div>

                                <Link
                                    v-if="course.delivery_type === 'video' && firstAccessibleLessonUrl"
                                    :href="firstAccessibleLessonUrl"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-[var(--primary-hover)]"
                                >
                                    <PlayCircleIcon class="h-5 w-5" />
                                    <span>{{ locale === 'en' ? 'Continue Learning' : 'ক্লাস শুরু করুন' }}</span>
                                </Link>

                                <a
                                    v-if="enrollment?.certificate_path"
                                    :href="`/storage/${enrollment.certificate_path}`"
                                    target="_blank"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-600"
                                >
                                    <CheckBadgeIcon class="h-5 w-5" />
                                    <span>{{ t('courses_page.download_certificate') }}</span>
                                </a>
                            </div>

                            <!-- CASE 2: LOGGED-IN USER CHECKOUT -->
                            <div v-else-if="isLoggedIn" class="mt-5 space-y-3">
                                <button
                                    v-if="+course.price === 0"
                                    type="button"
                                    @click="enrollAuthenticated('wallet')"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/25 transition hover:bg-[var(--primary-hover)]"
                                >
                                    <CheckBadgeIcon class="h-5 w-5" />
                                    <span>{{ t('courses_page.enroll_free') }}</span>
                                </button>

                                <template v-else>
                                    <button
                                        type="button"
                                        @click="enrollAuthenticated('zinipay')"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/25 transition hover:bg-[var(--primary-hover)]"
                                    >
                                        <CreditCardIcon class="h-5 w-5" />
                                        <span>{{ t('book_show.pay_with_gateway') }} (৳{{ money(course.price) }})</span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="enrollAuthenticated('wallet')"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface2)]/70 px-6 py-3 text-sm font-semibold transition hover:border-[var(--primary)]"
                                    >
                                        <WalletIcon class="h-5 w-5 text-[var(--secondary)]" />
                                        <span>{{ t('book_show.pay_with_wallet') }} ({{ locale === 'en' ? 'Bal' : 'ব্যালেন্স' }}: ৳{{ money(walletBalance) }})</span>
                                    </button>
                                </template>
                            </div>

                            <!-- CASE 3: GUEST VISITOR -> BOTH GUEST CHECKOUT AND USER AUTH CHECKOUT -->
                            <div v-else class="mt-5">
                                <!-- Switch Tabs: Guest Checkout vs Account Login -->
                                <div class="grid grid-cols-2 gap-1 rounded-xl border border-[var(--border)] bg-[var(--surface2)] p-1 text-xs font-bold">
                                    <button
                                        type="button"
                                        @click="checkoutTab = 'guest'"
                                        class="rounded-lg py-2 transition"
                                        :class="checkoutTab === 'guest'
                                            ? 'bg-[var(--primary)] text-white shadow'
                                            : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                                    >
                                        {{ locale === 'en' ? 'Guest Checkout' : 'গেস্ট চেকআউট' }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="checkoutTab = 'auth'"
                                        class="rounded-lg py-2 transition"
                                        :class="checkoutTab === 'auth'
                                            ? 'bg-[var(--primary)] text-white shadow'
                                            : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                                    >
                                        {{ locale === 'en' ? 'Account Checkout' : 'লগইন চেকআউট' }}
                                    </button>
                                </div>

                                <!-- TAB A: Guest Checkout Form -->
                                <form v-if="checkoutTab === 'guest'" @submit.prevent="submitGuestCheckout" class="mt-4 space-y-3.5">
                                    <p class="text-xs leading-relaxed text-[var(--text-muted)]">
                                        {{ locale === 'en'
                                            ? 'No account needed! Enter your name and email below to enroll directly and receive instant course access.'
                                            : 'লগইন ছাড়াই সরাসরি কোর্সটি কিনুন বা এনরোল করুন। আপনার ইমেইলে তাৎক্ষণিক অ্যাক্সেস পাঠিয়ে দেওয়া হবে।' }}
                                    </p>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold">{{ locale === 'en' ? 'Your Full Name *' : 'আপনার নাম *' }}</label>
                                        <div class="relative">
                                            <UserIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                                            <input
                                                v-model="guestForm.name"
                                                type="text"
                                                required
                                                :placeholder="locale === 'en' ? 'Enter your name' : 'আপনার পূর্ণ নাম লিখুন'"
                                                class="input pl-9 text-sm"
                                            />
                                        </div>
                                        <p v-if="guestForm.errors.name" class="mt-1 text-xs text-rose-500">{{ guestForm.errors.name }}</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold">{{ locale === 'en' ? 'Email Address *' : 'ইমেইল ঠিকানা *' }}</label>
                                        <div class="relative">
                                            <EnvelopeIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                                            <input
                                                v-model="guestForm.email"
                                                type="email"
                                                required
                                                :placeholder="locale === 'en' ? 'you@example.com' : ' আপনার ইমেইল লিখুন'"
                                                class="input pl-9 text-sm"
                                            />
                                        </div>
                                        <p v-if="guestForm.errors.email" class="mt-1 text-xs text-rose-500">{{ guestForm.errors.email }}</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold">{{ locale === 'en' ? 'Mobile Number (Optional)' : 'মোবাইল নম্বর (ঐচ্ছিক)' }}</label>
                                        <div class="relative">
                                            <PhoneIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                                            <input
                                                v-model="guestForm.phone"
                                                type="tel"
                                                placeholder="01XXXXXXXXX"
                                                class="input pl-9 text-sm"
                                            />
                                        </div>
                                    </div>

                                    <button
                                        type="submit"
                                        :disabled="guestForm.processing"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/25 transition hover:bg-[var(--primary-hover)] disabled:opacity-60"
                                    >
                                        <CreditCardIcon v-if="+course.price > 0" class="h-5 w-5" />
                                        <CheckBadgeIcon v-else class="h-5 w-5" />
                                        <span>
                                            {{ +course.price === 0
                                                ? (locale === 'en' ? 'Instant Free Enroll' : 'গেস্ট হিসেবে ফ্রি এনরোল করুন')
                                                : (locale === 'en' ? `Proceed to Payment (৳${money(course.price)})` : `সরাসরি পেমেন্ট ও এনরোল (৳${money(course.price)})`) }}
                                        </span>
                                    </button>
                                </form>

                                <!-- TAB B: User Auth Checkout -->
                                <div v-else class="mt-4 space-y-3">
                                    <p class="text-xs leading-relaxed text-[var(--text-muted)]">
                                        {{ locale === 'en'
                                            ? 'Sign in to your Sikhun account to pay with your Wallet balance or manage all your courses in one dashboard.'
                                            : 'আপনার শিখুন অ্যাকাউন্টে লগইন করে ওয়ালেট ব্যালেন্স বা অনলাইন পেমেন্টের মাধ্যমে কোর্সটি এনরোল করুন।' }}
                                    </p>

                                    <Link
                                        :href="loginRedirectUrl"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/25 transition hover:bg-[var(--primary-hover)]"
                                    >
                                        <span>{{ locale === 'en' ? 'Sign In & Checkout' : 'লগইন করে এনরোল করুন' }}</span>
                                    </Link>

                                    <Link
                                        :href="registerRedirectUrl"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface2)]/60 px-6 py-3 text-sm font-semibold transition hover:border-[var(--primary)]"
                                    >
                                        <span>{{ locale === 'en' ? 'Create New Account' : 'নতুন অ্যাকাউন্ট তৈরি করুন' }}</span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Courses -->
            <div v-if="relatedCourses.length" class="mt-14">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="font-heading text-2xl font-extrabold">
                        {{ locale === 'en' ? 'More Courses You Might Like' : 'সম্পর্কিত আরও কোর্স' }}
                    </h2>
                    <Link href="/courses" class="text-sm font-semibold text-[var(--primary)] hover:underline">
                        {{ locale === 'en' ? 'All Courses →' : 'সব কোর্স দেখুন →' }}
                    </Link>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="rel in relatedCourses"
                        :key="rel.id"
                        :href="`/courses/${rel.slug}`"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] transition hover:-translate-y-1 hover:border-[var(--primary)]/50 hover:shadow-lg"
                    >
                        <div class="relative aspect-video overflow-hidden bg-[var(--surface2)]">
                            <img v-if="rel.cover_image_url" :src="rel.cover_image_url" :alt="rel.title" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            <div v-else class="flex h-full w-full items-center justify-center text-[var(--text-muted)]">
                                <AcademicCapIcon class="h-10 w-10 opacity-40" />
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="line-clamp-2 font-heading text-base font-bold group-hover:text-[var(--primary)]">{{ rel.title }}</h3>
                            <p v-if="rel.mentor?.name" class="mt-1 text-xs text-[var(--text-muted)]">{{ rel.mentor.name }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="font-heading font-extrabold" :class="+rel.price === 0 ? 'text-emerald-500' : ''">
                                    {{ +rel.price === 0 ? t('common.free') : `৳${money(rel.price)}` }}
                                </span>
                                <span class="text-xs text-[var(--text-muted)]">{{ rel.enrollments_count ?? 0 }} {{ locale === 'en' ? 'enrolled' : 'জন ভর্তি' }}</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
