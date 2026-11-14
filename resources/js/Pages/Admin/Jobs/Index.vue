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

function shareFacebook(job) {
    if (confirm(`"${job.title}" সার্কুলারটি কি ফেসবুক পেজে পোস্ট করতে চান? এতে ফটো কার্ড সহ পোস্ট হবে এবং প্রথম কমেন্টে সরাসরি লিঙ্ক যাবে।`)) {
        router.post(`/admin/jobs/${job.id}/share-facebook`);
    }
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

        <!-- Mobile Jobs Card List (md:hidden) -->
        <div class="md:hidden space-y-3 mb-6">
            <div
                v-for="job in jobs.data"
                :key="job.id"
                class="card p-4 space-y-3 border border-[var(--border)] bg-[var(--surface)] hover:border-[var(--primary)]/30 transition rounded-2xl"
            >
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <span class="text-xs font-bold text-[var(--primary)] block truncate">{{ job.organization }}</span>
                        <h3 class="font-bold text-[var(--text)] text-sm line-clamp-2 mt-0.5">
                            <Link :href="`/admin/jobs/${job.id}/edit`" class="hover:underline">
                                {{ job.title }}
                            </Link>
                        </h3>
                        <span class="text-[10px] text-[var(--text-muted)] mt-0.5 block">{{ job.location }}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[var(--surface2)] border border-[var(--border)] shrink-0">
                        {{ formatCategory(job.category) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-[var(--surface2)]/50 border border-[var(--border)] text-[11px]">
                    <div>
                        <span class="text-[var(--text-muted)] block text-[10px]">পদ ও বেতন:</span>
                        <span class="text-[var(--text)] font-semibold">{{ job.vacancy || 'নির্দিষ্ট নয়' }}</span>
                        <span class="text-[10px] text-[var(--text-muted)] block truncate">{{ job.salary || 'স্কেল অনুযায়ী' }}</span>
                    </div>
                    <div>
                        <span class="text-[var(--text-muted)] block text-[10px]">শেষ তারিখ:</span>
                        <span v-if="isExpired(job.deadline)" class="text-rose-500 font-bold">মেয়াদ শেষ</span>
                        <span v-else class="text-emerald-500 font-semibold flex items-center gap-1">
                            <ClockIcon class="w-3 h-3" />
                            {{ new Date(job.deadline).toLocaleDateString() }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2 pt-2 border-t border-[var(--border)]">
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="toggleFeatured(job)"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold transition"
                            :class="job.is_featured ? 'bg-amber-500/20 text-amber-500 border border-amber-500/30' : 'bg-[var(--surface2)] text-[var(--text-muted)]'"
                        >
                            <SparklesIcon class="w-3 h-3" :class="{ 'fill-amber-500 text-amber-500': job.is_featured }" />
                            {{ job.is_featured ? 'ফিচার্ড' : 'সাধারণ' }}
                        </button>
                        <span class="text-[10px] text-[var(--text-muted)]">ভিউ: {{ job.views_count }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            @click="shareFacebook(job)"
                            class="p-1.5 rounded-lg border border-blue-500/20 bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 relative"
                            :title="job.fb_shared_count > 0 ? `পুনরায় ফেসবুকে শেয়ার করুন (পূর্বে ${job.fb_shared_count} বার শেয়ার করা হয়েছে)` : 'ফেসবুকে পোস্ট করুন'"
                        >
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            <span v-if="job.fb_shared_count > 0" class="absolute -top-1 -right-1 px-1 rounded-full text-[8px] font-bold bg-blue-600 text-white leading-tight">
                                {{ job.fb_shared_count }}
                            </span>
                        </button>
                        <a
                            :href="`/jobs/${job.slug}`"
                            target="_blank"
                            class="p-1.5 rounded-lg border border-[var(--border)] text-[var(--text-muted)] hover:text-[var(--text)]"
                            title="পাবলিক পেজ"
                        >
                            <EyeIcon class="w-4 h-4" />
                        </a>
                        <Link
                            :href="`/admin/jobs/${job.id}/edit`"
                            class="p-1.5 rounded-lg border border-[var(--border)] text-[var(--text-muted)] hover:text-[var(--text)]"
                            title="এডিট"
                        >
                            <PencilSquareIcon class="w-4 h-4" />
                        </Link>
                        <ConfirmButton
                            @confirm="deleteJob(job)"
                            message="আপনি কি নিশ্চিতভাবে এই সার্কুলারটি ডিলিট করতে চান?"
                            class="p-1.5 rounded-lg border border-rose-500/30 text-rose-500 hover:bg-rose-500/10"
                            title="ডিলিট"
                        >
                            <TrashIcon class="w-4 h-4" />
                        </ConfirmButton>
                    </div>
                </div>
            </div>
            <div v-if="!jobs.data?.length" class="p-8 text-center text-xs text-[var(--text-muted)] card">
                কোনো চাকরির সার্কুলার পাওয়া যায়নি।
            </div>
        </div>

        <!-- Desktop Jobs Table (hidden md:block) -->
        <div class="card overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-xs mb-6 hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[var(--surface2)] text-[var(--text-muted)] uppercase tracking-wider font-bold border-b border-[var(--border)]">
                        <tr>
                            <th class="py-3.5 px-4">সার্কুলারের শিরোনাম ও প্রতিষ্ঠান</th>
                            <th class="py-3.5 px-3 whitespace-nowrap">ক্যাটাগরি</th>
                            <th class="py-3.5 px-3 whitespace-nowrap">পদ ও বেতন</th>
                            <th class="py-3.5 px-3 whitespace-nowrap">শেষ তারিখ</th>
                            <th class="py-3.5 px-3 text-center whitespace-nowrap">ফেসবুক শেয়ার</th>
                            <th class="py-3.5 px-3 text-center whitespace-nowrap">ফিচার্ড</th>
                            <th class="py-3.5 px-3 text-center whitespace-nowrap">ভিউ</th>
                            <th class="py-3.5 px-4 text-right whitespace-nowrap">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)] text-[var(--text)]">
                        <tr v-for="job in jobs.data" :key="job.id" class="hover:bg-[var(--surface2)]/40 transition">
                            <td class="py-3.5 px-4 max-w-[240px] lg:max-w-md">
                                <div class="font-bold text-sm text-[var(--text)] hover:text-[var(--primary)] transition flex items-center gap-2">
                                    <Link :href="`/admin/jobs/${job.id}/edit`" class="line-clamp-1" :title="job.title">
                                        {{ job.title }}
                                    </Link>
                                    <a :href="`/jobs/${job.slug}`" target="_blank" title="পাবলিক পেজ দেখুন" class="text-[var(--text-muted)] hover:text-[var(--primary)] shrink-0">
                                        <EyeIcon class="w-3.5 h-3.5" />
                                    </a>
                                </div>
                                <div class="text-[11px] text-[var(--text-muted)] mt-1 flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1 font-medium truncate max-w-[180px]" :title="job.organization">
                                        <BuildingOfficeIcon class="w-3 h-3 text-[var(--text-muted)] shrink-0" />
                                        {{ job.organization }}
                                    </span>
                                    <span>•</span>
                                    <span class="truncate max-w-[120px]">{{ job.location }}</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[var(--surface2)] border border-[var(--border)]">
                                    {{ formatCategory(job.category) }}
                                </span>
                            </td>

                            <td class="py-3.5 px-3 max-w-[150px]">
                                <div class="font-semibold text-[var(--text)] truncate">{{ job.vacancy || 'নির্দিষ্ট নয়' }}</div>
                                <div class="text-[11px] text-[var(--text-muted)] truncate" :title="job.salary">{{ job.salary || 'স্কেল অনুযায়ী' }}</div>
                            </td>

                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <div v-if="isExpired(job.deadline)" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500 bg-rose-500/10 px-2 py-0.5 rounded-full">
                                    মেয়াদ শেষ
                                </div>
                                <div v-else class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <ClockIcon class="w-3.5 h-3.5 shrink-0" />
                                    {{ new Date(job.deadline).toLocaleDateString() }}
                                </div>
                            </td>

                            <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                <div v-if="job.fb_shared_count > 0" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20" :title="'সর্বশেষ শেয়ার: ' + (job.fb_last_shared_at ? new Date(job.fb_last_shared_at).toLocaleDateString() : 'পূর্বে')">
                                    <svg class="w-3.5 h-3.5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    <span>শেয়ার্ড ({{ job.fb_shared_count }} বার)</span>
                                </div>
                                <div v-else class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium text-[var(--text-muted)] bg-[var(--surface2)] border border-[var(--border)]">
                                    <span>শেয়ার হয়নি</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-3 text-center whitespace-nowrap">
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

                            <td class="py-3.5 px-3 text-center font-mono font-bold text-xs text-[var(--text-muted)] whitespace-nowrap">
                                {{ job.views_count }}
                            </td>

                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click="shareFacebook(job)"
                                        class="p-1.5 rounded-lg border border-blue-500/20 bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 relative transition"
                                        :title="job.fb_shared_count > 0 ? `পুনরায় ফেসবুকে শেয়ার করুন (পূর্বে ${job.fb_shared_count} বার শেয়ার করা হয়েছে)` : 'ফেসবুক পেজে পোস্ট করুন (PagePilotBD)'"
                                    >
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        <span v-if="job.fb_shared_count > 0" class="absolute -top-1 -right-1 px-1 rounded-full text-[8px] font-bold bg-blue-600 text-white leading-tight">
                                            {{ job.fb_shared_count }}
                                        </span>
                                    </button>

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
