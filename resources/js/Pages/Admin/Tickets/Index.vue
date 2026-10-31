<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ tickets: Object, filters: Object });
const { t } = useI18n();

function filter(params) {
    router.get('/admin/tickets', { ...props.filters, ...params }, { preserveState: true, replace: true });
}
function overdue(ticket) {
    return ['open', 'pending'].includes(ticket.status) && ticket.sla_due_at && new Date(ticket.sla_due_at) < new Date();
}
</script>

<template>
    <Head :title="t('admin.support.tickets')" />
    <AdminLayout>
        <div class="space-y-5">
            <header><h1 class="font-heading text-2xl font-extrabold">{{ t('admin.support.tickets') }}</h1><p class="mt-1 text-sm text-[var(--text-muted)]">{{ t('admin.support.ticket_inbox_subtitle') }}</p></header>
            <div class="flex flex-wrap gap-2">
                <select :value="filters.status || ''" @change="filter({ status: $event.target.value })" class="rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm text-[var(--text)]">
                    <option value="">{{ t('common.all') }}</option><option v-for="status in ['open', 'pending', 'resolved', 'closed']" :key="status" :value="status">{{ t(`support.status_${status}`) }}</option>
                </select>
                <select :value="filters.priority || ''" @change="filter({ priority: $event.target.value })" class="rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm text-[var(--text)]">
                    <option value="">{{ t('admin.support.all_priorities') }}</option><option v-for="priority in ['low', 'normal', 'high', 'urgent']" :key="priority" :value="priority">{{ t(`support.priority_${priority}`) }}</option>
                </select>
                <input :value="filters.search || ''" @change="filter({ search: $event.target.value })" :placeholder="t('common.search')" class="min-w-48 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm text-[var(--text)]" />
            </div>
            <div class="space-y-2">
                <Link v-for="ticket in tickets.data" :key="ticket.id" :href="`/admin/tickets/${ticket.id}`" class="block rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4 hover:border-[var(--primary)]">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0"><div class="font-semibold">{{ ticket.subject }}</div><div class="mt-1 text-xs text-[var(--text-muted)]">{{ ticket.student?.name }} · {{ ticket.student?.email }}</div><div class="mt-2 max-w-2xl truncate text-sm text-[var(--text-muted)]">{{ ticket.messages[0]?.body }}</div></div>
                        <div class="flex flex-wrap gap-2 text-xs"><span class="rounded-full bg-[var(--surface2)] px-2 py-1">{{ t(`support.status_${ticket.status}`) }}</span><span class="rounded-full bg-[var(--surface2)] px-2 py-1">{{ t(`support.priority_${ticket.priority}`) }}</span><span v-if="overdue(ticket)" class="rounded-full bg-red-500/10 px-2 py-1 font-semibold text-red-600">{{ t('admin.support.sla_overdue') }}</span></div>
                    </div>
                    <div class="mt-3 text-xs text-[var(--text-muted)]">{{ t('admin.support.sla_due') }}: {{ ticket.sla_due_at ? new Date(ticket.sla_due_at).toLocaleString() : t('admin.support.no_sla') }} · {{ t('admin.support.assigned_to') }}: {{ ticket.assigned_admin?.name || t('admin.support.unassigned') }}</div>
                </Link>
                <div v-if="!tickets.data.length" class="py-10 text-center text-sm text-[var(--text-muted)]">{{ t('admin.support.no_tickets') }}</div>
            </div>
            <Pagination :links="tickets.links" />
        </div>
    </AdminLayout>
</template>
