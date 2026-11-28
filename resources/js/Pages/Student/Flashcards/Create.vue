<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    SparklesIcon,
    ArrowLeftIcon,
    RectangleStackIcon,
    BookOpenIcon,
    DocumentTextIcon,
    PencilSquareIcon,
    AcademicCapIcon,
    ExclamationCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    books: { type: Array, default: () => [] },
    myBooks: { type: Array, default: () => [] },
    subjects: { type: Array, default: () => [] },
    exams: { type: Array, default: () => [] },
    qbSectors: { type: Array, default: () => [] },
    subjectsBySector: { type: Object, default: () => ({}) },
    subjectExams: { type: Array, default: () => [] },
    canUseQuestionBank: { type: Boolean, default: null },
    prefill: { type: Object, default: () => ({}) },
});

const { t, locale } = useI18n();

const BN_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
function toBnDigits(val) {
    if (val === null || val === undefined) return '';
    return locale.value === 'en' ? String(val) : String(val).replace(/[0-9]/g, (d) => BN_DIGITS[Number(d)]);
}

const allowQuestionBank = computed(() => {
    if (props.canUseQuestionBank !== null) return props.canUseQuestionBank;
    return true;
});

const sourceType = ref(
    (!allowQuestionBank.value && props.prefill?.source_type === 'question_bank')
        ? 'topic'
        : (props.prefill?.source_type || 'topic')
);

const initialSector = props.prefill?.sector || props.qbSectors?.[0]?.key || 'hsc';

const form = useForm({
    title: props.prefill?.title || '',
    source_type: sourceType.value,
    source_topic: '',
    source_book_id: null,
    source_my_book_id: null,
    chapter_ids: [],
    subject_id: props.prefill?.subject_id || null,
    origin_tag: props.prefill?.origin_tag || '',
    sector: initialSector,
    source_text: '',
    count: 15,
});

const availableSubjects = computed(() => {
    const bySec = props.subjectsBySector?.[form.sector];
    if (Array.isArray(bySec) && bySec.length) {
        return bySec;
    }
    return props.subjects || [];
});

const availableExams = computed(() => {
    if (!form.subject_id) return [];
    const targetSub = Number(form.subject_id);
    if (props.subjectExams?.length) {
        return props.subjectExams.filter(
            (ex) => ex.segment === form.sector && Number(ex.subject_id) === targetSub
        );
    }
    return props.exams || [];
});

const errorList = computed(() => Object.values(form.errors || {}).filter(Boolean));

function selectSector(secKey) {
    if (form.sector === secKey) return;
    form.sector = secKey;
    const nextSubjects = props.subjectsBySector?.[secKey] || [];
    if (form.subject_id && !nextSubjects.some((s) => Number(s.id) === Number(form.subject_id))) {
        form.subject_id = null;
    }
    form.origin_tag = '';
    form.clearErrors('subject_id', 'origin_tag', 'sector');
}

watch(() => form.subject_id, (newSubId) => {
    form.clearErrors('subject_id', 'origin_tag');
    if (!newSubId) {
        form.origin_tag = '';
        return;
    }
    if (form.origin_tag && !availableExams.value.some((ex) => ex.origin_tag === form.origin_tag)) {
        form.origin_tag = '';
    }
});

const selectedBookKey = ref('');
function onBookSelectChange() {
    form.chapter_ids = [];
    if (!selectedBookKey.value) {
        form.source_book_id = null;
        form.source_my_book_id = null;
        return;
    }
    const [kind, id] = selectedBookKey.value.split(':');
    if (kind === 'my') {
        form.source_my_book_id = Number(id);
        form.source_book_id = null;
    } else {
        form.source_book_id = Number(id);
        form.source_my_book_id = null;
    }
}

onMounted(() => {
    if (props.prefill?.source_type) {
        if (!allowQuestionBank.value && props.prefill.source_type === 'question_bank') {
            sourceType.value = 'topic';
            form.source_type = 'topic';
        } else {
            sourceType.value = props.prefill.source_type;
            form.source_type = props.prefill.source_type;
        }
    }
});

function switchSourceType(type) {
    sourceType.value = type;
    form.source_type = type;
    form.clearErrors();
    if (type !== 'book') {
        selectedBookKey.value = '';
        form.source_book_id = null;
        form.source_my_book_id = null;
        form.chapter_ids = [];
    }
}

const selectedBook = computed(() => props.books?.find((b) => b.id === form.source_book_id));
const selectedMyBook = computed(() => props.myBooks?.find((b) => b.id === form.source_my_book_id));

const countPresets = [5, 10, 15, 20, 25, 30];

function submit() {
    if (sourceType.value === 'question_bank' && !form.subject_id) {
        form.setError('subject_id', locale.value === 'en'
            ? 'Please select a subject from the Question Bank.'
            : 'প্রশ্নব্যাংক থেকে ফ্ল্যাশকার্ড তৈরি করতে একটি বিষয় নির্বাচন করুন।');
        document.getElementById('qb-subject')?.focus();
        return;
    }

    // Auto-generate title if left blank
    if (!form.title.trim()) {
        if (sourceType.value === 'topic' && form.source_topic.trim()) {
            form.title = form.source_topic.trim() + ' ফ্ল্যাশকার্ড';
        } else if (sourceType.value === 'book' && (selectedBook.value || selectedMyBook.value)) {
            form.title = (selectedBook.value || selectedMyBook.value).title + ' ফ্ল্যাশকার্ড';
        } else if (sourceType.value === 'question_bank') {
            const subj = availableSubjects.value?.find((s) => Number(s.id) === Number(form.subject_id));
            form.title = (subj ? (subj.name_bn || subj.name_en) : 'প্রশ্নব্যাংক') + ' ফ্ল্যাশকার্ড';
        } else {
            form.title = 'কাস্টম রিভিশন ফ্ল্যাশকার্ড';
        }
    }

    form.post('/flashcards');
}
</script>

<template>
    <Head :title="t('flashcards.new_set')" />
    <StudentLayout>
        <div class="mx-auto max-w-2xl pb-12">
            <!-- Back Button -->
            <div class="mb-6 flex items-center justify-between">
                <Link
                    href="/flashcards"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--text)] transition-colors"
                >
                    <ArrowLeftIcon class="h-4 w-4" />
                    <span>{{ locale === 'en' ? 'Back to Flashcards' : 'সকল ফ্ল্যাশকার্ডে ফিরে যান' }}</span>
                </Link>
            </div>

            <!-- Header Card -->
            <div class="mb-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 flex items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-[var(--tertiary)]/10 px-3 py-1 text-xs font-semibold text-[var(--tertiary)] mb-2">
                        <SparklesIcon class="h-3.5 w-3.5" />
                        {{ locale === 'en' ? 'AI Flashcard Maker' : 'এআই ফ্ল্যাশকার্ড প্রস্তুতকারক' }}
                    </div>
                    <h1 id="flashcard-create-heading" class="font-heading text-2xl font-black tracking-tight sm:text-3xl">
                        {{ t('flashcards.new_set') }}
                    </h1>
                    <p class="mt-1 text-sm text-[var(--text-muted)]">
                        {{ locale === 'en'
                            ? (allowQuestionBank ? 'Generate front-and-back study flashcards from topics, books, question banks, or notes.' : 'Generate front-and-back study flashcards from topics, books, or notes.')
                            : (allowQuestionBank ? 'টপিক, বই, প্রশ্নব্যাংক বা নোট থেকে নিমেষেই ইন্টারেক্টিভ রিভিশন কার্ড তৈরি করুন।' : 'টপিক, বই বা নোট থেকে নিমেষেই ইন্টারেক্টিভ রিভিশন কার্ড তৈরি করুন।') }}
                    </p>
                </div>
                <div class="hidden sm:grid h-14 w-14 place-items-center rounded-2xl bg-[var(--surface2)] text-[var(--tertiary)] shrink-0">
                    <RectangleStackIcon class="h-8 w-8" />
                </div>
            </div>

            <!-- Form Card -->
            <form
                @submit.prevent="submit"
                aria-labelledby="flashcard-create-heading"
                class="space-y-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm"
            >
                <!-- Live Validation Error Summary -->
                <div
                    v-if="errorList.length"
                    role="status"
                    aria-live="polite"
                    class="flex items-start gap-2.5 rounded-xl border border-rose-500/30 bg-rose-500/10 p-3.5 text-xs font-medium text-rose-600 dark:text-rose-400"
                >
                    <ExclamationCircleIcon class="h-4 w-4 shrink-0 mt-0.5" />
                    <ul class="space-y-1">
                        <li v-for="(err, idx) in errorList" :key="idx">{{ err }}</li>
                    </ul>
                </div>

                <!-- Source Selection Tabs -->
                <fieldset>
                    <legend class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ locale === 'en' ? 'Choose Material Source' : 'ফ্ল্যাশকার্ড তৈরির উৎস নির্বাচন করুন' }}
                    </legend>
                    <div :class="['grid gap-2', allowQuestionBank ? 'grid-cols-2 sm:grid-cols-4' : 'grid-cols-3']">
                        <button
                            type="button"
                            @click="switchSourceType('topic')"
                            :aria-pressed="sourceType === 'topic'"
                            class="flex flex-col items-center justify-center gap-1.5 min-h-12 p-3 rounded-xl border text-xs font-bold transition text-center cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--primary)]"
                            :class="sourceType === 'topic' ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)] shadow-xs ring-1 ring-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            <PencilSquareIcon class="h-5 w-5" />
                            <span>{{ locale === 'en' ? 'Topic' : 'টপিক লিখুন' }}</span>
                        </button>
                        <button
                            type="button"
                            @click="switchSourceType('paragraph')"
                            :aria-pressed="sourceType === 'paragraph'"
                            class="flex flex-col items-center justify-center gap-1.5 min-h-12 p-3 rounded-xl border text-xs font-bold transition text-center cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--primary)]"
                            :class="sourceType === 'paragraph' ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)] shadow-xs ring-1 ring-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            <DocumentTextIcon class="h-5 w-5" />
                            <span>{{ locale === 'en' ? 'Paragraph/Note' : 'নোট/অনুচ্ছেদ' }}</span>
                        </button>
                        <button
                            type="button"
                            @click="switchSourceType('book')"
                            :aria-pressed="sourceType === 'book'"
                            class="flex flex-col items-center justify-center gap-1.5 min-h-12 p-3 rounded-xl border text-xs font-bold transition text-center cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--primary)]"
                            :class="sourceType === 'book' ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)] shadow-xs ring-1 ring-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            <BookOpenIcon class="h-5 w-5" />
                            <span>{{ locale === 'en' ? 'Book' : 'বই ও অধ্যায়' }}</span>
                        </button>
                        <button
                            v-if="allowQuestionBank"
                            type="button"
                            @click="switchSourceType('question_bank')"
                            :aria-pressed="sourceType === 'question_bank'"
                            class="flex flex-col items-center justify-center gap-1.5 min-h-12 p-3 rounded-xl border text-xs font-bold transition text-center cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--primary)]"
                            :class="sourceType === 'question_bank' ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)] shadow-xs ring-1 ring-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            <AcademicCapIcon class="h-5 w-5" />
                            <span>{{ locale === 'en' ? 'Question Bank' : 'প্রশ্নব্যাংক' }}</span>
                        </button>
                    </div>
                </fieldset>

                <!-- 1. TOPIC INPUT -->
                <div v-if="sourceType === 'topic'">
                    <label for="source-topic" class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ locale === 'en' ? 'Topic or Concept' : 'টপিক বা বিষয়ের নাম' }}
                    </label>
                    <input
                        id="source-topic"
                        name="source_topic"
                        v-model="form.source_topic"
                        type="text"
                        aria-describedby="topic-help"
                        :placeholder="locale === 'en' ? 'e.g. Periodic Table Elements, Photosynthesis, Computer Networking...' : 'যেমন: পর্যায় সারণির মৌলসমূহ, সালোকসংশ্লেষণ, নেটওয়ার্কিং প্রটোকল, বিসিএস বাংলা সাহিত্য...'"
                        class="w-full min-h-12 rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-base sm:text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    />
                    <p id="topic-help" class="mt-1.5 text-xs text-[var(--text-muted)]">
                        {{ locale === 'en' ? 'Our AI will generate high-yield question & answer pairs for this topic.' : 'এআই স্বয়ংক্রিয়ভাবে এই টপিকের গুরুত্বপূর্ণ সংজ্ঞা, সূত্র ও তথ্য দিয়ে কার্ড তৈরি করবে।' }}
                    </p>
                </div>

                <!-- 2. PARAGRAPH / NOTE INPUT -->
                <div v-if="sourceType === 'paragraph'">
                    <label for="source-text" class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('flashcards.source_text') }}
                    </label>
                    <textarea
                        id="source-text"
                        name="source_text"
                        v-model="form.source_text"
                        rows="5"
                        :aria-invalid="Boolean(form.errors.source_text)"
                        :aria-describedby="form.errors.source_text ? 'source-text-err' : undefined"
                        :placeholder="locale === 'en' ? 'Paste keywords, vocabulary, formulas, or lecture notes here...' : 'এখানে আপনার নোট, সংজ্ঞা, সূত্র বা গুরুত্বপূর্ণ প্যারাগ্রাফ পেস্ট করুন...'"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-base sm:text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                        @input="form.clearErrors('source_text')"
                    ></textarea>
                    <p v-if="form.errors.source_text" id="source-text-err" class="mt-1.5 text-xs font-medium text-rose-500">{{ form.errors.source_text }}</p>
                </div>

                <!-- 3. BOOK & CHAPTER SELECTION -->
                <div v-if="sourceType === 'book'">
                    <label for="source-book" class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('exams.source_book') }}
                    </label>
                    <select
                        id="source-book"
                        name="source_book"
                        v-model="selectedBookKey"
                        @change="onBookSelectChange"
                        class="w-full min-h-12 rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-base sm:text-sm font-medium focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    >
                        <option value="">— {{ locale === 'en' ? 'Select from Bookshelf or My Books' : 'বুকশেল্ফ বা নিজের যোগ করা বই নির্বাচন করুন' }} —</option>
                        <optgroup v-if="books.length" :label="locale === 'en' ? 'Bookshelf Books' : 'বুকশেল্ফের বই'">
                            <option v-for="b in books" :key="'book-' + b.id" :value="'book:' + b.id">{{ b.title }}</option>
                        </optgroup>
                        <optgroup v-if="myBooks.length" :label="locale === 'en' ? 'My Added Books' : 'আমার যোগ করা বই'">
                            <option v-for="mb in myBooks" :key="'my-' + mb.id" :value="'my:' + mb.id">{{ mb.title }}</option>
                        </optgroup>
                    </select>
                    <p v-if="!books.length && !myBooks.length" class="mt-2 text-xs text-amber-500">
                        {{ locale === 'en' ? 'No books in your Bookshelf or My Books yet. Add a book from Library or upload your own.' : 'আপনার বুকশেল্ফ বা নিজের বইয়ের তালিকায় কোনো বই নেই। লাইব্রেরি থেকে বই যোগ করুন অথবা নিজের বই আপলোড করুন।' }}
                    </p>

                    <div v-if="selectedBook?.chapters?.length" class="mt-3">
                        <label for="source-chapters" class="mb-1.5 block text-sm font-bold text-[var(--text)]">
                            {{ locale === 'en' ? 'Chapters (Optional)' : 'অধ্যায়সমূহ (ঐচ্ছিক)' }}
                        </label>
                        <select
                            id="source-chapters"
                            name="chapter_ids"
                            v-model="form.chapter_ids"
                            multiple
                            size="4"
                            aria-describedby="chapters-help"
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                        >
                            <option v-for="chapter in selectedBook.chapters" :key="chapter.id" :value="chapter.id">
                                {{ chapter.chapter_number ? `${chapter.chapter_number}. ` : '' }}{{ chapter.title }}
                            </option>
                        </select>
                        <p id="chapters-help" class="mt-1.5 text-xs text-[var(--text-muted)]">
                            {{ locale === 'en' ? 'Select chapter(s), or leave unselected to sample across the whole book.' : 'অধ্যায় নির্বাচন করুন, অথবা সমগ্র বই থেকে কার্ড বানাতে আনসিলেক্টেড রাখুন।' }}
                        </p>
                    </div>
                </div>

                <!-- 4. QUESTION BANK SELECTION -->
                <div v-if="sourceType === 'question_bank'" class="space-y-4">
                    <!-- Sector Selector Pills -->
                    <fieldset v-if="qbSectors?.length">
                        <legend class="block text-sm font-bold text-[var(--text)] mb-2">
                            {{ locale === 'en' ? 'Category / Sector' : 'সেক্টর নির্বাচন করুন' }}
                        </legend>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="sec in qbSectors"
                                :key="sec.key"
                                type="button"
                                @click="selectSector(sec.key)"
                                :aria-pressed="form.sector === sec.key"
                                class="inline-flex items-center gap-2 min-h-10 rounded-xl border px-3.5 py-2 text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--primary)]"
                                :class="form.sector === sec.key
                                    ? 'border-[var(--primary)] bg-[var(--primary)] text-white shadow-xs'
                                    : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:border-[var(--primary)]/40 hover:text-[var(--text)]'"
                            >
                                <span>{{ locale === 'en' ? sec.label_en : sec.label_bn }}</span>
                                <span
                                    class="rounded-full px-1.5 py-0.5 text-[10px] font-mono font-bold"
                                    :class="form.sector === sec.key ? 'bg-white/20 text-white' : 'bg-[var(--surface)] text-[var(--text-muted)]'"
                                >
                                    {{ toBnDigits(sec.count) }}
                                </span>
                            </button>
                        </div>
                    </fieldset>

                    <!-- Subject Select -->
                    <div>
                        <label for="qb-subject" class="block text-sm font-bold text-[var(--text)] mb-2">
                            {{ locale === 'en' ? 'Subject' : 'বিষয় নির্বাচন করুন' }}
                            <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="qb-subject"
                            name="subject_id"
                            v-model="form.subject_id"
                            :aria-invalid="Boolean(form.errors.subject_id)"
                            :aria-describedby="form.errors.subject_id ? 'qb-subject-err' : 'qb-subject-help'"
                            class="w-full min-h-12 rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-base sm:text-sm font-medium focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                        >
                            <option :value="null">— {{ locale === 'en' ? 'Select Subject' : 'বিষয় নির্বাচন করুন' }} —</option>
                            <option v-for="s in availableSubjects" :key="s.id" :value="s.id">
                                {{ locale === 'en' ? (s.name_en || s.name_bn) : (s.name_bn || s.name_en) }}
                                <template v-if="s.questions_count"> ({{ toBnDigits(s.questions_count) }} {{ locale === 'en' ? 'MCQs' : 'টি প্রশ্ন' }})</template>
                            </option>
                        </select>
                        <p v-if="form.errors.subject_id" id="qb-subject-err" class="mt-1.5 text-xs font-medium text-rose-500">
                            {{ form.errors.subject_id }}
                        </p>
                        <p v-else id="qb-subject-help" class="mt-1.5 text-xs text-[var(--text-muted)]">
                            {{ locale === 'en'
                                ? 'Selecting a subject automatically filters matching exams below.'
                                : 'বিষয় নির্বাচন করলে নিচে শুধুমাত্র ঐ বিষয়ের সংশ্লিষ্ট পরীক্ষাসমূহ দেখাবে।' }}
                        </p>
                    </div>

                    <!-- Exam / Institution Select (Strictly Filtered by Sector + Subject) -->
                    <div>
                        <label for="qb-exam" class="block text-sm font-bold text-[var(--text)] mb-2">
                            {{ locale === 'en' ? 'Specific Exam / Institution (Optional)' : 'নির্দিষ্ট পরীক্ষা বা প্রতিষ্ঠান (ঐচ্ছিক)' }}
                        </label>
                        <select
                            id="qb-exam"
                            name="origin_tag"
                            v-model="form.origin_tag"
                            :disabled="!form.subject_id"
                            :aria-invalid="Boolean(form.errors.origin_tag)"
                            :aria-describedby="form.errors.origin_tag ? 'qb-exam-err' : 'qb-exam-help'"
                            class="w-full min-h-12 rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-base sm:text-sm font-medium focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <option v-if="!form.subject_id" value="">
                                — {{ locale === 'en' ? 'Select a subject first' : 'প্রথমে বিষয় নির্বাচন করুন' }} —
                            </option>
                            <option v-else value="">
                                — {{ locale === 'en' ? 'All exams in this subject' : 'এই বিষয়ের সকল পরীক্ষা থেকে প্রশ্ন নিন' }} —
                            </option>
                            <option v-for="ex in availableExams" :key="ex.origin_tag" :value="ex.origin_tag">
                                {{ ex.exam_name || ex.origin_tag }} ({{ toBnDigits(ex.count) }} {{ locale === 'en' ? 'questions' : 'টি প্রশ্ন' }})
                            </option>
                        </select>
                        <p v-if="form.errors.origin_tag" id="qb-exam-err" class="mt-1.5 text-xs font-medium text-rose-500">
                            {{ form.errors.origin_tag }}
                        </p>
                        <p v-else id="qb-exam-help" class="mt-1.5 text-xs text-[var(--text-muted)]">
                            <template v-if="!form.subject_id">
                                {{ locale === 'en' ? 'Choose a subject above to browse its specific exams.' : 'নির্দিষ্ট পরীক্ষার প্রশ্ন ফিল্টার করতে আগে একটি বিষয় বেছে নিন।' }}
                            </template>
                            <template v-else-if="!availableExams.length">
                                {{ locale === 'en' ? 'Questions will be sampled across all available entries in this subject.' : 'এই বিষয়ের সকল প্রশ্ন থেকে কার্ড তৈরি করা হবে।' }}
                            </template>
                            <template v-else>
                                {{ locale === 'en'
                                    ? `${availableExams.length} matching exams available for the selected subject.`
                                    : `নির্বাচিত বিষয়ে ${toBnDigits(availableExams.length)}টি সংশ্লিষ্ট পরীক্ষা পাওয়া গেছে।` }}
                            </template>
                        </p>
                    </div>
                </div>

                <!-- Set Title -->
                <div>
                    <label for="set-title" class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('flashcards.set_title') }}
                        <span class="text-xs font-normal text-[var(--text-muted)] ml-1">
                            ({{ locale === 'en' ? 'Optional, auto-generated if blank' : 'ঐচ্ছিক, ফাঁকা রাখলে স্বয়ংক্রিয় তৈরি হবে' }})
                        </span>
                    </label>
                    <input
                        id="set-title"
                        name="title"
                        v-model="form.title"
                        type="text"
                        :aria-invalid="Boolean(form.errors.title)"
                        :aria-describedby="form.errors.title ? 'set-title-err' : undefined"
                        :placeholder="locale === 'en' ? 'e.g. DU Admission Chemistry, 50th BCS English...' : 'যেমন: ঢাবি রসায়ন ফাইনাল রিভিশন, বিসিএস ইংরেজি লিটারেচার...'"
                        class="w-full min-h-12 rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-base sm:text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    />
                    <p v-if="form.errors.title" id="set-title-err" class="mt-1.5 text-xs font-medium text-rose-500">{{ form.errors.title }}</p>
                </div>

                <!-- Card Count Presets -->
                <fieldset>
                    <legend class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('flashcards.count') }}
                    </legend>
                    <div class="grid grid-cols-6 gap-2">
                        <button
                            v-for="cnt in countPresets"
                            :key="cnt"
                            type="button"
                            @click="form.count = cnt"
                            :aria-pressed="form.count === cnt"
                            class="min-h-11 py-2.5 rounded-xl border text-xs font-bold transition text-center cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--primary)]"
                            :class="form.count === cnt
                                ? 'border-[var(--primary)] bg-[var(--primary)] text-white shadow-sm'
                                : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            {{ toBnDigits(cnt) }}
                        </button>
                    </div>
                </fieldset>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-[var(--border)] flex items-center justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn btn-primary inline-flex items-center gap-2 min-h-12 px-6 py-3 text-sm font-bold shadow-md hover:shadow-lg active:scale-[0.98] transition disabled:opacity-50"
                    >
                        <SparklesIcon class="h-4 w-4" :class="{ 'animate-spin': form.processing }" />
                        <span>{{ form.processing ? (locale === 'en' ? 'Generating Cards...' : 'কার্ড তৈরি হচ্ছে...') : (locale === 'en' ? 'Generate Flashcards' : 'ফ্ল্যাশকার্ড তৈরি করুন') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </StudentLayout>
</template>
