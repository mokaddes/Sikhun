<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { renderMath } from '@/Utils/renderMath';
import {
    AcademicCapIcon,
    ArrowLeftIcon,
    SparklesIcon,
    PlusIcon,
    TrashIcon,
    EyeIcon,
    UserGroupIcon,
    CreditCardIcon,
    CheckCircleIcon,
    LockClosedIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    question: Object,
    subjects: Array,
    sources: Array,
    plans: Array,
    studentTypes: Array,
});

const isEditing = computed(() => !!props.question);

const form = useForm({
    segment: props.question?.segment || 'admission',
    subject_id: props.question?.subject_id || (props.subjects[0]?.id ?? ''),
    topic_id: props.question?.topic_id || '',
    topic_name: props.question?.topic_name || '',
    institution: props.question?.institution || '',
    exam_name: props.question?.exam_name || '',
    origin_tag: props.question?.origin_tag || 'DU-24',
    year: props.question?.year || new Date().getFullYear(),
    session: props.question?.session || '',
    shift_or_set: props.question?.shift_or_set || '',
    question_text: props.question?.question_text || '',
    question_type: props.question?.question_type || 'mcq',
    options: props.question?.options || [
        { key: 'A', text: '' },
        { key: 'B', text: '' },
        { key: 'C', text: '' },
        { key: 'D', text: '' },
    ],
    correct_option: props.question?.correct_option || 'A',
    explanation: props.question?.explanation || '',
    marks: props.question?.marks || 1.00,
    negative_marks: props.question?.negative_marks || (props.question?.segment === 'job' ? 0.50 : 0.25),
    difficulty: props.question?.difficulty || 'medium',
    source_id: props.question?.source_id || '',
    status: props.question?.status || 'published',
    allowed_student_types: props.question?.allowed_student_types || ['all'],
    allowed_plan_ids: props.question?.allowed_plan_ids || ['all'],
    is_public_to_all: props.question?.is_public_to_all ?? false,
});

function addOption() {
    const nextKey = String.fromCharCode(65 + form.options.length); // A, B, C, D, E...
    form.options.push({ key: nextKey, text: '' });
}

function removeOption(index) {
    if (form.options.length <= 2) return;
    form.options.splice(index, 1);
}

// Student type toggle
function toggleStudentType(typeKey) {
    if (typeKey === 'all') {
        form.allowed_student_types = ['all'];
        return;
    }
    form.allowed_student_types = form.allowed_student_types.filter((t) => t !== 'all');
    if (form.allowed_student_types.includes(typeKey)) {
        form.allowed_student_types = form.allowed_student_types.filter((t) => t !== typeKey);
        if (form.allowed_student_types.length === 0) {
            form.allowed_student_types = ['all'];
        }
    } else {
        form.allowed_student_types.push(typeKey);
    }
}

// Plan toggle
function togglePlan(planId) {
    if (planId === 'all') {
        form.allowed_plan_ids = ['all'];
        return;
    }
    form.allowed_plan_ids = form.allowed_plan_ids.filter((p) => p !== 'all');
    const strId = String(planId);
    if (form.allowed_plan_ids.includes(strId)) {
        form.allowed_plan_ids = form.allowed_plan_ids.filter((p) => p !== strId);
        if (form.allowed_plan_ids.length === 0) {
            form.allowed_plan_ids = ['all'];
        }
    } else {
        form.allowed_plan_ids.push(strId);
    }
}

// AI generate explanation
const isGeneratingAi = ref(false);
async function generateAiExplanation() {
    if (!form.question_text) {
        alert('অনুগ্রহ করে আগে প্রশ্নের মূলভাব (Question text) লিখুন।');
        return;
    }

    isGeneratingAi.value = true;
    try {
        const selectedSubject = props.subjects.find((s) => s.id === form.subject_id);
        const response = await fetch('/admin/question-bank/ai-explain', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                question_text: form.question_text,
                options: form.options,
                correct_option: form.correct_option,
                subject_name: selectedSubject?.name_bn || '',
            }),
        });

        const data = await response.json();
        if (data.success && data.explanation) {
            form.explanation = data.explanation;
        } else {
            alert('AI ব্যাখ্যা তৈরি করতে পারেনি। অনুগ্রহ করে ম্যানুয়ালি লিখুন।');
        }
    } catch (e) {
        alert('AI সার্ভারে সংযোগে সমস্যা হয়েছে: ' + e.message);
    } finally {
        isGeneratingAi.value = false;
    }
}

function submit() {
    if (isEditing.value) {
        form.put(`/admin/question-bank/${props.question.id}`);
    } else {
        form.post('/admin/question-bank');
    }
}
</script>

<template>
    <AdminLayout>
        <Head :title="isEditing ? 'প্রশ্ন সম্পাদনা' : 'নতুন প্রশ্ন তৈরি'" />

        <div class="max-w-4xl mx-auto space-y-6 pb-12">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <Link
                        href="/admin/question-bank"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-primary mb-2 transition"
                    >
                        <ArrowLeftIcon class="w-4 h-4" />
                        <span>প্রশ্ন ব্যাংকে ফিরে যান</span>
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ isEditing ? 'প্রশ্ন তথ্য সম্পাদনা' : 'নতুন প্রশ্ন যুক্ত করুন' }}
                    </h1>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Group & Access Controls -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-5">
                    <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700/60 pb-3">
                        <UserGroupIcon class="w-5 h-5 text-primary" />
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">
                            টার্গেট শিক্ষার্থী ও সাবস্ক্রিপশন প্ল্যান এক্সেস (Group Access)
                        </h2>
                    </div>

                    <!-- Target Student Types -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                            টার্গেট স্টুডেন্ট গ্রুপ (Student Types):
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            <button
                                type="button"
                                @click="toggleStudentType('all')"
                                class="flex items-center gap-2 p-2.5 rounded-xl border text-left text-xs font-bold transition"
                                :class="form.allowed_student_types.includes('all')
                                    ? 'bg-primary/10 border-primary text-primary'
                                    : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300'"
                            >
                                <CheckCircleIcon class="w-4 h-4" :class="form.allowed_student_types.includes('all') ? 'text-primary' : 'text-gray-400'" />
                                <span>সকল শিক্ষার্থী (All)</span>
                            </button>

                            <button
                                v-for="st in studentTypes"
                                :key="st.key"
                                type="button"
                                @click="toggleStudentType(st.key)"
                                class="flex items-center gap-2 p-2.5 rounded-xl border text-left text-xs font-bold transition"
                                :class="form.allowed_student_types.includes(st.key) && !form.allowed_student_types.includes('all')
                                    ? 'bg-primary/10 border-primary text-primary'
                                    : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300'"
                            >
                                <CheckCircleIcon class="w-4 h-4" :class="form.allowed_student_types.includes(st.key) && !form.allowed_student_types.includes('all') ? 'text-primary' : 'text-gray-400'" />
                                <span>{{ st.label }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Target Subscription Plans -->
                    <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700/60">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                            সাবস্ক্রিপশন প্ল্যান এক্সেস (Plan Access):
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <button
                                type="button"
                                @click="togglePlan('all')"
                                class="flex items-center gap-2 p-2.5 rounded-xl border text-left text-xs font-bold transition"
                                :class="form.allowed_plan_ids.includes('all')
                                    ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-800 dark:text-emerald-200'
                                    : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300'"
                            >
                                <CheckCircleIcon class="w-4 h-4" :class="form.allowed_plan_ids.includes('all') ? 'text-emerald-600' : 'text-gray-400'" />
                                <span>উন্মুক্ত / All Plans</span>
                            </button>

                            <button
                                type="button"
                                @click="togglePlan('free')"
                                class="flex items-center gap-2 p-2.5 rounded-xl border text-left text-xs font-bold transition"
                                :class="form.allowed_plan_ids.includes('free') && !form.allowed_plan_ids.includes('all')
                                    ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-800 dark:text-emerald-200'
                                    : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300'"
                            >
                                <CheckCircleIcon class="w-4 h-4" :class="form.allowed_plan_ids.includes('free') && !form.allowed_plan_ids.includes('all') ? 'text-emerald-600' : 'text-gray-400'" />
                                <span>ফ্রি প্ল্যান (Free)</span>
                            </button>

                            <button
                                v-for="plan in plans"
                                :key="plan.id"
                                type="button"
                                @click="togglePlan(plan.id)"
                                class="flex items-center gap-2 p-2.5 rounded-xl border text-left text-xs font-bold transition"
                                :class="form.allowed_plan_ids.includes(String(plan.id)) && !form.allowed_plan_ids.includes('all')
                                    ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-800 dark:text-emerald-200'
                                    : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300'"
                            >
                                <CheckCircleIcon class="w-4 h-4" :class="form.allowed_plan_ids.includes(String(plan.id)) && !form.allowed_plan_ids.includes('all') ? 'text-emerald-600' : 'text-gray-400'" />
                                <span>{{ plan.name }} (৳{{ plan.price_monthly }})</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Basic Meta Info -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700/60 pb-3">
                        মৌলিক তথ্য ও ক্যাটাগরি
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Segment -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">ক্যাটাগরি *</label>
                            <select
                                v-model="form.segment"
                                class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900"
                            >
                                <option value="admission">বিশ্ববিদ্যালয় ভর্তি (Admission)</option>
                                <option value="job">চাকরি ও বিসিএস (Job)</option>
                            </select>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">বিষয় *</label>
                            <select
                                v-model="form.subject_id"
                                class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900"
                            >
                                <option v-for="s in subjects" :key="s.id" :value="s.id">
                                    {{ s.name_bn }} ({{ s.name_en }})
                                </option>
                            </select>
                        </div>

                        <!-- Origin Tag -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">অরিজিন ট্যাগ *</label>
                            <input
                                v-model="form.origin_tag"
                                type="text"
                                placeholder="DU-24 / BCS-46"
                                class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 font-mono font-bold uppercase"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Topic Name -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">অধ্যায় / টপিক</label>
                            <input
                                v-model="form.topic_name"
                                type="text"
                                placeholder="যেমন: মহাকর্ষ ও অভিকর্ষ"
                                class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900"
                            />
                        </div>

                        <!-- Institution -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">প্রতিষ্ঠান</label>
                            <input
                                v-model="form.institution"
                                type="text"
                                placeholder="যেমন: University of Dhaka"
                                class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900"
                            />
                        </div>

                        <!-- Year -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">সাল (Year)</label>
                            <input
                                v-model.number="form.year"
                                type="number"
                                placeholder="2024"
                                class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Difficulty -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">কঠিনতা *</label>
                            <select
                                v-model="form.difficulty"
                                class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900"
                            >
                                <option value="easy">Easy (সহজ)</option>
                                <option value="medium">Medium (মাঝারি)</option>
                                <option value="hard">Hard (কঠিন)</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">স্ট্যাটাস *</label>
                            <select
                                v-model="form.status"
                                class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900"
                            >
                                <option value="published">Published</option>
                                <option value="pending_review">Pending Review</option>
                                <option value="draft">Draft</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <!-- Source Registry -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">উৎস (Question Source)</label>
                            <select
                                v-model="form.source_id"
                                class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900"
                            >
                                <option :value="''">কোনো সোর্স নির্দিষ্ট নেই</option>
                                <option v-for="src in sources" :key="src.id" :value="src.id">
                                    {{ src.name }} ({{ src.institution || 'N/A' }})
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Question Text -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60 pb-3">
                        <span>প্রশ্নের মূলভাব (Question Stem) *</span>
                        <span class="text-xs font-normal text-gray-400">LaTeX সমর্থন: $ সূত্র $ অথবা $$ সূত্র $$</span>
                    </h3>

                    <textarea
                        v-model="form.question_text"
                        rows="4"
                        required
                        placeholder="প্রশ্নের মূলভাব লিখুন। প্রয়োজনে LaTeX গণিত চিহ্ন ($E=mc^2$) ব্যবহার করুন।"
                        class="w-full text-xs sm:text-sm px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white leading-relaxed"
                    ></textarea>

                    <!-- Live Math Preview -->
                    <div v-if="form.question_text" class="p-4 bg-primary/[0.03] dark:bg-primary/[0.06] rounded-xl border border-primary/20">
                        <div class="text-[11px] font-bold text-primary uppercase tracking-wider mb-1">
                            লাইভ প্রিভিউ (KaTeX Math Preview):
                        </div>
                        <div class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed" v-html="renderMath(form.question_text)"></div>
                    </div>
                </div>

                <!-- Options -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                            বিকল্পসমূহ ও সঠিক উত্তর (Options & Correct Answer)
                        </h3>
                        <button
                            type="button"
                            @click="addOption"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200"
                        >
                            <PlusIcon class="w-3.5 h-3.5" />
                            <span>অপশন যোগ করুন</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(opt, idx) in form.options"
                            :key="idx"
                            class="flex items-center gap-3 p-3 rounded-xl border transition-colors"
                            :class="form.correct_option === opt.key ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-700' : 'bg-gray-50/50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700'"
                        >
                            <label class="flex items-center gap-1.5 cursor-pointer shrink-0">
                                <input
                                    type="radio"
                                    name="correct_option"
                                    :value="opt.key"
                                    v-model="form.correct_option"
                                    class="text-emerald-600 focus:ring-emerald-500"
                                />
                                <span class="font-bold text-xs" :class="form.correct_option === opt.key ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-500'">
                                    {{ opt.key }}
                                </span>
                            </label>

                            <input
                                v-model="opt.text"
                                type="text"
                                required
                                :placeholder="`অপশন ${opt.key}-এর মান লিখুন (LaTeX গণিত অনুমোদিত)`"
                                class="flex-1 text-xs px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                            />

                            <div class="min-w-[80px] max-w-[200px] text-xs px-2 truncate" v-html="renderMath(opt.text)"></div>

                            <button
                                v-if="form.options.length > 2"
                                type="button"
                                @click="removeOption(idx)"
                                class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40"
                            >
                                <TrashIcon class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Explanation with AI Assistance -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <SparklesIcon class="w-4 h-4 text-primary" />
                                <span>বিস্তারিত ব্যাখ্যা ও কনসেপ্ট বিশ্লেষণ (Solution & Explanation)</span>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                প্রতিটি ধাপের সূত্র, ব্যাকরণ বা ঐতিহাসিক তথ্য সুন্দর করে উপস্থাপন করুন।
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="generateAiExplanation"
                            :disabled="isGeneratingAi"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-primary to-indigo-600 text-white text-xs font-bold shadow-sm hover:opacity-95 disabled:opacity-50 transition"
                        >
                            <SparklesIcon class="w-3.5 h-3.5" :class="{ 'animate-spin': isGeneratingAi }" />
                            <span>{{ isGeneratingAi ? 'AI তৈরি করছে...' : '✨ AI দ্বারা ব্যাখ্যা তৈরি করুন' }}</span>
                        </button>
                    </div>

                    <textarea
                        v-model="form.explanation"
                        rows="5"
                        placeholder="প্রশ্নের সঠিক সমাধানের ব্যাখ্যা দিন। গাণিতিক সূত্রের জন্য $$...$$ ব্যবহার করতে পারেন।"
                        class="w-full text-xs px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white leading-relaxed font-mono"
                    ></textarea>

                    <div v-if="form.explanation" class="p-4 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-200/80 dark:border-gray-700">
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                            ব্যাখ্যার লাইভ প্রিভিউ:
                        </div>
                        <div class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-wrap" v-html="renderMath(form.explanation)"></div>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <Link
                        href="/admin/question-bank"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50"
                    >
                        বাতিল
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary/90 shadow-md shadow-primary/20 disabled:opacity-50 transition active:scale-95"
                    >
                        {{ form.processing ? 'সংরক্ষণ হচ্ছে...' : (isEditing ? 'পরিবর্তন সংরক্ষণ করুন' : 'প্রশ্ন সেভ করুন') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
