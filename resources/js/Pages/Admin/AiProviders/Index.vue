<script setup>
import { ref, reactive } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
import { useI18n } from '@/i18n';
import {
    PlusIcon,
    CommandLineIcon,
    CpuChipIcon,
    PencilSquareIcon,
    TrashIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowPathIcon,
    GlobeAltIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    providers: {
        type: Array,
        default: () => [],
    },
});

const { t, locale } = useI18n();

const testState = reactive({});

const typeMeta = {
    openai_compatible: {
        label: 'OpenAI Compatible',
        color: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        badge: 'Recommended',
    },
    anthropic_compatible: {
        label: 'Anthropic Compatible',
        color: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
    },
    google_gemini: {
        label: 'Google Gemini',
        color: 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
    },
    custom: {
        label: 'Custom API',
        color: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
    },
};

function getTypeInfo(type) {
    return typeMeta[type] || {
        label: type?.replace('_', ' ')?.toUpperCase() || 'Provider',
        color: 'bg-[var(--surface2)] text-[var(--text-muted)] border-[var(--border)]',
    };
}

async function testConnection(provider) {
    testState[provider.id] = { loading: true, success: null, message: '' };
    try {
        const { data } = await axios.post(`/admin/ai-providers/${provider.id}/test`);
        testState[provider.id] = {
            loading: false,
            success: Boolean(data.success),
            message: data.message || (data.success ? t('admin.ai_providers.test_success') : t('admin.ai_providers.test_failed')),
        };
    } catch (e) {
        testState[provider.id] = {
            loading: false,
            success: false,
            message: e.response?.data?.message || t('admin.ai_providers.test_failed'),
        };
    }
}

function getProviderActions(p) {
    return [
        { label: t('common.edit'), href: `/admin/ai-providers/${p.id}/edit`, icon: PencilSquareIcon },
        { divider: true },
        { label: t('common.delete'), href: `/admin/ai-providers/${p.id}`, method: 'delete', danger: true, icon: TrashIcon },
    ];
}
</script>

<template>
    <Head :title="t('admin.ai_providers.title')" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--primary)]/10 text-[var(--primary)]">
                        <CpuChipIcon class="h-6 w-6" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="font-heading text-2xl font-extrabold text-[var(--text)]">{{ t('admin.ai_providers.title') }}</h1>
                            <span v-if="providers?.length !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                                {{ providers.length }}
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs sm:text-sm text-[var(--text-muted)]">
                            {{ locale === 'en' ? 'Configure LLM providers and assign them to specific platform tasks.' : 'এআই প্রোভাইডার কনফিগারেশন এবং ব্যবহারের ক্ষেত্র পরিচালনা করুন।' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <Link
                    href="/admin/ai-logs"
                    class="inline-flex items-center gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3.5 py-2 text-xs sm:text-sm font-semibold text-[var(--text)] hover:bg-[var(--surface2)] transition-colors shadow-xs"
                >
                    <CommandLineIcon class="h-4 w-4 text-[var(--primary)]" />
                    {{ t('admin.ai_logs.title') }}
                </Link>
                <Link
                    href="/admin/ai-providers/create"
                    class="btn btn-primary gap-2"
                >
                    <PlusIcon class="h-4 w-4" />
                    {{ t('admin.ai_providers.new') }}
                </Link>
            </div>
        </div>

        <!-- Provider Cards Grid -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div
                v-for="p in providers"
                :key="p.id"
                class="card rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 transition-all shadow-xs hover:border-[var(--primary)]/30 hover:shadow-md flex flex-col justify-between"
            >
                <div>
                    <!-- Top row: Name, Type badge, Status & Actions -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-heading text-base font-bold text-[var(--text)] truncate">{{ p.name }}</h3>
                                <span
                                    class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[11px] font-semibold"
                                    :class="getTypeInfo(p.type).color"
                                >
                                    {{ getTypeInfo(p.type).label }}
                                    <span
                                        v-if="getTypeInfo(p.type).badge"
                                        class="ml-1 rounded-full bg-emerald-500/20 px-1.5 py-0.2 text-[9px] font-bold text-emerald-700 dark:text-emerald-300"
                                    >
                                        {{ getTypeInfo(p.type).badge }}
                                    </span>
                                </span>
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-[var(--text-muted)]">
                                <span class="font-mono text-[var(--text)] font-medium">Model: {{ p.model_name }}</span>
                                <span v-if="p.max_tokens">Max: {{ p.max_tokens }} tokens</span>
                                <span v-if="p.temperature !== null && p.temperature !== undefined">Temp: {{ p.temperature }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                :class="p.is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-[var(--surface2)] text-[var(--text-muted)]'"
                            >
                                <span class="h-1.5 w-1.5 rounded-full" :class="p.is_active ? 'bg-emerald-500' : 'bg-[var(--text-muted)]'"></span>
                                {{ p.is_active ? t('common.active') : t('common.inactive') }}
                            </span>
                            <TableDropdown :items="getProviderActions(p)" align="right" />
                        </div>
                    </div>

                    <!-- Custom endpoint or headers display if any -->
                    <div v-if="p.api_endpoint" class="mb-3 rounded-lg bg-[var(--surface2)]/60 px-3 py-1.5 text-xs font-mono text-[var(--text-muted)] break-all flex items-center gap-2">
                        <GlobeAltIcon class="h-3.5 w-3.5 shrink-0 text-[var(--text-muted)]" />
                        <span class="truncate">{{ p.api_endpoint }}</span>
                    </div>

                    <!-- Use cases list -->
                    <div class="mb-4">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">
                            {{ locale === 'en' ? 'Assigned Tasks' : 'নির্ধারিত ব্যবহার' }}
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <span
                                v-for="uc in p.use_cases"
                                :key="uc.id"
                                class="inline-flex items-center gap-1 rounded-lg px-2 py-0.75 text-xs font-medium"
                                :class="uc.is_default ? 'bg-[var(--primary)]/15 text-[var(--primary)] font-semibold border border-[var(--primary)]/20' : 'bg-[var(--surface2)] text-[var(--text-muted)]'"
                            >
                                <SparklesIcon v-if="uc.is_default" class="h-3 w-3 text-[var(--primary)]" />
                                <span>{{ uc.use_case.replace(/_/g, ' ') }}</span>
                                <span v-if="uc.is_default" class="text-[10px] opacity-80">(default)</span>
                            </span>
                            <span v-if="!p.use_cases?.length" class="text-xs text-[var(--text-muted)] italic">
                                {{ locale === 'en' ? 'No use cases assigned' : 'কোনো ব্যবহার নির্ধারিত নেই' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer: Test connection button & response state -->
                <div class="pt-3 border-t border-[var(--border)] flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <button
                            type="button"
                            @click="testConnection(p)"
                            :disabled="testState[p.id]?.loading"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-2.5 py-1 text-xs font-medium text-[var(--text)] hover:bg-[var(--surface2)] disabled:opacity-50 transition-colors shadow-2xs"
                        >
                            <ArrowPathIcon
                                class="h-3.5 w-3.5"
                                :class="{ 'animate-spin': testState[p.id]?.loading }"
                            />
                            {{ testState[p.id]?.loading ? t('admin.ai_providers.testing') : t('admin.ai_providers.test_connection') }}
                        </button>

                        <div v-if="testState[p.id] && !testState[p.id].loading" class="flex items-center gap-1.5 text-xs truncate">
                            <CheckCircleIcon v-if="testState[p.id].success" class="h-4 w-4 text-emerald-500 shrink-0" />
                            <XCircleIcon v-else class="h-4 w-4 text-rose-500 shrink-0" />
                            <span
                                class="truncate"
                                :class="testState[p.id].success ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-rose-600 dark:text-rose-400 font-medium'"
                                :title="testState[p.id].message"
                            >
                                {{ testState[p.id].message }}
                            </span>
                        </div>
                    </div>

                    <Link
                        :href="`/admin/ai-providers/${p.id}/edit`"
                        class="text-xs font-semibold text-[var(--primary)] hover:underline inline-flex items-center gap-1"
                    >
                        <PencilSquareIcon class="h-3.5 w-3.5" />
                        {{ t('common.edit') }}
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="!providers.length" class="rounded-2xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)] bg-[var(--surface)] mt-4">
            <CpuChipIcon class="mx-auto h-12 w-12 text-[var(--text-muted)] opacity-50 mb-3" />
            <p class="font-heading font-bold text-base text-[var(--text)]">{{ t('common.no_results') }}</p>
            <p class="text-xs text-[var(--text-muted)] mt-1 mb-4">
                {{ locale === 'en' ? 'Get started by adding your first AI provider.' : 'আপনার প্রথম এআই প্রোভাইডার যোগ করে শুরু করুন।' }}
            </p>
            <Link href="/admin/ai-providers/create" class="btn btn-primary gap-2">
                <PlusIcon class="h-4 w-4" />
                {{ t('admin.ai_providers.new') }}
            </Link>
        </div>
    </AdminLayout>
</template>
