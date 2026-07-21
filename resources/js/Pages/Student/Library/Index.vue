<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ books: Object, categories: Array, subjects: Array, filters: Object });
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
    <StudentLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('library.title') }}</h1>
            <button v-if="activeFilterCount()" @click="resetFilters" class="text-xs text-[var(--text-muted)] hover:text-[var(--accent)]">
                Clear filters ({{ activeFilterCount() }})
            </button>
        </div>

        <div class="grid lg:grid-cols-[240px_1fr] gap-6">
            <!-- Filter sidebar -->
            <aside class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold uppercase text-[var(--text-muted)] mb-2">{{ t('common.search') }}</label>
                    <input v-model="search" type="text" :placeholder="t('common.search')" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm" />
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-[var(--text-muted)] mb-2">{{ t('common.level') }}</label>
                    <select v-model="level" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm">
                        <option value="">{{ t('library.all_levels') }}</option>
                        <option value="ssc">SSC</option><option value="hsc">HSC</option>
                        <option value="university">University</option><option value="job">Job</option>
                    </select>
                </div>

                <div v-if="subjects.length">
                    <label class="block text-xs font-semibold uppercase text-[var(--text-muted)] mb-2">{{ locale === 'en' ? 'Subject' : 'বিষয়' }}</label>
                    <select v-model="subject" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm">
                        <option value="">{{ t('common.all') }}</option>
                        <option v-for="s in subjects" :key="s" :value="s">{{ s }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-[var(--text-muted)] mb-2">{{ locale === 'en' ? 'Category' : 'ক্যাটাগরি' }}</label>
                    <select v-model="categoryId" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm">
                        <option value="">{{ t('common.all') }}</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-[var(--text-muted)] mb-2">{{ locale === 'en' ? 'Sort' : 'সাজান' }}</label>
                    <select v-model="sort" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm">
                        <option value="newest">{{ locale === 'en' ? 'Newest' : 'নতুন' }}</option>
                        <option value="popular">{{ locale === 'en' ? 'Most Read' : 'সর্বাধিক পঠিত' }}</option>
                        <option value="price_low">{{ locale === 'en' ? 'Price: Low to High' : 'মূল্য: কম থেকে বেশি' }}</option>
                        <option value="price_high">{{ locale === 'en' ? 'Price: High to Low' : 'মূল্য: বেশি থেকে কম' }}</option>
                    </select>
                </div>

                <label class="flex items-center gap-2 text-sm px-1">
                    <input v-model="free" type="checkbox" class="rounded" /> {{ t('library.free_only') }}
                </label>
            </aside>

            <!-- Results -->
            <div>
                <div v-if="books.data.length" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                    <Link v-for="book in books.data" :key="book.id" :href="`/library/${book.slug}`"
                        class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-[3/4] bg-[var(--surface2)] flex items-center justify-center text-[var(--text-muted)] text-xs overflow-hidden">
                            <img v-if="book.cover_image_url" :src="book.cover_image_url" :alt="book.title" loading="lazy" class="w-full h-full object-cover" />
                            <span v-else>{{ t('home.no_cover') }}</span>
                        </div>
                        <div class="p-4">
                            <div class="font-medium text-sm mb-1 line-clamp-2">{{ book.title }}</div>
                            <div class="text-xs text-[var(--text-muted)] uppercase mb-2">{{ book.level }}</div>
                            <div class="font-heading font-bold">
                                <span v-if="book.is_free" class="text-[var(--secondary)]">{{ t('common.free') }}</span>
                                <span v-else>৳{{ book.price }}</span>
                            </div>
                        </div>
                    </Link>
                </div>
                <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
                    {{ t('library.no_books') }}
                </div>

                <Pagination :links="books.links" />
            </div>
        </div>
    </StudentLayout>
</template>
