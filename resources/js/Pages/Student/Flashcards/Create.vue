<script setup>
import { ref, computed, onMounted } from 'vue';
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
    BoltIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    books: { type: Array, default: () => [] },
    subjects: { type: Array, default: () => [] },
    exams: { type: Array, default: () => [] },
    prefill: { type: Object, default: () => ({}) },
});

const { t, locale } = useI18n();

const sourceType = ref(props.prefill?.source_type || 'topic');

const form = useForm({
    title: props.prefill?.title || '',
    source_type: props.prefill?.source_type || 'topic',
    source_topic: '',
    source_book_id: null,
    chapter_ids: [],
    subject_id: props.prefill?.subject_id || null,
    origin_tag: props.prefill?.origin_tag || '',
    sector: props.prefill?.sector || 'admission',
    source_text: '',
    count: 15,
});

onMounted(() => {
    if (props.prefill?.source_type) {
        sourceType.value = props.prefill.source_type;
        form.source_type = props.prefill.source_type;
    }
});

function switchSourceType(type) {
    sourceType.value = type;
    form.source_type = type;
    if (type !== 'book') {
        form.source_book_id = null;
        form.chapter_ids = [];
    }
}

const selectedBook = computed(() => props.books?.find((b) => b.id === form.source_book_id));

const countPresets = [5, 10, 15, 20, 25, 30];

function submit() {
    // Auto-generate title if left blank
    if (!form.title.trim()) {
        if (sourceType.value === 'topic' && form.source_topic.trim()) {
            form.title = form.source_topic.trim() + ' ফ্ল্যাশকার্ড';
        } else if (sourceType.value === 'book' && selectedBook.value) {
            form.title = selectedBook.value.title + ' ফ্ল্যাশকার্ড';
        } else if (sourceType.value === 'question_bank') {
            const subj = props.subjects?.find((s) => s.id === form.subject_id);
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
                    <h1 class="font-heading text-2xl font-black tracking-tight sm:text-3xl">
                        {{ t('flashcards.new_set') }}
                    </h1>
                    <p class="mt-1 text-sm text-[var(--text-muted)]">
                        {{ locale === 'en'
                            ? 'Generate front-and-back study flashcards from topics, books, question banks, or notes.'
                            : 'টপিক, বই, প্রশ্নব্যাংক বা নোট থেকে নিমেষেই ইন্টারেক্টিভ রিভিশন কার্ড তৈরি করুন।' }}
                    </p>
                </div>
                <div class="hidden sm:grid h-14 w-14 place-items-center rounded-2xl bg-[var(--surface2)] text-[var(--tertiary)] shrink-0">
                    <RectangleStackIcon class="h-8 w-8" />
                </div>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="submit" class="space-y-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                <!-- Source Selection Tabs -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ locale === 'en' ? 'Choose Material Source' : 'ফ্ল্যাশকার্ড তৈরির উৎস নির্বাচন করুন' }}
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button
                            type="button"
                            @click="switchSourceType('topic')"
                            class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border text-xs font-bold transition text-center cursor-pointer"
                            :class="sourceType === 'topic' ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)] shadow-xs ring-1 ring-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            <PencilSquareIcon class="h-5 w-5" />
                            <span>{{ locale === 'en' ? 'Topic' : 'টপিক লিখুন' }}</span>
                        </button>
                        <button
                            type="button"
                            @click="switchSourceType('paragraph')"
                            class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border text-xs font-bold transition text-center cursor-pointer"
                            :class="sourceType === 'paragraph' ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)] shadow-xs ring-1 ring-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            <DocumentTextIcon class="h-5 w-5" />
                            <span>{{ locale === 'en' ? 'Paragraph/Note' : 'নোট/অনুচ্ছেদ' }}</span>
                        </button>
                        <button
                            type="button"
                            @click="switchSourceType('book')"
                            class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border text-xs font-bold transition text-center cursor-pointer"
                            :class="sourceType === 'book' ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)] shadow-xs ring-1 ring-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            <BookOpenIcon class="h-5 w-5" />
                            <span>{{ locale === 'en' ? 'Book' : 'বই ও অধ্যায়' }}</span>
                        </button>
                        <button
                            type="button"
                            @click="switchSourceType('question_bank')"
                            class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border text-xs font-bold transition text-center cursor-pointer"
                            :class="sourceType === 'question_bank' ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)] shadow-xs ring-1 ring-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            <AcademicCapIcon class="h-5 w-5" />
                            <span>{{ locale === 'en' ? 'Question Bank' : 'প্রশ্নব্যাংক' }}</span>
                        </button>
                    </div>
                </div>

                <!-- 1. TOPIC INPUT -->
                <div v-if="sourceType === 'topic'">
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ locale === 'en' ? 'Topic or Concept' : 'টপিক বা বিষয়ের নাম' }}
                    </label>
                    <input
                        v-model="form.source_topic"
                        type="text"
                        :placeholder="locale === 'en' ? 'e.g. Periodic Table Elements, Photosynthesis, Computer Networking...' : 'যেমন: পর্যায় সারণির মৌলসমূহ, সালোকসংশ্লেষণ, নেটওয়ার্কিং প্রটোকল, বিসিএস বাংলা সাহিত্য...'"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    />
                    <p class="mt-1 text-xs text-[var(--text-muted)]">
                        {{ locale === 'en' ? 'Our AI will generate high-yield question & answer pairs for this topic.' : 'এআই স্বয়ংক্রিয়ভাবে এই টপিকের গুরুত্বপূর্ণ সংজ্ঞা, সূত্র ও তথ্য দিয়ে কার্ড তৈরি করবে।' }}
                    </p>
                </div>

                <!-- 2. PARAGRAPH / NOTE INPUT -->
                <div v-if="sourceType === 'paragraph'">
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('flashcards.source_text') }}
                    </label>
                    <textarea
                        v-model="form.source_text"
                        rows="5"
                        :placeholder="locale === 'en' ? 'Paste keywords, vocabulary, formulas, or lecture notes here...' : 'এখানে আপনার নোট, সংজ্ঞা, সূত্র বা গুরুত্বপূর্ণ প্যারাগ্রাফ পেস্ট করুন...'"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    ></textarea>
                    <p v-if="form.errors.source_text" class="mt-1 text-xs text-rose-500">{{ form.errors.source_text }}</p>
                </div>

                <!-- 3. BOOK & CHAPTER SELECTION -->
                <div v-if="sourceType === 'book'">
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('exams.source_book') }}
                    </label>
                    <select
                        v-model="form.source_book_id"
                        @change="form.chapter_ids = []"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    >
                        <option :value="null">— {{ locale === 'en' ? 'Select a book from your library' : 'আপনার লাইব্রেরি থেকে একটি বই নির্বাচন করুন' }} —</option>
                        <option v-for="b in books" :key="b.id" :value="b.id">{{ b.title }}</option>
                    </select>

                    <div v-if="selectedBook?.chapters?.length" class="mt-3">
                        <label class="mb-1 block text-sm font-bold text-[var(--text)]">
                            {{ locale === 'en' ? 'Chapters (Optional)' : 'অধ্যায়সমূহ (ঐচ্ছিক)' }}
                        </label>
                        <select
                            v-model="form.chapter_ids"
                            multiple
                            size="4"
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                        >
                            <option v-for="chapter in selectedBook.chapters" :key="chapter.id" :value="chapter.id">
                                {{ chapter.chapter_number ? `${chapter.chapter_number}. ` : '' }}{{ chapter.title }}
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-[var(--text-muted)]">
                            {{ locale === 'en' ? 'Select chapter(s), or leave unselected to sample across the whole book.' : 'অধ্যায় নির্বাচন করুন, অথবা সমগ্র বই থেকে কার্ড বানাতে আনসিলেক্টেড রাখুন।' }}
                        </p>
                    </div>
                </div>

                <!-- 4. QUESTION BANK SELECTION -->
                <div v-if="sourceType === 'question_bank'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-[var(--text)] mb-2">
                            {{ locale === 'en' ? 'Subject' : 'বিষয় নির্বাচন করুন' }}
                        </label>
                        <select
                            v-model="form.subject_id"
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                        >
                            <option :value="null">— বিষয় নির্বাচন করুন —</option>
                            <option v-for="s in subjects" :key="s.id" :value="s.id">
                                {{ s.name_bn || s.name_en }} ({{ s.segment }})
                            </option>
                        </select>
                    </div>

                    <div v-if="exams?.length">
                        <label class="block text-sm font-bold text-[var(--text)] mb-2">
                            {{ locale === 'en' ? 'Specific Exam / Institution (Optional)' : 'নির্দিষ্ট পরীক্ষা বা প্রতিষ্ঠান (ঐচ্ছিক)' }}
                        </label>
                        <select
                            v-model="form.origin_tag"
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:outline-none"
                        >
                            <option value="">— সকল পরীক্ষা থেকে প্রশ্ন নিন —</option>
                            <option v-for="ex in exams" :key="ex.origin_tag" :value="ex.origin_tag">
                                {{ ex.exam_name || ex.origin_tag }} ({{ ex.count }} টি প্রশ্ন)
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Set Title -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('flashcards.set_title') }}
                        <span class="text-xs font-normal text-[var(--text-muted)] ml-1">
                            ({{ locale === 'en' ? 'Optional, auto-generated if blank' : 'ঐচ্ছিক, ফাঁকা রাখলে স্বয়ংক্রিয় তৈরি হবে' }})
                        </span>
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        :placeholder="locale === 'en' ? 'e.g. DU Admission Chemistry, 50th BCS English...' : 'যেমন: ঢাবি রসায়ন ফাইনাল রিভিশন, বিসিএস ইংরেজি লিটারেচার...'"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-rose-500">{{ form.errors.title }}</p>
                </div>

                <!-- Card Count Presets -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('flashcards.count') }}
                    </label>
                    <div class="grid grid-cols-6 gap-2">
                        <button
                            v-for="cnt in countPresets"
                            :key="cnt"
                            type="button"
                            @click="form.count = cnt"
                            class="py-2.5 rounded-xl border text-xs font-bold transition text-center cursor-pointer"
                            :class="form.count === cnt
                                ? 'border-[var(--primary)] bg-[var(--primary)] text-white shadow-sm'
                                : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                        >
                            {{ cnt }}
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-[var(--border)] flex items-center justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn btn-primary inline-flex items-center gap-2 px-6 py-3 text-sm font-bold shadow-md hover:shadow-lg disabled:opacity-50"
                    >
                        <SparklesIcon class="h-4 w-4" :class="{ 'animate-spin': form.processing }" />
                        <span>{{ form.processing ? (locale === 'en' ? 'Generating Cards...' : 'কার্ড তৈরি হচ্ছে...') : (locale === 'en' ? 'Generate Flashcards' : 'ফ্ল্যাশকার্ড তৈরি করুন') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </StudentLayout>
</template>
