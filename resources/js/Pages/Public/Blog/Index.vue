<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    posts: Object,
    categories: Array,
    activeCategory: Object,
    tags: Array,
    activeTag: Object,
    filters: Object,
    seo: Object,
});

const { t, locale } = useI18n();
const en = computed(() => locale.value === 'en');
const search = ref(props.filters?.search ?? '');

const cardPalettes = [
    'from-[#0e513d] to-[#0a0e18]',
    'from-[#7c3d13] to-[#171b26]',
    'from-[#123859] to-[#0a0e18]',
    'from-[#40336e] to-[#0a0e18]',
];

function filterUrl(tag = null) {
    const baseUrl = props.activeCategory
        ? `/blog/category/${props.activeCategory.slug}`
        : '/blog';
    const params = new URLSearchParams();
    if (tag) params.set('tag', tag.slug);
    const query = params.toString();
    return query ? `${baseUrl}?${query}` : baseUrl;
}

function submitSearch() {
    const baseUrl = props.activeCategory
        ? `/blog/category/${props.activeCategory.slug}`
        : '/blog';

    router.get(baseUrl, {
        search: search.value || undefined,
        tag: props.activeTag?.slug || undefined,
    }, { preserveState: true, replace: true });
}
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-16">
            <!-- Hero Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div>
                    <span class="font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[var(--primary)]">
                        {{ en ? 'Sikhun Blog · Science, Tech, AI & Education' : 'Sikhun ব্লগ · বিজ্ঞান, প্রযুক্তি, AI ও শিক্ষা' }}
                    </span>
                    <h1 class="mt-2 font-heading text-3xl md:text-4xl font-extrabold tracking-tight">
                        {{ activeCategory ? activeCategory.name : t('blog_page.title') }}
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm md:text-base text-[var(--text-muted)]">
                        {{ t('blog_page.subtitle') }}
                    </p>
                </div>

                <form @submit.prevent="submitSearch" class="flex items-center gap-2 w-full md:w-80">
                    <input
                        v-model="search"
                        type="search"
                        :placeholder="t('common.search')"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-[var(--surface)] border border-[var(--border)] text-sm focus:border-[var(--primary)] focus:outline-none"
                    />
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[var(--primary)] text-white text-sm font-semibold">
                        {{ en ? 'Search' : 'খুঁজুন' }}
                    </button>
                </form>
            </div>

            <!-- Category Pills -->
            <div class="flex flex-wrap items-center gap-2 mb-8">
                <Link
                    href="/blog"
                    class="px-4 py-2 rounded-full text-xs font-semibold transition border"
                    :class="!activeCategory
                        ? 'bg-[var(--primary)] text-white border-[var(--primary)]'
                        : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:text-[var(--text)]'"
                >
                    {{ t('common.all') }}
                </Link>
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="`/blog/category/${cat.slug}`"
                    class="px-4 py-2 rounded-full text-xs font-semibold transition border"
                    :class="activeCategory?.id === cat.id
                        ? 'bg-[var(--primary)] text-white border-[var(--primary)]'
                        : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:text-[var(--text)]'"
                >
                    {{ cat.name }} <span v-if="cat.posts_count" class="opacity-70">({{ cat.posts_count }})</span>
                </Link>
            </div>

            <!-- Tag filters -->
            <div v-if="tags?.length" class="mb-8">
                <p class="mb-2 text-xs font-semibold text-[var(--text-muted)]">{{ t('blog_page.filter_by_tag') }}</p>
                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        :href="filterUrl()"
                        class="px-3.5 py-2 rounded-full text-xs font-semibold transition border"
                        :class="!activeTag ? 'bg-[var(--primary)] text-white border-[var(--primary)]' : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:text-[var(--text)]'"
                    >
                        {{ t('blog_page.all_tags') }}
                    </Link>
                    <Link
                        v-for="tag in tags"
                        :key="tag.id"
                        :href="filterUrl(tag)"
                        class="px-3.5 py-2 rounded-full text-xs font-semibold transition border"
                        :class="activeTag?.id === tag.id ? 'bg-[var(--primary)] text-white border-[var(--primary)]' : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:text-[var(--text)]'"
                    >
                        {{ tag.name }} <span class="opacity-70">({{ tag.posts_count }})</span>
                    </Link>
                </div>
            </div>

            <!-- Blog Posts Grid -->
            <div v-if="posts.data?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link
                    v-for="(post, idx) in posts.data"
                    :key="post.id"
                    :href="`/blog/${post.slug}`"
                    class="group flex flex-col overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[var(--primary)]/40 hover:shadow-xl"
                >
                    <div class="relative h-48 overflow-hidden bg-gradient-to-br" :class="cardPalettes[idx % cardPalettes.length]">
                        <img
                            v-if="post.featured_image_url"
                            :src="post.featured_image_url"
                            :alt="post.title"
                            loading="lazy"
                            class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        />
                        <div v-else class="absolute inset-0 grid place-items-center text-white/50">
                            <span class="material-symbols-outlined !text-5xl">article</span>
                        </div>
                        <span
                            v-if="post.category"
                            class="absolute left-3 top-3 rounded-full bg-[#0a0e18]/85 px-3 py-1 font-mono text-[10px] font-semibold uppercase tracking-wider text-[#4cd7f6] backdrop-blur"
                        >
                            {{ post.category.name }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col justify-between gap-4 p-5">
                        <div>
                            <h2 class="line-clamp-2 font-heading text-lg font-bold leading-snug transition group-hover:text-[var(--primary)]">
                                {{ post.title }}
                            </h2>
                            <p v-if="post.excerpt" class="mt-2.5 line-clamp-3 text-sm leading-relaxed text-[var(--text-muted)]">
                                {{ post.excerpt }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between border-t border-[var(--border)] pt-3.5 text-xs text-[var(--text-muted)]">
                            <span>
                                {{ post.published_at ? new Date(post.published_at).toLocaleDateString(en ? 'en-GB' : 'bn-BD', { day: 'numeric', month: 'short', year: 'numeric' }) : '' }}
                            </span>
                            <span class="font-semibold text-[var(--primary)]">
                                {{ t('blog_page.read_more') }} →
                            </span>
                        </div>
                    </div>
                </Link>
            </div>

            <div v-else class="rounded-2xl border border-dashed border-[var(--border)] bg-[var(--surface)] p-14 text-center text-sm text-[var(--text-muted)]">
                {{ t('blog_page.no_posts') }}
            </div>

            <div class="mt-8">
                <Pagination :links="posts.links" />
            </div>
        </div>
    </PublicLayout>
</template>
