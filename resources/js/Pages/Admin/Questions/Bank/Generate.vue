<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import {
    SparklesIcon,
    ArrowLeftIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    ShieldCheckIcon,
    UserGroupIcon,
    CreditCardIcon,
    CheckCircleIcon,
    InformationCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    subjects: Array,
    plans: Array,
    studentTypes: Array,
});

const form = useForm({
    student_types: ['admission'],
    plan_ids: ['all'],
    segment: 'admission',
    subject_id: props.subjects[0]?.id || '',
    topic_name: '',
    origin_tag: 'DU-25',
    institution: 'University of Dhaka',
    difficulty: 'medium',
    count: 5,
    status: 'published',
});

// Sync segment when subject changes
function onSubjectChange() {
    const selected = props.subjects.find((s) => s.id === form.subject_id);
    if (selected && selected.segment) {
        form.segment = selected.segment;
        if (selected.segment === 'job') {
            form.origin_tag = 'BCS-47';
            form.institution = 'BPSC / Bangladesh Bank';
            if (!form.student_types.includes('job')) {
                form.student_types = ['job'];
            }
        } else {
            form.origin_tag = 'DU-25';
            form.institution = 'University of Dhaka';
            if (!form.student_types.includes('admission')) {
                form.student_types = ['admission'];
            }
        }
    }
}

// Quick origin tag suggestions
const quickTags = computed(() => {
    if (form.segment === 'job') {
        return ['BCS-47', 'BCS-46', 'COMB-BANK-25', 'BB-AD-24', 'PRIMARY-25', 'NTRCA-19'];
    }
    return ['DU-25', 'BUET-25', 'MED-25', 'RU-25', 'CU-25', 'GST-25', 'HSC-25', 'SSC-25'];
});

function setTag(tag) {
    form.origin_tag = tag;
}

// Student type toggle helpers
function toggleStudentType(typeKey) {
    if (typeKey === 'all') {
        form.student_types = ['all'];
        return;
    }
    // Remove 'all' if present
    form.student_types = form.student_types.filter((t) => t !== 'all');
    if (form.student_types.includes(typeKey)) {
        form.student_types = form.student_types.filter((t) => t !== typeKey);
        if (form.student_types.length === 0) {
            form.student_types = ['all'];
        }
    } else {
        form.student_types.push(typeKey);
    }
}

function selectAllStudentTypes() {
    form.student_types = ['all'];
}

// Plan toggle helpers
function togglePlan(planId) {
    if (planId === 'all') {
        form.plan_ids = ['all'];
        return;
    }
    form.plan_ids = form.plan_ids.filter((p) => p !== 'all');
    const strId = String(planId);
    if (form.plan_ids.includes(strId)) {
        form.plan_ids = form.plan_ids.filter((p) => p !== strId);
        if (form.plan_ids.length === 0) {
            form.plan_ids = ['all'];
        }
    } else {
        form.plan_ids.push(strId);
    }
}

function selectAllPlans() {
    form.plan_ids = ['all'];
}

function submit() {
    form.post('/admin/question-bank/generate');
}
</script>

<template>
    <AdminLayout>
        <Head title="AI প্রশ্ন জেনারেটর ও গ্রুপ পাবলিশার" />

        <div class="max-w-4xl mx-auto space-y-6 pb-16">
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
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-purple-100 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                            <SparklesIcon class="w-5 h-5" />
                        </span>
                        <h1 class="text-2xl font-black text-gray-900 dark:text-white">
                            AI প্রশ্ন জেনারেটর ও গ্রুপ পাবলিশিং
                        </h1>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                        কৃত্রিম বুদ্ধিমত্তা দিয়ে নির্দিষ্ট প্ল্যান ও স্টুডেন্ট ক্যাটাগরির (SSC, HSC, Admission, Job, University) জন্য নির্ভুল প্রশ্ন, অপশন ও LaTeX সমাধান জেনারেট করুন।
                    </p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Group 1: Target Student Types -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60 pb-3">
                        <div class="flex items-center gap-2">
                            <UserGroupIcon class="w-5 h-5 text-primary" />
                            <h2 class="text-sm font-bold text-gray-900 dark:text-white">
                                ১. টার্গেট শিক্ষার্থী গ্রুপ নির্বাচন (Target Student Types)
                            </h2>
                        </div>
                        <button
                            type="button"
                            @click="selectAllStudentTypes"
                            class="text-xs font-bold text-primary hover:underline"
                        >
                            সকলকে অনুমোদন দিন (All)
                        </button>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        যেসব শিক্ষার্থী ক্যাটাগরির অ্যাকাউন্টে এই প্রশ্নগুলো দেখানো হবে তা নির্বাচন করুন:
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <button
                            type="button"
                            @click="toggleStudentType('all')"
                            class="flex items-center gap-2.5 p-3 rounded-xl border text-left text-xs font-bold transition-all"
                            :class="form.student_types.includes('all')
                                ? 'bg-primary/10 border-primary text-primary shadow-sm ring-1 ring-primary'
                                : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-gray-400'"
                        >
                            <CheckCircleIcon class="w-4 h-4 shrink-0" :class="form.student_types.includes('all') ? 'text-primary' : 'text-gray-400'" />
                            <span>সকল শিক্ষার্থী (All)</span>
                        </button>

                        <button
                            v-for="st in studentTypes"
                            :key="st.key"
                            type="button"
                            @click="toggleStudentType(st.key)"
                            class="flex items-center gap-2.5 p-3 rounded-xl border text-left text-xs font-bold transition-all"
                            :class="form.student_types.includes(st.key) && !form.student_types.includes('all')
                                ? 'bg-primary/10 border-primary text-primary shadow-sm ring-1 ring-primary'
                                : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-gray-400'"
                        >
                            <CheckCircleIcon
                                class="w-4 h-4 shrink-0"
                                :class="form.student_types.includes(st.key) && !form.student_types.includes('all') ? 'text-primary' : 'text-gray-400'"
                            />
                            <span>{{ st.label }}</span>
                        </button>
                    </div>
                    <div v-if="form.errors.student_types" class="text-xs text-rose-500 mt-1">
                        {{ form.errors.student_types }}
                    </div>
                </div>

                <!-- Group 2: Target Subscription Plans -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60 pb-3">
                        <div class="flex items-center gap-2">
                            <CreditCardIcon class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                            <h2 class="text-sm font-bold text-gray-900 dark:text-white">
                                ২. সাবস্ক্রিপশন প্ল্যান এক্সেস (Subscription Access Group)
                            </h2>
                        </div>
                        <button
                            type="button"
                            @click="selectAllPlans"
                            class="text-xs font-bold text-emerald-600 hover:underline"
                        >
                            সকল প্ল্যান ও ফ্রি ইউজার (All)
                        </button>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        যেসব প্ল্যানের শিক্ষার্থীরা সমাধান ও ব্যাখ্যা দেখতে পাবে। ফ্রি ব্যবহারকারী প্রশ্ন দেখতে পেলেও সমাধান আনলক করতে প্ল্যান আপগ্রেড করতে হবে।
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <button
                            type="button"
                            @click="togglePlan('all')"
                            class="flex items-center gap-2.5 p-3 rounded-xl border text-left text-xs font-bold transition-all"
                            :class="form.plan_ids.includes('all')
                                ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-800 dark:text-emerald-200 ring-1 ring-emerald-500'
                                : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-gray-400'"
                        >
                            <CheckCircleIcon class="w-4 h-4 shrink-0" :class="form.plan_ids.includes('all') ? 'text-emerald-600' : 'text-gray-400'" />
                            <span>উন্মুক্ত / All Plans</span>
                        </button>

                        <button
                            type="button"
                            @click="togglePlan('free')"
                            class="flex items-center gap-2.5 p-3 rounded-xl border text-left text-xs font-bold transition-all"
                            :class="form.plan_ids.includes('free') && !form.plan_ids.includes('all')
                                ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-800 dark:text-emerald-200 ring-1 ring-emerald-500'
                                : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-gray-400'"
                        >
                            <CheckCircleIcon class="w-4 h-4 shrink-0" :class="form.plan_ids.includes('free') && !form.plan_ids.includes('all') ? 'text-emerald-600' : 'text-gray-400'" />
                            <span>ফ্রি প্ল্যান (Free)</span>
                        </button>

                        <button
                            v-for="plan in plans"
                            :key="plan.id"
                            type="button"
                            @click="togglePlan(plan.id)"
                            class="flex items-center gap-2.5 p-3 rounded-xl border text-left text-xs font-bold transition-all"
                            :class="form.plan_ids.includes(String(plan.id)) && !form.plan_ids.includes('all')
                                ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-800 dark:text-emerald-200 ring-1 ring-emerald-500'
                                : 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-gray-400'"
                        >
                            <CheckCircleIcon
                                class="w-4 h-4 shrink-0"
                                :class="form.plan_ids.includes(String(plan.id)) && !form.plan_ids.includes('all') ? 'text-emerald-600' : 'text-gray-400'"
                            />
                            <div>
                                <div>{{ plan.name }}</div>
                                <div class="text-[10px] text-gray-400 font-normal">৳{{ plan.price_monthly }}/মাস</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Group 3: Subject & Topic Details -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm space-y-5">
                    <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700/60 pb-3">
                        <AcademicCapIcon class="w-5 h-5 text-indigo-500" />
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">
                            ৩. একাডেমিক বিষয়, টপিক ও পরীক্ষার উৎস
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Subject -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                বিষয় (Subject) <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.subject_id"
                                @change="onSubjectChange"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option v-for="sub in subjects" :key="sub.id" :value="sub.id">
                                    {{ sub.name_bn }} ({{ sub.name_en }}) — {{ sub.segment === 'job' ? '💼 জব' : '🎓 ভর্তি' }}
                                </option>
                            </select>
                        </div>

                        <!-- Topic / Chapter -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                অধ্যায় / টপিক কিওয়ার্ড (Topic / Focus)
                            </label>
                            <input
                                v-model="form.topic_name"
                                type="text"
                                placeholder="যেমন: মহাকর্ষ ও অভিকর্ষ / Subject-Verb Agreement"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>

                        <!-- Origin Tag -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                অরিজিন ট্যাগ (Origin Tag) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.origin_tag"
                                type="text"
                                placeholder="যেমন: DU-25, BCS-47, BUET-25"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-mono font-bold uppercase focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                            <!-- Quick Tags -->
                            <div class="flex items-center gap-1.5 flex-wrap mt-2">
                                <span class="text-[10px] text-gray-400">কুইক সিলেক্ট:</span>
                                <button
                                    v-for="tag in quickTags"
                                    :key="tag"
                                    type="button"
                                    @click="setTag(tag)"
                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-primary hover:text-white transition"
                                >
                                    {{ tag }}
                                </button>
                            </div>
                        </div>

                        <!-- Institution -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                শিক্ষা প্রতিষ্ঠান / কমিশন (Institution)
                            </label>
                            <input
                                v-model="form.institution"
                                type="text"
                                placeholder="যেমন: University of Dhaka / BPSC"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>
                    </div>
                </div>

                <!-- Group 4: Generation Parameters -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm space-y-5">
                    <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700/60 pb-3">
                        <SparklesIcon class="w-5 h-5 text-amber-500" />
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">
                            ৪. জেনারেশন প্যারামিটার ও প্রশ্নের সংখ্যা
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Count -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                প্রশ্নের সংখ্যা (Question Count) <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model.number="form.count"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option :value="3">৩ টি প্রশ্ন (কুইক টেস্টিং)</option>
                                <option :value="5">৫ টি প্রশ্ন (স্ট্যান্ডার্ড)</option>
                                <option :value="10">১০ টি প্রশ্ন (ব্যাচ)</option>
                                <option :value="15">১৫ টি প্রশ্ন</option>
                                <option :value="20">২০ টি প্রশ্ন (পূর্ণাঙ্গ সেট)</option>
                            </select>
                        </div>

                        <!-- Difficulty -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                কাঠিন্যের মাত্রা (Difficulty) <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.difficulty"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="easy">সহজ (Easy)</option>
                                <option value="medium">মাঝারি (Medium - Standard)</option>
                                <option value="hard">কঠিন (Hard - Advanced)</option>
                                <option value="mixed">মিশ্র (Mixed - Real Exam)</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                পাবলিশ স্ট্যাটাস <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="published">সরাসরি শিক্ষার্থীদের জন্য লাইভ করুন (Published)</option>
                                <option value="pending_review">রিভিউ পেন্ডিং রাখুন (Pending Review)</option>
                                <option value="draft">ড্রাফট / খসড়া হিসেবে রাখুন (Draft)</option>
                            </select>
                        </div>
                    </div>

                    <!-- AI Notice Box -->
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200/80 dark:border-purple-800/80 text-xs text-purple-900 dark:text-purple-200 leading-relaxed">
                        <InformationCircleIcon class="w-5 h-5 shrink-0 text-purple-600 dark:text-purple-400 mt-0.5" />
                        <div>
                            <span class="font-bold">স্বয়ংক্রিয় AI প্রসেসিং:</span> প্রতিটি প্রশ্নের জন্য চারটি স্বতন্ত্র অপশন, সঠিক উত্তর, গাণিতিক সূত্রের জন্য KaTeX ($...$) নোটেশন এবং পূর্ণাঙ্গ ব্যাখ্যামূলক সমাধান বাংলা ভাষায় স্বয়ংক্রিয়ভাবে তৈরি ও ভেরিফাই হবে।
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <Link
                        href="/admin/question-bank"
                        class="px-5 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    >
                        বাতিল
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 via-indigo-600 to-primary text-white text-xs font-bold shadow-lg shadow-purple-500/20 hover:opacity-95 active:scale-95 transition disabled:opacity-50"
                    >
                        <SparklesIcon class="w-4 h-4" :class="{ 'animate-spin': form.processing }" />
                        <span v-if="form.processing">AI প্রশ্ন তৈরি ও পাবলিশ করছে...</span>
                        <span v-else>AI দিয়ে প্রশ্ন জেনারেট ও গ্রুপে পাবলিশ করুন</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
