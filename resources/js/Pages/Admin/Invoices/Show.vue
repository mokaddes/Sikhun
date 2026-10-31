<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';
import { ArrowLeftIcon, PencilSquareIcon, PrinterIcon } from '@heroicons/vue/24/outline';

defineProps({ invoice: Object });

const { t, money } = useI18n();

const statusClass = (status) => ({
    paid: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
    sent: 'bg-sky-500/15 text-sky-600 dark:text-sky-400',
    overdue: 'bg-red-500/15 text-red-600 dark:text-red-400',
    void: 'bg-[var(--surface2)] text-[var(--text-muted)]',
    draft: 'bg-amber-500/15 text-amber-600 dark:text-amber-400',
}[status] ?? 'bg-[var(--surface2)] text-[var(--text-muted)]');

const fmtDate = (value) => (value ? new Date(value).toLocaleDateString('en-GB') : '—');
</script>

<template>
    <Head :title="invoice.invoice_number" />
    <AdminLayout>
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <Link href="/admin/invoices" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--primary)]">
                <ArrowLeftIcon class="h-4 w-4" />
                {{ t('invoices.back') }}
            </Link>
            <div class="flex items-center gap-2">
                <a :href="`/admin/invoices/${invoice.id}/print`" target="_blank" rel="noopener" class="btn inline-flex items-center gap-2 rounded-xl border border-[var(--border)]">
                    <PrinterIcon class="h-4 w-4" />
                    {{ t('invoices.print') }}
                </a>
                <Link :href="`/admin/invoices/${invoice.id}/edit`" class="btn btn-primary inline-flex items-center gap-2 rounded-xl">
                    <PencilSquareIcon class="h-4 w-4" />
                    {{ t('invoices.edit') }}
                </Link>
            </div>
        </div>

        <div class="card overflow-hidden rounded-2xl">
            <div class="border-b border-[var(--border)] bg-[var(--surface2)]/40 px-6 py-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-[.12em] text-[var(--text-muted)]">{{ t('invoices.title') }}</div>
                        <div class="mt-1 font-mono text-lg font-extrabold">{{ invoice.invoice_number }}</div>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide" :class="statusClass(invoice.status)">
                        {{ t(`invoices.${invoice.status}`) }}
                    </span>
                </div>
            </div>

            <div class="grid gap-6 px-6 py-6 sm:grid-cols-2">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ t('invoices.billed_to') }}</div>
                    <div class="mt-1 font-semibold">{{ invoice.student?.name }}</div>
                    <div class="text-sm text-[var(--text-muted)]">{{ invoice.student?.email }}</div>
                    <Link :href="`/admin/students/${invoice.student_id}`" class="mt-1 inline-block text-xs font-semibold text-[var(--primary)]">
                        {{ t('invoices.student') }} →
                    </Link>
                </div>
                <div class="sm:text-right">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ t('invoices.invoice_details') }}</div>
                    <div class="mt-1 text-sm text-[var(--text-muted)]">
                        {{ t('invoices.issued') }}: <strong class="text-[var(--text)]">{{ fmtDate(invoice.issued_at) }}</strong><br>
                        {{ t('invoices.due') }}: <strong class="text-[var(--text)]">{{ fmtDate(invoice.due_at) }}</strong>
                        <template v-if="invoice.period_start">
                            <br>{{ t('invoices.period') }}: {{ fmtDate(invoice.period_start) }} – {{ fmtDate(invoice.period_end) }}
                        </template>
                        <template v-if="invoice.order">
                            <br>{{ t('invoices.order') }}: <span class="font-mono text-xs">{{ invoice.order.order_number }}</span>
                        </template>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-y border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-6 py-3">{{ t('invoices.description') }}</th>
                            <th class="px-6 py-3 text-right">{{ t('invoices.qty') }}</th>
                            <th class="px-6 py-3 text-right">{{ t('invoices.unit_price') }}</th>
                            <th class="px-6 py-3 text-right">{{ t('invoices.line_total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr v-for="item in invoice.items" :key="item.id">
                            <td class="px-6 py-3.5">{{ item.description }}</td>
                            <td class="px-6 py-3.5 text-right">{{ Number(item.qty) }}</td>
                            <td class="px-6 py-3.5 text-right">৳{{ money(item.unit_price) }}</td>
                            <td class="px-6 py-3.5 text-right font-semibold">৳{{ money(item.amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end px-6 py-5">
                <div class="w-full max-w-xs space-y-1.5 text-sm">
                    <div class="flex justify-between"><span class="text-[var(--text-muted)]">{{ t('invoices.subtotal') }}</span><span>৳{{ money(invoice.subtotal) }}</span></div>
                    <div v-if="Number(invoice.discount) > 0" class="flex justify-between"><span class="text-[var(--text-muted)]">{{ t('invoices.discount') }}</span><span>−৳{{ money(invoice.discount) }}</span></div>
                    <div v-if="Number(invoice.vat) > 0" class="flex justify-between"><span class="text-[var(--text-muted)]">{{ t('invoices.vat') }}</span><span>৳{{ money(invoice.vat) }}</span></div>
                    <div class="flex justify-between border-t border-[var(--border)] pt-2 font-heading text-base font-extrabold"><span>{{ t('invoices.total') }}</span><span>৳{{ money(invoice.total) }}</span></div>
                </div>
            </div>

            <div v-if="invoice.notes" class="border-t border-[var(--border)] px-6 py-4 text-sm text-[var(--text-muted)]">
                <strong class="text-[var(--text)]">{{ t('invoices.notes') }}:</strong> {{ invoice.notes }}
            </div>
        </div>
    </AdminLayout>
</template>
