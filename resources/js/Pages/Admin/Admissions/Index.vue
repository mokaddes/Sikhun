<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import {
    AcademicCapIcon,
    PlusIcon,
    ArrowPathIcon,
    MagnifyingGlassIcon,
    PencilSquareIcon,
    TrashIcon,
    EyeIcon,
    CheckCircleIcon,
    ClockIcon,
    BuildingLibraryIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    notices: Object,
    filters: Object,
    counts: Object,
});

const search = ref(props.filters?.search ?? '');
const type = ref(props.filters?.type ?? '');
const status = ref(props.filters?.status ?? '');
const isScraping = ref(false);
const previewNotice = ref(null);

function applyFilters() {
    router.get('/admin/admissions', {
        search: search.value || undefined,
        type: type.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true });
}

function resetFilters() {
    search.value = '';
    type.value = '';
    status.value = '';
    applyFilters();
}

function triggerScrape() {
    if (isScraping.value) return;
    isScraping.value = true;
    router.post('/admin/admissions/scrape-now', {}, {
        preserveScroll: true,
        onFinish: () => {
            isScraping.value = false;
        },
    });
}

function togglePublish(notice) {
    router.patch(`/admin/admissions/${notice.id}/toggle-publish`, {}, { preserveScroll: true });
}

function deleteNotice(notice) {
    router.delete(`/admin/admissions/${notice.id}`, { preserveScroll: true });
}

function shareFacebook(item) {
    if (confirm(`"${item.title}" বিজ্ঞপ্তিটি কি ফেসবুক পেজে পোস্ট করতে চান? এতে ফটো কার্ড সহ পোস্ট হবে এবং প্রথম কমেন্টে সরাসরি লিঙ্ক যাবে।`)) {
        router.post(`/admin/admissions/${item.id}/share-facebook`);
    }
}

function formatType(t) {
    switch (t) {
        case 'public': return 'পাবলিক বিশ্ববিদ্যালয়';
        case 'guccho': return 'গুচ্ছ বিশ্ববিদ্যালয়';
        case 'private': return 'বেসরকারি বিশ্ববিদ্যালয়';
        default: return 'বিশ্ববিদ্যালয়';
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="ভর্তি বিজ্ঞপ্তি ব্যবস্থাপনা - Admin" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                        <AcademicCapIcon class="w-7 h-7 text-orange-500" />
                        <span>বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি ব্যবস্থাপনা</span>
                    </h1>
                    <p class="text-sm text-gray-400 mt-1">
                        পাবলিক, গুচ্ছ ও বেসরকারি বিশ্ববিদ্যালয়ের অফিসিয়াল সার্কুলার সংগ্রহ, ড্রাফট রিভিউ ও প্রকাশ।
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        @click="triggerScrape"
                        :disabled="isScraping"
                        class="btn btn-secondary inline-flex items-center gap-2 text-xs"
                    >
                        <ArrowPathIcon class="w-4 h-4" :class="{ 'animate-spin': isScraping }" />
                        <span>{{ isScraping ? 'স্ক্র্যাপ হচ্ছে...' : 'এখনই স্ক্র্যাপ করুন' }}</span>
                    </button>
                    <Link href="/admin/admissions/create" class="btn btn-primary inline-flex items-center gap-2 text-xs">
                        <PlusIcon class="w-4 h-4" />
                        <span>নতুন ভর্তি বিজ্ঞপ্তি</span>
                    </Link>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="card p-4">
                    <div class="text-xs text-gray-400">মোট সার্কুলার</div>
                    <div class="text-xl font-bold text-white mt-1">{{ counts.total }}</div>
                </div>
                <div class="card p-4 border-amber-500/30 bg-amber-500/5">
                    <div class="text-xs text-amber-400 flex items-center gap-1">
                        <ClockIcon class="w-3.5 h-3.5" />
                        <span>অপেক্ষমাণ ড্রাফট</span>
                    </div>
                    <div class="text-xl font-bold text-amber-400 mt-1">{{ counts.draft }}</div>
                </div>
                <div class="card p-4 border-emerald-500/30 bg-emerald-500/5">
                    <div class="text-xs text-emerald-400 flex items-center gap-1">
                        <CheckCircleIcon class="w-3.5 h-3.5" />
                        <span>প্রকাশিত</span>
                    </div>
                    <div class="text-xl font-bold text-emerald-400 mt-1">{{ counts.published }}</div>
                </div>
                <div class="card p-4">
                    <div class="text-xs text-blue-400">পাবলিক</div>
                    <div class="text-xl font-bold text-blue-400 mt-1">{{ counts.public }}</div>
                </div>
                <div class="card p-4">
                    <div class="text-xs text-purple-400">গুচ্ছ</div>
                    <div class="text-xl font-bold text-purple-400 mt-1">{{ counts.guccho }}</div>
                </div>
                <div class="card p-4">
                    <div class="text-xs text-gray-400">বেসরকারি</div>
                    <div class="text-xl font-bold text-gray-300 mt-1">{{ counts.private }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card p-4 flex flex-col md:flex-row gap-3 items-center justify-between">
                <div class="flex-1 w-full md:w-auto relative">
                    <MagnifyingGlassIcon class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="বিশ্ববিদ্যালয়ের নাম, শিরোনাম বা কীওয়ার্ড..."
                        class="input pl-9 w-full text-xs"
                        @keyup.enter="applyFilters"
                    />
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <select v-model="type" class="input text-xs" @change="applyFilters">
                        <option value="">সকল ক্যাটাগরি</option>
                        <option value="public">পাবলিক বিশ্ববিদ্যালয়</option>
                        <option value="guccho">গুচ্ছ বিশ্ববিদ্যালয়</option>
                        <option value="private">বেসরকারি বিশ্ববিদ্যালয়</option>
                    </select>

                    <select v-model="status" class="input text-xs" @change="applyFilters">
                        <option value="">সকল স্ট্যাটাস</option>
                        <option value="draft">ড্রাফট (Draft)</option>
                        <option value="published">প্রকাশিত (Published)</option>
                    </select>

                    <button @click="resetFilters" class="btn btn-ghost text-xs">রিসেট</button>
                </div>
            </div>

            <!-- Notices Table -->
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table w-full text-left text-xs">
                        <thead class="border-b border-white/5 bg-white/[0.02] text-gray-400 uppercase">
                            <tr>
                                <th class="p-3">প্রতিষ্ঠান ও শিরোনাম</th>
                                <th class="p-3">ক্যাটাগরি</th>
                                <th class="p-3">শেষ তারিখ</th>
                                <th class="p-3">জিপিএ / ফি</th>
                                <th class="p-3">স্ট্যাটাস</th>
                                <th class="p-3 text-right">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr v-for="item in notices.data" :key="item.id" class="hover:bg-white/[0.02]">
                                <td class="p-3 max-w-sm">
                                    <div class="font-bold text-orange-400 text-[11px] mb-0.5">{{ item.institution_name }}</div>
                                    <div class="font-medium text-white truncate">{{ item.title }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">সেশন: {{ item.academic_year }}</div>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded-full text-[10px] font-bold border',
                                            item.university_type === 'public' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' :
                                            item.university_type === 'guccho' ? 'bg-purple-500/10 text-purple-400 border-purple-500/20' :
                                            'bg-amber-500/10 text-amber-400 border-amber-500/20'
                                        ]"
                                    >
                                        {{ formatType(item.university_type) }}
                                    </span>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <div class="text-white font-medium">{{ item.application_deadline || 'ঘোষণা সাপেক্ষে' }}</div>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <div class="text-gray-300">{{ item.total_gpa_required || 'সার্কুলার দেখুন' }}</div>
                                    <div class="text-emerald-400 text-[10px]">{{ item.application_fee || '-' }}</div>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <button
                                        @click="togglePublish(item)"
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-[10px] font-bold transition flex items-center gap-1',
                                            item.status === 'published'
                                                ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20'
                                                : 'bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20'
                                        ]"
                                        :title="item.status === 'published' ? 'ড্রাফট করতে ক্লিক করুন' : 'পাবলিশ করতে ক্লিক করুন'"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full" :class="item.status === 'published' ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                                        <span>{{ item.status === 'published' ? 'Published' : 'Draft' }}</span>
                                    </button>
                                </td>
                                <td class="p-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Share to Facebook via PagePilotBD -->
                                        <button
                                            @click="shareFacebook(item)"
                                            class="p-1.5 rounded hover:bg-blue-500/20 text-blue-400 transition"
                                            title="ফেসবুক পেজে পোস্ট করুন (PagePilotBD)"
                                        >
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        </button>
                                        <!-- Pre-publish Preview Modal trigger -->
                                        <button
                                            @click="previewNotice = item"
                                            class="p-1.5 rounded hover:bg-white/10 text-gray-300 hover:text-white transition"
                                            title="প্রিভিউ দেখুন"
                                        >
                                            <EyeIcon class="w-4 h-4" />
                                        </button>
                                        <Link
                                            :href="`/admin/admissions/${item.id}/edit`"
                                            class="p-1.5 rounded hover:bg-white/10 text-gray-300 hover:text-white transition"
                                            title="এডিট করুন"
                                        >
                                            <PencilSquareIcon class="w-4 h-4" />
                                        </Link>
                                        <ConfirmButton
                                            @confirm="deleteNotice(item)"
                                            confirm-message="আপনি কি এই ভর্তি বিজ্ঞপ্তিটি মুছে ফেলতে চান?"
                                            class="p-1.5 rounded hover:bg-rose-500/20 text-rose-400 transition"
                                            title="মুছে ফেলুন"
                                        >
                                            <TrashIcon class="w-4 h-4" />
                                        </ConfirmButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="!notices.data?.length" class="p-8 text-center text-gray-400 text-xs">
                    কোনো ভর্তি বিজ্ঞপ্তি পাওয়া যায়নি।
                </div>

                <div v-if="notices.links?.length > 3" class="p-4 border-t border-white/5">
                    <Pagination :links="notices.links" />
                </div>
            </div>
        </div>

        <!-- Pre-publish Preview Modal -->
        <div
            v-if="previewNotice"
            class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
            @click.self="previewNotice = null"
        >
            <div class="relative w-full max-w-4xl bg-[#151923] border border-white/10 rounded-3xl p-6 md:p-8 max-h-[90vh] overflow-y-auto text-white">
                <div class="flex items-center justify-between pb-4 border-b border-white/10 mb-6">
                    <div>
                        <span class="text-xs font-bold text-orange-400 uppercase tracking-wider">প্রি-পাবলিশ প্রিভিউ</span>
                        <h2 class="text-xl font-bold mt-1">{{ previewNotice.title }}</h2>
                    </div>
                    <button
                        @click="previewNotice = null"
                        class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center"
                    >
                        ✕
                    </button>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 rounded-xl bg-white/5 border border-white/5 text-xs mb-6">
                    <div>
                        <span class="text-gray-400 block">প্রতিষ্ঠান:</span>
                        <strong class="text-white">{{ previewNotice.institution_name }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block">ক্যাটাগরি:</span>
                        <strong class="text-white">{{ formatType(previewNotice.university_type) }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block">শেষ তারিখ:</span>
                        <strong class="text-red-400">{{ previewNotice.application_deadline || 'TBA' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block">আবেদন ফি:</span>
                        <strong class="text-emerald-400">{{ previewNotice.application_fee || 'TBA' }}</strong>
                    </div>
                </div>

                <div v-if="previewNotice.notice_image_url" class="mb-6 text-center">
                    <img :src="previewNotice.notice_image_url" alt="Notice Preview" class="max-h-72 object-contain mx-auto rounded-xl border border-white/10" />
                </div>

                <div class="prose prose-invert max-w-none text-xs leading-relaxed border-t border-white/10 pt-4" v-html="previewNotice.description" />

                <div class="mt-8 pt-4 border-t border-white/10 flex items-center justify-end gap-3">
                    <button @click="previewNotice = null" class="btn btn-ghost text-xs">
                        বন্ধ করুন
                    </button>
                    <button
                        v-if="previewNotice.status === 'draft'"
                        @click="togglePublish(previewNotice); previewNotice.status = 'published'"
                        class="btn btn-primary text-xs"
                    >
                        ১-ক্লিকে প্রকাশ করুন (Publish Now)
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
