<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import ShareActions from '@/Components/ShareActions.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    notice: Object,
    relatedNotices: Array,
    practiceBridge: Object,
    sampleQuestions: Array,
    seo: Object,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');
const imageModalOpen = ref(false);

const timer = ref({ days: '00', hours: '00', minutes: '00', seconds: '00' });
const isExpired = ref(false);
let timerInterval = null;

// Practice Bridge & Mini-Test State
const activeQIndex = ref(0);
const userAnswers = ref({});
const isTestSubmitted = ref(false);
const testElapsedSeconds = ref(0);
let testTimerInterval = null;

const quizQuestions = computed(() => {
    if (props.sampleQuestions && props.sampleQuestions.length > 0) {
        return props.sampleQuestions;
    }
    return [
        {
            id: 1,
            question_text: 'কোন বানানটি শুদ্ধ?',
            options: [
                { key: 'A', text: 'মুমূর্ষু' },
                { key: 'B', text: 'মুমুর্য্য' },
                { key: 'C', text: 'মুমুর্ষু' },
                { key: 'D', text: 'মুর্মূষু' },
            ],
            correct_option: 'A',
            explanation: 'মুমূর্ষু বানানে প্রথমটি হ্রস্ব-উ, দ্বিতীয়টি দীর্ঘ-ঊ এবং শেষেরটি হ্রস্ব-উ কার।',
            origin_tag: 'DU-A',
        },
        {
            id: 2,
            question_text: 'Which one is the correct antonym of "Prolific"?',
            options: [
                { key: 'A', text: 'Fertile' },
                { key: 'B', text: 'Productive' },
                { key: 'C', text: 'Barren' },
                { key: 'D', text: 'Abundant' },
            ],
            correct_option: 'C',
            explanation: 'Prolific অর্থ ফলপ্রসূ বা উর্বর; এর বিপরীত শব্দ Barren (অনুর্বর)।',
            origin_tag: 'DU-B',
        },
        {
            id: 3,
            question_text: 'একটি প্রক্ষেপকের সর্বোচ্চ পাল্লার জন্য নিক্ষেপণ কোণ কত হতে হবে?',
            options: [
                { key: 'A', text: '30°' },
                { key: 'B', text: '45°' },
                { key: 'C', text: '60°' },
                { key: 'D', text: '90°' },
            ],
            correct_option: 'B',
            explanation: 'প্রক্ষেপকের আনুভূমিক পাল্লা R = (v₀² sin 2θ)/g; সর্বোচ্চ মান হয় θ = 45° কোণে।',
            origin_tag: 'RU-C',
        },
    ];
});

const currentQ = computed(() => quizQuestions.value[activeQIndex.value] || null);

function selectOption(key) {
    if (isTestSubmitted.value) return;
    const qId = currentQ.value?.id;
    if (qId) {
        userAnswers.value[qId] = key;
    }
}

function nextQuestion() {
    if (activeQIndex.value < quizQuestions.value.length - 1) {
        activeQIndex.value++;
    }
}

function prevQuestion() {
    if (activeQIndex.value > 0) {
        activeQIndex.value--;
    }
}

function submitTest() {
    isTestSubmitted.value = true;
    if (testTimerInterval) clearInterval(testTimerInterval);

    try {
        const payload = {
            score: testScore.value.netScore,
            correct: testScore.value.correct,
            total: quizQuestions.value.length,
            institution: props.notice.institution_name,
            timestamp: new Date().toISOString(),
        };
        localStorage.setItem('sikhun_guest_quiz_result', JSON.stringify(payload));
    } catch (_) {}
}

function resetTest() {
    userAnswers.value = {};
    isTestSubmitted.value = false;
    activeQIndex.value = 0;
    testElapsedSeconds.value = 0;
    startTestTimer();
}

function startTestTimer() {
    if (testTimerInterval) clearInterval(testTimerInterval);
    testTimerInterval = setInterval(() => {
        if (!isTestSubmitted.value) {
            testElapsedSeconds.value++;
        }
    }, 1000);
}

const formattedTestTime = computed(() => {
    const mins = Math.floor(testElapsedSeconds.value / 60).toString().padStart(2, '0');
    const secs = (testElapsedSeconds.value % 60).toString().padStart(2, '0');
    return `${mins}:${secs}`;
});

const testScore = computed(() => {
    let correct = 0;
    let incorrect = 0;
    const total = quizQuestions.value.length;

    quizQuestions.value.forEach((q) => {
        const ans = userAnswers.value[q.id];
        if (ans) {
            if (ans.toUpperCase() === (q.correct_option || '').toUpperCase()) {
                correct++;
            } else {
                incorrect++;
            }
        }
    });

    const unanswered = total - (correct + incorrect);
    const netScore = Math.max(0, +(correct - (incorrect * 0.25)).toFixed(2));
    const accuracy = correct + incorrect > 0 ? Math.round((correct / (correct + incorrect)) * 100) : 0;

    return {
        total,
        correct,
        incorrect,
        unanswered,
        netScore,
        accuracy,
    };
});

const registerCtaUrl = computed(() => {
    const s = testScore.value.netScore;
    const uni = encodeURIComponent(props.notice.institution_name || '');
    return `/register?ref=admission_quiz&score=${s}&uni=${uni}`;
});

const targetDate = computed(() => {
    if (props.notice.target_countdown_iso) {
        return new Date(props.notice.target_countdown_iso);
    }
    if (props.notice.application_deadline) {
        return new Date(props.notice.application_deadline);
    }
    return null;
});

const countdownTitle = computed(() => {
    if (props.notice.application_status === 'upcoming') {
        return en.value ? 'Application Starting In' : 'আবেদন শুরু হতে বাকি';
    }
    if (isExpired.value || props.notice.application_status === 'closed') {
        return en.value ? 'Application Closed' : 'আবেদনের সময়সীমা শেষ';
    }
    return en.value ? 'Application Deadline In' : 'আবেদনের শেষ সময় বাকি';
});

const countdownSubTitle = computed(() => {
    if (props.notice.application_status === 'upcoming') {
        return 'Application Starting In';
    }
    return 'Application Deadline Countdown';
});

function updateTimer() {
    if (!targetDate.value) return;
    const now = new Date().getTime();
    const distance = targetDate.value.getTime() - now;

    if (distance <= 0) {
        timer.value = { days: '00', hours: '00', minutes: '00', seconds: '00' };
        if (props.notice.application_status === 'open') {
            isExpired.value = true;
        }
        return;
    }

    const d = Math.floor(distance / (1000 * 60 * 60 * 24));
    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const s = Math.floor((distance % (1000 * 60)) / 1000);

    timer.value = {
        days: d.toString().padStart(2, '0'),
        hours: h.toString().padStart(2, '0'),
        minutes: m.toString().padStart(2, '0'),
        seconds: s.toString().padStart(2, '0'),
    };
}

onMounted(() => {
    updateTimer();
    timerInterval = setInterval(updateTimer, 1000);
    startTestTimer();
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
    if (testTimerInterval) clearInterval(testTimerInterval);
});

function formatDate(dateStr) {
    if (!dateStr) return en.value ? 'To be announced' : 'ঘোষণা সাপেক্ষে';
    const d = new Date(dateStr);
    return d.toLocaleDateString(en.value ? 'en-US' : 'bn-BD', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function formatDateTimeBn(dt) {
    if (!dt) return en.value ? 'TBA' : 'ঘোষণা সাপেক্ষে';
    const d = new Date(dt);
    return d.toLocaleString(en.value ? 'en-US' : 'bn-BD', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

const ogImage = computed(() => {
    if (props.notice.notice_image_url) {
        return props.notice.notice_image_url.startsWith('http')
            ? props.notice.notice_image_url
            : `https://sikhun.com${props.notice.notice_image_url.startsWith('/') ? '' : '/'}${props.notice.notice_image_url}`;
    }
    return `https://sikhun.com/storage/social-cards/admission-card-${props.notice.id}.png`;
});

const typeLabel = computed(() => {
    switch (props.notice.university_type) {
        case 'public': return en.value ? 'Public University' : 'পাবলিক বিশ্ববিদ্যালয়';
        case 'guccho': return en.value ? 'Guccho Cluster' : 'গুচ্ছ বিশ্ববিদ্যালয়';
        case 'private': return en.value ? 'Private University' : 'বেসরকারি বিশ্ববিদ্যালয়';
        default: return en.value ? 'University' : 'বিশ্ববিদ্যালয়';
    }
});
</script>

<template>
    <SeoHead v-if="seo" :seo="seo" />
    <PublicLayout>
        <Head v-if="!seo">
            <title>{{ notice.title }} | Sikhun Admission Portal</title>
            <meta name="description" :content="notice.meta_description || notice.title" />
            <meta property="og:title" :content="notice.title" />
            <meta property="og:description" :content="notice.meta_description || notice.title" />
            <meta property="og:image" :content="ogImage" />
            <meta property="og:type" content="article" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="notice.title" />
            <meta name="twitter:description" :content="notice.meta_description || notice.title" />
            <meta name="twitter:image" :content="ogImage" />
        </Head>

        <div class="min-h-screen bg-[var(--bg)] text-[var(--text)] pb-20">
            <!-- Breadcrumbs -->
            <div class="border-b border-gray-200 dark:border-white/5 bg-white/50 dark:bg-[#121620]/50 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto px-4 md:px-6 py-3 flex items-center gap-2 text-xs text-[var(--text-muted)]">
                    <Link href="/" class="hover:text-orange-500 transition">{{ en ? 'Home' : 'হোম' }}</Link>
                    <span>/</span>
                    <Link href="/admission-notice" class="hover:text-orange-500 transition">{{ en ? 'Admission Notices' : 'ভর্তি বিজ্ঞপ্তি' }}</Link>
                    <span>/</span>
                    <span class="truncate max-w-xs text-gray-900 dark:text-white font-medium">{{ notice.institution_name }}</span>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 md:px-6 py-8 md:py-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Main Article Column (8 cols) -->
                    <main class="lg:col-span-8">
                        <article class="bg-white dark:bg-[#171c28] rounded-3xl border border-gray-200 dark:border-white/5 p-6 md:p-10 shadow-sm dark:shadow-none">
                            <!-- Category Badge & Year -->
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20">
                                    {{ typeLabel }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300">
                                    {{ en ? 'Session:' : 'শিক্ষাবর্ষ:' }} {{ notice.academic_year }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-auto flex items-center gap-1">
                                    <span class="material-symbols-outlined !text-xs">visibility</span>
                                    <span>{{ notice.views_count }} {{ en ? 'views' : 'বার দেখা হয়েছে' }}</span>
                                </span>
                            </div>

                            <!-- Institution & Title -->
                            <div class="mb-6">
                                <span class="text-sm font-bold text-orange-500 dark:text-orange-400 block mb-1">
                                    {{ notice.institution_name }}
                                </span>
                                <h1 class="text-2xl md:text-4xl font-extrabold font-heading text-gray-900 dark:text-white leading-tight">
                                    {{ notice.title }}
                                </h1>
                                <div class="mt-4"><ShareActions :title="notice.title" :description="notice.institution_name || ''" :en="en" /></div>
                            </div>

                            <!-- Real-Time Countdown Hero Banner (Matching official portal admission.eis.du.ac.bd) -->
                            <div class="my-6 rounded-3xl overflow-hidden border border-blue-200 dark:border-blue-500/25 bg-gradient-to-br from-blue-50/70 via-white to-slate-50 dark:from-[#0c1938] dark:via-[#101e42] dark:to-[#0c1630] text-slate-900 dark:text-white p-6 md:p-8 shadow-sm dark:shadow-xl relative">
                                <div class="pointer-events-none absolute -top-20 left-1/2 -translate-x-1/2 w-80 h-80 bg-blue-500/15 rounded-full blur-3xl"></div>

                                <div class="relative z-10 text-center max-w-xl mx-auto">
                                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 dark:bg-blue-500/20 border border-blue-500/20 dark:border-blue-400/30 text-blue-600 dark:text-blue-300 text-xs font-bold mb-2">
                                        <span class="material-symbols-outlined !text-sm">hourglass_top</span>
                                        <span>{{ countdownTitle }}</span>
                                    </div>

                                    <div class="text-[11px] font-semibold text-slate-500 dark:text-blue-200/80 uppercase tracking-widest mb-3">
                                        {{ countdownSubTitle }}
                                    </div>

                                    <!-- 4-Block Live Countdown Timer (দিন : ঘণ্টা : মিনিট : সেকেন্ড) -->
                                    <div v-if="!isExpired" class="flex items-center justify-center gap-2 sm:gap-4 my-4 font-mono">
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-2xl bg-white dark:bg-[#172554]/95 border border-blue-200 dark:border-blue-400/40 flex items-center justify-center text-xl sm:text-3xl font-black text-slate-900 dark:text-white shadow-xs dark:shadow-inner">
                                                {{ timer.days }}
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-slate-600 dark:text-blue-300 mt-1.5">{{ en ? 'Days' : 'দিন' }}</span>
                                        </div>
                                        <span class="text-xl sm:text-3xl font-bold text-blue-500 dark:text-blue-400 -mt-5">:</span>
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-2xl bg-white dark:bg-[#172554]/95 border border-blue-200 dark:border-blue-400/40 flex items-center justify-center text-xl sm:text-3xl font-black text-slate-900 dark:text-white shadow-xs dark:shadow-inner">
                                                {{ timer.hours }}
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-slate-600 dark:text-blue-300 mt-1.5">{{ en ? 'Hours' : 'ঘণ্টা' }}</span>
                                        </div>
                                        <span class="text-xl sm:text-3xl font-bold text-blue-500 dark:text-blue-400 -mt-5">:</span>
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-2xl bg-white dark:bg-[#172554]/95 border border-blue-200 dark:border-blue-400/40 flex items-center justify-center text-xl sm:text-3xl font-black text-slate-900 dark:text-white shadow-xs dark:shadow-inner">
                                                {{ timer.minutes }}
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-slate-600 dark:text-blue-300 mt-1.5">{{ en ? 'Minutes' : 'মিনিট' }}</span>
                                        </div>
                                        <span class="text-xl sm:text-3xl font-bold text-blue-500 dark:text-blue-400 -mt-5">:</span>
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-2xl bg-white dark:bg-[#172554]/95 border border-blue-200 dark:border-blue-400/40 flex items-center justify-center text-xl sm:text-3xl font-black text-slate-900 dark:text-white shadow-xs dark:shadow-inner">
                                                {{ timer.seconds }}
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-slate-600 dark:text-blue-300 mt-1.5">{{ en ? 'Seconds' : 'সেকেন্ড' }}</span>
                                        </div>
                                    </div>
                                    <div v-else class="my-3 py-2 px-4 rounded-xl bg-red-500/20 border border-red-500/40 text-red-300 font-bold text-xs inline-block">
                                        {{ en ? 'Application Deadline Has Passed' : 'এই সার্কুলারের আবেদনের সময়সীমা শেষ হয়েছে' }}
                                    </div>

                                    <!-- 2 Official Date Boxes (Matching Image 1) -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6 pt-5 border-t border-blue-500/20 text-left">
                                        <div class="p-3.5 rounded-2xl bg-white dark:bg-[#142145]/90 border border-blue-200 dark:border-blue-400/25 shadow-xs">
                                            <span class="text-[11px] font-bold text-blue-600 dark:text-blue-300 uppercase block mb-1">
                                                {{ en ? 'Application Starting' : 'আবেদন শুরু' }}
                                            </span>
                                            <div class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">
                                                {{ notice.application_start_text || formatDateTimeBn(notice.application_start) }}
                                            </div>
                                        </div>
                                        <div class="p-3.5 rounded-2xl bg-white dark:bg-[#142145]/90 border border-blue-200 dark:border-blue-400/25 shadow-xs">
                                            <span class="text-[11px] font-bold text-blue-600 dark:text-blue-300 uppercase block mb-1">
                                                {{ en ? 'Application Deadline' : 'শেষ তারিখ' }}
                                            </span>
                                            <div class="text-xs sm:text-sm font-extrabold text-red-400">
                                                {{ notice.application_deadline_text || formatDateTimeBn(notice.application_deadline) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Key Overview Summary Box -->
                            <div class="my-6 p-6 rounded-2xl bg-gradient-to-br from-orange-500/5 to-amber-500/5 border border-orange-500/20 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block mb-1">{{ en ? 'Application Deadline' : 'আবেদনের শেষ তারিখ' }}</span>
                                    <span class="font-bold text-red-500 text-sm flex items-center gap-1">
                                        <span class="material-symbols-outlined !text-base">alarm</span>
                                        {{ notice.application_deadline_text || formatDate(notice.application_deadline) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block mb-1">{{ en ? 'Minimum GPA' : 'ন্যূনতম জিপিএ' }}</span>
                                    <span class="font-bold text-gray-900 dark:text-white text-sm">
                                        {{ notice.total_gpa_required || (en ? 'See Circular' : 'সার্কুলার দেখুন') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block mb-1">{{ en ? 'Application Fee' : 'আবেদন ফি' }}</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                        {{ notice.application_fee || (en ? 'Mentioned in circular' : 'বিজ্ঞপ্তি দেখুন') }}
                                    </span>
                                </div>
                            </div>

                            <!-- HIGH-IMPACT PRACTICE BRIDGE CARD & INTERACTIVE MINI-TEST WIDGET -->
                            <section class="my-8 rounded-3xl border border-orange-500/30 bg-gradient-to-br from-[#12192b] via-[#161d30] to-[#0d1322] text-white p-6 md:p-8 shadow-xl relative overflow-hidden">
                                <!-- Background Glow -->
                                <div class="pointer-events-none absolute -top-16 -right-16 w-64 h-64 bg-orange-500/15 rounded-full blur-3xl"></div>
                                <div class="pointer-events-none absolute -bottom-16 -left-16 w-64 h-64 bg-blue-500/15 rounded-full blur-3xl"></div>

                                <div class="relative z-10 space-y-6">
                                    <!-- Header -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-white/10">
                                        <div>
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-500/20 text-orange-400 text-xs font-bold uppercase tracking-wider mb-2">
                                                <span class="material-symbols-outlined !text-sm">school</span>
                                                <span>{{ en ? 'PAST EXAM QUESTION BANK' : 'ভর্তি পরীক্ষার প্রস্তুতি ব্রিজ' }}</span>
                                            </div>
                                            <h2 class="text-xl md:text-2xl font-black font-heading text-white">
                                                {{ en ? 'Practice Past Questions for this University' : 'এই বিশ্ববিদ্যালয়ের বিগত বছরের প্রশ্নব্যাংক অনুশীলন করুন' }}
                                            </h2>
                                            <p class="text-xs md:text-sm text-gray-300 mt-1">
                                                {{ en ? 'Master genuine past entrance exam questions with step-by-step explanations and realistic negative-marking model tests.' : 'বিগত বছরের মূল প্রশ্ন সমাধান করুন, বিষয়ভিত্তিক দুর্বলতা চিহ্নিত করুন এবং ফ্রি মডেল টেস্ট দিয়ে নিজের প্রস্তুতি যাচাই করুন।' }}
                                            </p>
                                        </div>

                                        <Link
                                            href="/question-bank?segment=admission"
                                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-md shadow-orange-500/30 transition shrink-0"
                                        >
                                            <span class="material-symbols-outlined !text-base">menu_book</span>
                                            <span>{{ en ? 'Full Question Bank' : 'সম্পূর্ণ প্রশ্নব্যাংক' }}</span>
                                            <span>→</span>
                                        </Link>
                                    </div>

                                    <!-- Unit-Wise Direct Links -->
                                    <div v-if="practiceBridge?.units?.length" class="space-y-3">
                                        <div class="flex items-center justify-between text-xs text-gray-300 font-semibold">
                                            <span>{{ en ? 'Unit-wise Question Bank Direct Access:' : 'ইউনিটভিত্তিক প্রশ্নব্যাংক সরাসরি অনুশীলন করুন:' }}</span>
                                            <span class="text-orange-400 text-[11px] font-mono">{{ practiceBridge.total_questions_count?.toLocaleString() || '১৫,০০০+' }}টি প্রশ্ন সংগ্রহ</span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                            <Link
                                                v-for="(unit, idx) in practiceBridge.units"
                                                :key="idx"
                                                :href="unit.link"
                                                class="p-3.5 rounded-2xl bg-white/5 hover:bg-orange-500/15 border border-white/10 hover:border-orange-500/40 transition group flex flex-col justify-between"
                                            >
                                                <div>
                                                    <div class="flex items-center justify-between gap-1 mb-1.5">
                                                        <span class="text-xs font-bold text-white group-hover:text-orange-400 transition">
                                                            {{ unit.name }}
                                                        </span>
                                                        <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-white/10 text-orange-300 shrink-0">
                                                            {{ unit.count }}+
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-gray-400 line-clamp-1 mb-2">
                                                        {{ unit.subjects }}
                                                    </p>
                                                </div>
                                                <div class="text-[11px] font-bold text-orange-400 flex items-center gap-1 mt-auto group-hover:translate-x-0.5 transition-transform">
                                                    <span>{{ en ? 'Practice Now' : 'অনুশীলন শুরু করুন' }}</span>
                                                    <span class="material-symbols-outlined !text-xs">arrow_forward</span>
                                                </div>
                                            </Link>
                                        </div>
                                    </div>

                                    <!-- INTERACTIVE MINI-TEST WIDGET -->
                                    <div class="mt-8 pt-6 border-t border-white/10">
                                        <!-- Mini-Test Header Strip -->
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/30">
                                                    <span class="material-symbols-outlined !text-xl">timer</span>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                                                            {{ en ? 'Free Sample Test (10 Questions)' : 'ফ্রি স্যাম্পল টেস্ট দিন (১০টি প্রশ্ন)' }}
                                                        </h3>
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                                            {{ en ? 'No Login Required' : 'লগইন ছাড়াই ফ্রি' }}
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-slate-600 dark:text-gray-300">
                                                        {{ en ? 'Instant evaluation with negative marking (-0.25) & detailed explanations.' : 'নেগেটিভ মার্কিংসহ তাৎক্ষণিক মূল্যায়ন ও স্টেপ-বাই-স্টেপ সমাধান।' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Timer & Progress -->
                                            <div class="flex items-center gap-3">
                                                <div class="px-3 py-1.5 rounded-xl bg-black/40 border border-white/10 flex items-center gap-1.5 font-mono text-xs text-blue-300">
                                                    <span class="material-symbols-outlined !text-sm text-blue-400">schedule</span>
                                                    <span>{{ formattedTestTime }}</span>
                                                </div>
                                                <div class="px-3 py-1.5 rounded-xl bg-orange-500/15 border border-orange-500/30 text-orange-400 text-xs font-bold font-mono">
                                                    {{ Object.keys(userAnswers).length }}/{{ quizQuestions.length }} উত্তর
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ACTIVE TEST TAKING VIEW (When NOT Submitted) -->
                                        <div v-if="!isTestSubmitted" class="space-y-4">
                                            <!-- Question Number Pills Navigation -->
                                            <div class="flex items-center gap-1.5 overflow-x-auto pb-2 scrollbar-none">
                                                <button
                                                    v-for="(q, idx) in quizQuestions"
                                                    :key="q.id"
                                                    @click="activeQIndex = idx"
                                                    :class="[
                                                        'w-8 h-8 rounded-xl text-xs font-mono font-bold transition shrink-0 flex items-center justify-center border',
                                                        activeQIndex === idx
                                                            ? 'bg-orange-500 text-white border-orange-400 shadow-md shadow-orange-500/30 scale-105'
                                                            : userAnswers[q.id]
                                                                ? 'bg-blue-600/30 border-blue-400/40 text-blue-300'
                                                                : 'bg-slate-100 dark:bg-white/5 border-slate-200 dark:border-white/10 text-slate-600 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-white/10'
                                                    ]"
                                                >
                                                    {{ idx + 1 }}
                                                </button>
                                            </div>

                                            <!-- Current Question Card -->
                                            <div v-if="currentQ" class="p-5 rounded-2xl bg-slate-50 dark:bg-[#0e1424] border border-slate-200 dark:border-blue-500/25 space-y-4 shadow-xs dark:shadow-inner">
                                                <!-- Question Header -->
                                                <div class="flex items-center justify-between text-xs text-gray-400">
                                                    <span class="font-bold text-orange-400 uppercase tracking-wider">
                                                        প্রশ্ন {{ activeQIndex + 1 }} / {{ quizQuestions.length }}
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded bg-white/5 text-[11px] font-mono text-gray-300 border border-white/5">
                                                        {{ currentQ.origin_tag || 'ADMISSION' }}
                                                    </span>
                                                </div>

                                                <!-- Question Text -->
                                                <div class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-relaxed">
                                                    {{ currentQ.question_text }}
                                                </div>

                                                <!-- 4 Option Buttons Grid -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                                                    <button
                                                        v-for="opt in currentQ.options"
                                                        :key="opt.key"
                                                        @click="selectOption(opt.key)"
                                                        :class="[
                                                            'p-3 rounded-xl border text-left text-xs sm:text-sm font-medium transition flex items-center gap-3',
                                                            userAnswers[currentQ.id] === opt.key
                                                                ? 'bg-orange-500/20 border-orange-500 text-white ring-2 ring-orange-500/30 shadow-md'
                                                                : 'bg-white dark:bg-white/5 border-slate-200 dark:border-white/10 hover:border-orange-400 text-slate-800 dark:text-gray-200 hover:bg-slate-50 dark:hover:bg-white/10 shadow-xs'
                                                        ]"
                                                    >
                                                        <span
                                                            :class="[
                                                                'w-6 h-6 rounded-lg text-xs font-mono font-bold flex items-center justify-center shrink-0 border',
                                                                userAnswers[currentQ.id] === opt.key
                                                                    ? 'bg-orange-500 text-white border-orange-400'
                                                                    : 'bg-white/10 text-gray-300 border-white/10'
                                                            ]"
                                                        >
                                                            {{ opt.key }}
                                                        </span>
                                                        <span class="flex-1">{{ opt.text }}</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Controls & Submit Buttons -->
                                            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                                                <div class="flex items-center gap-2">
                                                    <button
                                                        @click="prevQuestion"
                                                        :disabled="activeQIndex === 0"
                                                        class="px-3.5 py-2 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 disabled:opacity-40 text-xs font-bold transition flex items-center gap-1"
                                                    >
                                                        <span class="material-symbols-outlined !text-sm">arrow_back</span>
                                                        <span>পূর্ববর্তী</span>
                                                    </button>

                                                    <button
                                                        @click="nextQuestion"
                                                        :disabled="activeQIndex === quizQuestions.length - 1"
                                                        class="px-3.5 py-2 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 disabled:opacity-40 text-xs font-bold transition flex items-center gap-1"
                                                    >
                                                        <span>পরবর্তী</span>
                                                        <span class="material-symbols-outlined !text-sm">arrow_forward</span>
                                                    </button>
                                                </div>

                                                <button
                                                    @click="submitTest"
                                                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-emerald-500/25 transition flex items-center gap-2 hover:scale-[1.02]"
                                                >
                                                    <span class="material-symbols-outlined !text-base">check_circle</span>
                                                    <span>সাবমিট করুন ও ফলাফল দেখুন</span>
                                                    <span class="material-symbols-outlined !text-sm">arrow_forward</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- POST-SUBMISSION RESULTS VIEW (The Funnel Conversion Engine!) -->
                                        <div v-else class="space-y-6">
                                            <!-- Score Celebration Banner -->
                                            <div class="p-6 rounded-2xl bg-gradient-to-br from-[#0f233d] to-[#141b30] border border-blue-500/40 text-center relative overflow-hidden">
                                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold mb-3">
                                                    <span class="material-symbols-outlined !text-sm">emoji_events</span>
                                                    <span>টেস্ট সম্পন্ন হয়েছে!</span>
                                                </div>

                                                <div class="text-3xl sm:text-5xl font-black font-mono text-white mb-2">
                                                    {{ testScore.netScore }} <span class="text-xl sm:text-2xl text-gray-400">/ {{ quizQuestions.length }}.০০</span>
                                                </div>
                                                <p class="text-xs sm:text-sm text-blue-200 max-w-md mx-auto">
                                                    মানদণ্ড: সঠিক উত্তরে +১.০, ভুল উত্তরে -০.২৫ নেগেটিভ মার্কিং। সময় লেগেছে {{ formattedTestTime }}।
                                                </p>

                                                <!-- 4 Stat Metric Cards -->
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5 text-center">
                                                    <div class="p-3 rounded-xl bg-white/5 border border-emerald-500/30">
                                                        <div class="text-xl font-black text-emerald-400">{{ testScore.correct }}টি</div>
                                                        <div class="text-[10px] text-gray-300 mt-0.5">সঠিক উত্তর</div>
                                                    </div>
                                                    <div class="p-3 rounded-xl bg-white/5 border border-rose-500/30">
                                                        <div class="text-xl font-black text-rose-400">{{ testScore.incorrect }}টি</div>
                                                        <div class="text-[10px] text-gray-300 mt-0.5">ভুল উত্তর</div>
                                                    </div>
                                                    <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                                                        <div class="text-xl font-black text-gray-400">{{ testScore.unanswered }}টি</div>
                                                        <div class="text-[10px] text-gray-300 mt-0.5">উত্তর দেননি</div>
                                                    </div>
                                                    <div class="p-3 rounded-xl bg-white/5 border border-blue-500/30">
                                                        <div class="text-xl font-black text-blue-400">{{ testScore.accuracy }}%</div>
                                                        <div class="text-[10px] text-gray-300 mt-0.5">নির্ভুলতা</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 1-CLICK REGISTRATION HIGH CONVERSION HERO CALLOUT -->
                                            <div class="p-6 rounded-2xl bg-gradient-to-r from-orange-500/20 via-amber-500/20 to-orange-500/10 border-2 border-orange-500/50 relative overflow-hidden">
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                                                    <div class="space-y-1.5 max-w-xl">
                                                        <div class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-amber-400">
                                                            <span class="material-symbols-outlined !text-sm">military_tech</span>
                                                            <span>জাতীয় লিডারবোর্ডে আপনার অবস্থান দেখুন</span>
                                                        </div>
                                                        <h4 class="text-lg sm:text-xl font-black text-white">
                                                            আপনার ফলাফল ও অবস্থান লিডারবোর্ডে দেখতে এবং ২০,০০০+ ভর্তি প্রশ্ন প্র্যাকটিস করতে এখনই ফ্রি একাউন্ট খুলুন
                                                        </h4>
                                                        <p class="text-xs text-gray-300 leading-relaxed">
                                                            আপনার এই স্যাম্পল টেস্টের {{ testScore.netScore }} স্কোরটি স্বয়ংক্রিয়ভাবে সংরক্ষিত হবে এবং দেশজুড়ে ভর্তি পরীক্ষার্থীদের মেধা তালিকায় আপনার র‍্যাংক দেখতে পাবেন।
                                                        </p>
                                                    </div>

                                                    <div class="shrink-0 flex flex-col gap-2">
                                                        <Link
                                                            :href="registerCtaUrl"
                                                            class="px-6 py-3.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-sm shadow-xl shadow-orange-500/30 transition flex items-center justify-center gap-2 hover:scale-[1.02] text-center"
                                                        >
                                                            <span class="material-symbols-outlined !text-base">how_to_reg</span>
                                                            <span>১-ক্লিকে ফ্রি একাউন্ট খুলুন</span>
                                                            <span>→</span>
                                                        </Link>
                                                        <div class="flex items-center justify-center gap-3 text-[11px] text-gray-400">
                                                            <button @click="resetTest" class="hover:text-white underline">
                                                                পুনরায় টেস্ট দিন
                                                            </button>
                                                            <span>•</span>
                                                            <Link href="/question-bank?segment=admission" class="hover:text-orange-400">
                                                                প্রশ্নব্যাংক দেখুন
                                                            </Link>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Full Question-by-Question Detailed Review & Explanations -->
                                            <div class="space-y-3 pt-3">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-sm font-bold text-white flex items-center gap-1.5">
                                                        <span class="material-symbols-outlined text-orange-400 !text-base">fact_check</span>
                                                        <span>১০টি প্রশ্নের নির্ভুল সমাধান ও ব্যাখ্যা</span>
                                                    </h4>
                                                    <span class="text-xs text-gray-400">সঠিক উত্তরের সাথে মিলিয়ে নিন</span>
                                                </div>

                                                <div class="space-y-3">
                                                    <div
                                                        v-for="(q, idx) in quizQuestions"
                                                        :key="q.id"
                                                        class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-2 text-xs"
                                                    >
                                                        <div class="flex items-center justify-between text-[11px]">
                                                            <span class="font-bold text-orange-300">প্রশ্ন {{ idx + 1 }}</span>
                                                            <span
                                                                v-if="userAnswers[q.id]?.toUpperCase() === (q.correct_option || '').toUpperCase()"
                                                                class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-bold flex items-center gap-1"
                                                            >
                                                                <span class="material-symbols-outlined !text-xs">check</span>
                                                                <span>সঠিক</span>
                                                            </span>
                                                            <span
                                                                v-else-if="userAnswers[q.id]"
                                                                class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 font-bold flex items-center gap-1"
                                                            >
                                                                <span class="material-symbols-outlined !text-xs">close</span>
                                                                <span>ভুল উত্তর</span>
                                                            </span>
                                                            <span
                                                                v-else
                                                                class="px-2 py-0.5 rounded-full bg-gray-500/20 text-gray-400 font-bold"
                                                            >
                                                                উত্তর দেননি
                                                            </span>
                                                        </div>

                                                        <div class="font-semibold text-white text-sm">
                                                            {{ q.question_text }}
                                                        </div>

                                                        <!-- Options Breakdown -->
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 pt-1">
                                                            <div
                                                                v-for="opt in q.options"
                                                                :key="opt.key"
                                                                :class="[
                                                                    'p-2 rounded-lg border text-[11px] flex items-center gap-2',
                                                                    opt.key.toUpperCase() === (q.correct_option || '').toUpperCase()
                                                                        ? 'bg-emerald-500/20 border-emerald-500/50 text-emerald-200 font-bold'
                                                                        : userAnswers[q.id] === opt.key
                                                                            ? 'bg-rose-500/20 border-rose-500/50 text-rose-200 font-bold line-through'
                                                                            : 'bg-white/5 border-white/5 text-gray-300'
                                                                ]"
                                                            >
                                                                <span class="font-mono font-bold">{{ opt.key }}.</span>
                                                                <span>{{ opt.text }}</span>
                                                                <span
                                                                    v-if="opt.key.toUpperCase() === (q.correct_option || '').toUpperCase()"
                                                                    class="ml-auto text-[10px] text-emerald-300 font-bold"
                                                                >
                                                                    (সঠিক উত্তর)
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Explanation -->
                                                        <div v-if="q.explanation" class="mt-2 p-2.5 rounded-lg bg-black/30 border border-white/5 text-[11px] text-gray-300 leading-relaxed">
                                                            <strong class="text-orange-400">ব্যাখ্যা:</strong> {{ q.explanation }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Official Circular Notice Image Section -->
                            <div v-if="notice.notice_image_url" class="my-8">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-orange-500 !text-base">image</span>
                                        <span>{{ en ? 'Official Notice Image / Scan' : 'অফিসিয়াল সার্কুলার ইমেজ / নোটিশ' }}</span>
                                    </h3>
                                    <button
                                        @click="imageModalOpen = true"
                                        class="text-xs font-bold text-orange-500 hover:text-orange-600 flex items-center gap-1"
                                    >
                                        <span class="material-symbols-outlined !text-xs">zoom_in</span>
                                        <span>{{ en ? 'View Fullscreen' : 'বড় করে দেখুন' }}</span>
                                    </button>
                                </div>
                                <div
                                    @click="imageModalOpen = true"
                                    class="cursor-pointer group relative overflow-hidden rounded-2xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-black/20"
                                >
                                    <img
                                        :src="notice.notice_image_url"
                                        :alt="notice.title"
                                        class="w-full max-h-[460px] object-contain mx-auto group-hover:scale-[1.02] transition-transform duration-300"
                                    />
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-sm font-bold gap-2">
                                        <span class="material-symbols-outlined">zoom_in</span>
                                        <span>{{ en ? 'Click to Zoom' : 'সম্পূর্ণ নোটিশ জুম করে দেখতে ক্লিক করুন' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Blog-style Body Content -->
                            <div
                                class="prose prose-slate dark:prose-invert max-w-none text-sm md:text-base leading-relaxed my-8"
                                v-html="notice.description"
                            />

                            <!-- Units & Faculties Section -->
                            <div v-if="notice.units?.length" class="my-8 pt-6 border-t border-gray-200 dark:border-white/10">
                                <h3 class="text-base md:text-lg font-bold font-heading text-gray-900 dark:text-white mb-4">
                                    {{ en ? 'Units, Faculties & Exam Schedule' : 'ইউনিট ও অনুষদভিত্তিক পরীক্ষার সময়সূচি' }}
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse border border-gray-200 dark:border-white/10 text-xs md:text-sm">
                                        <thead>
                                            <tr class="bg-gray-50 dark:bg-white/5 text-left text-gray-700 dark:text-gray-300">
                                                <th class="p-3 border border-gray-200 dark:border-white/10">{{ en ? 'Unit' : 'ইউনিট' }}</th>
                                                <th class="p-3 border border-gray-200 dark:border-white/10">{{ en ? 'Faculty / Group' : 'অনুষদ / শাখা' }}</th>
                                                <th class="p-3 border border-gray-200 dark:border-white/10">{{ en ? 'Min GPA' : 'ন্যূনতম জিপিএ' }}</th>
                                                <th class="p-3 border border-gray-200 dark:border-white/10">{{ en ? 'Exam Date' : 'পরীক্ষার তারিখ' }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(unit, idx) in notice.units" :key="idx" class="border-b border-gray-200 dark:border-white/5">
                                                <td class="p-3 font-bold text-gray-900 dark:text-white border border-gray-200 dark:border-white/10">{{ unit.name || unit }}</td>
                                                <td class="p-3 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-white/10">{{ unit.faculty || '-' }}</td>
                                                <td class="p-3 font-semibold text-blue-600 dark:text-blue-400 border border-gray-200 dark:border-white/10">{{ unit.min_gpa || '-' }}</td>
                                                <td class="p-3 font-semibold text-red-500 border border-gray-200 dark:border-white/10">{{ unit.exam_date || '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Source Attribution Credit -->
                            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-gray-500 dark:text-gray-400 gap-2">
                                <div>
                                    <span v-if="notice.source_name">
                                        {{ en ? 'Source / Credit: ' : 'বিজ্ঞপ্তির সূত্র: ' }}
                                        <strong class="text-gray-700 dark:text-gray-300">{{ notice.source_name }}</strong>
                                    </span>
                                </div>
                                <a
                                    v-if="notice.source_url"
                                    :href="notice.source_url"
                                    target="_blank"
                                    class="text-orange-500 hover:underline flex items-center gap-1"
                                >
                                    <span>{{ en ? 'Original Source Link' : 'মূল সার্কুলার লিংক' }}</span>
                                    <span class="material-symbols-outlined !text-xs">open_in_new</span>
                                </a>
                            </div>
                        </article>
                    </main>

                    <!-- Sidebar Column (4 cols) -->
                    <aside class="lg:col-span-4 space-y-6">
                        <!-- Quick Action Card -->
                        <div class="bg-white dark:bg-[#171c28] rounded-3xl border border-gray-200 dark:border-white/5 p-6 shadow-sm dark:shadow-none sticky top-24">
                            <h3 class="text-base font-bold font-heading text-gray-900 dark:text-white mb-4">
                                {{ en ? 'Apply & Guidelines' : 'আবেদন ও নিয়মাবলী' }}
                            </h3>

                            <div class="space-y-3">
                                <a
                                    v-if="notice.apply_url"
                                    :href="notice.apply_url"
                                    target="_blank"
                                    class="w-full py-3.5 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm shadow-lg shadow-orange-500/20 transition flex items-center justify-center gap-2"
                                >
                                    <span class="material-symbols-outlined !text-base">touch_app</span>
                                    <span>{{ en ? 'Apply Online Now' : 'অনলাইনে আবেদন করুন' }}</span>
                                    <span class="material-symbols-outlined !text-sm">open_in_new</span>
                                </a>

                                <a
                                    v-if="notice.official_website_url"
                                    :href="notice.official_website_url"
                                    target="_blank"
                                    class="w-full py-3 px-4 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 font-semibold text-xs transition flex items-center justify-center gap-2"
                                >
                                    <span class="material-symbols-outlined !text-sm">language</span>
                                    <span>{{ en ? 'Official Website' : 'অফিসিয়াল ওয়েবসাইট' }}</span>
                                </a>
                            </div>

                            <!-- Preparation Bundle Suggestion -->
                            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-white/5">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-orange-500 block mb-2">
                                    {{ en ? 'RECOMMENDED PREPARATION' : 'ভর্তি প্রস্তুতি সহায়ক' }}
                                </span>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 leading-relaxed">
                                    {{ en ? 'Access digitised textbooks, previous years question bank, and AI revision companion on Sikhun.' : 'শিখুনে ডিজিটাল পাঠ্যবই, বিগত বছরের প্রশ্নব্যাংক ও এআই টিউটর দিয়ে ভর্তি পরীক্ষার চূড়ান্ত প্রস্তুতি নিন।' }}
                                </p>
                                <Link
                                    href="/library"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-orange-500 hover:text-orange-600 mb-3"
                                >
                                    <span>{{ en ? 'Explore Textbooks & Materials' : 'পাঠ্যবই ও স্টাডি উপাদান দেখুন' }}</span>
                                    <span>→</span>
                                </Link>

                                <div class="space-y-2">
                                    <Link
                                        href="/question-bank?segment=admission"
                                        class="w-full py-2.5 px-3 rounded-xl bg-orange-500/10 hover:bg-orange-500/20 border border-orange-500/30 text-orange-500 font-bold text-xs flex items-center justify-between transition"
                                    >
                                        <span>ভর্তি প্রশ্নব্যাংক (১৫,০০০+)</span>
                                        <span>→</span>
                                    </Link>
                                    <Link
                                        href="/question-bank?segment=admission"
                                        class="w-full py-2.5 px-3 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 text-blue-400 font-bold text-xs flex items-center justify-between transition"
                                    >
                                        <span>ফ্রি পূর্ণাঙ্গ মডেল টেস্ট</span>
                                        <span class="material-symbols-outlined !text-sm">quiz</span>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Related Circulars -->
                        <div v-if="relatedNotices?.length" class="bg-white dark:bg-[#171c28] rounded-3xl border border-gray-200 dark:border-white/5 p-6 shadow-sm dark:shadow-none">
                            <h3 class="text-sm font-bold font-heading text-gray-900 dark:text-white mb-4">
                                {{ en ? 'Other Admission Notices' : 'অন্যান্য ভর্তি বিজ্ঞপ্তি' }}
                            </h3>
                            <div class="space-y-4">
                                <div
                                    v-for="rel in relatedNotices"
                                    :key="rel.id"
                                    class="pb-4 border-b border-gray-100 dark:border-white/5 last:border-0 last:pb-0"
                                >
                                    <span class="text-[10px] font-bold text-orange-500 block mb-1">
                                        {{ rel.institution_name }}
                                    </span>
                                    <Link :href="`/admission-notice/${rel.slug}`">
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-white hover:text-orange-500 transition line-clamp-2">
                                            {{ rel.title }}
                                        </h4>
                                    </Link>
                                    <span class="text-[11px] text-red-500 mt-1 block">
                                        {{ en ? 'Deadline:' : 'শেষ তারিখ:' }} {{ formatDate(rel.application_deadline) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <!-- Lightbox Modal for Notice Image -->
            <div
                v-if="imageModalOpen"
                class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4"
                @click.self="imageModalOpen = false"
            >
                <div class="relative max-w-5xl max-h-[90vh] bg-[#121620] rounded-2xl p-4 overflow-auto border border-white/10">
                    <button
                        @click="imageModalOpen = false"
                        class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg"
                    >
                        ✕
                    </button>
                    <img
                        :src="notice.notice_image_url"
                        :alt="notice.title"
                        class="max-w-full max-h-[82vh] object-contain mx-auto rounded-lg"
                    />
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
