<script setup>
import { reactive } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ providers: Array });
const { t } = useI18n();

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
</script>

<template>
    <Head :title="t('admin.ai_providers.title')" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.ai_providers.title') }}</h1>
            <Link href="/admin/ai-providers/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('admin.ai_providers.new') }}
            </Link>
        </div>

        <div class="space-y-3">
            <div v-for="p in providers" :key="p.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="font-semibold">{{ p.name }}</div>
                        <div class="text-xs text-[var(--text-muted)]">{{ p.type }} · {{ p.model_name }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded text-xs font-medium" :class="p.is_active ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                            {{ p.is_active ? t('common.active') : t('common.inactive') }}
                        </span>
                        <Link :href="`/admin/ai-providers/${p.id}/edit`" class="text-sm font-medium text-[var(--primary)] hover:underline">{{ t('common.edit') }}</Link>
                        <ConfirmButton :href="`/admin/ai-providers/${p.id}`" method="delete" />
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
