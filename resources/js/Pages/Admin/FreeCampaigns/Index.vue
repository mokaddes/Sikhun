<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

defineProps({ campaigns: Object });
const { t } = useI18n();

function isLive(c) {
    const now = Date.now();
    return c.is_active && new Date(c.starts_at).getTime() <= now && new Date(c.ends_at).getTime() > now;
}
</script>

<template>
    <Head title="Free Campaigns" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="font-heading text-2xl font-extrabold">Free Campaigns</h1>
                <p class="text-sm text-[var(--text-muted)]">Open free access to all content for a time window.</p>
            </div>
            <Link href="/admin/free-campaigns/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('common.create') }}
            </Link>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div v-for="c in campaigns.data" :key="c.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="font-heading text-lg font-bold">{{ c.title }}</div>
                    <span class="px-2 py-0.5 rounded text-xs font-medium"
                          :class="isLive(c) ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                        {{ isLive(c) ? 'LIVE' : c.is_active ? 'Scheduled' : t('common.inactive') }}
                    </span>
                </div>
                <div class="text-sm text-[var(--text-muted)] mb-1">
                    {{ new Date(c.starts_at).toLocaleString() }} → {{ new Date(c.ends_at).toLocaleString() }}
                </div>
                <div class="text-sm text-[var(--text-muted)] mb-4">
                    {{ c.scope === 'all' ? 'All students' : `${c.students_count} selected student${c.students_count === 1 ? '' : 's'}` }}
                </div>
                <p v-if="c.description" class="text-sm text-[var(--text-muted)] mb-4">{{ c.description }}</p>
                <div class="flex gap-3">
                    <Link :href="`/admin/free-campaigns/${c.id}/edit`" class="text-sm font-medium text-[var(--primary)] hover:underline">{{ t('common.edit') }}</Link>
                    <ConfirmButton :href="`/admin/free-campaigns/${c.id}`" method="delete" />
                </div>
            </div>
        </div>

        <p v-if="!campaigns.data.length" class="mt-8 text-center text-[var(--text-muted)]">{{ t('common.no_results') }}</p>
    </AdminLayout>
</template>