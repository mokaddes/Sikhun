<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';
import {
    CommandLineIcon,
    CpuChipIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    EyeIcon,
    TrashIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    BoltIcon,
    ClipboardDocumentIcon,
    ClipboardDocumentCheckIcon,
    XMarkIcon,
    ArrowPathIcon,
    UserIcon,
    AcademicCapIcon,
    ShieldCheckIcon,
    SparklesIcon,
    CalendarIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    logs: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    stats: {
        type: Object,
        default: () => ({
            total_calls: 0,
            successful_calls: 0,
            failed_calls: 0,
            total_tokens: 0,
            avg_latency_ms: 0,
        }),
    },
    useCases: {
        type: Object,
        default: () => ({}),
    },
    providers: {
        type: Array,
        default: () => [],
    },
});

const { t, locale } = useI18n();

// Filter states
const search = ref(props.filters.search ?? '');
const useCase = ref(props.filters.use_case ?? '');
const userType = ref(props.filters.user_type ?? '');
const providerId = ref(props.filters.provider_id ?? '');
const status = ref(props.filters.status ?? '');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo = ref(props.filters.date_to ?? '');

let debounceTimer = null;
function applyFilters() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/admin/ai-logs', {
            search: search.value || undefined,
            use_case: useCase.value || undefined,
            user_type: userType.value || undefined,
            provider_id: providerId.value || undefined,
            status: status.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        }, {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        });
    }, 300);
}

watch([search, useCase, userType, providerId, status, dateFrom, dateTo], () => {
    applyFilters();
});

function resetFilters() {
    search.value = '';
    useCase.value = '';
    userType.value = '';
    providerId.value = '';
    status.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    router.get('/admin/ai-logs', {}, { preserveState: true, replace: true });
}

const hasActiveFilters = computed(() => {
    return Boolean(
        search.value ||
        useCase.value ||
        userType.value ||
        providerId.value ||
        status.value ||
        dateFrom.value ||
        dateTo.value
    );
});

// Calculate success rate
const successRate = computed(() => {
    const total = props.stats.total_calls || 0;
    if (total === 0) return 100;
    const successful = props.stats.successful_calls || 0;
    return Math.round((successful / total) * 1000) / 10;
});

function formatTokens(num) {
    if (!num) return '0';
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toLocaleString();
}

function formatDate(isoStr) {
    if (!isoStr) return '-';
    try {
        const d = new Date(isoStr);
        return d.toLocaleString(locale.value === 'bn' ? 'bn-BD' : 'en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    } catch {
        return isoStr;
    }
}

// Color badges for use cases
const useCaseBadges = {
    book_chat: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
    exam_gen: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
    flashcard_gen: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
    essay_grade: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
    schedule_gen: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
    notification_gen: 'bg-pink-500/10 text-pink-600 dark:text-pink-400 border-pink-500/20',
    support_bot: 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border-cyan-500/20',
    book_ocr: 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
    book_retrieval: 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
    book_structure: 'bg-violet-500/10 text-violet-600 dark:text-violet-400 border-violet-500/20',
    blog_translate: 'bg-lime-500/10 text-lime-600 dark:text-lime-400 border-lime-500/20',
    blog_rewrite: 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20',
};

function getUseCaseBadge(key) {
    return useCaseBadges[key] || 'bg-[var(--surface2)] text-[var(--text-muted)] border-[var(--border)]';
}

function getUseCaseLabel(key) {
    return props.useCases[key] || key?.replace(/_/g, ' ') || 'General AI';
}

// User role badge
function getUserRoleBadge(type) {
    switch (type) {
        case 'student':
            return { label: 'Student', color: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20' };
        case 'admin':
            return { label: 'Admin', color: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20' };
        case 'guest':
            return { label: 'Guest', color: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20' };
        default:
            return { label: 'System', color: 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20' };
    }
}

// Latency color
function getLatencyClass(ms) {
    if (!ms) return 'text-[var(--text-muted)]';
    if (ms < 1500) return 'text-emerald-600 dark:text-emerald-400';
    if (ms < 3500) return 'text-amber-600 dark:text-amber-400';
    return 'text-rose-600 dark:text-rose-400';
}

// Inspection Modal state
const selectedLog = ref(null);
const loadingDetail = ref(false);
const copiedField = ref(null);

async function inspectLog(log) {
    loadingDetail.value = true;
    selectedLog.value = log;
    try {
        const { data } = await axios.get(`/admin/ai-logs/${log.id}`);
        selectedLog.value = data;
    } catch {
        // Fallback to table data
    } finally {
        loadingDetail.value = false;
    }
}

function closeInspectModal() {
    selectedLog.value = null;
    copiedField.value = null;
}

function copyToClipboard(text, field) {
    if (!text) return;
    navigator.clipboard.writeText(typeof text === 'object' ? JSON.stringify(text, null, 2) : text);
    copiedField.value = field;
    setTimeout(() => {
        if (copiedField.value === field) copiedField.value = null;
    }, 2000);
}

// Format prompt if it is JSON messages array
function parsePromptMessages(promptStr) {
    if (!promptStr) return null;
    try {
        const parsed = JSON.parse(promptStr);
        if (Array.isArray(parsed)) return parsed;
        return null;
    } catch {
        return null;
    }
}

// Clear Logs Modal state
const showClearModal = ref(false);
const clearDays = ref('30');
const clearing = ref(false);

function submitClearLogs() {
    clearing.value = true;
    const payload = clearDays.value === 'all' ? { all: true } : { days: parseInt(clearDays.value, 10) };
    router.post('/admin/ai-logs/clear', payload, {
        preserveScroll: true,
        onFinish: () => {
            clearing.value = false;
            showClearModal.value = false;
        },
    });
}
</script>

<template>
    <Head :title="t('admin.ai_logs.title')" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--primary)]/10 text-[var(--primary)]">
                        <CommandLineIcon class="h-6 w-6" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="font-heading text-2xl font-extrabold text-[var(--text)]">{{ t('admin.ai_logs.title') }}</h1>
                            <span v-if="logs?.total !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                                {{ logs.total }}
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs sm:text-sm text-[var(--text-muted)]">
                            {{ t('admin.ai_logs.subtitle') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <Link
                    href="/admin/ai-providers"
                    class="inline-flex items-center gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3.5 py-2 text-xs sm:text-sm font-semibold text-[var(--text)] hover:bg-[var(--surface2)] transition-colors shadow-2xs"
                >
                    <CpuChipIcon class="h-4 w-4 text-[var(--primary)]" />
                    {{ t('admin.ai_providers.title') }}
                </Link>
                <button
                    type="button"
                    @click="showClearModal = true"
                    class="inline-flex items-center gap-2 rounded-xl border border-rose-500/20 bg-rose-500/10 px-3.5 py-2 text-xs sm:text-sm font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 transition-colors shadow-2xs"
                >
                    <TrashIcon class="h-4 w-4" />
                    {{ t('admin.ai_logs.clear_logs') }}
                </button>
            </div>
        </div>

        <!-- Metric Summary Cards -->
        <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
            <!-- Total Requests -->
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-2xs">
                <div class="flex items-center justify-between text-xs font-medium text-[var(--text-muted)]">
                    <span>{{ t('admin.ai_logs.total_calls') }}</span>
                    <BoltIcon class="h-4 w-4 text-[var(--primary)]" />
                </div>
                <div class="mt-2 font-heading text-2xl font-black text-[var(--text)]">
                    {{ stats.total_calls?.toLocaleString() || 0 }}
                </div>
                <div class="mt-1 text-[11px] text-[var(--text-muted)]">
                    {{ stats.successful_calls || 0 }} {{ t('common.success').toLowerCase() }}
                </div>
            </div>

            <!-- Success Rate -->
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-2xs">
                <div class="flex items-center justify-between text-xs font-medium text-[var(--text-muted)]">
                    <span>{{ t('admin.ai_logs.success_rate') }}</span>
                    <CheckCircleIcon class="h-4 w-4 text-emerald-500" />
                </div>
                <div class="mt-2 font-heading text-2xl font-black text-emerald-600 dark:text-emerald-400">
                    {{ successRate }}%
                </div>
                <div class="mt-1 text-[11px] text-[var(--text-muted)]">
                    {{ stats.failed_calls || 0 }} failed
                </div>
            </div>

            <!-- Total Tokens -->
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-2xs">
                <div class="flex items-center justify-between text-xs font-medium text-[var(--text-muted)]">
                    <span>{{ t('admin.ai_logs.total_tokens') }}</span>
                    <SparklesIcon class="h-4 w-4 text-amber-500" />
                </div>
                <div class="mt-2 font-heading text-2xl font-black text-[var(--text)]">
                    {{ formatTokens(stats.total_tokens) }}
                </div>
                <div class="mt-1 text-[11px] text-[var(--text-muted)]">
                    Prompt + completion
                </div>
            </div>

            <!-- Avg Latency -->
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-2xs">
                <div class="flex items-center justify-between text-xs font-medium text-[var(--text-muted)]">
                    <span>{{ t('admin.ai_logs.avg_latency') }}</span>
                    <ClockIcon class="h-4 w-4 text-indigo-500" />
                </div>
                <div class="mt-2 font-heading text-2xl font-black" :class="getLatencyClass(stats.avg_latency_ms)">
                    {{ stats.avg_latency_ms ? stats.avg_latency_ms + ' ms' : '-' }}
                </div>
                <div class="mt-1 text-[11px] text-[var(--text-muted)]">
                    Across success calls
                </div>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="mb-5 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-2xs space-y-3">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <!-- Search input -->
                <div class="relative lg:col-span-2">
                    <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                    <input
                        v-model="search"
                        type="text"
                        :placeholder="locale === 'en' ? 'Search user, model, prompt, response...' : 'ব্যবহারকারী, মডেল, প্রম্পট অনুসন্ধান...'"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] py-2 pl-9.5 pr-4 text-xs font-medium text-[var(--text)] placeholder:text-[var(--text-muted)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                    />
                </div>

                <!-- Use Type filter -->
                <div>
                    <select
                        v-model="useCase"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-xs font-medium text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                    >
                        <option value="">{{ t('admin.ai_logs.use_case_filter') }}</option>
                        <option v-for="(label, key) in useCases" :key="key" :value="key">
                            {{ label }}
                        </option>
                    </select>
                </div>

                <!-- User Type filter -->
                <div>
                    <select
                        v-model="userType"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-xs font-medium text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                    >
                        <option value="">{{ t('admin.ai_logs.user_type_filter') }}</option>
                        <option value="student">Student</option>
                        <option value="admin">Admin</option>
                        <option value="guest">Guest</option>
                        <option value="system">System</option>
                    </select>
                </div>

                <!-- Provider filter -->
                <div>
                    <select
                        v-model="providerId"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-xs font-medium text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                    >
                        <option value="">All Providers</option>
                        <option v-for="prov in providers" :key="prov.id" :value="prov.id">
                            {{ prov.name }} ({{ prov.type }})
                        </option>
                    </select>
                </div>

                <!-- Status filter -->
                <div>
                    <select
                        v-model="status"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-xs font-medium text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                    >
                        <option value="">{{ t('admin.ai_logs.status_filter') }}</option>
                        <option value="success">Success</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>

            <!-- Date Range & Reset Row -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-[var(--border)]/60 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[var(--text-muted)] flex items-center gap-1 font-medium">
                        <CalendarIcon class="h-3.5 w-3.5" /> Date:
                    </span>
                    <input
                        v-model="dateFrom"
                        type="date"
                        class="rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-1 text-xs text-[var(--text)] focus:border-[var(--primary)] focus:outline-hidden"
                    />
                    <span class="text-[var(--text-muted)]">to</span>
                    <input
                        v-model="dateTo"
                        type="date"
                        class="rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-1 text-xs text-[var(--text)] focus:border-[var(--primary)] focus:outline-hidden"
                    />
                </div>

                <div v-if="hasActiveFilters" class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="resetFilters"
                        class="text-xs text-[var(--text-muted)] hover:text-[var(--primary)] hover:underline font-medium inline-flex items-center gap-1"
                    >
                        <XMarkIcon class="h-3.5 w-3.5" />
                        {{ locale === 'en' ? 'Reset Filters' : 'ফিল্টার রিসেট করুন' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Logs Datatable -->
        <div class="card overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-4 py-3.5">{{ t('admin.ai_logs.user') }}</th>
                            <th class="px-4 py-3.5">{{ t('admin.ai_logs.use_type') }}</th>
                            <th class="px-4 py-3.5">{{ t('admin.ai_logs.provider') }}</th>
                            <th class="px-4 py-3.5">{{ t('admin.ai_logs.latency') }} & {{ t('admin.ai_logs.tokens') }}</th>
                            <th class="px-4 py-3.5">{{ t('common.status') }}</th>
                            <th class="px-4 py-3.5">{{ t('common.date') }}</th>
                            <th class="px-4 py-3.5 text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr
                            v-for="log in logs.data"
                            :key="log.id"
                            class="hover:bg-[var(--surface2)]/40 transition-colors"
                        >
                            <!-- User / Student Column -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[var(--surface2)] font-semibold text-[var(--text)] border border-[var(--border)] text-xs">
                                        {{ (log.user_name || 'U').charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-[var(--text)] truncate max-w-[140px]" :title="log.user_name">
                                                {{ log.user_name || 'Anonymous' }}
                                            </span>
                                            <span
                                                class="rounded-full border px-1.5 py-0.2 text-[9px] font-semibold"
                                                :class="getUserRoleBadge(log.user_type).color"
                                            >
                                                {{ getUserRoleBadge(log.user_type).label }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-[var(--text-muted)] truncate max-w-[160px]" :title="log.user_email">
                                            {{ log.user_email || '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Use Type Column -->
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center rounded-lg border px-2 py-0.75 text-[11px] font-medium"
                                    :class="getUseCaseBadge(log.use_case)"
                                >
                                    {{ getUseCaseLabel(log.use_case) }}
                                </span>
                            </td>

                            <!-- Provider & Model Column -->
                            <td class="px-4 py-3">
                                <div class="min-w-0">
                                    <div class="font-bold text-[var(--text)] truncate max-w-[150px]">
                                        {{ log.provider_name || log.provider?.name || 'Default Provider' }}
                                    </div>
                                    <div class="font-mono text-[11px] text-[var(--text-muted)] truncate max-w-[170px]" :title="log.model_name">
                                        {{ log.model_name || '-' }}
                                    </div>
                                </div>
                            </td>

                            <!-- Latency & Tokens Column -->
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-0.5 font-mono">
                                    <div class="font-bold" :class="getLatencyClass(log.latency_ms)">
                                        {{ log.latency_ms ? log.latency_ms + ' ms' : '-' }}
                                    </div>
                                    <div class="text-[11px] text-[var(--text-muted)]">
                                        {{ log.total_tokens ? log.total_tokens.toLocaleString() + ' tok' : '-' }}
                                    </div>
                                </div>
                            </td>

                            <!-- Status Column -->
                            <td class="px-4 py-3">
                                <span
                                    v-if="log.status === 'success'"
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 border border-emerald-500/20"
                                >
                                    <CheckCircleIcon class="h-3.5 w-3.5" />
                                    Success
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1 rounded-full bg-rose-500/10 px-2 py-0.5 text-[11px] font-semibold text-rose-600 dark:text-rose-400 border border-rose-500/20"
                                    :title="log.error_message"
                                >
                                    <XCircleIcon class="h-3.5 w-3.5" />
                                    Failed
                                </span>
                            </td>

                            <!-- Date Column -->
                            <td class="px-4 py-3 text-[var(--text-muted)] whitespace-nowrap">
                                {{ formatDate(log.created_at) }}
                            </td>

                            <!-- Actions Column -->
                            <td class="px-4 py-3 text-right">
                                <button
                                    type="button"
                                    @click="inspectLog(log)"
                                    class="inline-flex items-center gap-1 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-2.5 py-1 text-xs font-semibold text-[var(--primary)] hover:bg-[var(--surface2)] transition-colors shadow-2xs"
                                >
                                    <EyeIcon class="h-3.5 w-3.5" />
                                    {{ t('common.view') }}
                                </button>
                            </td>
                        </tr>

                        <tr v-if="!logs.data?.length">
                            <td colspan="7" class="p-12 text-center text-[var(--text-muted)]">
                                <CommandLineIcon class="mx-auto h-10 w-10 text-[var(--text-muted)] opacity-40 mb-2" />
                                <div class="font-heading font-bold text-sm text-[var(--text)]">{{ t('admin.ai_logs.no_logs') }}</div>
                                <div class="text-xs text-[var(--text-muted)] mt-1">
                                    {{ hasActiveFilters ? 'Try adjusting your search filters.' : 'AI calls will automatically appear here as students and features use LLMs.' }}
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="logs.links?.length" class="border-t border-[var(--border)] p-4 flex justify-end">
                <Pagination :links="logs.links" />
            </div>
        </div>

        <!-- Inspection Modal -->
        <div
            v-if="selectedLog"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 sm:p-6"
            @click.self="closeInspectModal"
        >
            <div class="relative flex flex-col w-full max-w-4xl max-h-[90vh] rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-[var(--border)] px-6 py-4 bg-[var(--surface2)]/40">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[var(--primary)]/10 text-[var(--primary)]">
                            <CommandLineIcon class="h-5 w-5" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-heading font-bold text-base text-[var(--text)]">
                                    {{ t('admin.ai_logs.view_details') }} #{{ selectedLog.id }}
                                </h3>
                                <span
                                    class="rounded-lg border px-2 py-0.5 text-xs font-semibold"
                                    :class="getUseCaseBadge(selectedLog.use_case)"
                                >
                                    {{ getUseCaseLabel(selectedLog.use_case) }}
                                </span>
                                <span
                                    v-if="selectedLog.status === 'success'"
                                    class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 border border-emerald-500/20"
                                >
                                    Success
                                </span>
                                <span
                                    v-else
                                    class="rounded-full bg-rose-500/10 px-2 py-0.5 text-xs font-semibold text-rose-600 dark:text-rose-400 border border-rose-500/20"
                                >
                                    Failed
                                </span>
                            </div>
                            <div class="text-xs text-[var(--text-muted)] mt-0.5">
                                {{ formatDate(selectedLog.created_at) }}
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="closeInspectModal"
                        class="rounded-lg p-1.5 text-[var(--text-muted)] hover:bg-[var(--surface2)] hover:text-[var(--text)] transition-colors"
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

                <!-- Modal Content Body -->
                <div class="flex-1 overflow-y-auto p-6 space-y-5">
                    <!-- Key Meta Grid -->
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 rounded-xl border border-[var(--border)] bg-[var(--surface2)]/30 p-3.5 text-xs">
                        <div>
                            <span class="text-[var(--text-muted)] block text-[11px]">User</span>
                            <span class="font-bold text-[var(--text)]">{{ selectedLog.user_name || 'Anonymous' }}</span>
                            <span class="block text-[11px] text-[var(--text-muted)] font-mono">{{ selectedLog.user_email || '-' }}</span>
                        </div>
                        <div>
                            <span class="text-[var(--text-muted)] block text-[11px]">Provider & Model</span>
                            <span class="font-bold text-[var(--text)]">{{ selectedLog.provider_name || 'Default' }}</span>
                            <span class="block text-[11px] text-[var(--text-muted)] font-mono">{{ selectedLog.model_name || '-' }}</span>
                        </div>
                        <div>
                            <span class="text-[var(--text-muted)] block text-[11px]">Latency & Tokens</span>
                            <span class="font-bold" :class="getLatencyClass(selectedLog.latency_ms)">
                                {{ selectedLog.latency_ms ? selectedLog.latency_ms + ' ms' : '-' }}
                            </span>
                            <span class="block text-[11px] text-[var(--text-muted)]">
                                In: {{ selectedLog.input_tokens || 0 }} | Out: {{ selectedLog.output_tokens || 0 }} (Total: {{ selectedLog.total_tokens || 0 }})
                            </span>
                        </div>
                        <div>
                            <span class="text-[var(--text-muted)] block text-[11px]">Client IP</span>
                            <span class="font-mono text-[var(--text)]">{{ selectedLog.ip_address || 'Server / Local' }}</span>
                            <span class="block text-[11px] text-[var(--text-muted)] capitalize">Role: {{ selectedLog.user_type }}</span>
                        </div>
                    </div>

                    <!-- Error Alert if failed -->
                    <div v-if="selectedLog.error_message" class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-xs text-rose-600 dark:text-rose-400">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <ExclamationTriangleIcon class="h-4 w-4" />
                            Error Message
                        </div>
                        <div class="font-mono whitespace-pre-wrap break-all">{{ selectedLog.error_message }}</div>
                    </div>

                    <!-- Prompt Section -->
                    <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-hidden">
                        <div class="flex items-center justify-between border-b border-[var(--border)] px-4 py-2.5 bg-[var(--surface2)]/40">
                            <span class="text-xs font-bold text-[var(--text)] flex items-center gap-1.5">
                                <CommandLineIcon class="h-4 w-4 text-[var(--primary)]" />
                                {{ t('admin.ai_logs.prompt') }}
                            </span>
                            <button
                                type="button"
                                @click="copyToClipboard(selectedLog.prompt, 'prompt')"
                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-[var(--primary)] hover:underline"
                            >
                                <component
                                    :is="copiedField === 'prompt' ? ClipboardDocumentCheckIcon : ClipboardDocumentIcon"
                                    class="h-3.5 w-3.5"
                                />
                                {{ copiedField === 'prompt' ? 'Copied!' : 'Copy' }}
                            </button>
                        </div>
                        <div class="p-4 max-h-72 overflow-y-auto text-xs">
                            <!-- Parsed message bubbles if array of messages -->
                            <div v-if="parsePromptMessages(selectedLog.prompt)" class="space-y-2.5">
                                <div
                                    v-for="(msg, idx) in parsePromptMessages(selectedLog.prompt)"
                                    :key="idx"
                                    class="rounded-xl p-3 border text-xs leading-relaxed"
                                    :class="msg.role === 'system' ? 'bg-[var(--surface2)] border-[var(--border)] text-[var(--text-muted)]' : msg.role === 'user' ? 'bg-[var(--primary)]/10 border-[var(--primary)]/20 text-[var(--text)]' : 'bg-purple-500/10 border-purple-500/20 text-[var(--text)]'"
                                >
                                    <div class="font-bold uppercase tracking-wider text-[10px] mb-1 opacity-75">
                                        {{ msg.role }}
                                    </div>
                                    <div class="whitespace-pre-wrap break-words font-mono">{{ msg.content }}</div>
                                </div>
                            </div>
                            <!-- Raw string fallback -->
                            <div v-else class="whitespace-pre-wrap break-words font-mono text-[var(--text)] leading-relaxed">
                                {{ selectedLog.prompt || '(Empty prompt)' }}
                            </div>
                        </div>
                    </div>

                    <!-- Response Section -->
                    <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-hidden">
                        <div class="flex items-center justify-between border-b border-[var(--border)] px-4 py-2.5 bg-[var(--surface2)]/40">
                            <span class="text-xs font-bold text-[var(--text)] flex items-center gap-1.5">
                                <SparklesIcon class="h-4 w-4 text-emerald-500" />
                                {{ t('admin.ai_logs.response') }}
                            </span>
                            <button
                                type="button"
                                @click="copyToClipboard(selectedLog.response, 'response')"
                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-[var(--primary)] hover:underline"
                            >
                                <component
                                    :is="copiedField === 'response' ? ClipboardDocumentCheckIcon : ClipboardDocumentIcon"
                                    class="h-3.5 w-3.5"
                                />
                                {{ copiedField === 'response' ? 'Copied!' : 'Copy' }}
                            </button>
                        </div>
                        <div class="p-4 max-h-80 overflow-y-auto text-xs whitespace-pre-wrap break-words font-mono text-[var(--text)] leading-relaxed bg-[var(--surface2)]/20">
                            {{ selectedLog.response || (selectedLog.status === 'failed' ? '(No response generated)' : '(Empty response)') }}
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end border-t border-[var(--border)] px-6 py-3.5 bg-[var(--surface2)]/40">
                    <button
                        type="button"
                        @click="closeInspectModal"
                        class="rounded-xl border border-[var(--border)] bg-[var(--surface)] px-4 py-2 text-xs font-semibold text-[var(--text)] hover:bg-[var(--surface2)] transition-colors shadow-2xs"
                    >
                        {{ t('common.close') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Clear Logs Confirmation Modal -->
        <div
            v-if="showClearModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
            @click.self="showClearModal = false"
        >
            <div class="relative w-full max-w-md rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600">
                        <TrashIcon class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-base text-[var(--text)]">{{ t('admin.ai_logs.clear_logs') }}</h3>
                        <p class="text-xs text-[var(--text-muted)]">Clean up historical AI execution logs</p>
                    </div>
                </div>

                <div class="space-y-3 mb-5">
                    <label class="block text-xs font-semibold text-[var(--text)]">Select Retention Policy</label>
                    <select
                        v-model="clearDays"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-xs font-medium text-[var(--text)] focus:border-[var(--primary)] focus:outline-hidden"
                    >
                        <option value="7">Delete logs older than 7 days</option>
                        <option value="30">Delete logs older than 30 days</option>
                        <option value="90">Delete logs older than 90 days</option>
                        <option value="all">Delete ALL logs (truncate table)</option>
                    </select>
                    <p class="text-[11px] text-[var(--text-muted)]">
                        This action is irreversible and frees up database space.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5">
                    <button
                        type="button"
                        @click="showClearModal = false"
                        class="rounded-xl border border-[var(--border)] px-4 py-2 text-xs font-semibold text-[var(--text)] hover:bg-[var(--surface2)] transition-colors"
                    >
                        {{ t('common.cancel') }}
                    </button>
                    <button
                        type="button"
                        @click="submitClearLogs"
                        :disabled="clearing"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700 disabled:opacity-50 transition-colors shadow-2xs"
                    >
                        <ArrowPathIcon v-if="clearing" class="h-3.5 w-3.5 animate-spin" />
                        {{ clearing ? 'Clearing...' : 'Confirm Delete' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
