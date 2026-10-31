<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';
import { DocumentTextIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';

defineProps({ invoices: Object });

const { t, locale, money } = useI18n();

const statusClass = (status) => ({
    paid: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
    sent: 'bg-sky-500/15 text-sky-600 dark:text-sky-400',
    overdue: 'bg-red-500/15 text-red-600 dark:text-red-400',
    void: 'bg-[var(--surface2)] text-[var(--text-muted)]',
    draft: 'bg-amber-500/15 text-amber-600 dark:text-amber-400',
}[status] ?? 'bg-[var(--surface2)] text-[var(--text-muted)]');
</script>

<template>
    <Head :title="t('invoices.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6">
            <span class="eyebrow">{{ locale === 'en' ? 'Account' : 'অ্যাকাউন্ট' }}</span>
            <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ t('invoices.title') }}</h1>
            <p class="mt-1 text-sm text-[var(--text-muted)]">{{ t('invoices.subtitle') }}</p>
        </header>

        <div v-if="invoices.data.length" class="card anim-rise overflow-hidden rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-5 py-3.5">{{ t('invoices.number') }}</th>
                            <th class="px-5 py-3.5">{{ t('invoices.issued') }}</th>
                            <th class="px-5 py-3.5">{{ t('invoices.amount') }}</th>
                            <th class="px-5 py-3.5">{{ t('invoices.status') }}</th>
                            <th class="px-5 py-3.5 text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr v-for="invoice in invoices.data" :key="invoice.id" class="transition-colors hover:bg-[var(--surface2)]/40">
                            <td class="px-5 py-4">
                                <Link :href="`/invoices/${invoice.id}`" class="font-mono text-xs font-semibold text-[var(--primary)]">
                                    {{ invoice.invoice_number }}
                                </Link>
                                <div class="mt-0.5 text-[11px] text-[var(--text-muted)]">
                                    {{ invoice.type === 'recurring' ? t('invoices.recurring') : t('invoices.one_time') }}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs text-[var(--text-muted)]">
                                {{ invoice.issued_at ? new Date(invoice.issued_at).toLocaleDateString(locale === 'bn' ? 'bn-BD' : 'en-GB') : '—' }}
                            </td>
                            <td class="px-5 py-4 font-heading font-bold">৳{{ money(invoice.total) }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide" :class="statusClass(invoice.status)">
                                    {{ t(`invoices.${invoice.status}`) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="`/invoices/${invoice.id}`" class="rounded-lg border border-[var(--border)] px-2.5 py-1 text-xs font-semibold hover:border-[var(--primary)]">
                                        {{ t('invoices.view') }}
                                    </Link>
                                    <a
                                        :href="`/invoices/${invoice.id}/print`"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex items-center gap-1 rounded-lg bg-[var(--surface2)] px-2.5 py-1 text-xs font-semibold hover:text-[var(--primary)]"
                                    >
                                        <ArrowDownTrayIcon class="h-3.5 w-3.5" />
                                        {{ t('invoices.print') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-else class="card anim-rise rounded-2xl px-6 py-16 text-center">
            <DocumentTextIcon class="mx-auto h-12 w-12 text-[var(--text-muted)]/40" />
            <h2 class="mt-4 font-heading text-lg font-bold">{{ t('invoices.no_invoices') }}</h2>
            <p class="mt-1 text-sm text-[var(--text-muted)]">{{ t('invoices.no_invoices_hint') }}</p>
            <Link href="/library" class="btn btn-primary mt-6 inline-flex rounded-xl">{{ locale === 'en' ? 'Browse books' : 'বই দেখুন' }}</Link>
        </div>

        <Pagination v-if="invoices.links?.length > 3" :links="invoices.links" class="mt-5" />
    </StudentLayout>
</template>
