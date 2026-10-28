<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ stats: Object, categories: Array, topCourses: Array, popularBooks: Array, latestBlogs: Array, plans: Array, testimonials: Array, seo: Object });
const { locale } = useI18n();
const en = computed(() => locale.value === 'en');

const steps = computed(() => en.value ? [
    { icon: 'person_add', title: 'Create your account', text: 'Choose a learning goal and set up your Sikhun account in a few simple steps.', meta: 'A clear place to begin' },
    { icon: 'travel_explore', title: 'Find your resources', text: 'Explore books and mentor-led courses by subject, level, and category.', meta: 'Books and courses' },
    { icon: 'psychology', title: 'Study actively', text: 'Ask questions, practice with AI study tools, and revisit difficult ideas.', meta: 'Learn by doing' },
    { icon: 'trending_up', title: 'Build your momentum', text: 'Keep track of your learning and return to your goals whenever you are ready.', meta: 'Progress at your pace' },
] : [
    { icon: 'person_add', title: 'অ্যাকাউন্ট তৈরি করুন', text: 'আপনার শেখার লক্ষ্য বেছে নিয়ে কয়েকটি সহজ ধাপে Sikhun অ্যাকাউন্ট তৈরি করুন।', meta: 'শুরু হোক পরিষ্কারভাবে' },
    { icon: 'travel_explore', title: 'উপকরণ খুঁজে নিন', text: 'বিষয়, লেভেল ও ক্যাটাগরি অনুযায়ী বই এবং মেন্টর পরিচালিত কোর্স দেখুন।', meta: 'বই ও কোর্স' },
    { icon: 'psychology', title: 'সক্রিয়ভাবে শিখুন', text: 'প্রশ্ন করুন, AI স্টাডি টুলে অনুশীলন করুন এবং কঠিন বিষয় আবার দেখুন।', meta: 'অনুশীলনে শেখা' },
    { icon: 'trending_up', title: 'এগিয়ে চলুন', text: 'নিজের শেখার অগ্রগতি দেখুন এবং প্রস্তুত হলে আবার লক্ষ্যে ফিরে আসুন।', meta: 'নিজের গতিতে' },
]);

const features = computed(() => en.value ? [
    ['auto_awesome', 'Book-aware study chat', 'Ask questions while you study and get help grounded in the book you are reading.'],
    ['quiz', 'Practice questions', 'Create exam questions to review a chapter and test your understanding.'],
    ['style', 'Flashcards for recall', 'Turn key ideas into flashcards and review them in focused sessions.'],
    ['edit_note', 'Essay feedback', 'Get structured feedback that helps you reflect on and improve your writing.'],
    ['event_note', 'A study plan that fits', 'Organize your learning into a manageable schedule and keep moving forward.'],
] : [
    ['auto_awesome', 'বইভিত্তিক স্টাডি চ্যাট', 'পড়ার সময় প্রশ্ন করুন এবং যে বই পড়ছেন, তার ভিত্তিতে সহায়তা নিন।'],
    ['quiz', 'অনুশীলনের প্রশ্ন', 'অধ্যায় ঝালিয়ে নিতে এবং বোঝাপড়া যাচাই করতে পরীক্ষার প্রশ্ন তৈরি করুন।'],
    ['style', 'মনে রাখার ফ্ল্যাশকার্ড', 'গুরুত্বপূর্ণ ধারণা ফ্ল্যাশকার্ডে নিন এবং মনোযোগ দিয়ে পুনরাবৃত্তি করুন।'],
    ['edit_note', 'রচনায় ফিডব্যাক', 'লেখা পর্যালোচনা ও উন্নত করতে সাজানো পরামর্শ নিন।'],
    ['event_note', 'আপনার উপযোগী পড়ার পরিকল্পনা', 'সহজ একটি সময়সূচিতে পড়াশোনা সাজিয়ে ধাপে ধাপে এগিয়ে যান।'],
]);

const pathwayLabel = computed(() => en.value ? 'Find a subject to explore' : 'পছন্দের বিষয় খুঁজে নিন');
const coursePalette = ['from-[#0e513d] to-[#0a0e18]', 'from-[#7c3d13] to-[#171b26]', 'from-[#123859] to-[#0a0e18]'];
const bookPalette = ['from-[#123858] to-[#0a0e18]', 'from-[#0e513d] to-[#0a0e18]', 'from-[#5b2f22] to-[#0a0e18]', 'from-[#40336e] to-[#0a0e18]', 'from-[#77521c] to-[#0a0e18]'];
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <div class="home-design relative isolate overflow-hidden bg-[var(--bg)]">
            <!-- Ambient color field from the supplied reference -->
            <div class="pointer-events-none absolute -top-48 left-[20%] -z-10 h-[620px] w-[620px] rounded-full bg-[var(--primary)]/10 blur-[140px]"></div>
            <div class="pointer-events-none absolute top-[1120px] -right-48 -z-10 h-[560px] w-[560px] rounded-full bg-[var(--secondary)]/10 blur-[150px]"></div>
            <div class="pointer-events-none absolute top-[2900px] -left-40 -z-10 h-[640px] w-[640px] rounded-full bg-[var(--primary)]/10 blur-[160px]"></div>

            <!-- Hero: headline, live product preview, and real platform counts -->
            <section class="max-w-7xl mx-auto px-4 md:px-6 pt-10 md:pt-14 pb-16 md:pb-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 xl:gap-14 items-center">
                    <div class="lg:col-span-7 flex flex-col items-start">
                        <div class="inline-flex items-center gap-2.5 rounded-full border border-white/5 bg-[#1c1f2a]/80 px-4 py-2 shadow-sm backdrop-blur">
                            <span class="relative flex h-2.5 w-2.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#ff7a00] opacity-60"></span><span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-[#ff7a00]"></span></span>
                            <span class="font-mono text-[10px] md:text-[11px] font-semibold uppercase tracking-[.11em] text-[var(--primary)]">{{ en ? 'Learning tools for every next step' : 'আপনার শেখার প্রতিটি পরবর্তী ধাপে' }}</span>
                            <span class="hidden sm:inline rounded bg-[#0a0e18] px-2 py-0.5 font-mono text-[9px] text-[var(--text-muted)]">SSC · HSC · MORE</span>
                        </div>
                        <h1 class="mt-6 font-heading text-[2.7rem] sm:text-5xl xl:text-[3.55rem] font-extrabold leading-[1.08] tracking-[-.04em]">
                            {{ en ? 'Learn Anything.' : 'শিখুন যেকোনো কিছু।' }}<br>
                            <span class="bg-gradient-to-r from-[#ff7a00] via-[#ffb690] to-[#00f2fe] bg-clip-text text-transparent">{{ en ? 'Grow Everywhere.' : 'এগিয়ে যান সবখানে।' }}</span>
                        </h1>
                        <p class="mt-5 max-w-2xl text-base md:text-lg leading-8 text-[var(--text-muted)]">{{ en ? 'Books, mentor-led courses, bilingual AI study tools, and focused practice come together in one learning space. Prepare for exams, build your skills, and keep learning at your own pace.' : 'বই, মেন্টর পরিচালিত কোর্স, দ্বিভাষিক AI স্টাডি টুল এবং মনোযোগী অনুশীলন—সব এক শেখার পরিবেশে। পরীক্ষার প্রস্তুতি নিন, দক্ষতা গড়ুন এবং নিজের গতিতে শিখে চলুন।' }}</p>
                        <div class="mt-7 flex flex-wrap items-center gap-3">
                            <Link href="/register" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#ff7a00] to-[#f97316] px-6 py-3.5 text-sm font-bold text-white shadow-[0_10px_30px_rgba(249,115,22,.25)] transition hover:-translate-y-0.5 hover:shadow-[0_12px_34px_rgba(249,115,22,.4)]">{{ en ? 'Start learning' : 'শেখা শুরু করুন' }} <span class="material-symbols-outlined">arrow_forward</span></Link>
                            <a href="#courses" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-[#262a35]/80 px-5 py-3.5 text-sm font-semibold text-[var(--text)] shadow-sm transition hover:bg-[#313540]">{{ en ? 'Explore courses' : 'কোর্স ঘুরে দেখুন' }}</a>
                            <span class="inline-flex items-center gap-2 rounded-lg bg-[#171b26] px-3 py-2 text-xs text-[var(--text-muted)]"><span class="h-2 w-2 rounded-full bg-emerald-400"></span>{{ en ? 'Learn in English and Bangla' : 'বাংলা ও ইংরেজিতে শিখুন' }}</span>
                        </div>
                        <div class="mt-9 grid w-full max-w-2xl grid-cols-2 sm:grid-cols-4 gap-3">
                            <div v-for="metric in [
                                [stats.courses, en ? 'Courses' : 'কোর্স', en ? 'Learn from mentors' : 'মেন্টরের সাথে শিখুন'],
                                [stats.books, en ? 'Digital books' : 'ডিজিটাল বই', en ? 'Read and explore' : 'পড়ুন ও জানুন'],
                                [stats.students, en ? 'Learners' : 'শিক্ষার্থী', en ? 'Learning on Sikhun' : 'Sikhun-এ শিখছেন'],
                                [stats.mentors, en ? 'Mentors' : 'মেন্টর', en ? 'Guidance and lessons' : 'পাঠ ও দিকনির্দেশনা'],
                            ]" :key="metric[1]" class="rounded-xl border border-white/5 bg-[#1c1f2a]/80 px-3.5 py-4 shadow-sm">
                                <span class="font-heading text-2xl font-extrabold tracking-tight" :class="['text-[#ff9a52]','text-[#ffb690]','text-[#4cd7f6]','text-[#10b981]'][[en?'Courses':'কোর্স',en?'Digital books':'ডিজিটাল বই',en?'Learners':'শিক্ষার্থী',en?'Mentors':'মেন্টর'].indexOf(metric[1])]">{{ metric[0] }}</span>
                                <span class="mt-1 block text-[10px] font-bold uppercase tracking-wider text-[var(--text)]">{{ metric[1] }}</span>
                                <span class="mt-1 block text-[10px] leading-4 text-[var(--text-muted)]">{{ metric[2] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Product-preview terminal. The conversation is clearly a sample, never presented as live usage data. -->
                    <div class="relative lg:col-span-5">
                        <div class="absolute -inset-7 rounded-[2rem] bg-gradient-to-br from-[#f97316]/10 via-transparent to-[#2563eb]/15 blur-2xl"></div>
                        <div class="relative rounded-2xl border border-white/10 bg-[#1c1f2a]/90 p-3.5 md:p-4 shadow-[0_32px_90px_rgba(0,0,0,.45)] backdrop-blur-xl">
                            <div class="flex items-center justify-between rounded-xl bg-[#171b26] px-3.5 py-3">
                                <div class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#ff6b6b]"></span><span class="h-2.5 w-2.5 rounded-full bg-[#ffb02e]"></span><span class="h-2.5 w-2.5 rounded-full bg-[#10b981]"></span><span class="ml-2 font-mono text-[10px] md:text-xs text-[var(--text-muted)]">sikhun.ai / study-room</span></div>
                                <span class="rounded-full bg-[#0e513d]/40 px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wider text-[#4cd7f6]">{{ en ? 'Study preview' : 'নমুনা পাঠ' }}</span>
                            </div>
                            <div class="mt-3 rounded-xl border border-white/[.04] bg-[#0a0e18]/90 p-3.5 md:p-4">
                                <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                    <div class="flex items-center gap-2.5"><span class="grid h-8 w-8 place-items-center rounded-lg bg-[#f97316]/15 text-[#ff9a52]"><span class="material-symbols-outlined">psychology</span></span><div><div class="text-xs font-semibold">{{ en ? 'Physics · Newton’s Laws' : 'পদার্থবিজ্ঞান · নিউটনের সূত্র' }}</div><div class="mt-0.5 text-[9px] text-[var(--text-muted)]">{{ en ? 'A sample study conversation' : 'নমুনা পড়াশোনার কথোপকথন' }}</div></div></div>
                                    <span class="rounded bg-[#262a35] px-2 py-1 font-mono text-[9px] text-[#ffb690]">HSC</span>
                                </div>
                                <div class="mt-4 flex justify-end"><div class="max-w-[86%] rounded-2xl rounded-tr-sm bg-[#262a35] px-3.5 py-2.5 text-xs leading-relaxed">{{ en ? 'Explain Newton’s third law with an everyday example.' : 'দৈনন্দিন উদাহরণ দিয়ে নিউটনের তৃতীয় সূত্র বুঝিয়ে বলো।' }}</div></div>
                                <div class="mt-3 max-w-[94%] rounded-2xl rounded-tl-sm bg-[#1c1f2a] p-3.5 shadow-lg">
                                    <div class="mb-2 flex items-center gap-1.5 text-xs font-semibold text-[#ff9a52]"><span class="material-symbols-outlined">auto_awesome</span>{{ en ? 'A simple explanation' : 'সহজ ব্যাখ্যা' }}</div>
                                    <p class="text-[11px] leading-5 text-[var(--text-muted)]">{{ en ? 'When you push against a wall, it pushes back on you with an equal force in the opposite direction. Forces act in pairs.' : 'আপনি যখন দেয়ালকে ধাক্কা দেন, দেয়ালও সমান বল দিয়ে বিপরীত দিকে আপনাকে ধাক্কা দেয়। বল জোড়ায় কাজ করে।' }}</p>
                                    <div class="mt-3 flex flex-wrap gap-1.5"><span class="rounded-md bg-[#171b26] px-2 py-1.5 text-[9px] text-[#b4c5ff]">{{ en ? '+ Practice questions' : '+ অনুশীলনের প্রশ্ন' }}</span><span class="rounded-md bg-[#171b26] px-2 py-1.5 text-[9px] text-[#ffb690]">{{ en ? '+ Make flashcards' : '+ ফ্ল্যাশকার্ড তৈরি' }}</span></div>
                                </div>
                                <div class="mt-3 flex justify-end"><div class="rounded-2xl rounded-tr-sm bg-[#f97316]/15 px-3.5 py-2.5 text-xs text-[#ffb690]">{{ en ? 'Give me a question to try' : 'আমাকে অনুশীলনের জন্য একটি প্রশ্ন দাও' }}</div></div>
                                <div class="mt-4 flex items-center gap-2 rounded-lg border border-white/5 bg-[#171b26] px-3 py-2.5 text-[10px] text-[#7e8799]"><span class="material-symbols-outlined text-[#4cd7f6]">edit</span>{{ en ? 'Ask about the lesson you are studying…' : 'আপনার পড়া বিষয় নিয়ে প্রশ্ন করুন…' }}<span class="material-symbols-outlined ml-auto text-[#ff9a52]">arrow_upward</span></div>
                            </div>
                            <div class="flex items-center justify-between px-1 pt-3 text-[9px] text-[var(--text-muted)]"><span class="flex items-center gap-1"><span class="material-symbols-outlined text-[var(--tertiary)]">menu_book</span>{{ en ? 'Books · courses · study tools' : 'বই · কোর্স · স্টাডি টুল' }}</span><span>{{ en ? 'Made for focused learning' : 'মনোযোগী শেখার জন্য' }}</span></div>
                        </div>
                        <div class="absolute -bottom-5 -left-4 hidden sm:flex items-center gap-3 rounded-xl border border-white/10 bg-[#262a35]/95 px-3.5 py-3 shadow-xl"><span class="grid h-9 w-9 place-items-center rounded-lg bg-[#f97316]/15 text-[#ff9a52]"><span class="material-symbols-outlined">local_fire_department</span></span><div><div class="text-[11px] font-semibold">{{ en ? 'Learning, one step at a time' : 'ধাপে ধাপে শেখার পথে' }}</div><div class="mt-0.5 text-[9px] text-[#10b981]">{{ en ? 'Build a routine that works for you' : 'নিজের উপযোগী রুটিন গড়ুন' }}</div></div></div>
                        <div class="absolute -right-3 -top-4 hidden sm:flex items-center gap-2 rounded-xl border border-white/10 bg-[#262a35]/95 px-3 py-2.5 text-[10px] font-semibold shadow-xl"><span class="h-2 w-2 rounded-full bg-[#00f2fe]"></span>{{ en ? 'English + বাংলা' : 'বাংলা + English' }}</div>
                    </div>
                </div>
            </section>

            <!-- Reference pathway selector, populated with actual catalog categories -->
            <section class="max-w-7xl mx-auto px-4 md:px-6 pb-7">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 rounded-2xl border border-white/5 bg-[#1c1f2a]/75 p-5 md:px-7 shadow-lg backdrop-blur-md">
                    <div><span class="font-mono text-[10px] font-bold uppercase tracking-[.14em] text-[var(--primary)]">{{ en ? 'Target your goal' : 'লক্ষ্য অনুযায়ী বেছে নিন' }}</span><h2 class="mt-1 text-base font-bold">{{ pathwayLabel }}</h2></div>
                    <div class="flex flex-wrap gap-2"><Link v-for="category in categories" :key="category.id" :href="`/library?category_id=${category.id}`" class="rounded-lg border border-white/5 bg-[#0a0e18] px-3.5 py-2 text-xs font-semibold text-[var(--text-muted)] transition hover:border-[#f97316]/40 hover:text-[var(--primary)]"><span class="mr-1.5">{{ ({science:'⚛', humanities:'▤', business:'◈', novel:'✦', essay:'✎', academic:'⌂', 'non-academic':'↗'})[category.slug] || '•' }}</span>{{ category.name }}</Link><span v-if="!categories.length" class="text-xs text-[var(--text-muted)]">{{ en ? 'Browse the latest books and courses below.' : 'নিচে সর্বশেষ বই ও কোর্স দেখুন।' }}</span></div>
                </div>
            </section>

            <!-- Latest courses: same three-column card treatment as the reference, with real data -->
            <section id="courses" class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24">
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div><span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[#ff9a52]">{{ en ? 'Fresh from our mentors' : 'মেন্টরদের নতুন সংযোজন' }}</span><h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight">{{ en ? 'Latest courses' : 'সর্বশেষ কোর্স' }}</h2><p class="mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]">{{ en ? 'Explore guided lessons created to help you understand a subject, practice with purpose, and keep your learning moving.' : 'বিষয় বুঝতে, উদ্দেশ্য নিয়ে অনুশীলন করতে এবং শেখার ধারাবাহিকতা রাখতে সাজানো পাঠগুলো ঘুরে দেখুন।' }}</p></div>
                    <Link href="/courses" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--primary)] transition hover:text-[#ffb690]">{{ en ? 'View all courses' : 'সব কোর্স দেখুন' }} <span class="material-symbols-outlined">arrow_forward</span></Link>
                </div>
                <div v-if="topCourses.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <Link v-for="(course, i) in topCourses.slice(0, 3)" :key="course.id" :href="`/courses/${course.slug}`" class="group flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-[#1c1f2a]/80 shadow-lg transition duration-300 hover:-translate-y-1.5 hover:border-white/10 hover:shadow-2xl">
                        <div class="relative h-48 overflow-hidden bg-gradient-to-br" :class="coursePalette[i % coursePalette.length]">
                            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#10B981_1px,transparent_1px)] [background-size:16px_16px]"></div><img v-if="course.cover_image_url" :src="course.cover_image_url" :alt="course.title" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"><div v-else class="absolute inset-0 grid place-items-center text-6xl text-white/60"><span class="material-symbols-outlined !text-6xl">school</span></div>
                            <span class="absolute left-3 top-3 rounded-full bg-[#0a0e18]/80 px-2.5 py-1 font-mono text-[9px] font-semibold uppercase tracking-wider text-[#4cd7f6] backdrop-blur">{{ course.level || (en ? 'COURSE' : 'কোর্স') }}</span><span class="absolute right-3 top-3 rounded-full bg-[#0a0e18]/90 px-3 py-1 text-xs font-bold text-[#ffb690] backdrop-blur">{{ +course.price === 0 ? (en ? 'Free' : 'ফ্রি') : `৳${course.price}` }}</span>
                        </div>
                        <div class="flex flex-1 flex-col justify-between gap-5 p-5">
                            <div><div class="mb-2 flex items-center gap-2 text-[10px] text-[var(--text-muted)]"><span class="material-symbols-outlined text-[#ff9a52]">person</span>{{ course.mentor?.name || (en ? 'Sikhun mentor' : 'Sikhun মেন্টর') }}</div><h3 class="line-clamp-2 text-lg font-bold leading-snug transition group-hover:text-[var(--primary)]">{{ course.title }}</h3><p v-if="course.description" class="mt-2 line-clamp-2 text-xs leading-relaxed text-[var(--text-muted)]">{{ course.description }}</p></div>
                            <div class="flex items-center justify-between border-t border-white/[.06] pt-4"><span class="inline-flex items-center gap-1.5 text-xs text-[var(--text-muted)]"><span class="material-symbols-outlined">group</span>{{ course.enrollments_count || 0 }} {{ en ? 'learners' : 'জন শিক্ষার্থী' }}</span><span class="rounded-lg bg-[#262a35] px-3 py-2 text-xs font-bold transition group-hover:bg-[#f97316] group-hover:text-white">{{ en ? 'Explore course' : 'কোর্স দেখুন' }} <span aria-hidden="true">→</span></span></div>
                        </div>
                    </Link>
                </div>
                <div v-else class="rounded-2xl border border-dashed border-white/10 bg-[#1c1f2a]/60 p-12 text-center text-sm text-[var(--text-muted)]">{{ en ? 'New courses will appear here soon.' : 'নতুন কোর্স শিগগিরই এখানে যুক্ত হবে।' }}</div>
            </section>

            <!-- Four steps, following the reference's numbered cards -->
            <section class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20">
                <div class="mx-auto mb-10 max-w-2xl text-center"><span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[var(--primary)]">{{ en ? 'Get started in a few steps' : 'সহজ কয়েকটি ধাপে শুরু করুন' }}</span><h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight">{{ en ? 'How Sikhun works' : 'Sikhun যেভাবে কাজ করে' }}</h2><p class="mt-3 text-sm leading-relaxed text-[var(--text-muted)]">{{ en ? 'A flexible learning loop: discover something useful, study it with support, then practice what you have learned.' : 'সহজ শেখার ধারা: দরকারি বিষয় খুঁজুন, সহায়তা নিয়ে শিখুন, তারপর শেখা বিষয় অনুশীলন করুন।' }}</p></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <article v-for="(step, i) in steps" :key="step.title" class="group relative overflow-hidden rounded-2xl border border-white/5 bg-[#1c1f2a]/75 p-5 md:p-6 shadow-lg transition hover:-translate-y-1 hover:bg-[#262a35]">
                        <div class="flex items-center justify-between"><span class="grid h-12 w-12 place-items-center rounded-xl bg-[#f97316]/15 text-[#ff9a52]"><span class="material-symbols-outlined !text-2xl">{{ step.icon }}</span></span><span class="font-mono text-xl font-bold text-white/20">0{{ i + 1 }}</span></div><h3 class="mt-5 text-lg font-bold">{{ step.title }}</h3><p class="mt-2 min-h-[72px] text-sm leading-relaxed text-[var(--text-muted)]">{{ step.text }}</p><div class="mt-4 flex items-center gap-1 font-mono text-[9px] font-semibold uppercase tracking-wider text-[var(--primary)]">{{ step.meta }} <span class="material-symbols-outlined !text-sm">chevron_right</span></div>
                    </article>
                </div>
            </section>

            <!-- Latest books: five-cover shelf with real catalog records -->
            <section id="library" class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24">
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4"><div><span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[#4cd7f6]">{{ en ? 'Digital library · Read and explore' : 'ডিজিটাল লাইব্রেরি · পড়ুন ও জানুন' }}</span><h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight">{{ en ? 'Latest books' : 'সর্বশেষ বই' }}</h2><p class="mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]">{{ en ? 'Find a new book to read, revisit a familiar subject, or explore a topic that caught your attention.' : 'নতুন বই পড়ুন, পরিচিত বিষয় আবার দেখুন কিংবা আগ্রহ জাগানো কোনো বিষয় ঘুরে দেখুন।' }}</p></div><Link href="/library" class="inline-flex items-center gap-2 text-sm font-semibold text-[#4cd7f6] transition hover:text-white">{{ en ? 'Browse the library' : 'লাইব্রেরি দেখুন' }} <span class="material-symbols-outlined">arrow_forward</span></Link></div>
                <div v-if="popularBooks.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-5">
                    <Link v-for="(book, i) in popularBooks.slice(0, 5)" :key="book.id" :href="`/library/${book.slug}`" class="group overflow-hidden rounded-2xl border border-white/5 bg-[#1c1f2a]/80 shadow-lg transition duration-300 hover:-translate-y-1 hover:border-white/10 hover:shadow-2xl">
                        <div class="relative aspect-[3/4] overflow-hidden bg-gradient-to-br" :class="bookPalette[i % bookPalette.length]"><img v-if="book.cover_image_url" :src="book.cover_image_url" :alt="book.title" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"><div v-else class="absolute inset-0 grid place-items-center text-5xl text-white/60"><span class="material-symbols-outlined !text-5xl">menu_book</span></div><span v-if="book.is_free" class="absolute left-2.5 top-2.5 rounded-full bg-[#0e513d]/90 px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wide text-[#6ee7b7]">{{ en ? 'Free' : 'ফ্রি' }}</span><span class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-[#0a0e18]/80 to-transparent"></span></div>
                        <div class="p-3.5 md:p-4"><div class="font-mono text-[9px] uppercase tracking-wider text-[#4cd7f6]">{{ book.level || (en ? 'DIGITAL BOOK' : 'ডিজিটাল বই') }}</div><h3 class="mt-1.5 line-clamp-2 min-h-10 text-sm font-semibold leading-snug transition group-hover:text-[var(--primary)]">{{ book.title }}</h3><div class="mt-2 truncate text-[10px] text-[var(--text-muted)]">{{ book.author?.name || (en ? 'Sikhun library' : 'Sikhun লাইব্রেরি') }}</div><div v-if="book.total_pages" class="mt-1 text-[10px] text-[var(--text-muted)]">{{ book.total_pages }} {{ en ? 'pages' : 'পৃষ্ঠা' }}</div></div>
                    </Link>
                </div>
                <div v-else class="rounded-2xl border border-dashed border-white/10 bg-[#1c1f2a]/60 p-12 text-center text-sm text-[var(--text-muted)]">{{ en ? 'New books will appear here soon.' : 'নতুন বই শিগগিরই এখানে যুক্ত হবে।' }}</div>
            </section>

            <!-- AI companion feature deep-dive and sample chat terminal -->
            <section id="ai-study-hub" class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-20">
                <div class="relative overflow-hidden rounded-[2rem] border border-white/5 bg-gradient-to-b from-[#1c1f2a] to-[#171b26] p-5 md:p-9 lg:p-12 shadow-2xl">
                    <div class="pointer-events-none absolute -right-20 -bottom-20 h-96 w-96 rounded-full bg-[#f97316]/10 blur-[110px]"></div>
                    <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-10 xl:gap-14 items-center">
                        <div class="lg:col-span-6 flex flex-col items-start">
                            <div class="inline-flex items-center gap-2 rounded-full bg-[#f97316]/15 px-3.5 py-2 text-[#ff9a52]"><span class="material-symbols-outlined">smart_toy</span><span class="font-mono text-[10px] font-bold uppercase tracking-wider">{{ en ? 'AI study companion' : 'AI স্টাডি সঙ্গী' }}</span></div>
                            <h2 class="mt-5 font-heading text-3xl md:text-4xl font-bold leading-tight">{{ en ? 'A little more help, right when you need it.' : 'প্রয়োজনের মুহূর্তে পাশে থাকুক বাড়তি সহায়তা।' }}</h2>
                            <p class="mt-4 text-sm md:text-base leading-7 text-[var(--text-muted)]">{{ en ? 'Use Sikhun’s AI study features to ask about books, create practice material, reflect on your writing, and make a study plan that feels manageable.' : 'বই নিয়ে প্রশ্ন করতে, অনুশীলনের উপকরণ তৈরি করতে, লেখার ফিডব্যাক পেতে এবং বাস্তবসম্মত পড়ার পরিকল্পনা সাজাতে Sikhun-এর AI স্টাডি ফিচার ব্যবহার করুন।' }}</p>
                            <div class="mt-6 flex flex-col gap-4">
                                <div v-for="feature in features" :key="feature[1]" class="flex items-start gap-3"><span class="material-symbols-outlined mt-0.5 text-[#10b981]">check_circle</span><div><div class="text-sm font-semibold">{{ feature[1] }}</div><div class="mt-1 text-xs leading-relaxed text-[var(--text-muted)]">{{ feature[2] }}</div></div></div>
                            </div>
                            <Link href="/register" class="mt-7 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#ff7a00] to-[#f97316] px-5 py-3 text-sm font-bold text-white shadow-lg transition hover:shadow-[0_0_28px_rgba(249,115,22,.3)]">{{ en ? 'Explore AI study tools' : 'AI স্টাডি টুল দেখুন' }} <span class="material-symbols-outlined">arrow_forward</span></Link>
                        </div>
                        <div class="lg:col-span-6 rounded-2xl border border-white/5 bg-[#0a0e18] p-3.5 md:p-5 shadow-2xl">
                            <div class="flex items-center justify-between border-b border-white/5 px-1 pb-3"><div class="flex items-center gap-2"><span class="grid h-8 w-8 place-items-center rounded-lg bg-[#f97316]/15 text-[#ff9a52]"><span class="material-symbols-outlined">neurology</span></span><div><div class="text-xs font-bold">{{ en ? 'Sikhun AI study preview' : 'Sikhun AI স্টাডি প্রিভিউ' }}</div><div class="font-mono text-[9px] text-[var(--text-muted)]">{{ en ? 'Example conversation' : 'নমুনা কথোপকথন' }}</div></div></div><span class="rounded-full bg-[#262a35] px-2.5 py-1 font-mono text-[9px] text-[#4cd7f6]">{{ en ? 'SAMPLE' : 'নমুনা' }}</span></div>
                            <div class="mt-4 flex justify-end"><div class="max-w-[85%] rounded-2xl rounded-tr-none bg-[#262a35] px-3.5 py-2.5 text-xs leading-relaxed">{{ en ? 'Explain Newton’s laws with simple examples.' : 'সহজ উদাহরণ দিয়ে নিউটনের সূত্রগুলো বুঝিয়ে বলো।' }}</div></div>
                            <div class="mt-3 max-w-[96%] rounded-2xl rounded-tl-none bg-[#1c1f2a] p-4 shadow-lg"><div class="mb-2 flex items-center gap-1.5 text-xs font-semibold text-[#ff9a52]"><span class="material-symbols-outlined">bolt</span>{{ en ? 'Let’s break it down' : 'চলুন সহজভাবে বুঝি' }}</div><p class="text-[11px] leading-5 text-[var(--text-muted)]"><strong class="text-[var(--text)]">{{ en ? 'First law:' : 'প্রথম সূত্র:' }}</strong> {{ en ? 'An object keeps its state of rest or motion unless a force changes it.' : 'কোনো বস্তুর ওপর বল প্রয়োগ না হলে স্থির বা গতিশীল অবস্থায় থাকে।' }}<br><strong class="text-[var(--text)]">{{ en ? 'Second law:' : 'দ্বিতীয় সূত্র:' }}</strong> {{ en ? 'A greater force causes greater acceleration for the same mass.' : 'একই ভরের ক্ষেত্রে বেশি বল বেশি ত্বরণ সৃষ্টি করে।' }}<br><strong class="text-[var(--text)]">{{ en ? 'Third law:' : 'তৃতীয় সূত্র:' }}</strong> {{ en ? 'Forces come in equal and opposite pairs.' : 'বল সমান ও বিপরীত জোড়ায় কাজ করে।' }}</p><div class="mt-3 flex flex-wrap gap-1.5"><span class="rounded-md bg-[#171b26] px-2 py-1.5 text-[9px] text-[#b4c5ff]">{{ en ? 'Create practice questions' : 'অনুশীলনের প্রশ্ন' }}</span><span class="rounded-md bg-[#171b26] px-2 py-1.5 text-[9px] text-[#10b981]">{{ en ? 'Save as a flashcard' : 'ফ্ল্যাশকার্ডে রাখুন' }}</span></div></div>
                            <div class="mt-3 flex justify-end"><div class="rounded-2xl rounded-tr-none bg-[#f97316]/15 px-3.5 py-2.5 text-xs text-[#ffb690]">{{ en ? 'Give me a multiple-choice question' : 'আমাকে একটি বহুনির্বাচনী প্রশ্ন দাও' }}</div></div>
                            <div class="mt-3 rounded-2xl rounded-tl-none bg-[#1c1f2a] p-4"><span class="font-mono text-[9px] font-bold tracking-wider text-[#10b981]">{{ en ? 'PRACTICE QUESTION' : 'অনুশীলনের প্রশ্ন' }}</span><p class="mt-2 text-xs font-semibold leading-relaxed">{{ en ? 'Which equation describes the relationship between force, mass, and acceleration?' : 'বল, ভর ও ত্বরণের সম্পর্ক কোন সমীকরণে প্রকাশ করা হয়?' }}</p><div class="mt-3 grid grid-cols-2 gap-2"><span v-for="answer in ['F = ma', 'v = u + at', 'W = mg', 'P = F/A']" :key="answer" class="rounded-lg bg-[#171b26] px-3 py-2 text-[10px] text-[var(--text-muted)]">{{ answer }}</span></div></div>
                            <div class="mt-4 flex items-center gap-2 rounded-lg border border-white/5 bg-[#171b26] px-3 py-2.5 text-[10px] text-[#7e8799]"><span class="material-symbols-outlined text-[#4cd7f6]">edit</span>{{ en ? 'Ask about what you are studying…' : 'আপনার পড়া বিষয় নিয়ে প্রশ্ন করুন…' }}<span class="material-symbols-outlined ml-auto text-[#ff9a52]">send</span></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Current plan data rendered in the reference's three-tier layout -->
            <section id="pricing" class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24">
                <div class="mx-auto mb-10 max-w-2xl text-center">
                    <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-4 py-1.5 text-xs font-bold text-emerald-400 mb-3">
                        <span class="material-symbols-outlined !text-sm text-emerald-400">verified</span>
                        <span>{{ en ? 'Start with a 7-day Free Trial — No card required' : '৭ দিনের সম্পূর্ণ ফ্রি ট্রায়াল দিয়ে শুরু করুন — কোনো কার্ড প্রয়োজন নেই' }}</span>
                    </div>
                    <h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight">{{ en ? 'Choose the support that fits you' : 'আপনার উপযোগী সহায়তা বেছে নিন' }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-[var(--text-muted)]">{{ en ? 'Compare the available plans and choose what works for your learning goals.' : 'উপলভ্য প্ল্যানগুলো দেখে আপনার শেখার লক্ষ্যের সাথে মানানসইটি বেছে নিন।' }}</p>
                </div>
                <div v-if="plans.length" class="grid grid-cols-1 md:grid-cols-3 gap-5 items-stretch">
                    <article v-for="(plan, i) in plans.slice(0, 3)" :key="plan.id" class="relative flex flex-col justify-between rounded-3xl border p-6 md:p-7 shadow-lg transition duration-300 hover:-translate-y-1" :class="i === 1 ? 'border-[#f97316]/40 bg-[#262a35] shadow-[0_24px_60px_rgba(0,0,0,.35)]' : 'border-white/5 bg-[#1c1f2a]/75'">
                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-lg font-bold">{{ plan.name }}</h3>
                                <span class="rounded bg-[#0a0e18] px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wider" :class="i === 1 ? 'text-[#ff9a52]' : 'text-[var(--text-muted)]'">
                                    {{ i === 0 ? (en ? 'STANDARD' : 'স্ট্যান্ডার্ড') : i === 1 ? (en ? 'POPULAR' : 'জনপ্রিয়') : (en ? 'ELITE' : 'এলিট') }}
                                </span>
                            </div>
                            <p v-if="plan.description" class="mt-2 min-h-10 text-xs leading-relaxed text-[var(--text-muted)]">{{ plan.description }}</p>
                            <div class="my-5 flex flex-col">
                                <div class="flex items-baseline gap-1">
                                    <span class="font-heading text-4xl font-extrabold tracking-tight" :class="i === 1 ? 'text-[#ff9a52]' : ''">৳{{ plan.price_monthly }}</span>
                                    <span class="text-sm text-[var(--text-muted)]">{{ en ? '/ month' : '/ মাস' }}</span>
                                </div>
                                <span class="text-[11px] font-medium text-emerald-400 mt-1">
                                    ✓ {{ en ? 'Includes 7-day free trial' : '৭ দিনের ফ্রি ট্রায়াল অন্তর্ভুক্ত' }}
                                </span>
                            </div>
                            <div class="flex flex-col gap-3 text-xs text-[var(--text-muted)]">
                                <div v-for="feature in (Array.isArray(plan.features) ? plan.features : [])" :key="feature" class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined !text-base text-[#10b981]">check_circle</span>
                                    <span>{{ feature }}</span>
                                </div>
                            </div>
                        </div>
                        <Link href="/register" class="mt-7 flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold transition" :class="i === 1 ? 'bg-gradient-to-r from-[#ff7a00] to-[#f97316] text-white shadow-lg shadow-[#f97316]/20' : 'bg-[#313540] text-[var(--text)] hover:bg-[#3b404d]'">
                            {{ en ? 'Start 7-Day Free Trial' : '৭ দিনের ফ্রি ট্রায়াল শুরু করুন' }} <span aria-hidden="true">→</span>
                        </Link>
                    </article>
                </div>
                <div v-else class="rounded-2xl border border-dashed border-white/10 bg-[#1c1f2a]/60 p-12 text-center text-sm text-[var(--text-muted)]">{{ en ? 'Plans will be available here soon.' : 'প্ল্যান শিগগিরই এখানে যুক্ত হবে।' }}</div>
            </section>

            <!-- Latest Blog Posts -->
            <section v-if="latestBlogs?.length" id="blog" class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24">
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[#10b981]">{{ en ? 'Insights · Science · AI · Education' : 'বিজ্ঞান · প্রযুক্তি · AI · শিক্ষা' }}</span>
                        <h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight">{{ en ? 'Latest from the blog' : 'সর্বশেষ ব্লগ আর্টিকেল' }}</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]">{{ en ? 'Read Bengali articles on science, technology, artificial intelligence, and study tips.' : 'বিজ্ঞান, প্রযুক্তি, কৃত্রিম বুদ্ধিমত্তা ও পড়াশোনার কৌশল নিয়ে আমাদের সাম্প্রতিক বাংলা আর্টিকেলগুলো পড়ুন।' }}</p>
                    </div>
                    <Link href="/blog" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--primary)] transition hover:text-[#ffb690]">{{ en ? 'Read all articles' : 'সব ব্লগ পড়ুন' }} <span class="material-symbols-outlined">arrow_forward</span></Link>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <Link v-for="(post, i) in latestBlogs.slice(0, 3)" :key="post.id" :href="`/blog/${post.slug}`" class="group flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-[#1c1f2a]/80 shadow-lg transition duration-300 hover:-translate-y-1.5 hover:border-white/10 hover:shadow-2xl">
                        <div class="relative h-44 overflow-hidden bg-gradient-to-br" :class="coursePalette[i % coursePalette.length]">
                            <img v-if="post.featured_image_url" :src="post.featured_image_url" :alt="post.title" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            <div v-else class="absolute inset-0 grid place-items-center text-5xl text-white/60"><span class="material-symbols-outlined !text-5xl">article</span></div>
                            <span v-if="post.category" class="absolute left-3 top-3 rounded-full bg-[#0a0e18]/85 px-2.5 py-1 font-mono text-[9px] font-semibold uppercase tracking-wider text-[#4cd7f6] backdrop-blur">{{ post.category.name }}</span>
                        </div>
                        <div class="flex flex-1 flex-col justify-between gap-4 p-5">
                            <div>
                                <h3 class="line-clamp-2 text-lg font-bold leading-snug transition group-hover:text-[var(--primary)]">{{ post.title }}</h3>
                                <p v-if="post.excerpt" class="mt-2 line-clamp-3 text-xs leading-relaxed text-[var(--text-muted)]">{{ post.excerpt }}</p>
                            </div>
                            <div class="flex items-center justify-between border-t border-white/[.06] pt-3 text-xs text-[var(--text-muted)]">
                                <span>{{ post.published_at ? new Date(post.published_at).toLocaleDateString(en ? 'en-GB' : 'bn-BD') : '' }}</span>
                                <span class="font-semibold text-[var(--primary)]">{{ en ? 'Read more →' : 'বিস্তারিত পড়ুন →' }}</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </section>

            <!-- Closing call to action -->
            <section class="max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20">
                <div class="relative flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8 overflow-hidden rounded-[2rem] border border-white/5 bg-gradient-to-r from-[#1c1f2a] via-[#262a35] to-[#1c1f2a] p-7 md:p-10 xl:p-12 shadow-2xl">
                    <div class="pointer-events-none absolute -right-12 -top-20 h-72 w-72 rounded-full bg-[#f97316]/15 blur-[90px]"></div><div class="pointer-events-none absolute bottom-0 left-16 h-52 w-52 rounded-full bg-[#2563eb]/15 blur-[90px]"></div>
                    <div class="relative max-w-2xl text-[var(--text)]"><span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[#ff9a52]">{{ en ? 'Start today' : 'আজই শুরু করুন' }}</span><h2 class="mt-2 font-heading text-3xl md:text-4xl font-bold">{{ en ? 'Make your next study session count.' : 'পরের পড়ার সময়টুকু কাজে লাগান।' }}</h2><p class="mt-3 text-sm leading-relaxed text-[var(--text-muted)]">{{ en ? 'Find a book, explore a course, or start with a question. Your next step can be a small one.' : 'একটি বই খুঁজুন, কোর্স দেখুন কিংবা একটি প্রশ্ন দিয়ে শুরু করুন। পরের পদক্ষেপটি ছোট হলেও যথেষ্ট।' }}</p><div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-xs text-[var(--text-muted)]"><span class="flex items-center gap-1.5"><span class="material-symbols-outlined !text-sm text-[#10b981]">verified</span>{{ en ? 'Bilingual learning space' : 'দ্বিভাষিক শেখার পরিবেশ' }}</span><span class="flex items-center gap-1.5"><span class="material-symbols-outlined !text-sm text-[#10b981]">verified</span>{{ en ? 'Books and guided courses' : 'বই ও নির্দেশিত কোর্স' }}</span><span class="flex items-center gap-1.5"><span class="material-symbols-outlined !text-sm text-[#10b981]">verified</span>{{ en ? 'Study tools when you need them' : 'প্রয়োজনে স্টাডি টুল' }}</span></div></div>
                    <div class="relative flex shrink-0 flex-wrap gap-3"><Link href="/register" class="inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-[#ff7a00] to-[#f97316] px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-[#f97316]/20 transition hover:scale-[1.02]">{{ en ? 'Create a free account' : 'ফ্রি অ্যাকাউন্ট তৈরি করুন' }} <span class="material-symbols-outlined">arrow_forward</span></Link><Link href="/courses" class="inline-flex items-center justify-center rounded-full bg-[#0a0e18] px-5 py-3.5 text-sm font-semibold text-[var(--text)] transition hover:bg-[#313540]">{{ en ? 'Browse courses' : 'কোর্স দেখুন' }}</Link></div>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
