<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import FlashcardFlip from '@/Components/Flashcard/FlashcardFlip.vue';
import { useI18n } from '@/i18n';
import { renderMath } from '@/Utils/renderMath';
import {
    ArrowPathIcon,
    ArrowLeftIcon,
    ArrowRightIcon,
    ArrowDownTrayIcon,
    ArrowsRightLeftIcon,
    Squares2X2Icon,
    RectangleStackIcon,
    CheckCircleIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ set: Object });
const { t, locale } = useI18n();

const BN_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
function toBnDigits(val) {
    if (val === null || val === undefined) return '';
    return locale.value === 'en' ? String(val) : String(val).replace(/[0-9]/g, (d) => BN_DIGITS[Number(d)]);
}

const cardsList = ref([...(props.set.flashcards || [])]);
watch(() => props.set.flashcards, (fresh) => {
    cardsList.value = [...(fresh || [])];
});

const currentIndex = ref(0);
const viewMode = ref('deck'); // 'deck' | 'grid'
const knownIds = ref(new Set());
const reviewAgainIds = ref(new Set());

const currentCard = computed(() => cardsList.value[currentIndex.value]);
const done = computed(() => cardsList.value.length > 0 && currentIndex.value >= cardsList.value.length);
const generationStatus = computed(() => props.set.generation_status ?? 'completed');
const retrying = ref(false);
let pollInterval = null;

const progressPercent = computed(() => {
    if (!cardsList.value.length) return 0;
    const completed = Math.min(currentIndex.value, cardsList.value.length);
    return Math.round((completed / cardsList.value.length) * 100);
});

function poll() {
    pollInterval = setInterval(() => {
        router.reload({ only: ['set'], preserveScroll: true });
    }, 2500);
}

function stopPollingIfReady(status) {
    if (status !== 'generating' && pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
}

onMounted(() => {
    if (generationStatus.value === 'generating') poll();
});
onBeforeUnmount(() => clearInterval(pollInterval));
watch(generationStatus, stopPollingIfReady);

function retryGeneration() {
    retrying.value = true;
    router.post(`/flashcards/${props.set.id}/retry`, {}, {
        onFinish: () => { retrying.value = false; },
    });
}

function handleReview({ card, result }) {
    if (result === 'known') {
        knownIds.value.add(card.id);
        reviewAgainIds.value.delete(card.id);
    } else {
        reviewAgainIds.value.add(card.id);
        knownIds.value.delete(card.id);
    }

    router.post(`/flashcards/${props.set.id}/cards/${card.id}/review`, { result }, {
        preserveScroll: true,
        onSuccess: () => {
            currentIndex.value++;
        },
    });
}

function goPrev() {
    if (currentIndex.value > 0) currentIndex.value--;
}

function goNext() {
    if (currentIndex.value < cardsList.value.length - 1) currentIndex.value++;
}

function shuffleCards() {
    const arr = [...cardsList.value];
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    cardsList.value = arr;
    currentIndex.value = 0;
}

function restartAll() {
    cardsList.value = [...(props.set.flashcards || [])];
    knownIds.value = new Set();
    reviewAgainIds.value = new Set();
    currentIndex.value = 0;
}

function practiceReviewAgain() {
    const missed = (props.set.flashcards || []).filter((c) => reviewAgainIds.value.has(c.id));
    if (!missed.length) {
        restartAll();
        return;
    }
    cardsList.value = missed;
    reviewAgainIds.value = new Set();
    currentIndex.value = 0;
}
</script>

<template>
    <Head :title="set.title" />
    <StudentLayout>
        <div class="mx-auto max-w-3xl pb-12">
            <!-- Top Navigation & Action Bar -->
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <Link
                    href="/flashcards"
                    class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--text)] transition-colors"
                >
                    <ArrowLeftIcon class="h-4 w-4" />
                    <span>{{ locale === 'en' ? 'All Flashcard Sets' : 'সকল ফ্ল্যাশকার্ড' }}</span>
                </Link>

                <div class="flex flex-wrap items-center gap-2">
                    <a
                        :href="`/flashcards/${set.id}/pdf`"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3.5 py-2 text-xs font-bold text-[var(--text)] shadow-xs transition hover:border-[var(--primary)]/50 hover:text-[var(--primary)]"
                    >
                        <ArrowDownTrayIcon class="h-4 w-4" />
                        <span>{{ t('flashcards.download_pdf') }}</span>
                    </a>
                </div>
            </div>

            <!-- Deck Header Card -->
            <header class="mb-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 sm:p-6 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span v-if="set.source_label" class="inline-flex items-center gap-1 rounded-full bg-[var(--tertiary)]/10 px-2.5 py-0.5 text-[11px] font-bold text-[var(--tertiary)]">
                                <SparklesIcon class="h-3 w-3" />
                                {{ set.source_label }}
                            </span>
                            <span class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-[11px] font-mono font-semibold text-[var(--text-muted)]">
                                {{ toBnDigits(set.flashcards?.length || 0) }} {{ t('flashcards.cards_count') }}
                            </span>
                        </div>
                        <h1 class="font-heading text-xl sm:text-2xl font-black tracking-tight text-[var(--text)] truncate">
                            {{ set.title }}
                        </h1>
                    </div>

                    <!-- Deck Toolbar: View Toggle & Shuffle -->
                    <div v-if="generationStatus === 'completed' && set.flashcards?.length" class="flex items-center gap-2 shrink-0">
                        <button
                            v-if="viewMode === 'deck' && !done"
                            type="button"
                            @click="shuffleCards"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text)] transition cursor-pointer"
                            :title="locale === 'en' ? 'Shuffle deck' : 'কার্ড এলোমেলো করুন'"
                        >
                            <ArrowsRightLeftIcon class="h-4 w-4" />
                            <span>{{ locale === 'en' ? 'Shuffle' : 'শাফল' }}</span>
                        </button>
                        <button
                            type="button"
                            @click="viewMode = viewMode === 'deck' ? 'grid' : 'deck'"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text)] transition cursor-pointer"
                        >
                            <component :is="viewMode === 'deck' ? Squares2X2Icon : RectangleStackIcon" class="h-4 w-4" />
                            <span>{{ viewMode === 'deck'
                                ? (locale === 'en' ? 'All Cards' : 'সব কার্ড দেখুন')
                                : (locale === 'en' ? 'Study Deck' : 'ডেক মোড') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Session Progress Bar -->
                <div v-if="generationStatus === 'completed' && cardsList.length && viewMode === 'deck'" class="mt-4 pt-4 border-t border-[var(--border)]/70">
                    <div class="flex flex-wrap items-center justify-between gap-2 text-xs mb-2">
                        <span class="font-bold text-[var(--text)]">
                            {{ done
                                ? (locale === 'en' ? 'Session Completed' : 'সেশন সম্পন্ন হয়েছে')
                                : `${locale === 'en' ? 'Card' : 'কার্ড'} ${toBnDigits(currentIndex + 1)} / ${toBnDigits(cardsList.length)}` }}
                        </span>
                        <div class="flex items-center gap-3 text-[11px] font-semibold">
                            <span class="text-emerald-600 dark:text-emerald-400">
                                ✓ {{ locale === 'en' ? 'Known:' : 'জানি:' }} {{ toBnDigits(knownIds.size) }}
                            </span>
                            <span class="text-amber-600 dark:text-amber-400">
                                ↻ {{ locale === 'en' ? 'Review:' : 'আবার দেখব:' }} {{ toBnDigits(reviewAgainIds.size) }}
                            </span>
                        </div>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-[var(--surface2)]" aria-hidden="true">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-[var(--primary)] via-amber-500 to-emerald-500 transition-all duration-300"
                            :style="{ width: progressPercent + '%' }"
                        ></div>
                    </div>
                    <progress class="sr-only" :value="Math.min(currentIndex, cardsList.length)" :max="cardsList.length">
                        {{ progressPercent }}%
                    </progress>
                </div>
            </header>

            <!-- Generating Skeleton -->
            <div v-if="generationStatus === 'generating'" class="mx-auto max-w-xl rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-10 text-center shadow-lg">
                <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-[var(--primary)]/10 text-[var(--primary)]">
                    <ArrowPathIcon class="h-7 w-7 animate-spin" />
                </div>
                <h2 class="font-heading text-lg font-bold text-[var(--text)]">{{ t('flashcards.generating') }}</h2>
                <p class="mt-1.5 text-xs sm:text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Crafting question and full answer cards for your study deck...' : 'আপনার জন্য প্রশ্ন ও পূর্ণাঙ্গ উত্তরসহ ফ্ল্যাশকার্ড ডেক প্রস্তুত করা হচ্ছে...' }}
                </p>
            </div>

            <!-- Failed State -->
            <div v-else-if="generationStatus === 'failed'" class="mx-auto max-w-lg rounded-2xl border border-rose-500/30 bg-rose-500/10 p-8 text-center">
                <p class="font-semibold text-rose-600 dark:text-rose-400">{{ locale === 'en' ? 'Flashcard generation failed' : 'ফ্ল্যাশকার্ড তৈরি করা যায়নি' }}</p>
                <p class="mt-2 text-sm text-[var(--text-muted)]">{{ locale === 'en' ? 'Your source and settings are saved. You can retry without submitting them again.' : 'আপনার উৎস ও সেটিংস সংরক্ষিত আছে। আবার জমা না দিয়েই চেষ্টা করতে পারবেন।' }}</p>
                <button type="button" :disabled="retrying" class="btn btn-primary mt-5 inline-flex items-center gap-2 disabled:opacity-60" @click="retryGeneration">
                    <ArrowPathIcon class="h-4 w-4" :class="retrying ? 'animate-spin' : ''" />
                    {{ retrying ? (locale === 'en' ? 'Retrying…' : 'আবার চেষ্টা হচ্ছে…') : (locale === 'en' ? 'Retry generation' : 'আবার তৈরি করুন') }}
                </button>
            </div>

            <!-- Empty State -->
            <div v-else-if="!set.flashcards.length" class="text-center text-[var(--text-muted)] py-12 rounded-3xl border border-dashed border-[var(--border)] bg-[var(--surface)]">
                {{ locale === 'en' ? 'No flashcards were generated.' : 'কোনো ফ্ল্যাশকার্ড তৈরি হয়নি।' }}
            </div>

            <!-- Grid / All Cards Overview Mode -->
            <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <article
                    v-for="(c, idx) in cardsList"
                    :key="c.id"
                    class="flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 shadow-xs transition hover:border-[var(--primary)]/40 hover:shadow-md"
                >
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="rounded-full bg-[var(--primary)]/10 px-2.5 py-0.5 text-[11px] font-bold text-[var(--primary)]">
                                #{{ toBnDigits(idx + 1) }}
                            </span>
                            <button
                                type="button"
                                @click="currentIndex = idx; viewMode = 'deck';"
                                class="text-[11px] font-bold text-[var(--primary)] hover:underline cursor-pointer"
                            >
                                {{ locale === 'en' ? 'Open Card →' : 'কার্ডে যান →' }}
                            </button>
                        </div>
                        <div class="text-sm font-bold text-[var(--text)] leading-relaxed mb-3" v-html="renderMath(c.front)"></div>
                    </div>
                    <div class="rounded-xl border border-emerald-500/25 bg-emerald-500/10 p-3 text-xs sm:text-sm font-semibold text-emerald-900 dark:text-emerald-200 whitespace-pre-line" v-html="renderMath(c.back)"></div>
                </article>
            </div>

            <!-- Session Complete Summary Card -->
            <div v-else-if="done" class="mx-auto max-w-xl rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-8 sm:p-10 text-center shadow-xl">
                <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                    <CheckCircleIcon class="h-9 w-9" />
                </div>
                <h2 class="font-heading text-2xl font-black text-[var(--text)]">
                    {{ locale === 'en' ? 'Great job! Deck completed' : 'দারুণ! সম্পূর্ণ ডেক অনুশীলন শেষ' }}
                </h2>
                <p class="mt-1.5 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en'
                        ? `You reviewed all ${cardsList.length} flashcards in this session.`
                        : `এই সেশনে মোট ${toBnDigits(cardsList.length)}টি ফ্ল্যাশকার্ড অনুশীলন করেছেন।` }}
                </p>

                <div class="mt-6 grid grid-cols-2 gap-3 max-w-sm mx-auto">
                    <div class="rounded-2xl border border-emerald-500/25 bg-emerald-500/10 p-4">
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ toBnDigits(knownIds.size) }}</div>
                        <div class="text-xs font-semibold text-[var(--text-muted)] mt-0.5">{{ t('flashcards.known') }}</div>
                    </div>
                    <div class="rounded-2xl border border-amber-500/25 bg-amber-500/10 p-4">
                        <div class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ toBnDigits(reviewAgainIds.size) }}</div>
                        <div class="text-xs font-semibold text-[var(--text-muted)] mt-0.5">{{ t('flashcards.review_again') }}</div>
                    </div>
                </div>

                <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
                    <button
                        v-if="reviewAgainIds.size > 0"
                        type="button"
                        @click="practiceReviewAgain"
                        class="btn btn-primary px-5 py-2.5 text-xs sm:text-sm font-bold rounded-xl shadow-md"
                    >
                        {{ locale === 'en' ? `Practice ${reviewAgainIds.size} Missed Cards` : `আবার দেখার ${toBnDigits(reviewAgainIds.size)}টি কার্ড অনুশীলন করুন` }}
                    </button>
                    <button
                        type="button"
                        @click="restartAll"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-5 py-2.5 text-xs sm:text-sm font-bold text-[var(--text)] hover:border-[var(--primary)]/50 transition cursor-pointer"
                    >
                        <ArrowPathIcon class="h-4 w-4" />
                        <span>{{ locale === 'en' ? 'Review All Again' : 'সবগুলো আবার অনুশীলন করুন' }}</span>
                    </button>
                </div>
            </div>

            <!-- Active Study Card Stage -->
            <div v-else class="space-y-4">
                <FlashcardFlip
                    :key="currentCard.id"
                    :card="currentCard"
                    :index="currentIndex"
                    :total="cardsList.length"
                    :set-title="set.title"
                    @review="handleReview"
                />

                <!-- Previous / Next Card Browsing Controls -->
                <div class="mx-auto flex max-w-xl items-center justify-between pt-1 text-xs">
                    <button
                        type="button"
                        :disabled="currentIndex === 0"
                        @click="goPrev"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3.5 py-2 font-semibold text-[var(--text-muted)] transition hover:text-[var(--text)] disabled:opacity-40 cursor-pointer disabled:cursor-not-allowed"
                    >
                        <ArrowLeftIcon class="h-3.5 w-3.5" />
                        <span>{{ locale === 'en' ? 'Previous' : 'পূর্ববর্তী কার্ড' }}</span>
                    </button>
                    <span class="font-mono text-[var(--text-muted)]">
                        {{ toBnDigits(currentIndex + 1) }} / {{ toBnDigits(cardsList.length) }}
                    </span>
                    <button
                        type="button"
                        :disabled="currentIndex >= cardsList.length - 1"
                        @click="goNext"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3.5 py-2 font-semibold text-[var(--text-muted)] transition hover:text-[var(--text)] disabled:opacity-40 cursor-pointer disabled:cursor-not-allowed"
                    >
                        <span>{{ locale === 'en' ? 'Next' : 'পরবর্তী কার্ড' }}</span>
                        <ArrowRightIcon class="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
