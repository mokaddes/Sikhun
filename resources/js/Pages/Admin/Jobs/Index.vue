<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import {
    BriefcaseIcon,
    PlusIcon,
    ArrowPathIcon,
    MagnifyingGlassIcon,
    PencilSquareIcon,
    TrashIcon,
    EyeIcon,
    SparklesIcon,
    CheckCircleIcon,
    ClockIcon,
    BuildingOfficeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    jobs: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');
const category = ref(props.filters?.category ?? '');
const status = ref(props.filters?.status ?? '');
const isScraping = ref(false);

function applyFilters() {
    router.get('/admin/jobs', {
        search: search.value || undefined,
        category: category.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true });
}

function resetFilters() {
    search.value = '';
    category.value = '';
    status.value = '';
    applyFilters();
}

function triggerScrape() {
    if (isScraping.value) return;
    isScraping.value = true;
    router.post('/admin/jobs/scrape-now', {}, {
        preserveScroll: true,
        onFinish: () => {
            isScraping.value = false;
        },
    });
}

function toggleFeatured(job) {
    router.patch(`/admin/jobs/${job.id}/toggle-featured`, {}, { preserveScroll: true });
}

function deleteJob(job) {
    router.delete(`/admin/jobs/${job.id}`, { preserveScroll: true });
}

function formatCategory(cat) {
    const map = {
        government: 'সরকারি চাকরি',
        bank: 'ব্যাংক জব',
        private: 'প্রাইভেট কোম্পানি',
        ngo: 'এনজিও ও উন্নয়ন সংস্থা',
        it_tech: 'আইটি ও সফটওয়্যার',
        education: 'শিক্ষা ও শিক্ষকতা',
    };
    return map[cat] || cat;
}

function isExpired(date) {
    return new Date(date) < new Date(new Date().setHours(0,0,0,0));
}
</script>

<template>
    <Head title="চাকরির সার্কুলার ম্যানেজমেন্ট" />

    <AdminLayout>
        <!-- Header -->
        <header class="anim-rise mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="eyebrow">ক্যারিয়ার হাব</span>
                <h1 class="mt-1 flex items-center gap-2 font-heading text-2xl font-extrabold sm:text-3xl text-[var(--text)]">
                    <BriefcaseIcon class="h-8 w-8 text-[var(--primary)]" /> চাকরির সার্কুলার
                </h1>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    বাংলাদেশের সরকারি ও বেসরকারি চাকরি ও ভর্তি সার্কুলার নিয়ন্ত্রণ এবং লাইভ অটো-স্ক্র্যাপিং।
                </p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <button
                    type="button"
                    @click="triggerScrape"
                    :disabled="isScraping"
                    class="btn btn-outline text-xs py-2.5 px-4 flex items-center gap-2 font-bold hover:border-[var(--primary)]"
                >
                    <ArrowPathIcon class="w-4 h-4" :class="{ 'animate-spin': isScraping }" />
                    {{ isScraping ? 'স্ক্র্যাপ হচ্ছে...' : 'এখনই সার্কুলার স্ক্র্যাপ করুন' }}
                </button>

                <Link
                    href="/admin/jobs/create"
                    class="btn btn-primary text-xs py-2.5 px-4 flex items-center gap-2 font-bold shadow-md shadow-[var(--primary)]/20"
                >
                    <PlusIcon class="w-4 h-4" />
                    নতুন সার্কুলার যোগ করুন
                </Link>
            </div>
        </header>

        <!-- Stats Counter Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card p-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)]">
                <div class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">মোট সার্কুলার</div>
                <div class="mt-1 text-2xl font-extrabold font-heading text-[var(--text)]">{{ stats.total }}</div>
            </div>
            <div class="card p-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)]">
                <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">সক্রিয় আবেদন</div>
                <div class="mt-1 text-2xl font-extrabold font-heading text-emerald-600 dark:text-emerald-400">{{ stats.active }}</div>
            </div>
            <div class="card p-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)]">
                <div class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">ফিচার্ড সার্কুলার</div>
                <div class="mt-1 text-2xl font-extrabold font-heading text-amber-600 dark:text-amber-400">{{ stats.featured }}</div>
            </div>
            <div class="card p-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)]">
                <div class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">মোট ভিউয়ার সংখ্যা</div>
                <div class="mt-1 text-2xl font-extrabold font-heading text-[var(--text)]">{{ stats.total_views }}</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card p-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)] mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="relative md:col-span-2">
                    <MagnifyingGlassIcon class="w-4 h-4 text-[var(--text-muted)] absolute left-3 top-3" />
                    <input
                        v-model="search"
                        @keyup.enter="applyFilters"
                        type="text"
                        placeholder="সার্কুলারের শিরোনাম বা প্রতিষ্ঠান খুঁজুন..."
                        class="input pl-9 w-full text-xs py-2"
                    />
                </div>

                <div>
                    <select v-model="category" @change="applyFilters" class="input w-full text-xs py-2">
                        <option value="">সকল ক্যাটাগরি</option>
                        <option value="government">সরকারি চাকরি</option>
                        <option value="bank">ব্যাংক ও আর্থিক প্রতিষ্ঠান</option>
                        <option value="ngo">এনজিও ও উন্নয়ন সংস্থা</option>
                        <option value="private">প্রাইভেট কোম্পানি</option>
                        <option value="it_tech">আইটি ও সফটওয়্যার</option>
                        <option value="education">শিক্ষা ও শিক্ষকতা</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <select v-model="status" @change="applyFilters" class="input flex-1 text-xs py-2">
                        <option value="">সকল স্ট্যাটাস</option>
                        <option value="active">সক্রিয় (চলমান)</option>
                        <option value="expired">মেয়াদ শেষ</option>
                    </select>

                    <button
                        v-if="search || category || status"
                        type="button"
                        @click="resetFilters"
                        class="btn btn-outline text-xs py-2 px-3 shrink-0"
                    >
                        রিসেট
                    </button>
                </div>
            </div>
        </div>

        <!-- Jobs Table -->
        <div class="card overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-xs mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[var(--surface2)] text-[var(--text-muted)] uppercase tracking-wider font-bold border-b border-[var(--border)]">
                        <tr>
                            <th class="py-3.5 px-4">সার্কুলারের শিরোনাম ও প্রতিষ্ঠান</th>
                            <th class="py-3.5 px-3">ক্যাটাগরি</th>
                            <th class="py-3.5 px-3">পদ ও বেতন</th>
                            <th class="py-3.5 px-3">শেষ তারিখ</th>
                            <th class="py-3.5 px-3 text-center">ফিচার্ড</th>
                            <th class="py-3.5 px-3 text-center">ভিউ</th>
                            <th class="py-3.5 px-4 text-right">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)] text-[var(--text)]">
                        <tr v-for="job in jobs.data" :key="job.id" class="hover:bg-[var(--surface2)]/40 transition">
                            <td class="py-3.5 px-4 max-w-xs sm:max-w-md">
                                <div class="font-bold text-sm text-[var(--text)] hover:text-[var(--primary)] transition flex items-center gap-2">
                                    <Link :href="`/admin/jobs/${job.id}/edit`">
                                        {{ job.title }}
                                    </Link>
                                    <a :href="`/jobs/${job.slug}`" target="_blank" title="পাবলিক পেজ দেখুন" class="text-[var(--text-muted)] hover:text-[var(--primary)] shrink-0">
                                        <EyeIcon class="w-3.5 h-3.5" />
                                    </a>
                                </div>
                                <div class="text-[11px] text-[var(--text-muted)] mt-1 flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1 font-medium">
                                        <BuildingOfficeIcon class="w-3 h-3 text-[var(--text-muted)]" />
                                        {{ job.organization }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ job.location }}</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[var(--surface2)] border border-[var(--border)]">
                                    {{ formatCategory(job.category) }}
                                </span>
                            </td>

                            <td class="py-3.5 px-3">
                                <div class="font-semibold text-[var(--text)]">{{ job.vacancy || 'নির্দিষ্ট নয়' }}</div>
                                <div class="text-[11px] text-[var(--text-muted)] truncate max-w-[150px]">{{ job.salary || 'স্কেল অনুযায়ী' }}</div>
                            </td>

                            <td class="py-3.5 px-3">
                                <div v-if="isExpired(job.deadline)" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500 bg-rose-500/10 px-2 py-0.5 rounded-full">
                                    মেয়াদ শেষ
                                </div>
                                <div v-else class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <ClockIcon class="w-3.5 h-3.5 shrink-0" />
                                    {{ new Date(job.deadline).toLocaleDateString() }}
                                </div>
                            </td>

                            <td class="py-3.5 px-3 text-center">
                                <button
                                    type="button"
                                    @click="toggleFeatured(job)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold transition"
                                    :class="job.is_featured ? 'bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30' : 'bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
                                >
                                    <SparklesIcon class="w-3.5 h-3.5" :class="{ 'fill-amber-500 text-amber-500': job.is_featured }" />
                                    {{ job.is_featured ? 'ফিচার্ড' : 'সাধারণ' }}
                                </button>
                            </td>

                            <td class="py-3.5 px-3 text-center font-mono font-bold text-xs text-[var(--text-muted)]">
                                {{ job.views_count }}
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <Link
                                        :href="`/admin/jobs/${job.id}/edit`"
                                        class="p-1.5 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-[var(--text)] transition"
                                        title="এডিট করুন"
                                    >
                                        <PencilSquareIcon class="w-4 h-4" />
                                    </Link>

                                    <ConfirmButton
                                        @confirm="deleteJob(job)"
                                        message="আপনি কি নিশ্চিতভাবে এই সার্কুলারটি ডিলিট করতে চান?"
                                        class="p-1.5 rounded-lg border border-rose-500/30 text-rose-500 hover:bg-rose-500/10 transition"
                                        title="ডিলিট করুন"
                                    >
                                        <TrashIcon class="w-4 h-4" />
                                    </ConfirmButton>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!jobs.data?.length">
                            <td colspan="7" class="py-10 text-center text-xs text-[var(--text-muted)]">
                                কোনো চাকরির সার্কুলার পাওয়া যায়নি। নতুন সার্কুলার তৈরি করুন অথবা উপর থেকে স্ক্র্যাপ সম্পন্ন করুন।
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="jobs.links && jobs.links.length > 3" class="p-4 border-t border-[var(--border)]">
                <Pagination :links="jobs.links" />
            </div>
        </div>
    </AdminLayout>
</template>
