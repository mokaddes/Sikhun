<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ orders: Object, filters: Object });
const { t } = useI18n();

const status = ref(props.filters.status ?? '');
watch(status, (val) => {
    router.get('/admin/orders', { status: val }, { preserveState: true, replace: true });
});

function approve(order) {
    router.post(`/admin/orders/${order.id}/approve`, {}, { preserveScroll: true });
}

const statusColor = (s) => ({
    pending: 'bg-yellow-500/15 text-yellow-500',
    completed: 'bg-[var(--secondary)]/15 text-[var(--secondary)]',
    failed: 'bg-[var(--accent)]/15 text-[var(--accent)]',
    refunded: 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]',
}[s] ?? '');
</script>

<template>
    <Head :title="t('admin.orders.title')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('admin.orders.title') }}</h1>

        <select v-model="status" class="mb-5 px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
            <option value="">{{ t('common.all') }}</option>
            <option value="pending">{{ t('admin.orders.pending') }}</option>
            <option value="completed">{{ t('admin.orders.completed') }}</option>
            <option value="failed">{{ t('admin.orders.failed') }}</option>
            <option value="refunded">{{ t('admin.orders.refunded') }}</option>
        </select>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">{{ t('admin.orders.order_number') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.orders.student') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.orders.amount') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.orders.method') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.status') }}</th>
                        <th class="px-5 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="o in orders.data" :key="o.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3 font-mono text-xs">{{ o.order_number }}</td>
                        <td class="px-5 py-3">{{ o.student?.name }}</td>
                        <td class="px-5 py-3">৳{{ o.amount }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)] uppercase text-xs">{{ o.payment_method }}</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 rounded text-xs font-medium" :class="statusColor(o.status)">{{ o.status }}</span></td>
                        <td class="px-5 py-3 text-right">
                            <button v-if="o.status === 'pending'" @click="approve(o)" class="text-sm font-medium text-[var(--primary)] hover:underline">
                                {{ t('admin.orders.approve') }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!orders.data.length"><td colspan="6" class="px-5 py-10 text-center text-[var(--text-muted)]">{{ t('common.no_results') }}</td></tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="orders.links" />
    </AdminLayout>
</template>
