<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

defineProps({ essays: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('essays.title')" />
    <StudentLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('essays.title') }}</h1>
            <Link href="/essays/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('essays.new_essay') }}
            </Link>
        </div>

        <div v-if="essays.data.length" class="space-y-2">
            <Link v-for="e in essays.data" :key="e.id" :href="`/essays/${e.id}`" class="flex items-center justify-between rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
                <div>
                    <div class="font-medium text-sm">{{ e.grading_type.replace('_', ' ').toUpperCase() }}</div>
                    <div class="text-xs text-[var(--text-muted)]">{{ new Date(e.created_at).toLocaleDateString() }} · {{ e.status }}</div>
                </div>
                <div v-if="e.result" class="font-heading font-bold">{{ e.result.total_score }}/{{ e.result.max_score }}</div>
            </Link>
        </div>
        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
            {{ t('essays.no_essays') }}
        </div>
        <Pagination :links="essays.links" />
    </StudentLayout>
</template>
