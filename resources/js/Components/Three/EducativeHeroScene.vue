<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    locale: { type: String, default: 'bn' },
});

const en = computed(() => props.locale === 'en');

const scenes = computed(() =>
    en.value
        ? [
              {
                  badge: 'SSC Biology · Chapter 3',
                  pageLeft: '64',
                  pageRight: '65',
                  bookTitle: 'Cell Division & Meiosis',
                  leftHeading: '3.2 Meiosis Cell Division',
                  leftPara1:
                      'In meiosis, a diploid (2n) mother cell divides twice in succession to produce four haploid (n) daughter cells.',
                  highlightText:
                      'Because chromosome numbers halve (2n → n), species maintain a constant chromosome count across generations.',
                  leftPara2:
                      'Crossing over during Prophase-I exchanges genetic segments between homologous chromosomes.',
                  diagramLabel: 'Fig 3.10: Reduction Division (2n → n)',
                  rightHeading: 'Key Comparison',
                  points: [
                      'Occurs in germ mother cells',
                      'Nucleus divides twice, chromosomes once',
                      'Creates genetic diversity in offspring',
                  ],
                  question: 'Explain why meiosis is called reduction division with an example.',
                  answer:
                      'In meiosis, the chromosome number in daughter cells becomes half (n) of the mother cell (2n). That is why it is called reduction division!',
                  followUp: 'Generate 3 MCQs from Page 64',
              },
              {
                  badge: 'HSC ICT · Chapter 3',
                  pageLeft: '112',
                  pageRight: '113',
                  bookTitle: 'Number Systems & Logic',
                  leftHeading: 'Binary to Decimal Conversion',
                  leftPara1:
                      'Every positional number system has a base or radix. Binary uses base 2 (0 and 1) while Decimal uses base 10.',
                  highlightText:
                      'To convert (1011)₂ to decimal, multiply each bit by 2 raised to its positional weight.',
                  leftPara2:
                      'Formula: (1×2³) + (0×2²) + (1×2¹) + (1×2⁰) = 8 + 0 + 2 + 1 = (11)₁₀.',
                  diagramLabel: 'Table 3.4: Positional Weights (2³ 2² 2¹ 2⁰)',
                  rightHeading: 'Quick Shortcut',
                  points: [
                      'Write series: 1, 2, 4, 8, 16...',
                      'Add weights where bit = 1',
                      '8 + 2 + 1 = 11 (Instant check!)',
                  ],
                  question: 'How do I quickly convert binary (1011)₂ to decimal?',
                  answer:
                      'Use the 8-4-2-1 weight trick! Since bits are 1-0-1-1, just add 8 + 0 + 2 + 1 = 11₁₀. Takes 3 seconds in exams!',
                  followUp: 'Practice 5 ICT Board Questions',
              },
              {
                  badge: 'BCS Preliminary Digest',
                  pageLeft: '28',
                  pageRight: '29',
                  bookTitle: 'Bangla Literature & Modern Era',
                  leftHeading: 'Rabindranath & Gitanjali',
                  leftPara1:
                      'Gitanjali was published in Bengali in 1910 (1317 BS). Its English translation "Song Offerings" appeared in 1912.',
                  highlightText:
                      'Irish poet W.B. Yeats wrote the introduction to Song Offerings, which earned the Nobel Prize in 1913.',
                  leftPara2:
                      'Frequently asked in BCS, Bank, and University Admission competitive exams.',
                  diagramLabel: 'Timeline: 1910 (BN) → 1912 (EN) → 1913 (Nobel)',
                  rightHeading: 'Exam Highlights',
                  points: [
                      'Total poems in Song Offerings: 103',
                      'Introduction by: W.B. Yeats',
                      'Published by: India Society, London',
                  ],
                  question: 'What are the most common BCS questions from this page?',
                  answer:
                      '3 top questions: 1) Who wrote the intro to Song Offerings? (W.B. Yeats) 2) Publication year? (1912) 3) Nobel year? (1913).',
                  followUp: 'Save to Smart Flashcards',
              },
          ]
        : [
              {
                  badge: 'এসএসসি জীববিজ্ঞান · অধ্যায় ৩',
                  pageLeft: '৬৪',
                  pageRight: '৬৫',
                  bookTitle: 'কোষ বিভাজন ও মিয়োসিস',
                  leftHeading: '৩.২ মিয়োসিস কোষ বিভাজন',
                  leftPara1:
                      'মিয়োসিস কোষ বিভাজনে একটি ডিপ্লয়েড (2n) মাতৃকোষ পরপর দুবার বিভাজিত হয়ে চারটি হ্যাপ্লয়েড (n) অপত্য কোষ সৃষ্টি করে।',
                  highlightText:
                      'ক্রোমোজোম সংখ্যা অর্ধেক (2n → n) হয়ে যায় বলে এই বিভাজনকে হ্রাসমূলক বিভাজন বলা হয়।',
                  leftPara2:
                      'প্রোফেজ-১ দশায় হোমোলোগাস ক্রোমোজোমের মধ্যে ক্রসিং ওভারের ফলে জিনের বিনিময় ঘটে।',
                  diagramLabel: 'চিত্র ৩.১০: মিয়োসিস বিভাজন (2n → n)',
                  rightHeading: 'গুরুত্বপূর্ণ সারসংক্ষেপ',
                  points: [
                      'জনন মাতৃকোষে সংঘটিত হয়',
                      'নিউক্লিয়াস দুবার ও ক্রোমোজোম একবার বিভাজিত হয়',
                      'প্রজাতির ক্রোমোজোম সংখ্যা ধ্রুব রাখে',
                  ],
                  question: 'মিয়োসিসকে কেন হ্রাসমূলক বিভাজন বলা হয়? সহজভাবে বুঝিয়ে দাও।',
                  answer:
                      'মিয়োসিসে মাতৃকোষের ডিপ্লয়েড (2n) ক্রোমোজোম অর্ধেক হয়ে অপত্য কোষে হ্যাপ্লয়েড (n) হয়। তাই একে হ্রাসমূলক বিভাজন বলে!',
                  followUp: 'পৃষ্ঠা ৬৪ থেকে ৩টি MCQ দিন',
              },
              {
                  badge: 'এইচএসসি আইসিটি · অধ্যায় ৩',
                  pageLeft: '১১২',
                  pageRight: '১১৩',
                  bookTitle: 'সংখ্যা পদ্ধতি ও রূপান্তর',
                  leftHeading: 'বাইনারি থেকে ডেসিমেল রূপান্তর',
                  leftPara1:
                      'পজিশনাল সংখ্যা পদ্ধতিতে প্রতিটি অঙ্কের স্থানীয় মান থাকে। বাইনারির ভিত্তি ২ (০ ও ১) এবং ডেসিমেলের ভিত্তি ১০।',
                  highlightText:
                      '(1011)₂ কে ডেসিমেলে নিতে প্রতিটি বিটকে তার স্থানীয় মান (2ⁿ) দিয়ে গুণ করে যোগ করতে হয়।',
                  leftPara2:
                      'সমাধান: (1×2³) + (0×2²) + (1×2¹) + (1×2⁰) = 8 + 0 + 2 + 1 = (11)₁₀।',
                  diagramLabel: 'ছক ৩.৪: স্থানীয় মান (৮ - ৪ - ২ - ১ পদ্ধতি)',
                  rightHeading: 'পরীক্ষার শর্টকাট কৌশল',
                  points: [
                      'ডান থেকে ১, ২, ৪, ৮ ক্রম লিখুন',
                      'যেসব বিটে 1 আছে সেগুলোর মান যোগ করুন',
                      '৮ + ২ + ১ = ১১ (মাত্র ৩ সেকেন্ডে উত্তর!)',
                  ],
                  question: 'বাইনারি (1011)₂ থেকে ডেসিমেল করার শর্টকাট নিয়মটি কী?',
                  answer:
                      '৮-৪-২-১ ট্রিক ব্যবহার করুন! ১০১১-তে ১ আছে ৮, ২ এবং ১-এর ঘরে। তাই ৮ + ২ + ১ = ১১। এমসিকিউতে মুহূর্তেই উত্তর বের হবে!',
                  followUp: 'বোর্ড প্রশ্নের কুইজ দিন',
              },
              {
                  badge: 'বিসিএস প্রিলিমিনারি ডাইজেস্ট',
                  pageLeft: '২৮',
                  pageRight: '২৯',
                  bookTitle: 'বাংলা ভাষা ও সাহিত্য',
                  leftHeading: 'রবীন্দ্রনাথ ঠাকুর ও গীতাঞ্জলি',
                  leftPara1:
                      'গীতাঞ্জলি কাব্যগ্রন্থটি ১৯১০ সালে (১৩১৭ বঙ্গাব্দ) প্রকাশিত হয়। এর ইংরেজি অনুবাদ "Song Offerings" ১৯১২ সালে প্রকাশিত হয়।',
                  highlightText:
                      'আইরিশ কবি W.B. Yeats "Song Offerings"-এর ভূমিকা লেখেন এবং ১৯১৩ সালে রবীন্দ্রনাথ নোবেল পুরস্কার পান।',
                  leftPara2:
                      'বিসিএস প্রিলিমিনারি, ব্যাংক ও বিশ্ববিদ্যালয় ভর্তি পরীক্ষায় এখান থেকে নিয়মিত প্রশ্ন আসে।',
                  diagramLabel: 'কালানুক্রম: ১৯১০ (বাংলা) → ১৯১২ (ইংরেজি) → ১৯১৩ (নোবেল)',
                  rightHeading: 'একনজরে পরীক্ষার তথ্য',
                  points: [
                      'Song Offerings-এ মোট কবিতা: ১০৩টি',
                      'ভূমিকা লেখেন: ডব্লিউ. বি. ইয়েটস',
                      'প্রকাশক: ইন্ডিয়া সোসাইটি অব লন্ডন',
                  ],
                  question: 'এই পৃষ্ঠা থেকে বিসিএস পরীক্ষায় কোন প্রশ্নগুলো সবচেয়ে বেশি আসে?',
                  answer:
                      'সবচেয়ে বেশি আসা ৩টি তথ্য: ১) Song Offerings-এর ভূমিকা কার লেখা? (W.B. Yeats) ২) প্রকাশকাল? (১৯১২) ৩) নোবেল জয়ের বছর? (১৯১৩)।',
                  followUp: 'ফ্ল্যাশকার্ডে সেভ করুন',
              },
          ]
);

const activeIndex = ref(0);
// Phase: 0 = user question appear, 1 = AI typing dots, 2 = AI full answer + highlight pulse
const chatPhase = ref(2);
const isFlipping = ref(false);

const currentScene = computed(() => scenes.value[activeIndex.value] || scenes.value[0]);

let cycleTimer = null;
let phaseTimers = [];

function clearPhaseTimers() {
    phaseTimers.forEach((id) => clearTimeout(id));
    phaseTimers = [];
}

function runSceneSequence(nextIndex = null) {
    clearPhaseTimers();
    if (nextIndex !== null && nextIndex !== activeIndex.value) {
        isFlipping.value = true;
        phaseTimers.push(
            setTimeout(() => {
                activeIndex.value = nextIndex;
                isFlipping.value = false;
            }, 240)
        );
    }

    chatPhase.value = 0;
    phaseTimers.push(
        setTimeout(() => {
            chatPhase.value = 1;
        }, 700)
    );
    phaseTimers.push(
        setTimeout(() => {
            chatPhase.value = 2;
        }, 1650)
    );
}

function selectTopic(idx) {
    if (idx === activeIndex.value && chatPhase.value === 2) return;
    runSceneSequence(idx);
    restartAutoCycle();
}

function restartAutoCycle() {
    if (cycleTimer) clearInterval(cycleTimer);
    cycleTimer = setInterval(() => {
        const next = (activeIndex.value + 1) % scenes.value.length;
        runSceneSequence(next);
    }, 6200);
}

watch(
    () => props.locale,
    () => {
        chatPhase.value = 2;
    }
);

onMounted(() => {
    runSceneSequence(0);
    restartAutoCycle();
});

onUnmounted(() => {
    clearPhaseTimers();
    if (cycleTimer) clearInterval(cycleTimer);
});
</script>

<template>
    <div
        class="hero-book-chat relative w-full h-[420px] sm:h-[460px] flex flex-col justify-center select-none"
        aria-label="Interactive Open Book and AI Tutor Conversation"
    >
        <!-- Warm Ambient Backdrop Glow -->
        <div class="pointer-events-none absolute inset-0 -z-10 flex items-center justify-center">
            <div
                class="w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-[radial-gradient(circle,rgba(183,139,82,0.22),transparent_70%)] blur-2xl"
            ></div>
        </div>

        <!-- Top Topic Switcher Pills -->
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2 px-1">
            <div class="inline-flex items-center gap-1.5 rounded-full border border-[var(--border)] bg-[var(--surface)]/95 px-3 py-1 text-[11px] font-semibold text-[var(--text)] shadow-xs">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ currentScene.badge }}</span>
            </div>

            <div class="flex items-center gap-1">
                <button
                    v-for="(s, idx) in scenes"
                    :key="idx"
                    type="button"
                    @click="selectTopic(idx)"
                    class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                    :class="
                        activeIndex === idx
                            ? 'w-6 bg-[#8a5a32] dark:bg-[#d2ad7c]'
                            : 'w-2 bg-[var(--border)] hover:bg-[#8a5a32]/50'
                    "
                    :aria-label="`Scene ${idx + 1}`"
                ></button>
            </div>
        </div>

        <!-- OPEN 2-PAGE BOOK SPREAD -->
        <div
            class="book-spread-shell relative mx-auto w-full max-w-[540px] rounded-2xl border border-[#cbb69b]/70 dark:border-white/15 bg-[#4a3520] p-1.5 sm:p-2 shadow-2xl transition-transform duration-300"
            :class="{ 'scale-[0.985]': isFlipping }"
        >
            <div
                class="relative grid grid-cols-2 overflow-hidden rounded-xl bg-[#fcf9f2] dark:bg-[#1b1a17] text-[#231f1a] dark:text-[#f1ede4] min-h-[250px] sm:min-h-[275px]"
            >
                <!-- Center Book Spine Crease & Ribbon -->
                <div
                    class="pointer-events-none absolute inset-y-0 left-1/2 -translate-x-1/2 w-5 z-20 bg-gradient-to-r from-black/10 via-black/25 to-black/10 dark:from-black/30 dark:via-black/60 dark:to-black/30"
                ></div>
                <div
                    class="pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-1.5 h-8 z-20 bg-[#9b4d22] rounded-b-sm shadow"
                ></div>

                <!-- LEFT PAGE -->
                <div class="relative z-10 flex flex-col justify-between p-3 sm:p-4 pr-3.5 sm:pr-5 border-r border-black/5 dark:border-white/5">
                    <div>
                        <div class="flex items-center justify-between text-[9px] sm:text-[10px] font-mono text-[#7c6f5b] dark:text-[#a39887] border-b border-[#e6decb] dark:border-white/10 pb-1">
                            <span class="truncate">{{ currentScene.bookTitle }}</span>
                            <span>{{ en ? 'p.' : 'পৃ.' }} {{ currentScene.pageLeft }}</span>
                        </div>

                        <h4 class="mt-2 font-serif text-xs sm:text-sm font-bold text-[#8a5a32] dark:text-[#d2ad7c] leading-snug">
                            {{ currentScene.leftHeading }}
                        </h4>

                        <p class="mt-1.5 text-[10px] sm:text-[11px] leading-relaxed text-[#3d372e] dark:text-[#d6d0c4] line-clamp-3">
                            {{ currentScene.leftPara1 }}
                        </p>

                        <!-- Animated AI-Highlighted Passage -->
                        <div
                            class="mt-2 rounded-lg border border-amber-500/40 bg-amber-300/25 dark:bg-amber-500/15 px-2 py-1.5 text-[10px] sm:text-[11px] font-medium leading-snug text-[#2b2113] dark:text-amber-100 transition-all duration-500"
                            :class="chatPhase === 2 ? 'ring-2 ring-amber-500/40 shadow-xs' : 'opacity-85'"
                        >
                            <span class="inline-block mr-1 text-[9px] font-bold uppercase tracking-wider text-[#8a5a32] dark:text-amber-300">
                                ✦ AI Focus:
                            </span>
                            {{ currentScene.highlightText }}
                        </div>
                    </div>

                    <!-- Mini Diagram / Formula Box on Left Page -->
                    <div class="mt-2.5 rounded-lg border border-dashed border-[#d4c7b0] dark:border-white/15 bg-[#f4efe4] dark:bg-white/5 px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded bg-[#8a5a32]/15 text-[#8a5a32] dark:text-[#d2ad7c] text-[10px] font-bold">
                                2n
                            </span>
                            <span class="truncate text-[9.5px] sm:text-[10px] font-semibold text-[#5c5142] dark:text-[#c7bfb0]">
                                {{ currentScene.diagramLabel }}
                            </span>
                        </div>
                        <span class="text-[9px] font-mono text-[#8a5a32] dark:text-[#d2ad7c] shrink-0">→ n</span>
                    </div>
                </div>

                <!-- RIGHT PAGE -->
                <div class="relative z-10 flex flex-col justify-between p-3 sm:p-4 pl-3.5 sm:pl-5">
                    <div>
                        <div class="flex items-center justify-between text-[9px] sm:text-[10px] font-mono text-[#7c6f5b] dark:text-[#a39887] border-b border-[#e6decb] dark:border-white/10 pb-1">
                            <span>sikhun.com</span>
                            <span>{{ en ? 'p.' : 'পৃ.' }} {{ currentScene.pageRight }}</span>
                        </div>

                        <p class="mt-2 text-[10px] sm:text-[11px] leading-relaxed text-[#3d372e] dark:text-[#d6d0c4] line-clamp-2">
                            {{ currentScene.leftPara2 }}
                        </p>

                        <div class="mt-2.5 rounded-lg bg-[#f4efe4] dark:bg-white/5 p-2 sm:p-2.5 border border-[#e5dcc8] dark:border-white/10">
                            <div class="text-[10px] font-bold text-[#8a5a32] dark:text-[#d2ad7c]">
                                {{ currentScene.rightHeading }}
                            </div>
                            <ul class="mt-1 space-y-1">
                                <li
                                    v-for="(pt, idx) in currentScene.points"
                                    :key="idx"
                                    class="flex items-start gap-1.5 text-[9.5px] sm:text-[10px] text-[#453e34] dark:text-[#cfc8ba]"
                                >
                                    <span class="text-[#8a5a32] dark:text-[#d2ad7c] font-bold">•</span>
                                    <span class="line-clamp-1">{{ pt }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-2 flex items-center justify-between text-[9px] text-[#8a5a32] dark:text-[#d2ad7c] font-semibold">
                        <span>{{ en ? 'Ask AI about this page' : 'এই পৃষ্ঠা নিয়ে AI-কে প্রশ্ন করুন' }}</span>
                        <span>↘</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FLOATING AI CHAT CONVERSATION CARD OVERLAY -->
        <div
            class="ai-chat-card relative z-30 -mt-12 sm:-mt-14 ml-auto mr-1 sm:mr-2 w-[92%] sm:w-[82%] rounded-2xl border border-[var(--border)] bg-[var(--surface)]/95 backdrop-blur-md p-3 sm:p-3.5 shadow-xl"
        >
            <!-- Chat Header -->
            <div class="flex items-center justify-between border-b border-[var(--border)] pb-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-[#8a5a32] text-white text-xs font-bold shadow-xs">
                        ✦
                    </span>
                    <div>
                        <div class="text-[11px] sm:text-xs font-bold text-[var(--text)] leading-none">
                            {{ en ? 'Sikhun AI Page Tutor' : 'শিখুন AI পৃষ্ঠা টিউটর' }}
                        </div>
                        <div class="mt-0.5 text-[9.5px] text-[var(--text-muted)]">
                            {{ en ? `Reading Page ${currentScene.pageLeft} · Context Active` : `পৃষ্ঠা ${currentScene.pageLeft} থেকে সরাসরি ব্যাখ্যা` }}
                        </div>
                    </div>
                </div>
                <span class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-[9.5px] font-bold text-emerald-600 dark:text-emerald-400">
                    {{ en ? 'Live AI' : 'সক্রিয়' }}
                </span>
            </div>

            <!-- Conversation Body -->
            <div class="mt-2.5 space-y-2 text-[11px] sm:text-xs">
                <!-- Student Question Bubble -->
                <div class="flex justify-end">
                    <div class="max-w-[88%] rounded-2xl rounded-br-xs bg-[#8a5a32] text-white px-3 py-1.5 text-[10.5px] sm:text-[11.5px] font-medium shadow-xs">
                        {{ currentScene.question }}
                    </div>
                </div>

                <!-- AI Typing Indicator OR AI Response Bubble -->
                <div class="flex items-start gap-2">
                    <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#8a5a32]/15 text-[#8a5a32] dark:text-[#d2ad7c] text-[10px] font-bold">
                        AI
                    </span>

                    <div
                        v-if="chatPhase < 2"
                        class="inline-flex items-center gap-1.5 rounded-2xl rounded-bl-xs border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-[10.5px] text-[var(--text-muted)]"
                    >
                        <span class="h-1.5 w-1.5 rounded-full bg-[#8a5a32] animate-bounce"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-[#8a5a32] animate-bounce [animation-delay:150ms]"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-[#8a5a32] animate-bounce [animation-delay:300ms]"></span>
                        <span class="ml-1 text-[10px]">
                            {{ en ? 'Reading page & explaining...' : 'পৃষ্ঠা বিশ্লেষণ করে উত্তর লিখছে...' }}
                        </span>
                    </div>

                    <div
                        v-else
                        class="flex-1 rounded-2xl rounded-bl-xs border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-[10.5px] sm:text-[11.5px] leading-relaxed text-[var(--text)] transition-all duration-300"
                    >
                        <p>{{ currentScene.answer }}</p>
                        <div class="mt-1.5 flex flex-wrap items-center justify-between gap-2 border-t border-[var(--border)] pt-1.5">
                            <span class="inline-flex items-center gap-1 text-[9.5px] font-semibold text-[#8a5a32] dark:text-[#d2ad7c]">
                                ✦ {{ currentScene.followUp }}
                            </span>
                            <button
                                type="button"
                                @click="selectTopic((activeIndex + 1) % scenes.length)"
                                class="text-[9.5px] font-bold text-[var(--text-muted)] hover:text-[var(--text)] cursor-pointer"
                            >
                                {{ en ? 'Next example →' : 'পরের উদাহরণ →' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.book-spread-shell {
    box-shadow:
        0 20px 40px -15px rgba(24, 18, 10, 0.28),
        0 4px 0 0 #d9cbb3,
        0 7px 0 0 #2b1e12;
}
.ai-chat-card {
    box-shadow: 0 18px 38px -12px rgba(15, 23, 42, 0.22);
}
</style>
