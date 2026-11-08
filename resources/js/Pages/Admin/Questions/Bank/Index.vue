<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { renderMath } from '@/Utils/renderMath';
import {
    AcademicCapIcon,
    BriefcaseIcon,
    PlusIcon,
    ArrowUpTrayIcon,
    ScaleIcon,
    PencilSquareIcon,
    TrashIcon,
    CheckCircleIcon,
    SparklesIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    TagIcon,
    BuildingOfficeIcon,
    UserGroupIcon,
    LockClosedIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    questions: Object,
    stats: Object,
    subjects: Array,
    originTags: Array,
    years: Array,
    filters: Object,
});

const filterForm = reactive({
    search: props.filters.search || '',
    segment: props.filters.segment || 'all',
    subject_id: props.filters.subject_id || 'all',
    origin_tag: props.filters.origin_tag || 'all',
    difficulty: props.filters.difficulty || 'all',
    status: props.filters.status || 'all',
    year: props.filters.year || 'all',
    student_type: props.filters.student_type || 'all',
});

function applyFilters() {
    router.get('/admin/question-bank', filterForm, {
        preserveState: true,
        replace: true,
    });
}

function setSegment(seg) {
    filterForm.segment = seg;
    applyFilters();
}

function resetFilters() {
    filterForm.search = '';
    filterForm.segment = 'all';
    filterForm.subject_id = 'all';
    filterForm.origin_tag = 'all';
    filterForm.difficulty = 'all';
    filterForm.status = 'all';
    filterForm.year = 'all';
    filterForm.student_type = 'all';
    applyFilters();
}

// Track expanded explanations
const expandedQuestions = ref(new Set());

function toggleExplanation(id) {
    if (expandedQuestions.value.has(id)) {
        expandedQuestions.value.delete(id);
    } else {
        expandedQuestions.value.add(id);
    }
}

function deleteQuestion(q) {
    router.delete(`/admin/question-bank/${q.id}`, {
        preserveScroll: true,
    });
}

function formatStudentTypes(types) {
    if (!types || !Array.isArray(types) || types.length === 0 || types.includes('all')) {
        return 'উন্মুক্ত (All)';
    }
    return types.map(t => t.toUpperCase()).join(', ');
}

function formatPlanIds(plans) {
    if (!plans || !Array.isArray(plans) || plans.length === 0 || plans.includes('all')) {
        return 'সকল প্ল্যান (All)';
    }
    return plans.map(p => {
        if (p === 'free') return 'Free';
        return `Plan #${p}`;
    }).join(', ');
}
</script>

<template>
    <AdminLayout>
        <Head title="প্রশ্ন ভাণ্ডার ও প্রশ্ন ব্যাংক (Question Bank)" />

        <div class="space-y-6 pb-12">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-primary">
                        <AcademicCapIcon class="w-4 h-4" />
                        <span>Centralized Question Bank</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        প্রশ্ন ভাণ্ডার (Question Bank)
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        বিশ্ববিদ্যালয় ভর্তি (DU, BUET, Medical) ও চাকরির (BCS, Bank) শ্রেণীবদ্ধ প্রশ্ন, সমাধান ও গ্রুপ এক্সেস ব্যবস্থাপনা।
                    </p>
                </div>

                <div class="flex items-center flex-wrap gap-2.5">
                    <Link
                        href="/admin/question-bank/generate"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 via-indigo-600 to-primary text-white text-xs font-bold hover:opacity-95 shadow-md shadow-purple-500/20 transition active:scale-95"
                    >
                        <SparklesIcon class="w-4 h-4" />
                        <span>AI প্রশ্ন জেনারেটর</span>
                    </Link>
                    <Link
                        href="/admin/question-sources"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                    >
                        <ScaleIcon class="w-4 h-4 text-primary" />
                        <span>সোর্স ও অনুমতি</span>
                    </Link>
                    <Link
                        href="/admin/question-import/pdf"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 text-xs font-semibold hover:bg-purple-100 transition"
                    >
                        <ArrowUpTrayIcon class="w-4 h-4" />
                        <span>পিডিএফ OCR ইমপোর্ট</span>
                    </Link>
                    <Link
                        href="/admin/question-bank/create"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary/90 shadow-sm shadow-primary/20 transition active:scale-95"
                    >
                        <PlusIcon class="w-4 h-4" />
                        <span>নতুন প্রশ্ন যুক্ত করুন</span>
                    </Link>
                </div>
            </div>

            <!-- Stats & Segment Quick Switcher -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div
                    @click="setSegment('all')"
                    class="cursor-pointer p-4 rounded-2xl border transition-all"
                    :class="filterForm.segment === 'all' ? 'bg-primary/5 border-primary shadow-sm' : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 hover:border-gray-300'"
                >
                    <div class="text-xs font-semibold text-gray-500">সর্বমোট প্রশ্ন</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ stats.total }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">সব ক্যাটাগরি মিলিয়ে</div>
                </div>

                <div
                    @click="setSegment('admission')"
                    class="cursor-pointer p-4 rounded-2xl border transition-all"
                    :class="filterForm.segment === 'admission' ? 'bg-blue-50/70 dark:bg-blue-950/30 border-blue-500 shadow-sm' : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 hover:border-gray-300'"
                >
                    <div class="text-xs font-semibold text-blue-600 dark:text-blue-400">🎓 বিশ্ববিদ্যালয় ভর্তি (Admission)</div>
                    <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ stats.admission }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">DU, BUET, Medical, RU, GST</div>
                </div>

                <div
                    @click="setSegment('job')"
                    class="cursor-pointer p-4 rounded-2xl border transition-all"
                    :class="filterForm.segment === 'job' ? 'bg-emerald-50/70 dark:bg-emerald-950/30 border-emerald-500 shadow-sm' : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 hover:border-gray-300'"
                >
                    <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">💼 সরকারি চাকরি ও ব্যাংক (Job)</div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ stats.job }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">BCS, 9-Bank, Primary, Govt</div>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                    <div class="text-xs font-semibold text-purple-600 dark:text-purple-400">ভেরিফাইড ও নির্ভুল প্রশ্ন</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ stats.verified }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">LaTeX ও কনসেপ্ট সমর্থিত</div>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 shadow-sm space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
                    <!-- Search input -->
                    <div class="col-span-1 sm:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">কীওয়ার্ড খুঁজুন</label>
                        <input
                            v-model="filterForm.search"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="প্রশ্নের অংশ, প্রতিষ্ঠান বা ট্যাগ..."
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"
                        />
                    </div>

                    <!-- Student Type Filter -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">টার্গেট গ্রুপ</label>
                        <select
                            v-model="filterForm.student_type"
                            @change="applyFilters"
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"
                        >
                            <option value="all">সব শিক্ষার্থী</option>
                            <option value="ssc">SSC</option>
                            <option value="hsc">HSC</option>
                            <option value="admission">Admission</option>
                            <option value="job">Job</option>
                            <option value="university">University</option>
                        </select>
                    </div>

                    <!-- Subject filter -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">বিষয়</label>
                        <select
                            v-model="filterForm.subject_id"
                            @change="applyFilters"
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"
                        >
                            <option value="all">সব বিষয়</option>
                            <option v-for="s in subjects" :key="s.id" :value="s.id">
                                {{ s.name_bn }}
                            </option>
                        </select>
                    </div>

                    <!-- Origin Tag filter -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">পরীক্ষার ট্যাগ</label>
                        <select
                            v-model="filterForm.origin_tag"
                            @change="applyFilters"
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"
                        >
                            <option value="all">সব ট্যাগ</option>
                            <option v-for="tag in originTags" :key="tag" :value="tag">
                                {{ tag }}
                            </option>
                        </select>
                    </div>

                    <!-- Difficulty -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">কঠিনতা</label>
                        <select
                            v-model="filterForm.difficulty"
                            @change="applyFilters"
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"
                        >
                            <option value="all">সব মাত্রা</option>
                            <option value="easy">Easy (সহজ)</option>
                            <option value="medium">Medium (মাঝারি)</option>
                            <option value="hard">Hard (কঠিন)</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">স্ট্যাটাস</label>
                        <select
                            v-model="filterForm.status"
                            @change="applyFilters"
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"
                        >
                            <option value="all">সব স্ট্যাটাস</option>
                            <option value="published">Published</option>
                            <option value="pending_review">Pending Review</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                    <!-- Filter / Reset buttons -->
                    <div class="flex items-end gap-1.5">
                        <button
                            type="button"
                            @click="applyFilters"
                            class="flex-1 px-3 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90 transition"
                        >
                            ফিল্টার
                        </button>
                        <button
                            type="button"
                            @click="resetFilters"
                            class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-medium rounded-xl hover:bg-gray-200 transition"
                        >
                            রিসেট
                        </button>
                    </div>
                </div>
            </div>

            <!-- Questions List -->
            <div class="space-y-4">
                <div
                    v-for="(q, idx) in questions.data"
                    :key="q.id"
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow"
                >
                    <!-- Question Header & Badges -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-gray-100 dark:border-gray-700/60 text-xs">
                        <div class="flex items-center flex-wrap gap-2">
                            <!-- Origin Tag Pill -->
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg font-black tracking-wide text-xs bg-primary/10 text-primary border border-primary/20">
                                <TagIcon class="w-3.5 h-3.5" />
                                <span>{{ q.origin_tag }}</span>
                            </span>

                            <!-- Segment -->
                            <span
                                class="px-2 py-0.5 rounded-md font-semibold text-[11px]"
                                :class="q.segment === 'admission' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'"
                            >
                                {{ q.segment === 'admission' ? 'ভর্তি পরীক্ষা' : 'চাকরি প্রস্তুতি' }}
                            </span>

                            <!-- Subject & Topic -->
                            <span class="font-medium text-gray-700 dark:text-gray-300">
                                {{ q.subject?.name_bn || 'সাধারণ' }}
                                <span v-if="q.topic_name" class="text-gray-400">· {{ q.topic_name }}</span>
                            </span>

                            <!-- Target Student Group Badge -->
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold border border-indigo-200/60 dark:border-indigo-800/60">
                                <UserGroupIcon class="w-3 h-3" />
                                <span>{{ formatStudentTypes(q.allowed_student_types) }}</span>
                            </span>

                            <!-- Target Plan Badge -->
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold border border-emerald-200/60 dark:border-emerald-800/60">
                                <LockClosedIcon class="w-3 h-3" />
                                <span>{{ formatPlanIds(q.allowed_plan_ids) }}</span>
                            </span>

                            <!-- Exam Name / Institution -->
                            <span v-if="q.institution" class="hidden sm:inline-block text-gray-400 text-[11px]">
                                ({{ q.institution }}<span v-if="q.year">, {{ q.year }}</span>)
                            </span>
                        </div>

                        <!-- Right Badges: Difficulty & Actions -->
                        <div class="flex items-center gap-2">
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                                :class="{
                                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300': q.difficulty === 'easy',
                                    'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300': q.difficulty === 'medium',
                                    'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300': q.difficulty === 'hard',
                                }"
                            >
                                {{ q.difficulty }}
                            </span>
                            <Link
                                :href="`/admin/question-bank/${q.id}/edit`"
                                class="p-1.5 text-gray-400 hover:text-primary rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                title="সম্পাদনা করুন"
                            >
                                <PencilSquareIcon class="w-4 h-4" />
                            </Link>
                            <ConfirmButton
                                @confirmed="deleteQuestion(q)"
                                title="প্রশ্ন মুছে ফেলুন"
                                message="আপনি কি নিশ্চিত এই প্রশ্নটি মুছে ফেলতে চান?"
                                class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition"
                            >
                                <TrashIcon class="w-4 h-4" />
                            </ConfirmButton>
                        </div>
                    </div>

                    <!-- Question Stem -->
                    <div class="pt-4 pb-3">
                        <div
                            class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white leading-relaxed"
                            v-html="renderMath(q.question_text)"
                        ></div>
                    </div>

                    <!-- Options Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                        <div
                            v-for="opt in q.options"
                            :key="opt.key"
                            class="flex items-start gap-3 p-3 rounded-xl border text-sm transition-all"
                            :class="opt.key.toUpperCase() === q.correct_option?.toUpperCase()
                                ? 'bg-emerald-50/70 dark:bg-emerald-950/30 border-emerald-300 dark:border-emerald-700 text-emerald-950 dark:text-emerald-100 font-semibold shadow-sm'
                                : 'bg-gray-50/50 dark:bg-gray-900/40 border-gray-200/80 dark:border-gray-700 text-gray-800 dark:text-gray-200'"
                        >
                            <span
                                class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-black shrink-0"
                                :class="opt.key.toUpperCase() === q.correct_option?.toUpperCase()
                                    ? 'bg-emerald-600 text-white shadow-sm'
                                    : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            >
                                {{ opt.key }}
                            </span>
                            <div class="flex-1 leading-snug pt-0.5" v-html="renderMath(opt.text)"></div>
                            <span
                                v-if="opt.key.toUpperCase() === q.correct_option?.toUpperCase()"
                                class="text-emerald-600 dark:text-emerald-400 text-xs font-bold shrink-0 self-center"
                            >
                                ✓ সঠিক উত্তর
                            </span>
                        </div>
                    </div>

                    <!-- Explanation Toggle & AI Analysis Box -->
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60">
                        <button
                            type="button"
                            @click="toggleExplanation(q.id)"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:underline transition"
                        >
                            <SparklesIcon class="w-4 h-4" />
                            <span>{{ expandedQuestions.has(q.id) ? 'বিশ্লেষণ ও ব্যাখ্যা বন্ধ করুন' : 'বিস্তারিত সমাধান ও AI কনসেপ্ট ব্যাখ্যা দেখুন' }}</span>
                            <component :is="expandedQuestions.has(q.id) ? ChevronUpIcon : ChevronDownIcon" class="w-3.5 h-3.5" />
                        </button>

                        <div
                            v-if="expandedQuestions.has(q.id)"
                            class="mt-3 p-4 bg-primary/[0.03] dark:bg-primary/[0.06] rounded-xl border border-primary/20 text-xs text-gray-800 dark:text-gray-200 leading-relaxed space-y-2 animate-in fade-in"
                        >
                            <div class="flex items-center gap-1.5 font-bold text-primary text-xs">
                                <SparklesIcon class="w-4 h-4" />
                                <span>ব্যাখ্যা ও সূত্র বিশ্লেষণ (Solution Analysis):</span>
                            </div>
                            <div class="pt-1 whitespace-pre-wrap leading-relaxed" v-html="renderMath(q.explanation || 'এই প্রশ্নের জন্য এখনো কোনো বিস্তারিত ব্যাখ্যা প্রদান করা হয়নি।')"></div>
                        </div>
                    </div>
                </div>

                <div v-if="!questions.data || questions.data.length === 0" class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
                    <AcademicCapIcon class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                    <div class="text-base font-bold text-gray-800 dark:text-gray-200">কোনো প্রশ্ন পাওয়া যায়নি</div>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                        অন্য কোনো বিষয় বা ফিল্টার দিয়ে চেষ্টা করুন অথবা নতুন প্রশ্ন তৈরি বা পিডিএফ ইমপোর্ট করুন।
                    </p>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <Link
                            href="/admin/question-bank/generate"
                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-primary text-white text-xs font-semibold rounded-xl hover:opacity-95"
                        >
                            AI দিয়ে প্রশ্ন জেনারেট করুন
                        </Link>
                        <Link
                            href="/admin/question-bank/create"
                            class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90"
                        >
                            ম্যানুয়ালি প্রশ্ন তৈরি
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="questions.links && questions.links.length > 3" class="pt-2">
                <Pagination :links="questions.links" />
            </div>
        </div>
    </AdminLayout>
</template>
