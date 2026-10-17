<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    SparklesIcon,
    ArrowLeftIcon,
    RectangleStackIcon,
    BookOpenIcon,
    DocumentTextIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ books: Array });
const { t, locale } = useI18n();

const form = useForm({
    title: '',
    source_book_id: null,
    source_text: '',
    count: 15,
});

const countPresets = [5, 10, 15, 20, 25, 30];

function submit() {
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
                            ? 'Generate front-and-back study flashcards from books or custom text notes.'
                            : 'বইয়ের অধ্যায় বা আপনার নোট থেকে এক ক্লিকেই ইন্টারেক্টিভ প্রশ্নোত্তর কার্ড তৈরি করুন।' }}
                    </p>
                </div>
                <div class="hidden sm:grid h-14 w-14 place-items-center rounded-2xl bg-[var(--surface2)] text-[var(--tertiary)] shrink-0">
                    <RectangleStackIcon class="h-8 w-8" />
                </div>
            </div>

            <!-- Form Card (Centered) -->
            <form @submit.prevent="submit" class="space-y-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                <!-- Set Title -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('flashcards.set_title') }}
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        required
                        :placeholder="locale === 'en' ? 'e.g. Periodic Table Elements, Biology Cell Organelles...' : 'যেমন: পর্যায় সারণির গুরুত্বপূর্ণ মৌল, জীববিজ্ঞান পরিপাকতন্ত্র...'"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-rose-500">{{ form.errors.title }}</p>
                </div>

                <!-- Book Selection if available -->
                <div v-if="books && books.length">
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('exams.source_book') }}
                        <span class="text-xs font-normal text-[var(--text-muted)] ml-1">
                            ({{ locale === 'en' ? 'Optional, pick from your library' : 'ঐচ্ছিক, আপনার লাইব্রেরির বই' }})
                        </span>
                    </label>
                    <select
                        v-model="form.source_book_id"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-3 text-sm font-medium focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                    >
                        <option :value="null">— {{ locale === 'en' ? 'None (use text notes below)' : 'কোনোটি নয় (নিচের নোট ব্যবহার করুন)' }} —</option>
                        <option v-for="b in books" :key="b.id" :value="b.id">{{ b.title }}</option>
                    </select>
                </div>

                <!-- Source text notes if no book selected -->
                <div v-if="!form.source_book_id">
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

                <!-- Card Count -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('flashcards.count') }}
                    </label>
                    <div class="flex items-center gap-2">
                        <button
                            v-for="cnt in countPresets"
                            :key="cnt"
                            type="button"
                            class="flex-1 rounded-xl py-2.5 text-xs font-bold border transition-all"
                            :class="form.count === cnt
                                ? 'border-[var(--primary)] bg-[var(--primary)] text-white shadow-sm'
                                : 'border-[var(--border)] bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                            @click="form.count = cnt"
                        >
                            {{ cnt }}
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn btn-primary w-full justify-center gap-2 rounded-xl py-3.5 text-base font-bold shadow-lg shadow-[var(--primary)]/20 transition-all hover:scale-[1.01] active:scale-[0.99] disabled:opacity-60"
                    >
                        <SparklesIcon class="h-5 w-5" />
                        <span>{{ form.processing ? t('flashcards.generating') : t('flashcards.generate') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </StudentLayout>
</template>
