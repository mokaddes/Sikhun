<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';
import {
    MagnifyingGlassIcon,
    AdjustmentsHorizontalIcon,
    BookOpenIcon,
    SparklesIcon,
    XMarkIcon,
    ArrowRightIcon,
    EyeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    seo: Object,
    books: Object,
    categories: { type: Array, default: () => [] },
    subjects: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const { t, locale, money, n } = useI18n();

const search = ref(props.filters.search ?? '');
const level = ref(props.filters.level ?? '');
const subject = ref(props.filters.subject ?? '');
const categoryId = ref(props.filters.category_id ?? '');
const free = ref(!!props.filters.free);
const sort = ref(props.filters.sort ?? 'newest');
const mobileFiltersOpen = ref(false);

const levelTabs = computed(() => [
    { value: '', label: locale.value === 'en' ? 'All Levels' : 'সব লেভেল' },
    { value: 'ssc', label: 'SSC' },
    { value: 'hsc', label: 'HSC' },
    { value: 'university', label: locale.value === 'en' ? 'University' : 'ভার্সিটি' },
    { value: 'job', label: locale.value === 'en' ? 'Job Prep' : 'চাকরি প্রস্তুতি' },
]);

function applyFilters() {
    router.get('/library', {
        search: search.value || undefined,
        level: level.value || undefined,
        subject: subject.value || undefined,
        category_id: categoryId.value || undefined,
        free: free.value ? 1 : undefined,
        sort: sort.value !== 'newest' ? sort.value : undefined,
    }, { preserveState: true, replace: true });
}

let debounce;
watch(search, () => {
    clearTimeout(debounce);
    debounce = setTimeout(applyFilters, 350);
});
watch([level, subject, categoryId, free, sort], applyFilters);

function resetFilters() {
    search.value = '';
    level.value = '';
    subject.value = '';
    categoryId.value = '';
    free.value = false;
    sort.value = 'newest';
}

const activeFilterCount = computed(() =>
    [search.value, level.value, subject.value, categoryId.value, free.value ? 1 : ''].filter(Boolean).length
);

function formatLevel(lvl) {
    if (!lvl) return '';
    const map = {
        ssc: 'SSC',
        hsc: 'HSC',
        university: locale.value === 'en' ? 'University' : 'ভার্সিটি',
        job: locale.value === 'en' ? 'Job Prep' : 'জব',
    };
    return map[lvl] || lvl.toUpperCase();
}
</script>

<template>
    <Head :title="t('library.title')" />
    <SeoHead v-if="seo" :seo="seo" />

    <PublicLayout>
        <!-- Hero Banner -->
        <section class="relative overflow-hidden border-b border-[var(--border)] bg-gradient-to-br from-[#0f172a] via-[#111827] to-[#1e1b4b] text-white">
            <div class="pointer-events-none absolute -top-24 -right-24 h-80 w-80 rounded-full bg-[var(--primary)]/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-emerald-500/15 blur-3xl"></div>

            <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:py-16">
                <div class="grid items-center gap-8 lg:grid-cols-12">
                    <div class="lg:col-span-7">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3.5 py-1.5 text-xs font-semibold tracking-wide text-orange-300 backdrop-blur">
                            <SparklesIcon class="h-4 w-4" />
                            <span>{{ locale === 'en' ? 'DIGITAL INTERACTIVE LIBRARY' : 'ডিজিটাল ইন্টারেক্টিভ লাইব্রেরি' }}</span>
                        </div>

                        <h1 class="mt-4 font-heading text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl">
                            {{ locale === 'en' ? 'Read Smart Books with AI Study Companion' : 'পড়ুন পছন্দের বই, সাথে থাকুক AI স্টাডি অ্যাসিস্ট্যান্ট' }}
                        </h1>

                        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-300 sm:text-base">
                            {{ locale === 'en'
                                ? 'Explore academic, admission, and career books. Read online, buy individual chapters or full books, and ask AI questions directly from any page.'
                                : 'একাডেমিক, ভর্তি ও চাকরি প্রস্তুতির সেরা বইগুলো খুঁজুন। পুরো বই অথবা নির্দিষ্ট অধ্যায় কিনে পড়ুন এবং যেকোনো পৃষ্ঠা থেকে সরাসরি AI-কে প্রশ্ন করুন।' }}
                        </p>

                        <!-- Quick Level Pills -->
                        <div class="mt-6 flex flex-wrap items-center gap-2">
                            <button
                                v-for="tab in levelTabs"
                                :key="tab.value"
                                type="button"
                                @click="level = tab.value"
                                class="rounded-full px-4 py-2 text-xs font-semibold transition-all sm:text-sm"
                                :class="level === tab.value
                                    ? 'bg-[var(--primary)] text-white shadow-lg shadow-orange-500/25'
                                    : 'border border-white/15 bg-white/5 text-slate-200 hover:bg-white/10'"
                            >
                                {{ tab.label }}
                            </button>

                            <button
                                type="button"
                                @click="free = !free"
                                class="rounded-full px-4 py-2 text-xs font-semibold transition-all sm:text-sm"
                                :class="free
                                    ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/25'
                                    : 'border border-emerald-400/30 bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20'"
                            >
                                ✦ {{ locale === 'en' ? 'Free Books' : 'ফ্রি বইসমূহ' }}
                            </button>
                        </div>
                    </div>

                    <!-- Search & Quick Stats Box -->
                    <div class="lg:col-span-5">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                                {{ locale === 'en' ? 'Search the Catalog' : 'বই বা বিষয় খুঁজুন' }}
                            </label>
                            <div class="relative mt-2">
                                <MagnifyingGlassIcon class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                                <input
                                    v-model="search"
                                    type="text"
                                    :placeholder="locale === 'en' ? 'Search by book title or subject...' : 'বইয়ের নাম বা বিষয় লিখে খুঁজুন...'"
                                    class="w-full rounded-xl border border-white/15 bg-[#0b0f19]/90 py-3.5 pl-11 pr-10 text-sm text-white placeholder-slate-400 focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/40"
                                />
                                <button
                                    v-if="search"
                                    type="button"
                                    @click="search = ''"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white"
                                >
                                    <XMarkIcon class="h-4 w-4" />
                                </button>
                            </div>

                            <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                                <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                    <div class="font-heading text-xl font-extrabold text-white">{{ books.total ?? 0 }}</div>
                                    <div class="text-[11px] text-slate-300">{{ locale === 'en' ? 'Books Found' : 'মোট বই' }}</div>
                                </div>
                                <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                    <div class="font-heading text-xl font-extrabold text-emerald-400">{{ categories.length }}</div>
                                    <div class="text-[11px] text-slate-300">{{ locale === 'en' ? 'Categories' : 'ক্যাটাগরি' }}</div>
                                </div>
                                <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                    <div class="font-heading text-xl font-extrabold text-orange-400">AI</div>
                                    <div class="text-[11px] text-slate-300">{{ locale === 'en' ? 'Smart Reader' : 'স্মার্ট রিডার' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Catalog Area -->
        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
            <!-- Mobile Filter & Sort Bar -->
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 lg:hidden">
                <button
                    type="button"
                    @click="mobileFiltersOpen = !mobileFiltersOpen"
                    class="inline-flex items-center gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-4 py-2.5 text-sm font-semibold shadow-sm"
                >
                    <AdjustmentsHorizontalIcon class="h-4 w-4 text-[var(--primary)]" />
                    <span>{{ locale === 'en' ? 'Filters' : 'ফিল্টার' }}</span>
                    <span v-if="activeFilterCount" class="rounded-full bg-[var(--primary)] px-2 py-0.5 text-xs text-white">{{ activeFilterCount }}</span>
                </button>

                <select v-model="sort" class="input max-w-[200px] py-2 text-sm">
                    <option value="newest">{{ locale === 'en' ? 'Newest First' : 'নতুন বই আগে' }}</option>
                    <option value="popular">{{ locale === 'en' ? 'Most Read' : 'সর্বাধিক পঠিত' }}</option>
                    <option value="price_low">{{ locale === 'en' ? 'Price: Low to High' : 'মূল্য: কম থেকে বেশি' }}</option>
                    <option value="price_high">{{ locale === 'en' ? 'Price: High to Low' : 'মূল্য: বেশি থেকে কম' }}</option>
                </select>
            </div>

            <div class="grid gap-8 lg:grid-cols-[270px_1fr]">
                <!-- Filter Sidebar -->
                <aside
                    :class="[
                        'lg:block lg:sticky lg:top-24 lg:h-fit',
                        mobileFiltersOpen ? 'block' : 'hidden',
                    ]"
                >
                    <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between border-b border-[var(--border)] pb-3">
                            <div class="flex items-center gap-2 font-heading text-base font-bold">
                                <AdjustmentsHorizontalIcon class="h-5 w-5 text-[var(--primary)]" />
                                <span>{{ locale === 'en' ? 'Refine Books' : 'বই ফিল্টার করুন' }}</span>
                            </div>
                            <button
                                v-if="activeFilterCount"
                                type="button"
                                @click="resetFilters"
                                class="text-xs font-semibold text-[var(--accent)] hover:underline"
                            >
                                {{ locale === 'en' ? 'Reset All' : 'সব মুছুন' }}
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="label">{{ locale === 'en' ? 'Category' : 'ক্যাটাগরি' }}</label>
                                <select v-model="categoryId" class="input">
                                    <option value="">{{ t('common.all') }}</option>
                                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="label">{{ t('common.level') }}</label>
                                <select v-model="level" class="input">
                                    <option value="">{{ t('library.all_levels') }}</option>
                                    <option value="ssc">SSC</option>
                                    <option value="hsc">HSC</option>
                                    <option value="university">{{ locale === 'en' ? 'University' : 'ভার্সিটি' }}</option>
                                    <option value="job">{{ locale === 'en' ? 'Job Prep' : 'চাকরি প্রস্তুতি' }}</option>
                                </select>
                            </div>

                            <div v-if="subjects.length">
                                <label class="label">{{ locale === 'en' ? 'Subject' : 'বিষয়' }}</label>
                                <select v-model="subject" class="input">
                                    <option value="">{{ t('common.all') }}</option>
                                    <option v-for="s in subjects" :key="s" :value="s">{{ s }}</option>
                                </select>
                            </div>

                            <div class="hidden lg:block">
                                <label class="label">{{ locale === 'en' ? 'Sort By' : 'সাজান' }}</label>
                                <select v-model="sort" class="input">
                                    <option value="newest">{{ locale === 'en' ? 'Newest First' : 'নতুন বই আগে' }}</option>
                                    <option value="popular">{{ locale === 'en' ? 'Most Read' : 'সর্বাধিক পঠিত' }}</option>
                                    <option value="price_low">{{ locale === 'en' ? 'Price: Low to High' : 'মূল্য: কম থেকে বেশি' }}</option>
                                    <option value="price_high">{{ locale === 'en' ? 'Price: High to Low' : 'মূল্য: বেশি থেকে কম' }}</option>
                                </select>
                            </div>

                            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-[var(--border)] bg-[var(--surface2)]/60 p-3 text-sm font-medium transition hover:border-[var(--primary)]/40">
                                <span>{{ t('library.free_only') }}</span>
                                <input v-model="free" type="checkbox" class="h-4 w-4 rounded accent-[var(--primary)]" />
                            </label>
                        </div>
                    </div>
                </aside>

                <!-- Books Grid -->
                <div>
                    <div class="mb-5 hidden items-center justify-between lg:flex">
                        <p class="text-sm text-[var(--text-muted)]">
                            {{ locale === 'en' ? `Showing ${books.data.length} of ${books.total} books` : `মোট ${books.total}টি বইয়ের মধ্যে ${books.data.length}টি দেখানো হচ্ছে` }}
                        </p>
                        <div v-if="activeFilterCount" class="flex items-center gap-2">
                            <span class="text-xs text-[var(--text-muted)]">{{ activeFilterCount }} {{ locale === 'en' ? 'active filter(s)' : 'টি ফিল্টার সক্রিয়' }}</span>
                            <button type="button" @click="resetFilters" class="text-xs font-semibold text-[var(--primary)] hover:underline">
                                {{ locale === 'en' ? 'Clear all' : 'সব সাফ করুন' }}
                            </button>
                        </div>
                    </div>

                    <div v-if="books.data.length" class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-3 xl:grid-cols-4">
                        <Link
                            v-for="book in books.data"
                            :key="book.id"
                            :href="`/library/${book.slug}`"
                            class="group flex flex-col overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[var(--primary)]/50 hover:shadow-xl"
                        >
                            <!-- Book Cover -->
                            <div class="relative aspect-[3/4] overflow-hidden bg-[var(--surface2)]">
                                <img
                                    v-if="book.cover_image_url"
                                    :src="book.cover_image_url"
                                    :alt="book.title"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div v-else class="flex h-full w-full flex-col items-center justify-center gap-2 bg-gradient-to-br from-slate-800 to-slate-900 p-4 text-center text-slate-400">
                                    <BookOpenIcon class="h-12 w-12 opacity-40" />
                                    <span class="line-clamp-2 text-xs font-medium text-slate-300">{{ book.title }}</span>
                                </div>

                                <!-- Top Badges -->
                                <div class="absolute inset-x-2.5 top-2.5 flex items-center justify-between gap-1.5">
                                    <span
                                        v-if="book.level"
                                        class="rounded-full bg-[#0b0f19]/80 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur"
                                    >
                                        {{ formatLevel(book.level) }}
                                    </span>
                                    <span v-else></span>

                                    <span
                                        v-if="book.is_free"
                                        class="rounded-full bg-emerald-500 px-2.5 py-1 text-[11px] font-bold text-white shadow"
                                    >
                                        {{ t('common.free') }}
                                    </span>
                                    <span
                                        v-else
                                        class="rounded-full bg-[var(--primary)] px-2.5 py-1 text-[11px] font-bold text-white shadow"
                                    >
                                        ৳{{ money(book.price) }}
                                    </span>
                                </div>

                                <!-- Hover Overlay -->
                                <div class="absolute inset-x-0 bottom-0 flex translate-y-full items-center justify-center bg-gradient-to-t from-black/85 via-black/50 to-transparent p-3 pt-8 text-xs font-semibold text-white transition-transform duration-300 group-hover:translate-y-0">
                                    <span class="inline-flex items-center gap-1.5">
                                        {{ locale === 'en' ? 'View Details' : 'বিস্তারিত দেখুন' }}
                                        <ArrowRightIcon class="h-3.5 w-3.5" />
                                    </span>
                                </div>
                            </div>

                            <!-- Book Info -->
                            <div class="flex flex-1 flex-col justify-between p-4">
                                <div>
                                    <div v-if="book.category?.name || book.subject" class="mb-1 truncate text-[11px] font-medium text-[var(--primary)]">
                                        {{ book.category?.name || book.subject }}
                                    </div>
                                    <h3 class="line-clamp-2 font-heading text-sm font-bold leading-snug text-[var(--text)] transition-colors group-hover:text-[var(--primary)] sm:text-base">
                                        {{ book.title }}
                                    </h3>
                                    <p v-if="book.author?.name" class="mt-1 truncate text-xs text-[var(--text-muted)]">
                                        {{ book.author.name }}
                                    </p>
                                </div>

                                <div class="mt-3 flex items-center justify-between border-t border-[var(--border)] pt-2.5 text-xs">
                                    <span class="font-heading text-sm font-extrabold" :class="book.is_free ? 'text-emerald-500' : 'text-[var(--text)]'">
                                        {{ book.is_free ? t('common.free') : `৳${money(book.price)}` }}
                                    </span>
                                    <span v-if="book.total_pages" class="text-[var(--text-muted)]">
                                        {{ n(book.total_pages) }} {{ locale === 'en' ? 'pages' : 'পৃষ্ঠা' }}
                                    </span>
                                    <span v-else-if="book.reading_count" class="inline-flex items-center gap-1 text-[var(--text-muted)]">
                                        <EyeIcon class="h-3.5 w-3.5" /> {{ n(book.reading_count) }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-[var(--border)] bg-[var(--surface)] px-6 py-16 text-center"
                    >
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[var(--primary)]/10 text-[var(--primary)]">
                            <BookOpenIcon class="h-8 w-8" />
                        </div>
                        <h3 class="mt-4 font-heading text-lg font-bold">{{ t('library.no_books') }}</h3>
                        <p class="mt-1 max-w-sm text-sm text-[var(--text-muted)]">
                            {{ locale === 'en' ? 'Try clearing some filters or searching with a different keyword.' : 'অন্য কোনো শব্দ দিয়ে খুঁজুন অথবা ফিল্টার রিসেট করে আবার চেষ্টা করুন।' }}
                        </p>
                        <button
                            v-if="activeFilterCount"
                            type="button"
                            @click="resetFilters"
                            class="btn btn-primary mt-5"
                        >
                            {{ locale === 'en' ? 'Reset Filters' : 'ফিল্টার রিসেট করুন' }}
                        </button>
                    </div>

                    <div class="mt-8">
                        <Pagination :links="books.links" />
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
