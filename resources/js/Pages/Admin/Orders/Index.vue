<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
import { useI18n } from '@/i18n';
import {
    MagnifyingGlassIcon,
    InboxIcon,
    CheckCircleIcon,
    EyeIcon,
    CalendarDaysIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    orders: Object,
    filters: { type: Object, default: () => ({ status: '', search: '' }) },
});

const { t, locale } = useI18n();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

let debounceTimer = null;
watch([search, status], () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/admin/orders', {
            search: search.value || undefined,
            status: status.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

function approve(order) {
    if (confirm(locale.value === 'en' ? 'Are you sure you want to approve this order?' : 'আপনি কি নিশ্চিত যে এই অর্ডারটি অনুমোদন করতে চান?')) {
        router.post(`/admin/orders/${order.id}/approve`, {}, { preserveScroll: true });
    }
}

const statusColor = (s) => ({
    pending: 'bg-amber-500/15 text-amber-500',
    completed: 'bg-[var(--secondary)]/15 text-[var(--secondary)]',
    failed: 'bg-red-500/15 text-red-500',
    refunded: 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]',
}[s] ?? 'bg-[var(--surface2)] text-[var(--text-muted)]');

function getOrderActions(order) {
    const actions = [];
    if (order.status === 'pending') {
        actions.push({
            label: t('admin.orders.approve'),
            onClick: () => approve(order),
            icon: CheckCircleIcon,
        });
    }
    if (order.student_id) {
        actions.push({
            label: locale.value === 'en' ? 'Student Profile' : 'শিক্ষার্থীর প্রোফাইল',
            href: `/admin/students/${order.student_id}`,
            icon: EyeIcon,
        });
    }
    return actions;
}
</script>

<template>
    <Head :title="t('admin.orders.title')" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.orders.title') }}</h1>
                    <span v-if="orders?.total !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                        {{ orders.total }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Review transactions, verify manual payments, and approve customer orders.' : 'লেনদেন পর্যালোচনা করুন এবং পেমেন্ট অনুমোদন করুন।' }}
                </p>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 max-w-md">
                <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                <input
                    v-model="search"
                    type="text"
                    :placeholder="t('common.search') + ' (order #, student)...'"
                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] py-2 pl-9.5 pr-4 text-sm text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                />
            </div>

            <!-- Status Tabs -->
            <div class="flex flex-wrap items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-1">
                <button
                    type="button"
                    @click="status = ''"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="!status ? 'bg-[var(--primary)] text-white shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                >
                    {{ t('common.all') }}
                </button>
                <button
                    type="button"
                    @click="status = 'pending'"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="status === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                >
                    {{ t('admin.orders.pending') }}
                </button>
                <button
                    type="button"
                    @click="status = 'completed'"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="status === 'completed' ? 'bg-[var(--secondary)] text-white shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                >
                    {{ t('admin.orders.completed') }}
                </button>
                <button
                    type="button"
                    @click="status = 'failed'"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="status === 'failed' ? 'bg-red-500 text-white shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                >
                    {{ t('admin.orders.failed') }}
                </button>
            </div>
        </div>

        <!-- Modern Datatable -->
        <div class="card overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-5 py-3.5">{{ t('admin.orders.order_number') }}</th>
                            <th class="px-5 py-3.5">{{ t('admin.orders.student') }}</th>
                            <th class="px-5 py-3.5">{{ t('admin.orders.amount') }}</th>
                            <th class="px-5 py-3.5">{{ t('admin.orders.method') }}</th>
                            <th class="px-5 py-3.5">{{ t('common.date') || 'Date' }}</th>
                            <th class="px-5 py-3.5">{{ t('common.status') }}</th>
                            <th class="px-5 py-3.5 text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr
                            v-for="o in orders.data"
                            :key="o.id"
                            class="group transition-colors hover:bg-[var(--surface2)]/40"
                        >
                            <td class="px-5 py-4 font-mono font-medium text-xs text-[var(--text)]">
                                {{ o.order_number }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-medium text-[var(--text)]">{{ o.student?.name || '—' }}</div>
                                <div class="text-xs text-[var(--text-muted)]">{{ o.student?.email || '—' }}</div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="font-heading font-bold text-[var(--text)]">
                                    ৳{{ o.amount }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span class="rounded-md bg-[var(--surface2)] px-2 py-0.5 font-mono text-[11px] font-semibold uppercase tracking-wide text-[var(--text-muted)]">
                                    {{ o.payment_method }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-xs text-[var(--text-muted)]">
                                {{ new Date(o.created_at).toLocaleDateString() }}
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase"
                                    :class="statusColor(o.status)"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full" :class="o.status === 'completed' ? 'bg-[var(--secondary)]' : o.status === 'pending' ? 'bg-amber-500' : 'bg-red-500'"></span>
                                    {{ o.status }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        v-if="o.status === 'pending'"
                                        type="button"
                                        @click="approve(o)"
                                        class="inline-flex items-center gap-1 rounded-lg bg-[var(--primary)] px-2.5 py-1 text-xs font-semibold text-white hover:bg-[var(--primary-hover)] transition-colors shadow-2xs"
                                    >
                                        <CheckCircleIcon class="h-3.5 w-3.5" />
                                        {{ t('admin.orders.approve') }}
                                    </button>
                                    <TableDropdown v-if="getOrderActions(o).length" :items="getOrderActions(o)" align="right" />
                                </div>
                            </td>
                        </tr>

                        <!-- Empty state -->
                        <tr v-if="!orders.data || !orders.data.length">
                            <td colspan="7" class="px-5 py-16 text-center">
                                <InboxIcon class="mx-auto h-10 w-10 text-[var(--text-muted)]/50" />
                                <div class="mt-3 font-heading font-semibold text-base">{{ t('common.no_results') }}</div>
                                <p class="mt-1 text-xs text-[var(--text-muted)]">
                                    {{ locale === 'en' ? 'No orders found matching the filter.' : 'কোনো অর্ডার পাওয়া যায়নি।' }}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-5">
            <Pagination :links="orders.links" />
        </div>
    </AdminLayout>
</template>
