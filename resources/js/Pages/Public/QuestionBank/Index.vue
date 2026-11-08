<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { renderMath } from '@/Utils/renderMath';
import { useI18n } from '@/i18n';
import {
    AcademicCapIcon,
    BriefcaseIcon,
    MagnifyingGlassIcon,
    SparklesIcon,
    CheckCircleIcon,
    XCircleIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    TagIcon,
    LockClosedIcon,
    ShieldCheckIcon,
    BookOpenIcon,
    UserGroupIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    questions: Object,
    subjects: Array,
    origins: Array,
    years: Array,
    counts: Object,
    currentSegment: String,
    studentType: String,
    filters: Object,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');

const filterForm = reactive({
    search: props.filters.search || '',
    subject: props.filters.subject || 'all',
    origin: props.filters.origin || 'all',
    difficulty: props.filters.difficulty || 'all',
    year: props.filters.year || 'all',
    segment: props.currentSegment || 'admission',
});

function applyFilters() {
    router.get('/question-bank', filterForm, {
        preserveState: true,
        replace: true,
    });
}

function setSegment(seg) {
    filterForm.segment = seg;
    filterForm.subject = 'all';
    filterForm.origin = 'all';
    applyFilters();
}

function selectSubject(slug) {
    filterForm.subject = filterForm.subject === slug ? 'all' : slug;
    applyFilters();
}

function selectOrigin(originTag) {
    filterForm.origin = filterForm.origin === originTag ? 'all' : originTag;
    applyFilters();
}

// Student interactive practice state
const userSelectedOptions = ref({});
const revealedSolutions = ref(new Set());

function selectOption(qId, key, isLocked) {
    if (isLocked) return;
    userSelectedOptions.value[qId] = key;
    revealedSolutions.value.add(qId);
}

function toggleSolution(qId, isLocked) {
    if (isLocked) return;
    if (revealedSolutions.value.has(qId)) {
        revealedSolutions.value.delete(qId);
    } else {
        revealedSolutions.value.add(qId);
    }
}

function getStudentTypeBadge(type) {
    const map = {
        ssc: en.value ? 'SSC' : 'এসএসসি (SSC)',
        hsc: en.value ? 'HSC' : 'এইচএসসি (HSC)',
        admission: en.value ? 'University Admission' : 'বিশ্ববিদ্যালয় ভর্তি (Admission)',
        job: en.value ? 'BCS & Govt Jobs' : 'বিসিএস ও চাকরি (Job)',
        university: en.value ? 'University' : 'বিশ্ববিদ্যালয় (University)',
    };
    return map[type] || (en.value ? 'Student' : 'শিক্ষার্থী');
}
</script>

<template>
    <StudentLayout>
        <Head :title="en ? 'Question Bank — Admission & Job Preparation' : 'প্রশ্ন ব্যাংক — বিশ্ববিদ্যালয় ভর্তি ও চাকরি প্রস্তুতি (Question Bank)'" />

        <div class="space-y-6 pb-12">
            <!-- Hero Banner -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-primary/95 to-indigo-950 text-white p-6 sm:p-10 shadow-xl border border-white/10">
                <div class="relative z-10 max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold tracking-wider text-emerald-300 border border-white/15">
                        <SparklesIcon class="w-4 h-4" />
                        <span>{{ en ? 'Target: ' : 'আপনার লক্ষ্য: ' }}{{ getStudentTypeBadge(studentType) }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight leading-tight">
                        <template v-if="en">Chapter-wise Question Bank & <span class="text-emerald-400">AI Explanations</span></template>
                        <template v-else>অধ্যায়ভিত্তিক প্রশ্ন ব্যাংক ও <span class="text-emerald-400">AI বিশ্লেষণ সমাধান</span></template>
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-200/90 leading-relaxed">
                        {{ en
                            ? 'Comprehensive categorized question bank from DU, BUET, Medical, 46th & previous BCS, and Bank recruitment exams with instant interactive practice.'
                            : 'ঢাকা বিশ্ববিদ্যালয়, বুয়েট, মেডিক্যাল, ৪৬তম ও বিগত বিসিএস এবং ব্যাংক রিক্রুটমেন্ট পরীক্ষার প্রশ্নের সম্পূর্ণ শ্রেণীবদ্ধ অনুশীলন ভাণ্ডার।'
                        }}
                    </p>
                </div>

                <!-- Abstract Decorative Shapes -->
                <div class="absolute -right-12 -bottom-12 w-80 h-80 bg-primary/30 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute right-1/4 -top-12 w-60 h-60 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- Segment Switcher (Admission vs Job) -->
            <div class="flex items-center justify-center p-1.5 bg-gray-100 dark:bg-gray-800 rounded-2xl max-w-md mx-auto border border-gray-200 dark:border-gray-700 shadow-inner">
                <button
                    type="button"
                    @click="setSegment('admission')"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all"
                    :class="filterForm.segment === 'admission'
                        ? 'bg-white dark:bg-gray-900 text-primary shadow-sm'
                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                >
                    <AcademicCapIcon class="w-4 h-4" />
                    <span>{{ en ? 'Admission' : 'বিশ্ববিদ্যালয় ভর্তি' }} ({{ counts.admission }})</span>
                </button>
                <button
                    type="button"
                    @click="setSegment('job')"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all"
                    :class="filterForm.segment === 'job'
                        ? 'bg-white dark:bg-gray-900 text-emerald-600 dark:text-emerald-400 shadow-sm'
                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                >
                    <BriefcaseIcon class="w-4 h-4" />
                    <span>{{ en ? 'Jobs & BCS' : 'চাকরি ও বিসিএস' }} ({{ counts.job }})</span>
                </button>
            </div>

            <!-- Subject Category Pills -->
            <div class="space-y-2">
                <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ en ? 'Select Subject:' : 'বিষয় বেছে নিন:' }}
                </div>
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                    <button
                        type="button"
                        @click="selectSubject('all')"
                        class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all border"
                        :class="filterForm.subject === 'all'
                            ? 'bg-primary text-white border-primary shadow-sm shadow-primary/25'
                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-primary/50'"
                    >
                        {{ en ? 'All Subjects' : 'সব বিষয়' }}
                    </button>
                    <button
                        v-for="sub in subjects"
                        :key="sub.id"
                        type="button"
                        @click="selectSubject(sub.slug)"
                        class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all border"
                        :class="filterForm.subject === sub.slug
                            ? 'bg-primary text-white border-primary shadow-sm shadow-primary/25'
                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-primary/50'"
                    >
                        {{ en ? (sub.name_en || sub.name_bn) : sub.name_bn }}
                    </button>
                </div>
            </div>

            <!-- Origin Tags Chips (e.g. DU-24, BCS-46) -->
            <div v-if="origins && origins.length > 0" class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 shrink-0">{{ en ? 'Exam Filter:' : 'পরীক্ষা ফিল্টার:' }}</span>
                <button
                    v-for="tag in origins"
                    :key="tag"
                    type="button"
                    @click="selectOrigin(tag)"
                    class="px-3 py-1 rounded-lg text-xs font-bold whitespace-nowrap border transition-all"
                    :class="filterForm.origin === tag
                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                        : 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-950/50'"
                >
                    {{ tag }}
                </button>
            </div>

            <!-- Search Bar -->
            <div class="relative max-w-xl">
                <MagnifyingGlassIcon class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" />
                <input
                    v-model="filterForm.search"
                    @keyup.enter="applyFilters"
                    type="text"
                    :placeholder="en ? 'Search any keyword or question stem...' : 'প্রশ্নের যেকোনো অংশ লিখে খুঁজুন...'"
                    class="w-full pl-11 pr-24 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm"
                />
                <button
                    type="button"
                    @click="applyFilters"
                    class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-1.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90"
                >
                    {{ en ? 'Search' : 'খুঁজুন' }}
                </button>
            </div>

            <!-- Questions Practice Feed -->
            <div class="space-y-6">
                <div
                    v-for="(q, index) in questions.data"
                    :key="q.id"
                    class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6 sm:p-7 shadow-sm hover:shadow-md transition-shadow space-y-4"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center flex-wrap gap-2">
                            <!-- Question index -->
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs">
                                {{ (questions.current_page - 1) * questions.per_page + index + 1 }}
                            </span>

                            <!-- Origin Tag Badge -->
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg font-black text-xs bg-primary/10 text-primary border border-primary/20">
                                <TagIcon class="w-3.5 h-3.5" />
                                <span>{{ q.origin_tag }}</span>
                            </span>

                            <!-- Subject & Topic -->
                            <span class="font-bold text-gray-800 dark:text-gray-200">
                                {{ en ? (q.subject?.name_en || q.subject?.name_bn) : q.subject?.name_bn }}
                                <span v-if="q.topic_name" class="font-normal text-gray-400">· {{ q.topic_name }}</span>
                            </span>

                            <!-- Locked Status Badge -->
                            <span
                                v-if="q.is_locked"
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60"
                            >
                                <LockClosedIcon class="w-3 h-3" />
                                <span>{{ en ? 'Subscription Required' : 'সাবস্ক্রিপশন সংরক্ষিত' }}</span>
                            </span>
                        </div>

                        <span class="text-gray-400 text-[11px]">
                            {{ q.institution }}<span v-if="q.year">, {{ q.year }}</span>
                        </span>
                    </div>

                    <!-- Question Stem -->
                    <div
                        class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white leading-relaxed pt-1"
                        v-html="renderMath(q.question_text)"
                    ></div>

                    <!-- Interactive Options -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <button
                            v-for="opt in q.options"
                            :key="opt.key"
                            type="button"
                            @click="selectOption(q.id, opt.key, q.is_locked)"
                            class="flex items-start gap-3 p-3.5 rounded-2xl border text-left text-sm transition-all"
                            :class="{
                                // Locked state
                                'opacity-85 cursor-not-allowed bg-gray-50/50 dark:bg-gray-900/30 border-gray-200 dark:border-gray-700': q.is_locked,
                                // Correct and selected (when unlocked)
                                'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-950 dark:text-emerald-100 shadow-sm ring-2 ring-emerald-500/20 font-bold cursor-pointer': !q.is_locked && userSelectedOptions[q.id] && opt.key.toUpperCase() === q.correct_option?.toUpperCase(),
                                // Wrong selected (when unlocked)
                                'bg-rose-50 dark:bg-rose-950/40 border-rose-400 text-rose-900 dark:text-rose-200 ring-2 ring-rose-400/20 cursor-pointer': !q.is_locked && userSelectedOptions[q.id] === opt.key && opt.key.toUpperCase() !== q.correct_option?.toUpperCase(),
                                // Unselected standard (when unlocked)
                                'bg-gray-50/70 dark:bg-gray-900/50 border-gray-200/80 dark:border-gray-700/80 text-gray-800 dark:text-gray-200 hover:border-primary/50 hover:bg-gray-100/50 cursor-pointer': !q.is_locked && !userSelectedOptions[q.id],
                                // Other options once chosen
                                'opacity-70 bg-gray-50/40 dark:bg-gray-900/30 border-gray-200 dark:border-gray-700 cursor-pointer': !q.is_locked && userSelectedOptions[q.id] && userSelectedOptions[q.id] !== opt.key && opt.key.toUpperCase() !== q.correct_option?.toUpperCase()
                            }"
                        >
                            <span
                                class="inline-flex items-center justify-center w-7 h-7 rounded-xl text-xs font-black shrink-0 transition-colors"
                                :class="{
                                    'bg-emerald-600 text-white shadow-sm': !q.is_locked && userSelectedOptions[q.id] && opt.key.toUpperCase() === q.correct_option?.toUpperCase(),
                                    'bg-rose-600 text-white': !q.is_locked && userSelectedOptions[q.id] === opt.key && opt.key.toUpperCase() !== q.correct_option?.toUpperCase(),
                                    'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300': q.is_locked || (!userSelectedOptions[q.id] || (userSelectedOptions[q.id] !== opt.key && opt.key.toUpperCase() !== q.correct_option?.toUpperCase()))
                                }"
                            >
                                {{ opt.key }}
                            </span>
                            <div class="flex-1 leading-snug pt-1" v-html="renderMath(opt.text)"></div>
                            <span
                                v-if="!q.is_locked && userSelectedOptions[q.id] && opt.key.toUpperCase() === q.correct_option?.toUpperCase()"
                                class="text-emerald-600 dark:text-emerald-400 text-xs font-black shrink-0 self-center"
                            >
                                {{ en ? '✓ Correct' : '✓ সঠিক' }}
                            </span>
                        </button>
                    </div>

                    <!-- Locked Overlay Banner (Freemium Upsell CTA) -->
                    <div
                        v-if="q.is_locked"
                        class="mt-3 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-500/[0.08] to-primary/[0.06] border border-amber-200/80 dark:border-amber-800/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-sm">
                                <LockClosedIcon class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">
                                    {{ en ? '🔒 Subscription required to view verified answer and AI concept breakdown' : '🔒 এই প্রশ্নের সঠিক উত্তর ও AI কনসেপ্ট ব্যাখ্যা দেখতে সাবস্ক্রিপশন প্রয়োজন' }}
                                </div>
                                <div class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ en ? 'Upgrade your plan to unlock complete mathematical solutions and detailed explanations.' : 'পূর্ণাঙ্গ গাণিতিক সমাধান ও বিশ্লেষণ আনলক করতে প্ল্যান আপগ্রেড করুন।' }}
                                </div>
                            </div>
                        </div>
                        <Link
                            href="/plans"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-600 to-primary text-white text-xs font-bold shadow-md shadow-amber-500/20 hover:opacity-95 whitespace-nowrap transition active:scale-95"
                        >
                            <SparklesIcon class="w-3.5 h-3.5" />
                            <span>{{ en ? 'Upgrade Plan' : 'প্ল্যান আপগ্রেড করুন' }}</span>
                        </Link>
                    </div>

                    <!-- Solution & Concept Toggle (when unlocked) -->
                    <div v-else class="pt-3 border-t border-gray-100 dark:border-gray-700/60">
                        <button
                            type="button"
                            @click="toggleSolution(q.id, false)"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline transition"
                        >
                            <SparklesIcon class="w-4 h-4" />
                            <span>
                                {{ en
                                    ? (revealedSolutions.has(q.id) ? 'Hide Solution & Concept' : 'View Verified Solution & AI Explanation')
                                    : (revealedSolutions.has(q.id) ? 'সমাধান ও কনসেপ্ট বন্ধ করুন' : 'বিস্তারিত সমাধান ও AI কনসেপ্ট ব্যাখ্যা দেখুন')
                                }}
                            </span>
                            <component :is="revealedSolutions.has(q.id) ? ChevronUpIcon : ChevronDownIcon" class="w-3.5 h-3.5" />
                        </button>

                        <div
                            v-if="revealedSolutions.has(q.id)"
                            class="mt-3 p-4 sm:p-5 bg-gradient-to-br from-primary/[0.04] to-indigo-50/30 dark:from-primary/[0.08] dark:to-indigo-950/20 rounded-2xl border border-primary/20 text-xs text-gray-800 dark:text-gray-200 leading-relaxed space-y-2.5 animate-in fade-in"
                        >
                            <div class="flex items-center justify-between pb-2 border-b border-primary/10">
                                <div class="font-black text-primary flex items-center gap-1.5">
                                    <SparklesIcon class="w-4 h-4" />
                                    <span>{{ en ? `Correct Answer: Option (${q.correct_option})` : `সঠিক উত্তর: অপশন (${q.correct_option})` }}</span>
                                </div>
                                <span class="text-[11px] text-gray-400 font-mono">Verified Solution</span>
                            </div>
                            <div
                                class="pt-1 whitespace-pre-wrap leading-relaxed text-xs sm:text-sm text-gray-800 dark:text-gray-200"
                                v-html="renderMath(q.explanation || (en ? 'No detailed explanation added for this question yet.' : 'এই প্রশ্নের জন্য এখনো কোনো বিস্তারিত সমাধান যুক্ত করা হয়নি।'))"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!questions.data || questions.data.length === 0" class="text-center py-20 bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-8">
                    <AcademicCapIcon class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                    <div class="text-lg font-bold text-gray-800 dark:text-gray-200">
                        {{ en ? 'No questions found' : 'কোনো প্রশ্ন খুঁজে পাওয়া যায়নি' }}
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        {{ en
                            ? 'Questions for this category are being prepared. Select another subject or try different keywords.'
                            : 'আপনার ক্যাটাগরির জন্য এই বিষয়ে প্রশ্ন প্রস্তুত করা হচ্ছে। অন্য কোনো বিষয় সিলেক্ট করুন অথবা কিওয়ার্ড পরিবর্তন করুন।'
                        }}
                    </p>
                </div>

                <!-- Pagination -->
                <div v-if="questions.links && questions.links.length > 3" class="pt-4">
                    <Pagination :links="questions.links" />
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
