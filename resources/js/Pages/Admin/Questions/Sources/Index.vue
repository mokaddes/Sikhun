<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import {
    ShieldCheckIcon,
    ShieldExclamationIcon,
    GlobeAltIcon,
    BuildingOfficeIcon,
    ArrowPathIcon,
    PencilSquareIcon,
    TrashIcon,
    PlusIcon,
    CheckCircleIcon,
    XCircleIcon,
    ExclamationTriangleIcon,
    DocumentMagnifyingGlassIcon,
    ScaleIcon,
    ClockIcon,
    CommandLineIcon,
    ArrowTopRightOnSquareIcon,
    FunnelIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    sources: Object,
    stats: Object,
    filters: Object,
});

const filterForm = reactive({
    search: props.filters.search || '',
    source_type: props.filters.source_type || 'all',
    content_category: props.filters.content_category || 'all',
    permission_status: props.filters.permission_status || 'all',
    robots_txt_status: props.filters.robots_txt_status || 'all',
    is_active: props.filters.is_active || 'all',
});

function applyFilters() {
    router.get('/admin/question-sources', filterForm, {
        preserveState: true,
        replace: true,
    });
}

function resetFilters() {
    filterForm.search = '';
    filterForm.source_type = 'all';
    filterForm.content_category = 'all';
    filterForm.permission_status = 'all';
    filterForm.robots_txt_status = 'all';
    filterForm.is_active = 'all';
    applyFilters();
}

// Modal state
const isFormModalOpen = ref(false);
const isLegalModalOpen = ref(false);
const selectedSource = ref(null);
const editingId = ref(null);

const sourceForm = useForm({
    name: '',
    base_url: '',
    source_type: 'official_university',
    institution: '',
    content_category: 'admission',
    crawl_frequency: 'weekly',
    robots_txt_status: 'unchecked',
    tos_review: 'pending',
    tos_notes: '',
    permission_status: 'pending_review',
    permission_document_url: '',
    license_info: 'Educational Fair Use / Public Archive',
    commercial_redistribution: false,
    attribution_requirements: '',
    license_expiry: '',
    rate_limit_rpm: 30,
    crawl_delay_ms: 2000,
    is_active: true,
    notes: '',
});

function openCreateModal() {
    editingId.value = null;
    sourceForm.reset();
    isFormModalOpen.value = true;
}

function openEditModal(src) {
    editingId.value = src.id;
    sourceForm.name = src.name;
    sourceForm.base_url = src.base_url;
    sourceForm.source_type = src.source_type;
    sourceForm.institution = src.institution || '';
    sourceForm.content_category = src.content_category;
    sourceForm.crawl_frequency = src.crawl_frequency;
    sourceForm.robots_txt_status = src.robots_txt_status;
    sourceForm.tos_review = src.tos_review;
    sourceForm.tos_notes = src.tos_notes || '';
    sourceForm.permission_status = src.permission_status;
    sourceForm.permission_document_url = src.permission_document_url || '';
    sourceForm.license_info = src.license_info || 'Educational Fair Use / Public Archive';
    sourceForm.commercial_redistribution = !!src.commercial_redistribution;
    sourceForm.attribution_requirements = src.attribution_requirements || '';
    sourceForm.license_expiry = src.license_expiry ? src.license_expiry.substring(0, 10) : '';
    sourceForm.rate_limit_rpm = src.rate_limit_rpm || 30;
    sourceForm.crawl_delay_ms = src.crawl_delay_ms || 2000;
    sourceForm.is_active = !!src.is_active;
    sourceForm.notes = src.notes || '';
    isFormModalOpen.value = true;
}

function saveSource() {
    if (editingId.value) {
        sourceForm.put(`/admin/question-sources/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                isFormModalOpen.value = false;
                editingId.value = null;
            },
        });
    } else {
        sourceForm.post('/admin/question-sources', {
            preserveScroll: true,
            onSuccess: () => {
                isFormModalOpen.value = false;
                sourceForm.reset();
            },
        });
    }
}

function openLegalModal(src) {
    selectedSource.value = src;
    isLegalModalOpen.value = true;
}

const checkingRobotsId = ref(null);
function checkRobots(src) {
    checkingRobotsId.value = src.id;
    router.post(`/admin/question-sources/${src.id}/check-robots`, {}, {
        preserveScroll: true,
        onFinish: () => {
            checkingRobotsId.value = null;
        },
    });
}

const testingConnectionId = ref(null);
function testConnection(src) {
    testingConnectionId.value = src.id;
    router.post(`/admin/question-sources/${src.id}/test-connection`, {}, {
        preserveScroll: true,
        onFinish: () => {
            testingConnectionId.value = null;
        },
    });
}

function toggleActive(src) {
    router.patch(`/admin/question-sources/${src.id}/toggle-active`, {}, {
        preserveScroll: true,
    });
}

function deleteSource(src) {
    router.delete(`/admin/question-sources/${src.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AdminLayout>
        <Head title="প্রশ্ন সোর্স ও অনুমতি রেজিস্ট্রি (Source & Permission Registry)" />

        <div class="space-y-6 pb-12">
            <!-- Header & Action -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-primary">
                        <ScaleIcon class="w-4 h-4" />
                        <span>Source Registry & Legal Compliance</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        প্রশ্ন সোর্স ও অনুমতি রেজিস্ট্রি
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        বিশ্ববিদ্যালয় ভর্তি ও সরকারি চাকরির প্রশ্ন সংগ্রহের সোর্স ব্যবস্থাপনা, Robots.txt ও কপিরাইট অনুমতি পর্যবেক্ষণ।
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/admin/question-bank"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                    >
                        <span>প্রশ্ন ভাণ্ডার</span>
                    </Link>
                    <button
                        type="button"
                        @click="openCreateModal"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 shadow-sm shadow-primary/20 transition active:scale-95"
                    >
                        <PlusIcon class="w-4 h-4" />
                        <span>নতুন সোর্স রেজিস্টার করুন</span>
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">মোট সোর্স</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="text-xs font-medium text-emerald-600 dark:text-emerald-400">সক্রিয় সোর্স</div>
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ stats.active }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400">সরকারি / ভার্সিটি</div>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ stats.official }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="text-xs font-medium text-purple-600 dark:text-purple-400">এডুকেশনাল আর্কাইভ</div>
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ stats.archives }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="text-xs font-medium text-indigo-600 dark:text-indigo-400">অনুমোদন প্রাপ্ত</div>
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ stats.authorized }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="text-xs font-medium text-teal-600 dark:text-teal-400">Robots.txt অনুমোদিত</div>
                    <div class="text-2xl font-bold text-teal-600 dark:text-teal-400 mt-1">{{ stats.robots_allowed }}</div>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <!-- Search input -->
                    <div class="lg:col-span-2">
                        <input
                            v-model="filterForm.search"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="সোর্সের নাম, বিশ্ববিদ্যালয় বা লিঙ্ক দিয়ে খুঁজুন..."
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary"
                        />
                    </div>

                    <!-- Source Type -->
                    <div>
                        <select
                            v-model="filterForm.source_type"
                            @change="applyFilters"
                            class="w-full text-xs px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-200 focus:ring-primary focus:border-primary"
                        >
                            <option value="all">সকল সোর্স টাইপ</option>
                            <option value="official_university">Official University</option>
                            <option value="official_commission">Official Commission</option>
                            <option value="educational_archive">Educational Archive</option>
                            <option value="public_portal">Public Portal</option>
                        </select>
                    </div>

                    <!-- Content Category -->
                    <div>
                        <select
                            v-model="filterForm.content_category"
                            @change="applyFilters"
                            class="w-full text-xs px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-200 focus:ring-primary focus:border-primary"
                        >
                            <option value="all">সকল ক্যাটাগরি</option>
                            <option value="admission">ভর্তি পরীক্ষা (Admission)</option>
                            <option value="job">চাকরি পরীক্ষা (Job)</option>
                            <option value="both">উভয় (Both)</option>
                        </select>
                    </div>

                    <!-- Permission Status -->
                    <div>
                        <select
                            v-model="filterForm.permission_status"
                            @change="applyFilters"
                            class="w-full text-xs px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-800 dark:text-gray-200 focus:ring-primary focus:border-primary"
                        >
                            <option value="all">সকল পারমিশন স্ট্যাটাস</option>
                            <option value="fair_use_educational">Fair Use Educational</option>
                            <option value="public_domain">Public Domain</option>
                            <option value="authorized">Authorized</option>
                            <option value="pending_review">Pending Review</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="applyFilters"
                            class="flex-1 px-3 py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90 transition"
                        >
                            ফিল্টার
                        </button>
                        <button
                            type="button"
                            @click="resetFilters"
                            class="px-3 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                        >
                            রিসেট
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sources Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50/75 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-5 py-4">সোর্স ও প্রতিষ্ঠান</th>
                                <th class="px-4 py-4">টাইপ ও ক্যাটাগরি</th>
                                <th class="px-4 py-4">Robots.txt</th>
                                <th class="px-4 py-4">অনুমতি ও লাইসেন্স</th>
                                <th class="px-4 py-4">ফ্রিকোয়েন্সি ও লিমিট</th>
                                <th class="px-4 py-4 text-center">সংগৃহীত প্রশ্ন</th>
                                <th class="px-4 py-4 text-center">স্ট্যাটাস</th>
                                <th class="px-5 py-4 text-right">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-gray-700 dark:text-gray-300 text-xs">
                            <tr
                                v-for="src in sources.data"
                                :key="src.id"
                                class="hover:bg-gray-50/60 dark:hover:bg-gray-700/25 transition-colors"
                            >
                                <!-- Source Name & URL -->
                                <td class="px-5 py-4 max-w-xs">
                                    <div class="font-semibold text-gray-900 dark:text-white text-sm line-clamp-1">
                                        {{ src.name }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5 text-gray-500 dark:text-gray-400 text-xs">
                                        <BuildingOfficeIcon class="w-3.5 h-3.5 shrink-0" />
                                        <span class="truncate">{{ src.institution || 'জাতীয় প্রতিষ্ঠান' }}</span>
                                    </div>
                                    <a
                                        :href="src.base_url"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 mt-1 text-[11px] text-primary hover:underline"
                                    >
                                        <span class="truncate max-w-[200px]">{{ src.base_url }}</span>
                                        <ArrowTopRightOnSquareIcon class="w-3 h-3 shrink-0" />
                                    </a>
                                </td>

                                <!-- Type & Category -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-block px-2 py-0.5 rounded-md text-[10px] font-semibold tracking-wide uppercase mb-1"
                                        :class="{
                                            'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': src.source_type === 'official_university',
                                            'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300': src.source_type === 'official_commission',
                                            'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300': src.source_type === 'educational_archive',
                                            'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': !['official_university', 'official_commission', 'educational_archive'].includes(src.source_type)
                                        }"
                                    >
                                        {{ src.source_type.replace('_', ' ') }}
                                    </span>
                                    <div class="text-[11px] text-gray-500 font-medium">
                                        {{ src.content_category === 'admission' ? '🎓 ভর্তি পরীক্ষা' : (src.content_category === 'job' ? '💼 চাকরি পরীক্ষা' : '🎓+💼 উভয়') }}
                                    </div>
                                </td>

                                <!-- Robots.txt Status -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                            :class="{
                                                'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800': src.robots_txt_status === 'allowed',
                                                'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800': src.robots_txt_status === 'disallowed',
                                                'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800': src.robots_txt_status === 'conditional',
                                                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400': src.robots_txt_status === 'unchecked',
                                            }"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full" :class="{
                                                'bg-emerald-500': src.robots_txt_status === 'allowed',
                                                'bg-rose-500': src.robots_txt_status === 'disallowed',
                                                'bg-amber-500': src.robots_txt_status === 'conditional',
                                                'bg-gray-400': src.robots_txt_status === 'unchecked'
                                            }"></span>
                                            <span>{{ src.robots_txt_status }}</span>
                                        </span>
                                        <button
                                            type="button"
                                            @click="checkRobots(src)"
                                            :disabled="checkingRobotsId === src.id"
                                            title="Robots.txt লাইভ পরীক্ষা করুন"
                                            class="p-1 text-gray-400 hover:text-primary rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                        >
                                            <ArrowPathIcon class="w-3.5 h-3.5" :class="{ 'animate-spin': checkingRobotsId === src.id }" />
                                        </button>
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-1">
                                        {{ src.robots_txt_checked_at ? new Date(src.robots_txt_checked_at).toLocaleDateString('bn-BD') : 'অযাচাইকৃত' }}
                                    </div>
                                </td>

                                <!-- Permission & Legal -->
                                <td class="px-4 py-4 max-w-[200px]">
                                    <div class="flex items-center gap-1">
                                        <span
                                            class="inline-block px-2 py-0.5 rounded text-[10px] font-bold"
                                            :class="{
                                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': ['authorized', 'public_domain', 'fair_use_educational'].includes(src.permission_status),
                                                'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': src.permission_status === 'pending_review',
                                                'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300': src.permission_status === 'rejected'
                                            }"
                                        >
                                            {{ src.permission_status }}
                                        </span>
                                        <button
                                            type="button"
                                            @click="openLegalModal(src)"
                                            class="p-1 text-gray-400 hover:text-primary rounded hover:bg-gray-100 dark:hover:bg-gray-700"
                                            title="আইনি ও অ্যাট্রিবিউশন বিবরণ দেখুন"
                                        >
                                            <DocumentMagnifyingGlassIcon class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                    <div class="text-[11px] text-gray-500 truncate mt-0.5" :title="src.license_info">
                                        {{ src.license_info }}
                                    </div>
                                </td>

                                <!-- Frequency & Rate Limit -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ src.crawl_frequency }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ src.rate_limit_rpm }} RPM ({{ src.crawl_delay_ms }}ms delay)
                                    </div>
                                </td>

                                <!-- Questions count -->
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        {{ src.questions_count || src.questions_imported_count || 0 }}
                                    </span>
                                </td>

                                <!-- Active Toggle -->
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <button
                                        type="button"
                                        @click="toggleActive(src)"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="src.is_active ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'"
                                    >
                                        <span
                                            class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="src.is_active ? 'translate-x-4' : 'translate-x-0'"
                                        />
                                    </button>
                                </td>

                                <!-- Action buttons -->
                                <td class="px-5 py-4 text-right whitespace-nowrap space-x-1">
                                    <button
                                        type="button"
                                        @click="testConnection(src)"
                                        :disabled="testingConnectionId === src.id"
                                        class="p-1.5 text-gray-500 hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition"
                                        title="কানেকশন টেস্ট করুন"
                                    >
                                        <CommandLineIcon class="w-4 h-4" :class="{ 'animate-pulse text-primary': testingConnectionId === src.id }" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="openEditModal(src)"
                                        class="p-1.5 text-gray-500 hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition"
                                        title="সম্পাদনা করুন"
                                    >
                                        <PencilSquareIcon class="w-4 h-4" />
                                    </button>
                                    <ConfirmButton
                                        @confirmed="deleteSource(src)"
                                        title="সোর্স মুছে ফেলুন"
                                        message="আপনি কি নিশ্চিত এই প্রশ্ন সোর্সটি মুছে ফেলতে চান?"
                                        class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition"
                                    >
                                        <TrashIcon class="w-4 h-4" />
                                    </ConfirmButton>
                                </td>
                            </tr>
                            <tr v-if="!sources.data || sources.data.length === 0">
                                <td colspan="8" class="text-center py-12 text-gray-400">
                                    কোনো প্রশ্ন সোর্স পাওয়া যায়নি।
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="sources.links && sources.links.length > 3" class="p-4 border-t border-gray-100 dark:border-gray-700">
                    <Pagination :links="sources.links" />
                </div>
            </div>
        </div>

        <!-- Create / Edit Modal -->
        <div
            v-if="isFormModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
        >
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 my-8">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ editingId ? 'প্রশ্ন সোর্স ও লাইসেন্স প্রোফাইল সম্পাদনা' : 'নতুন প্রশ্ন সোর্স রেজিস্টার করুন' }}
                    </h3>
                    <button
                        type="button"
                        @click="isFormModalOpen = false"
                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="saveSource" class="space-y-4 mt-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Source Name -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">সোর্স নাম *</label>
                            <input
                                v-model="sourceForm.name"
                                type="text"
                                required
                                placeholder="উদাঃ University of Dhaka (DU) Admission Portal"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Base URL -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">বেস ইউআরএল (Base URL) *</label>
                            <input
                                v-model="sourceForm.base_url"
                                type="url"
                                required
                                placeholder="https://du.ac.bd/"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Source Type -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">সোর্স টাইপ *</label>
                            <select
                                v-model="sourceForm.source_type"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="official_university">Official University</option>
                                <option value="official_commission">Official Commission</option>
                                <option value="educational_archive">Educational Archive</option>
                                <option value="public_portal">Public Portal</option>
                                <option value="community">Community</option>
                            </select>
                        </div>

                        <!-- Institution -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">বিশ্ববিদ্যালয় / প্রতিষ্ঠান</label>
                            <input
                                v-model="sourceForm.institution"
                                type="text"
                                placeholder="উদাঃ University of Dhaka"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Content Category -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">কন্টেন্ট ক্যাটাগরি *</label>
                            <select
                                v-model="sourceForm.content_category"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="admission">বিশ্ববিদ্যালয় ভর্তি পরীক্ষা (Admission)</option>
                                <option value="job">চাকরি ও বিসিএস (Job)</option>
                                <option value="both">উভয় (Both)</option>
                            </select>
                        </div>

                        <!-- Crawl Frequency -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">ক্রল ফ্রিকোয়েন্সি *</label>
                            <select
                                v-model="sourceForm.crawl_frequency"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="hourly">Hourly</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="manual">Manual Only</option>
                            </select>
                        </div>

                        <!-- Robots.txt Status -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Robots.txt স্ট্যাটাস *</label>
                            <select
                                v-model="sourceForm.robots_txt_status"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="allowed">Allowed (অনুমোদিত)</option>
                                <option value="disallowed">Disallowed (নিষিদ্ধ)</option>
                                <option value="conditional">Conditional (শর্তসাপেক্ষ)</option>
                                <option value="unchecked">Unchecked (অযাচাইকৃত)</option>
                            </select>
                        </div>

                        <!-- Permission Status -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">অনুমতি স্ট্যাটাস (Permission Status) *</label>
                            <select
                                v-model="sourceForm.permission_status"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="fair_use_educational">Fair Use Educational</option>
                                <option value="public_domain">Public Domain</option>
                                <option value="authorized">Authorized</option>
                                <option value="pending_review">Pending Review</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Terms of Service Review -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">শর্তাবলি রিভিউ (Terms Review) *</label>
                            <select
                                v-model="sourceForm.tos_review"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="compliant">Compliant (অনুকূল)</option>
                                <option value="pending">Pending</option>
                                <option value="restricted">Restricted</option>
                                <option value="violation_risk">Violation Risk</option>
                            </select>
                        </div>

                        <!-- License Info -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">লাইসেন্স ইনফরমেশন *</label>
                            <input
                                v-model="sourceForm.license_info"
                                type="text"
                                placeholder="উদাঃ Educational Fair Use / Public Domain"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Rate Limit RPM -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">রেট লিমিট (Requests Per Minute)</label>
                            <input
                                v-model.number="sourceForm.rate_limit_rpm"
                                type="number"
                                min="1"
                                max="300"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Crawl Delay MS -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">ক্রল ডিলে (Milliseconds Delay)</label>
                            <input
                                v-model.number="sourceForm.crawl_delay_ms"
                                type="number"
                                min="500"
                                step="500"
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Attribution Requirements -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">অ্যাট্রিবিউশন রিকোয়ারমেন্টস (Attribution Requirements)</label>
                            <textarea
                                v-model="sourceForm.attribution_requirements"
                                rows="2"
                                placeholder="উদাঃ Source: Dhaka University Past Papers. Educational non-commercial use only."
                                class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            ></textarea>
                        </div>

                        <!-- Commercial Redistribution & Active -->
                        <div class="sm:col-span-2 flex items-center gap-6 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-gray-700 dark:text-gray-300">
                                <input
                                    v-model="sourceForm.commercial_redistribution"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <span>Commercial Redistribution Allowed</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-gray-700 dark:text-gray-300">
                                <input
                                    v-model="sourceForm.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <span>সোর্স সক্রিয় রাখুন (Is Active)</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button
                            type="button"
                            @click="isFormModalOpen = false"
                            class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        >
                            বাতিল
                        </button>
                        <button
                            type="submit"
                            :disabled="sourceForm.processing"
                            class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary/90 disabled:opacity-50"
                        >
                            {{ sourceForm.processing ? 'সংরক্ষণ হচ্ছে...' : (editingId ? 'আপডেট করুন' : 'সোর্স সংরক্ষণ করুন') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Legal Details Modal -->
        <div
            v-if="isLegalModalOpen && selectedSource"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
        >
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2 text-primary font-bold">
                        <ScaleIcon class="w-5 h-5" />
                        <span>আইনি ও লাইসেন্স বিবরণ</span>
                    </div>
                    <button
                        type="button"
                        @click="isLegalModalOpen = false"
                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <div class="text-gray-500 font-medium">সোর্স নাম:</div>
                        <div class="font-bold text-gray-900 dark:text-white mt-0.5">{{ selectedSource.name }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 font-medium">লাইসেন্স:</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5">{{ selectedSource.license_info }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 font-medium">অ্যাট্রিবিউশন রিকোয়ারমেন্টস:</div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-xl text-gray-700 dark:text-gray-300 mt-1 leading-relaxed">
                            {{ selectedSource.attribution_requirements || 'কোনো নির্দিষ্ট অ্যাট্রিবিউশন শর্ত উল্লেখ নেই।' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-500 font-medium">শর্তাবলি রিভিউ নোট:</div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-xl text-gray-700 dark:text-gray-300 mt-1 leading-relaxed">
                            {{ selectedSource.tos_notes || 'কোনো নোট সংরক্ষিত নেই।' }}
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-gray-500">কমার্শিয়াল রিডিসট্রিবিউশন:</span>
                        <span class="font-bold" :class="selectedSource.commercial_redistribution ? 'text-emerald-600' : 'text-rose-500'">
                            {{ selectedSource.commercial_redistribution ? 'Allowed (অনুমোদিত)' : 'Not Allowed (শুধু শিক্ষা ও ফেয়ার ইউজ)' }}
                        </span>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-gray-700">
                    <button
                        type="button"
                        @click="isLegalModalOpen = false"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600"
                    >
                        বন্ধ করুন
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
