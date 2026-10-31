<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    SparklesIcon,
    ArrowLeftIcon,
    BookOpenIcon,
    DocumentTextIcon,
    ChatBubbleBottomCenterTextIcon,
    AcademicCapIcon,
    ClockIcon,
    CheckCircleIcon,
    BoltIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    books: {
        type: Array,
        default: () => [],
    },
});

const { t, locale } = useI18n();

const form = useForm({
    title: '',
    source_type: 'topic',
    source_book_id: props.books.length > 0 ? props.books[0].id : null,
    chapter_ids: [],
    source_text: '',
    type: 'mcq',
    count: 10,
    duration: 20,
    mode: 'practice',
});

const selectedBook = computed(() => props.books.find(book => book.id === form.source_book_id));

const questionTypes = [
    { id: 'mcq', label: 'MCQ', descEn: 'Multiple choice with 4 options', descBn: 'বহুনির্বাচনী প্রশ্ন (৪ অপশন)' },
    { id: 'true_false', label: locale.value === 'en' ? 'True / False' : 'সত্য / মিথ্যা', descEn: 'Binary choice questions', descBn: 'সত্য বা মিথ্যা ভিত্তিক' },
    { id: 'short', label: locale.value === 'en' ? 'Short Answer' : 'সংক্ষিপ্ত উত্তর', descEn: 'Brief direct answers', descBn: 'এক কথায় বা সংক্ষেপে উত্তর' },
    { id: 'fill_blank', label: locale.value === 'en' ? 'Fill in Blank' : 'শূন্যস্থান পূরণ', descEn: 'Missing keyword completion', descBn: 'উপযুক্ত শব্দ দিয়ে পূরণ' },
    { id: 'cq', label: locale.value === 'en' ? 'Creative (CQ)' : 'সৃজনশীল প্রশ্ন (CQ)', descEn: 'Multi-part scenario questions', descBn: 'উদ্দীপক ও জ্ঞান/অনুধাবন/প্রয়োগ' },
];

const countPresets = [5, 10, 15, 20, 30];
const durationPresets = [
    { value: 0, labelEn: 'No limit', labelBn: 'সময়সীমা নেই' },
    { value: 10, labelEn: '10 min', labelBn: '১০ মিনিট' },
    { value: 15, labelEn: '15 min', labelBn: '১৫ মিনিট' },
    { value: 20, labelEn: '20 min', labelBn: '২০ মিনিট' },
    { value: 30, labelEn: '30 min', labelBn: '৩০ মিনিট' },
    { value: 45, labelEn: '45 min', labelBn: '৪৫ মিনিট' },
];

function submit() {
    form.post('/exams');
}
</script>

<template>
    <Head :title="t('exams.new_exam')" />
    <StudentLayout>
        <!-- Centered Container -->
        <div class="mx-auto max-w-3xl pb-12">
            <!-- Back navigation & Title -->
            <div class="mb-6 flex items-center justify-between">
                <Link
                    href="/exams"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--text)] transition-colors"
                >
                    <ArrowLeftIcon class="h-4 w-4" />
                    <span>{{ locale === 'en' ? 'Back to Exams' : 'সকল পরীক্ষায় ফিরে যান' }}</span>
                </Link>
            </div>

            <!-- Header Card -->
            <div class="mb-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-[var(--primary)]/10 px-3 py-1 text-xs font-semibold text-[var(--primary)] mb-2">
                        <SparklesIcon class="h-3.5 w-3.5" />
                        {{ locale === 'en' ? 'AI Exam Generator' : 'এআই পরীক্ষা প্রস্তুতকারক' }}
                    </div>
                    <h1 class="font-heading text-2xl font-black tracking-tight sm:text-3xl">
                        {{ t('exams.new_exam') }}
                    </h1>
                    <p class="mt-1 text-sm text-[var(--text-muted)]">
                        {{ locale === 'en'
                            ? 'Configure your topics, question format, and difficulty to generate an instant AI test.'
                            : 'বিষয়বস্তু, প্রশ্নের ধরন ও সময় নির্ধারণ করে নিমেষেই স্বয়ংক্রিয় এআই পরীক্ষা তৈরি করুন।' }}
                    </p>
                </div>
                <div class="hidden sm:grid h-16 w-16 place-items-center rounded-2xl bg-[var(--surface2)] text-[var(--primary)] shrink-0">
                    <AcademicCapIcon class="h-9 w-9" />
                </div>
            </div>

            <!-- Form Card (Centered) -->
            <form @submit.prevent="submit" class="space-y-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                <!-- 1. Exam Name / Title -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('exams.exam_name') }}
                        <span class="text-xs font-normal text-[var(--text-muted)] ml-1">
                            ({{ locale === 'en' ? 'Optional, gives your test a custom title' : 'ঐচ্ছিক, পরীক্ষার বিশেষ নাম দিতে পারেন' }})
                        </span>
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        :placeholder="t('exams.exam_name_placeholder')"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium transition focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-rose-500">{{ form.errors.title }}</p>
                </div>

                <!-- 2. Source Selection (Segmented Tabs) -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('exams.source') }}
                    </label>
                    <div class="grid grid-cols-3 gap-2.5 rounded-xl bg-[var(--surface2)] p-1.5 border border-[var(--border)]">
                        <button
                            type="button"
                            class="flex flex-col sm:flex-row items-center justify-center gap-1.5 rounded-lg py-2.5 px-3 text-xs font-bold transition-all"
                            :class="form.source_type === 'topic'
                                ? 'bg-[var(--surface)] text-[var(--primary)] shadow-sm'
                                : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                            @click="form.source_type = 'topic'"
                        >
                            <ChatBubbleBottomCenterTextIcon class="h-4 w-4" />
                            <span>{{ t('exams.source_topic') }}</span>
                        </button>
                        <button
                            type="button"
                            class="flex flex-col sm:flex-row items-center justify-center gap-1.5 rounded-lg py-2.5 px-3 text-xs font-bold transition-all"
                            :class="form.source_type === 'paragraph'
                                ? 'bg-[var(--surface)] text-[var(--primary)] shadow-sm'
                                : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                            @click="form.source_type = 'paragraph'"
                        >
                            <DocumentTextIcon class="h-4 w-4" />
                            <span>{{ t('exams.source_paragraph') }}</span>
                        </button>
                        <button
                            type="button"
                            :disabled="!books.length"
                            class="flex flex-col sm:flex-row items-center justify-center gap-1.5 rounded-lg py-2.5 px-3 text-xs font-bold transition-all disabled:opacity-40"
                            :class="form.source_type === 'book'
                                ? 'bg-[var(--surface)] text-[var(--primary)] shadow-sm'
                                : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                            @click="form.source_type = 'book'"
                        >
                            <BookOpenIcon class="h-4 w-4" />
                            <span>{{ t('exams.source_book') }}</span>
                        </button>
                    </div>

                    <!-- Source Input based on choice -->
                    <div class="mt-3">
                        <!-- Book dropdown -->
                        <div v-if="form.source_type === 'book'">
                            <select
                                v-model="form.source_book_id"
                                @change="form.chapter_ids = []"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                            >
                                <option :value="null" disabled>{{ t('exams.select_book') }}</option>
                                <option v-for="b in books" :key="b.id" :value="b.id">
                                    {{ b.title }}
                                </option>
                            </select>
                            <p v-if="form.errors.source_book_id" class="mt-1 text-xs text-rose-500">{{ form.errors.source_book_id }}</p>
                            <div v-if="selectedBook?.chapters?.length" class="mt-3">
                                <label class="mb-1 block text-sm font-bold text-[var(--text)]">
                                    {{ locale === 'en' ? 'Chapters (optional)' : 'অধ্যায় (ঐচ্ছিক)' }}
                                </label>
                                <select
                                    v-model="form.chapter_ids"
                                    multiple
                                    size="5"
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                                >
                                    <option v-for="chapter in selectedBook.chapters" :key="chapter.id" :value="chapter.id">
                                        {{ chapter.chapter_number ? `${chapter.chapter_number}. ` : '' }}{{ chapter.title }}
                                    </option>
                                </select>
                                <p class="mt-1 text-xs text-[var(--text-muted)]">
                                    {{ locale === 'en' ? 'Leave unselected to sample across the whole book you can access.' : 'কিছু নির্বাচন না করলে আপনার অ্যাক্সেসযোগ্য পুরো বই থেকে নমুনা নেওয়া হবে।' }}
                                </p>
                                <p v-if="form.errors.chapter_ids" class="mt-1 text-xs text-rose-500">{{ form.errors.chapter_ids }}</p>
                                <p v-if="form.errors['chapter_ids.0']" class="mt-1 text-xs text-rose-500">{{ form.errors['chapter_ids.0'] }}</p>
                            </div>
                        </div>

                        <!-- Topic input -->
                        <div v-else-if="form.source_type === 'topic'">
                            <input
                                v-model="form.source_text"
                                type="text"
                                :placeholder="locale === 'en' ? 'e.g. Newton\'s laws of motion, Photosynthesis, French Revolution...' : 'যেমন: নিউটনের গতিসূত্র, উদ্ভিদের সালোকসংশ্লেষণ, জৈব যৌগ...'"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                            />
                            <p v-if="form.errors.source_text" class="mt-1 text-xs text-rose-500">{{ form.errors.source_text }}</p>
                        </div>

                        <!-- Paragraph textarea -->
                        <div v-else>
                            <textarea
                                v-model="form.source_text"
                                rows="4"
                                :placeholder="locale === 'en' ? 'Paste any text, article, or lecture snippet to generate questions directly from it...' : 'যে কোনো অনুচ্ছেদ বা পড়ার অংশ এখানে পেস্ট করুন, এআই তা থেকে প্রশ্ন তৈরি করবে...'"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                            ></textarea>
                            <p v-if="form.errors.source_text" class="mt-1 text-xs text-rose-500">{{ form.errors.source_text }}</p>
                        </div>
                    </div>
                </div>

                <!-- 3. Question Type Selector (Cards) -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('exams.question_type') }}
                    </label>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <button
                            v-for="qType in questionTypes"
                            :key="qType.id"
                            type="button"
                            class="flex flex-col items-start rounded-xl border p-3 text-left transition-all"
                            :class="form.type === qType.id
                                ? 'border-[var(--primary)] bg-[var(--primary)]/8 ring-1 ring-[var(--primary)]'
                                : 'border-[var(--border)] bg-[var(--surface2)]/50 hover:border-[var(--border)] hover:bg-[var(--surface2)]'"
                            @click="form.type = qType.id"
                        >
                            <div class="flex items-center justify-between w-full">
                                <span class="font-heading text-xs font-bold" :class="form.type === qType.id ? 'text-[var(--primary)]' : 'text-[var(--text)]'">
                                    {{ qType.label }}
                                </span>
                                <span v-if="form.type === qType.id" class="h-2 w-2 rounded-full bg-[var(--primary)]"></span>
                            </div>
                            <span class="mt-1 text-[11px] text-[var(--text-muted)] line-clamp-1">
                                {{ locale === 'en' ? qType.descEn : qType.descBn }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- 4. Configuration Grid: Count, Duration, Mode -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <!-- Question Count with Presets -->
                    <div>
                        <label class="block text-sm font-bold text-[var(--text)] mb-2">
                            {{ t('exams.count') }}
                        </label>
                        <div class="flex items-center gap-2">
                            <button
                                v-for="cnt in countPresets"
                                :key="cnt"
                                type="button"
                                class="flex-1 rounded-xl py-2 text-xs font-bold border transition-all"
                                :class="form.count === cnt
                                    ? 'border-[var(--primary)] bg-[var(--primary)] text-white shadow-sm'
                                    : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                                @click="form.count = cnt"
                            >
                                {{ cnt }}
                            </button>
                        </div>
                    </div>

                    <!-- Duration with Presets -->
                    <div>
                        <label class="block text-sm font-bold text-[var(--text)] mb-2">
                            {{ t('exams.duration') }}
                        </label>
                        <select
                            v-model="form.duration"
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm font-medium focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                        >
                            <option v-for="opt in durationPresets" :key="opt.value" :value="opt.value">
                                {{ locale === 'en' ? opt.labelEn : opt.labelBn }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- 5. Mode Selection (Practice vs Exam) -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('exams.mode') }}
                    </label>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <!-- Practice Card -->
                        <button
                            type="button"
                            class="flex items-start gap-3 rounded-2xl border p-4 text-left transition-all"
                            :class="form.mode === 'practice'
                                ? 'border-blue-500 bg-blue-500/10 ring-1 ring-blue-500'
                                : 'border-[var(--border)] bg-[var(--surface2)]/40 hover:bg-[var(--surface2)]'"
                            @click="form.mode = 'practice'"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-blue-500/20 text-blue-500">
                                <BoltIcon class="h-5 w-5" />
                            </span>
                            <div>
                                <span class="block text-sm font-bold text-[var(--text)]">{{ t('exams.mode_practice') }}</span>
                                <span class="text-xs text-[var(--text-muted)]">
                                    {{ locale === 'en'
                                        ? 'Instant explanations after every question. No stress.'
                                        : 'প্রতিটি প্রশ্নের পর সঠিক উত্তর ও ব্যাখ্যা তাৎক্ষণিক দেখা যাবে।' }}
                                </span>
                            </div>
                        </button>

                        <!-- Exam Card -->
                        <button
                            type="button"
                            class="flex items-start gap-3 rounded-2xl border p-4 text-left transition-all"
                            :class="form.mode === 'exam'
                                ? 'border-purple-500 bg-purple-500/10 ring-1 ring-purple-500'
                                : 'border-[var(--border)] bg-[var(--surface2)]/40 hover:bg-[var(--surface2)]'"
                            @click="form.mode = 'exam'"
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-purple-500/20 text-purple-500">
                                <ClockIcon class="h-5 w-5" />
                            </span>
                            <div>
                                <span class="block text-sm font-bold text-[var(--text)]">{{ t('exams.mode_exam') }}</span>
                                <span class="text-xs text-[var(--text-muted)]">
                                    {{ locale === 'en'
                                        ? 'Timed test, final score sheet & leaderboard eligibility.'
                                        : 'প্রকৃত পরীক্ষার মতো সময়সীমা ও লিডারবোর্ডে স্কোর যোগ হওয়ার সুযোগ।' }}
                                </span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- 6. Submit Button -->
                <div class="pt-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn btn-primary w-full justify-center gap-2 rounded-xl py-3.5 text-base font-bold shadow-lg shadow-[var(--primary)]/20 transition-all hover:scale-[1.01] active:scale-[0.99] disabled:opacity-60"
                    >
                        <SparklesIcon class="h-5 w-5" />
                        <span>{{ form.processing ? t('exams.generating') : t('exams.generate') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </StudentLayout>
</template>
