<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    mode: String,
    header: Object,
    totalCount: Number,
    questions: Object,
    years: Array,
    filters: Object,
    relatedCollections: Array,
    relatedBooks: Array,
    seo: Object,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');

const searchInput = ref(props.filters?.search || '');
const selectedYear = ref(props.filters?.year || 'all');

// Interactive self-practice state per question
const selectedAnswers = ref({});
const revealedQuestions = ref({});

function selectOption(questionId, optionKey) {
    if (selectedAnswers.value[questionId]) return;
    selectedAnswers.value[questionId] = optionKey;
    revealedQuestions.value[questionId] = true;
}

function toggleReveal(questionId) {
    revealedQuestions.value[questionId] = !revealedQuestions.value[questionId];
}

const sessionStats = computed(() => {
    const items = props.questions?.data || [];
    let answered = 0;
    let correct = 0;
    for (const q of items) {
        const picked = selectedAnswers.value[q.id];
        if (picked) {
            answered++;
            if (picked === q.correct_option) correct++;
        }
    }
    return { answered, correct, wrong: answered - correct };
});

function applyFilters() {
    const base = props.mode === 'subject'
        ? `/questions/subject/${props.header.slug}`
        : `/questions/${props.header.slug}`;
    router.get(base, {
        search: searchInput.value || undefined,
        year: selectedYear.value !== 'all' ? selectedYear.value : undefined,
    }, { preserveState: true, replace: true });
}
</script>

<template>
    <SeoHead v-if="seo" :seo="seo" />
    <PublicLayout>
        <!-- Breadcrumb -->
        <div class="border-b border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-[#0e1320] py-3 text-xs text-slate-500 dark:text-slate-400">
            <div class="max-w-7xl mx-auto px-4 md:px-6 flex items-center gap-2 flex-wrap">
                <Link href="/" class="hover:text-[var(--primary)]">{{ en ? 'Home' : 'হোম' }}</Link>
                <span>/</span>
                <Link href="/questions" class="hover:text-[var(--primary)]">{{ en ? 'Question Bank' : 'প্রশ্নব্যাংক' }}</Link>
                <span>/</span>
                <span class="text-slate-900 dark:text-slate-200 font-semibold">{{ header.short_bn }}</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 md:px-6 py-8 md:py-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Main Column -->
                <main class="lg:col-span-8 space-y-6">
                    <!-- Header Card -->
                    <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#171c28] p-6 md:p-8 shadow-sm">
                        <div class="flex flex-wrap items-center gap-2 text-xs font-bold mb-3">
                            <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                {{ (totalCount || 0).toLocaleString() }}+ {{ en ? 'Verified MCQs' : 'ভেরিফাইড MCQ' }}
                            </span>
                            <span class="text-slate-500 dark:text-slate-400">{{ header.institution_bn }}</span>
                        </div>

                        <h1 class="font-heading text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white leading-snug">
                            {{ en ? header.title_en : header.title_bn }}
                        </h1>
                        <p class="mt-3 text-sm md:text-base text-slate-600 dark:text-slate-300 leading-relaxed">
                            {{ header.description_bn }}
                        </p>

                        <!-- Search & Year Filter -->
                        <form class="mt-5 flex flex-col sm:flex-row gap-3" @submit.prevent="applyFilters">
                            <input
                                v-model="searchInput"
                                type="text"
                                :placeholder="en ? 'Search question keyword...' : 'প্রশ্ন বা টপিক লিখে খুঁজুন...'"
                                class="flex-1 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#0e1320] px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:border-[var(--primary)]"
                            />
                            <select
                                v-if="years && years.length"
                                v-model="selectedYear"
                                class="rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#0e1320] px-3 py-2.5 text-sm text-slate-900 dark:text-white"
                                @change="applyFilters"
                            >
                                <option value="all">{{ en ? 'All Years' : 'সকল সাল' }}</option>
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[var(--primary)] text-white text-sm font-bold hover:opacity-90 transition">
                                {{ en ? 'Filter' : 'খুঁজুন' }}
                            </button>
                        </form>

                        <!-- Live Mini Score Bar -->
                        <div v-if="sessionStats.answered > 0" class="mt-4 flex flex-wrap items-center gap-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 px-4 py-2.5 text-xs font-bold">
                            <span class="text-slate-700 dark:text-slate-200">{{ en ? 'Practice Score:' : 'আপনার স্কোর:' }}</span>
                            <span class="text-emerald-600 dark:text-emerald-400">✓ {{ sessionStats.correct }} {{ en ? 'Correct' : 'সঠিক' }}</span>
                            <span class="text-rose-600 dark:text-rose-400">✕ {{ sessionStats.wrong }} {{ en ? 'Wrong' : 'ভুল' }}</span>
                        </div>
                    </div>

                    <!-- Questions List -->
                    <div class="space-y-4">
                        <article
                            v-for="(q, idx) in questions.data"
                            :key="q.id"
                            class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#171c28] p-5 md:p-6 shadow-xs"
                        >
                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500 dark:text-slate-400 mb-3">
                                <span class="font-mono font-bold text-[var(--primary)]">
                                    #{{ ((questions.current_page - 1) * questions.per_page) + idx + 1 }}
                                </span>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span v-if="q.exam_name" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-slate-300">
                                        {{ q.exam_name }}
                                    </span>
                                    <span v-if="q.year" class="px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-700 dark:text-amber-400 font-mono">
                                        {{ q.year }}
                                    </span>
                                    <Link
                                        v-if="q.subject"
                                        :href="`/questions/subject/${q.subject.slug}`"
                                        class="px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 hover:underline"
                                    >
                                        {{ q.subject.name_bn || q.subject.name_en }}
                                    </Link>
                                </div>
                            </div>

                            <h2 class="text-base md:text-lg font-bold text-slate-900 dark:text-white leading-relaxed">
                                {{ q.question_text }}
                            </h2>

                            <!-- Options -->
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <button
                                    v-for="opt in q.options"
                                    :key="opt.key"
                                    type="button"
                                    :class="[
                                        'text-left px-4 py-3 rounded-xl border text-sm transition flex items-start gap-2.5',
                                        revealedQuestions[q.id] && opt.key === q.correct_option
                                            ? 'border-emerald-500 bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 font-semibold'
                                            : selectedAnswers[q.id] === opt.key && opt.key !== q.correct_option
                                                ? 'border-rose-500 bg-rose-500/10 text-rose-700 dark:text-rose-300'
                                                : 'border-slate-200 dark:border-white/10 bg-slate-50/70 dark:bg-[#0e1320]/60 text-slate-800 dark:text-slate-200 hover:border-[var(--primary)]/50'
                                    ]"
                                    @click="selectOption(q.id, opt.key)"
                                >
                                    <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-current text-xs font-bold">
                                        {{ opt.key }}
                                    </span>
                                    <span class="leading-snug">{{ opt.text }}</span>
                                </button>
                            </div>

                            <!-- Reveal / Explanation -->
                            <div class="mt-4 flex items-center justify-between">
                                <button
                                    type="button"
                                    class="text-xs font-bold text-[var(--primary)] hover:underline"
                                    @click="toggleReveal(q.id)"
                                >
                                    {{ revealedQuestions[q.id] ? (en ? 'Hide Answer & Explanation' : 'উত্তর ও ব্যাখ্যা লুকান') : (en ? 'Show Answer & Explanation' : 'সঠিক উত্তর ও ব্যাখ্যা দেখুন') }}
                                </button>
                            </div>

                            <div
                                v-if="revealedQuestions[q.id]"
                                class="mt-3 rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4 text-sm text-slate-800 dark:text-slate-200"
                            >
                                <div class="font-bold text-emerald-700 dark:text-emerald-400">
                                    {{ en ? 'Correct Answer:' : 'সঠিক উত্তর:' }} ({{ q.correct_option }}) {{ q.correct_option_text }}
                                </div>
                                <p v-if="q.explanation" class="mt-1.5 text-xs md:text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                    {{ q.explanation }}
                                </p>
                            </div>
                        </article>
                    </div>

                    <!-- Pagination -->
                    <nav v-if="questions.links && questions.links.length > 3" class="flex flex-wrap items-center justify-center gap-1.5 pt-4">
                        <template v-for="(link, i) in questions.links" :key="i">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="[
                                    'px-3.5 py-2 rounded-xl text-xs font-bold border transition',
                                    link.active
                                        ? 'bg-[var(--primary)] text-white border-[var(--primary)]'
                                        : 'bg-white dark:bg-[#171c28] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-white/10 hover:border-[var(--primary)]'
                                ]"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="px-3.5 py-2 rounded-xl text-xs text-slate-400 border border-transparent"
                                v-html="link.label"
                            />
                        </template>
                    </nav>

                    <!-- GEO FAQ Section -->
                    <section v-if="header.faqs && header.faqs.length" class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#171c28] p-6 md:p-8">
                        <h2 class="font-heading text-xl font-bold text-slate-900 dark:text-white mb-4">
                            {{ en ? 'Frequently Asked Questions' : 'সাধারণ জিজ্ঞাসা (FAQ)' }}
                        </h2>
                        <div class="space-y-4">
                            <div v-for="(faq, idx) in header.faqs" :key="idx" class="border-b border-slate-100 dark:border-white/5 pb-4 last:border-0 last:pb-0">
                                <h3 class="font-bold text-sm md:text-base text-slate-900 dark:text-white">{{ faq.question }}</h3>
                                <p class="mt-1.5 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ faq.answer }}</p>
                            </div>
                        </div>
                    </section>
                </main>

                <!-- Sidebar -->
                <aside class="lg:col-span-4 space-y-6">
                    <!-- Full Mock Test CTA -->
                    <div class="rounded-3xl bg-gradient-to-br from-slate-900 to-indigo-950 text-white p-6 border border-white/10 shadow-xl">
                        <span class="inline-block px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 text-[11px] font-bold mb-3">
                            AI EXAM ENGINE
                        </span>
                        <h3 class="font-heading text-xl font-extrabold leading-snug">
                            {{ en ? 'Take Full Timed Model Test' : 'পূর্ণাঙ্গ টাইমড মডেল টেস্ট দিন' }}
                        </h3>
                        <p class="mt-2 text-xs text-slate-300 leading-relaxed">
                            {{ en
                                ? 'Practice with negative marking, instant merit analytics, Mistake Review, and 24/7 AI Tutor explanations.'
                                : 'নেগেটিভ মার্কিং, সময়সীমা, ভুল উত্তরের তালিকা এবং AI টিউটরের বিস্তারিত ব্যাখ্যাসহ পূর্ণাঙ্গ পরীক্ষা দিতে ফ্রি অ্যাকাউন্ট খুলুন।'
                            }}
                        </p>
                        <div class="mt-5 flex flex-col gap-2.5">
                            <Link href="/register" class="w-full text-center px-4 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-sm transition">
                                {{ en ? 'Start Free Exam' : 'ফ্রি পরীক্ষা শুরু করুন' }}
                            </Link>
                            <Link href="/plans" class="w-full text-center px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white font-semibold text-xs border border-white/15 transition">
                                {{ en ? 'View Study Plans' : 'স্টাডি প্ল্যান দেখুন' }}
                            </Link>
                        </div>
                    </div>

                    <!-- Related Exam Collections -->
                    <div v-if="relatedCollections && relatedCollections.length" class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#171c28] p-6">
                        <h3 class="font-heading text-base font-bold text-slate-900 dark:text-white mb-4">
                            {{ en ? 'More Question Banks' : 'অন্যান্য পরীক্ষার প্রশ্নব্যাংক' }}
                        </h3>
                        <div class="space-y-2.5">
                            <Link
                                v-for="col in relatedCollections"
                                :key="col.slug"
                                :href="`/questions/${col.slug}`"
                                class="block rounded-xl border border-slate-200 dark:border-white/10 px-4 py-3 text-sm font-semibold text-slate-800 dark:text-slate-200 hover:border-[var(--primary)] hover:text-[var(--primary)] transition"
                            >
                                {{ en ? col.title_en : col.short_bn }}
                            </Link>
                        </div>
                    </div>

                    <!-- Related Digital Books -->
                    <div v-if="relatedBooks && relatedBooks.length" class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#171c28] p-6">
                        <h3 class="font-heading text-base font-bold text-slate-900 dark:text-white mb-4">
                            {{ en ? 'Recommended Digital Books' : 'প্রস্তুতির সহায়ক ডিজিটাল বই' }}
                        </h3>
                        <div class="space-y-2.5">
                            <Link
                                v-for="book in relatedBooks"
                                :key="book.id"
                                :href="`/library/${book.slug}`"
                                class="block rounded-xl border border-slate-200 dark:border-white/10 px-4 py-3 hover:border-[var(--primary)] transition"
                            >
                                <div class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1">{{ book.title }}</div>
                                <div class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5 font-semibold">
                                    {{ book.is_free ? (en ? 'Free Book' : 'ফ্রি বই') : `৳${book.price}` }}
                                </div>
                            </Link>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </PublicLayout>
</template>
