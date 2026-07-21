<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    stats: Object,
    categories: Array,
    topCourses: Array,
    popularBooks: Array,
    plans: Array,
    testimonials: Array,
    seo: Object,
});

const { t, locale } = useI18n();
const isEn = computed(() => locale.value === 'en');

// Marketing copy that isn't real platform data (headline, step descriptions,
// category icons) is inline bilingual here rather than routed through the
// shared lang/*.json files — this content is specific to this one page and
// keeping it local avoids bloating the global translation dictionary.
const categoryIcons = { science: '🔬', humanities: '📖', business: '💼', novel: '📚', essay: '✍️', academic: '🎓', 'non-academic': '🌱' };

const steps = computed(() => isEn.value ? [
    { icon: '🎯', title: 'Create Account', text: 'Sign up free in 30 seconds and pick your level — SSC, HSC, university, or job prep.' },
    { icon: '🔍', title: 'Discover Content', text: 'Browse books and courses filtered by subject, level, and category.' },
    { icon: '🤖', title: 'Learn with AI', text: 'Chat with your books, generate practice exams, and build flashcards instantly.' },
    { icon: '🏆', title: 'Track & Grow', text: 'Climb the leaderboard, earn certificates, and watch your scores improve.' },
] : [
    { icon: '🎯', title: 'অ্যাকাউন্ট তৈরি করুন', text: 'মাত্র ৩০ সেকেন্ডে ফ্রি সাইন আপ করুন এবং আপনার লেভেল বেছে নিন — SSC, HSC, বিশ্ববিদ্যালয় বা চাকরির প্রস্তুতি।' },
    { icon: '🔍', title: 'কনটেন্ট খুঁজুন', text: 'বিষয়, লেভেল ও ক্যাটাগরি অনুযায়ী বই ও কোর্স ব্রাউজ করুন।' },
    { icon: '🤖', title: 'AI দিয়ে শিখুন', text: 'বইয়ের সাথে চ্যাট করুন, তাৎক্ষণিক অনুশীলন পরীক্ষা ও ফ্ল্যাশকার্ড তৈরি করুন।' },
    { icon: '🏆', title: 'ট্র্যাক করুন ও এগিয়ে যান', text: 'লিডারবোর্ডে এগিয়ে যান, সার্টিফিকেট অর্জন করুন এবং নিজের উন্নতি দেখুন।' },
]);

const aiFeatures = computed(() => isEn.value ? [
    'Chat with any book using AI', 'Auto-generate MCQ & CQ exams', 'Instant flashcard generation with spaced repetition',
    'AI essay grading with detailed feedback', 'Personalized AI study schedules',
] : [
    'যেকোনো বইয়ের সাথে AI চ্যাট', 'স্বয়ংক্রিয় MCQ ও CQ পরীক্ষা তৈরি', 'তাৎক্ষণিক ফ্ল্যাশকার্ড — স্পেসড রিপিটিশনসহ',
    'বিস্তারিত ফিডব্যাকসহ AI প্রবন্ধ মূল্যায়ন', 'ব্যক্তিগতকৃত AI স্টাডি শিডিউল',
]);
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <!-- ============ HERO ============ -->
        <section class="relative overflow-hidden bg-grain border-b border-[var(--border)]">
            <div class="max-w-7xl mx-auto px-5 pt-16 pb-14 grid lg:grid-cols-2 gap-12 items-center relative">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[var(--surface)] border border-[var(--border)] text-xs font-semibold text-[var(--secondary)] mb-6">
                        🇧🇩 {{ isEn ? "Bangladesh's #1 AI Learning Platform" : 'বাংলাদেশের #১ AI শিক্ষা প্ল্যাটফর্ম' }}
                    </span>
                    <h1 class="font-heading text-4xl md:text-5xl xl:text-6xl font-extrabold leading-[1.1] mb-5">
                        {{ isEn ? 'Learn Anything.' : 'শিখুন যেকোনো কিছু।' }}<br />
                        <span class="text-[var(--secondary)]">{{ isEn ? 'Grow Everywhere.' : 'এগিয়ে যান সবখানে।' }}</span>
                    </h1>
                    <p class="text-lg text-[var(--text-muted)] max-w-lg mb-8">
                        {{ isEn
                            ? 'Books, AI chat, auto-generated exams, and courses — all in one place. Built for HSC, SSC, university, and job-prep students.'
                            : 'বই, AI চ্যাট, স্বয়ংক্রিয় পরীক্ষা এবং কোর্স — সব এক জায়গায়। HSC, SSC, বিশ্ববিদ্যালয় ও চাকরির প্রস্তুতির শিক্ষার্থীদের জন্য তৈরি।' }}
                    </p>
                    <div class="flex flex-wrap gap-3 mb-10">
                        <Link href="/courses" class="px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold shadow-sm">
                            {{ isEn ? 'Explore Courses →' : 'কোর্স দেখুন →' }}
                        </Link>
                        <Link href="/library" class="px-6 py-3 rounded-lg border border-[var(--border)] bg-[var(--surface)] hover:bg-[var(--surface2)] font-semibold">
                            {{ isEn ? 'Browse Library' : 'লাইব্রেরি ব্রাউজ করুন' }}
                        </Link>
                    </div>
                    <div class="grid grid-cols-4 gap-4 max-w-md">
                        <div v-for="s in [
                            [stats.courses, isEn ? 'Courses' : 'কোর্স'],
                            [stats.books, isEn ? 'Books' : 'বই'],
                            [stats.students, isEn ? 'Students' : 'শিক্ষার্থী'],
                            [stats.mentors, isEn ? 'Mentors' : 'মেন্টর'],
                        ]" :key="s[1]">
                            <div class="font-heading text-xl md:text-2xl font-extrabold">{{ s[0] }}+</div>
                            <div class="text-xs text-[var(--text-muted)]">{{ s[1] }}</div>
                        </div>
                    </div>
                </div>

                <!-- Decorative illustrative mockup card (not live data) -->
                <div class="relative hidden lg:block">
                    <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-xl overflow-hidden">
                        <div class="h-40 bg-gradient-to-br from-[var(--secondary)] to-[#123d2f] relative">
                            <span class="absolute top-3 left-3 px-2 py-0.5 rounded bg-white/90 text-[10px] font-bold text-[var(--secondary)]">
                                {{ isEn ? 'AI CHAT' : 'AI চ্যাট' }}
                            </span>
                        </div>
                        <div class="p-5">
                            <div class="font-heading font-bold mb-1">{{ isEn ? 'Ask about any chapter' : 'যেকোনো অধ্যায় নিয়ে জিজ্ঞাসা করুন' }}</div>
                            <div class="text-xs text-[var(--text-muted)] mb-4">{{ isEn ? 'Grounded in your actual textbook' : 'আপনার পাঠ্যবই অনুযায়ী উত্তর' }}</div>
                            <div class="h-1.5 rounded-full bg-[var(--surface2)] overflow-hidden">
                                <div class="h-full w-2/3 bg-[var(--primary)]"></div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -bottom-5 -left-8 rounded-xl border border-[var(--border)] bg-[var(--surface)] shadow-lg p-3 flex items-center gap-2 max-w-[200px]">
                        <span class="text-lg">🔥</span>
                        <span class="text-xs font-medium">{{ isEn ? 'Leaderboard streak active' : 'লিডারবোর্ড স্ট্রিক চলছে' }}</span>
                    </div>
                </div>
            </div>

            <!-- Category chips -->
            <div class="max-w-7xl mx-auto px-5 pb-8 flex flex-wrap gap-2">
                <Link v-for="c in categories" :key="c.id" :href="`/library?category_id=${c.id}`"
                    class="px-4 py-2 rounded-full border border-[var(--border)] bg-[var(--surface)] hover:bg-[var(--surface2)] text-sm font-medium flex items-center gap-1.5">
                    <span>{{ categoryIcons[c.slug] ?? '📘' }}</span> {{ c.name }}
                </Link>
            </div>
        </section>

        <!-- ============ TOP COURSES ============ -->
        <section class="max-w-7xl mx-auto px-5 py-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="text-xs font-semibold text-[var(--secondary)] uppercase tracking-wide">{{ isEn ? 'Popular Right Now' : 'এখন জনপ্রিয়' }}</span>
                    <h2 class="font-heading text-2xl md:text-3xl font-extrabold mt-1">{{ isEn ? 'Top Courses' : 'শীর্ষ কোর্সসমূহ' }}</h2>
                </div>
                <Link href="/courses" class="px-4 py-2 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-sm font-semibold whitespace-nowrap">
                    {{ isEn ? 'View All →' : 'সব দেখুন →' }}
                </Link>
            </div>

            <div v-if="topCourses.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <Link v-for="(course, i) in topCourses" :key="course.id" :href="`/courses/${course.slug}`"
                    class="group rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="aspect-video relative overflow-hidden flex items-center justify-center text-white"
                        :style="{ background: ['linear-gradient(135deg,#1f6f54,#123d2f)','linear-gradient(135deg,#6c5ce7,#3f2f8f)','linear-gradient(135deg,#8a5a3c,#4a2f1c)','linear-gradient(135deg,#1f6f54,#0d2b21)'][i % 4] }">
                        <img v-if="course.cover_image_url" :src="course.cover_image_url" :alt="course.title" loading="lazy" class="absolute inset-0 w-full h-full object-cover" />
                        <span v-else class="text-4xl opacity-80">🎓</span>
                        <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold bg-white/90 text-[var(--text)]">
                            {{ +course.price === 0 ? (isEn ? 'FREE' : 'ফ্রি') : `৳${course.price}` }}
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center gap-2 text-[10px] text-[var(--text-muted)] uppercase font-medium mb-1.5">
                            <span>{{ course.level }}</span>
                        </div>
                        <div class="font-semibold text-sm mb-1 line-clamp-2 group-hover:text-[var(--primary)]">{{ course.title }}</div>
                        <div class="text-xs text-[var(--text-muted)] mb-3">{{ course.mentor?.name }}</div>
                        <div class="text-xs text-[var(--text-muted)]">
                            {{ course.enrollments_count }} {{ isEn ? 'enrolled' : 'জন ভর্তি' }}
                        </div>
                    </div>
                </Link>
            </div>
            <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
                {{ isEn ? 'Courses coming soon.' : 'শীঘ্রই কোর্স যুক্ত হবে।' }}
            </div>
        </section>

        <!-- ============ HOW IT WORKS ============ -->
        <section class="bg-[var(--surface2)] border-y border-[var(--border)] py-16">
            <div class="max-w-5xl mx-auto px-5 text-center mb-10">
                <span class="text-xs font-semibold text-[var(--primary)] uppercase tracking-wide">{{ isEn ? 'Get Started in Minutes' : 'কয়েক মিনিটেই শুরু করুন' }}</span>
                <h2 class="font-heading text-2xl md:text-3xl font-extrabold mt-1">{{ isEn ? 'How Sikhun Works' : 'Sikhun কিভাবে কাজ করে' }}</h2>
            </div>
            <div class="max-w-5xl mx-auto px-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div v-for="(step, i) in steps" :key="step.title" class="rounded-xl bg-[var(--surface)] border border-[var(--border)] p-6 text-center">
                    <div class="text-3xl mb-3">{{ step.icon }}</div>
                    <div class="text-xs font-mono text-[var(--text-muted)] mb-1">0{{ i + 1 }}</div>
                    <div class="font-heading font-bold mb-2">{{ step.title }}</div>
                    <p class="text-sm text-[var(--text-muted)]">{{ step.text }}</p>
                </div>
            </div>
        </section>

        <!-- ============ POPULAR BOOKS ============ -->
        <section class="max-w-7xl mx-auto px-5 py-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="text-xs font-semibold text-[var(--secondary)] uppercase tracking-wide">{{ isEn ? 'Digital Library' : 'ডিজিটাল লাইব্রেরি' }}</span>
                    <h2 class="font-heading text-2xl md:text-3xl font-extrabold mt-1">{{ isEn ? 'Popular Books' : 'জনপ্রিয় বই' }}</h2>
                </div>
                <Link href="/library" class="px-4 py-2 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-sm font-semibold whitespace-nowrap">
                    {{ isEn ? 'Browse All →' : 'সব দেখুন →' }}
                </Link>
            </div>

            <div v-if="popularBooks.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5">
                <Link v-for="(book, i) in popularBooks" :key="book.id" :href="`/library/${book.slug}`"
                    class="group rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="aspect-[3/4] relative flex items-center justify-center text-white"
                        :style="{ background: ['#1f3a63','#123d2f','#1f6f54','#8a2f2f','#8a5a1c'][i % 5] }">
                        <img v-if="book.cover_image_url" :src="book.cover_image_url" :alt="book.title" loading="lazy" class="absolute inset-0 w-full h-full object-cover" />
                        <span v-else class="text-3xl opacity-80">📘</span>
                        <span v-if="book.is_free" class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-bold bg-white/90 text-[var(--secondary)]">
                            {{ isEn ? 'FREE' : 'ফ্রি' }}
                        </span>
                    </div>
                    <div class="p-3">
                        <div class="text-[10px] uppercase text-[var(--text-muted)] mb-1">{{ book.level }}</div>
                        <div class="font-medium text-sm mb-1 line-clamp-2 group-hover:text-[var(--primary)]">{{ book.title }}</div>
                        <div class="text-xs text-[var(--text-muted)]">{{ book.author?.name }}</div>
                        <div class="text-xs text-[var(--text-muted)]" v-if="book.total_pages">{{ book.total_pages }} {{ isEn ? 'pages' : 'পৃষ্ঠা' }}</div>
                    </div>
                </Link>
            </div>
            <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
                {{ isEn ? 'Books coming soon.' : 'শীঘ্রই বই যুক্ত হবে।' }}
            </div>
        </section>

        <!-- ============ AI STUDY COMPANION (dark showcase) ============ -->
        <section class="dark bg-[var(--bg)] text-[var(--text)] py-16 border-y border-[var(--border)]">
            <div class="max-w-6xl mx-auto px-5 grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-[var(--primary)]/15 text-[var(--primary)] text-xs font-semibold mb-4">
                        {{ isEn ? '✦ PREMIUM AI FEATURES' : '✦ প্রিমিয়াম AI ফিচার' }}
                    </span>
                    <h2 class="font-heading text-3xl font-extrabold mb-4 leading-tight">
                        {{ isEn ? 'Your AI-Powered Study Companion' : 'আপনার AI-চালিত স্টাডি সঙ্গী' }}
                    </h2>
                    <p class="text-[var(--text-muted)] mb-6">
                        {{ isEn
                            ? 'Chat with any book, generate custom exams and flashcards, get essays graded, and build a personalized study schedule — all powered by AI.'
                            : 'যেকোনো বইয়ের সাথে চ্যাট করুন, কাস্টম পরীক্ষা ও ফ্ল্যাশকার্ড তৈরি করুন, প্রবন্ধ মূল্যায়ন করান এবং ব্যক্তিগতকৃত স্টাডি শিডিউল তৈরি করুন — সবকিছু AI দ্বারা চালিত।' }}
                    </p>
                    <ul class="space-y-2.5 mb-8">
                        <li v-for="f in aiFeatures" :key="f" class="flex items-center gap-2.5 text-sm">
                            <span class="text-[var(--secondary)]">✓</span> {{ f }}
                        </li>
                    </ul>
                    <Link href="/plans" class="inline-flex px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold">
                        {{ isEn ? 'See Plans →' : 'প্ল্যান দেখুন →' }}
                    </Link>
                </div>
                <div class="space-y-3">
                    <div class="ml-auto max-w-[80%] rounded-2xl rounded-tr-sm bg-[var(--surface2)] px-4 py-3 text-sm">
                        {{ isEn ? 'Explain Chapter 3 in simple terms' : 'তৃতীয় অধ্যায়টি সহজ ভাষায় বুঝিয়ে দাও' }}
                    </div>
                    <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-[var(--primary)]/15 px-4 py-3 text-sm">
                        {{ isEn ? "Chapter 3 covers Newton's Laws of Motion. Here's a simple breakdown: 1st Law — an object stays at rest or in motion unless a force acts on it..." : 'তৃতীয় অধ্যায়ে নিউটনের গতিসূত্র আলোচনা করা হয়েছে। সহজ ব্যাখ্যা: প্রথম সূত্র — কোনো বস্তুর উপর বল প্রয়োগ না করা পর্যন্ত এটি স্থির বা গতিশীল থাকে...' }}
                    </div>
                    <div class="ml-auto max-w-[80%] rounded-2xl rounded-tr-sm bg-[var(--surface2)] px-4 py-3 text-sm">
                        {{ isEn ? 'Now generate 10 MCQs from this chapter' : 'এখন এই অধ্যায় থেকে ১০টি MCQ তৈরি করো' }}
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ PRICING ============ -->
        <section id="pricing" class="max-w-6xl mx-auto px-5 py-16 scroll-mt-20">
            <div class="text-center mb-10">
                <span class="text-xs font-semibold text-[var(--primary)] uppercase tracking-wide">{{ isEn ? 'Simple Pricing' : 'সহজ মূল্য নির্ধারণ' }}</span>
                <h2 class="font-heading text-2xl md:text-3xl font-extrabold mt-1">{{ isEn ? 'Plans for Every Student' : 'প্রতিটি শিক্ষার্থীর জন্য প্ল্যান' }}</h2>
                <p class="text-[var(--text-muted)] mt-2">{{ isEn ? 'Start free. Upgrade anytime for more AI features.' : 'ফ্রি শুরু করুন। যেকোনো সময় আরও AI ফিচারের জন্য আপগ্রেড করুন।' }}</p>
            </div>

            <div v-if="plans.length" class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div v-for="(plan, i) in plans" :key="plan.id"
                    class="rounded-2xl border p-6 flex flex-col"
                    :class="i === 1 ? 'border-[var(--primary)] bg-[var(--surface)] shadow-lg relative scale-[1.03]' : 'border-[var(--border)] bg-[var(--surface)]'">
                    <span v-if="i === 1" class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-[var(--primary)] text-white text-xs font-bold">
                        {{ isEn ? 'MOST POPULAR' : 'সবচেয়ে জনপ্রিয়' }}
                    </span>
                    <div class="font-heading text-lg font-bold mb-1">{{ plan.name }}</div>
                    <p class="text-xs text-[var(--text-muted)] mb-4">{{ plan.description }}</p>
                    <div class="text-3xl font-heading font-extrabold mb-5">
                        ৳{{ plan.price_monthly }}<span class="text-sm font-normal text-[var(--text-muted)]">{{ isEn ? '/mo' : '/মাস' }}</span>
                    </div>
                    <ul class="text-sm text-[var(--text-muted)] space-y-2 mb-6 flex-1">
                        <li v-for="f in plan.features" :key="f" class="flex items-start gap-2">
                            <span class="text-[var(--secondary)] mt-0.5">✓</span> {{ f }}
                        </li>
                    </ul>
                    <Link href="/register" class="text-center py-2.5 rounded-lg font-semibold text-sm"
                        :class="i === 1 ? 'bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white' : 'border border-[var(--border)] hover:bg-[var(--surface2)]'">
                        {{ isEn ? 'Get Started' : 'শুরু করুন' }}
                    </Link>
                </div>
            </div>
            <div v-else class="text-center text-[var(--text-muted)]">
                {{ isEn ? 'Plans coming soon.' : 'শীঘ্রই প্ল্যান যুক্ত হবে।' }}
            </div>
        </section>

        <!-- ============ TESTIMONIALS — only renders when real ones exist ============ -->
        <section v-if="testimonials.length" class="dark bg-[var(--bg)] text-[var(--text)] py-16 border-y border-[var(--border)]">
            <div class="max-w-5xl mx-auto px-5 text-center mb-10">
                <span class="text-xs font-semibold text-[var(--primary)] uppercase tracking-wide">{{ isEn ? 'Success Stories' : 'সাফল্যের গল্প' }}</span>
                <h2 class="font-heading text-2xl md:text-3xl font-extrabold mt-1">{{ isEn ? 'What Our Students Say' : 'আমাদের শিক্ষার্থীরা যা বলেন' }}</h2>
            </div>
            <div class="max-w-5xl mx-auto px-5 grid md:grid-cols-3 gap-5">
                <div v-for="testimonial in testimonials" :key="testimonial.student_name" class="rounded-xl bg-[var(--surface)] border border-[var(--border)] p-6">
                    <p class="text-sm text-[var(--text-muted)] mb-4">"{{ testimonial.quote }}"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[var(--primary)] text-white flex items-center justify-center text-sm font-semibold">
                            {{ testimonial.student_name.charAt(0) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold">{{ testimonial.student_name }}</div>
                            <div class="text-xs text-[var(--text-muted)]">{{ testimonial.student_role }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ FINAL CTA ============ -->
        <section class="max-w-5xl mx-auto px-5 py-16 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="font-heading text-2xl md:text-3xl font-extrabold mb-1">{{ isEn ? 'Start Learning Today' : 'আজই শেখা শুরু করুন' }}</h2>
                <p class="text-[var(--text-muted)]">{{ isEn ? 'Free plan available. No credit card required.' : 'ফ্রি প্ল্যান উপলব্ধ। কোনো কার্ড প্রয়োজন নেই।' }}</p>
            </div>
            <div class="flex gap-3 shrink-0">
                <Link href="/register" class="px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold">
                    {{ isEn ? 'Create Free Account' : 'ফ্রি অ্যাকাউন্ট তৈরি করুন' }}
                </Link>
                <Link href="/courses" class="px-6 py-3 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] font-semibold">
                    {{ isEn ? 'Browse Courses' : 'কোর্স ব্রাউজ করুন' }}
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
