<script setup>
import { computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import EducativeHeroScene from '@/Components/Three/EducativeHeroScene.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    stats: Object, categories: Array, topCourses: Array, popularBooks: Array,
    latestBlogs: Array, plans: Array, testimonials: Array, seo: Object,
    featuredJobs: Array, featuredAdmissions: Array,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');
let revealObserver;

const paths = computed(() => en.value ? [
    { number: '01', title: 'Read with clarity', detail: 'Books, made easier to understand.', href: '/library', action: 'Open the library' },
    { number: '02', title: 'Practice with purpose', detail: 'Past questions. Useful explanations.', href: '/question-bank', action: 'Try a question' },
    { number: '03', title: 'Learn with guidance', detail: 'Thoughtful lessons from mentors.', href: '/courses', action: 'Explore courses' },
] : [
    { number: '০১', title: 'বুঝে পড়ুন', detail: 'বই হোক আরও সহজবোধ্য।', href: '/library', action: 'লাইব্রেরি দেখুন' },
    { number: '০২', title: 'উদ্দেশ্য নিয়ে অনুশীলন', detail: 'বিগত প্রশ্ন, প্রয়োজনীয় ব্যাখ্যা।', href: '/question-bank', action: 'প্রশ্ন অনুশীলন করুন' },
    { number: '০৩', title: 'পরামর্শ নিয়ে শিখুন', detail: 'অভিজ্ঞ মেন্টরের সাজানো পাঠ।', href: '/courses', action: 'কোর্স দেখুন' },
]);

const studySteps = computed(() => en.value ? [
    { title: 'Learn the idea', detail: 'Read a chapter or ask the AI study companion to explain a difficult part.', href: '/library', action: 'Choose a book' },
    { title: 'Try it yourself', detail: 'Practice admission and university questions, then review each explanation.', href: '/question-bank', action: 'Practice questions' },
    { title: 'See what improved', detail: 'Keep your sessions, exams and goals together so the next step feels clear.', href: '/register', action: 'Build your study desk' },
] : [
    { title: 'বিষয়টি বুঝে নিন', detail: 'অধ্যায় পড়ুন, কঠিন অংশে AI সহায়কের কাছে ব্যাখ্যা চান।', href: '/library', action: 'বই বেছে নিন' },
    { title: 'নিজে অনুশীলন করুন', detail: 'ভর্তি ও বিশ্ববিদ্যালয়ের প্রশ্ন সমাধান করে ব্যাখ্যাগুলো দেখুন।', href: '/question-bank', action: 'প্রশ্ন অনুশীলন' },
    { title: 'উন্নতি দেখে এগোন', detail: 'পড়ার সেশন, পরীক্ষা ও লক্ষ্য একসাথে রাখুন—পরের ধাপ হবে স্পষ্ট।', href: '/register', action: 'নিজের স্টাডি ডেস্ক খুলুন' },
]);

const platformStats = computed(() => [
    { value: props.stats?.books ?? 0, label: en.value ? 'books to explore' : 'টি বইয়ের সংগ্রহ' },
    { value: props.stats?.courses ?? 0, label: en.value ? 'mentor-led courses' : 'টি মেন্টর পরিচালিত কোর্স' },
    { value: props.stats?.students ?? 0, label: en.value ? 'active learners' : 'জন সক্রিয় শিক্ষার্থী' },
]);

function formatCount(value) {
    return Number(value || 0).toLocaleString(en.value ? 'en-US' : 'bn-BD');
}

onMounted(() => {
    const nodes = document.querySelectorAll('.home-reveal');
    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        nodes.forEach((node) => node.classList.add('is-visible'));
        return;
    }
    revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            revealObserver.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -32px 0px' });
    nodes.forEach((node) => revealObserver.observe(node));
});

onUnmounted(() => revealObserver?.disconnect());
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <div class="home-design">
            <section class="home-hero mx-auto grid max-w-7xl items-center gap-5 px-5 pb-12 pt-12 sm:px-8 sm:pb-16 sm:pt-16 lg:grid-cols-2 lg:gap-12 lg:pt-20">
                <div class="relative z-10 home-reveal">
                    <p class="home-kicker">{{ en ? 'A quieter way to make progress' : 'মনোযোগ দিয়ে শেখার একটি সহজ ঠিকানা' }}</p>
                    <h1 class="home-title mt-5 max-w-2xl">{{ en ? 'Make room for deeper learning.' : 'শেখার জন্য একটু মনোযোগী পরিসর।' }}</h1>
                    <p class="home-lede mt-5 max-w-xl">{{ en ? 'Read, practice and ask better questions—with a study companion that keeps the important things together.' : 'পড়ুন, অনুশীলন করুন, প্রশ্ন করুন—পড়াশোনার দরকারি সবকিছু থাকুক একই পরিসরে।' }}</p>
                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        <Link href="/register" class="home-primary">{{ en ? 'Begin studying' : 'শেখা শুরু করুন' }} <span aria-hidden="true">↗</span></Link>
                        <Link href="/courses" class="home-secondary">{{ en ? 'Explore learning' : 'শেখার উপকরণ দেখুন' }}</Link>
                    </div>
                    <div class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-[var(--text-muted)]">
                        <span>{{ en ? 'Books' : 'বই' }}</span><span class="home-dot">·</span>
                        <span>{{ en ? 'Mentor-led courses' : 'মেন্টর পরিচালিত কোর্স' }}</span><span class="home-dot">·</span>
                        <span>{{ en ? 'AI study tools' : 'AI স্টাডি টুল' }}</span>
                    </div>
                </div>
                <div class="home-scene home-reveal" aria-label="Animated study scene">
                    <EducativeHeroScene :locale="locale" />
                    <div class="home-note"><span class="home-note-mark">✳</span>{{ en ? 'A little progress, every day.' : 'প্রতিদিন একটু করে এগিয়ে চলুন।' }}</div>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
                <div class="home-section-head home-reveal">
                    <p class="home-kicker">{{ en ? 'A thoughtful study rhythm' : 'পড়াশোনার সহজ ছন্দ' }}</p>
                    <h2 class="home-heading mt-2">{{ en ? 'Start where you are.' : 'যেখান থেকে শুরু করতে চান।' }}</h2>
                </div>
                <div class="mt-6 grid gap-3 md:grid-cols-3">
                    <Link v-for="path in paths" :key="path.number" :href="path.href" class="home-path home-reveal">
                        <span class="home-path-number">{{ path.number }}</span>
                        <h3>{{ path.title }}</h3>
                        <p>{{ path.detail }}</p>
                        <span class="home-path-action">{{ path.action }} <span aria-hidden="true">→</span></span>
                    </Link>
                </div>
            </section>

            <section class="home-shelf mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
                <div class="home-reveal">
                    <p class="home-kicker">{{ en ? 'A study rhythm that makes sense' : 'পড়াশোনার কাজে লাগা ছন্দ' }}</p>
                    <h2 class="home-heading mt-2">{{ en ? 'Understand. Practice. Improve.' : 'বুঝুন। অনুশীলন করুন। এগিয়ে যান।' }}</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)] sm:text-base">{{ en ? 'Good preparation is more than collecting materials. Sikhun brings reading, deliberate practice and progress into one simple routine.' : 'ভালো প্রস্তুতি মানে শুধু উপকরণ জড়ো করা নয়। শিখুনে পড়া, মনোযোগী অনুশীলন ও অগ্রগতি—সবকিছু সাজানো থাকে একটি সহজ রুটিনে।' }}</p>
                </div>
                <div class="mt-6 grid gap-3 md:grid-cols-3">
                    <article v-for="(step, index) in studySteps" :key="step.title" class="home-step home-reveal">
                        <span class="home-path-number">{{ en ? `STEP 0${index + 1}` : `ধাপ ০${formatCount(index + 1)}` }}</span>
                        <h3>{{ step.title }}</h3>
                        <p>{{ step.detail }}</p>
                        <Link :href="step.href" class="home-path-action">{{ step.action }} →</Link>
                    </article>
                </div>
            </section>

            <section v-if="stats" class="home-stats mx-auto max-w-7xl px-5 py-9 sm:px-8 sm:py-12">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div v-for="stat in platformStats" :key="stat.label" class="home-stat home-reveal">
                        <strong>{{ formatCount(stat.value) }}</strong>
                        <span>{{ stat.label }}</span>
                    </div>
                </div>
            </section>

            <section v-if="topCourses?.length" class="home-shelf mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
                <div class="flex flex-wrap items-end justify-between gap-3 home-reveal">
                    <div><p class="home-kicker">{{ en ? 'Learn with a mentor' : 'মেন্টরের সঙ্গে শিখুন' }}</p><h2 class="home-heading mt-2">{{ en ? 'A good place to continue.' : 'এগিয়ে যাওয়ার ভালো সঙ্গী।' }}</h2></div>
                    <Link href="/courses" class="home-text-link">{{ en ? 'All courses' : 'সব কোর্স' }} →</Link>
                </div>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link v-for="course in topCourses.slice(0, 3)" :key="course.id" :href="`/courses/${course.slug}`" class="home-course home-reveal">
                        <img v-if="course.cover_image_url" :src="course.cover_image_url" :alt="course.title" loading="lazy" />
                        <div v-else class="home-course-cover"><span>✳</span></div>
                        <div class="p-4"><p class="home-course-meta">{{ course.mentor?.name || (en ? 'Mentor-led course' : 'মেন্টর পরিচালিত কোর্স') }}</p><h3>{{ course.title }}</h3><p class="home-course-price">{{ Number(course.price) === 0 ? (en ? 'Free' : 'বিনামূল্যে') : `৳${course.price}` }}</p></div>
                    </Link>
                </div>
            </section>

            <section v-if="popularBooks?.length" class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
                <div class="flex flex-wrap items-end justify-between gap-3 home-reveal">
                    <div><p class="home-kicker">{{ en ? 'From the library' : 'লাইব্রেরি থেকে' }}</p><h2 class="home-heading mt-2">{{ en ? 'Keep a good book close.' : 'পছন্দের বই পাশে রাখুন।' }}</h2></div>
                    <Link href="/library" class="home-text-link">{{ en ? 'Browse books' : 'বই দেখুন' }} →</Link>
                </div>
                <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <Link v-for="book in popularBooks.slice(0, 4)" :key="book.id" :href="`/library/${book.slug}`" class="home-book home-reveal">
                        <img v-if="book.cover_image_url" :src="book.cover_image_url" :alt="book.title" loading="lazy" />
                        <span v-else class="home-book-placeholder">{{ en ? 'READ' : 'পড়ুন' }}</span>
                        <span class="home-book-title">{{ book.title }}</span>
                    </Link>
                </div>
            </section>

            <section v-if="featuredAdmissions?.length || featuredJobs?.length" class="home-shelf mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
                <div class="flex flex-wrap items-end justify-between gap-3 home-reveal">
                    <div><p class="home-kicker">{{ en ? 'Keep your next step in view' : 'পরের সুযোগটির খোঁজ রাখুন' }}</p><h2 class="home-heading mt-2">{{ en ? 'Notices worth a look.' : 'দরকারি বিজ্ঞপ্তিগুলো এক নজরে।' }}</h2></div>
                    <div class="flex gap-4"><Link href="/admission-notice" class="home-text-link">{{ en ? 'Admissions' : 'ভর্তি বিজ্ঞপ্তি' }} →</Link><Link href="/job-news" class="home-text-link">{{ en ? 'Jobs' : 'চাকরির খবর' }} →</Link></div>
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="home-notice home-reveal">
                        <p class="home-course-meta">{{ en ? 'ADMISSIONS' : 'ভর্তি' }}</p>
                        <Link v-for="notice in featuredAdmissions.slice(0, 2)" :key="notice.id" :href="`/admission-notice/${notice.slug}`" class="home-notice-row"><span>{{ notice.institution_name || notice.title }}</span><span aria-hidden="true">→</span></Link>
                        <Link v-if="!featuredAdmissions.length" href="/admission-notice" class="home-notice-row">{{ en ? 'See current admission notices' : 'সাম্প্রতিক ভর্তি বিজ্ঞপ্তি দেখুন' }} →</Link>
                    </div>
                    <div class="home-notice home-reveal">
                        <p class="home-course-meta">{{ en ? 'CAREER' : 'ক্যারিয়ার' }}</p>
                        <Link v-for="job in featuredJobs.slice(0, 2)" :key="job.id" :href="`/job-news/${job.slug}`" class="home-notice-row"><span>{{ job.title }}</span><span aria-hidden="true">→</span></Link>
                        <Link v-if="!featuredJobs.length" href="/job-news" class="home-notice-row">{{ en ? 'Explore recent job circulars' : 'সাম্প্রতিক চাকরির সার্কুলার দেখুন' }} →</Link>
                    </div>
                </div>
            </section>

            <section v-if="latestBlogs?.length" class="home-shelf mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
                <div class="flex flex-wrap items-end justify-between gap-3 home-reveal">
                    <div><p class="home-kicker">{{ en ? 'Ideas for curious minds' : 'জানার আগ্রহে আরও কিছু' }}</p><h2 class="home-heading mt-2">{{ en ? 'Read beyond the syllabus.' : 'পাঠ্যবইয়ের বাইরেও জানুন।' }}</h2></div>
                    <Link href="/blog" class="home-text-link">{{ en ? 'All articles' : 'সব লেখা' }} →</Link>
                </div>
                <div class="mt-5 grid gap-3 md:grid-cols-3">
                    <Link v-for="post in latestBlogs.slice(0, 3)" :key="post.id" :href="`/blog/${post.slug}`" class="home-reading home-reveal">
                        <p class="home-course-meta">{{ post.category?.name_en || post.category?.name_bn || (en ? 'Sikhun journal' : 'শিখুন জার্নাল') }}</p>
                        <h3>{{ post.title }}</h3>
                        <p class="home-reading-excerpt">{{ post.excerpt }}</p>
                        <span class="home-path-action">{{ en ? 'Read article' : 'লেখাটি পড়ুন' }} →</span>
                    </Link>
                </div>
            </section>

            <section class="home-closing mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16">
                <div class="home-closing-inner home-reveal">
                    <div><p class="home-kicker">{{ en ? 'Your next chapter starts here' : 'আপনার পরের অধ্যায় শুরু হোক' }}</p><h2 class="home-heading mt-2">{{ en ? 'One focused step at a time.' : 'একবারে একটি মনোযোগী পদক্ষেপ।' }}</h2></div>
                    <Link href="/register" class="home-primary">{{ en ? 'Create a free account' : 'বিনামূল্যে অ্যাকাউন্ট খুলুন' }} <span aria-hidden="true">↗</span></Link>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>

<style>
.home-design {
    --home-accent: #8a5a32;
    --home-accent-soft: color-mix(in srgb, var(--home-accent) 10%, var(--surface));
    background: var(--bg);
    color: var(--text);
}
.dark .home-design { --home-accent: #d2ad7c; }
.home-hero { min-height: 480px; }
.home-title, .home-heading { font-family: 'Playfair Display', 'Noto Serif Bengali', Georgia, serif; }
.home-title { max-width: 12ch; font-size: clamp(2.7rem, 6vw, 5.2rem); font-weight: 600; letter-spacing: -.045em; line-height: 1.06; }
.home-heading { font-size: clamp(1.65rem, 3.2vw, 2.5rem); font-weight: 600; letter-spacing: -.025em; line-height: 1.2; }
.home-lede { color: var(--text-muted); font-size: clamp(1rem, 1.4vw, 1.12rem); line-height: 1.8; }
.home-kicker { color: var(--home-accent); font-size: .72rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
.home-primary, .home-secondary { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; gap: .65rem; border-radius: 999px; padding: .7rem 1.25rem; font-size: .9rem; font-weight: 650; transition: transform .2s ease, background .2s ease; }
.home-primary { background: var(--text); color: var(--bg); }
.home-primary:hover, .home-secondary:hover { transform: translateY(-2px); }
.home-secondary { border: 1px solid var(--border); color: var(--text); }
.home-secondary:hover { background: var(--surface2); }
.home-dot { color: var(--home-accent); }
.home-scene { position: relative; min-width: 0; }
.home-scene > div:first-child { height: clamp(290px, 39vw, 440px); }
.home-note { position: absolute; bottom: 7%; left: 8%; display: flex; align-items: center; gap: .6rem; border: 1px solid var(--border); border-radius: 999px; background: var(--surface); padding: .65rem .9rem; color: var(--text); font-size: .8rem; box-shadow: var(--shadow); }
.home-note-mark { color: var(--home-accent); }
.home-section-head { max-width: 40rem; }
.home-path { display: flex; min-height: 206px; flex-direction: column; border: 1px solid var(--border); border-radius: 1rem; background: var(--surface); padding: 1.2rem; transition: border-color .2s ease, transform .2s ease, background .2s ease; }
.home-path:hover { transform: translateY(-3px); border-color: color-mix(in srgb, var(--home-accent) 38%, var(--border)); background: var(--home-accent-soft); }
.home-path-number, .home-course-meta { color: var(--home-accent); font-size: .72rem; font-weight: 700; letter-spacing: .12em; }
.home-path h3 { margin-top: 1.5rem; font-family: 'Playfair Display', 'Noto Serif Bengali', Georgia, serif; font-size: 1.25rem; font-weight: 600; }
.home-path p { margin-top: .35rem; color: var(--text-muted); font-size: .9rem; }
.home-path-action { margin-top: auto; padding-top: 1.1rem; color: var(--text); font-size: .8rem; font-weight: 650; }
.home-step, .home-reading, .home-notice { display: flex; flex-direction: column; border: 1px solid var(--border); border-radius: 1rem; background: var(--surface); padding: 1.15rem; }
.home-step { min-height: 205px; }
.home-step h3, .home-reading h3 { margin-top: 1rem; color: var(--text); font-family: 'Playfair Display', 'Noto Serif Bengali', Georgia, serif; font-size: 1.1rem; font-weight: 600; line-height: 1.4; }
.home-step > p, .home-reading-excerpt { margin-top: .45rem; color: var(--text-muted); font-size: .86rem; line-height: 1.65; }
.home-step .home-path-action { margin-top: auto; }
.home-stats { border-block: 1px solid var(--border); }
.home-stat { display: flex; align-items: baseline; justify-content: center; gap: .5rem; text-align: center; }
.home-stat strong { color: var(--home-accent); font-family: 'Playfair Display', 'Noto Serif Bengali', Georgia, serif; font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 600; }
.home-stat span { color: var(--text-muted); font-size: .82rem; }
.home-notice { gap: .45rem; }
.home-notice-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; border-top: 1px solid var(--border); padding-top: .7rem; color: var(--text); font-size: .88rem; font-weight: 550; }
.home-notice-row:hover { color: var(--home-accent); }
.home-reading { min-height: 205px; transition: border-color .2s ease, transform .2s ease; }
.home-reading:hover { transform: translateY(-2px); border-color: color-mix(in srgb, var(--home-accent) 38%, var(--border)); }
.home-reading-excerpt { display: -webkit-box; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 3; }
.home-reading .home-path-action { margin-top: auto; }
.home-text-link { color: var(--home-accent); font-size: .9rem; font-weight: 650; }
.home-shelf, .home-closing { border-top: 1px solid var(--border); }
.home-course { overflow: hidden; border: 1px solid var(--border); border-radius: 1rem; background: var(--surface); transition: transform .2s ease, box-shadow .2s ease; }
.home-course:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
.home-course > img, .home-course-cover { width: 100%; height: 150px; object-fit: cover; background: var(--surface2); }
.home-course-cover { display: grid; place-items: center; color: var(--home-accent); font-size: 2.5rem; }
.home-course h3 { margin-top: .45rem; color: var(--text); font-size: 1rem; font-weight: 650; line-height: 1.5; }
.home-course-price { margin-top: .55rem; color: var(--text-muted); font-size: .83rem; }
.home-book { display: flex; min-height: 100px; align-items: center; gap: .9rem; border-bottom: 1px solid var(--border); padding: .6rem; }
.home-book img, .home-book-placeholder { width: 56px; height: 74px; flex: none; border-radius: .35rem; object-fit: cover; }
.home-book-placeholder { display: grid; place-items: center; background: var(--surface2); color: var(--home-accent); font-family: 'Playfair Display', serif; font-size: .75rem; }
.home-book-title { color: var(--text); font-size: .9rem; font-weight: 600; line-height: 1.45; }
.home-closing-inner { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.5rem; border-radius: 1.25rem; background: var(--surface2); padding: clamp(1.25rem, 4vw, 2.5rem); }
.home-reveal { opacity: 0; transform: translateY(16px); transition: opacity .65s ease, transform .65s cubic-bezier(.2,.75,.25,1); }
.home-reveal.is-visible { opacity: 1; transform: translateY(0); }
@media (max-width: 640px) { .home-hero { min-height: unset; } .home-title { max-width: 11ch; } .home-note { left: 2%; bottom: 4%; font-size: .72rem; } }
@media (prefers-reduced-motion: reduce) { .home-reveal { opacity: 1; transform: none; transition: none; } }
</style>
