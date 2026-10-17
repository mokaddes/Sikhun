<script setup>
import { reactive } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
import { useI18n } from '@/i18n';
import {
    PlusIcon,
    CpuChipIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ providers: Array });
const { t, locale } = useI18n();

const testState = reactive({});

async function testConnection(provider) {
    testState[provider.id] = { loading: true, success: null, message: '' };
    try {
        const { data } = await axios.post(`/admin/ai-providers/${provider.id}/test`);
        testState[provider.id] = { loading: false, success: data.success, message: data.message };
    } catch (e) {
        testState[provider.id] = { loading: false, success: false, message: t('admin.ai_providers.test_failed') };
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
                    <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.ai_providers.title') }}</h1>
                    <span v-if="providers?.length !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                        {{ providers.length }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Manage LLM providers (OpenAI, Gemini, Anthropic, Ollama, etc.) and assign use cases.' : 'এআই প্রোভাইডার কনফিগারেশন এবং ব্যবহারের ক্ষেত্র পরিচালনা করুন।' }}
                </p>
            </div>
            <Link href="/admin/ai-providers/create" class="btn btn-primary self-start sm:self-auto gap-2">
                <PlusIcon class="h-4 w-4" />
                {{ t('admin.ai_providers.new') }}
            </Link>
        </div>

        <div class="space-y-4">
            <div v-for="p in providers" :key="p.id" class="card card-hover rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 transition-all shadow-sm">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-heading text-base font-bold text-[var(--text)]">{{ p.name }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-[var(--surface2)] text-[var(--text-muted)]">
                                {{ p.type }}
                            </span>
                        </div>
                        <div class="text-xs text-[var(--text-muted)] mt-1 font-mono">Model: {{ p.model_name }}</div>
                        <div v-if="p.type === 'custom' && p.api_endpoint" class="text-xs text-[var(--text-muted)] mt-0.5 break-all font-mono">{{ p.api_endpoint }}</div>
                        <div v-if="p.type === 'custom' && p.custom_headers?.length" class="text-xs text-[var(--text-muted)] mt-0.5">
                            {{ p.custom_headers.length }} {{ t('admin.ai_providers.header_count') }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                            :class="p.is_active ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'"
                        >
                            <span class="h-1.5 w-1.5 rounded-full" :class="p.is_active ? 'bg-[var(--secondary)]' : 'bg-[var(--text-muted)]'"></span>
                            {{ p.is_active ? t('common.active') : t('common.inactive') }}
                        </span>
                        <Link
                            :href="`/admin/ai-providers/${p.id}/edit`"
                            class="hidden sm:inline-flex items-center gap-1 rounded-lg border border-transparent px-2.5 py-1 text-xs font-semibold text-[var(--primary)] hover:border-[var(--primary)]/20 hover:bg-[var(--primary)]/10 transition-colors"
                        >
                            {{ t('common.edit') }}
                        </Link>
                        <TableDropdown :items="getProviderActions(p)" align="right" />
                    </div>
                </div>

                <!-- Use case badges — this is the multi-select in action -->
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <span v-for="uc in p.use_cases" :key="uc.id"
                        class="px-2 py-1 rounded text-xs font-medium"
                        :class="uc.is_default ? 'bg-[var(--primary)]/15 text-[var(--primary)]' : 'bg-[var(--surface2)] text-[var(--text-muted)]'">
                        {{ uc.use_case.replace('_', ' ') }}
                        <span v-if="uc.is_default">★</span>
                    </span>
                    <span v-if="!p.use_cases.length" class="text-xs text-[var(--text-muted)] italic">No use cases assigned</span>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="testConnection(p)" :disabled="testState[p.id]?.loading"
                        class="px-3 py-1.5 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-xs font-medium disabled:opacity-60">
                        {{ testState[p.id]?.loading ? t('admin.ai_providers.testing') : t('admin.ai_providers.test_connection') }}
                    </button>
                    <span v-if="testState[p.id] && !testState[p.id].loading" class="text-xs"
                        :class="testState[p.id].success ? 'text-[var(--secondary)]' : 'text-[var(--accent)]'">
                        {{ testState[p.id].message }}
                    </span>
                </div>
            </div>

            <div v-if="!providers.length" class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
                {{ t('common.no_results') }}
            </div>
        </div>
    </AdminLayout>
</template>
