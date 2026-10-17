<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    post: Object,
    related: Array,
    featuredBooks: Array,
    featuredCourses: Array,
    seo: Object,
});

const { t, locale } = useI18n();
const en = computed(() => locale.value === 'en');
const copied = ref(false);

function copyShareLink() {
    if (typeof window === 'undefined') return;
    navigator.clipboard?.writeText(window.location.href);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
}
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <article class="max-w-4xl mx-auto px-4 md:px-6 py-10 md:py-14">
            <!-- Breadcrumb -->
            <nav class="flex flex-wrap items-center gap-2 text-xs text-[var(--text-muted)] mb-6">
                <Link href="/" class="hover:text-[var(--primary)]">{{ en ? 'Home' : 'হোম' }}</Link>
                <span>/</span>
                <Link href="/blog" class="hover:text-[var(--primary)]">{{ t('nav.blog') }}</Link>
                <template v-if="post.category">
                    <span>/</span>
                    <Link :href="`/blog/category/${post.category.slug}`" class="hover:text-[var(--primary)]">
                        {{ post.category.name }}
                    </Link>
                </template>
            </nav>

            <!-- Article Header -->
            <header class="mb-8">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <Link
                        v-if="post.category"
                        :href="`/blog/category/${post.category.slug}`"
                        class="rounded-full bg-[var(--primary)]/15 px-3.5 py-1 text-xs font-semibold text-[var(--primary)]"
                    >
                        {{ post.category.name }}
                    </Link>
                    <span v-if="post.published_at" class="text-xs text-[var(--text-muted)]">
                        {{ new Date(post.published_at).toLocaleDateString(en ? 'en-GB' : 'bn-BD', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                    </span>
                    <span class="text-xs text-[var(--text-muted)]">
                        • {{ post.view_count ?? 1 }} {{ en ? 'views' : 'বার পঠিত' }}
                    </span>
                </div>

                <h1 class="font-heading text-2xl sm:text-3xl md:text-4xl font-extrabold leading-tight">
                    {{ post.title }}
                </h1>

                <p v-if="post.excerpt" class="mt-4 text-base md:text-lg text-[var(--text-muted)] leading-relaxed border-l-4 border-[var(--primary)] pl-4">
                    {{ post.excerpt }}
                </p>
            </header>

            <!-- Featured Image -->
            <div v-if="post.featured_image_url" class="mb-8 overflow-hidden rounded-2xl border border-[var(--border)]">
                <img
                    :src="post.featured_image_url"
                    :alt="post.title"
                    class="w-full max-h-[440px] object-cover"
                />
            </div>

            <!-- Article Body -->
            <div
                class="blog-prose rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 md:p-10 leading-8 text-base space-y-4"
                v-html="post.content"
            />

            <!-- Share & Back Footer -->
            <div class="mt-8 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-5 py-4">
                <Link href="/blog" class="text-sm font-semibold text-[var(--primary)] hover:underline">
                    ← {{ t('blog_page.all_posts') }}
                </Link>
                <button
                    type="button"
                    @click="copyShareLink"
                    class="px-4 py-2 rounded-lg border border-[var(--border)] bg-[var(--surface2)] text-xs font-semibold hover:border-[var(--primary)]"
                >
                    {{ copied ? t('referral_page.copied') : t('blog_page.share_link') }}
                </button>
            </div>

            <!-- Conversion CTA: Books & Courses -->
            <section v-if="featuredCourses?.length || featuredBooks?.length" class="mt-12 rounded-2xl border border-[var(--primary)]/30 bg-gradient-to-br from-[var(--primary)]/10 via-[var(--surface)] to-[var(--secondary)]/10 p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="font-heading text-xl font-extrabold">
                            {{ en ? 'Continue Learning on Sikhun.com' : 'Sikhun.com-এ আপনার শেখা এগিয়ে নিন' }}
                        </h2>
                        <p class="text-sm text-[var(--text-muted)] mt-1">
                            {{ en ? 'Explore digital books, mentor-led courses, and AI study tools.' : 'ডিজিটাল বই পড়ুন, কোর্সে অংশ নিন এবং AI স্টাডি টুল দিয়ে প্রস্তুতি নিন।' }}
                        </p>
                    </div>
                    <Link href="/register" class="px-5 py-2.5 rounded-full bg-[var(--primary)] text-white text-xs font-bold shrink-0">
                        {{ en ? 'Start Free →' : 'ফ্রি শুরু করুন →' }}
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Link
                        v-for="course in featuredCourses"
                        :key="`c-${course.id}`"
                        :href="`/courses/${course.slug}`"
                        class="flex items-center gap-3.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-3.5 hover:border-[var(--primary)] transition"
                    >
                        <img v-if="course.cover_image_url" :src="course.cover_image_url" :alt="course.title" loading="lazy" class="h-14 w-14 rounded-lg object-cover shrink-0" />
                        <div class="min-w-0">
                            <span class="text-[10px] font-mono uppercase text-[var(--primary)]">{{ en ? 'Course' : 'কোর্স' }}</span>
                            <div class="font-bold text-sm truncate">{{ course.title }}</div>
                        </div>
                    </Link>
                    <Link
                        v-for="book in featuredBooks"
                        :key="`b-${book.id}`"
                        :href="`/library/${book.slug}`"
                        class="flex items-center gap-3.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-3.5 hover:border-[var(--primary)] transition"
                    >
                        <img v-if="book.cover_image_url" :src="book.cover_image_url" :alt="book.title" loading="lazy" class="h-14 w-14 rounded-lg object-cover shrink-0" />
                        <div class="min-w-0">
                            <span class="text-[10px] font-mono uppercase text-[#4cd7f6]">{{ en ? 'Digital Book' : 'ডিজিটাল বই' }}</span>
                            <div class="font-bold text-sm truncate">{{ book.title }}</div>
                        </div>
                    </Link>
                </div>
            </section>

            <!-- Related Posts -->
            <section v-if="related?.length" class="mt-12">
                <h2 class="font-heading text-xl font-extrabold mb-5">{{ t('blog_page.related_posts') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <Link
                        v-for="item in related"
                        :key="item.id"
                        :href="`/blog/${item.slug}`"
                        class="group rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 hover:border-[var(--primary)]/40 transition"
                    >
                        <span v-if="item.category" class="text-[11px] font-semibold text-[var(--primary)]">
                            {{ item.category.name }}
                        </span>
                        <h3 class="mt-1 font-heading font-bold text-base line-clamp-2 group-hover:text-[var(--primary)]">
                            {{ item.title }}
                        </h3>
                        <p v-if="item.excerpt" class="mt-2 text-xs text-[var(--text-muted)] line-clamp-2">
                            {{ item.excerpt }}
                        </p>
                    </Link>
                </div>
            </section>
        </article>
    </PublicLayout>
</template>

<style scoped>
.blog-prose :deep(h2) {
    font-size: 1.4rem;
    font-weight: 800;
    margin-top: 1.75rem;
    margin-bottom: 0.75rem;
    line-height: 1.35;
}
.blog-prose :deep(h3) {
    font-size: 1.15rem;
    font-weight: 700;
    margin-top: 1.4rem;
    margin-bottom: 0.5rem;
}
.blog-prose :deep(p) {
    margin-bottom: 1rem;
    line-height: 1.85;
}
.blog-prose :deep(ul),
.blog-prose :deep(ol) {
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}
.blog-prose :deep(ul) {
    list-style-type: disc;
}
.blog-prose :deep(ol) {
    list-style-type: decimal;
}
.blog-prose :deep(li) {
    margin-bottom: 0.35rem;
}
.blog-prose :deep(blockquote) {
    border-left: 3px solid var(--primary);
    padding-left: 1rem;
    color: var(--text-muted);
    font-style: italic;
    margin: 1.25rem 0;
}
.blog-prose :deep(a) {
    color: var(--primary);
    text-decoration: underline;
}
</style>
