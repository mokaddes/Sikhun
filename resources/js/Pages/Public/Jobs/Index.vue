<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';
import {
    BriefcaseIcon,
    MagnifyingGlassIcon,
    BuildingOfficeIcon,
    CalendarDaysIcon,
    BanknotesIcon,
    UserGroupIcon,
    MapPinIcon,
    AcademicCapIcon,
    CalculatorIcon,
    SparklesIcon,
    ArrowRightIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    jobs: Object,
    filters: Object,
    stats: Object,
    seo: Object,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || 'all');

let searchDebounce = null;
watch(search, (val) => {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => {
        applyFilter();
    }, 350);
});

function setCategory(cat) {
    category.value = cat;
    applyFilter();
}

function applyFilter() {
    router.get(
        '/job-news',
        { category: category.value !== 'all' ? category.value : undefined, search: search.value || undefined },
        { preserveState: true, replace: true }
    );
}

const categories = computed(() => [
    { key: 'all', label: en.value ? 'All Jobs' : 'সকল চাকরি', icon: 'all' },
    { key: 'government', label: en.value ? 'Govt Jobs' : 'সরকারি চাকরি', icon: 'govt' },
    { key: 'bank', label: en.value ? 'Bank Jobs' : 'ব্যাংক জবস', icon: 'bank' },
    { key: 'ngo', label: en.value ? 'NGO & Dev' : 'এনজিও ও উন্নয়ন', icon: 'ngo' },
    { key: 'private', label: en.value ? 'Private & Corp' : 'বেসরকারি ও কর্পোরেট', icon: 'private' },
    { key: 'it_tech', label: en.value ? 'IT & Tech' : 'আইটি ও ইঞ্জিনিয়ারিং', icon: 'it' },
]);

function getCategoryColor(cat) {
    switch (cat) {
        case 'government':
            return 'bg-blue-500/15 text-blue-600 dark:text-blue-400 border-blue-500/30';
        case 'bank':
            return 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30';
        case 'ngo':
            return 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/30';
        case 'it_tech':
            return 'bg-purple-500/15 text-purple-600 dark:text-purple-400 border-purple-500/30';
        default:
            return 'bg-slate-500/15 text-slate-700 dark:text-slate-300 border-slate-500/30';
    }
}

function getCategoryLabel(cat) {
    if (en.value) {
        switch (cat) {
            case 'government': return 'Govt';
            case 'bank': return 'Bank';
            case 'ngo': return 'NGO';
            case 'it_tech': return 'IT & Tech';
            case 'education': return 'Education';
            default: return 'Private';
        }
    }
    switch (cat) {
        case 'government': return 'সরকারি';
        case 'bank': return 'ব্যাংক';
        case 'ngo': return 'এনজিও';
        case 'it_tech': return 'আইটি ও টেক';
        case 'education': return 'শিক্ষা প্রতিষ্ঠান';
        default: return 'বেসরকারি';
    }
}

function formatDate(d) {
    if (!d) return en.value ? 'Soon' : 'শীঘ্রই';
    return new Date(d).toLocaleDateString(en.value ? 'en-US' : 'bn-BD', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

<template>
    <SeoHead v-if="seo" :seo="seo" />
    <Head v-else :title="en ? 'Job Circulars & Career 2026 - Govt, Bank & Top Jobs' : 'চাকরির খবর ও সার্কুলার ২০২৬ - সরকারি, ব্যাংক ও শীর্ষ জবস'" />
    <PublicLayout>
        <!-- Hero Header -->
        <section class="relative overflow-hidden bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-[#0b0f19] dark:via-[#111827] dark:to-[var(--bg)] border-b border-gray-200 dark:border-white/5 py-12 md:py-16">
            <div class="pointer-events-none absolute -top-32 left-1/2 -translate-x-1/2 h-96 w-96 rounded-full bg-[var(--primary)]/15 blur-[120px]"></div>

            <div class="max-w-7xl mx-auto px-4 md:px-6 relative z-10">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider mb-4">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                        {{ en ? 'Daily Updated Circular Portal' : 'দৈনিক আপডেটকৃত সার্কুলার পোর্টাল' }}
                    </span>
                    <h1 class="font-heading text-3xl md:text-5xl font-black text-gray-900 dark:text-white leading-tight">
                        {{ en ? 'Job Circulars & Recruitment' : 'চাকরির খবর ও সার্কুলার' }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-amber-500">2026</span>
                    </h1>
                    <p class="mt-3 text-sm md:text-base text-gray-600 dark:text-[#a2aabc] leading-relaxed">
                        {{ en ? 'Verified circulars from BCS, Government Ministries, Bangladesh Bank, state banks, and leading private organizations. 100% free with direct apply links.' : 'বিসিএস, সরকারি মন্ত্রণালয়, বাংলাদেশ ব্যাংক, রাষ্ট্রায়ত্ত ব্যাংক ও শীর্ষস্থানীয় বেসরকারি প্রতিষ্ঠানের সর্বশেষ নিয়োগ বিজ্ঞপ্তি এক প্ল্যাটফর্মে। সম্পূর্ণ ফ্রি ও লগইন ছাড়া আবেদন লিংকসহ।' }}
                    </p>
                </div>

                <!-- Search & Filters Bar -->
                <div class="mt-8 flex flex-col md:flex-row gap-3">
                    <div class="relative flex-1">
                        <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" />
                        <input
                            v-model="search"
                            type="text"
                            :placeholder="en ? 'Search by organization, designation or location (e.g. Bangladesh Bank, Assistant Director)...' : 'প্রতিষ্ঠানের নাম, পদের নাম বা এলাকা লিখে খুঁজুন (যেমন: বাংলাদেশ ব্যাংক, সহকারী পরিচালক)...'"
                            class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-white dark:bg-[#1c2233] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white placeholder-gray-400 text-sm focus:outline-none focus:border-[var(--primary)] shadow-sm dark:shadow-lg"
                        />
                    </div>
                    <Link
                        href="/calculators"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl bg-white dark:bg-[#1e273a] hover:bg-gray-100 dark:hover:bg-[#28344e] border border-gray-200 dark:border-white/10 text-gray-800 dark:text-white text-sm font-semibold transition"
                    >
                        <CalculatorIcon class="w-5 h-5 text-amber-500 dark:text-amber-400" />
                        <span>{{ en ? 'Age Calculator' : 'বয়স ক্যালকুলেটর' }}</span>
                    </Link>
                </div>

                <!-- Category Tabs -->
                <div class="mt-5 flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                    <button
                        v-for="cat in categories"
                        :key="cat.key"
                        @click="setCategory(cat.key)"
                        class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all border shrink-0 flex items-center gap-2"
                        :class="category === cat.key ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-md shadow-[var(--primary)]/20' : 'bg-white dark:bg-[#181f2f] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/20'"
                    >
                        <span>{{ cat.label }}</span>
                        <span v-if="cat.key === 'all' && stats?.total" class="px-1.5 py-0.2 rounded-full bg-black/10 dark:bg-white/20 text-[10px]">{{ stats.total }}</span>
                        <span v-else-if="cat.key === 'government' && stats?.govt" class="px-1.5 py-0.2 rounded-full bg-black/10 dark:bg-white/20 text-[10px]">{{ stats.govt }}</span>
                        <span v-else-if="cat.key === 'bank' && stats?.bank" class="px-1.5 py-0.2 rounded-full bg-black/10 dark:bg-white/20 text-[10px]">{{ stats.bank }}</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-10">
            <!-- Job Listings Grid -->
            <div v-if="jobs?.data?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <article
                    v-for="job in jobs.data"
                    :key="job.id"
                    class="group relative flex flex-col justify-between rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] hover:border-gray-300 dark:hover:border-white/20 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:hover:shadow-2xl overflow-hidden p-5 text-gray-900 dark:text-white"
                >
                    <!-- Top Organization & Category -->
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border" :class="getCategoryColor(job.category)">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ getCategoryLabel(job.category) }}
                            </span>
                            <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <CalendarDaysIcon class="w-3.5 h-3.5 text-rose-500 dark:text-rose-400" />
                                <span>{{ en ? 'Ends:' : 'শেষ:' }} <strong>{{ formatDate(job.deadline) }}</strong></span>
                            </span>
                        </div>

                        <div class="flex items-center gap-2 mb-2 text-xs font-semibold text-[var(--primary)]">
                            <BuildingOfficeIcon class="w-4 h-4 shrink-0" />
                            <span class="truncate">{{ job.organization }}</span>
                        </div>

                        <Link :href="`/jobs/${job.slug}`">
                            <h2 class="font-heading text-base font-bold text-gray-900 dark:text-white group-hover:text-[var(--primary)] transition line-clamp-2 leading-snug">
                                {{ job.title }}
                            </h2>
                        </Link>

                        <!-- Key Badges (Vacancy, Salary, Location) -->
                        <div class="mt-4 flex flex-wrap gap-1.5 text-xs">
                            <span v-if="job.vacancy" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-[#202738] text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-white/5">
                                <UserGroupIcon class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400" />
                                <span>{{ en ? 'Vacancy:' : '' }} {{ job.vacancy }}</span>
                            </span>
                            <span v-if="job.salary" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-[#202738] text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-white/5">
                                <BanknotesIcon class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" />
                                <span class="truncate max-w-[170px]">{{ job.salary }}</span>
                            </span>
                            <span v-if="job.location" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-[#202738] text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-white/5">
                                <MapPinIcon class="w-3.5 h-3.5 text-rose-500 dark:text-rose-400" />
                                <span class="truncate max-w-[120px]">{{ job.location }}</span>
                            </span>
                        </div>

                        <!-- Brief description snippet -->
                        <p class="mt-3 text-xs text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                            {{ job.education_requirement ? `${en ? 'Eligibility:' : 'যোগ্যতা:'} ${job.education_requirement}` : job.description }}
                        </p>
                    </div>

                    <!-- Bottom Action Row -->
                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between gap-2">
                        <Link
                            :href="`/job-news/${job.slug}`"
                            class="text-xs font-bold text-gray-900 dark:text-white group-hover:text-[var(--primary)] flex items-center gap-1 transition"
                        >
                            <span>{{ en ? 'View Details' : 'বিস্তারিত সার্কুলার' }}</span>
                            <ArrowRightIcon class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                        </Link>
                        <a
                            v-if="job.apply_url"
                            :href="job.apply_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold shadow-md shadow-emerald-500/20 flex items-center gap-1"
                        >
                            <span>{{ en ? 'Apply' : 'আবেদন' }}</span>
                            <span>↗</span>
                        </a>
                    </div>
                </article>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16 bg-gray-50 dark:bg-[#171c28] rounded-3xl border border-gray-200 dark:border-white/5 p-8 text-gray-900 dark:text-white">
                <BriefcaseIcon class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                <h3 class="font-heading text-lg font-bold text-gray-900 dark:text-white">{{ en ? 'No Circulars Found' : 'কোনো সার্কুলার পাওয়া যায়নি' }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ en ? 'Try searching with different keywords or switch category.' : 'অন্য কোনো কিওয়ার্ড বা ক্যাটাগরি দিয়ে সার্চ করে দেখুন।' }}</p>
            </div>

            <!-- Job Preparation Promo Banner -->
            <div class="mt-12 rounded-3xl border border-gray-200 dark:border-[var(--primary)]/30 bg-white dark:bg-gradient-to-r dark:from-[#171d2b] dark:to-[#221c2c] p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl text-gray-900 dark:text-white">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-3 py-1 rounded-full mb-3">
                        <SparklesIcon class="w-3.5 h-3.5" /> {{ en ? 'BCS & Bank Exam Special' : 'বিসিএস ও ব্যাংক জব স্পেশাল' }}
                    </span>
                    <h3 class="font-heading text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                        {{ en ? 'Complete Job Preparation with Sikhun Job Bundle' : 'চাকরি পরীক্ষার সম্পূর্ণ প্রস্তুতির জন্য শিখুন জব বান্ডেল' }}
                    </h3>
                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">
                        {{ en ? '20 years of solved question archives, unlimited AI mock tests, GK monthly digests, and written evaluation. Prepare effectively from home in 3 or 6 month durations.' : 'বিগত ২০ বছরের প্রশ্নব্যাংক, আনলিমিটেড AI মডেল টেস্ট, কারেন্ট অ্যাফেয়ার্স ডাইজেস্ট ও নির্ভুল রিটেন মূল্যায়ন। ৩ মাস বা ৬ মাসের মেয়াদে প্রস্তুতি নিন ঘরে বসেই।' }}
                    </p>
                </div>
                <Link
                    href="/plans"
                    class="px-6 py-3.5 rounded-2xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-bold shadow-lg shadow-[var(--primary)]/20 shrink-0 flex items-center gap-2"
                >
                    <span>{{ en ? 'Explore Job Bundles' : 'জব প্রিপারেশন বান্ডেল দেখুন' }}</span>
                    <span>→</span>
                </Link>
            </div>
        </div>
    </PublicLayout>
</template>
