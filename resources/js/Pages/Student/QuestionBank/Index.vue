<script setup>
import { ref, computed } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { renderMath } from '@/Utils/renderMath';
import axios from 'axios';
import {
    AcademicCapIcon,
    BookOpenIcon,
    SparklesIcon,
    BoltIcon,
    FunnelIcon,
    MagnifyingGlassIcon,
    CheckCircleIcon,
    XCircleIcon,
    LockClosedIcon,
    ClockIcon,
    TrophyIcon,
    ArrowPathIcon,
    ChevronDownIcon,
    LightBulbIcon,
    BriefcaseIcon,
    XMarkIcon,
    ArrowRightIcon,
    RectangleStackIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    questions: Object,
    subjects: Array,
    exams: Array,
    filters: Object,
    isPaid: Boolean,
    studentType: String,
    activeSector: { type: String, default: 'admission' },
    sectorCounts: { type: Object, default: () => ({ admission: 0, job: 0, hsc: 0, ssc: 0 }) },
});

const BN_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
function toBnDigits(num) {
    if (num === null || num === undefined) return '';
    return String(num).replace(/[0-9]/g, (d) => BN_DIGITS[Number(d)]);
}

// Sector Configuration
const sectors = [
    {
        key: 'admission',
        title: 'বিশ্ববিদ্যালয় ভর্তি প্রশ্নব্যাংক',
        sub: 'ঢাবি, রাবি, চবি, বুয়েট, মেডিকেল ও গুচ্ছ ভর্তি পরীক্ষার পূর্ণাঙ্গ প্রশ্ন সমাধান',
        shortTitle: 'ভর্তি পরীক্ষা',
        badge: 'ভর্তি প্রস্তুতি',
        icon: AcademicCapIcon,
        color: 'from-orange-500 to-amber-600',
    },
    {
        key: 'job',
        title: 'বিসিএস ও সরকারি চাকরি প্রশ্নব্যাংক',
        sub: 'বিসিএস প্রিলিমিনারি, সমন্বিত সরকারি ব্যাংক, বিভিন্ন মন্ত্রণালয় ও বিপিএসসি বিগত প্রশ্ন',
        shortTitle: 'বিসিএস ও চাকরি',
        badge: 'চাকরি প্রস্তুতি',
        icon: BriefcaseIcon,
        color: 'from-blue-600 to-indigo-600',
    },
    {
        key: 'hsc',
        title: 'উচ্চ মাধ্যমিক (HSC) প্রশ্নব্যাংক',
        sub: 'এইচএসসি বিজ্ঞান, মানবিক ও ব্যবসায় শিক্ষা বোর্ড প্রশ্ন ও শীর্ষ কলেজ টেস্ট পেপার',
        shortTitle: 'এইচএসসি (HSC)',
        badge: 'HSC প্রস্তুতি',
        icon: BookOpenIcon,
        color: 'from-emerald-600 to-teal-600',
    },
    {
        key: 'ssc',
        title: 'মাধ্যমিক (SSC) প্রশ্নব্যাংক',
        sub: 'এসএসসি সকল শিক্ষা বোর্ডের বিগত বছরের প্রশ্ন ও অধ্যায়ভিত্তিক অনুশীলন',
        shortTitle: 'এসএসসি (SSC)',
        badge: 'SSC প্রস্তুতি',
        icon: BookOpenIcon,
        color: 'from-purple-600 to-pink-600',
    },
];

const currentSector = ref(props.activeSector || 'admission');
const activeSectorData = computed(() => {
    return sectors.find((s) => s.key === currentSector.value) || sectors[0];
});

// State for active practice interaction
const selectedAnswers = ref({}); // { [questionId]: 'A' }
const aiExplanations = ref({});  // { [questionId]: { text: '...', loading: false, show: true } }
const searchInput = ref(props.filters?.search || '');
const activeMode = ref(props.filters?.mode || 'all'); // 'all', 'subject', 'exam'
const selectedSubjectId = ref(props.filters?.subject_id || '');
const selectedOriginTag = ref(props.filters?.origin_tag || '');

const subjectSearch = ref('');
const examSearch = ref('');
const showMobileDrawer = ref(false);

const filteredSubjects = computed(() => {
    if (!subjectSearch.value.trim()) return props.subjects || [];
    const q = subjectSearch.value.toLowerCase().trim();
    return (props.subjects || []).filter((s) =>
        (s.name_bn && s.name_bn.toLowerCase().includes(q)) ||
        (s.name_en && s.name_en.toLowerCase().includes(q))
    );
});

const filteredExams = computed(() => {
    if (!examSearch.value.trim()) return props.exams || [];
    const q = examSearch.value.toLowerCase().trim();
    return (props.exams || []).filter((e) =>
        (e.exam_name && e.exam_name.toLowerCase().includes(q)) ||
        (e.origin_tag && e.origin_tag.toLowerCase().includes(q)) ||
        (e.institution && e.institution.toLowerCase().includes(q))
    );
});

const selectedSubject = computed(() => {
    if (!selectedSubjectId.value) return null;
    return (props.subjects || []).find((s) => String(s.id) === String(selectedSubjectId.value));
});

const selectedExam = computed(() => {
    if (!selectedOriginTag.value) return null;
    return (props.exams || []).find((e) => e.origin_tag === selectedOriginTag.value);
});

const hasActiveFilters = computed(() => {
    return !!(selectedSubjectId.value || selectedOriginTag.value || searchInput.value);
});

// Test Generator Modal State
const showTestModal = ref(false);
const testForm = ref({
    subject_id: '',
    origin_tag: '',
    question_count: 20,
    duration_minutes: 20,
    negative_marking: true,
});
const generatingTest = ref(false);
const testError = ref('');

// Switch sector
function setSector(secKey) {
    if (currentSector.value === secKey) return;
    currentSector.value = secKey;
    selectedSubjectId.value = '';
    selectedOriginTag.value = '';
    searchInput.value = '';
    router.get(
        '/question-bank',
        { sector: secKey },
        { preserveState: true, replace: true }
    );
}

function selectSubject(id) {
    selectedSubjectId.value = String(selectedSubjectId.value) === String(id) ? '' : id;
    applyFilter();
}

function selectExam(tag) {
    selectedOriginTag.value = selectedOriginTag.value === tag ? '' : tag;
    applyFilter();
}

function setMode(mode) {
    activeMode.value = mode;
    applyFilter();
}

function applyFilter() {
    router.get(
        '/question-bank',
        {
            sector: currentSector.value,
            mode: activeMode.value !== 'all' ? activeMode.value : undefined,
            subject_id: selectedSubjectId.value || undefined,
            origin_tag: selectedOriginTag.value || undefined,
            search: searchInput.value ? searchInput.value.trim() : undefined,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    );
}

function resetFilters() {
    selectedSubjectId.value = '';
    selectedOriginTag.value = '';
    searchInput.value = '';
    activeMode.value = 'all';
    applyFilter();
}

// Option selection & immediate feedback
function selectOption(qId, key) {
    if (selectedAnswers.value[qId]) return;
    selectedAnswers.value[qId] = key;
}

// AI Explainer
async function requestAiExplain(q) {
    if (aiExplanations.value[q.id]?.text) {
        aiExplanations.value[q.id].show = !aiExplanations.value[q.id].show;
        return;
    }

    aiExplanations.value[q.id] = { text: '', loading: true, show: true };

    try {
        const response = await axios.post(`/question-bank/${q.id}/ai-explain`);
        if (response.data.success && response.data.explanation) {
            aiExplanations.value[q.id].text = response.data.explanation;
        } else {
            aiExplanations.value[q.id].text = q.explanation || 'ব্যাখ্যা বর্তমানে উপলব্ধ নেই।';
        }
    } catch (e) {
        aiExplanations.value[q.id].text = q.explanation || 'AI ব্যাখ্যা তৈরিতে সাময়িক বিঘ্ন ঘটেছে। দয়া করে কিছুক্ষণ পর আবার চেষ্টা করুন।';
    } finally {
        aiExplanations.value[q.id].loading = false;
    }
}

// Generate Custom Test
async function handleGenerateTest() {
    if (!props.isPaid) return;

    generatingTest.value = true;
    testError.value = '';

    try {
        const res = await axios.post('/question-bank/generate-test', testForm.value);
        if (res.data.success && res.data.redirect_url) {
            window.location.href = res.data.redirect_url;
        }
    } catch (err) {
        testError.value = err.response?.data?.message || 'মক টেস্ট তৈরি করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।';
    } finally {
        generatingTest.value = false;
    }
}
</script>

<template>
    <Head title="প্রশ্নব্যাংক ও অনুশীলন | Sikhun.com" />
    <StudentLayout>
        <!-- Sector Selector Navigation Pills -->
        <nav class="mb-4 flex flex-wrap items-center gap-2" aria-label="Question Bank Sectors">
            <button
                v-for="sec in sectors"
                :key="sec.key"
                @click="setSector(sec.key)"
                type="button"
                class="flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-bold transition-all cursor-pointer shadow-xs"
                :class="[
                    currentSector === sec.key
                        ? 'bg-[var(--primary)] text-white shadow-md shadow-[var(--primary)]/20 ring-2 ring-[var(--primary)]/30'
                        : 'border border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:border-[var(--primary)]/40 hover:text-[var(--text)]'
                ]"
            >
                <component :is="sec.icon" class="h-4 w-4 shrink-0" />
                <span>{{ sec.shortTitle }}</span>
                <span
                    class="rounded-full px-2 py-0.5 text-[10px] font-mono font-bold"
                    :class="[
                        currentSector === sec.key
                            ? 'bg-white/20 text-white'
                            : (props.sectorCounts?.[sec.key] > 0
                                ? 'bg-[var(--surface2)] text-[var(--text)]'
                                : 'bg-amber-500/10 text-amber-600 dark:text-amber-400')
                    ]"
                >
                    {{ props.sectorCounts?.[sec.key] > 0 ? toBnDigits(props.sectorCounts[sec.key]) : 'শীঘ্রই' }}
                </span>
            </button>
        </nav>

        <!-- Top Hero Banner with Sector Badge & Title -->
        <header
            class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-r p-6 text-white shadow-xl md:p-8 transition-colors duration-500"
            :class="activeSectorData.color"
        >
            <div class="relative z-10 flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-md">
                        <component :is="activeSectorData.icon" class="h-4 w-4" />
                        <span>{{ activeSectorData.badge }}</span>
                        <span v-if="props.sectorCounts?.[currentSector] > 0" class="ml-1 opacity-90 font-mono">
                            · {{ toBnDigits(props.sectorCounts[currentSector]) }} টি প্রশ্ন
                        </span>
                    </div>
                    <h1 class="font-heading text-2xl font-black tracking-tight sm:text-3xl">
                        {{ activeSectorData.title }}
                    </h1>
                    <p class="mt-1 text-sm text-white/90 max-w-2xl">
                        {{ activeSectorData.sub }}
                    </p>
                </div>

                <!-- Action Buttons: Generate Mock Test, Flashcards, Mistakes, Circulars -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <Link
                        :href="'/flashcards/create?source_type=question_bank&sector=' + currentSector + (selectedSubjectId ? '&subject_id=' + selectedSubjectId : '') + (selectedOriginTag ? '&origin_tag=' + encodeURIComponent(selectedOriginTag) : '')"
                        class="flex items-center gap-1.5 rounded-2xl bg-white/20 hover:bg-white/30 backdrop-blur-md px-4 py-2.5 font-heading text-xs font-bold text-white transition-all shadow-sm"
                    >
                        <RectangleStackIcon class="h-4 w-4 text-emerald-300" />
                        <span>ফ্ল্যাশকার্ড তৈরি করুন</span>
                    </Link>
                    <Link
                        href="/mistakes"
                        class="flex items-center gap-1.5 rounded-2xl bg-white/20 hover:bg-white/30 backdrop-blur-md px-4 py-2.5 font-heading text-xs font-bold text-white transition-all shadow-sm"
                    >
                        <SparklesIcon class="h-4 w-4 text-amber-300" />
                        <span>ভুল প্রশ্নের খাতা</span>
                    </Link>
                    <Link
                        v-if="currentSector === 'admission'"
                        href="/admission-notice"
                        class="flex items-center gap-1.5 rounded-2xl bg-white/20 hover:bg-white/30 backdrop-blur-md px-4 py-2.5 font-heading text-xs font-bold text-white transition-all shadow-sm"
                    >
                        <span>📢 ভর্তি সার্কুলার</span>
                    </Link>
                    <Link
                        v-if="currentSector === 'job'"
                        href="/job-news"
                        class="flex items-center gap-1.5 rounded-2xl bg-white/20 hover:bg-white/30 backdrop-blur-md px-4 py-2.5 font-heading text-xs font-bold text-white transition-all shadow-sm"
                    >
                        <span>💼 চাকরির খবর</span>
                    </Link>
                    <button
                        type="button"
                        @click="showTestModal = true"
                        class="group flex items-center gap-2 rounded-2xl bg-white px-5 py-2.5 font-heading text-xs font-bold text-gray-900 shadow-lg shadow-black/10 transition-all hover:bg-white/95 hover:shadow-xl active:scale-95 cursor-pointer"
                    >
                        <BoltIcon class="h-4.5 w-4.5 text-amber-500 transition-transform group-hover:rotate-12" />
                        <span>কাস্টম মক টেস্ট</span>
                        <span v-if="!isPaid" class="flex items-center gap-1 rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] font-bold text-amber-700">
                            <LockClosedIcon class="h-3 w-3" /> Pro
                        </span>
                    </button>
                </div>
            </div>

            <!-- Background decorative elements -->
            <div class="pointer-events-none absolute -right-10 -bottom-10 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -left-10 -top-10 h-64 w-64 rounded-full bg-black/10 blur-2xl"></div>
        </header>

        <!-- Main Content Grid with Dedicated Sidebar -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- ================= LEFT SIDEBAR (Desktop) ================= -->
            <aside class="hidden lg:block lg:col-span-4 xl:col-span-3.5 space-y-5 sticky top-20">
                <!-- Search & Reset Box -->
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-xs">
                    <form @submit.prevent="applyFilter" class="relative">
                        <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                        <input
                            v-model="searchInput"
                            type="text"
                            placeholder="প্রশ্ন বা বিষয় খুঁজুন..."
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] py-2 pl-9 pr-3 text-xs text-[var(--text)] placeholder-[var(--text-muted)] focus:border-[var(--primary)] focus:outline-none focus:ring-1 focus:ring-[var(--primary)]"
                        />
                    </form>

                    <!-- Active Mode Switcher -->
                    <div class="mt-3 grid grid-cols-3 gap-1 rounded-xl bg-[var(--surface2)] p-1 text-[11px] font-bold">
                        <button
                            type="button"
                            @click="setMode('all')"
                            class="rounded-lg py-1.5 transition text-center cursor-pointer"
                            :class="activeMode === 'all' ? 'bg-[var(--surface)] text-[var(--primary)] shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            সব
                        </button>
                        <button
                            type="button"
                            @click="setMode('subject')"
                            class="rounded-lg py-1.5 transition text-center cursor-pointer"
                            :class="activeMode === 'subject' ? 'bg-[var(--surface)] text-[var(--primary)] shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            বিষয়
                        </button>
                        <button
                            type="button"
                            @click="setMode('exam')"
                            class="rounded-lg py-1.5 transition text-center cursor-pointer"
                            :class="activeMode === 'exam' ? 'bg-[var(--surface)] text-[var(--primary)] shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            পরীক্ষা
                        </button>
                    </div>

                    <!-- Clear filters button if active -->
                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        @click="resetFilters"
                        class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-xl border border-rose-500/30 bg-rose-500/10 py-1.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 transition cursor-pointer"
                    >
                        <XMarkIcon class="h-3.5 w-3.5" />
                        <span>ফিল্টার মুছুন</span>
                    </button>
                </div>

                <!-- Subjects Sidebar Box -->
                <div v-if="subjects?.length" class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-xs">
                    <div class="flex items-center justify-between mb-2.5">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] flex items-center gap-1.5">
                            <BookOpenIcon class="h-3.5 w-3.5 text-[var(--primary)]" />
                            <span>বিষয়সমূহ ({{ toBnDigits(subjects.length) }})</span>
                        </h2>
                        <button
                            v-if="selectedSubjectId"
                            @click="selectSubject('')"
                            type="button"
                            class="text-[11px] font-semibold text-[var(--primary)] hover:underline cursor-pointer"
                        >
                            সকল বিষয়
                        </button>
                    </div>

                    <!-- Subject Search if more than 6 -->
                    <div v-if="subjects.length > 6" class="mb-2">
                        <input
                            v-model="subjectSearch"
                            type="text"
                            placeholder="বিষয় ফিল্টার..."
                            class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-1 text-[11px] text-[var(--text)] placeholder-[var(--text-muted)] focus:outline-none focus:border-[var(--primary)]"
                        />
                    </div>

                    <div class="max-h-56 overflow-y-auto space-y-1 pr-1 custom-scrollbar">
                        <button
                            @click="selectSubject('')"
                            type="button"
                            class="w-full flex items-center justify-between rounded-xl px-2.5 py-1.5 text-xs font-semibold transition cursor-pointer text-left"
                            :class="!selectedSubjectId ? 'bg-[var(--primary)] text-white font-bold shadow-xs' : 'text-[var(--text)] hover:bg-[var(--surface2)]'"
                        >
                            <span>সকল বিষয়</span>
                            <span class="text-[10px] opacity-75 font-mono">ALL</span>
                        </button>
                        <button
                            v-for="sub in filteredSubjects"
                            :key="sub.id"
                            @click="selectSubject(sub.id)"
                            type="button"
                            class="w-full flex items-center justify-between rounded-xl px-2.5 py-1.5 text-xs transition cursor-pointer text-left"
                            :class="selectedSubjectId == sub.id ? 'bg-[var(--primary)] text-white font-bold shadow-xs' : 'text-[var(--text)] hover:bg-[var(--surface2)]'"
                        >
                            <span class="truncate mr-2">{{ sub.name_bn }}</span>
                            <span
                                class="shrink-0 rounded-full px-1.5 py-0.5 text-[10px] font-mono font-bold"
                                :class="selectedSubjectId == sub.id ? 'bg-white/25 text-white' : 'bg-[var(--surface2)] text-[var(--text-muted)]'"
                            >
                                {{ toBnDigits(sub.questions_count || 0) }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Exams Sidebar Box (No more horizontal pill wall!) -->
                <div v-if="exams?.length" class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-xs">
                    <div class="flex items-center justify-between mb-2.5">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] flex items-center gap-1.5">
                            <TrophyIcon class="h-3.5 w-3.5 text-amber-500" />
                            <span>পরীক্ষাসমূহ ({{ toBnDigits(exams.length) }})</span>
                        </h2>
                        <button
                            v-if="selectedOriginTag"
                            @click="selectExam('')"
                            type="button"
                            class="text-[11px] font-semibold text-[var(--primary)] hover:underline cursor-pointer"
                        >
                            সকল পরীক্ষা
                        </button>
                    </div>

                    <!-- Exam Search Input -->
                    <div class="mb-2">
                        <input
                            v-model="examSearch"
                            type="text"
                            placeholder="পরীক্ষা বা সাল খুঁজুন..."
                            class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-1 text-[11px] text-[var(--text)] placeholder-[var(--text-muted)] focus:outline-none focus:border-[var(--primary)]"
                        />
                    </div>

                    <div class="max-h-72 overflow-y-auto space-y-1.5 pr-1 custom-scrollbar">
                        <button
                            @click="selectExam('')"
                            type="button"
                            class="w-full flex items-center justify-between rounded-xl px-2.5 py-2 text-xs font-semibold transition cursor-pointer text-left"
                            :class="!selectedOriginTag ? 'bg-[var(--primary)] text-white font-bold shadow-xs' : 'text-[var(--text)] hover:bg-[var(--surface2)]'"
                        >
                            <span>সকল পরীক্ষা</span>
                            <span class="text-[10px] opacity-75 font-mono">ALL</span>
                        </button>

                        <button
                            v-for="ex in filteredExams"
                            :key="ex.origin_tag"
                            @click="selectExam(ex.origin_tag)"
                            type="button"
                            class="w-full flex flex-col items-start rounded-xl px-2.5 py-2 text-xs transition cursor-pointer text-left border"
                            :class="selectedOriginTag === ex.origin_tag ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)] font-bold' : 'border-transparent text-[var(--text)] hover:bg-[var(--surface2)]'"
                        >
                            <div class="flex items-center justify-between w-full gap-2">
                                <span class="truncate font-semibold">{{ ex.exam_name || ex.origin_tag }}</span>
                                <span class="shrink-0 rounded-md bg-[var(--surface2)] px-1.5 py-0.5 text-[10px] font-mono text-[var(--text-muted)]">
                                    {{ toBnDigits(ex.count || 0) }} টি
                                </span>
                            </div>
                            <div v-if="ex.institution || ex.year" class="mt-0.5 text-[10px] text-[var(--text-muted)] truncate flex items-center gap-1.5">
                                <span v-if="ex.institution">{{ ex.institution }}</span>
                                <span v-if="ex.year">({{ toBnDigits(ex.year) }})</span>
                            </div>
                        </button>
                    </div>
                </div>
            </aside>

            <!-- ================= RIGHT MAIN CONTENT ================= -->
            <main class="lg:col-span-8 xl:col-span-8.5 space-y-5">
                <!-- Top Toolbar: Dropdown Selects for Subject and Exam + Mobile Trigger -->
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-xs flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[280px]">
                        <!-- Subject Dropdown -->
                        <div class="relative min-w-[150px] flex-1 sm:max-w-[220px]">
                            <select
                                :value="selectedSubjectId"
                                @change="selectSubject($event.target.value)"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] py-2 pl-3 pr-8 text-xs font-semibold text-[var(--text)] focus:border-[var(--primary)] focus:outline-none cursor-pointer"
                            >
                                <option value="">সকল বিষয় (Subjects)</option>
                                <option v-for="sub in subjects" :key="sub.id" :value="sub.id">
                                    {{ sub.name_bn }} ({{ toBnDigits(sub.questions_count || 0) }})
                                </option>
                            </select>
                        </div>

                        <!-- Exam Dropdown -->
                        <div class="relative min-w-[150px] flex-1 sm:max-w-[260px]">
                            <select
                                :value="selectedOriginTag"
                                @change="selectExam($event.target.value)"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] py-2 pl-3 pr-8 text-xs font-semibold text-[var(--text)] focus:border-[var(--primary)] focus:outline-none cursor-pointer"
                            >
                                <option value="">সকল পরীক্ষা (Exams)</option>
                                <option v-for="ex in exams" :key="ex.origin_tag" :value="ex.origin_tag">
                                    {{ ex.exam_name || ex.origin_tag }} ({{ toBnDigits(ex.count || 0) }})
                                </option>
                            </select>
                        </div>

                        <!-- Mobile Filters Trigger Button -->
                        <button
                            type="button"
                            @click="showMobileDrawer = true"
                            class="lg:hidden flex items-center gap-1.5 px-3 py-2 rounded-xl border border-[var(--border)] bg-[var(--surface2)] text-xs font-bold text-[var(--text)] cursor-pointer"
                        >
                            <FunnelIcon class="w-4 h-4 text-[var(--primary)]" />
                            <span>ফিল্টার</span>
                            <span v-if="hasActiveFilters" class="w-2 h-2 rounded-full bg-[var(--primary)]"></span>
                        </button>
                    </div>

                    <!-- Search Input (Right aligned) -->
                    <form @submit.prevent="applyFilter" class="relative w-full sm:w-60">
                        <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[var(--text-muted)]" />
                        <input
                            v-model="searchInput"
                            type="text"
                            placeholder="প্রশ্ন খুঁজুন..."
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] py-1.5 pl-8 pr-3 text-xs text-[var(--text)] placeholder-[var(--text-muted)] focus:border-[var(--primary)] focus:outline-none"
                        />
                    </form>
                </div>

                <!-- Active Filter Badges Strip -->
                <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="text-[var(--text-muted)] font-medium">সক্রিয় ফিল্টার:</span>
                    <span v-if="selectedSubject" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[var(--primary)]/10 text-[var(--primary)] font-semibold">
                        <span>বিষয়: {{ selectedSubject.name_bn }}</span>
                        <button @click="selectSubject('')" class="hover:opacity-75 cursor-pointer">✕</button>
                    </span>
                    <span v-if="selectedExam" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 font-semibold">
                        <span>পরীক্ষা: {{ selectedExam.exam_name || selectedExam.origin_tag }}</span>
                        <button @click="selectExam('')" class="hover:opacity-75 cursor-pointer">✕</button>
                    </span>
                    <span v-if="searchInput" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[var(--surface2)] text-[var(--text)] font-semibold">
                        <span>খোঁজা হচ্ছে: "{{ searchInput }}"</span>
                        <button @click="searchInput = ''; applyFilter();" class="hover:opacity-75 cursor-pointer">✕</button>
                    </span>
                    <button
                        @click="resetFilters"
                        class="text-[var(--text-muted)] hover:text-rose-500 transition text-[11px] font-bold cursor-pointer underline mr-auto"
                    >
                        সব মুছুন
                    </button>
                    <Link
                        v-if="selectedSubjectId || selectedOriginTag"
                        :href="'/flashcards/create?source_type=question_bank&sector=' + currentSector + (selectedSubjectId ? '&subject_id=' + selectedSubjectId : '') + (selectedOriginTag ? '&origin_tag=' + encodeURIComponent(selectedOriginTag) : '')"
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-xs font-bold hover:bg-emerald-500/20 transition"
                    >
                        <RectangleStackIcon class="w-3.5 h-3.5" />
                        <span>এই ফিল্টারে ফ্ল্যাশকার্ড বানান</span>
                    </Link>
                </div>

                <!-- Empty State for HSC / SSC (Coming Soon notification) -->
                <div
                    v-if="(props.sectorCounts?.[currentSector] ?? 0) === 0"
                    class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-8 text-center shadow-sm space-y-4"
                >
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/15 text-amber-500 mx-auto flex items-center justify-center text-3xl">
                        ⏳
                    </div>
                    <div>
                        <h2 class="font-heading text-xl font-bold text-[var(--text)]">
                            {{ activeSectorData.title }} দ্রুত যুক্ত করা হচ্ছে
                        </h2>
                        <p class="text-xs sm:text-sm text-[var(--text-muted)] max-w-lg mx-auto mt-1 leading-relaxed">
                            বর্তমানে শিখুনে বিশ্ববিদ্যালয় ভর্তি (১৫,০০০+ প্রশ্ন) এবং বিসিএস ও চাকরি প্রশ্নব্যাংক (৩৪,০০০+ প্রশ্ন) সক্রিয় রয়েছে। এইচএসসি ও এসএসসি প্রশ্নব্যাংক প্রস্তুত করার কাজ দ্রুতগতিতে চলছে।
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                        <button
                            type="button"
                            @click="setSector('admission')"
                            class="px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold transition shadow-sm cursor-pointer flex items-center gap-1.5"
                        >
                            <AcademicCapIcon class="w-4 h-4" />
                            <span>বিশ্ববিদ্যালয় ভর্তি প্রশ্নব্যাংক দেখুন ({{ toBnDigits(props.sectorCounts?.admission || 0) }})</span>
                        </button>
                        <button
                            type="button"
                            @click="setSector('job')"
                            class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-sm cursor-pointer flex items-center gap-1.5"
                        >
                            <BriefcaseIcon class="w-4 h-4" />
                            <span>বিসিএস ও চাকরি প্রশ্নব্যাংক দেখুন ({{ toBnDigits(props.sectorCounts?.job || 0) }})</span>
                        </button>
                    </div>
                </div>

                <!-- Empty State if filter yields no questions -->
                <div
                    v-else-if="!questions?.data?.length"
                    class="rounded-3xl border border-dashed border-[var(--border)] bg-[var(--surface)] p-12 text-center"
                >
                    <BookOpenIcon class="h-12 w-12 text-[var(--text-muted)] mx-auto mb-3 opacity-40" />
                    <h2 class="font-heading text-base font-bold text-[var(--text)]">কোনো প্রশ্ন পাওয়া যায়নি</h2>
                    <p class="text-xs text-[var(--text-muted)] mt-1 max-w-sm mx-auto">
                        আপনার নির্বাচিত বিষয় বা পরীক্ষার কোনো প্রশ্ন এই মুহূর্তে পাওয়া যায়নি। ফিল্টার পরিবর্তন করে আবার চেষ্টা করুন।
                    </p>
                    <button
                        type="button"
                        @click="resetFilters"
                        class="mt-4 px-4 py-2 rounded-xl bg-[var(--primary)] text-white text-xs font-bold hover:opacity-90 transition cursor-pointer"
                    >
                        সকল প্রশ্ন দেখুন
                    </button>
                </div>

                <!-- Questions Stream List -->
                <div v-else class="space-y-4">
                    <article
                        v-for="(q, idx) in questions.data"
                        :key="q.id"
                        class="card rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-5 md:p-6 shadow-sm transition hover:shadow-md"
                    >
                        <!-- Question Header Strip -->
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2 border-b border-[var(--border)] pb-3 text-xs">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-lg bg-[var(--primary)]/10 px-2.5 py-1 font-mono font-bold text-[var(--primary)]">
                                    #{{ toBnDigits((questions.current_page - 1) * questions.per_page + idx + 1) }}
                                </span>
                                <span v-if="q.exam_name || q.origin_tag" class="rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-1 font-medium text-[var(--text-muted)]">
                                    {{ q.exam_name || q.origin_tag }}
                                </span>
                                <span v-if="q.subject?.name_bn" class="rounded-lg bg-orange-500/10 px-2.5 py-1 font-medium text-orange-600 dark:text-orange-400">
                                    {{ q.subject.name_bn }}
                                </span>
                                <span v-if="q.topic_name" class="rounded-lg bg-cyan-500/10 px-2.5 py-1 font-medium text-cyan-600 dark:text-cyan-400">
                                    {{ q.topic_name }}
                                </span>
                            </div>

                            <div class="text-[11px] text-[var(--text-muted)] font-mono">
                                মান: ১.০০ | নেগেটিভ: {{ q.negative_marks ? toBnDigits(q.negative_marks) : '০.২৫' }}
                            </div>
                        </div>

                        <!-- Question Text (KaTeX Math rendering) -->
                        <div
                            class="font-heading text-base font-semibold text-[var(--text)] leading-relaxed sm:text-lg mb-4"
                            v-html="renderMath(q.question_text)"
                        ></div>

                        <!-- MCQ Options (Interactive) -->
                        <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 mb-4">
                            <button
                                v-for="opt in q.options"
                                :key="opt.key"
                                @click="selectOption(q.id, opt.key)"
                                type="button"
                                class="flex items-center gap-3 rounded-2xl border p-3.5 text-left text-sm transition-all cursor-pointer"
                                :class="[
                                    selectedAnswers[q.id]
                                        ? (opt.key === q.correct_option
                                            ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 font-semibold ring-2 ring-emerald-500/20'
                                            : (selectedAnswers[q.id] === opt.key
                                                ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200 font-semibold'
                                                : 'border-[var(--border)] bg-[var(--surface2)]/40 text-[var(--text-muted)] opacity-60'))
                                        : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text)] hover:border-[var(--primary)] hover:bg-[var(--primary)]/5 active:scale-[0.99]'
                                ]"
                            >
                                <span
                                    class="grid h-7 w-7 shrink-0 place-items-center rounded-xl font-mono text-xs font-bold"
                                    :class="[
                                        selectedAnswers[q.id]
                                            ? (opt.key === q.correct_option
                                                ? 'bg-emerald-600 text-white'
                                                : (selectedAnswers[q.id] === opt.key ? 'bg-rose-600 text-white' : 'bg-[var(--border)] text-[var(--text-muted)]'))
                                            : 'bg-[var(--surface)] border border-[var(--border)] text-[var(--text-muted)]'
                                    ]"
                                >
                                    {{ opt.key }}
                                </span>
                                <div class="flex-1" v-html="renderMath(opt.text)"></div>

                                <span v-if="selectedAnswers[q.id]">
                                    <CheckCircleIcon v-if="opt.key === q.correct_option" class="h-5 w-5 text-emerald-600 shrink-0" />
                                    <XCircleIcon v-else-if="selectedAnswers[q.id] === opt.key" class="h-5 w-5 text-rose-600 shrink-0" />
                                </span>
                            </button>
                        </div>

                        <!-- Footer Strip: AI Explainer Button & Reveal -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-[var(--border)] text-xs">
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="requestAiExplain(q)"
                                    class="flex items-center gap-1.5 rounded-xl border border-indigo-500/20 bg-indigo-500/10 px-3.5 py-1.5 font-bold text-indigo-600 hover:bg-indigo-500/20 dark:text-indigo-400 transition cursor-pointer"
                                >
                                    <SparklesIcon class="h-4 w-4" />
                                    <span>{{ aiExplanations[q.id]?.show ? 'ব্যাখ্যা লুকান' : 'AI দিয়ে ব্যাখ্যা দেখুন' }}</span>
                                    <ArrowPathIcon v-if="aiExplanations[q.id]?.loading" class="h-3.5 w-3.5 animate-spin" />
                                </button>

                                <span v-if="selectedAnswers[q.id]" class="text-xs font-semibold">
                                    <span v-if="selectedAnswers[q.id] === q.correct_option" class="text-emerald-600 dark:text-emerald-400">
                                        ✓ সঠিক উত্তর!
                                    </span>
                                    <span v-else class="text-rose-600 dark:text-rose-400">
                                        ✗ সঠিক উত্তর: <strong>{{ q.correct_option }}</strong>
                                    </span>
                                </span>
                            </div>

                            <span v-if="q.year" class="text-[11px] text-[var(--text-muted)] font-mono">
                                বছর: {{ toBnDigits(q.year) }}
                            </span>
                        </div>

                        <!-- Expandable AI Explanation Box -->
                        <div
                            v-if="aiExplanations[q.id]?.show"
                            class="mt-4 rounded-2xl border border-indigo-500/30 bg-indigo-50/50 p-4 dark:bg-indigo-950/20 text-xs sm:text-sm leading-relaxed"
                        >
                            <div class="mb-1.5 flex items-center gap-1.5 font-bold text-indigo-700 dark:text-indigo-300">
                                <LightBulbIcon class="h-4 w-4 shrink-0" />
                                <span>AI সমাধান ও ব্যাখ্যা:</span>
                            </div>
                            <div
                                v-if="aiExplanations[q.id]?.text"
                                class="text-[var(--text)] prose prose-sm dark:prose-invert max-w-none"
                                v-html="renderMath(aiExplanations[q.id].text)"
                            ></div>
                            <div v-else class="flex items-center gap-2 text-[var(--text-muted)] py-2">
                                <ArrowPathIcon class="h-4 w-4 animate-spin text-indigo-500" />
                                <span>AI ব্যাখ্যা তৈরি হচ্ছে...</span>
                            </div>
                        </div>
                    </article>

                    <!-- Pagination -->
                    <div class="mt-8">
                        <Pagination :links="questions.links" />
                    </div>
                </div>
            </main>
        </div>

        <!-- Mobile Filters Modal Drawer -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showMobileDrawer"
                class="fixed inset-0 z-50 flex justify-end bg-black/60 backdrop-blur-xs lg:hidden"
                @click.self="showMobileDrawer = false"
            >
                <div class="w-full max-w-md bg-[var(--surface)] h-full overflow-y-auto p-6 shadow-2xl flex flex-col justify-between">
                    <div class="space-y-5">
                        <div class="flex items-center justify-between border-b border-[var(--border)] pb-3">
                            <h3 class="font-heading text-lg font-bold text-[var(--text)] flex items-center gap-2">
                                <FunnelIcon class="w-5 h-5 text-[var(--primary)]" />
                                <span>ফিল্টার ও নেভিগেশন</span>
                            </h3>
                            <button @click="showMobileDrawer = false" class="text-[var(--text-muted)] p-1 hover:text-[var(--text)]">
                                ✕
                            </button>
                        </div>

                        <!-- Sector selection -->
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] mb-2 uppercase">সেক্টর নির্বাচন</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    v-for="sec in sectors"
                                    :key="sec.key"
                                    @click="setSector(sec.key); showMobileDrawer = false;"
                                    type="button"
                                    class="p-2.5 rounded-xl border text-xs font-bold text-left transition"
                                    :class="currentSector === sec.key ? 'border-[var(--primary)] bg-[var(--primary)] text-white' : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text)]'"
                                >
                                    <div>{{ sec.shortTitle }}</div>
                                    <div class="text-[10px] opacity-75 font-mono">
                                        {{ props.sectorCounts?.[sec.key] > 0 ? toBnDigits(props.sectorCounts[sec.key]) : 'শীঘ্রই' }}
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Subject Dropdown -->
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] mb-1 uppercase">বিষয় নির্বাচন</label>
                            <select
                                :value="selectedSubjectId"
                                @change="selectSubject($event.target.value); showMobileDrawer = false;"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] p-2.5 text-xs font-semibold text-[var(--text)]"
                            >
                                <option value="">সকল বিষয়</option>
                                <option v-for="sub in subjects" :key="sub.id" :value="sub.id">
                                    {{ sub.name_bn }} ({{ toBnDigits(sub.questions_count || 0) }})
                                </option>
                            </select>
                        </div>

                        <!-- Exam Dropdown -->
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] mb-1 uppercase">নির্দিষ্ট পরীক্ষা নির্বাচন</label>
                            <select
                                :value="selectedOriginTag"
                                @change="selectExam($event.target.value); showMobileDrawer = false;"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] p-2.5 text-xs font-semibold text-[var(--text)]"
                            >
                                <option value="">সকল পরীক্ষা</option>
                                <option v-for="ex in exams" :key="ex.origin_tag" :value="ex.origin_tag">
                                    {{ ex.exam_name || ex.origin_tag }} ({{ toBnDigits(ex.count || 0) }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-[var(--border)] flex gap-2">
                        <button
                            type="button"
                            @click="resetFilters(); showMobileDrawer = false;"
                            class="flex-1 py-2.5 rounded-xl border border-[var(--border)] text-xs font-bold text-[var(--text-muted)]"
                        >
                            রিসেট
                        </button>
                        <button
                            type="button"
                            @click="showMobileDrawer = false"
                            class="flex-1 py-2.5 rounded-xl bg-[var(--primary)] text-white text-xs font-bold"
                        >
                            ফলাফল দেখুন
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Custom Mock Test Modal -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="showTestModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
                @click.self="showTestModal = false"
            >
                <div class="w-full max-w-lg rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-2xl">
                    <!-- Non-Paid User Upgrade Prompt -->
                    <div v-if="!isPaid" class="text-center py-4">
                        <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-amber-500/15 text-amber-500">
                            <LockClosedIcon class="h-7 w-7" />
                        </div>
                        <h3 class="font-heading text-xl font-bold text-[var(--text)]">কাস্টম মক টেস্ট প্রো ফিচার</h3>
                        <p class="mt-2 text-xs sm:text-sm text-[var(--text-muted)] leading-relaxed">
                            কাস্টম মক টেস্ট তৈরি করে রিয়েল-টাইম নেগেটিভ মার্কিং সহ প্র্যাকটিস করতে আপনার অ্যাকাউন্টে সাবস্ক্রিপশন প্রয়োজন।
                        </p>
                        <div class="mt-6 flex flex-col gap-2">
                            <Link href="/subscription" class="btn btn-primary w-full py-3">
                                <BoltIcon class="h-4.5 w-4.5" />
                                <span>প্ল্যান দেখুন ও এখনই আপগ্রেড করুন</span>
                            </Link>
                            <button @click="showTestModal = false" class="btn btn-outline w-full py-2.5">
                                পরে করব
                            </button>
                        </div>
                    </div>

                    <!-- Paid User: Generator Form -->
                    <div v-else>
                        <div class="mb-4 flex items-center justify-between border-b border-[var(--border)] pb-3">
                            <h3 class="flex items-center gap-2 font-heading text-lg font-bold text-[var(--text)]">
                                <BoltIcon class="h-5 w-5 text-amber-500" />
                                <span>কাস্টম মক টেস্ট তৈরি করুন</span>
                            </h3>
                            <button @click="showTestModal = false" class="text-[var(--text-muted)] hover:text-[var(--text)]">
                                ✕
                            </button>
                        </div>

                        <p class="mb-4 text-xs text-[var(--text-muted)]">
                            প্রশ্নব্যাংকের প্রশ্ন নিয়ে একটি নির্দিষ্ট সময় ও নেগেটিভ মার্কিং সহ লাইভ পরীক্ষা তৈরি করুন।
                        </p>

                        <form @submit.prevent="handleGenerateTest" class="space-y-4 text-sm">
                            <!-- Subject Selection -->
                            <div>
                                <label class="mb-1 block font-medium text-xs">বিষয় নির্বাচন করুন</label>
                                <select v-model="testForm.subject_id" class="input w-full">
                                    <option value="">সকল বিষয় (সম্পূর্ণ সিলেবাস)</option>
                                    <option v-for="sub in subjects" :key="sub.id" :value="sub.id">
                                        {{ sub.name_bn }}
                                    </option>
                                </select>
                            </div>

                            <!-- Exam Selection -->
                            <div>
                                <label class="mb-1 block font-medium text-xs">নির্দিষ্ট পরীক্ষার প্রশ্ন (ঐচ্ছিক)</label>
                                <select v-model="testForm.origin_tag" class="input w-full">
                                    <option value="">সকল পরীক্ষা থেকে প্রশ্ন নিন</option>
                                    <option v-for="ex in exams" :key="ex.origin_tag" :value="ex.origin_tag">
                                        {{ ex.exam_name || ex.origin_tag }}
                                    </option>
                                </select>
                            </div>

                            <!-- Question Count & Duration -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block font-medium text-xs">প্রশ্ন সংখ্যা</label>
                                    <select v-model="testForm.question_count" class="input w-full">
                                        <option :value="10">১০ টি প্রশ্ন</option>
                                        <option :value="20">২০ টি প্রশ্ন</option>
                                        <option :value="30">৩০ টি প্রশ্ন</option>
                                        <option :value="50">৫০ টি প্রশ্ন</option>
                                        <option :value="100">১০০ টি প্রশ্ন</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block font-medium text-xs">পরীক্ষার সময়</label>
                                    <select v-model="testForm.duration_minutes" class="input w-full">
                                        <option :value="10">১০ মিনিট</option>
                                        <option :value="15">১৫ মিনিট</option>
                                        <option :value="20">২০ মিনিট</option>
                                        <option :value="45">৪৫ মিনিট</option>
                                        <option :value="60">৬০ মিনিট</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Negative Marking Toggle -->
                            <label class="flex cursor-pointer items-center gap-2 pt-1 text-xs">
                                <input v-model="testForm.negative_marking" type="checkbox" class="h-4 w-4 rounded accent-[var(--primary)]" />
                                <span>নেগেটিভ মার্কিং সক্রিয় থাকবে (ভুল উত্তরের জন্য ০.২৫ কাটা যাবে)</span>
                            </label>

                            <div v-if="testError" class="rounded-xl bg-rose-500/10 p-3 text-xs text-rose-500 font-medium">
                                {{ testError }}
                            </div>

                            <div class="mt-6 flex items-center justify-end gap-2 border-t border-[var(--border)] pt-4">
                                <button type="button" @click="showTestModal = false" class="btn btn-outline text-xs px-4 py-2">
                                    বাতিল
                                </button>
                                <button type="submit" :disabled="generatingTest" class="btn btn-primary text-xs px-5 py-2 font-bold">
                                    <ArrowPathIcon v-if="generatingTest" class="h-4 w-4 animate-spin" />
                                    <span>{{ generatingTest ? 'তৈরি হচ্ছে...' : 'মক টেস্ট শুরু করুন' }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </transition>
    </StudentLayout>
</template>
