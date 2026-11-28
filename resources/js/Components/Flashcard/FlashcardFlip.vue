<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { useI18n } from '@/i18n';
import { renderMath } from '@/Utils/renderMath';
import {
    SparklesIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    BookOpenIcon,
    EyeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    card: { type: Object, required: true },
    index: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
    setTitle: { type: String, default: '' },
});

const emit = defineEmits(['review']);
const { t, locale } = useI18n();

const flipped = ref(false);

const BN_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
function toBnDigits(val) {
    if (val === null || val === undefined) return '';
    return locale.value === 'en' ? String(val) : String(val).replace(/[0-9]/g, (d) => BN_DIGITS[Number(d)]);
}

watch(() => props.card?.id, () => {
    flipped.value = false;
});

function toggleFlip() {
    flipped.value = !flipped.value;
}

function review(result) {
    emit('review', { card: props.card, result });
    flipped.value = false;
}

function handleKeydown(e) {
    if (e.isComposing) return;
    const tag = e.target?.tagName;
    if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || e.target?.isContentEditable) return;

    if (e.code === 'Space') {
        if (tag === 'BUTTON') return;
        e.preventDefault();
        toggleFlip();
    } else if (flipped.value && (e.key === '1' || e.key === 'ArrowLeft')) {
        if (e.key === '1') {
            e.preventDefault();
            review('review_again');
        }
    } else if (flipped.value && e.key === '2') {
        e.preventDefault();
        review('known');
    }
}

onMounted(() => window.addEventListener('keydown', handleKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', handleKeydown));
</script>

<template>
    <article class="flashcard-deck-root flex flex-col items-center w-full">
        <!-- Layered Physical Deck Wrapper -->
        <div class="relative w-full max-w-xl pt-1 pb-5">
            <!-- Stacked deck cards peeking underneath -->
            <div
                aria-hidden="true"
                class="pointer-events-none absolute inset-x-6 bottom-1 h-24 rounded-3xl border border-[var(--border)] bg-[var(--surface2)]/70 shadow-sm transition-transform duration-300"
            ></div>
            <div
                aria-hidden="true"
                class="pointer-events-none absolute inset-x-3 bottom-3 h-24 rounded-3xl border border-[var(--border)] bg-[var(--surface)]/90 shadow-md transition-transform duration-300"
            ></div>

            <!-- Interactive Native Button Flip Surface -->
            <button
                type="button"
                @click="toggleFlip"
                :aria-pressed="flipped"
                :aria-label="flipped
                    ? (locale === 'en' ? 'Flashcard answer side shown. Press to flip back to question.' : 'সঠিক উত্তর দেখানো হচ্ছে। প্রশ্নে ফিরতে চাপুন।')
                    : (locale === 'en' ? 'Flashcard question side shown. Press to reveal answer.' : 'প্রশ্ন দেখানো হচ্ছে। উত্তর দেখতে চাপুন।')"
                class="flashcard-stage relative z-10 block w-full min-h-[310px] sm:min-h-[340px] text-left cursor-pointer rounded-3xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[var(--primary)]"
            >
                <div class="flashcard-inner relative w-full h-full min-h-[310px] sm:min-h-[340px]" :class="{ 'is-flipped': flipped }">
                    <!-- ================= FRONT FACE (QUESTION) ================= -->
                    <section
                        :aria-hidden="flipped"
                        class="flashcard-face flashcard-front absolute inset-0 flex flex-col justify-between overflow-hidden rounded-3xl border border-[var(--border)] bg-gradient-to-br from-[var(--surface)] via-[var(--surface)] to-amber-50/40 dark:to-amber-950/15 shadow-xl shadow-black/5"
                    >
                        <!-- Top Accent Bar -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-[var(--primary)] via-amber-500 to-orange-400"></div>

                        <!-- Card Header -->
                        <header class="flex items-center justify-between gap-3 px-5 sm:px-7 pt-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-[var(--primary)]/25 bg-[var(--primary)]/10 px-3 py-1 text-[11px] font-bold tracking-wide text-[var(--primary)]">
                                <SparklesIcon class="h-3.5 w-3.5" />
                                <span>{{ locale === 'en' ? 'QUESTION' : 'প্রশ্ন' }}</span>
                            </span>

                            <div class="flex items-center gap-2 text-xs text-[var(--text-muted)]">
                                <span v-if="total > 0" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 font-mono font-bold text-[11px]">
                                    #{{ toBnDigits(index + 1) }} / {{ toBnDigits(total) }}
                                </span>
                            </div>
                        </header>

                        <!-- Centered Question Body -->
                        <div class="flex-1 flex items-center justify-center px-6 sm:px-10 py-5 overflow-y-auto text-center">
                            <div
                                class="font-heading text-lg sm:text-xl font-bold leading-relaxed text-[var(--text)] break-words max-w-full"
                                v-html="renderMath(card.front)"
                            ></div>
                        </div>

                        <!-- Card Footer Hint -->
                        <footer class="flex items-center justify-between gap-2 border-t border-[var(--border)]/60 bg-[var(--surface2)]/40 px-5 sm:px-7 py-3 text-xs text-[var(--text-muted)]">
                            <span class="inline-flex items-center gap-1.5 font-medium">
                                <EyeIcon class="h-4 w-4 text-[var(--primary)]" />
                                <span>{{ t('flashcards.flip_hint') }}</span>
                            </span>
                            <kbd class="hidden sm:inline-flex items-center rounded-md border border-[var(--border)] bg-[var(--surface)] px-2 py-0.5 font-mono text-[10px] font-semibold">
                                Space
                            </kbd>
                        </footer>
                    </section>

                    <!-- ================= BACK FACE (ANSWER) ================= -->
                    <section
                        :aria-hidden="!flipped"
                        class="flashcard-face flashcard-back absolute inset-0 flex flex-col justify-between overflow-hidden rounded-3xl border-2 border-emerald-500/40 bg-gradient-to-br from-[var(--surface)] via-emerald-50/40 to-teal-50/50 dark:via-emerald-950/20 dark:to-teal-950/15 shadow-xl shadow-emerald-950/5"
                    >
                        <!-- Top Celebratory Emerald Accent Bar -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500"></div>

                        <!-- Card Header -->
                        <header class="flex items-center justify-between gap-3 px-5 sm:px-7 pt-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/15 px-3 py-1 text-[11px] font-bold tracking-wide text-emerald-700 dark:text-emerald-300">
                                <CheckCircleIcon class="h-3.5 w-3.5" />
                                <span>{{ locale === 'en' ? 'ANSWER' : 'সঠিক উত্তর' }}</span>
                            </span>

                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--text-muted)]">
                                <ArrowPathIcon class="h-3.5 w-3.5" />
                                <span>{{ locale === 'en' ? 'Tap to see question' : 'প্রশ্ন দেখতে ট্যাপ করুন' }}</span>
                            </span>
                        </header>

                        <!-- Centered Answer Body -->
                        <div class="flex-1 flex flex-col items-center justify-center px-6 sm:px-10 py-5 overflow-y-auto text-center">
                            <div
                                class="text-base sm:text-xl font-bold leading-relaxed text-emerald-950 dark:text-emerald-100 whitespace-pre-line break-words max-w-full"
                                v-html="renderMath(card.back)"
                            ></div>

                            <div
                                v-if="card.source_ref"
                                class="mt-4 inline-flex items-center gap-1.5 rounded-full border border-[var(--border)] bg-[var(--surface)]/80 px-3 py-1 text-[11px] font-medium text-[var(--text-muted)]"
                            >
                                <BookOpenIcon class="h-3.5 w-3.5 text-[var(--primary)]" />
                                <span v-if="card.source_ref.chapter">{{ card.source_ref.chapter }}</span>
                                <span v-if="card.source_ref.page">{{ card.source_ref.chapter ? ' · ' : '' }}{{ locale === 'en' ? 'Page' : 'পৃষ্ঠা' }} {{ card.source_ref.page }}</span>
                                <span v-if="!card.source_ref.chapter && !card.source_ref.page">Chunk {{ card.source_ref.chunk_id }}</span>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <footer class="flex items-center justify-between gap-2 border-t border-emerald-500/20 bg-emerald-500/5 px-5 sm:px-7 py-3 text-[11px] text-[var(--text-muted)]">
                            <span v-if="setTitle" class="truncate font-medium">{{ setTitle }}</span>
                            <span v-else>{{ locale === 'en' ? 'Rate your recall below' : 'নিচের বাটন থেকে আপনার প্রস্তুতি নির্বাচন করুন' }}</span>
                            <span class="hidden sm:inline font-mono">1 / 2</span>
                        </footer>
                    </section>
                </div>
            </button>
        </div>

        <!-- Tactile Control Actions Below Card -->
        <div class="mt-3 flex w-full max-w-xl items-center justify-center gap-3 min-h-[52px]">
            <button
                v-if="!flipped"
                type="button"
                @click="toggleFlip"
                class="inline-flex items-center justify-center gap-2 min-h-12 w-full sm:w-auto px-8 py-3 rounded-2xl border border-[var(--primary)]/30 bg-[var(--primary)]/10 text-[var(--primary)] text-sm font-bold shadow-xs transition-all duration-150 hover:bg-[var(--primary)] hover:text-white hover:shadow-md active:scale-[0.97] cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--primary)]"
            >
                <EyeIcon class="h-4.5 w-4.5" />
                <span>{{ locale === 'en' ? 'Reveal Answer' : 'উত্তর দেখুন' }}</span>
            </button>

            <template v-else>
                <button
                    type="button"
                    @click="review('review_again')"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 min-h-12 px-6 py-3 rounded-2xl border-2 border-amber-500/40 bg-amber-500/10 text-amber-700 dark:text-amber-300 text-sm font-bold transition-all duration-150 hover:bg-amber-500/20 hover:shadow-md active:scale-[0.97] cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500"
                >
                    <ArrowPathIcon class="h-4.5 w-4.5" />
                    <span>{{ t('flashcards.review_again') }}</span>
                    <kbd class="hidden sm:inline rounded bg-amber-500/15 px-1.5 py-0.5 font-mono text-[10px]">1</kbd>
                </button>
                <button
                    type="button"
                    @click="review('known')"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 min-h-12 px-7 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-md shadow-emerald-600/20 transition-all duration-150 hover:shadow-lg active:scale-[0.97] cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
                >
                    <CheckCircleIcon class="h-4.5 w-4.5" />
                    <span>{{ t('flashcards.known') }}</span>
                    <kbd class="hidden sm:inline rounded bg-white/20 px-1.5 py-0.5 font-mono text-[10px]">2</kbd>
                </button>
            </template>
        </div>
    </article>
</template>

<style scoped>
.flashcard-deck-root {
    container-type: inline-size;
}

.flashcard-stage {
    perspective: 1200px;
}

.flashcard-inner {
    transform-style: preserve-3d;
    transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
}

.flashcard-inner.is-flipped {
    transform: rotateY(180deg);
}

.flashcard-face {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.flashcard-back {
    transform: rotateY(180deg);
}

@media (prefers-reduced-motion: reduce) {
    .flashcard-inner {
        transition: none;
        transform-style: flat;
    }
    .flashcard-inner.is-flipped {
        transform: none;
    }
    .flashcard-front {
        transition: opacity 0.15s ease-out;
    }
    .flashcard-back {
        transform: none;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.15s ease-out;
    }
    .flashcard-inner.is-flipped .flashcard-front {
        opacity: 0;
        pointer-events: none;
    }
    .flashcard-inner.is-flipped .flashcard-back {
        opacity: 1;
        pointer-events: auto;
    }
}
</style>
