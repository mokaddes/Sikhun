<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

defineProps({ coupons: Object, filters: Object });
const { t } = useI18n();

function windowLabel(c) {
    if (c.duration_days) return `${c.duration_days} day${c.duration_days > 1 ? 's' : ''} from use`;
    if (c.starts_at && c.ends_at) {
        return `${new Date(c.starts_at).toLocaleDateString()} → ${new Date(c.ends_at).toLocaleDateString()}`;
    }
    return '—';
}
</script>

<template>
    <Head title="Coupons" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="font-heading text-2xl font-extrabold">Coupons</h1>
                <p class="text-sm text-[var(--text-muted)]">Grant students full access without a package (or beyond their package).</p>
            </div>
            <Link href="/admin/coupons/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('common.create') }}
            </Link>
        </div>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[var(--text-muted)] border-b border-[var(--border)]">
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Code</th>
                        <th class="px-4 py-3 font-medium">Assigned To</th>
                        <th class="px-4 py-3 font-medium">Access Window</th>
                        <th class="px-4 py-3 font-medium">Uses</th>
                        <th class="px-4 py-3 font-medium">{{ t('common.status') }}</th>
                        <th class="px-4 py-3 font-medium">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="c in coupons.data" :key="c.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-4 py-3 font-medium">{{ c.name }}</td>
                        <td class="px-4 py-3 text-[var(--text-muted)]">{{ c.code ?? '—' }}</td>
                        <td class="px-4 py-3 text-[var(--text-muted)]">{{ c.student ? `${c.student.name} (${c.student.email})` : 'Anyone' }}</td>
                        <td class="px-4 py-3 text-[var(--text-muted)]">{{ windowLabel(c) }}</td>
                        <td class="px-4 py-3 text-[var(--text-muted)]">{{ c.max_uses ? `${c.uses_count} / ${c.max_uses}` : c.uses_count }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium" :class="c.is_active ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                                {{ c.is_active ? t('common.active') : t('common.inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex gap-3">
                            <Link :href="`/admin/coupons/${c.id}/edit`" class="font-medium text-[var(--primary)] hover:underline">{{ t('common.edit') }}</Link>
                            <ConfirmButton :href="`/admin/coupons/${c.id}`" method="delete" />
                        </td>
                    </tr>
                    <tr v-if="!coupons.data.length">
                        <td colspan="7" class="px-4 py-8 text-center text-[var(--text-muted)]">{{ t('common.no_results') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>