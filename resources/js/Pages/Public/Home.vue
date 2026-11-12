<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    stats: Object,
    categories: Array,
    topCourses: Array,
    popularBooks: Array,
    latestBlogs: Array,
    plans: Array,
    testimonials: Array,
    seo: Object,
    featuredJobs: Array,
    featuredAdmissions: Array,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');

// Target application deadlines for Admissions 2026
const duDeadline = new Date('2026-11-25T23:59:59+06:00');
const ruDeadline = new Date('2026-11-30T23:59:59+06:00');

const duTimer = ref({ days: '00', hours: '00', minutes: '00', seconds: '00' });
const ruTimer = ref({ days: '00', hours: '00', minutes: '00', seconds: '00' });
let homeTimerInterval = null;

function updateHomeTimers() {
    const now = new Date().getTime();

    // DU
    const distDU = Math.max(0, duDeadline.getTime() - now);
    duTimer.value = {
        days: Math.floor(distDU / (1000 * 60 * 60 * 24)).toString().padStart(2, '0'),
        hours: Math.floor((distDU % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toString().padStart(2, '0'),
        minutes: Math.floor((distDU % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0'),
        seconds: Math.floor((distDU % (1000 * 60)) / 1000).toString().padStart(2, '0'),
    };

    // RU
    const distRU = Math.max(0, ruDeadline.getTime() - now);
    ruTimer.value = {
        days: Math.floor(distRU / (1000 * 60 * 60 * 24)).toString().padStart(2, '0'),
        hours: Math.floor((distRU % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toString().padStart(2, '0'),
        minutes: Math.floor((distRU % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0'),
        seconds: Math.floor((distRU % (1000 * 60)) / 1000).toString().padStart(2, '0'),
    };
}

onMounted(() => {
    updateHomeTimers();
    homeTimerInterval = setInterval(updateHomeTimers, 1000);
});

onUnmounted(() => {
    if (homeTimerInterval) clearInterval(homeTimerInterval);
});

const steps = computed(() => en.value ? [
    { icon: 'person_add', title: 'Create your account', text: 'Choose a learning goal and set up your Sikhun account in a few simple steps.', meta: 'A clear place to begin' },
    { icon: 'travel_explore', title: 'Find your resources', text: 'Explore books and mentor-led courses by subject, level, and category.', meta: 'Books and courses' },
    { icon: 'psychology', title: 'Study actively', text: 'Ask questions, practice with AI study tools, and revisit difficult ideas.', meta: 'Learn by doing' },
    { icon: 'trending_up', title: 'Build your momentum', text: 'Keep track of your learning and return to your goals whenever you are ready.', meta: 'Progress at your pace' },
] : [
    { icon: 'person_add', title: 'অ্যাকাউন্ট তৈরি করুন', text: 'আপনার শেখার লক্ষ্য বেছে নিয়ে কয়েকটি সহজ ধাপে Sikhun অ্যাকাউন্ট তৈরি করুন।', meta: 'শুরু হোক পরিষ্কারভাবে' },
    { icon: 'travel_explore', title: 'উপকরণ খুঁজে নিন', text: 'বিষয়, লেভেল ও ক্যাটাগরি অনুযায়ী বই এবং মেন্টর পরিচালিত কোর্স দেখুন।', meta: 'বই ও কোর্স' },
    { icon: 'psychology', title: 'সক্রিয়ভাবে শিখুন', text: 'প্রশ্ন করুন, AI স্টাডি টুলে অনুশীলন করুন এবং কঠিন বিষয় আবার দেখুন।', meta: 'অনুশীলনে শেখা' },
    { icon: 'trending_up', title: 'এগিয়ে চলুন', text: 'নিজের শেখার অগ্রগতি দেখুন এবং প্রস্তুত হলে আবার লক্ষ্যে ফিরে আসুন।', meta: 'নিজের গতিতে' },
]);

const pathwayLabel = computed(() => en.value ? 'Find a subject to explore' : 'পছন্দের বিষয় খুঁজে নিন');
const coursePalette = ['from-[#0e513d] to-[#0a0e18]', 'from-[#7c3d13] to-[#171b26]', 'from-[#123859] to-[#0a0e18]'];
const bookPalette = ['from-[#123858] to-[#0a0e18]', 'from-[#0e513d] to-[#0a0e18]', 'from-[#5b2f22] to-[#0a0e18]', 'from-[#40336e] to-[#0a0e18]', 'from-[#77521c] to-[#0a0e18]'];

function formatDate(d) {
    if (!d) return en.value ? 'Soon' : 'শীঘ্রই';
    return new Date(d).toLocaleDateString(en.value ? 'en-US' : 'bn-BD', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <div class="home-design relative isolate overflow-hidden bg-[var(--bg)]">
            <!-- Ambient color fields -->
            <div class="pointer-events-none absolute -top-48 left-[20%] -z-10 h-[620px] w-[620px] rounded-full bg-[var(--primary)]/10 blur-[140px]"></div>
            <div class="pointer-events-none absolute top-[1120px] -right-48 -z-10 h-[560px] w-[560px] rounded-full bg-[var(--secondary)]/10 blur-[150px]"></div>
            <div class="pointer-events-none absolute top-[2900px] -left-40 -z-10 h-[640px] w-[640px] rounded-full bg-[var(--primary)]/10 blur-[160px]"></div>

            <!-- Hero Section -->
            <section class="max-w-7xl mx-auto px-4 md:px-6 pt-10 md:pt-14 pb-16 md:pb-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 xl:gap-14 items-center">
                    <div class="lg:col-span-7 flex flex-col items-start">
                        <div class="inline-flex items-center gap-2.5 rounded-full border border-white/5 bg-[#1c1f2a]/80 px-4 py-2 shadow-sm backdrop-blur">
                            <span class="relative flex h-2.5 w-2.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#ff7a00] opacity-60"></span><span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-[#ff7a00]"></span></span>
                            <span class="font-mono text-[10px] md:text-[11px] font-semibold uppercase tracking-[.11em] text-[var(--primary)]">{{ en ? 'Learning tools for every next step' : 'আপনার শেখার প্রতিটি পরবর্তী ধাপে' }}</span>
                            <span class="hidden sm:inline rounded bg-[#0a0e18] px-2 py-0.5 font-mono text-[9px] text-[var(--text-muted)]">SSC · HSC · ADMISSION · JOB</span>
                        </div>
                        <h1 class="mt-6 font-heading text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.08] text-[var(--text)]">
                            {{ en ? 'A clearer way to read, practice, and learn.' : 'পড়াশোনা, চাকরি ও ভর্তি প্রস্তুতির সহজ মাধ্যম।' }}
                        </h1>
                        <p class="mt-5 max-w-xl text-base md:text-lg leading-relaxed text-[var(--text-muted)]">
                            {{ en ? 'Access digitized textbooks, mentor-led courses, real-time AI companions, job circulars, and academic calculators—all in one place.' : 'ডিজিটাল পাঠ্যবই, মেন্টর পরিচালিত কোর্স, লক্ষ্যভিত্তিক এআই স্টাডি সহায়তা, প্রতিদিনের চাকরির সার্কুলার ও ক্যালকুলেটর—সবকিছু এক জায়গায়।' }}
                        </p>
                        <div class="mt-8 flex flex-wrap items-center gap-3 w-full sm:w-auto">
                            <Link href="/register" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#ff7a00] to-[#f97316] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-[#f97316]/25 transition hover:shadow-[0_0_32px_rgba(249,115,22,.4)] hover:-translate-y-0.5">
                                {{ en ? 'Get started free' : 'বিনামূল্যে শুরু করুন' }} <span aria-hidden="true">→</span>
                            </Link>
                            <Link href="/job-news" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-[#1c1f2a] px-5 py-3.5 text-sm font-semibold text-[var(--text)] transition hover:bg-[#262a35] hover:border-white/20">
                                <span>{{ en ? 'Job News' : 'চাকরির খবর' }}</span>
                                <span class="material-symbols-outlined !text-sm">work</span>
                            </Link>
                            <Link href="/calculators" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-white/10 bg-[#1c1f2a] px-4 py-3.5 text-sm font-semibold text-[var(--text)] transition hover:bg-[#262a35]">
                                <span>{{ en ? 'Calculators' : 'ক্যালকুলেটর' }}</span>
                                <span class="material-symbols-outlined !text-sm">calculate</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Hero Visual / Stats -->
                    <div class="lg:col-span-5 relative">
                        <div class="relative overflow-hidden rounded-3xl border border-white/5 bg-gradient-to-b from-[#1c1f2a] to-[#121622] p-6 md:p-8 shadow-2xl">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-white/5">
                                    <span class="text-xs font-mono font-bold text-[var(--primary)] uppercase tracking-wider">{{ en ? 'PLATFORM AT A GLANCE' : 'এক নজরে প্ল্যাটফর্ম' }}</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 text-[10px] font-bold">LIVE</span>
                                </div>
                                <div class="grid grid-cols-2 gap-3 text-center">
                                    <div class="p-4 rounded-2xl bg-white dark:bg-[#0e1320] border border-gray-200 dark:border-white/5">
                                        <div class="font-heading text-2xl font-black text-gray-900 dark:text-white">{{ stats?.students?.toLocaleString(en ? 'en-US' : 'bn-BD') || (en ? '12,000+' : '১২,০০০+') }}</div>
                                        <div class="text-[11px] text-[var(--text-muted)] mt-0.5">{{ en ? 'Active Students' : 'সক্রিয় শিক্ষার্থী' }}</div>
                                    </div>
                                    <div class="p-4 rounded-2xl bg-white dark:bg-[#0e1320] border border-gray-200 dark:border-white/5">
                                        <div class="font-heading text-2xl font-black text-emerald-500 dark:text-emerald-400">{{ stats?.books?.toLocaleString(en ? 'en-US' : 'bn-BD') || (en ? '50+' : '৫০+') }}</div>
                                        <div class="text-[11px] text-[var(--text-muted)] mt-0.5">{{ en ? 'Digital Books' : 'ডিজিটাল বই' }}</div>
                                    </div>
                                    <div class="p-4 rounded-2xl bg-white dark:bg-[#0e1320] border border-gray-200 dark:border-white/5">
                                        <div class="font-heading text-2xl font-black text-amber-500 dark:text-amber-400">{{ en ? '4 Models' : '৪টি' }}</div>
                                        <div class="text-[11px] text-[var(--text-muted)] mt-0.5">{{ en ? 'AI Study Models' : 'পাওয়ারফুল এআই মডেল' }}</div>
                                    </div>
                                    <div class="p-4 rounded-2xl bg-white dark:bg-[#0e1320] border border-gray-200 dark:border-white/5">
                                        <div class="font-heading text-2xl font-black text-blue-500 dark:text-blue-400">{{ en ? '100% Free' : '১০০%' }}</div>
                                        <div class="text-[11px] text-[var(--text-muted)] mt-0.5">{{ en ? 'Public Student Tools' : 'ফ্রি পাবলিক টুলস' }}</div>
                                    </div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#171c28] border border-gray-200 dark:border-white/5 flex items-center justify-between text-xs">
                                    <span class="text-gray-700 dark:text-gray-300">{{ en ? 'Job & Admission Bundles:' : 'চাকরি ও ভর্তি স্পেশাল বান্ডেল:' }}</span>
                                    <Link href="/#bundles" class="text-[var(--primary)] font-bold hover:underline">{{ en ? 'View details →' : 'বিস্তারিত দেখুন →' }}</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION: Admissions 2026-2027 Live Countdown & Practice Hub -->
            <section id="admissions-season" class="max-w-7xl mx-auto px-4 md:px-6 py-10 md:py-14 scroll-mt-24">
                <div class="relative overflow-hidden rounded-3xl border border-orange-500/25 bg-gradient-to-br from-[#11192e] via-[#0d1424] to-[#161226] text-white p-6 sm:p-10 shadow-2xl">
                    <!-- Glow effects -->
                    <div class="pointer-events-none absolute -top-24 right-10 w-96 h-96 bg-orange-500/15 rounded-full blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-24 left-10 w-96 h-96 bg-blue-500/15 rounded-full blur-3xl"></div>

                    <div class="relative z-10">
                        <!-- Top Header Strip -->
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-white/10">
                            <div>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-500/15 border border-orange-500/30 text-orange-400 text-xs font-bold uppercase tracking-wider mb-2">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                    </span>
                                    <span>{{ en ? 'ADMISSIONS 2026-2027' : 'ভর্তি সেশন ২০২৬-২০২৭ লাইভ' }}</span>
                                </div>
                                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold font-heading text-white tracking-tight">
                                    {{ en ? 'University Admission Preparation 2026-2027' : 'বিশ্ববিদ্যালয় ভর্তি প্রস্তুতি ২০২৬-২০২৭' }}
                                </h2>
                                <p class="mt-2 text-sm md:text-base text-gray-300 max-w-2xl leading-relaxed">
                                    {{ en ? 'Live application deadlines, 15,000+ past year solved questions for DU, RU, BUET & GST with realistic model tests and instant AI explanations.' : 'ঢাকা, রাজশাহী, বুয়েট ও গুচ্ছসহ সকল বিশ্ববিদ্যালয়ের আবেদনের সময়সীমা, বিগত ২৫ বছরের ১৫,০০০+ সলভড প্রশ্নব্যাংক এবং এআই সমাধানসহ ফ্রি মডেল টেস্ট।' }}
                                </p>
                            </div>

                            <!-- Direct CTA Buttons -->
                            <div class="flex flex-wrap items-center gap-3">
                                <Link
                                    href="/question-bank?segment=admission"
                                    class="px-5 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs sm:text-sm shadow-lg shadow-orange-500/25 transition flex items-center gap-2 hover:-translate-y-0.5"
                                >
                                    <span class="material-symbols-outlined !text-base">menu_book</span>
                                    <span>{{ en ? 'Admission Question Bank' : 'ভর্তি প্রশ্নব্যাংক' }}</span>
                                    <span>→</span>
                                </Link>

                                <Link
                                    href="/question-bank?segment=admission"
                                    class="px-5 py-3 rounded-xl bg-blue-600/90 hover:bg-blue-600 text-white font-bold text-xs sm:text-sm border border-blue-400/30 shadow-lg shadow-blue-500/20 transition flex items-center gap-2 hover:-translate-y-0.5"
                                >
                                    <span class="material-symbols-outlined !text-base">quiz</span>
                                    <span>{{ en ? 'Free Model Test' : 'ফ্রি মডেল টেস্ট' }}</span>
                                    <span class="px-1.5 py-0.2 text-[10px] rounded bg-white/20 text-white font-bold">১০টি প্রশ্ন</span>
                                </Link>

                                <Link
                                    href="/admission-notice"
                                    class="px-4 py-3 rounded-xl border border-white/15 bg-white/5 hover:bg-white/10 text-gray-200 font-semibold text-xs sm:text-sm transition flex items-center gap-1.5"
                                >
                                    <span class="material-symbols-outlined !text-base">campaign</span>
                                    <span>{{ en ? 'All Admission Circulars' : 'সকল ভর্তি সার্কুলার' }}</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Open Application Countdown Cards Grid -->
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- DU Application Countdown Card -->
                            <div class="p-6 rounded-2xl bg-[#141d33]/90 border border-blue-500/30 hover:border-blue-400/60 transition shadow-lg relative overflow-hidden group">
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div>
                                        <span class="px-2.5 py-1 rounded-full bg-blue-500/20 text-blue-300 text-[11px] font-bold uppercase tracking-wider inline-block mb-1.5">
                                            ঢাকা বিশ্ববিদ্যালয় (DU)
                                        </span>
                                        <h3 class="text-lg sm:text-xl font-bold font-heading text-white">
                                            আন্ডারগ্র্যাজুয়েট ভর্তি পরীক্ষা ২০২৬
                                        </h3>
                                        <p class="text-xs text-blue-200/80 mt-0.5">
                                            আবেদনের শেষ সময়: <strong class="text-rose-400 font-bold">২৫ নভেম্বর ২০২৬</strong>
                                        </p>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 animate-pulse">
                                        আবেদন চলছে
                                    </span>
                                </div>

                                <!-- Live 4-Block Countdown Timer -->
                                <div class="py-3 px-4 rounded-xl bg-[#0d1424] border border-blue-400/20 flex items-center justify-around font-mono text-center mb-4">
                                    <div>
                                        <div class="text-xl sm:text-2xl font-black text-white">{{ duTimer.days }}</div>
                                        <div class="text-[10px] text-blue-300 uppercase font-sans">দিন</div>
                                    </div>
                                    <span class="text-blue-500 font-bold text-lg">:</span>
                                    <div>
                                        <div class="text-xl sm:text-2xl font-black text-white">{{ duTimer.hours }}</div>
                                        <div class="text-[10px] text-blue-300 uppercase font-sans">ঘণ্টা</div>
                                    </div>
                                    <span class="text-blue-500 font-bold text-lg">:</span>
                                    <div>
                                        <div class="text-xl sm:text-2xl font-black text-white">{{ duTimer.minutes }}</div>
                                        <div class="text-[10px] text-blue-300 uppercase font-sans">মিনিট</div>
                                    </div>
                                    <span class="text-blue-500 font-bold text-lg">:</span>
                                    <div>
                                        <div class="text-xl sm:text-2xl font-black text-rose-400">{{ duTimer.seconds }}</div>
                                        <div class="text-[10px] text-blue-300 uppercase font-sans">সেকেন্ড</div>
                                    </div>
                                </div>

                                <!-- DU Units Direct Links -->
                                <div class="space-y-2">
                                    <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                                        ইউনিটভিত্তিক প্রশ্নব্যাংক অনুশীলন:
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                        <Link
                                            href="/question-bank?segment=admission&origin=DU-A"
                                            class="p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"
                                        >
                                            <div class="text-xs font-bold text-white group-hover/u:text-orange-400">ক ইউনিট</div>
                                            <div class="text-[10px] text-gray-400">১৬৯৩ প্রশ্ন</div>
                                        </Link>
                                        <Link
                                            href="/question-bank?segment=admission&origin=DU-B"
                                            class="p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"
                                        >
                                            <div class="text-xs font-bold text-white group-hover/u:text-orange-400">খ ইউনিট</div>
                                            <div class="text-[10px] text-gray-400">৮৪০ প্রশ্ন</div>
                                        </Link>
                                        <Link
                                            href="/question-bank?segment=admission&origin=DU-C"
                                            class="p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"
                                        >
                                            <div class="text-xs font-bold text-white group-hover/u:text-orange-400">গ ইউনিট</div>
                                            <div class="text-[10px] text-gray-400">১৩৯৫ প্রশ্ন</div>
                                        </Link>
                                        <Link
                                            href="/question-bank?segment=admission&origin=DU-D"
                                            class="p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"
                                        >
                                            <div class="text-xs font-bold text-white group-hover/u:text-orange-400">ঘ / IBA</div>
                                            <div class="text-[10px] text-gray-400">৮৩৭ প্রশ্ন</div>
                                        </Link>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs">
                                    <Link href="/admission-notice" class="text-blue-300 hover:text-white font-medium flex items-center gap-1">
                                        <span>অফিসিয়াল সার্কুলার দেখুন</span>
                                        <span>→</span>
                                    </Link>
                                    <Link href="/question-bank?segment=admission&origin=DU-A" class="text-orange-400 hover:text-orange-300 font-bold flex items-center gap-1">
                                        <span>ঢাবি সম্পূর্ণ প্রশ্নব্যাংক</span>
                                        <span class="material-symbols-outlined !text-xs">north_east</span>
                                    </Link>
                                </div>
                            </div>

                            <!-- RU Application Countdown Card -->
                            <div class="p-6 rounded-2xl bg-[#141d33]/90 border border-purple-500/30 hover:border-purple-400/60 transition shadow-lg relative overflow-hidden group">
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div>
                                        <span class="px-2.5 py-1 rounded-full bg-purple-500/20 text-purple-300 text-[11px] font-bold uppercase tracking-wider inline-block mb-1.5">
                                            রাজশাহী বিশ্ববিদ্যালয় (RU)
                                        </span>
                                        <h3 class="text-lg sm:text-xl font-bold font-heading text-white">
                                            স্নাতক প্রথম বর্ষ ভর্তি পরীক্ষা ২০২৬
                                        </h3>
                                        <p class="text-xs text-purple-200/80 mt-0.5">
                                            আবেদনের শেষ সময়: <strong class="text-rose-400 font-bold">৩০ নভেম্বর ২০২৬</strong>
                                        </p>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 animate-pulse">
                                        আবেদন চলছে
                                    </span>
                                </div>

                                <!-- Live 4-Block Countdown Timer -->
                                <div class="py-3 px-4 rounded-xl bg-[#0d1424] border border-purple-400/20 flex items-center justify-around font-mono text-center mb-4">
                                    <div>
                                        <div class="text-xl sm:text-2xl font-black text-white">{{ ruTimer.days }}</div>
                                        <div class="text-[10px] text-purple-300 uppercase font-sans">দিন</div>
                                    </div>
                                    <span class="text-purple-500 font-bold text-lg">:</span>
                                    <div>
                                        <div class="text-xl sm:text-2xl font-black text-white">{{ ruTimer.hours }}</div>
                                        <div class="text-[10px] text-purple-300 uppercase font-sans">ঘণ্টা</div>
                                    </div>
                                    <span class="text-purple-500 font-bold text-lg">:</span>
                                    <div>
                                        <div class="text-xl sm:text-2xl font-black text-white">{{ ruTimer.minutes }}</div>
                                        <div class="text-[10px] text-purple-300 uppercase font-sans">মিনিট</div>
                                    </div>
                                    <span class="text-purple-500 font-bold text-lg">:</span>
                                    <div>
                                        <div class="text-xl sm:text-2xl font-black text-rose-400">{{ ruTimer.seconds }}</div>
                                        <div class="text-[10px] text-purple-300 uppercase font-sans">সেকেন্ড</div>
                                    </div>
                                </div>

                                <!-- RU Units Direct Links -->
                                <div class="space-y-2">
                                    <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                                        ইউনিটভিত্তিক প্রশ্নব্যাংক অনুশীলন:
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <Link
                                            href="/question-bank?segment=admission&origin=RU-A"
                                            class="p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"
                                        >
                                            <div class="text-xs font-bold text-white group-hover/u:text-orange-400">A ইউনিট (মানবিক)</div>
                                            <div class="text-[10px] text-gray-400">২৫২৯ প্রশ্ন</div>
                                        </Link>
                                        <Link
                                            href="/question-bank?segment=admission&origin=RU-B"
                                            class="p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"
                                        >
                                            <div class="text-xs font-bold text-white group-hover/u:text-orange-400">B ইউনিট (বাণিজ্য)</div>
                                            <div class="text-[10px] text-gray-400">৬৯৬ প্রশ্ন</div>
                                        </Link>
                                        <Link
                                            href="/question-bank?segment=admission&origin=RU-C"
                                            class="p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"
                                        >
                                            <div class="text-xs font-bold text-white group-hover/u:text-orange-400">C ইউনিট (বিজ্ঞান)</div>
                                            <div class="text-[10px] text-gray-400">১৯৩১ প্রশ্ন</div>
                                        </Link>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs">
                                    <Link href="/admission-notice" class="text-purple-300 hover:text-white font-medium flex items-center gap-1">
                                        <span>অফিসিয়াল সার্কুলার দেখুন</span>
                                        <span>→</span>
                                    </Link>
                                    <Link href="/question-bank?segment=admission&origin=RU-A" class="text-orange-400 hover:text-orange-300 font-bold flex items-center gap-1">
                                        <span>রাবি সম্পূর্ণ প্রশ্নব্যাংক</span>
                                        <span class="material-symbols-outlined !text-xs">north_east</span>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Quick University Pills strip -->
                        <div class="mt-6 pt-5 border-t border-white/10 flex flex-wrap items-center justify-between gap-3 text-xs">
                            <span class="text-gray-400 font-semibold">{{ en ? 'More University Question Banks:' : 'অন্যান্য বিশ্ববিদ্যালয় প্রশ্নব্যাংক:' }}</span>
                            <div class="flex flex-wrap items-center gap-2">
                                <Link
                                    href="/question-bank?segment=admission&origin=BUET"
                                    class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 border border-white/10 text-white font-medium transition flex items-center gap-1"
                                >
                                    <span>বুয়েট ও ইঞ্জিনিয়ারিং</span>
                                    <span class="text-[10px] text-orange-400">১৭০০+</span>
                                </Link>
                                <Link
                                    href="/question-bank?segment=admission&origin=GST-C"
                                    class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 border border-white/10 text-white font-medium transition flex items-center gap-1"
                                >
                                    <span>জিএসটি গুচ্ছ (২৪ ভার্সিটি)</span>
                                    <span class="text-[10px] text-orange-400">১৬০০+</span>
                                </Link>
                                <Link
                                    href="/question-bank?segment=admission&search=মেডিকেল"
                                    class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 border border-white/10 text-white font-medium transition flex items-center gap-1"
                                >
                                    <span>মেডিকেল ও ডেন্টাল</span>
                                    <span class="text-[10px] text-orange-400">১২০০+</span>
                                </Link>
                                <Link
                                    href="/admission-notice"
                                    class="px-3 py-1.5 rounded-lg bg-orange-500/20 hover:bg-orange-500/30 border border-orange-500/30 text-orange-300 font-bold transition flex items-center gap-1"
                                >
                                    <span>সকল সার্কুলার দেখুন →</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION: Public Student Tools & Calculators Hub -->
            <section id="tools" class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-16 scroll-mt-24">
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-amber-500 dark:text-amber-400">{{ en ? 'FREE STUDENT & JOB UTILITIES' : 'ফ্রি স্টুডেন্ট ও জব ইউটিলিটিস' }}</span>
                        <h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {{ en ? 'Student Tools & Calculators' : 'স্টুডেন্ট টুলস ও ক্যালকুলেটর' }}
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]">
                            {{ en ? 'Free government job age calculator, SSC/HSC GPA calculation, and board exam result checker without login.' : 'কোনো রেজিস্ট্রেশন বা লগইন ছাড়াই ব্যবহার করুন সরকারি চাকরির বয়স ক্যালকুলেটর, জিপিএ ও রেজাল্ট চেকার।' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link href="/calculators" class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-500 dark:text-amber-400 hover:text-[var(--primary)] transition">
                            <span>{{ en ? 'All Calculators & Tools' : 'সকল ক্যালকুলেটর ও টুলস' }}</span> <span class="material-symbols-outlined !text-sm">arrow_forward</span>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Tool 1: Job Age Calculator -->
                    <Link
                        href="/calculators"
                        class="group p-6 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gradient-to-br dark:from-[#1a202f] dark:to-[#141824] hover:border-amber-500/40 transition-all duration-300 hover:-translate-y-1 shadow-sm dark:shadow-lg"
                    >
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-500 dark:text-amber-400 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">badge</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-gray-900 dark:text-white group-hover:text-amber-500 dark:group-hover:text-amber-400 transition">
                            {{ en ? 'Govt Job Age Calculator' : 'সরকারি চাকরির বয়স ক্যালকুলেটর' }}
                        </h3>
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ en ? 'Calculate exact age in years, months, and days with general (30 yrs) and quota (32 yrs) eligibility checks.' : 'জন্মতারিখ ও সার্কুলার দিয়ে সাধারণ (৩০ বছর) ও কোটা (৩২ বছর) অনুযায়ী বছর, মাস ও দিনে নির্ভুল বয়স যাচাই করুন।' }}
                        </p>
                        <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-amber-500 dark:text-amber-400 group-hover:translate-x-1 transition-transform">
                            <span>{{ en ? 'Calculate Age' : 'বয়স হিসাব করুন' }}</span> <span>→</span>
                        </span>
                    </Link>

                    <!-- Tool 2: GPA & CGPA Calculator -->
                    <Link
                        href="/calculators"
                        class="group p-6 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gradient-to-br dark:from-[#1a202f] dark:to-[#141824] hover:border-blue-500/40 transition-all duration-300 hover:-translate-y-1 shadow-sm dark:shadow-lg"
                    >
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/15 text-blue-500 dark:text-blue-400 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">calculate</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-500 dark:group-hover:text-blue-400 transition">
                            {{ en ? 'SSC / HSC GPA & NU CGPA' : 'SSC / HSC GPA ও NU CGPA' }}
                        </h3>
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ en ? 'Calculate secondary and higher secondary GPA with 4th subject bonus points, plus National University honours CGPA.' : 'শিক্ষাবোর্ডের নিয়মানুযায়ী ৪র্থ বিষয়ের বোনাস পয়েন্টসহ জিপিএ এবং জাতীয় বিশ্ববিদ্যালয়ের অনার্স সিজিপিএ বের করুন।' }}
                        </p>
                        <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-blue-500 dark:text-blue-400 group-hover:translate-x-1 transition-transform">
                            <span>{{ en ? 'Calculate GPA' : 'জিপিএ হিসাব করুন' }}</span> <span>→</span>
                        </span>
                    </Link>

                    <!-- Tool 3: University Admission Notices -->
                    <Link
                        href="/admission-notice"
                        class="group p-6 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gradient-to-br dark:from-[#1a202f] dark:to-[#141824] hover:border-emerald-500/40 transition-all duration-300 hover:-translate-y-1 shadow-sm dark:shadow-lg"
                    >
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-500 dark:text-emerald-400 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">account_balance</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-gray-900 dark:text-white group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition">
                            {{ en ? 'University Admission Notices' : 'বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি' }}
                        </h3>
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ en ? 'Public, Guccho, and Private university admission circulars, eligibility, units, and deadlines.' : 'পাবলিক, গুচ্ছ ও শীর্ষ বেসরকারি বিশ্ববিদ্যালয়ের ভর্তি বিজ্ঞপ্তি, যোগ্যতা, ইউনিট ও সময়সূচির সম্পূর্ণ গাইড।' }}
                        </p>
                        <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-500 dark:text-emerald-400 group-hover:translate-x-1 transition-transform">
                            <span>{{ en ? 'View Notices' : 'ভর্তি বিজ্ঞপ্তি দেখুন' }}</span> <span>→</span>
                        </span>
                    </Link>
                </div>
            </section>

            <!-- SECTION: Latest Job Circulars Preview -->
            <section id="jobs" class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-16 scroll-mt-24 border-t border-gray-200 dark:border-white/5">
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-emerald-500 dark:text-emerald-400">{{ en ? 'CAREERS & RECRUITMENT' : 'ক্যারিয়ার ও নিয়োগ বিজ্ঞপ্তি' }}</span>
                        <h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {{ en ? 'Latest Job News' : 'সর্বশেষ চাকরির খবর ও সার্কুলার' }}
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]">
                            {{ en ? 'Verified circulars from BCS, Bangladesh Bank, Government departments, and leading private organizations.' : 'বিসিএস, বাংলাদেশ ব্যাংক, সরকারি বিভিন্ন দপ্তর ও শীর্ষ বেসরকারি প্রতিষ্ঠানের সাম্প্রতিক নিয়োগ বিজ্ঞপ্তি।' }}
                        </p>
                    </div>
                    <Link href="/job-news" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-500 dark:text-emerald-400 hover:text-[var(--primary)] transition">
                        <span>{{ en ? 'All Job News' : 'সকল চাকরির খবর' }}</span> <span class="material-symbols-outlined !text-sm">arrow_forward</span>
                    </Link>
                </div>

                <div v-if="featuredJobs?.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <article
                        v-for="job in featuredJobs"
                        :key="job.id"
                        class="p-5 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] hover:border-[var(--primary)]/40 transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between shadow-sm dark:shadow-none"
                    >
                        <div>
                            <div class="flex items-center justify-between text-[11px] mb-2">
                                <span class="px-2 py-0.5 rounded-full bg-blue-500/15 text-blue-600 dark:text-blue-400 font-bold uppercase">
                                    {{ job.category }}
                                </span>
                                <span class="text-rose-500 dark:text-rose-400 font-semibold text-[10px]">{{ en ? 'Ends:' : 'শেষ:' }} {{ formatDate(job.deadline) }}</span>
                            </div>
                            <span class="text-xs font-semibold text-[var(--primary)] truncate block mb-1">{{ job.organization }}</span>
                            <Link :href="`/job-news/${job.slug}`">
                                <h3 class="font-heading text-sm font-bold text-gray-900 dark:text-white line-clamp-2 hover:text-[var(--primary)] transition">
                                    {{ job.title }}
                                </h3>
                            </Link>
                            <div class="mt-3 text-[11px] text-gray-600 dark:text-gray-400 space-y-1">
                                <div v-if="job.vacancy">{{ en ? 'Vacancy:' : 'পদ:' }} <strong>{{ job.vacancy }}</strong></div>
                                <div v-if="job.salary" class="truncate">{{ en ? 'Salary:' : 'বেতন:' }} {{ job.salary }}</div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-xs">
                            <Link :href="`/job-news/${job.slug}`" class="font-bold text-gray-900 dark:text-white hover:text-[var(--primary)]">{{ en ? 'Details →' : 'বিস্তারিত →' }}</Link>
                            <a v-if="job.apply_url" :href="job.apply_url" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px]">{{ en ? 'Apply ↗' : 'আবেদন ↗' }}</a>
                        </div>
                    </article>
                </div>
                <div v-else class="text-center py-8 rounded-2xl bg-gray-50 dark:bg-[#171c28] border border-gray-200 dark:border-white/5 text-xs text-gray-500 dark:text-gray-400">
                    <Link href="/job-news" class="text-[var(--primary)] font-bold">{{ en ? 'Visit job circulars page to view all circulars →' : 'চাকরির পেজে গিয়ে সকল সার্কুলার দেখুন →' }}</Link>
                </div>
            </section>

            <!-- SECTION: Target-Based Preparation Bundles (Admission & Job 3 / 6 Months) -->
            <section id="bundles" class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24 border-t border-gray-200 dark:border-white/5">
                <div class="mx-auto mb-10 max-w-3xl text-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[var(--primary)]/15 border border-[var(--primary)]/30 text-[var(--primary)] text-xs font-bold uppercase tracking-wider mb-3">
                        {{ en ? 'SPECIAL PREPARATION BUNDLES' : 'লক্ষ্যভিত্তিক স্পেশাল বান্ডেল' }}
                    </span>
                    <h2 class="font-heading text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        {{ en ? 'Goal-Oriented Preparation Bundles' : 'লক্ষ্যভিত্তিক স্পেশাল প্রিপারেশন বান্ডেল' }}
                    </h2>
                    <p class="mt-3 text-sm md:text-base leading-relaxed text-[var(--text-muted)]">
                        {{ en ? 'Complete 3-month and 6-month specialized preparation packages for university admission, BCS, and bank exams.' : 'এইচএসসি পরবর্তী ভর্তি পরীক্ষা এবং বিসিএস ও ব্যাংক জব প্রস্তুতির জন্য ৩ মাস বা ৬ মাসের কমপ্লিট প্যাকেজ।' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
                    <!-- BUNDLE 1: ADMISSION PREPARATION (BUET, Medical, DU) -->
                    <div class="rounded-3xl border border-amber-500/30 bg-white dark:bg-gradient-to-b dark:from-[#1d1f2e] dark:via-[#161a26] dark:to-[#0e121d] p-7 md:p-9 shadow-xl relative flex flex-col justify-between overflow-hidden">
                        <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-amber-500/10 blur-[80px]"></div>

                        <div>
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <span class="px-3 py-1 rounded-full bg-amber-500/15 text-amber-600 dark:text-amber-300 font-mono text-xs font-bold uppercase tracking-wider">
                                    {{ en ? 'Admission Special (3 & 6 Months)' : 'ভর্তি পরীক্ষা স্পেশাল (৩ ও ৬ মাস)' }}
                                </span>
                                <span class="text-xs font-bold text-gray-700 dark:text-white bg-gray-100 dark:bg-white/10 px-3 py-1 rounded-full">BUET · Medical · DU · GST</span>
                            </div>

                            <h3 class="font-heading text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">
                                {{ en ? 'University Admission Bundle' : 'অ্যাডমিশন প্রিপারেশন বান্ডেল' }}
                            </h3>
                            <p class="mt-2 text-xs md:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                {{ en ? 'Continuous top-tier AI reasoning and problem breakdown for tricky equations in engineering and medical entrance tests.' : 'শীর্ষ প্রকৌশল ও মেডিকেল ভর্তি পরীক্ষার কঠিন সমীকরণ এবং ট্রিকি প্রশ্ন সমাধানে টপ-টায়ার হাই-রিজনিং AI মডেলের সার্বক্ষণিক সহায়তা।' }}
                            </p>

                            <!-- Features List -->
                            <div class="mt-6 space-y-3 text-xs md:text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined !text-base text-amber-500 dark:text-amber-400 mt-0.5">check_circle</span>
                                    <span><strong>{{ en ? '25-Year Solved Archive:' : 'বিগত ২৫ বছরের প্রশ্নব্যাংক:' }}</strong> {{ en ? 'Solved questions from BUET, DU A-Unit, and Medical.' : 'বুয়েট, ঢাবি ‘ক’ ইউনিট ও মেডিকেল ভর্তি পরীক্ষার সমাধান।' }}</span>
                                </div>
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined !text-base text-amber-500 dark:text-amber-400 mt-0.5">check_circle</span>
                                    <span><strong>{{ en ? 'High-Reasoning AI Model:' : 'টপ-টায়ার হাই-রিজনিং AI মডেল:' }}</strong> {{ en ? 'Step-by-step reasoning for tough physics and advanced math equations.' : 'গাণিতিক ও পদার্থবিজ্ঞানের জটিল স্টেপ-বাই-স্টেপ ব্যাখ্যা।' }}</span>
                                </div>
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined !text-base text-amber-500 dark:text-amber-400 mt-0.5">check_circle</span>
                                    <span><strong>{{ en ? 'Unlimited Model Tests:' : 'আনলিমিটেড পূর্ণাঙ্গ মডেল টেস্ট:' }}</strong> {{ en ? 'Realistic exam experience with negative marking and timer.' : 'নেগেটিভ মার্কিংসহ বাস্তবসম্মত ভর্তি পরীক্ষার অভিজ্ঞতা।' }}</span>
                                </div>
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined !text-base text-amber-500 dark:text-amber-400 mt-0.5">check_circle</span>
                                    <span><strong>{{ en ? 'Concept Mastery Tracker:' : 'চ্যাপ্টারভিত্তিক কনসেপ্ট ট্র্যাকার:' }}</strong> {{ en ? 'AI recommendations detecting and fixing weak chapters.' : 'দুর্বল অধ্যায় চিহ্নিত করে এআই রিকমেন্ডেশন।' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <span class="text-xs text-amber-600 dark:text-amber-400 font-bold block">{{ en ? '10% OFF on 3 months & 20% MEGA OFF on 6 months!' : '৩ মাসে ১০% এবং ৬ মাসে ২০% মেগা ছাড়!' }}</span>
                                <span class="font-heading text-lg font-bold text-gray-900 dark:text-white">{{ en ? 'Included in Pro and Elite Plans' : 'প্রো ও এলিট প্যাকেজে অন্তর্ভুক্ত' }}</span>
                            </div>
                            <Link
                                href="/plans"
                                class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white text-xs font-bold shadow-lg shadow-orange-500/20 text-center"
                            >
                                {{ en ? 'Start Admission Bundle →' : 'অ্যাডমিশন বান্ডেল শুরু করুন →' }}
                            </Link>
                        </div>
                    </div>

                    <!-- BUNDLE 2: JOB & BCS PREPARATION -->
                    <div class="rounded-3xl border border-emerald-500/30 bg-white dark:bg-gradient-to-b dark:from-[#162524] dark:via-[#121c22] dark:to-[#0e121d] p-7 md:p-9 shadow-xl relative flex flex-col justify-between overflow-hidden">
                        <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-emerald-500/10 blur-[80px]"></div>

                        <div>
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <span class="px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-300 font-mono text-xs font-bold uppercase tracking-wider">
                                    {{ en ? 'Job Exam Special (3 & 6 Months)' : 'চাকরি পরীক্ষা স্পেশাল (৩ ও ৬ মাস)' }}
                                </span>
                                <span class="text-xs font-bold text-gray-700 dark:text-white bg-gray-100 dark:bg-white/10 px-3 py-1 rounded-full">BCS · Bank · Primary · Govt</span>
                            </div>

                            <h3 class="font-heading text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">
                                {{ en ? 'Job & BCS Preparation Bundle' : 'জব ও বিসিএস প্রিপারেশন বান্ডেল' }}
                            </h3>
                            <p class="mt-2 text-xs md:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                {{ en ? 'Targeted question bank and daily practice for BCS preliminary, written, state bank, and primary teacher recruitment.' : 'বিসিএস প্রিলিমিনারি, রিটেন, রাষ্ট্রায়ত্ত ব্যাংক ও প্রাথমিক সহকারী শিক্ষক নিয়োগ পরীক্ষার প্রশ্নব্যাংক ও স্পেশাল ডেইলি প্র্যাকটিস।' }}
                            </p>

                            <!-- Features List -->
                            <div class="mt-6 space-y-3 text-xs md:text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined !text-base text-emerald-500 dark:text-emerald-400 mt-0.5">check_circle</span>
                                    <span><strong>{{ en ? '20-Year BCS & Bank Archives:' : 'বিগত ২০ বছরের বিসিএস ও ব্যাংক জব প্রশ্নব্যাংক:' }}</strong> {{ en ? 'Subject-wise solved questions with detailed explanations.' : 'ব্যাখ্যাসহ বিষয়ভিত্তিক সমাধান।' }}</span>
                                </div>
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined !text-base text-emerald-500 dark:text-emerald-400 mt-0.5">check_circle</span>
                                    <span><strong>{{ en ? 'Written Paper AI Feedback:' : 'লিখিত সৃজনশীল খাতার AI মূল্যায়ন:' }}</strong> {{ en ? 'Constructive feedback on English essays, translations, and composition.' : 'ইংরেজি রচনা, অনুবাদ ও বাংলা সাহিত্যের গঠনমূলক ফিডব্যাক।' }}</span>
                                </div>
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined !text-base text-emerald-500 dark:text-emerald-400 mt-0.5">check_circle</span>
                                    <span><strong>{{ en ? 'Job Alerts & Age Calculator:' : 'জব সার্কুলার এলার্ট ও বয়স ক্যালকুলেটর:' }}</strong> {{ en ? 'Track deadlines, age eligibility, and application guidelines.' : 'আবেদনের শেষ তারিখ ও বয়সসীমা ট্র্যাকিং।' }}</span>
                                </div>
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined !text-base text-emerald-500 dark:text-emerald-400 mt-0.5">check_circle</span>
                                    <span><strong>{{ en ? 'Recent GK & Monthly Digest:' : 'সাম্প্রতিক সাধারণ জ্ঞান ও মাসিক ডাইজেস্ট:' }}</strong> {{ en ? 'Quizzes on Bangladesh and international current affairs.' : 'বাংলাদেশ ও আন্তর্জাতিক বিষয়ক কুইজ।' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold block">{{ en ? '10% OFF on 3 months & 20% MEGA OFF on 6 months!' : '৩ মাসে ১০% এবং ৬ মাসে ২০% মেগা ছাড়!' }}</span>
                                <span class="font-heading text-lg font-bold text-gray-900 dark:text-white">{{ en ? 'Affordable Monthly & Multi-Month Plans' : 'সাশ্রয়ী মাসিক ও কোয়ার্টারলি প্ল্যান' }}</span>
                            </div>
                            <Link
                                href="/plans"
                                class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold shadow-lg shadow-emerald-500/20 text-center"
                            >
                                {{ en ? 'Start Job Bundle →' : 'জব বান্ডেল শুরু করুন →' }}
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Latest courses -->
            <section id="courses" class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24 border-t border-white/5">
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[var(--primary)]">{{ en ? 'Mentor-led learning · Courses' : 'মেন্টর পরিচালিত · অনলাইন কোর্স' }}</span>
                        <h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight">{{ en ? 'Top courses' : 'সেরা কোর্সসমূহ' }}</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]">{{ en ? 'Follow courses taught by experienced mentors who explain each step clearly and keep lessons grounded in practice.' : 'অভিজ্ঞ মেন্টরদের পরিচালিত কোর্স করে প্রতিটি বিষয় সহজে ও বাস্তব অনুশীলনের মাধ্যমে আয়ত্ত করুন।' }}</p>
                    </div>
                    <Link href="/courses" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--primary)] transition hover:text-[#ffb690]">{{ en ? 'Explore all courses' : 'সব কোর্স দেখুন' }} <span class="material-symbols-outlined">arrow_forward</span></Link>
                </div>
                <div v-if="topCourses.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <article v-for="(course, i) in topCourses" :key="course.id" class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-white/5 bg-[#1c1f2a]/80 shadow-lg transition duration-300 hover:-translate-y-1.5 hover:border-white/10 hover:shadow-2xl">
                        <div>
                            <div class="relative h-44 overflow-hidden bg-gradient-to-br" :class="coursePalette[i % coursePalette.length]">
                                <img v-if="course.cover_image_url" :src="course.cover_image_url" :alt="course.title" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                <div v-else class="absolute inset-0 grid place-items-center text-5xl text-white/60"><span class="material-symbols-outlined !text-5xl">school</span></div>
                                <span class="absolute left-3 top-3 rounded-full bg-[#0a0e18]/80 px-2.5 py-1 font-mono text-[10px] uppercase tracking-wide text-[#ff9a52] backdrop-blur">{{ course.level || (en ? 'COURSE' : 'কোর্স') }}</span>
                            </div>
                            <div class="p-4 md:p-5">
                                <div class="font-mono text-[10px] uppercase tracking-wider text-[var(--primary)]">{{ course.mentor?.name || (en ? 'Experienced mentor' : 'অভিজ্ঞ মেন্টর') }}</div>
                                <h3 class="mt-1.5 line-clamp-2 min-h-12 text-base font-bold leading-snug transition group-hover:text-[var(--primary)]">{{ course.title }}</h3>
                            </div>
                        </div>
                        <div class="border-t border-white/5 bg-[#171b26]/60 px-4 py-3 md:px-5 flex items-center justify-between">
                            <span class="font-heading text-lg font-bold text-[#4cd7f6]">{{ Number(course.price) === 0 ? (en ? 'Free' : 'ফ্রি') : `৳${course.price}` }}</span>
                            <Link :href="`/courses/${course.slug}`" class="text-xs font-semibold text-white transition hover:text-[var(--primary)]">{{ en ? 'View course' : 'কোর্স দেখুন' }} →</Link>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Latest books -->
            <section id="library" class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24 border-t border-white/5">
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[#4cd7f6]">{{ en ? 'Digital library · Read and explore' : 'ডিজিটাল লাইব্রেরি · পড়ুন ও জানুন' }}</span>
                        <h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight">{{ en ? 'Latest books' : 'সর্বশেষ বই' }}</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]">{{ en ? 'Find a new book to read, revisit a familiar subject, or explore a topic that caught your attention.' : 'নতুন বই পড়ুন, পরিচিত বিষয় আবার দেখুন কিংবা আগ্রহ জাগানো কোনো বিষয় ঘুরে দেখুন।' }}</p>
                    </div>
                    <Link href="/library" class="inline-flex items-center gap-2 text-sm font-semibold text-[#4cd7f6] transition hover:text-white">{{ en ? 'Browse the library' : 'লাইব্রেরি দেখুন' }} <span class="material-symbols-outlined">arrow_forward</span></Link>
                </div>
                <div v-if="popularBooks.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-5">
                    <Link v-for="(book, i) in popularBooks.slice(0, 5)" :key="book.id" :href="`/library/${book.slug}`" class="group overflow-hidden rounded-2xl border border-white/5 bg-[#1c1f2a]/80 shadow-lg transition duration-300 hover:-translate-y-1 hover:border-white/10 hover:shadow-2xl">
                        <div class="relative aspect-[3/4] overflow-hidden bg-gradient-to-br" :class="bookPalette[i % bookPalette.length]"><img v-if="book.cover_image_url" :src="book.cover_image_url" :alt="book.title" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"><div v-else class="absolute inset-0 grid place-items-center text-5xl text-white/60"><span class="material-symbols-outlined !text-5xl">menu_book</span></div><span v-if="book.is_free" class="absolute left-2.5 top-2.5 rounded-full bg-[#0e513d]/90 px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wide text-[#6ee7b7]">{{ en ? 'Free' : 'ফ্রি' }}</span><span class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-[#0a0e18]/80 to-transparent"></span></div>
                        <div class="p-3.5 md:p-4"><div class="font-mono text-[9px] uppercase tracking-wider text-[#4cd7f6]">{{ book.level || (en ? 'DIGITAL BOOK' : 'ডিজিটাল বই') }}</div><h3 class="mt-1.5 line-clamp-2 min-h-10 text-sm font-semibold leading-snug transition group-hover:text-[var(--primary)]">{{ book.title }}</h3><div class="mt-2 truncate text-[10px] text-[var(--text-muted)]">{{ book.author?.name || (en ? 'Sikhun library' : 'Sikhun লাইব্রেরি') }}</div><div v-if="book.total_pages" class="mt-1 text-[10px] text-[var(--text-muted)]">{{ book.total_pages }} {{ en ? 'pages' : 'পৃষ্ঠা' }}</div></div>
                    </Link>
                </div>
            </section>

            <!-- 4 Subscription Plans Section -->
            <section id="pricing" class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24 border-t border-white/5">
                <div class="mx-auto mb-10 max-w-2xl text-center">
                    <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-4 py-1.5 text-xs font-bold text-emerald-400 mb-3">
                        <span class="material-symbols-outlined !text-sm text-emerald-400">verified</span>
                        <span>{{ en ? 'Start with Free Plan — No payment required' : 'বিনামূল্যে ফ্রি প্ল্যান দিয়ে শুরু করুন — কোনো পেমেন্ট ছাড়াই ইনস্ট্যান্ট অ্যাক্টিভেশন' }}</span>
                    </div>
                    <h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight text-white">{{ en ? 'Choose the support that fits you' : 'আপনার উপযোগী সহায়তা বেছে নিন' }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-[var(--text-muted)]">{{ en ? 'Compare the available plans and choose what works for your learning goals.' : 'উপলভ্য ৪টি প্ল্যান দেখে আপনার প্রয়োজন অনুযায়ী সেরা প্ল্যানটি বেছে নিন।' }}</p>
                </div>

                <div v-if="plans.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 items-stretch">
                    <article
                        v-for="(plan, i) in plans"
                        :key="plan.id"
                        class="relative flex flex-col justify-between rounded-3xl border p-5 md:p-6 shadow-lg transition duration-300 hover:-translate-y-1"
                        :class="[
                            (plan.slug === 'pro' || Number(plan.price_monthly) === 199) ? 'border-[#f97316]/50 bg-[#262a35] shadow-[0_24px_60px_rgba(0,0,0,.35)] ring-2 ring-[var(--primary)]' : 'border-white/5 bg-[#1c1f2a]/75'
                        ]"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <h3 class="text-base font-bold text-white">{{ plan.name }}</h3>
                                <span class="rounded bg-[#0a0e18] px-2 py-0.5 font-mono text-[9px] font-bold uppercase tracking-wider" :class="(plan.slug === 'pro' || Number(plan.price_monthly) === 199) ? 'text-[#ff9a52]' : 'text-[var(--text-muted)]'">
                                    {{ Number(plan.price_monthly) === 0 ? 'FREE' : Number(plan.price_monthly) === 99 ? 'STARTER' : Number(plan.price_monthly) === 199 ? 'POPULAR' : 'ELITE' }}
                                </span>
                            </div>
                            <p v-if="plan.description" class="mt-1 min-h-8 text-xs leading-relaxed text-[var(--text-muted)]">{{ plan.description }}</p>
                            <div class="my-4 flex flex-col">
                                <div class="flex items-baseline gap-1">
                                    <span class="font-heading text-3xl font-extrabold tracking-tight text-white" :class="(plan.slug === 'pro' || Number(plan.price_monthly) === 199) ? 'text-[#ff9a52]' : ''">৳{{ plan.price_monthly }}</span>
                                    <span class="text-xs text-[var(--text-muted)]">{{ en ? '/ month' : '/ মাস' }}</span>
                                </div>
                                <span class="text-[10px] font-medium text-emerald-400 mt-1">
                                    ✓ {{ Number(plan.price_monthly) === 0 ? (en ? 'Lifetime Free Access' : 'লাইফটাইম ফ্রি অ্যাক্সেস') : (en ? 'Instant activation & full access' : 'ইনস্ট্যান্ট অ্যাক্টিভেশন ও ফুল অ্যাক্সেস') }}
                                </span>
                            </div>

                            <!-- Quotas summary -->
                            <div class="mb-4 grid grid-cols-2 gap-1.5 text-[11px]">
                                <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-[#0e1320] text-gray-800 dark:text-gray-300">
                                    <span class="text-[9px] text-gray-500 block font-bold">AI CHAT</span>
                                    <strong>{{ plan.ai_chat_minutes }} {{ en ? 'mins' : 'মিনিট' }}</strong>
                                </div>
                                <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-[#0e1320] text-gray-800 dark:text-gray-300">
                                    <span class="text-[9px] text-gray-500 block font-bold">VISION SOLVER</span>
                                    <strong>{{ plan.ai_vision_limit || 0 }} {{ en ? 'images' : 'টি ছবি' }}</strong>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 text-xs text-[var(--text-muted)] pt-2 border-t border-white/5">
                                <div v-for="feature in (Array.isArray(plan.features) ? plan.features.slice(0, 5) : [])" :key="feature" class="flex items-start gap-2">
                                    <span class="material-symbols-outlined !text-sm text-[#10b981] mt-0.5">check_circle</span>
                                    <span class="leading-tight">{{ feature }}</span>
                                </div>
                            </div>
                        </div>

                        <Link
                            :href="Number(plan.price_monthly) === 0 ? `/register?plan_id=${plan.id}` : '/plans'"
                            class="mt-6 flex w-full items-center justify-center gap-1.5 rounded-xl px-4 py-2.5 text-xs font-bold transition"
                            :class="(plan.slug === 'pro' || Number(plan.price_monthly) === 199) ? 'bg-gradient-to-r from-[#ff7a00] to-[#f97316] text-white shadow-lg shadow-[#f97316]/20' : 'bg-[#313540] text-[var(--text)] hover:bg-[#3b404d]'"
                        >
                            {{ Number(plan.price_monthly) === 0 ? (en ? 'Start for free' : 'বিনামূল্যে শুরু করুন') : (en ? 'Choose Plan' : 'প্ল্যান বেছে নিন') }} <span>→</span>
                        </Link>
                    </article>
                </div>
            </section>

            <!-- Latest Blog Posts -->
            <section v-if="latestBlogs?.length" id="blog" class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24 border-t border-white/5">
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[#10b981]">{{ en ? 'Insights · Science · AI · Education' : 'বিজ্ঞান · প্রযুক্তি · AI · শিক্ষা' }}</span>
                        <h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight">{{ en ? 'Latest from the blog' : 'সর্বশেষ ব্লগ আর্টিকেল' }}</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]">{{ en ? 'Read Bengali articles on science, technology, artificial intelligence, and study tips.' : 'বিজ্ঞান, প্রযুক্তি, কৃত্রিম বুদ্ধিমত্তা ও পড়াশোনার কৌশল নিয়ে আমাদের সাম্প্রতিক বাংলা আর্টিকেলগুলো পড়ুন।' }}</p>
                    </div>
                    <Link href="/blog" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--primary)] transition hover:text-[#ffb690]">{{ en ? 'Read all articles' : 'সব ব্লগ পড়ুন' }} <span class="material-symbols-outlined">arrow_forward</span></Link>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <Link v-for="(post, i) in latestBlogs.slice(0, 3)" :key="post.id" :href="`/blog/${post.slug}`" class="group flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-[#1c1f2a]/80 shadow-lg transition duration-300 hover:-translate-y-1.5 hover:border-white/10 hover:shadow-2xl">
                        <div class="relative h-44 overflow-hidden bg-gradient-to-br" :class="coursePalette[i % coursePalette.length]">
                            <img v-if="post.featured_image_url" :src="post.featured_image_url" :alt="post.title" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            <div v-else class="absolute inset-0 grid place-items-center text-5xl text-white/60"><span class="material-symbols-outlined !text-5xl">article</span></div>
                        </div>
                        <div class="p-4 md:p-5 flex flex-col flex-1 justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-[var(--primary)] uppercase tracking-wider">{{ post.category?.name_bn || post.category?.name_en || (en ? 'Article' : 'আর্টিকেল') }}</span>
                                <h3 class="mt-1 text-sm font-bold text-white group-hover:text-[var(--primary)] transition line-clamp-2">{{ post.title }}</h3>
                                <p class="mt-2 text-xs text-gray-400 line-clamp-2 leading-relaxed">{{ post.excerpt }}</p>
                            </div>
                            <span class="mt-4 text-[11px] text-gray-400 font-semibold flex items-center gap-1">{{ en ? 'Read' : 'পড়ুন' }} <span class="material-symbols-outlined !text-xs">arrow_forward</span></span>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
