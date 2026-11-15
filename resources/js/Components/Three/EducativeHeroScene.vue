<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    locale: { type: String, default: 'bn' },
});

const en = computed(() => props.locale === 'en');

const bookPool = computed(() =>
    en.value
        ? [
              {
                  badge: 'SSC Biology · Chapter 3',
                  bookTitle: 'SSC Biology (NCTB)',
                  pageNo: '64',
                  heading: '3.2 Meiosis Cell Division',
                  para1:
                      'In meiosis, a diploid (2n) germ mother cell divides twice in succession to produce four haploid (n) daughter cells.',
                  highlight:
                      'Because chromosome numbers halve (2n → n), species maintain a constant chromosome count across generations.',
                  para2:
                      'During Prophase-I, crossing over between homologous chromosomes exchanges genes and creates genetic variation.',
                  diagramTitle: 'Fig 3.10: Reduction Division',
                  diagramFormula: 'Mother Cell (2n) → 4 Gametes (n)',
                  turns: [
                      {
                          q: 'Why is meiosis called reduction division?',
                          a: 'Because the diploid (2n) chromosome set in the mother cell is reduced to half (n) in the four daughter cells.',
                      },
                      {
                          q: 'Where does crossing over take place on this page?',
                          a: 'In Prophase-I between non-sister chromatids of homologous chromosomes, creating genetic diversity.',
                      },
                  ],
              },
              {
                  badge: 'HSC ICT · Chapter 3',
                  bookTitle: 'Information & Communication Technology',
                  pageNo: '112',
                  heading: 'Binary to Decimal Conversion',
                  para1:
                      'Every positional number system has a base or radix. Binary uses base 2 (0, 1) while Decimal uses base 10 (0–9).',
                  highlight:
                      'To convert (1011)₂ to decimal, multiply each bit by 2 raised to its positional weight and sum the values.',
                  para2:
                      'Calculation: (1×2³) + (0×2²) + (1×2¹) + (1×2⁰) = 8 + 0 + 2 + 1 = (11)₁₀.',
                  diagramTitle: 'Table 3.4: 8-4-2-1 Weight Trick',
                  diagramFormula: 'Bits: 1  0  1  1  →  8 + 0 + 2 + 1 = 11₁₀',
                  turns: [
                      {
                          q: 'How can I convert (1011)₂ to decimal in 3 seconds?',
                          a: 'Write weights 8-4-2-1 under 1-0-1-1 and add the active bits: 8 + 2 + 1 = 11₁₀!',
                      },
                      {
                          q: 'What if there is a fractional binary like (.101)₂?',
                          a: 'Multiply bits after the point by negative powers: 2⁻¹ (0.5) + 0 + 2⁻³ (0.125) = 0.625₁₀.',
                      },
                  ],
              },
              {
                  badge: 'BCS Preliminary Digest · Bangla',
                  bookTitle: 'BCS Preliminary Digest (2025)',
                  pageNo: '28',
                  heading: 'Rabindranath Tagore & Gitanjali',
                  para1:
                      'Gitanjali was originally published in Bengali in 1910 (1317 BS). Its English translation "Song Offerings" appeared in 1912.',
                  highlight:
                      'Irish poet W.B. Yeats wrote the introduction to Song Offerings, which earned the Nobel Prize in Literature in 1913.',
                  para2:
                      'Song Offerings contains 103 poems translated by Tagore himself and was published by the India Society of London.',
                  diagramTitle: 'Exam Timeline Summary',
                  diagramFormula: '1910 (Gitanjali) → 1912 (EN) → 1913 (Nobel)',
                  turns: [
                      {
                          q: 'Who wrote the introduction to Song Offerings?',
                          a: 'Irish poet W.B. Yeats wrote the famous introduction to the 1912 London edition.',
                      },
                      {
                          q: 'Which BCS questions come most often from this page?',
                          a: '1) Publication year of Gitanjali (1910), 2) Intro author (W.B. Yeats), and 3) Total poems in Song Offerings (103).',
                      },
                  ],
              },
              {
                  badge: 'HSC Physics 1st Paper · Chapter 2',
                  bookTitle: 'HSC Physics 1st Paper',
                  pageNo: '45',
                  heading: '2.4 River-Boat Vector Crossing',
                  para1:
                      'When a boat crosses a river of width d with velocity v at angle α to the current u, two components act simultaneously.',
                  highlight:
                      'To cross in minimum time, the boat must be steered perpendicular to the bank (α = 90°), so t_min = d / v.',
                  para2:
                      'For shortest path (zero drift), steer at cos α = -u / v so the resultant velocity is straight across.',
                  diagramTitle: 'Eq 2.18: Crossing Time Formula',
                  diagramFormula: 't = d / (v sin α)  |  t_min = d / v (at α = 90°)',
                  turns: [
                      {
                          q: 'At what angle should the boat row for minimum time?',
                          a: 'At α = 90° (straight across relative to water), because sin 90° = 1 makes t = d / v minimum.',
                      },
                      {
                          q: 'Does river current speed u affect minimum crossing time?',
                          a: 'No! Current u only pushes the boat downstream (drift), while vertical speed v sin α determines crossing time.',
                      },
                  ],
              },
              {
                  badge: 'SSC Higher Math · Chapter 8',
                  bookTitle: 'SSC Higher Mathematics',
                  pageNo: '176',
                  heading: '8.2 Trigonometric Identities',
                  para1:
                      'For any acute angle θ in a right-angled triangle, the ratios of sides obey three Pythagorean identities.',
                  highlight:
                      'Fundamental identity: sin²θ + cos²θ = 1, from which sec²θ − tan²θ = 1 and cosec²θ − cot²θ = 1 are derived.',
                  para2:
                      'If sec θ + tan θ = k, then since (sec θ + tan θ)(sec θ − tan θ) = 1, we immediately get sec θ − tan θ = 1 / k.',
                  diagramTitle: 'Reciprocal Pair Rule',
                  diagramFormula: 'sec θ + tan θ = k  ⇒  sec θ − tan θ = 1/k',
                  turns: [
                      {
                          q: 'If sec θ + tan θ = 3, what is sec θ − tan θ?',
                          a: 'Using sec²θ − tan²θ = 1, (sec θ − tan θ) is simply the reciprocal: 1/3!',
                      },
                      {
                          q: 'How do I find sec θ from those two equations?',
                          a: 'Add both equations: 2 sec θ = 3 + 1/3 = 10/3, so sec θ = 5/3.',
                      },
                  ],
              },
          ]
        : [
              {
                  badge: 'এসএসসি জীববিজ্ঞান · অধ্যায় ৩',
                  bookTitle: 'জীববিজ্ঞান (নবম-দশম শ্রেণি)',
                  pageNo: '৬৪',
                  heading: '৩.২ মিয়োসিস কোষ বিভাজন',
                  para1:
                      'মিয়োসিস কোষ বিভাজনে একটি ডিপ্লয়েড (2n) জনন মাতৃকোষ পরপর দুবার বিভাজিত হয়ে চারটি হ্যাপ্লয়েড (n) অপত্য কোষ সৃষ্টি করে।',
                  highlight:
                      'অপত্য কোষে ক্রোমোজোম সংখ্যা অর্ধেক (2n → n) হয়ে যায় বলে এই বিভাজনকে হ্রাসমূলক বিভাজন বলা হয়।',
                  para2:
                      'প্রোফেজ-১ উপ-দশায় হোমোলোগাস ক্রোমোজোমের নন-সিস্টার ক্রোমাটিডের মধ্যে ক্রসিং ওভারের ফলে জিনের বিনিময় ঘটে।',
                  diagramTitle: 'চিত্র ৩.১০: হ্রাসমূলক বিভাজন',
                  diagramFormula: 'মাতৃকোষ (2n) → ৪টি গ্যামেট কোষ (n)',
                  turns: [
                      {
                          q: 'মিয়োসিসকে কেন হ্রাসমূলক বিভাজন বলা হয়?',
                          a: 'মাতৃকোষের ডিপ্লয়েড (2n) ক্রোমোজোম অর্ধেক হয়ে অপত্য কোষে হ্যাপ্লয়েড (n) হয় বলে একে হ্রাসমূলক বিভাজন বলে।',
                      },
                      {
                          q: 'এই পৃষ্ঠা অনুযায়ী ক্রসিং ওভারের গুরুত্ব কী?',
                          a: 'ক্রসিং ওভারের ফলে জিনের বিনিময় ঘটে, যা জীবজগতে জিনগত বৈচিত্র্য (Genetic Variation) সৃষ্টি করে।',
                      },
                  ],
              },
              {
                  badge: 'এইচএসসি আইসিটি · অধ্যায় ৩',
                  bookTitle: 'তথ্য ও যোগাযোগ প্রযুক্তি (একাদশ-দ্বাদশ)',
                  pageNo: '১১২',
                  heading: 'বাইনারি থেকে ডেসিমেল রূপান্তর',
                  para1:
                      'পজিশনাল সংখ্যা পদ্ধতিতে প্রতিটি অঙ্কের স্থানীয় মান থাকে। বাইনারির ভিত্তি ২ (০, ১) এবং ডেসিমেলের ভিত্তি ১০ (০–৯)।',
                  highlight:
                      '(1011)₂ কে ডেসিমেলে নিতে প্রতিটি বিটকে তার স্থানীয় মান (2ⁿ) দিয়ে গুণ করে যোগ করতে হয়।',
                  para2:
                      'গাণিতিক রূপান্তর: (1×2³) + (0×2²) + (1×2¹) + (1×2⁰) = 8 + 0 + 2 + 1 = (11)₁₀।',
                  diagramTitle: 'ছক ৩.৪: ৮-৪-২-১ শর্টকাট ট্রিক',
                  diagramFormula: 'বিট: ১ ০ ১ ১  ⇒  ৮ + ০ + ২ + ১ = ১১₁₀',
                  turns: [
                      {
                          q: 'বাইনারি (1011)₂ থেকে ডেসিমেল করার শর্টকাট নিয়ম কী?',
                          a: '৮-৪-২-১ ট্রিক! ১০১১-তে ১ আছে ৮, ২ ও ১-এর ঘরে। তাই ৮ + ২ + ১ = ১১। মাত্র ৩ সেকেন্ডে উত্তর!',
                      },
                      {
                          q: 'দশমিক ভগ্নাংশ (.101)₂ থাকলে কীভাবে করব?',
                          a: 'দশমিকের পরের বিটগুলোকে 2⁻¹ (0.5), 2⁻² (0.25), 2⁻³ (0.125) দিয়ে গুণ করুন: 0.5 + 0 + 0.125 = 0.625₁₀।',
                      },
                  ],
              },
              {
                  badge: 'বিসিএস প্রিলিমিনারি ডাইজেস্ট · বাংলা সাহিত্য',
                  bookTitle: 'বিসিএস প্রিলিমিনারি ডাইজেস্ট (২০২৫)',
                  pageNo: '২৮',
                  heading: 'রবীন্দ্রনাথ ঠাকুর ও গীতাঞ্জলি',
                  para1:
                      'গীতাঞ্জলি কাব্যগ্রন্থটি ১৯১০ সালে (১৩১৭ বঙ্গাব্দ) প্রকাশিত হয়। এর ইংরেজি অনুবাদ "Song Offerings" ১৯১২ সালে প্রকাশিত হয়।',
                  highlight:
                      'আইরিশ কবি W.B. Yeats "Song Offerings"-এর ভূমিকা লেখেন এবং ১৯১৩ সালে রবীন্দ্রনাথ সাহিত্যে নোবেল পুরস্কার পান।',
                  para2:
                      'Song Offerings-এ মোট ১০৩টি কবিতা রয়েছে এবং এটি লন্ডনের ইন্ডিয়া সোসাইটি থেকে প্রকাশিত হয়।',
                  diagramTitle: 'পরীক্ষার কালানুক্রম ছক',
                  diagramFormula: '১৯১০ (গীতাঞ্জলি) → ১৯১২ (ইংরেজি) → ১৯১৩ (নোবেল)',
                  turns: [
                      {
                          q: 'Song Offerings-এর ভূমিকা কে লিখেছিলেন?',
                          a: 'আইরিশ কবি ডব্লিউ. বি. ইয়েটস (W.B. Yeats) ১৯১২ সালে প্রকাশিত Song Offerings-এর ভূমিকা লেখেন।',
                      },
                      {
                          q: 'এই পৃষ্ঠা থেকে বিসিএসে কোন প্রশ্নগুলো সবচেয়ে বেশি আসে?',
                          a: '১) গীতাঞ্জলি প্রকাশকাল (১৯১০), ২) Song Offerings-এর ভূমিকা লেখক (W.B. Yeats), ৩) মোট কবিতা (১০৩টি)।',
                      },
                  ],
              },
              {
                  badge: 'এইচএসসি পদার্থবিজ্ঞান ১ম পত্র · অধ্যায় ২',
                  bookTitle: 'পদার্থবিজ্ঞান প্রথম পত্র (একাদশ-দ্বাদশ)',
                  pageNo: '৪৫',
                  heading: '২.৪ ভেক্টর উপাংশ ও নদী-নৌকার অংক',
                  para1:
                      'd প্রস্থের নদীতে স্রোতের বেগ u এবং নৌকার বেগ v পরস্পর α কোণে ক্রিয়া করলে নৌকাটি লব্ধি বেগে অপর পাড়ে পৌঁছায়।',
                  highlight:
                      'ন্যূনতম সময়ে নদী পার হতে নৌকাকে স্রোতের সাথে সমকোণে (α = 90°) চালাতে হয়; তখন সর্বনিম্ন সময় t_min = d / v।',
                  para2:
                      'ন্যূনতম দূরত্বে (সোজা অপর পাড়ে) পৌঁছাতে হলে cos α = −u / v কোণে উজানে নৌকা চালাতে হয়।',
                  diagramTitle: 'সূত্র ২.১৮: নদী পারাপারের সময়',
                  diagramFormula: 't = d / (v sin α)  |  t_min = d / v (যখন α = 90°)',
                  turns: [
                      {
                          q: 'ন্যূনতম সময়ে নদী পার হতে কোন কোণে নৌকা চালাতে হবে?',
                          a: 'স্রোতের সাথে α = 90° কোণে (সোজাসুজি)। কারণ sin 90° = 1 হওয়ায় t = d / v সর্বনিম্ন হয়।',
                      },
                      {
                          q: 'স্রোতের বেগ বাড়লে কি ন্যূনতম সময়ের কোনো পরিবর্তন হয়?',
                          a: 'না! t_min = d / v সূত্রে স্রোতের বেগ (u) নেই। স্রোত শুধু নৌকাকে অনুপ্রস্থে ভাসিয়ে নেয়।',
                      },
                  ],
              },
              {
                  badge: 'এসএসসি উচ্চতর গণিত · অধ্যায় ৮',
                  bookTitle: 'উচ্চতর গণিত (নবম-দশম শ্রেণি)',
                  pageNo: '১৭৬',
                  heading: '৮.২ ত্রিকোণমিতিক অভেদাবলি ও প্রয়োগ',
                  para1:
                      'সমকোণী ত্রিভুজের সূক্ষ্মকোণ θ-এর জন্য বাহুগুলোর অনুপাত তিনটি মৌলিক পিথাগোরীয় অভেদ মেনে চলে।',
                  highlight:
                      'মৌলিক অভেদ: sin²θ + cos²θ = 1 এবং sec²θ − tan²θ = 1 থেকে (sec θ + tan θ)(sec θ − tan θ) = 1 পাওয়া যায়।',
                  para2:
                      'সুতরাং sec θ + tan θ = k দেওয়া থাকলে মুহূর্তেই sec θ − tan θ = 1 / k নির্ণয় করা যায়।',
                  diagramTitle: 'বিপরীত অভেদ কৌশল',
                  diagramFormula: 'sec θ + tan θ = k  ⇒  sec θ − tan θ = 1 / k',
                  turns: [
                      {
                          q: 'sec θ + tan θ = 3 হলে sec θ − tan θ কত হবে?',
                          a: 'sec²θ − tan²θ = 1 অভেদ অনুযায়ী এটি সরাসরি বিপরীত মান অর্থাৎ 1/3 হবে!',
                      },
                      {
                          q: 'এখান থেকে sec θ-এর মান কীভাবে বের করব?',
                          a: 'সমীকরণ দুটি যোগ করলেই tan θ কাটা যাবে: 2 sec θ = 3 + 1/3 = 10/3, অতএব sec θ = 5/3।',
                      },
                  ],
              },
          ]
);

const bookIndex = ref(0);
const currentBook = computed(() => bookPool.value[bookIndex.value] || bookPool.value[0]);

// Animated chat state:
// visibleMessages: array of { id, role: 'user'|'ai', text }
const visibleMessages = ref([]);
const typingDraft = ref('');
const aiThinking = ref(false);
const highlightActive = ref(false);

let timers = [];

function clearTimers() {
    timers.forEach((id) => clearTimeout(id));
    timers = [];
}

function schedule(fn, delay) {
    const id = setTimeout(fn, delay);
    timers.push(id);
}

function runAnimatedConversation() {
    clearTimers();
    visibleMessages.value = [];
    typingDraft.value = '';
    aiThinking.value = false;
    highlightActive.value = false;

    const turns = currentBook.value.turns || [];
    if (!turns.length) return;

    let cursor = 300;

    turns.forEach((turn, idx) => {
        // Step 1: Type question into the input box progressively
        const qText = turn.q;
        const steps = 10;
        for (let s = 1; s <= steps; s++) {
            const sliceLen = Math.ceil((qText.length * s) / steps);
            schedule(() => {
                typingDraft.value = qText.slice(0, sliceLen);
            }, cursor + s * 45);
        }
        cursor += steps * 45 + 220;

        // Step 2: Push user question bubble into conversation feed
        schedule(() => {
            typingDraft.value = '';
            visibleMessages.value.push({
                id: `q-${idx}`,
                role: 'user',
                text: qText,
            });
            aiThinking.value = true;
            highlightActive.value = true;
        }, cursor);

        cursor += 1150;

        // Step 3: AI responds with answer bubble
        schedule(() => {
            aiThinking.value = false;
            visibleMessages.value.push({
                id: `a-${idx}`,
                role: 'ai',
                text: turn.a,
            });
        }, cursor);

        cursor += 2200;
    });

    // After both Q&A turns complete, hold for reading and then replay smoothly
    schedule(() => {
        runAnimatedConversation();
    }, cursor + 3800);
}

watch(
    () => props.locale,
    () => {
        runAnimatedConversation();
    }
);

onMounted(() => {
    // Pick a different book on EVERY page refresh using sessionStorage
    try {
        const total = bookPool.value.length;
        const prevRaw = sessionStorage.getItem('sikhun_hero_book_idx');
        const nextIdx = prevRaw !== null ? (parseInt(prevRaw, 10) + 1) % total : 0;
        bookIndex.value = Number.isNaN(nextIdx) ? 0 : nextIdx;
        sessionStorage.setItem('sikhun_hero_book_idx', String(bookIndex.value));
    } catch {
        bookIndex.value = Math.floor(Math.random() * bookPool.value.length);
    }

    runAnimatedConversation();
});

onUnmounted(() => {
    clearTimers();
});
</script>

<template>
    <div
        class="hero-book-workspace relative w-full py-2 flex flex-col justify-center select-none"
        aria-label="Open Book and Animated AI Tutor Conversation"
    >
        <!-- Ambient Warm Glow -->
        <div class="pointer-events-none absolute inset-0 -z-10 flex items-center justify-center">
            <div
                class="w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-[radial-gradient(circle,rgba(183,139,82,0.20),transparent_70%)] blur-2xl"
            ></div>
        </div>

        <!-- Top Active Book Pill (No slider dots) -->
        <div class="mb-2.5 flex items-center justify-between px-1">
            <div class="inline-flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--surface)]/95 px-3.5 py-1 text-[11px] font-semibold text-[var(--text)] shadow-xs">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ currentBook.badge }}</span>
            </div>
            <span class="text-[10.5px] font-mono text-[var(--text-muted)]">
                {{ en ? `Page ${currentBook.pageNo}` : `পৃষ্ঠা ${currentBook.pageNo}` }}
            </span>
        </div>

        <!-- OPEN 2-PAGE BOOK SPREAD: LEFT = OPEN BOOK PAGE, RIGHT = LIVE AI CONVERSATION -->
        <div
            class="book-spread-shell relative mx-auto w-full rounded-2xl border border-[#cbb69b]/80 dark:border-white/15 bg-[#4a3520] p-1.5 sm:p-2 shadow-2xl"
        >
            <div
                class="relative grid grid-cols-1 sm:grid-cols-2 overflow-hidden rounded-xl bg-[#fcf9f2] dark:bg-[#1b1a17] text-[#231f1a] dark:text-[#f1ede4] min-h-[340px] sm:min-h-[365px]"
            >
                <!-- Center Book Spine Crease & Bookmark Ribbon (Desktop/Tablet) -->
                <div
                    class="hidden sm:block pointer-events-none absolute inset-y-0 left-1/2 -translate-x-1/2 w-5 z-20 bg-gradient-to-r from-black/10 via-black/25 to-black/10 dark:from-black/30 dark:via-black/60 dark:to-black/30"
                ></div>
                <div
                    class="hidden sm:block pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-1.5 h-8 z-20 bg-[#9b4d22] rounded-b-sm shadow"
                ></div>

                <!-- LEFT PAGE: OPEN BOOK CONTENT -->
                <div class="relative z-10 flex flex-col justify-between p-3.5 sm:p-4 sm:pr-5 border-b sm:border-b-0 sm:border-r border-[#e5dec9] dark:border-white/10">
                    <div>
                        <div class="flex items-center justify-between text-[9.5px] sm:text-[10px] font-mono text-[#7c6f5b] dark:text-[#a39887] border-b border-[#e6decb] dark:border-white/10 pb-1.5">
                            <span class="truncate font-semibold">{{ currentBook.bookTitle }}</span>
                            <span class="shrink-0">{{ en ? 'p.' : 'পৃ.' }} {{ currentBook.pageNo }}</span>
                        </div>

                        <h4 class="mt-2.5 font-serif text-xs sm:text-[13.5px] font-bold text-[#8a5a32] dark:text-[#d2ad7c] leading-snug">
                            {{ currentBook.heading }}
                        </h4>

                        <p class="mt-2 text-[10.5px] sm:text-[11px] leading-relaxed text-[#3d372e] dark:text-[#d6d0c4]">
                            {{ currentBook.para1 }}
                        </p>

                        <!-- Highlighted Line referenced by AI -->
                        <div
                            class="mt-2.5 rounded-lg border px-2.5 py-2 text-[10.5px] sm:text-[11px] font-medium leading-relaxed transition-all duration-500"
                            :class="
                                highlightActive
                                    ? 'border-amber-500/60 bg-amber-300/30 dark:bg-amber-500/20 text-[#2b2113] dark:text-amber-100 ring-2 ring-amber-500/30'
                                    : 'border-amber-500/30 bg-amber-200/20 dark:bg-amber-500/10 text-[#3d372e] dark:text-[#e5dec9]'
                            "
                        >
                            <span class="inline-block mr-1 text-[9px] font-bold uppercase tracking-wider text-[#8a5a32] dark:text-amber-300">
                                ✦ AI Focus:
                            </span>
                            {{ currentBook.highlight }}
                        </div>

                        <p class="mt-2 text-[10px] sm:text-[10.5px] leading-relaxed text-[#524a3e] dark:text-[#bdb5a6]">
                            {{ currentBook.para2 }}
                        </p>
                    </div>

                    <!-- Textbook Formula / Diagram Box -->
                    <div class="mt-3 rounded-lg border border-dashed border-[#cbbba0] dark:border-white/15 bg-[#f3ede0] dark:bg-white/5 px-2.5 py-2">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-[#8a5a32] dark:text-[#d2ad7c]">
                            {{ currentBook.diagramTitle }}
                        </div>
                        <div class="mt-0.5 font-mono text-[10px] sm:text-[10.5px] font-semibold text-[#2d261e] dark:text-[#ece6da]">
                            {{ currentBook.diagramFormula }}
                        </div>
                    </div>
                </div>

                <!-- RIGHT PAGE: ANIMATED AI TUTOR CONVERSATION -->
                <div class="relative z-10 flex flex-col justify-between p-3.5 sm:p-4 sm:pl-5 bg-[#faf6ec]/90 dark:bg-[#161512]">
                    <!-- AI Tutor Header -->
                    <div>
                        <div class="flex items-center justify-between border-b border-[#e6decb] dark:border-white/10 pb-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#8a5a32] text-white text-xs font-bold shadow-xs">
                                    ✦
                                </span>
                                <div class="min-w-0">
                                    <div class="truncate text-[11px] sm:text-xs font-bold text-[#231f1a] dark:text-[#f1ede4] leading-none">
                                        {{ en ? 'Sikhun AI Page Tutor' : 'শিখুন AI পৃষ্ঠা টিউটর' }}
                                    </div>
                                    <div class="mt-0.5 truncate text-[9px] text-[#7c6f5b] dark:text-[#a39887]">
                                        {{ en ? `Answering from Page ${currentBook.pageNo}` : `পৃষ্ঠা ${currentBook.pageNo} থেকে প্রশ্নোত্তর` }}
                                    </div>
                                </div>
                            </div>
                            <span class="shrink-0 rounded-full bg-emerald-500/15 px-2 py-0.5 text-[9px] font-bold text-emerald-700 dark:text-emerald-400">
                                {{ en ? 'Live' : 'সক্রিয়' }}
                            </span>
                        </div>

                        <!-- Animatively Added Messages Feed -->
                        <TransitionGroup
                            tag="div"
                            name="chat-msg"
                            class="mt-2.5 space-y-2 overflow-hidden"
                        >
                            <div
                                v-for="msg in visibleMessages"
                                :key="msg.id"
                                :class="msg.role === 'user' ? 'flex justify-end' : 'flex items-start gap-1.5'"
                            >
                                <span
                                    v-if="msg.role === 'ai'"
                                    class="mt-0.5 inline-flex h-4.5 w-4.5 shrink-0 items-center justify-center rounded-full bg-[#8a5a32]/15 text-[#8a5a32] dark:text-[#d2ad7c] text-[8.5px] font-bold"
                                >
                                    AI
                                </span>

                                <div
                                    :class="
                                        msg.role === 'user'
                                            ? 'max-w-[88%] rounded-xl rounded-br-xs bg-[#8a5a32] text-white px-2.5 py-1.5 text-[10px] sm:text-[10.5px] font-medium leading-snug shadow-xs'
                                            : 'flex-1 rounded-xl rounded-bl-xs border border-[#e2d8c3] dark:border-white/10 bg-white dark:bg-[#22201c] px-2.5 py-1.5 text-[10px] sm:text-[10.5px] leading-relaxed text-[#2b2620] dark:text-[#e8e2d5] shadow-2xs'
                                    "
                                >
                                    {{ msg.text }}
                                </div>
                            </div>

                            <!-- AI Thinking / Analyzing Indicator -->
                            <div
                                v-if="aiThinking"
                                key="ai-thinking"
                                class="flex items-center gap-1.5"
                            >
                                <span class="inline-flex h-4.5 w-4.5 shrink-0 items-center justify-center rounded-full bg-[#8a5a32]/15 text-[#8a5a32] dark:text-[#d2ad7c] text-[8.5px] font-bold">
                                    AI
                                </span>
                                <div class="inline-flex items-center gap-1 rounded-xl rounded-bl-xs border border-[#e2d8c3] dark:border-white/10 bg-white dark:bg-[#22201c] px-2.5 py-1.5 text-[9.5px] text-[#7c6f5b] dark:text-[#a39887]">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#8a5a32] animate-bounce"></span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#8a5a32] animate-bounce [animation-delay:150ms]"></span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#8a5a32] animate-bounce [animation-delay:300ms]"></span>
                                    <span class="ml-1">
                                        {{ en ? 'Reading page...' : 'পৃষ্ঠা থেকে উত্তর লিখছে...' }}
                                    </span>
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>

                    <!-- Live Prompt Input Bar at Bottom of Right Page -->
                    <div class="mt-3 flex items-center justify-between gap-2 rounded-xl border border-[#dfd5c0] dark:border-white/10 bg-white dark:bg-[#22201c] px-2.5 py-1.5">
                        <span
                            class="truncate text-[10px] font-medium"
                            :class="typingDraft ? 'text-[#231f1a] dark:text-[#f1ede4]' : 'text-[#9c8f7a] dark:text-[#7c7467]'"
                        >
                            {{
                                typingDraft
                                    ? typingDraft + '▋'
                                    : en
                                      ? 'Ask anything from this page...'
                                      : 'এই পৃষ্ঠা থেকে যেকোনো প্রশ্ন করুন...'
                            }}
                        </span>
                        <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-lg bg-[#8a5a32] text-white text-[10px] font-bold">
                            ↑
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.book-spread-shell {
    box-shadow:
        0 22px 44px -15px rgba(24, 18, 10, 0.28),
        0 4px 0 0 #d9cbb3,
        0 7px 0 0 #2b1e12;
}

.chat-msg-enter-active,
.chat-msg-leave-active {
    transition: all 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
}
.chat-msg-enter-from {
    opacity: 0;
    transform: translateY(10px) scale(0.97);
}
.chat-msg-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}
</style>
