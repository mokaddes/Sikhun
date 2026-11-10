<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    notices: Object,
    filters: Object,
    stats: Object,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');

const search = ref(props.filters.search || '');
const activeType = ref(props.filters.type || 'all');

const filterTabs = computed(() => [
    { id: 'all', label: en.value ? 'All Universities' : 'সকল বিশ্ববিদ্যালয়', count: props.stats.total },
    { id: 'public', label: en.value ? 'Public Universities' : 'পাবলিক বিশ্ববিদ্যালয়', count: props.stats.public },
    { id: 'guccho', label: en.value ? 'Guccho Cluster' : 'গুচ্ছ বিশ্ববিদ্যালয়', count: props.stats.guccho },
    { id: 'private', label: en.value ? 'Private Universities' : 'বেসরকারি বিশ্ববিদ্যালয়', count: props.stats.private },
]);

function setType(type) {
    activeType.value = type;
    router.get('/admission-notice', {
        type: type === 'all' ? undefined : type,
        search: search.value || undefined,
    }, { preserveState: true, replace: true });
}

function handleSearch() {
    router.get('/admission-notice', {
        type: activeType.value === 'all' ? undefined : activeType.value,
        search: search.value || undefined,
    }, { preserveState: true, replace: true });
}

function formatDate(dateStr) {
    if (!dateStr) return en.value ? 'TBA' : 'ঘোষণা সাপেক্ষে';
    const d = new Date(dateStr);
    return d.toLocaleDateString(en.value ? 'en-US' : 'bn-BD', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function getTypeBadge(type) {
    switch (type) {
        case 'public':
            return {
                label: en.value ? 'Public University' : 'পাবলিক বিশ্ববিদ্যালয়',
                cls: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20'
            };
        case 'guccho':
            return {
                label: en.value ? 'Guccho Cluster' : 'গুচ্ছ বিশ্ববিদ্যালয়',
                cls: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20'
            };
        case 'private':
            return {
                label: en.value ? 'Private University' : 'বেসরকারি বিশ্ববিদ্যালয়',
                cls: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20'
            };
        default:
            return {
                label: en.value ? 'University' : 'বিশ্ববিদ্যালয়',
                cls: 'bg-gray-500/10 text-gray-600 dark:text-gray-400 border-gray-500/20'
            };
    }
}
</script>

<template>
    <PublicLayout>
        <Head>
            <title>{{ en ? 'University Admission Notices & Circulars | Sikhun' : 'বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি ও সার্কুলার ২০২৬-২০২৭ | Sikhun' }}</title>
            <meta name="description" :content="en ? 'Find all Public, Guccho Cluster, and Private university undergraduate admission notices in Bangladesh.' : 'বাংলাদেশের সকল পাবলিক, গুচ্ছ এবং শীর্ষ বেসরকারি বিশ্ববিদ্যালয়ের স্নাতক প্রথম বর্ষ ভর্তি বিজ্ঞপ্তি, যোগ্যতা, মানবণ্টন ও অনলাইন আবেদনের তথ্য।'" />
        </Head>

        <div class="min-h-screen bg-[var(--bg)] text-[var(--text)] pb-20">
            <!-- Hero Section -->
            <section class="border-b border-gray-200 dark:border-white/5 bg-gradient-to-b from-orange-500/5 via-transparent to-transparent py-12 md:py-16">
                <div class="max-w-7xl mx-auto px-4 md:px-6">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-600 dark:text-orange-400 text-xs font-bold uppercase tracking-wider mb-4">
                            <span class="material-symbols-outlined !text-sm">school</span>
                            {{ en ? 'Higher Education Portal' : 'উচ্চশিক্ষা ও ভর্তি নির্দেশিকা' }}
                        </span>
                        <h1 class="text-3xl md:text-5xl font-black font-heading tracking-tight text-gray-900 dark:text-white leading-tight">
                            {{ en ? 'University Admission Notices' : 'বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭' }}
                        </h1>
                        <p class="mt-4 text-base md:text-lg text-[var(--text-muted)] leading-relaxed">
                            {{ en ? 'Accurate admission circulars, unit breakdowns, minimum GPA eligibility, exam schedules, and direct application links for Public, Guccho, and Private universities in Bangladesh.' : 'ঢাকা বিশ্ববিদ্যালয়, বুয়েট, ২৪ বিশ্ববিদ্যালয় সমন্বিত গুচ্ছ এবং শীর্ষ প্রাইভেট বিশ্ববিদ্যালয়ের অফিসিয়াল ভর্তি বিজ্ঞপ্তি, ইউনিট ও জিপিএ যোগ্যতা এবং সরাসরি আবেদনের পোর্টাল।' }}
                        </p>
                    </div>

                    <!-- Search Bar -->
                    <div class="mt-8 max-w-2xl flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input
                                v-model="search"
                                type="text"
                                :placeholder="en ? 'Search university, unit or circular...' : 'বিশ্ববিদ্যালয়ের নাম, ইউনিট বা বিষয়ের নাম লিখুন...'"
                                class="w-full h-12 pl-12 pr-4 rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-[#151923] text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm shadow-sm"
                                @keyup.enter="handleSearch"
                            />
                        </div>
                        <button
                            @click="handleSearch"
                            class="h-12 px-6 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm shadow-lg shadow-orange-500/20 transition flex items-center justify-center gap-2"
                        >
                            <span>{{ en ? 'Search' : 'খুঁজুন' }}</span>
                            <span class="material-symbols-outlined !text-sm">arrow_forward</span>
                        </button>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="mt-8 flex flex-wrap gap-2 pt-4 border-t border-gray-200 dark:border-white/5">
                        <button
                            v-for="tab in filterTabs"
                            :key="tab.id"
                            @click="setType(tab.id)"
                            :class="[
                                'px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition flex items-center gap-2',
                                activeType === tab.id
                                    ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20'
                                    : 'bg-white dark:bg-[#171c28] border border-gray-200 dark:border-white/5 text-gray-600 dark:text-gray-300 hover:border-orange-500/40'
                            ]"
                        >
                            <span>{{ tab.label }}</span>
                            <span
                                :class="[
                                    'px-1.5 py-0.5 rounded-full text-[11px] font-bold',
                                    activeType === tab.id
                                        ? 'bg-white/20 text-white'
                                        : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400'
                                ]"
                            >{{ tab.count }}</span>
                        </button>
                    </div>
                </div>
            </section>

            <!-- Circulars Grid -->
            <section class="max-w-7xl mx-auto px-4 md:px-6 py-10">
                <div v-if="notices.data?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <article
                        v-for="notice in notices.data"
                        :key="notice.id"
                        class="group rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] hover:border-orange-500/40 transition-all duration-300 hover:-translate-y-1 shadow-sm dark:shadow-none flex flex-col justify-between overflow-hidden"
                    >
                        <div>
                            <!-- Header / Badge -->
                            <div class="p-6 pb-4 border-b border-gray-100 dark:border-white/5">
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span :class="['px-2.5 py-1 rounded-full text-[11px] font-bold border', getTypeBadge(notice.university_type).cls]">
                                        {{ getTypeBadge(notice.university_type).label }}
                                    </span>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400">
                                        {{ notice.academic_year }}
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-orange-600 dark:text-orange-400 block mb-1">
                                    {{ notice.institution_name }}
                                </span>
                                <Link :href="`/admission-notice/${notice.slug}`">
                                    <h2 class="font-heading text-lg font-bold text-gray-900 dark:text-white line-clamp-2 group-hover:text-orange-500 transition">
                                        {{ notice.title }}
                                    </h2>
                                </Link>
                            </div>

                            <!-- Key Specs -->
                            <div class="p-6 pt-4 space-y-2.5 text-xs">
                                <div v-if="notice.application_start_text" class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined !text-sm text-emerald-500">event_available</span>
                                        <span>{{ en ? 'Starting:' : 'আবেদন শুরু:' }}</span>
                                    </span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 truncate max-w-[200px]" :title="notice.application_start_text">
                                        {{ notice.application_start_text }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined !text-sm text-red-500">event_busy</span>
                                        <span>{{ en ? 'Deadline:' : 'শেষ তারিখ:' }}</span>
                                    </span>
                                    <span class="font-bold text-red-600 dark:text-red-400 truncate max-w-[200px]" :title="notice.application_deadline_text || formatDate(notice.application_deadline)">
                                        {{ notice.application_deadline_text || formatDate(notice.application_deadline) }}
                                    </span>
                                </div>

                                <div v-if="notice.total_gpa_required" class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined !text-sm text-blue-500">grade</span>
                                        <span>{{ en ? 'Required GPA:' : 'ন্যূনতম জিপিএ:' }}</span>
                                    </span>
                                    <span class="font-bold text-gray-900 dark:text-gray-200">
                                        {{ notice.total_gpa_required }}
                                    </span>
                                </div>

                                <div v-if="notice.application_fee" class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined !text-sm text-emerald-500">payments</span>
                                        <span>{{ en ? 'Application Fee:' : 'আবেদন ফি:' }}</span>
                                    </span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ notice.application_fee }}
                                    </span>
                                </div>

                                <div v-if="notice.units?.length" class="mt-3 pt-3 border-t border-gray-100 dark:border-white/5">
                                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 block mb-1">
                                        {{ en ? 'Available Units:' : 'উপলব্ধ ইউনিটসমূহ:' }}
                                    </span>
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="(unit, idx) in notice.units.slice(0, 3)"
                                            :key="idx"
                                            class="px-2 py-0.5 rounded bg-gray-100 dark:bg-white/5 text-[10px] font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            {{ unit.name || unit }}
                                        </span>
                                        <span v-if="notice.units.length > 3" class="text-[10px] text-gray-400 self-center">
                                            +{{ notice.units.length - 3 }} {{ en ? 'more' : 'আরও' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="p-6 pt-0 border-t border-gray-100 dark:border-white/5 mt-4 pt-4 flex items-center justify-between gap-3">
                            <Link
                                :href="`/admission-notice/${notice.slug}`"
                                class="inline-flex items-center gap-1 text-xs font-bold text-gray-900 dark:text-white hover:text-orange-500 transition"
                            >
                                <span>{{ en ? 'Circular Details' : 'বিস্তারিত সার্কুলার' }}</span>
                                <span class="material-symbols-outlined !text-sm">arrow_forward</span>
                            </Link>

                            <a
                                v-if="notice.apply_url"
                                :href="notice.apply_url"
                                target="_blank"
                                class="px-3 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-sm transition flex items-center gap-1"
                            >
                                <span>{{ en ? 'Apply Online' : 'অনলাইনে আবেদন' }}</span>
                                <span class="material-symbols-outlined !text-xs">open_in_new</span>
                            </a>
                        </div>
                    </article>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16 bg-white dark:bg-[#171c28] rounded-3xl border border-gray-200 dark:border-white/5">
                    <span class="material-symbols-outlined text-4xl text-gray-400">search_off</span>
                    <h3 class="mt-2 text-base font-bold text-gray-900 dark:text-white">
                        {{ en ? 'No admission circulars found' : 'কোনো ভর্তি বিজ্ঞপ্তি পাওয়া যায়নি' }}
                    </h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ en ? 'Try adjusting your search keyword or selected category tab.' : 'অনুগ্রহ করে ভিন্ন কীওয়ার্ড দিয়ে অনুসন্ধান করুন অথবা সকল ক্যাটাগরি ফিল্টার সিলেক্ট করুন।' }}
                    </p>
                </div>

                <!-- Pagination -->
                <div v-if="notices.links?.length > 3" class="mt-10 flex justify-center gap-2">
                    <Link
                        v-for="(link, idx) in notices.links"
                        :key="idx"
                        :href="link.url || '#'"
                        v-html="link.label"
                        :class="[
                            'px-3.5 py-2 rounded-xl text-xs font-bold transition',
                            link.active
                                ? 'bg-orange-500 text-white'
                                : 'bg-white dark:bg-[#171c28] border border-gray-200 dark:border-white/5 text-gray-700 dark:text-gray-300 hover:border-orange-500',
                            !link.url ? 'opacity-40 pointer-events-none' : ''
                        ]"
                    />
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
