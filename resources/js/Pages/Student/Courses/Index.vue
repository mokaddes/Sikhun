<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ seo: Object, courses: Object, categories: Array, filters: Object });
const { t, locale } = useI18n();

const search = ref(props.filters.search ?? '');
const level = ref(props.filters.level ?? '');
const categoryId = ref(props.filters.category_id ?? '');
const free = ref(!!props.filters.free);
const sort = ref(props.filters.sort ?? 'newest');

function applyFilters() {
    router.get('/courses', {
        search: search.value, level: level.value, category_id: categoryId.value,
        free: free.value ? 1 : '', sort: sort.value,
    }, { preserveState: true, replace: true });
}

let debounce;
watch(search, () => { clearTimeout(debounce); debounce = setTimeout(applyFilters, 350); });
watch([level, categoryId, free, sort], applyFilters);

function resetFilters() {
    search.value = ''; level.value = ''; categoryId.value = ''; free.value = false; sort.value = 'newest';
}
const activeFilterCount = () => [level.value, categoryId.value, free.value ? 1 : ''].filter(Boolean).length;
</script>

<template>
    <Head :title="t('courses_page.title')" />
    <SeoHead v-if="seo" :seo="seo" />
    <StudentLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('courses_page.title') }}</h1>
            <button v-if="activeFilterCount()" @click="resetFilters" class="text-xs text-[var(--text-muted)] hover:text-[var(--accent)]">
                {{ locale === 'en' ? 'Clear filters' : 'ফিল্টার সাফ করুন' }} ({{ activeFilterCount() }})
            </button>
        </div>

        <div class="grid lg:grid-cols-[240px_1fr] gap-6">
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
                        <option value="popular">{{ locale === 'en' ? 'Most Enrolled' : 'সর্বাধিক ভর্তি' }}</option>
                        <option value="price_low">{{ locale === 'en' ? 'Price: Low to High' : 'মূল্য: কম থেকে বেশি' }}</option>
                        <option value="price_high">{{ locale === 'en' ? 'Price: High to Low' : 'মূল্য: বেশি থেকে কম' }}</option>
                    </select>
                </div>
                <label class="flex items-center gap-2 text-sm px-1">
                    <input v-model="free" type="checkbox" class="rounded" /> {{ locale === 'en' ? 'Free courses only' : 'শুধু ফ্রি কোর্স' }}
                </label>
            </aside>

            <div>
                <div v-if="courses.data.length" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    <Link v-for="course in courses.data" :key="course.id" :href="`/courses/${course.slug}`"
                        class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-video bg-[var(--surface2)] flex items-center justify-center text-[var(--text-muted)] text-xs overflow-hidden">
                            <img v-if="course.cover_image_url" :src="course.cover_image_url" :alt="course.title" loading="lazy" class="w-full h-full object-cover" />
                            <span v-else>{{ t('home.no_cover') }}</span>
                        </div>
                        <div class="p-4">
                            <div class="text-[10px] uppercase text-[var(--text-muted)] mb-1">{{ course.level }}</div>
                            <div class="font-medium mb-1 line-clamp-2">{{ course.title }}</div>
                            <div class="text-xs text-[var(--text-muted)] mb-2">{{ course.mentor?.name }}</div>
                            <div class="flex items-center justify-between">
                                <span class="font-heading font-bold">
                                    <span v-if="+course.price === 0" class="text-[var(--secondary)]">{{ t('common.free') }}</span>
                                    <span v-else>৳{{ course.price }}</span>
                                </span>
                                <span class="text-xs text-[var(--text-muted)]">{{ course.enrollments_count }} {{ locale === 'en' ? 'enrolled' : 'জন ভর্তি' }}</span>
                            </div>
                        </div>
                    </Link>
                </div>
                <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
                    {{ t('courses_page.no_courses') }}
                </div>
                <Pagination :links="courses.links" />
            </div>
        </div>
    </StudentLayout>
</template>
