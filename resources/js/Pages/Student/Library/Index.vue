<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';
import { MagnifyingGlassIcon, AdjustmentsHorizontalIcon, BookOpenIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ seo: Object, books: Object, categories: Array, subjects: Array, filters: Object });
const { t, locale } = useI18n();

const search = ref(props.filters.search ?? '');
const level = ref(props.filters.level ?? '');
const subject = ref(props.filters.subject ?? '');
const categoryId = ref(props.filters.category_id ?? '');
const free = ref(!!props.filters.free);
const sort = ref(props.filters.sort ?? 'newest');

function applyFilters() {
    router.get('/library', {
        search: search.value, level: level.value, subject: subject.value,
        category_id: categoryId.value, free: free.value ? 1 : '', sort: sort.value,
    }, { preserveState: true, replace: true });
}

let debounce;
watch(search, () => {
    clearTimeout(debounce);
    debounce = setTimeout(applyFilters, 350);
});
watch([level, subject, categoryId, free, sort], applyFilters);

function resetFilters() {
    search.value = ''; level.value = ''; subject.value = ''; categoryId.value = ''; free.value = false; sort.value = 'newest';
}

const activeFilterCount = () => [level.value, subject.value, categoryId.value, free.value ? 1 : ''].filter(Boolean).length;
</script>

<template>
    <Head :title="t('library.title')" />
    <SeoHead v-if="seo" :seo="seo" />
    <StudentLayout>
        <header class="anim-rise mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <span class="eyebrow">{{ locale === 'en' ? 'Explore' : 'ঘুরে দেখুন' }}</span>
                <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ t('library.title') }}</h1>
                <p class="mt-1 text-sm text-[var(--text-muted)]">{{ books.total }} {{ locale === 'en' ? 'books available' : 'বই পাওয়া যাচ্ছে' }}</p>
            </div>
            <button v-if="activeFilterCount()" @click="resetFilters" class="btn btn-ghost btn-sm text-[var(--accent)]">
                {{ locale === 'en' ? 'Clear filters' : 'ফিল্টার সাফ করুন' }} ({{ activeFilterCount() }})
            </button>
        </header>

        <div class="grid gap-6 lg:grid-cols-[260px_1fr]">
            <!-- Filter panel -->
            <aside class="lg:sticky lg:top-24 lg:h-fit">
                <div class="card rounded-2xl p-5">
                    <div class="mb-4 flex items-center gap-2 font-heading font-bold">
                        <AdjustmentsHorizontalIcon class="h-4.5 w-4.5 text-[var(--secondary)]" /> {{ locale === 'en' ? 'Filters' : 'ফিল্টার' }}
                    </div>

                    <div class="space-y-4">
                        <div class="relative">
                            <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                            <input v-model="search" type="text" :placeholder="t('common.search')" class="input pl-9" />
                        </div>

                        <div>
                            <label class="label">{{ t('common.level') }}</label>
                            <select v-model="level" class="input">
                                <option value="">{{ t('library.all_levels') }}</option>
                                <option value="ssc">SSC</option><option value="hsc">HSC</option>
                                <option value="university">University</option><option value="job">Job</option>
                            </select>
                        </div>

                        <div v-if="subjects.length">
                            <label class="label">{{ locale === 'en' ? 'Subject' : 'বিষয়' }}</label>
                            <select v-model="subject" class="input">
                                <option value="">{{ t('common.all') }}</option>
                                <option v-for="s in subjects" :key="s" :value="s">{{ s }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="label">{{ locale === 'en' ? 'Category' : 'ক্যাটাগরি' }}</label>
                            <select v-model="categoryId" class="input">
                                <option value="">{{ t('common.all') }}</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="label">{{ locale === 'en' ? 'Sort by' : 'সাজান' }}</label>
                            <select v-model="sort" class="input">
                                <option value="newest">{{ locale === 'en' ? 'Newest' : 'নতুন' }}</option>
                                <option value="popular">{{ locale === 'en' ? 'Most Read' : 'সর্বাধিক পঠিত' }}</option>
                                <option value="price_low">{{ locale === 'en' ? 'Price: Low to High' : 'মূল্য: কম থেকে বেশি' }}</option>
                                <option value="price_high">{{ locale === 'en' ? 'Price: High to Low' : 'মূল্য: বেশি থেকে কম' }}</option>
                            </select>
                        </div>

                        <label class="flex cursor-pointer items-center gap-2.5 text-sm">
                            <input v-model="free" type="checkbox" class="h-4 w-4 rounded accent-[var(--primary)]" /> {{ t('library.free_only') }}
                        </label>
                    </div>
                </div>
            </aside>

            <!-- Results -->
            <div>
                <div v-if="books.data.length" class="stagger grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 xl:grid-cols-4">
                    <Link v-for="book in books.data" :key="book.id" :href="`/library/${book.slug}`" class="card card-hover group overflow-hidden rounded-2xl">
                        <div class="relative aspect-[3/4] overflow-hidden bg-[var(--surface2)]">
                            <img v-if="book.cover_image_url" :src="book.cover_image_url" :alt="book.title" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <div v-else class="flex h-full w-full items-center justify-center text-[var(--text-muted)]">
                                <BookOpenIcon class="h-10 w-10 opacity-40" />
                            </div>
                            <span v-if="book.level" class="badge absolute left-2 top-2 bg-[var(--surface)]/90 text-[var(--text)] backdrop-blur">{{ book.level }}</span>
                            <span v-if="book.is_free" class="badge absolute right-2 top-2 bg-[var(--secondary)] text-white">{{ t('common.free') }}</span>
                        </div>
                        <div class="p-4">
                            <div class="line-clamp-2 text-sm font-medium leading-snug transition-colors group-hover:text-[var(--secondary)]">{{ book.title }}</div>
                            <div class="mt-3 font-heading font-bold">
                                <span v-if="book.is_free" class="text-[var(--secondary)]">{{ t('common.free') }}</span>
                                <span v-else>৳{{ book.price }}</span>
                            </div>
                        </div>
                    </Link>
                </div>
                <div v-else class="card flex flex-col items-center gap-3 rounded-2xl border-dashed p-12 text-center text-[var(--text-muted)]">
                    <BookOpenIcon class="h-10 w-10 opacity-40" />
                    {{ t('library.no_books') }}
                </div>

                <Pagination :links="books.links" />
            </div>
        </div>
    </StudentLayout>
</template>
