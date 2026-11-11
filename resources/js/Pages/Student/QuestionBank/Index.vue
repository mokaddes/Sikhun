<script setup>
import { ref, computed } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { renderMath } from '@/utils/renderMath';
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
} from '@heroicons/vue/24/outline';

const props = defineProps({
    questions: Object,
    subjects: Array,
    exams: Array,
    filters: Object,
    isPaid: Boolean,
    studentType: String,
});

// State for active practice interaction
const selectedAnswers = ref({}); // { [questionId]: 'A' }
const aiExplanations = ref({});  // { [questionId]: { text: '...', loading: false } }
const searchInput = ref(props.filters?.search || '');
const activeMode = ref(props.filters?.mode || 'subject'); // 'subject' or 'exam'
const selectedSubjectId = ref(props.filters?.subject_id || '');
const selectedOriginTag = ref(props.filters?.origin_tag || '');

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

// Education Type Badge formatting
const educationBadge = computed(() => {
    switch (props.studentType) {
        case 'admission':
            return {
                title: 'বিশ্ববিদ্যালয় ভর্তি প্রশ্নব্যাংক',
                sub: 'ঢাবি, রাবি, বুয়েট, চবি ও গুচ্ছ বিগত বছরগুলোর প্রশ্ন সমাধান',
                color: 'from-orange-500 to-amber-600',
                label: 'ভর্তি প্রস্তুতি'
            };
        case 'job':
            return {
                title: 'বিসিএস ও সরকারি চাকরি প্রশ্নব্যাংক',
                sub: 'বিসিএস প্রিলিমিনারি, ব্যাংক রিক্রুটমেন্ট ও প্রাইমারি বিগত প্রশ্নসমূহ',
                color: 'from-blue-600 to-indigo-600',
                label: 'চাকরি প্রস্তুতি'
            };
        case 'hsc':
            return {
                title: 'উচ্চ মাধ্যমিক (HSC) প্রশ্নব্যাংক',
                sub: 'এইচএসসি বিজ্ঞান, মানবিক ও ব্যবসায় শিক্ষা বোর্ড প্রশ্ন ও টেস্ট পেপার',
                color: 'from-emerald-600 to-teal-600',
                label: 'HSC প্রস্তুতি'
            };
        case 'ssc':
            return {
                title: 'মাধ্যমিক (SSC) প্রশ্নব্যাংক',
                sub: 'এসএসসি সকল শিক্ষা বোর্ডের বিগত বছরের প্রশ্ন ও অধ্যায়ভিত্তিক অনুশীলন',
                color: 'from-purple-600 to-pink-600',
                label: 'SSC প্রস্তুতি'
            };
        default:
            return {
                title: 'বিশ্ববিদ্যালয় ও উচ্চশিক্ষা প্রশ্নব্যাংক',
                sub: 'অ্যাকাডেমিক ও প্রতিযোগিতামূলক পরীক্ষার পূর্ণাঙ্গ প্রশ্ন সমাধান',
                color: 'from-cyan-600 to-blue-600',
                label: 'উচ্চশিক্ষা'
            };
    }
});

// Switch practice mode (Subject vs Exam)
function setMode(mode) {
    activeMode.value = mode;
    applyFilter();
}

function selectSubject(id) {
    selectedSubjectId.value = selectedSubjectId.value === id ? '' : id;
    applyFilter();
}

function selectExam(tag) {
    selectedOriginTag.value = selectedOriginTag.value === tag ? '' : tag;
    applyFilter();
}

function applyFilter() {
    router.get(
        '/question-bank',
        {
            mode: activeMode.value,
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
    applyFilter();
}

// Option selection & immediate feedback
function selectOption(qId, key) {
    if (selectedAnswers.value[qId]) return; // already answered
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
    if (!props.isPaid) {
        // Free user attempted
        return;
    }

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
        <!-- Top Hero Banner with Student Stream Badge -->
        <header class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-r p-6 text-white shadow-xl md:p-8" :class="educationBadge.color">
            <div class="relative z-10 flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-md">
                        <AcademicCapIcon class="h-4 w-4" />
                        <span>{{ educationBadge.label }}</span>
                    </div>
                    <h1 class="font-heading text-2xl font-black tracking-tight sm:text-3xl">
                        {{ educationBadge.title }}
                    </h1>
                    <p class="mt-1 text-sm text-white/90">
                        {{ educationBadge.sub }}
                    </p>
                </div>

                <!-- Action Button: Generate Mock Test -->
                <div class="flex items-center gap-3">
                    <button
                        @click="showTestModal = true"
                        class="group flex items-center gap-2 rounded-2xl bg-white px-5 py-3 font-heading text-sm font-bold text-gray-900 shadow-lg shadow-black/10 transition-all hover:bg-white/95 hover:shadow-xl active:scale-95"
                    >
                        <BoltIcon class="h-5 w-5 text-amber-500 transition-transform group-hover:rotate-12" />
                        <span>কাস্টম মক টেস্ট তৈরি করুন</span>
                        <span v-if="!isPaid" class="flex items-center gap-1 rounded-full bg-amber-500/10 px-2 py-0.5 text-[11px] font-bold text-amber-700">
                            <LockClosedIcon class="h-3 w-3" /> Pro
                        </span>
                    </button>
                </div>
            </div>

            <!-- Background decorative elements -->
            <div class="pointer-events-none absolute -right-10 -bottom-10 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -left-10 -top-10 h-64 w-64 rounded-full bg-black/10 blur-2xl"></div>
        </header>

        <!-- Mode Switcher & Filter Toolbar -->
        <div class="mb-6 space-y-4">
            <!-- Mode Switch Tabs -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-1.5 shadow-sm">
                    <button
                        @click="setMode('subject')"
                        class="flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold transition-all"
                        :class="activeMode === 'subject' ? 'bg-[var(--primary)] text-white shadow' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                    >
                        <BookOpenIcon class="h-4.5 w-4.5" />
                        <span>বিষয়ভিত্তিক অনুশীলন (Subject-wise)</span>
                    </button>
                    <button
                        @click="setMode('exam')"
                        class="flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold transition-all"
                        :class="activeMode === 'exam' ? 'bg-[var(--primary)] text-white shadow' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                    >
                        <TrophyIcon class="h-4.5 w-4.5" />
                        <span>পরীক্ষাভিত্তিক অনুশীলন (Exam-wise)</span>
                    </button>
                </div>

                <!-- Search Input -->
                <form @submit.prevent="applyFilter" class="relative min-w-[240px] flex-1 sm:max-w-xs">
                    <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-[var(--text-muted)]" />
                    <input
                        v-model="searchInput"
                        type="text"
                        placeholder="প্রশ্ন বা বিষয় খুঁজুন..."
                        class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] py-2.5 pl-10 pr-4 text-sm text-[var(--text)] placeholder-[var(--text-muted)] focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    />
                </form>
            </div>

            <!-- Subject Pills (Subject Mode) -->
            <div v-if="activeMode === 'subject' && subjects?.length" class="flex flex-wrap items-center gap-2 overflow-x-auto pb-1">
                <button
                    @click="selectSubject('')"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition-all"
                    :class="!selectedSubjectId ? 'bg-[var(--primary)] text-white shadow' : 'border border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:bg-[var(--surface2)]'"
                >
                    সকল বিষয়
                </button>
                <button
                    v-for="sub in subjects"
                    :key="sub.id"
                    @click="selectSubject(sub.id)"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition-all"
                    :class="selectedSubjectId == sub.id ? 'bg-[var(--primary)] text-white shadow' : 'border border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:bg-[var(--surface2)]'"
                >
                    {{ sub.name_bn }} <span class="ml-1 opacity-70">({{ sub.questions_count || 0 }})</span>
                </button>
            </div>

            <!-- Exam Pills (Exam Mode) -->
            <div v-if="activeMode === 'exam' && exams?.length" class="flex flex-wrap items-center gap-2 overflow-x-auto pb-1">
                <button
                    @click="selectExam('')"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition-all"
                    :class="!selectedOriginTag ? 'bg-[var(--primary)] text-white shadow' : 'border border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:bg-[var(--surface2)]'"
                >
                    সকল পরীক্ষা
                </button>
                <button
                    v-for="ex in exams"
                    :key="ex.origin_tag"
                    @click="selectExam(ex.origin_tag)"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition-all"
                    :class="selectedOriginTag === ex.origin_tag ? 'bg-[var(--primary)] text-white shadow' : 'border border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:bg-[var(--surface2)]'"
                >
                    {{ ex.exam_name || ex.origin_tag }} <span class="ml-1 opacity-70">({{ ex.count || 0 }})</span>
                </button>
            </div>
        </div>

        <!-- Question List -->
        <div v-if="questions?.data?.length" class="space-y-5">
            <article
                v-for="(q, idx) in questions.data"
                :key="q.id"
                class="card anim-rise rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-5 md:p-6 shadow-sm transition hover:shadow-md"
            >
                <!-- Question Header Strip -->
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2 border-b border-[var(--border)] pb-3 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-lg bg-[var(--primary)]/10 px-2.5 py-1 font-mono font-bold text-[var(--primary)]">
                            #{{ (questions.current_page - 1) * questions.per_page + idx + 1 }}
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
                        মান: ১.০০ | নেগেটিভ: {{ q.negative_marks || '০.২৫' }}
                    </div>
                </div>

                <!-- Question Text (with KaTeX rendering) -->
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
                        class="flex items-center gap-3 rounded-2xl border p-3.5 text-left text-sm transition-all"
                        :class="[
                            // Already answered state
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

                        <!-- Feedback Icons -->
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
                            @click="requestAiExplain(q)"
                            class="flex items-center gap-1.5 rounded-xl border border-indigo-500/20 bg-indigo-500/10 px-3.5 py-1.5 font-bold text-indigo-600 hover:bg-indigo-500/20 dark:text-indigo-400 transition"
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
                                ✕ ভুল উত্তর! সঠিক উত্তর: {{ q.correct_option }}
                            </span>
                        </span>
                    </div>

                    <div v-if="q.difficulty" class="text-[11px] uppercase tracking-wider text-[var(--text-muted)]">
                        লেভেল: {{ q.difficulty === 'easy' ? 'সহজ' : (q.difficulty === 'hard' ? 'কঠিন' : 'মাঝারি') }}
                    </div>
                </div>

                <!-- AI Explainer Accordion Panel -->
                <div
                    v-if="aiExplanations[q.id]?.show"
                    class="mt-4 rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4 text-sm text-[var(--text)] transition-all"
                >
                    <div class="mb-2 flex items-center gap-2 font-heading text-xs font-bold text-indigo-600 dark:text-indigo-400">
                        <LightBulbIcon class="h-4 w-4" />
                        <span>AI শিক্ষকের বিস্তারিত সমাধান ও কনসেপ্ট বিশ্লেষণ:</span>
                    </div>

                    <div v-if="aiExplanations[q.id]?.loading" class="flex items-center gap-2 py-4 text-xs text-[var(--text-muted)]">
                        <ArrowPathIcon class="h-4 w-4 animate-spin text-indigo-600" />
                        <span>AI শিক্ষক বিস্তারিত গাণিতিক ও ধারণাগত ব্যাখ্যা প্রস্তুত করছেন...</span>
                    </div>

                    <div
                        v-else
                        class="leading-relaxed space-y-2 text-xs sm:text-sm"
                        v-html="renderMath(aiExplanations[q.id]?.text)"
                    ></div>
                </div>
            </article>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                <Pagination :links="questions.links" />
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="card flex flex-col items-center justify-center rounded-3xl border-dashed p-12 text-center text-[var(--text-muted)]">
            <BookOpenIcon class="mb-3 h-12 w-12 opacity-40" />
            <h3 class="font-heading text-lg font-bold text-[var(--text)]">কোনো প্রশ্ন পাওয়া যায়নি</h3>
            <p class="mt-1 text-sm">ফিল্টার বা সার্চ কি-ওয়ার্ড পরিবর্তন করে আবার চেষ্টা করুন।</p>
            <button @click="resetFilters" class="btn btn-outline btn-sm mt-4">
                ফিল্টার রিসেট করুন
            </button>
        </div>

        <!-- Modal: Custom Mock Test Generator -->
        <div v-if="showTestModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-2xl">
                <!-- Free User Upgrade Banner -->
                <div v-if="!isPaid" class="text-center py-4">
                    <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-3xl bg-amber-500/10 text-amber-500">
                        <LockClosedIcon class="h-8 w-8" />
                    </div>
                    <span class="inline-block rounded-full bg-amber-500/10 px-3 py-1 text-xs font-bold text-amber-600 dark:text-amber-400 mb-2">
                        Sikhun প্রিমিয়াম ফিচার
                    </span>
                    <h3 class="font-heading text-xl font-extrabold text-[var(--text)]">
                        কাস্টম মক টেস্ট জেনারেটর
                    </h3>
                    <p class="mt-2 text-sm text-[var(--text-muted)] leading-relaxed">
                        প্রশ্নব্যাংক থেকে নিজের পছন্দমতো বিষয়, প্রশ্নসংখ্যা এবং সময় নির্ধারণ করে আনলিমিটেড টাইমার ও নেগেটিভ মার্কিং সহ রিয়েল-টাইম মক টেস্ট তৈরি করার সুবিধা শুধুমাত্র Sikhun সাবস্ক্রিপশন প্ল্যানে উপলব্ধ।
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

                        <!-- Submit CTA -->
                        <div class="flex items-center gap-3 pt-3">
                            <button
                                type="submit"
                                :disabled="generatingTest"
                                class="btn btn-primary flex-1 py-3 flex items-center justify-center gap-2"
                            >
                                <ArrowPathIcon v-if="generatingTest" class="h-4 w-4 animate-spin" />
                                <span v-else>মক টেস্ট শুরু করুন</span>
                            </button>
                            <button type="button" @click="showTestModal = false" class="btn btn-outline py-3">
                                বাতিল
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
