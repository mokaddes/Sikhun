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
} from '@heroicons/vue/24/outline';

const props = defineProps({
    question: Object,
    subjects: Array,
    sources: Array,
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
});

function addOption() {
    const nextKey = String.fromCharCode(65 + form.options.length); // A, B, C, D, E...
    form.options.push({ key: nextKey, text: '' });
}

function removeOption(index) {
    if (form.options.length <= 2) return;
    form.options.splice(index, 1);
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
                        <span>প্রশ্ন ভাণ্ডারে ফিরে যান</span>
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ isEditing ? 'প্রশ্ন সম্পাদনা করুন' : 'নতুন প্রশ্ন তৈরি করুন' }}
                    </h1>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Meta Info Card -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <AcademicCapIcon class="w-4 h-4 text-primary" />
                        <span>পরীক্ষা ও শ্রেণিবিন্যাস (Classification)</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Segment -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">সেগমেন্ট *</label>
                            <select
                                v-model="form.segment"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="admission">বিশ্ববিদ্যালয় ভর্তি (Admission)</option>
                                <option value="job">সরকারি চাকরি ও ব্যাংক (Job)</option>
                            </select>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">বিষয় (Subject) *</label>
                            <select
                                v-model="form.subject_id"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option v-for="sub in subjects" :key="sub.id" :value="sub.id">
                                    {{ sub.name_bn }} ({{ sub.name_en }})
                                </option>
                            </select>
                        </div>

                        <!-- Subcategory / Topic -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">টপিক বা উপ-বিষয় (Topic)</label>
                            <input
                                v-model="form.topic_name"
                                type="text"
                                placeholder="উদাঃ কারক ও বিভক্তি / মহাকর্ষ"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Origin Tag -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                উৎস ট্যাগ (Origin Tag) *
                            </label>
                            <input
                                v-model="form.origin_tag"
                                type="text"
                                required
                                placeholder="DU-24, BCS-46, BUET-23, BB-AD-24"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white uppercase font-bold"
                            />
                        </div>

                        <!-- Institution -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">প্রতিষ্ঠান / বোর্ড / কমিশন</label>
                            <input
                                v-model="form.institution"
                                type="text"
                                placeholder="উদাঃ University of Dhaka, BPSC"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Exam Name -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">পরীক্ষার নাম (Exam Name)</label>
                            <input
                                v-model="form.exam_name"
                                type="text"
                                placeholder="উদাঃ DU KA Unit Admission 2024"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Year -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">বছর (Year)</label>
                            <input
                                v-model.number="form.year"
                                type="number"
                                min="1990"
                                max="2035"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Difficulty -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">কাঠিন্য মাত্রা *</label>
                            <select
                                v-model="form.difficulty"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="easy">সহজ (Easy)</option>
                                <option value="medium">মাঝারি (Medium)</option>
                                <option value="hard">কঠিন (Hard)</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">প্রকাশের স্ট্যাটাস *</label>
                            <select
                                v-model="form.status"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="published">Published (সরাসরি প্রকাশ)</option>
                                <option value="pending_review">Pending Review</option>
                                <option value="draft">Draft (খসড়া)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Question Text & Live Math Preview -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-bold text-gray-900 dark:text-white">
                            প্রশ্নের মূলভাব (Question Text) *
                        </label>
                        <span class="text-[11px] text-gray-400">
                            LaTeX সূত্র লিখতে $...$ বা $$...$$ ব্যবহার করুন
                        </span>
                    </div>

                    <textarea
                        v-model="form.question_text"
                        rows="3"
                        required
                        placeholder="প্রশ্নটি লিখুন। গণিত বা বৈজ্ঞানিক সূত্রের জন্য যেমনঃ $g' = \frac{1}{4}g$ বা $\lim_{x \to 0} \frac{\sin x}{x}$"
                        class="w-full text-sm px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white leading-relaxed font-mono"
                    ></textarea>

                    <!-- Live Math Preview -->
                    <div v-if="form.question_text" class="p-4 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-200/80 dark:border-gray-700">
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1 flex items-center gap-1">
                            <EyeIcon class="w-3.5 h-3.5" />
                            <span>লাইভ প্রিভিউ (KaTeX Math Render):</span>
                        </div>
                        <div class="text-base text-gray-900 dark:text-white font-medium" v-html="renderMath(form.question_text)"></div>
                    </div>
                </div>

                <!-- Options Card -->
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
                            <!-- Radio selector for correct answer -->
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

                            <!-- Option text input -->
                            <input
                                v-model="opt.text"
                                type="text"
                                required
                                :placeholder="`অপশন ${opt.key}-এর মান লিখুন (LaTeX গণিত অনুমোদিত)`"
                                class="flex-1 text-xs px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                            />

                            <!-- Live preview of option formula -->
                            <div class="min-w-[80px] max-w-[200px] text-xs px-2 truncate" v-html="renderMath(opt.text)"></div>

                            <!-- Remove option button -->
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

                    <!-- Live Math Preview for Explanation -->
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
