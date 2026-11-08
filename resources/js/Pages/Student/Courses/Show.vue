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
    ChevronDownIcon,
    ArrowTopRightOnSquareIcon,
    SparklesIcon,
    ShareIcon,
    CheckIcon,
    DocumentTextIcon,
    StarIcon,
    ArrowPathIcon,
    ShieldCheckIcon,
    ComputerDesktopIcon,
    ArrowDownTrayIcon,
    QuestionMarkCircleIcon,
    ChatBubbleBottomCenterTextIcon,
    BookOpenIcon,
    FireIcon,
} from '@heroicons/vue/24/outline';
import { StarIcon as StarSolidIcon, CheckCircleIcon as CheckCircleSolidIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    course: Object,
    enrollment: Object,
    isEnrolled: Boolean,
    hasAccess: Boolean,
    walletBalance: { type: Number, default: 0 },
    relatedCourses: { type: Array, default: () => [] },
    seo: Object,
});

const i18n = useI18n();
const t = i18n.t;
const locale = i18n.locale;
const money = i18n.money || ((v) => v);
const digits = i18n.digits || ((v) => v);
const page = usePage();
const isLoggedIn = computed(() => !!page.props.auth?.student);
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

// Active Tab navigation: 'overview' | 'curriculum' | 'mentor' | 'faq' | 'reviews'
const activeTab = ref('overview');

// Checkout mode tab for unauthenticated visitors: 'guest' | 'auth'
const checkoutTab = ref('guest');

// Link copied feedback toast
const copiedToast = ref(false);

function copyCourseLink() {
    if (navigator?.clipboard) {
        navigator.clipboard.writeText(window.location.href);
    }
    copiedToast.value = true;
    setTimeout(() => {
        copiedToast.value = false;
    }, 2500);
}

// Collapsible accordion state for curriculum sections
// By default, open the first 2 sections
const openSections = ref({});
if (props.course.sections?.length) {
    props.course.sections.forEach((s, idx) => {
        openSections.value[s.id] = idx < 2; // open first 2 sections
    });
}

function toggleSection(sectionId) {
    openSections.value[sectionId] = !openSections.value[sectionId];
}

const allExpanded = computed(() => {
    if (!props.course.sections?.length) return false;
    return props.course.sections.every(s => openSections.value[s.id]);
});

function toggleAllSections() {
    const targetState = !allExpanded.value;
    (props.course.sections || []).forEach(s => {
        openSections.value[s.id] = targetState;
    });
}

// FAQ Accordion State
const openFaq = ref({ 0: true });
function toggleFaq(idx) {
    openFaq.value[idx] = !openFaq.value[idx];
}

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

const totalDurationFormatted = computed(() => {
    const mins = totalDurationMinutes.value;
    if (!mins) return locale.value === 'en' ? '10+ Hours' : '১০+ ঘণ্টা';
    const hours = Math.floor(mins / 60);
    const remainder = mins % 60;
    if (hours > 0 && remainder > 0) {
        return locale.value === 'en' ? `${hours}h ${remainder}m` : `${hours} ঘণ্টা ${remainder} মিনিট`;
    } else if (hours > 0) {
        return locale.value === 'en' ? `${hours} Hours` : `${hours} ঘণ্টা`;
    }
    return locale.value === 'en' ? `${remainder} Minutes` : `${remainder} মিনিট`;
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

const firstPreviewLessonUrl = computed(() => {
    if (!props.course.sections?.length) return null;
    for (const section of props.course.sections) {
        for (const lesson of (section.lessons || [])) {
            if (lesson.is_free_preview) {
                return `/courses/${props.course.slug}/sections/${section.id}/lessons/${lesson.id}`;
            }
        }
    }
    return firstAccessibleLessonUrl.value;
});

// Format level display
const formattedLevel = computed(() => {
    const map = {
        ssc: locale.value === 'en' ? 'SSC Academic' : 'এসএসসি একাডেমিক',
        hsc: locale.value === 'en' ? 'HSC Academic' : 'এইচএসসি একাডেমিক',
        university: locale.value === 'en' ? 'University Admission' : 'ভার্সিটি ভর্তি প্রস্তুতি',
        job: locale.value === 'en' ? 'Job & Career Skills' : 'জব ও ক্যারিয়ার স্কিল',
    };
    return map[props.course.level] || (props.course.level ? props.course.level.toUpperCase() : 'ALL LEVELS');
});

// Calculate discount price representation (e.g. ৳99 vs ৳999)
const regularPrice = computed(() => {
    const p = Number(props.course.price) || 0;
    if (p === 0) return 0;
    return Math.max(p * 5, 999);
});

// Tab definitions
const tabs = computed(() => [
    { id: 'overview', label: locale.value === 'en' ? 'Overview' : 'কোর্স পরিচিতি', icon: BookOpenIcon },
    {
        id: 'curriculum',
        label: locale.value === 'en' ? `Curriculum (${totalLessonsCount.value})` : `সিলেবাস (${totalLessonsCount.value}টি লেসন)`,
        icon: PlayCircleIcon,
    },
    { id: 'mentor', label: locale.value === 'en' ? 'Instructor' : 'ইন্সট্রাক্টর', icon: UserIcon },
    { id: 'faq', label: locale.value === 'en' ? 'FAQ' : 'সাধারণ জিজ্ঞাসা', icon: QuestionMarkCircleIcon },
    { id: 'reviews', label: locale.value === 'en' ? 'Reviews (4.9 ★)' : 'রিভিউ (৪.৯ ★)', icon: StarIcon },
]);

function scrollToCheckout() {
    const el = document.getElementById('checkout-card');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>

<template>
    <Head :title="course.title" />
    <SeoHead v-if="seo" :seo="seo" />

    <PublicLayout>
        <!-- TOAST: Link Copied Feedback -->
        <Teleport to="body">
            <Transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="copiedToast"
                    class="fixed bottom-6 right-6 z-50 flex items-center gap-2.5 rounded-2xl bg-slate-900/95 px-5 py-3 text-xs font-bold text-white shadow-2xl backdrop-blur-md border border-white/10"
                >
                    <CheckCircleSolidIcon class="h-5 w-5 text-emerald-400" />
                    <span>{{ locale === 'en' ? 'Course link copied to clipboard!' : 'কোর্স লিংক ক্লিপবোর্ডে কপি করা হয়েছে!' }}</span>
                </div>
            </Transition>
        </Teleport>

        <!-- HERO SECTION: Premium Obsidian & Dark Aurora Gradient -->
        <section class="relative overflow-hidden border-b border-[var(--border)] bg-gradient-to-br from-[#07090e] via-[#0f1422] to-[#151c33] text-white pt-8 pb-12 sm:pt-12 sm:pb-16 lg:pb-20">
            <!-- Ambient Glow Orbs -->
            <div class="pointer-events-none absolute -top-32 -right-24 h-96 w-96 rounded-full bg-orange-500/20 blur-[100px]"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-indigo-500/15 blur-[110px]"></div>
            <div class="pointer-events-none absolute top-1/2 left-1/3 h-64 w-64 -translate-y-1/2 rounded-full bg-cyan-500/10 blur-[90px]"></div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <nav aria-label="Breadcrumb" class="mb-6 flex flex-wrap items-center gap-2 text-xs text-slate-400">
                    <Link href="/" class="hover:text-white transition flex items-center gap-1">
                        <span>{{ locale === 'en' ? 'Home' : 'হোম' }}</span>
                    </Link>
                    <ChevronRightIcon class="h-3 w-3 text-slate-600" />
                    <Link href="/courses" class="hover:text-white transition">{{ t('nav.courses') }}</Link>
                    <template v-if="course.category">
                        <ChevronRightIcon class="h-3 w-3 text-slate-600" />
                        <Link :href="`/courses?category_id=${course.category.id}`" class="hover:text-white transition">{{ course.category.name }}</Link>
                    </template>
                    <ChevronRightIcon class="h-3 w-3 text-slate-600" />
                    <span class="truncate max-w-[260px] sm:max-w-md text-orange-400 font-medium">{{ course.title }}</span>
                </nav>

                <div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-12">
                    <!-- Left Hero Content (7 or 8 cols) -->
                    <div class="space-y-5 lg:col-span-7 xl:col-span-8">
                        <!-- Top Badges Row -->
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span v-if="course.category" class="inline-flex items-center gap-1.5 rounded-full border border-orange-500/30 bg-orange-500/15 px-3 py-1 text-xs font-bold text-orange-300">
                                <SparklesIcon class="h-3.5 w-3.5" />
                                {{ course.category.name }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-400/30 bg-indigo-500/15 px-3 py-1 text-xs font-bold text-indigo-300">
                                <AcademicCapIcon class="h-3.5 w-3.5" />
                                {{ formattedLevel }}
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full border border-amber-400/30 bg-amber-500/15 px-3 py-1 text-xs font-bold text-amber-300">
                                <FireIcon class="h-3.5 w-3.5 text-amber-400" />
                                {{ locale === 'en' ? 'Bestseller' : 'বেস্টসেলার' }}
                            </span>
                            <span v-if="isEnrolled" class="inline-flex items-center gap-1 rounded-full border border-emerald-400/30 bg-emerald-500/20 px-3 py-1 text-xs font-bold text-emerald-300">
                                <CheckCircleSolidIcon class="h-4 w-4" />
                                {{ locale === 'en' ? 'Enrolled / Unlocked' : 'আপনার এনরোলকৃত' }}
                            </span>
                        </div>

                        <!-- Course Title -->
                        <h1 class="font-heading text-2xl font-extrabold tracking-tight sm:text-3xl md:text-4xl lg:text-[2.6rem] lg:leading-[1.2]">
                            {{ course.title }}
                        </h1>

                        <!-- Lead Highlights Snippet -->
                        <p class="text-sm sm:text-base leading-relaxed text-slate-300 max-w-3xl font-normal">
                            {{ locale === 'en'
                                ? 'Master data analysis with Python from zero to hero. Learn NumPy, Pandas, SQL, Data Visualization, and real-world Capstone projects designed for high-paying careers.'
                                : 'একদম শুরু থেকে প্রফেশনাল ডেটা অ্যানালাইসিস শিখুন পাইথনের মাধ্যমে। নামপাই, পান্ডাস, এসকিউএল, ডেটা ভিজ্যুয়ালাইজেশন ও জব-রেডি প্রজেক্ট তৈরির সম্পূর্ণ হ্যান্ডস-অন প্রোগ্রাম।' }}
                        </p>

                        <!-- Ratings & Social Proof Pill -->
                        <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm text-slate-300 pt-1">
                            <div class="flex items-center gap-1.5 rounded-xl bg-white/5 border border-white/10 px-3 py-1.5">
                                <div class="flex text-amber-400">
                                    <StarSolidIcon v-for="s in 5" :key="s" class="h-4 w-4" />
                                </div>
                                <span class="font-bold text-white">4.9</span>
                                <span class="text-slate-400">({{ locale === 'en' ? '128 ratings' : '১২৮+ রিভিউ' }})</span>
                            </div>

                            <div class="flex items-center gap-2 rounded-xl bg-white/5 border border-white/10 px-3 py-1.5">
                                <UsersIcon class="h-4 w-4 text-orange-400" />
                                <span class="font-semibold text-white">{{ course.enrollments_count ?? 0 }}</span>
                                <span class="text-slate-400">{{ locale === 'en' ? 'students enrolled' : 'জন শিক্ষার্থী' }}</span>
                            </div>

                            <button
                                type="button"
                                @click="copyCourseLink"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-white/5 border border-white/10 px-3 py-1.5 text-xs text-slate-300 hover:text-white hover:bg-white/10 transition"
                            >
                                <ShareIcon class="h-3.5 w-3.5" />
                                <span>{{ locale === 'en' ? 'Share' : 'শেয়ার করুন' }}</span>
                            </button>
                        </div>

                        <!-- Mentor Pill / Author Box in Hero -->
                        <div v-if="course.mentor" class="flex items-center gap-3 pt-2">
                            <div class="relative h-11 w-11 shrink-0 overflow-hidden rounded-full ring-2 ring-orange-500/40 bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center font-bold text-base shadow-md">
                                <img
                                    v-if="course.mentor.photo"
                                    :src="`/storage/${course.mentor.photo}`"
                                    :alt="course.mentor.name"
                                    class="h-full w-full object-cover"
                                />
                                <span v-else>{{ course.mentor.name?.charAt(0) || 'M' }}</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs text-slate-400">{{ locale === 'en' ? 'Created by' : 'প্রশিক্ষক / মেন্টর:' }}</span>
                                    <span class="text-sm font-bold text-white hover:underline cursor-pointer">{{ course.mentor.name }}</span>
                                    <CheckBadgeIcon class="h-4 w-4 text-sky-400" />
                                </div>
                                <p v-if="course.mentor.designation" class="text-xs text-slate-400">{{ course.mentor.designation }}</p>
                            </div>
                        </div>

                        <!-- Key Highlights Strip -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-3">
                            <div class="flex items-center gap-2.5 rounded-xl bg-white/[0.04] border border-white/10 p-3">
                                <ClockIcon class="h-5 w-5 text-indigo-400 shrink-0" />
                                <div class="text-xs">
                                    <span class="block text-[10px] text-slate-400 uppercase font-semibold">{{ locale === 'en' ? 'Duration' : 'মোট সময়' }}</span>
                                    <span class="font-bold text-white">{{ totalDurationFormatted }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5 rounded-xl bg-white/[0.04] border border-white/10 p-3">
                                <PlayCircleIcon class="h-5 w-5 text-emerald-400 shrink-0" />
                                <div class="text-xs">
                                    <span class="block text-[10px] text-slate-400 uppercase font-semibold">{{ locale === 'en' ? 'Lectures' : 'লেসন ও মডিউল' }}</span>
                                    <span class="font-bold text-white">{{ (course.sections || []).length }} {{ locale === 'en' ? 'Sections' : 'মডিউল' }} · {{ totalLessonsCount }} {{ locale === 'en' ? 'Lessons' : 'লেসন' }}</span>
                                </div>
                            </div>
                            <div class="col-span-2 sm:col-span-1 flex items-center gap-2.5 rounded-xl bg-white/[0.04] border border-white/10 p-3">
                                <ShieldCheckIcon class="h-5 w-5 text-orange-400 shrink-0" />
                                <div class="text-xs">
                                    <span class="block text-[10px] text-slate-400 uppercase font-semibold">{{ locale === 'en' ? 'Access' : 'অ্যাক্সেস' }}</span>
                                    <span class="font-bold text-white">{{ locale === 'en' ? 'Lifetime Access' : 'আজীবন অ্যাক্সেস' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column Quick Preview (Mobile & Desktop anchor) -->
                    <div class="hidden lg:block lg:col-span-5 xl:col-span-4">
                        <!-- Space holder so the sticky checkout card aligns nicely -->
                    </div>
                </div>
            </div>
        </section>

        <!-- MAIN CONTENT + STICKY SIDEBAR -->
        <section class="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
            <!-- Flash Alerts -->
            <div v-if="flashSuccess" class="mb-6 flex items-center gap-2.5 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-sm font-semibold text-emerald-600 dark:text-emerald-300 shadow-sm">
                <CheckCircleSolidIcon class="h-5 w-5 shrink-0" />
                <span>{{ flashSuccess }}</span>
            </div>
            <div v-if="flashError" class="mb-6 flex items-center gap-2.5 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-5 py-4 text-sm font-semibold text-rose-600 dark:text-rose-300 shadow-sm">
                <QuestionMarkCircleIcon class="h-5 w-5 shrink-0" />
                <span>{{ flashError }}</span>
            </div>

            <div class="grid gap-10 lg:grid-cols-12 lg:items-start">
                <!-- Left Column (7/8 cols): Tabs + Detailed Content -->
                <div class="space-y-8 lg:col-span-7 xl:col-span-8">
                    <!-- ENROLLED ACCESS BANNER (If user owns course) -->
                    <div
                        v-if="isEnrolled"
                        class="overflow-hidden rounded-3xl border border-emerald-500/40 bg-gradient-to-br from-emerald-500/10 via-[var(--surface)] to-emerald-500/5 p-6 sm:p-8 shadow-xl"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="space-y-1">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                    <CheckBadgeIcon class="h-4 w-4" />
                                    {{ locale === 'en' ? 'Course Unlocked & Ready' : 'কোর্স অ্যাক্সেস সক্রিয় রয়েছে' }}
                                </span>
                                <h2 class="font-heading text-xl font-extrabold text-[var(--text)] sm:text-2xl">
                                    {{ course.delivery_type === 'file_download' ? (locale === 'en' ? 'Download Full Course & Resources' : 'সম্পূর্ণ কোর্স ফাইল ও রিসোর্স ডাউনলোড করুন') : (locale === 'en' ? 'Access Course Content' : 'কোর্স ক্লাস শুরু করুন') }}
                                </h2>
                                <p class="text-xs text-[var(--text-muted)] max-w-xl">
                                    {{ locale === 'en'
                                        ? 'You have lifetime access to this course. You can download all lecture videos, Jupyter Notebooks, datasets, and project files anytime.'
                                        : 'এই কোর্সে আপনার আজীবন অ্যাক্সেস রয়েছে। সব লেকচার, জুপিটার নোটবুক ও ডেটাসেট যেকোনো সময় সরাসরি ডাউনলোড করে অফলাইনেও দেখতে পারবেন।' }}
                                </p>
                            </div>

                            <!-- Big Action Button for Enrolled Student -->
                            <div class="flex flex-wrap items-center gap-3">
                                <a
                                    v-if="course.external_link"
                                    :href="course.external_link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 transition hover:scale-[1.02] hover:shadow-xl active:scale-[0.98]"
                                >
                                    <ArrowDownTrayIcon v-if="course.delivery_type === 'file_download'" class="h-5 w-5" />
                                    <ArrowTopRightOnSquareIcon v-else class="h-5 w-5" />
                                    <span>{{ course.delivery_type === 'file_download' ? (locale === 'en' ? 'Open Drive & Download' : 'গুগল ড্রাইভ লিংক খুলুন') : (locale === 'en' ? 'Launch Course' : 'কোর্স শুরু করুন') }}</span>
                                </a>

                                <Link
                                    v-if="firstAccessibleLessonUrl"
                                    :href="firstAccessibleLessonUrl"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/25 transition hover:scale-[1.02] active:scale-[0.98]"
                                >
                                    <PlayCircleIcon class="h-5 w-5" />
                                    <span>{{ locale === 'en' ? 'Watch Online' : 'অনলাইনে দেখুন' }}</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Instructor's special note -->
                        <div v-if="course.link_note" class="mt-5 rounded-2xl border border-emerald-500/20 bg-[var(--surface)] p-4 text-xs text-[var(--text)] leading-relaxed shadow-sm">
                            <div class="flex items-center gap-1.5 font-bold text-emerald-600 dark:text-emerald-400 mb-1">
                                <SparklesIcon class="h-4 w-4" />
                                <span>{{ locale === 'en' ? 'Instructor Note / Instructions:' : 'ইন্সট্রাক্টরের বিশেষ নির্দেশিকা:' }}</span>
                            </div>
                            <p class="whitespace-pre-line text-[var(--text-muted)]">{{ course.link_note }}</p>
                        </div>
                    </div>

                    <!-- SMART TABBED NAVIGATION -->
                    <div class="sticky top-16 z-20 -mx-4 border-b border-[var(--border)] bg-[var(--bg)]/90 px-4 backdrop-blur-xl sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                        <div class="flex space-x-1 overflow-x-auto py-2.5 no-scrollbar">
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                type="button"
                                @click="activeTab = tab.id"
                                class="inline-flex shrink-0 items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all duration-200"
                                :class="activeTab === tab.id
                                    ? 'bg-[var(--primary)] text-white shadow-md shadow-[var(--primary)]/20'
                                    : 'text-[var(--text-muted)] hover:bg-[var(--surface2)] hover:text-[var(--text)]'"
                            >
                                <component :is="tab.icon" class="h-4 w-4" />
                                <span>{{ tab.label }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- TAB 1: OVERVIEW & LEARNING OUTCOMES -->
                    <div v-show="activeTab === 'overview'" class="space-y-8 animate-fadeIn">
                        <!-- What You'll Learn Box -->
                        <div class="rounded-3xl border border-orange-500/25 bg-gradient-to-br from-orange-500/[0.04] via-[var(--surface)] to-indigo-500/[0.03] p-6 sm:p-8 shadow-sm">
                            <div class="flex items-center gap-2.5 text-[var(--primary)] font-bold text-base sm:text-lg mb-4">
                                <SparklesIcon class="h-5 w-5" />
                                <h2 class="font-heading text-lg sm:text-xl font-bold text-[var(--text)]">
                                    {{ locale === 'en' ? 'What You Will Master in This Course' : 'এই কোর্স থেকে আপনি কী কী শিখবেন' }}
                                </h2>
                            </div>

                            <div class="grid gap-3.5 sm:grid-cols-2">
                                <div class="flex items-start gap-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3.5 transition hover:border-[var(--primary)]/40 hover:shadow-sm">
                                    <div class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-500/15 text-emerald-500 mt-0.5">
                                        <CheckIcon class="h-4 w-4 stroke-[2.5]" />
                                    </div>
                                    <div class="text-xs leading-relaxed">
                                        <strong class="text-[var(--text)] font-semibold">{{ locale === 'en' ? 'Python Programming Zero to Hero' : 'পাইথন প্রোগ্রামিং জিরো টু হিরো' }}</strong>
                                        <p class="text-[var(--text-muted)] mt-0.5">{{ locale === 'en' ? 'Variables, loops, data types, functions & best coding standards.' : 'ভ্যারিয়েবল, লুপ, ডেটা টাইপ, ফাংশন ও ক্লিন কোডিং স্ট্যান্ডার্ড।' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3.5 transition hover:border-[var(--primary)]/40 hover:shadow-sm">
                                    <div class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-500/15 text-emerald-500 mt-0.5">
                                        <CheckIcon class="h-4 w-4 stroke-[2.5]" />
                                    </div>
                                    <div class="text-xs leading-relaxed">
                                        <strong class="text-[var(--text)] font-semibold">{{ locale === 'en' ? 'Pandas & NumPy Mastery' : 'পান্ডাস ও নামপাই দিয়ে ডেটা প্রসেসিং' }}</strong>
                                        <p class="text-[var(--text-muted)] mt-0.5">{{ locale === 'en' ? 'Clean messy data, handle missing values, and build pivot tables.' : 'অপরিষ্কার ডেটাসেট ক্লিন, মিসিং ভ্যালু ফিক্স এবং পাইভট টেবিল তৈরি।' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3.5 transition hover:border-[var(--primary)]/40 hover:shadow-sm">
                                    <div class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-500/15 text-emerald-500 mt-0.5">
                                        <CheckIcon class="h-4 w-4 stroke-[2.5]" />
                                    </div>
                                    <div class="text-xs leading-relaxed">
                                        <strong class="text-[var(--text)] font-semibold">{{ locale === 'en' ? 'SQL with Python Integration' : 'এসকিউএল ও ডেটাবেস কানেক্টিভিটি' }}</strong>
                                        <p class="text-[var(--text-muted)] mt-0.5">{{ locale === 'en' ? 'Query relational databases and build DataFrames directly.' : 'রিলেশনাল ডেটাবেস থেকে কুয়েরি রান করে ডেটা ফেচ ও ফিল্টারিং।' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3.5 transition hover:border-[var(--primary)]/40 hover:shadow-sm">
                                    <div class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-500/15 text-emerald-500 mt-0.5">
                                        <CheckIcon class="h-4 w-4 stroke-[2.5]" />
                                    </div>
                                    <div class="text-xs leading-relaxed">
                                        <strong class="text-[var(--text)] font-semibold">{{ locale === 'en' ? 'Exploratory Data Analysis (EDA)' : 'এক্সপ্লোরেটরি ডেটা অ্যানালাইসিস' }}</strong>
                                        <p class="text-[var(--text-muted)] mt-0.5">{{ locale === 'en' ? 'Uncover hidden trends, business insights & statistics.' : 'বিজনেস স্ট্যাটিস্টিকস ও ডেটার হিডেন প্যাটার্ন খুঁজে বের করা।' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3.5 transition hover:border-[var(--primary)]/40 hover:shadow-sm">
                                    <div class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-500/15 text-emerald-500 mt-0.5">
                                        <CheckIcon class="h-4 w-4 stroke-[2.5]" />
                                    </div>
                                    <div class="text-xs leading-relaxed">
                                        <strong class="text-[var(--text)] font-semibold">{{ locale === 'en' ? 'Data Visualization & Storytelling' : 'ডেটা ভিজ্যুয়ালাইজেশন ও চার্টিং' }}</strong>
                                        <p class="text-[var(--text-muted)] mt-0.5">{{ locale === 'en' ? 'Create stunning charts with Matplotlib & Seaborn.' : 'ম্যাটপ্লটলিব ও সীবর্ন দিয়ে দৃষ্টিনন্দন চার্ট ও ড্যাশবোর্ড তৈরি।' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3.5 transition hover:border-[var(--primary)]/40 hover:shadow-sm">
                                    <div class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-emerald-500/15 text-emerald-500 mt-0.5">
                                        <CheckIcon class="h-4 w-4 stroke-[2.5]" />
                                    </div>
                                    <div class="text-xs leading-relaxed">
                                        <strong class="text-[var(--text)] font-semibold">{{ locale === 'en' ? 'Real-Life Capstone Project' : 'পূর্ণাঙ্গ পোর্টফোলিও প্রজেক্ট' }}</strong>
                                        <p class="text-[var(--text-muted)] mt-0.5">{{ locale === 'en' ? 'Complete an end-to-end dataset analysis to add to your CV.' : 'সিভি বা লিঙ্কডইনে যুক্ত করার মতো বাস্তব ডেটা অ্যানালাইসিস প্রজেক্ট।' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Who Is This Course For -->
                        <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                            <h3 class="font-heading text-lg sm:text-xl font-bold text-[var(--text)] mb-4 flex items-center gap-2">
                                <UsersIcon class="h-5 w-5 text-indigo-500" />
                                <span>{{ locale === 'en' ? 'Who Should Take This Course?' : 'কাদের জন্য এই কোর্সটি আদর্শ?' }}</span>
                            </h3>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4">
                                    <div class="font-bold text-xs sm:text-sm text-[var(--text)] flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                                        <span>{{ locale === 'en' ? 'Complete Beginners' : 'কোডিংয়ে সম্পূর্ণ নতুন যারা' }}</span>
                                    </div>
                                    <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">
                                        {{ locale === 'en' ? 'Those with zero prior programming knowledge who want to build a career in Data Analytics.' : 'যাদের প্রোগ্রামিংয়ের কোনো পূর্ব অভিজ্ঞতা নেই কিন্তু ডেটা অ্যানালাইটিক্সে ক্যারিয়ার গড়তে চান।' }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4">
                                    <div class="font-bold text-xs sm:text-sm text-[var(--text)] flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                        <span>{{ locale === 'en' ? 'Excel & Business Professionals' : 'এক্সেল ও বিজনেস প্রফেশনাল' }}</span>
                                    </div>
                                    <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">
                                        {{ locale === 'en' ? 'Spreadsheet power users looking to upgrade to automated, scalable Python workflows.' : 'এক্সেল ব্যবহারকারী যারা পাইথন দিয়ে বড় ডেটাসেট অটোমেশন ও দ্রুত কাজ করতে চান।' }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4">
                                    <div class="font-bold text-xs sm:text-sm text-[var(--text)] flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        <span>{{ locale === 'en' ? 'Students & Job Seekers' : 'বিশ্ববিদ্যালয়ের শিক্ষার্থী ও চাকরিপ্রত্যাশী' }}</span>
                                    </div>
                                    <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">
                                        {{ locale === 'en' ? 'Graduates wanting strong technical projects on their resume to stand out in interviews.' : 'যাঁরা সিভিতে আকর্ষণীয় প্রজেক্ট দেখিয়ে দেশি-বিদেশি ডেটা সায়েন্স ও আইটি জবে আবেদন করতে চান।' }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4">
                                    <div class="font-bold text-xs sm:text-sm text-[var(--text)] flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                                        <span>{{ locale === 'en' ? 'Freelancers' : 'ফ্রিল্যান্সার ও রিমোট ওয়ার্কার্স' }}</span>
                                    </div>
                                    <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">
                                        {{ locale === 'en' ? 'Upwork, Fiverr & remote workers who want to offer high-paying Data Analytics services.' : 'Upwork ও Fiverr-এ উচ্চমূল্যের ডেটা অ্যানালাইসিস ও ভিজ্যুয়ালাইজেশন সার্ভিস দিতে চান যারা।' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Course Requirements / Prerequisites -->
                        <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                            <h3 class="font-heading text-lg sm:text-xl font-bold text-[var(--text)] mb-3 flex items-center gap-2">
                                <CheckBadgeIcon class="h-5 w-5 text-emerald-500" />
                                <span>{{ locale === 'en' ? 'Requirements & Prerequisites' : 'কোর্সের পূর্বশর্ত' }}</span>
                            </h3>
                            <ul class="space-y-2 text-xs sm:text-sm text-[var(--text-muted)]">
                                <li class="flex items-center gap-2">
                                    <CheckIcon class="h-4 w-4 text-emerald-500 shrink-0" />
                                    <span>{{ locale === 'en' ? 'A desktop or laptop (Windows, Mac, or Linux) with basic internet connectivity.' : 'একটি কম্পিউটার বা ল্যাপটপ (উইন্ডোজ, ম্যাক বা লিনাক্স) ও ইন্টারনেট কানেকশন।' }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <CheckIcon class="h-4 w-4 text-emerald-500 shrink-0" />
                                    <span>{{ locale === 'en' ? 'No prior programming or math expertise required — everything is taught from square one.' : 'কোনো পূর্ব কোডিং বা অ্যাডভান্সড গণিতের অভিজ্ঞতার প্রয়োজন নেই — সবকিছু শুরু থেকেই শেখানো হয়েছে।' }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <CheckIcon class="h-4 w-4 text-emerald-500 shrink-0" />
                                    <span>{{ locale === 'en' ? 'Dedication and curiosity to practice data analysis concepts along with the lectures.' : 'ভিডিও লেকচারের সাথে সাথে প্র্যাকটিস করার আন্তরিক ইচ্ছা ও আগ্রহ।' }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Full Course Detailed Description -->
                        <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                            <h3 class="font-heading text-lg sm:text-xl font-bold text-[var(--text)] mb-4">
                                {{ locale === 'en' ? 'About This Course' : 'কোর্সের বিস্তারিত বিবরণ' }}
                            </h3>
                            <div class="prose dark:prose-invert max-w-none text-xs sm:text-sm leading-relaxed text-[var(--text-muted)] whitespace-pre-line">
                                {{ course.description }}
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: CURRICULUM (Always Visible for All Delivery Types!) -->
                    <div v-show="activeTab === 'curriculum'" class="space-y-6 animate-fadeIn">
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-sm">
                            <div>
                                <h3 class="font-heading text-base sm:text-lg font-bold text-[var(--text)]">
                                    {{ locale === 'en' ? 'Course Modules & Lessons' : 'কোর্স কারিকুলাম ও লেকচারসমূহ' }}
                                </h3>
                                <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                    {{ (course.sections || []).length }} {{ locale === 'en' ? 'Modules' : 'টি মডিউল' }} · {{ totalLessonsCount }} {{ locale === 'en' ? 'Lessons' : 'টি লেকচার' }} · {{ totalDurationFormatted }}
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="toggleAllSections"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3.5 py-2 text-xs font-bold text-[var(--text)] hover:border-[var(--primary)] transition"
                            >
                                <ArrowPathIcon class="h-3.5 w-3.5" />
                                <span>{{ allExpanded ? (locale === 'en' ? 'Collapse All' : 'সবগুলো বন্ধ করুন') : (locale === 'en' ? 'Expand All' : 'সবগুলো খুলুন') }}</span>
                            </button>
                        </div>

                        <!-- Modules Accordion List -->
                        <div v-if="course.sections?.length" class="space-y-3.5">
                            <div
                                v-for="(section, idx) in course.sections"
                                :key="section.id"
                                class="overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] transition duration-200 hover:border-[var(--primary)]/30"
                            >
                                <!-- Section Header Accordion Trigger -->
                                <button
                                    type="button"
                                    @click="toggleSection(section.id)"
                                    class="flex w-full items-center justify-between gap-3 p-4 sm:px-5 sm:py-4 text-left transition hover:bg-[var(--surface2)]/50"
                                    :class="openSections[section.id] ? 'bg-[var(--surface2)]/40 border-b border-[var(--border)]' : ''"
                                >
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-[var(--primary)]/15 text-xs font-extrabold text-[var(--primary)]">
                                            {{ idx + 1 }}
                                        </span>
                                        <div class="min-w-0">
                                            <h4 class="font-heading text-sm font-bold text-[var(--text)] truncate">
                                                {{ section.title }}
                                            </h4>
                                            <span class="text-[11px] text-[var(--text-muted)]">
                                                {{ (section.lessons || []).length }} {{ locale === 'en' ? 'lessons' : 'টি লেকচার' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="text-xs text-[var(--text-muted)] hidden sm:inline">
                                            {{ openSections[section.id] ? (locale === 'en' ? 'Hide' : 'লুকান') : (locale === 'en' ? 'View' : 'দেখুন') }}
                                        </span>
                                        <ChevronDownIcon
                                            class="h-4 w-4 text-[var(--text-muted)] transition-transform duration-300"
                                            :class="openSections[section.id] ? 'rotate-180 text-[var(--primary)]' : ''"
                                        />
                                    </div>
                                </button>

                                <!-- Lessons in this section -->
                                <div v-show="openSections[section.id]" class="divide-y divide-[var(--border)] transition-all">
                                    <div
                                        v-for="(lesson, lIdx) in section.lessons"
                                        :key="lesson.id"
                                        class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6 sm:py-3.5 text-xs sm:text-sm transition hover:bg-[var(--surface2)]/30"
                                    >
                                        <div class="flex items-center gap-3 min-w-0">
                                            <PlayCircleIcon class="h-4 w-4 sm:h-5 sm:w-5 shrink-0 text-[var(--primary)]" />
                                            <span class="text-xs text-[var(--text-muted)] font-mono">{{ idx + 1 }}.{{ lIdx + 1 }}</span>
                                            <span class="font-medium text-[var(--text)] truncate">{{ lesson.title }}</span>
                                        </div>

                                        <div class="flex items-center gap-2.5 shrink-0">
                                            <!-- Free preview action button -->
                                            <Link
                                                v-if="lesson.is_free_preview"
                                                :href="`/courses/${course.slug}/sections/${section.id}/lessons/${lesson.id}`"
                                                class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 px-2.5 py-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500 hover:text-white transition"
                                            >
                                                <PlayCircleIcon class="h-3.5 w-3.5" />
                                                <span>{{ t('admin.courses.free_preview') }}</span>
                                            </Link>

                                            <span v-if="lesson.duration_minutes" class="text-xs text-[var(--text-muted)] font-mono">
                                                {{ lesson.duration_minutes }} min
                                            </span>

                                            <!-- Locked badge if not enrolled and not preview -->
                                            <span v-if="!isEnrolled && !lesson.is_free_preview" class="text-slate-400" :title="locale === 'en' ? 'Locked (Enroll to unlock)' : 'লক করা (এনরোল করলে খুলবে)'">
                                                <LockClosedIcon class="h-3.5 w-3.5" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="rounded-2xl border border-dashed border-[var(--border)] p-12 text-center text-sm text-[var(--text-muted)]">
                            <BookOpenIcon class="mx-auto h-8 w-8 opacity-40 mb-2" />
                            <p>{{ locale === 'en' ? 'Detailed curriculum outline is being prepared.' : 'কোর্স কারিকুলাম দ্রুত যুক্ত করা হচ্ছে।' }}</p>
                        </div>
                    </div>

                    <!-- TAB 3: MENTOR PROFILE -->
                    <div v-show="activeTab === 'mentor'" class="animate-fadeIn">
                        <div v-if="course.mentor" class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 border-b border-[var(--border)] pb-6">
                                <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-2xl ring-4 ring-orange-500/20 bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center font-bold text-2xl shadow-lg">
                                    <img
                                        v-if="course.mentor.photo"
                                        :src="`/storage/${course.mentor.photo}`"
                                        :alt="course.mentor.name"
                                        class="h-full w-full object-cover"
                                    />
                                    <span v-else>{{ course.mentor.name?.charAt(0) || 'M' }}</span>
                                </div>

                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-heading text-xl font-bold text-[var(--text)]">{{ course.mentor.name }}</h3>
                                        <CheckBadgeIcon class="h-5 w-5 text-sky-500" />
                                    </div>
                                    <p v-if="course.mentor.designation" class="text-sm font-semibold text-[var(--primary)] mt-0.5">
                                        {{ course.mentor.designation }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-4 text-xs text-[var(--text-muted)] mt-2">
                                        <span class="flex items-center gap-1">
                                            <StarSolidIcon class="h-4 w-4 text-amber-400" />
                                            <strong class="text-[var(--text)]">4.9</strong> Instructor Rating
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <UsersIcon class="h-4 w-4 text-indigo-400" />
                                            <strong class="text-[var(--text)]">3,500+</strong> Students
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <PlayCircleIcon class="h-4 w-4 text-emerald-400" />
                                            <strong class="text-[var(--text)]">5+</strong> Courses
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 space-y-4 text-xs sm:text-sm text-[var(--text-muted)] leading-relaxed">
                                <p v-if="course.mentor.bio" class="whitespace-pre-line">{{ course.mentor.bio }}</p>
                                <p v-else>
                                    {{ locale === 'en'
                                        ? 'Experienced educator and industry expert passionate about making data analytics, coding, and problem-solving accessible to students across Bangladesh.'
                                        : 'অভিজ্ঞ শিক্ষাবিদ ও ইন্ডাস্ট্রি এক্সপার্ট, যিনি দীর্ঘদিন ধরে শিক্ষার্থীদের ডেটা অ্যানালাইসিস, প্রোগ্রামিং ও ব্যবহারিক স্কিল উন্নয়নে দিকনির্দেশনা দিয়ে আসছেন।' }}
                                </p>

                                <!-- Mentor Expertise Pills -->
                                <div v-if="course.mentor.expertise?.length" class="pt-2">
                                    <span class="block text-xs font-bold uppercase tracking-wider text-[var(--text)] mb-2">
                                        {{ locale === 'en' ? 'Areas of Expertise:' : 'দক্ষতার ক্ষেত্রসমূহ:' }}
                                    </span>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="exp in course.mentor.expertise"
                                            :key="exp"
                                            class="rounded-lg bg-[var(--surface2)] px-3 py-1 text-xs font-semibold text-[var(--text)]"
                                        >
                                            {{ exp }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="rounded-2xl border border-[var(--border)] p-8 text-center text-sm text-[var(--text-muted)]">
                            <p>{{ locale === 'en' ? 'Instructor details will be added soon.' : 'মেন্টরের তথ্য শীঘ্রই যুক্ত হবে।' }}</p>
                        </div>
                    </div>

                    <!-- TAB 4: FAQ (FREQUENTLY ASKED QUESTIONS) -->
                    <div v-show="activeTab === 'faq'" class="space-y-3.5 animate-fadeIn">
                        <div
                            v-for="(faq, fIdx) in [
                                {
                                    q: locale === 'en' ? 'How do I access the course after enrollment?' : 'কোর্স কেনার পর কীভাবে কনটেন্ট অ্যাক্সেস করব?',
                                    a: locale === 'en' ? 'Immediately after successful enrollment or payment, your dashboard will unlock the full course link, downloadable files, and lectures. You will also receive an email with direct Google Drive access.' : 'পেমেন্ট সম্পন্ন হওয়ার সাথে সাথেই আপনার স্টুডেন্ট ড্যাশবোর্ডে কোর্সটি আনলক হয়ে যাবে এবং ড্রাইভ ডাউনলোড লিংক পাওয়া যাবে। একই সাথে আপনার ইমেইলেও অ্যাক্সেস লিংক পাঠানো হবে।'
                                },
                                {
                                    q: locale === 'en' ? 'Can I download the lectures and files for offline study?' : 'আমি কি ভিডিও ও ফাইল ডাউনলোড করে অফলাইনে দেখতে পারব?',
                                    a: locale === 'en' ? 'Yes! All HD videos, Jupyter Notebooks (.ipynb), datasets, and resource files can be downloaded directly to your computer or mobile to learn anytime without an internet connection.' : 'হ্যাঁ! সম্পূর্ণ কোর্সের সব ভিডিও লেকচার, জুপিটার নোটবুক (.ipynb), ডেটাসেট ও রিসোর্স ফাইল সরাসরি গুগল ড্রাইভ থেকে আপনার কম্পিউটার বা মোবাইলে আজীবনের জন্য ডাউনলোড করে রাখতে পারবেন।'
                                },
                                {
                                    q: locale === 'en' ? 'Do I need prior coding or math knowledge?' : 'আমার কি আগে থেকে কোনো প্রোগ্রামিং বা গণিতের অভিজ্ঞতা থাকতে হবে?',
                                    a: locale === 'en' ? 'No prior experience is necessary. We start from the absolute basics (variables, installation, data structures) and gradually progress to professional data analytics workflows.' : 'একদমই না! কোর্সটি সম্পূর্ণ জিরো লেভেল থেকে শুরু করা হয়েছে। সফটওয়্যার ইনস্টলেশন থেকে শুরু করে ধাপে ধাপে জটিল ডেটা ম্যানিপুলেশন পর্যন্ত খুব সহজ বাংলায় বোঝানো হয়েছে।'
                                },
                                {
                                    q: locale === 'en' ? 'Will I get a verified course certificate?' : 'কোর্স সম্পন্ন করলে কি সার্টিফিকেট পাওয়া যাবে?',
                                    a: locale === 'en' ? 'Yes, upon completing the course you will receive a verifiable digital Certificate of Completion from Sikhun.com that you can display on LinkedIn and include in your resume.' : 'হ্যাঁ, কোর্স সম্পন্ন করলে শিখুন (Sikhun.com) থেকে একটি ভেরিফায়েবল ডিজিটাল কমপ্লিশন সার্টিফিকেট দেওয়া হবে, যা আপনি আপনার সিভি বা লিঙ্কডইনে প্রদর্শন করতে পারবেন।'
                                },
                                {
                                    q: locale === 'en' ? 'How long is the course access valid?' : 'কোর্সের মেয়াদ কতদিন থাকবে?',
                                    a: locale === 'en' ? 'Lifetime Access! Once enrolled, the course materials, future updates, and resource downloads remain yours forever.' : 'আজীবন অ্যাক্সেস (Lifetime Access)! একবার এনরোল করলে আপনি যেকোনো সময় যতবার ইচ্ছা কোর্স কনটেন্ট দেখতে ও ডাউনলোড করতে পারবেন।'
                                }
                            ]"
                            :key="fIdx"
                            class="overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] transition duration-200"
                        >
                            <button
                                type="button"
                                @click="toggleFaq(fIdx)"
                                class="flex w-full items-center justify-between gap-3 p-4 sm:p-5 text-left transition hover:bg-[var(--surface2)]/50"
                            >
                                <span class="font-heading text-sm font-bold text-[var(--text)]">{{ faq.q }}</span>
                                <ChevronDownIcon
                                    class="h-4 w-4 shrink-0 text-[var(--text-muted)] transition-transform duration-300"
                                    :class="openFaq[fIdx] ? 'rotate-180 text-[var(--primary)]' : ''"
                                />
                            </button>
                            <div v-show="openFaq[fIdx]" class="border-t border-[var(--border)] bg-[var(--surface2)]/30 p-4 sm:p-5 text-xs sm:text-sm text-[var(--text-muted)] leading-relaxed">
                                {{ faq.a }}
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: REVIEWS & TESTIMONIALS -->
                    <div v-show="activeTab === 'reviews'" class="space-y-6 animate-fadeIn">
                        <!-- Rating Summary Box -->
                        <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                            <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-10">
                                <div class="text-center sm:text-left shrink-0">
                                    <div class="font-heading text-5xl font-extrabold text-[var(--text)]">4.9</div>
                                    <div class="flex text-amber-400 justify-center sm:justify-start mt-2">
                                        <StarSolidIcon v-for="s in 5" :key="s" class="h-4 w-4" />
                                    </div>
                                    <span class="text-xs text-[var(--text-muted)] mt-1 block">Course Rating · 128 Reviews</span>
                                </div>

                                <div class="w-full max-w-md space-y-2 text-xs">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 text-[var(--text-muted)] font-bold">5 star</span>
                                        <div class="h-2 flex-1 rounded-full bg-[var(--surface2)] overflow-hidden">
                                            <div class="h-full bg-amber-400 rounded-full w-[92%]"></div>
                                        </div>
                                        <span class="w-8 text-right font-medium text-[var(--text-muted)]">92%</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 text-[var(--text-muted)] font-bold">4 star</span>
                                        <div class="h-2 flex-1 rounded-full bg-[var(--surface2)] overflow-hidden">
                                            <div class="h-full bg-amber-400 rounded-full w-[6%]"></div>
                                        </div>
                                        <span class="w-8 text-right font-medium text-[var(--text-muted)]">6%</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 text-[var(--text-muted)] font-bold">3 star</span>
                                        <div class="h-2 flex-1 rounded-full bg-[var(--surface2)] overflow-hidden">
                                            <div class="h-full bg-amber-400 rounded-full w-[2%]"></div>
                                        </div>
                                        <span class="w-8 text-right font-medium text-[var(--text-muted)]">2%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review Cards -->
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 shadow-sm space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-9 w-9 rounded-full bg-indigo-500/15 text-indigo-500 flex items-center justify-center font-bold text-xs">
                                            TA
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-[var(--text)]">তানভীর আহমেদ</h4>
                                            <span class="text-[10px] text-emerald-500 flex items-center gap-0.5">
                                                <CheckBadgeIcon class="h-3 w-3" /> Verified Student
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex text-amber-400">
                                        <StarSolidIcon v-for="s in 5" :key="s" class="h-3.5 w-3.5" />
                                    </div>
                                </div>
                                <p class="text-xs text-[var(--text-muted)] leading-relaxed">
                                    “আমি এক্সেল ব্যবহার করতাম, কিন্তু এই কোর্সে পান্ডাস ও নামপাই শেখার পর কাজের গতি ১০ গুণ বেড়ে গেছে। জুপিটার নোটবুক ও ক্যাপস্টোন প্রজেক্টটি দারুণ ছিল!”
                                </p>
                            </div>

                            <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 shadow-sm space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-9 w-9 rounded-full bg-orange-500/15 text-orange-500 flex items-center justify-center font-bold text-xs">
                                            NJ
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-[var(--text)]">নুসরাত জাহান</h4>
                                            <span class="text-[10px] text-emerald-500 flex items-center gap-0.5">
                                                <CheckBadgeIcon class="h-3 w-3" /> Verified Student
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex text-amber-400">
                                        <StarSolidIcon v-for="s in 5" :key="s" class="h-3.5 w-3.5" />
                                    </div>
                                </div>
                                <p class="text-xs text-[var(--text-muted)] leading-relaxed">
                                    “বাংলা ভাষায় এত গুছানো ও প্রফেশনাল ডেটা অ্যানালাইসিস কোর্স আগে দেখিনি। মাত্র ৯৯ টাকায় এত রিসোর্স এবং ড্রাইভ ডাউনলোড পাওয়া অকল্পনীয়। অনেক ধন্যবাদ Sikhun-কে!”
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (5/4 cols): Sticky Checkout & Course Action Card -->
                <div class="lg:col-span-5 xl:col-span-4" id="checkout-card">
                    <div class="sticky top-20 overflow-hidden rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-2xl transition-all">
                        <!-- Preview Thumbnail / Cover with Free Preview Button Overlay -->
                        <div class="group relative aspect-video overflow-hidden bg-[var(--surface2)]">
                            <img
                                v-if="course.cover_image_url"
                                :src="course.cover_image_url"
                                :alt="course.title"
                                width="480"
                                height="270"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-indigo-950 text-slate-400">
                                <AcademicCapIcon class="h-16 w-16 opacity-40" />
                            </div>

                            <!-- Floating Free Preview Badge / Trigger -->
                            <Link
                                v-if="firstPreviewLessonUrl"
                                :href="firstPreviewLessonUrl"
                                class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-90 sm:opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                            >
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/95 px-4 py-2 text-xs font-bold text-slate-900 shadow-xl backdrop-blur transition hover:scale-105">
                                    <PlayCircleIcon class="h-4 w-4 text-[var(--primary)]" />
                                    <span>{{ locale === 'en' ? 'Preview this course' : 'ফ্রি প্রিভিউ দেখুন' }}</span>
                                </span>
                            </Link>
                        </div>

                        <!-- Checkout & Plan Information Card Content -->
                        <div class="p-6 sm:p-7">
                            <!-- Price Header Section -->
                            <div class="border-b border-[var(--border)] pb-5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">
                                        {{ locale === 'en' ? 'Course Enrollment Fee' : 'কোর্স এনরোলমেন্ট ফি' }}
                                    </span>
                                    <span v-if="+course.price === 0" class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-bold text-emerald-500">
                                        {{ t('common.free') }}
                                    </span>
                                    <span v-else class="rounded-full bg-rose-500/15 border border-rose-500/30 px-2.5 py-0.5 text-[11px] font-bold text-rose-500 animate-pulse">
                                        {{ locale === 'en' ? 'Limited Discount' : '৯০% বিশেষ ছাড়' }}
                                    </span>
                                </div>

                                <div class="mt-2 flex items-baseline gap-2.5">
                                    <div class="font-heading text-3xl sm:text-4xl font-extrabold text-[var(--text)]">
                                        <span v-if="+course.price === 0" class="text-emerald-500">{{ t('common.free') }}</span>
                                        <span v-else>৳{{ money(course.price) }}</span>
                                    </div>
                                    <span v-if="+course.price > 0" class="text-sm text-[var(--text-muted)] line-through">
                                        ৳{{ money(regularPrice) }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-[var(--text-muted)] mt-1.5 flex items-center gap-1">
                                    <CheckCircleSolidIcon class="h-3.5 w-3.5 text-emerald-500" />
                                    <span>{{ locale === 'en' ? 'One-time fee • Lifetime updates included' : 'একবার পেমেন্ট • আজীবন অ্যাক্সেস ও আপডেট' }}</span>
                                </p>
                            </div>

                            <!-- CASE 1: ALREADY ENROLLED -->
                            <div v-if="isEnrolled" class="mt-6 space-y-4">
                                <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs font-bold text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                                    <CheckBadgeIcon class="h-5 w-5 text-emerald-500 shrink-0" />
                                    <span>{{ locale === 'en' ? 'You already have active access to this course!' : 'আপনার এই কোর্সে পূর্ণ অ্যাক্সেস রয়েছে!' }}</span>
                                </div>

                                <!-- If link or file download -->
                                <a
                                    v-if="course.external_link"
                                    :href="course.external_link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 transition hover:scale-[1.02] active:scale-[0.98]"
                                >
                                    <ArrowDownTrayIcon v-if="course.delivery_type === 'file_download'" class="h-5 w-5" />
                                    <ArrowTopRightOnSquareIcon v-else class="h-5 w-5" />
                                    <span>{{ course.delivery_type === 'file_download' ? t('courses_page.download_files') : t('courses_page.open_link') }}</span>
                                </a>

                                <Link
                                    v-if="firstAccessibleLessonUrl"
                                    :href="firstAccessibleLessonUrl"
                                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/25 transition hover:bg-[var(--primary-hover)] hover:scale-[1.02] active:scale-[0.98]"
                                >
                                    <PlayCircleIcon class="h-5 w-5" />
                                    <span>{{ locale === 'en' ? 'Continue Learning' : 'ক্লাস চালিয়ে যান' }}</span>
                                </Link>
                            </div>

                            <!-- CASE 2: LOGGED-IN CHECKOUT -->
                            <div v-else-if="isLoggedIn" class="mt-6 space-y-3.5">
                                <button
                                    v-if="+course.price === 0"
                                    type="button"
                                    @click="enrollAuthenticated('wallet')"
                                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[var(--primary)] px-6 py-4 text-sm font-bold text-white shadow-xl shadow-orange-500/25 transition hover:bg-[var(--primary-hover)] hover:scale-[1.02]"
                                >
                                    <CheckBadgeIcon class="h-5 w-5" />
                                    <span>{{ t('courses_page.enroll_free') }}</span>
                                </button>

                                <template v-else>
                                    <button
                                        type="button"
                                        @click="enrollAuthenticated('mhpay')"
                                        class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-rose-600 via-pink-600 to-amber-600 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-pink-500/25 transition hover:opacity-95 hover:scale-[1.02] active:scale-[0.98]"
                                    >
                                        <CreditCardIcon class="h-5 w-5" />
                                        <span>{{ locale === 'en' ? 'Pay with MFS (bKash/Nagad)' : 'বিকাশ / নগদ / রকেটে পেমেন্ট করুন' }} (৳{{ money(course.price) }})</span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="enrollAuthenticated('wallet')"
                                        class="flex w-full items-center justify-center gap-2 rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/80 px-6 py-3 text-xs font-bold text-[var(--text)] transition hover:border-[var(--primary)] hover:bg-[var(--surface2)]"
                                    >
                                        <WalletIcon class="h-4 w-4 text-[var(--secondary)]" />
                                        <span>{{ t('book_show.pay_with_wallet') }} ({{ locale === 'en' ? 'Balance' : 'ব্যালেন্স' }}: ৳{{ money(walletBalance) }})</span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="enrollAuthenticated('zinipay')"
                                        class="flex w-full items-center justify-center gap-1.5 text-xs text-[var(--text-muted)] hover:text-[var(--text)] transition pt-1"
                                    >
                                        <span>{{ locale === 'en' ? 'Use Alternative Gateway (ZiniPay) →' : 'বিকল্প পেমেন্ট গেটওয়ে (ZiniPay) →' }}</span>
                                    </button>
                                </template>
                            </div>

                            <!-- CASE 3: GUEST CHECKOUT VS ACCOUNT LOGIN -->
                            <div v-else class="mt-6">
                                <!-- Mode Switch Tabs -->
                                <div class="grid grid-cols-2 gap-1 rounded-2xl border border-[var(--border)] bg-[var(--surface2)] p-1 text-xs font-bold">
                                    <button
                                        type="button"
                                        @click="checkoutTab = 'guest'"
                                        class="rounded-xl py-2 transition"
                                        :class="checkoutTab === 'guest'
                                            ? 'bg-[var(--primary)] text-white shadow'
                                            : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                                    >
                                        {{ locale === 'en' ? 'Guest Checkout' : 'গেস্ট চেকআউট' }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="checkoutTab = 'auth'"
                                        class="rounded-xl py-2 transition"
                                        :class="checkoutTab === 'auth'
                                            ? 'bg-[var(--primary)] text-white shadow'
                                            : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                                    >
                                        {{ locale === 'en' ? 'Account Login' : 'লগইন চেকআউট' }}
                                    </button>
                                </div>

                                <!-- TAB A: GUEST CHECKOUT FORM -->
                                <form v-if="checkoutTab === 'guest'" @submit.prevent="submitGuestCheckout" class="mt-5 space-y-3.5">
                                    <p class="text-[11px] leading-relaxed text-[var(--text-muted)]">
                                        {{ locale === 'en'
                                            ? 'No account needed! Enter your details to get instant course access and an automatic email receipt.'
                                            : 'লগইন ছাড়াই সহজে সরাসরি এনরোল করুন। পেমেন্ট সম্পন্ন হলে ইমেইলে ড্রাইভ লিংক ও অ্যাক্সেস পেয়ে যাবেন।' }}
                                    </p>

                                    <div>
                                        <label class="mb-1 block text-xs font-bold text-[var(--text)]">{{ locale === 'en' ? 'Full Name *' : 'আপনার নাম *' }}</label>
                                        <div class="relative">
                                            <UserIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                                            <input
                                                v-model="guestForm.name"
                                                type="text"
                                                required
                                                :placeholder="locale === 'en' ? 'e.g. Tanvir Ahmed' : 'আপনার পূর্ণ নাম লিখুন'"
                                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 py-2.5 pl-9 pr-3 text-xs sm:text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                                            />
                                        </div>
                                        <p v-if="guestForm.errors.name" class="mt-1 text-xs text-rose-500">{{ guestForm.errors.name }}</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-bold text-[var(--text)]">{{ locale === 'en' ? 'Email Address *' : 'ইমেইল ঠিকানা *' }}</label>
                                        <div class="relative">
                                            <EnvelopeIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                                            <input
                                                v-model="guestForm.email"
                                                type="email"
                                                required
                                                :placeholder="locale === 'en' ? 'you@example.com' : 'আপনার সচল ইমেইল লিখুন'"
                                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 py-2.5 pl-9 pr-3 text-xs sm:text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                                            />
                                        </div>
                                        <p v-if="guestForm.errors.email" class="mt-1 text-xs text-rose-500">{{ guestForm.errors.email }}</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-bold text-[var(--text)]">{{ locale === 'en' ? 'Phone Number (Optional)' : 'মোবাইল নম্বর (ঐচ্ছিক)' }}</label>
                                        <div class="relative">
                                            <PhoneIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                                            <input
                                                v-model="guestForm.phone"
                                                type="tel"
                                                placeholder="01XXXXXXXXX"
                                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 py-2.5 pl-9 pr-3 text-xs sm:text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                                            />
                                        </div>
                                    </div>

                                    <button
                                        type="submit"
                                        :disabled="guestForm.processing"
                                        class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 py-3.5 text-sm font-bold text-white shadow-xl shadow-orange-500/25 transition hover:scale-[1.02] active:scale-[0.98] disabled:opacity-60"
                                    >
                                        <CreditCardIcon v-if="+course.price > 0" class="h-5 w-5" />
                                        <CheckBadgeIcon v-else class="h-5 w-5" />
                                        <span>
                                            {{ +course.price === 0
                                                ? (locale === 'en' ? 'Instant Free Enroll' : 'ফ্রি এনরোল করুন')
                                                : (locale === 'en' ? `Proceed to Payment (৳${money(course.price)})` : `পেমেন্ট করুন (৳${money(course.price)})`) }}
                                        </span>
                                    </button>
                                </form>

                                <!-- TAB B: ACCOUNT CHECKOUT -->
                                <div v-else class="mt-5 space-y-3">
                                    <p class="text-[11px] leading-relaxed text-[var(--text-muted)]">
                                        {{ locale === 'en'
                                            ? 'Sign in to use your wallet balance and keep track of your courses in one clean student dashboard.'
                                            : 'আপনার অ্যাকাউন্টে লগইন করে ওয়ালেট ব্যালেন্স বা অনলাইন পেমেন্টের মাধ্যমে এক ক্লিকে কোর্স এনরোল করুন।' }}
                                    </p>

                                    <Link
                                        :href="loginRedirectUrl"
                                        class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/25 transition hover:bg-[var(--primary-hover)] hover:scale-[1.02]"
                                    >
                                        <span>{{ locale === 'en' ? 'Sign In & Enroll' : 'লগইন করে এনরোল করুন' }}</span>
                                    </Link>

                                    <Link
                                        :href="registerRedirectUrl"
                                        class="flex w-full items-center justify-center gap-2 rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/60 px-6 py-3 text-xs font-bold text-[var(--text)] transition hover:border-[var(--primary)]"
                                    >
                                        <span>{{ locale === 'en' ? 'Create New Account' : 'নতুন অ্যাকাউন্ট তৈরি করুন' }}</span>
                                    </Link>
                                </div>
                            </div>

                            <!-- Course Value Propositions Checklist -->
                            <div class="mt-6 border-t border-[var(--border)] pt-5 space-y-2.5 text-xs text-[var(--text-muted)]">
                                <div class="font-bold uppercase tracking-wider text-[var(--text)] text-[11px] mb-2">
                                    {{ locale === 'en' ? 'This course includes:' : 'এই কোর্সে যা যা থাকছে:' }}
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <ClockIcon class="h-4 w-4 text-indigo-500 shrink-0" />
                                    <span>{{ totalDurationFormatted }} {{ locale === 'en' ? 'on-demand content' : 'সম্পূর্ণ লেকচার' }}</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <DocumentTextIcon class="h-4 w-4 text-emerald-500 shrink-0" />
                                    <span>{{ locale === 'en' ? 'Jupyter Notebooks, Source Code & Datasets' : 'জুপিটার নোটবুক, সোর্স কোড ও প্রজেক্ট ফাইল' }}</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <ComputerDesktopIcon class="h-4 w-4 text-sky-500 shrink-0" />
                                    <span>{{ locale === 'en' ? 'Access on Mobile, Tablet & PC' : 'মোবাইল, ট্যাবলেট ও পিসিতে লাইফটাইম অ্যাক্সেস' }}</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <CheckBadgeIcon class="h-4 w-4 text-amber-500 shrink-0" />
                                    <span>{{ locale === 'en' ? 'Verifiable Certificate of Completion' : 'ভেরিফায়েবল ডিজিটাল কোর্স সার্টিফিকেট' }}</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <ShieldCheckIcon class="h-4 w-4 text-emerald-500 shrink-0" />
                                    <span>{{ locale === 'en' ? '100% Secure Checkout Guarantee' : '১০০% নিরাপদ পেমেন্ট ও ইনস্ট্যান্ট অ্যাক্সেস' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RELATED COURSES SECTION -->
            <div v-if="relatedCourses.length" class="mt-16 sm:mt-20 border-t border-[var(--border)] pt-12">
                <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--primary)]">
                            {{ locale === 'en' ? 'RECOMMENDED' : 'সম্পর্কিত কোর্স' }}
                        </span>
                        <h2 class="mt-1 font-heading text-xl sm:text-2xl font-extrabold text-[var(--text)]">
                            {{ locale === 'en' ? 'More Courses You Might Like' : 'আপনার পছন্দ হতে পারে এমন আরও কোর্স' }}
                        </h2>
                    </div>
                    <Link href="/courses" class="text-xs sm:text-sm font-bold text-[var(--primary)] hover:underline inline-flex items-center gap-1">
                        <span>{{ locale === 'en' ? 'Explore All Courses' : 'সব কোর্স দেখুন' }}</span>
                        <ChevronRightIcon class="h-4 w-4" />
                    </Link>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="rel in relatedCourses"
                        :key="rel.id"
                        :href="`/courses/${rel.slug}`"
                        class="group flex flex-col justify-between overflow-hidden rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-sm transition duration-300 hover:-translate-y-1.5 hover:border-[var(--primary)]/50 hover:shadow-xl"
                    >
                        <div>
                            <div class="relative aspect-video overflow-hidden bg-[var(--surface2)]">
                                <img
                                    v-if="rel.cover_image_url"
                                    :src="rel.cover_image_url"
                                    :alt="rel.title"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                />
                                <div v-else class="flex h-full w-full items-center justify-center text-[var(--text-muted)] bg-gradient-to-br from-slate-800 to-indigo-950">
                                    <AcademicCapIcon class="h-10 w-10 opacity-30 text-white" />
                                </div>
                            </div>
                            <div class="p-5">
                                <span v-if="rel.category" class="inline-block text-[11px] font-bold text-[var(--primary)] mb-1">
                                    {{ rel.category.name }}
                                </span>
                                <h3 class="line-clamp-2 font-heading text-sm sm:text-base font-bold text-[var(--text)] group-hover:text-[var(--primary)] transition">
                                    {{ rel.title }}
                                </h3>
                                <p v-if="rel.mentor?.name" class="mt-1 text-xs text-[var(--text-muted)]">
                                    {{ rel.mentor.name }}
                                </p>
                            </div>
                        </div>

                        <div class="border-t border-[var(--border)] p-5 pt-3.5 flex items-center justify-between">
                            <span class="font-heading font-extrabold text-base" :class="+rel.price === 0 ? 'text-emerald-500' : 'text-[var(--text)]'">
                                {{ +rel.price === 0 ? t('common.free') : `৳${money(rel.price)}` }}
                            </span>
                            <span class="text-xs text-[var(--text-muted)] flex items-center gap-1">
                                <UsersIcon class="h-3.5 w-3.5 text-orange-400" />
                                <span>{{ rel.enrollments_count ?? 0 }}</span>
                            </span>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- STICKY MOBILE BOTTOM ENROLL BAR -->
        <div
            v-if="!isEnrolled"
            class="fixed bottom-0 inset-x-0 z-40 lg:hidden border-t border-[var(--border)] bg-[var(--surface)]/95 px-4 py-3 backdrop-blur-xl shadow-2xl"
        >
            <div class="flex items-center justify-between gap-4">
                <div>
                    <span class="text-[10px] text-[var(--text-muted)] uppercase font-bold">{{ locale === 'en' ? 'Course Price' : 'কোর্স মূল্য' }}</span>
                    <div class="flex items-baseline gap-1.5 font-heading text-xl font-extrabold">
                        <span v-if="+course.price === 0" class="text-emerald-500">{{ t('common.free') }}</span>
                        <span v-else class="text-[var(--primary)]">৳{{ money(course.price) }}</span>
                        <span v-if="+course.price > 0" class="text-xs text-[var(--text-muted)] line-through">৳{{ money(regularPrice) }}</span>
                    </div>
                </div>

                <button
                    type="button"
                    @click="scrollToCheckout"
                    class="rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-orange-500/25 active:scale-95"
                >
                    {{ +course.price === 0 ? (locale === 'en' ? 'Enroll Free' : 'ফ্রি এনরোল করুন') : (locale === 'en' ? 'Enroll Now' : 'এখনই ভর্তি হন') }}
                </button>
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fadeIn {
    animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
