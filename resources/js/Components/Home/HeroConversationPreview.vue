<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    locale: { type: String, default: 'bn' },
    books: { type: Array, default: () => [] },
});

const en = computed(() => props.locale === 'en');

const fallbackSlides = computed(() =>
    en.value
        ? [
              {
                  slug: null,
                  bookTitle: 'Physics 1st Paper',
                  level: 'HSC',
                  topic: 'Physics · Newton’s Laws',
                  question: 'Explain Newton’s third law with an everyday example.',
                  answer: 'When you push against a wall, it pushes back on you with an equal force in the opposite direction. Forces act in pairs.',
                  followUp: 'Give me a question to try',
              },
              {
                  slug: null,
                  bookTitle: 'Organic Chemistry',
                  level: 'ADMISSION',
                  topic: 'Chemistry · Benzene Ring',
                  question: 'Why does benzene prefer substitution over addition?',
                  answer: 'Delocalized pi-electrons make the aromatic ring extra stable; substitution preserves aromaticity while addition breaks it.',
                  followUp: 'Create 5 flashcards from this',
              },
              {
                  slug: null,
                  bookTitle: 'Biology & Genetics',
                  level: 'MEDICAL',
                  topic: 'Biology · DNA Replication',
                  question: 'What role does DNA helicase play during replication?',
                  answer: 'Helicase unwinds the double helix by breaking hydrogen bonds between complementary base pairs at the replication fork.',
                  followUp: 'Test me with a quick MCQ',
              },
              {
                  slug: null,
                  bookTitle: 'Higher Mathematics',
                  level: 'BUET',
                  topic: 'Calculus · Chain Rule',
                  question: 'How do I differentiate sin(3x² + 1) step by step?',
                  answer: 'Differentiate the outer sin(u) to cos(u), then multiply by du/dx = 6x. Final derivative: 6x·cos(3x² + 1).',
                  followUp: 'Show a practice problem',
              },
              {
                  slug: null,
                  bookTitle: 'BCS English & GK',
                  level: 'BCS / JOB',
                  topic: 'Grammar · Conditional Clauses',
                  question: 'When do we use "Had I known" in third conditionals?',
                  answer: 'It is an inverted form of "If I had known", paired with "would have + past participle" for unreal past situations.',
                  followUp: 'Give a BCS past question',
              },
          ]
        : [
              {
                  slug: null,
                  bookTitle: 'পদার্থবিজ্ঞান ১ম পত্র',
                  level: 'HSC',
                  topic: 'পদার্থবিজ্ঞান · নিউটনের সূত্র',
                  question: 'দৈনন্দিন উদাহরণ দিয়ে নিউটনের তৃতীয় সূত্র বুঝিয়ে বলো।',
                  answer: 'আপনি যখন দেয়ালকে ধাক্কা দেন, দেয়ালও সমান বল দিয়ে বিপরীত দিকে আপনাকে ধাক্কা দেয়। বল সবসময় জোড়ায় কাজ করে।',
                  followUp: 'আমাকে অনুশীলনের জন্য একটি প্রশ্ন দাও',
              },
              {
                  slug: null,
                  bookTitle: 'জৈব রসায়ন',
                  level: 'ভর্তি',
                  topic: 'রসায়ন · বেনজিন বলয়',
                  question: 'বেনজিন কেন সংযোজন বিক্রিয়ার চেয়ে প্রতিস্থাপন বিক্রিয়া পছন্দ করে?',
                  answer: 'সঞ্চারণশীল পাই-ইলেকট্রন অ্যারোমেটিক বলয়কে স্থিতিশীল রাখে; প্রতিস্থাপনে অ্যারোমেটিসিটি অটুট থাকে।',
                  followUp: 'এই অধ্যায় থেকে ফ্ল্যাশকার্ড তৈরি করো',
              },
              {
                  slug: null,
                  bookTitle: 'জীববিজ্ঞান ও জিনতত্ত্ব',
                  level: 'মেডিকেল',
                  topic: 'জীববিজ্ঞান · DNA প্রতিলিপন',
                  question: 'DNA প্রতিলিপনে হেলিকেজ এনজাইমের কাজ কী?',
                  answer: 'হেলিকেজ হাইড্রোজেন বন্ধন ভেঙে ডাবল হেলিক্সের দুটি সূত্রককে আলাদা করে প্রতিলিপন ফর্ক তৈরি করে।',
                  followUp: 'একটি দ্রুত MCQ টেস্ট দাও',
              },
              {
                  slug: null,
                  bookTitle: 'উচ্চতর গণিত',
                  level: 'BUET',
                  topic: 'ক্যালকুলাস · চেইন রুল',
                  question: 'sin(3x² + 1) এর অন্তরীকরণ ধাপে ধাপে কীভাবে করব?',
                  answer: 'প্রথমে sin(u) থেকে cos(u), তারপর ভেতরের 3x² + 1 থেকে 6x গুণ করুন। উত্তর: 6x·cos(3x² + 1)।',
                  followUp: 'অনুশীলনের আরেকটি অঙ্ক দাও',
              },
              {
                  slug: null,
                  bookTitle: 'বিসিএস ইংরেজি ও সাধারণ জ্ঞান',
                  level: 'BCS / চাকরি',
                  topic: 'গ্রামার · Conditional',
                  question: 'Third conditional-এ "Had I known" কখন ব্যবহার হয়?',
                  answer: 'এটি "If I had known"-এর সংক্ষিপ্ত রূপ, যার পরের অংশে "would have + V3" বসে।',
                  followUp: 'বিগত বিসিএস-এর প্রশ্ন দাও',
              },
          ],
);

const bookSlides = computed(() => {
    const base = fallbackSlides.value;
    const realBooks = (props.books || []).slice(0, 5);
    return base.map((slide, idx) => {
        const b = realBooks[idx];
        if (!b) return slide;
        return {
            ...slide,
            slug: b.slug,
            cover: b.cover_image_url || null,
            bookTitle: b.title || slide.bookTitle,
            level: b.level || slide.level,
        };
    });
});

const activeIndex = ref(0);
const step = ref(3); // 1: user question, 2: typing, 3: AI reply, 4: follow-up
let timer = null;

const currentSlide = computed(() => bookSlides.value[activeIndex.value] || bookSlides.value[0]);

function advanceAnimation() {
    if (step.value < 4) {
        step.value += 1;
    } else {
        step.value = 1;
        activeIndex.value = (activeIndex.value + 1) % bookSlides.value.length;
    }
}

function selectBook(idx) {
    activeIndex.value = idx;
    step.value = 3;
    restartTimer();
}

function restartTimer() {
    if (timer) clearInterval(timer);
    timer = setInterval(advanceAnimation, 2100);
}

watch(
    () => props.locale,
    () => {
        step.value = 3;
    },
);

onMounted(() => {
    step.value = 1;
    restartTimer();
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<template>
    <div class="relative">
        <div class="pointer-events-none absolute -inset-4 rounded-[2rem] bg-gradient-to-br from-[#f97316]/12 via-transparent to-[#8a5a32]/15 blur-2xl"></div>

        <div class="relative rounded-2xl border border-[var(--border)] bg-[var(--surface)]/95 p-3.5 sm:p-4 shadow-[var(--shadow-lg)] backdrop-blur-xl">
            <!-- Terminal Header -->
            <div class="flex items-center justify-between rounded-xl bg-[var(--surface2)] px-3.5 py-2.5 border border-[var(--border)]">
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-[#ff6b6b]"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-[#ffb02e]"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-[#10b981]"></span>
                    <span class="ml-2 font-mono text-[10px] sm:text-xs text-[var(--text-muted)]">sikhun.ai / study-room</span>
                </div>
                <span class="rounded-full bg-emerald-500/15 px-2.5 py-0.5 font-mono text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                    {{ en ? 'Study preview' : 'নমুনা পাঠ' }}
                </span>
            </div>

            <!-- 5 Books Interactive Selector Strip -->
            <div class="mt-3 grid grid-cols-5 gap-1.5">
                <button
                    v-for="(item, idx) in bookSlides"
                    :key="idx"
                    type="button"
                    @click="selectBook(idx)"
                    :class="[
                        'group flex flex-col items-center rounded-xl border p-1.5 text-center transition-all duration-300',
                        activeIndex === idx
                            ? 'border-[var(--home-accent)] bg-[var(--home-accent)]/12 -translate-y-0.5 shadow-sm'
                            : 'border-[var(--border)] bg-[var(--surface2)]/60 opacity-75 hover:opacity-100'
                    ]"
                >
                    <div class="relative h-10 w-8 overflow-hidden rounded-md bg-[var(--surface2)] border border-[var(--border)] flex items-center justify-center">
                        <img v-if="item.cover" :src="item.cover" :alt="item.bookTitle" class="h-full w-full object-cover" />
                        <span v-else class="material-symbols-outlined !text-base text-[var(--home-accent)]">menu_book</span>
                    </div>
                    <span class="mt-1 line-clamp-1 w-full text-[9px] font-semibold text-[var(--text)]">
                        {{ item.bookTitle }}
                    </span>
                </button>
            </div>

            <!-- Animated Conversation Body -->
            <div class="mt-3 rounded-xl border border-[var(--border)] bg-[var(--bg)]/85 p-3.5 sm:p-4 min-h-[265px] flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-[var(--border)] pb-2.5">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[var(--home-accent)]/15 text-[var(--home-accent)]">
                                <span class="material-symbols-outlined !text-lg">psychology</span>
                            </span>
                            <div class="min-w-0">
                                <div class="truncate text-xs font-semibold text-[var(--text)]">{{ currentSlide.topic }}</div>
                                <div class="mt-0.5 text-[9px] text-[var(--text-muted)]">
                                    {{ en ? 'A sample study conversation' : 'নমুনা পড়াশোনার কথোপকথন' }}
                                </div>
                            </div>
                        </div>
                        <Link
                            v-if="currentSlide.slug"
                            :href="`/library/${currentSlide.slug}`"
                            class="shrink-0 rounded bg-[var(--surface2)] px-2 py-1 font-mono text-[9px] font-bold text-[var(--home-accent)] hover:underline"
                        >
                            {{ currentSlide.level }} ↗
                        </Link>
                        <span v-else class="shrink-0 rounded bg-[var(--surface2)] px-2 py-1 font-mono text-[9px] font-bold text-[var(--home-accent)]">
                            {{ currentSlide.level }}
                        </span>
                    </div>

                    <!-- Step 1: Student Question -->
                    <div class="mt-3.5 flex justify-end transition-all duration-300" :class="step >= 1 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
                        <div class="max-w-[88%] rounded-2xl rounded-tr-sm bg-[var(--surface2)] border border-[var(--border)] px-3.5 py-2 text-xs leading-relaxed text-[var(--text)]">
                            {{ currentSlide.question }}
                        </div>
                    </div>

                    <!-- Step 2: Typing Indicator -->
                    <div v-if="step === 2" class="mt-3 inline-flex items-center gap-1.5 rounded-2xl rounded-tl-sm bg-[var(--surface)] border border-[var(--border)] px-3.5 py-2.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-[var(--home-accent)] animate-bounce"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-[var(--home-accent)] animate-bounce [animation-delay:150ms]"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-[var(--home-accent)] animate-bounce [animation-delay:300ms]"></span>
                    </div>

                    <!-- Step 3+: AI Reply -->
                    <div v-if="step >= 3" class="mt-3 max-w-[94%] rounded-2xl rounded-tl-sm bg-[var(--surface)] border border-[var(--border)] p-3 shadow-sm transition-all duration-300">
                        <div class="mb-1.5 flex items-center gap-1.5 text-[11px] font-semibold text-[var(--home-accent)]">
                            <span class="material-symbols-outlined !text-sm">auto_awesome</span>
                            <span>{{ en ? 'A simple explanation' : 'সহজ ব্যাখ্যা' }}</span>
                        </div>
                        <p class="text-[11px] leading-5 text-[var(--text-muted)]">{{ currentSlide.answer }}</p>
                        <div class="mt-2.5 flex flex-wrap gap-1.5">
                            <span class="rounded-md bg-[var(--surface2)] px-2 py-1 text-[9px] font-medium text-[var(--text)]">
                                {{ en ? '+ Practice questions' : '+ অনুশীলনের প্রশ্ন' }}
                            </span>
                            <span class="rounded-md bg-[var(--surface2)] px-2 py-1 text-[9px] font-medium text-[var(--home-accent)]">
                                {{ en ? '+ Make flashcards' : '+ ফ্ল্যাশকার্ড তৈরি' }}
                            </span>
                        </div>
                    </div>

                    <!-- Step 4: Follow-up -->
                    <div v-if="step >= 4" class="mt-2.5 flex justify-end transition-all duration-300">
                        <div class="rounded-2xl rounded-tr-sm bg-[var(--home-accent)]/15 px-3 py-1.5 text-[11px] font-medium text-[var(--home-accent)]">
                            {{ currentSlide.followUp }}
                        </div>
                    </div>
                </div>

                <!-- Bottom Input Mock -->
                <div class="mt-3.5 flex items-center gap-2 rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-[10px] text-[var(--text-muted)]">
                    <span class="material-symbols-outlined !text-sm text-[var(--home-accent)]">edit</span>
                    <span class="truncate">{{ en ? 'Ask about the lesson you are studying…' : 'আপনার পড়া বিষয় নিয়ে প্রশ্ন করুন…' }}</span>
                    <span class="material-symbols-outlined ml-auto !text-sm text-[var(--home-accent)]">arrow_upward</span>
                </div>
            </div>
        </div>
    </div>
</template>
