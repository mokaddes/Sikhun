<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';
import { MagnifyingGlassIcon, InboxIcon, EyeIcon, PencilSquareIcon, PlusIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    invoices: Object,
    filters: { type: Object, default: () => ({ status: '', search: '' }) },
});

const { t, locale, money } = useI18n();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

let debounceTimer = null;
watch([search, status], () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/admin/invoices', {
            search: search.value || undefined,
            status: status.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

const statuses = ['paid', 'sent', 'overdue', 'void', 'draft'];

const statusClass = (s) => ({
    paid: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
    sent: 'bg-sky-500/15 text-sky-600 dark:text-sky-400',
    overdue: 'bg-red-500/15 text-red-600 dark:text-red-400',
    void: 'bg-[var(--surface2)] text-[var(--text-muted)]',
    draft: 'bg-amber-500/15 text-amber-600 dark:text-amber-400',
}[s] ?? 'bg-[var(--surface2)] text-[var(--text-muted)]');
</script>

<template>
    <Head :title="t('invoices.title')" />
    <AdminLayout>
        <div class="mb-6 flex flex-col gap-2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold">{{ t('invoices.title') }}</h1>
                    <span class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">{{ invoices.total }}</span>
                </div>
                <Link href="/admin/invoices/create" class="btn btn-primary inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold">
                    <PlusIcon class="h-4 w-4" />
                    {{ locale === 'en' ? 'Create manual invoice' : 'ম্যানুয়াল ইনভয়েস তৈরি' }}
                </Link>
            </div>
            <p class="text-sm text-[var(--text-muted)]">{{ t('invoices.admin_subtitle') }}</p>
        </div>

        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative max-w-md flex-1">
                <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                <input
                    v-model="search"
                    type="text"
                    :placeholder="`${t('common.search')} (${locale === 'en' ? 'invoice #, student' : 'ইনভয়েস নম্বর, শিক্ষার্থী'})`"
                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] py-2 pl-9.5 pr-4 text-sm focus:border-[var(--primary)] focus:outline-hidden"
                />
            </div>

            <div class="flex flex-wrap items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-1">
                <button
                    type="button"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="!status ? 'bg-[var(--primary)] text-white' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                    @click="status = ''"
                >
                    {{ t('common.all') }}
                </button>
                <button
                    v-for="s in statuses"
                    :key="s"
                    type="button"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="status === s ? 'bg-[var(--primary)] text-white' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                    @click="status = s"
                >
                    {{ t(`invoices.${s}`) }}
                </button>
            </div>
        </div>

        <div class="card overflow-hidden rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-5 py-3.5">{{ t('invoices.number') }}</th>
                            <th class="px-5 py-3.5">{{ t('invoices.student') }}</th>
                            <th class="px-5 py-3.5">{{ t('invoices.issued') }}</th>
                            <th class="px-5 py-3.5">{{ t('invoices.due') }}</th>
                            <th class="px-5 py-3.5">{{ t('invoices.amount') }}</th>
                            <th class="px-5 py-3.5">{{ t('invoices.status') }}</th>
                            <th class="px-5 py-3.5 text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr v-for="invoice in invoices.data" :key="invoice.id" class="transition-colors hover:bg-[var(--surface2)]/40">
                            <td class="px-5 py-4">
                                <Link :href="`/admin/invoices/${invoice.id}`" class="font-mono text-xs font-semibold text-[var(--primary)]">{{ invoice.invoice_number }}</Link>
                                <div class="mt-0.5 text-[11px] text-[var(--text-muted)]">{{ invoice.type === 'recurring' ? t('invoices.recurring') : t('invoices.one_time') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium">{{ invoice.student?.name }}</div>
                                <div class="text-xs text-[var(--text-muted)]">{{ invoice.student?.email }}</div>
                            </td>
                            <td class="px-5 py-4 text-xs text-[var(--text-muted)]">{{ invoice.issued_at ? new Date(invoice.issued_at).toLocaleDateString('en-GB') : '—' }}</td>
                            <td class="px-5 py-4 text-xs text-[var(--text-muted)]">{{ invoice.due_at ? new Date(invoice.due_at).toLocaleDateString('en-GB') : '—' }}</td>
                            <td class="px-5 py-4 font-heading font-bold">৳{{ money(invoice.total) }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide" :class="statusClass(invoice.status)">
                                    {{ t(`invoices.${invoice.status}`) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="`/admin/invoices/${invoice.id}`" class="rounded-lg border border-[var(--border)] p-1.5 hover:border-[var(--primary)]" :title="t('invoices.view')">
                                        <EyeIcon class="h-4 w-4" />
                                    </Link>
                                    <Link :href="`/admin/invoices/${invoice.id}/edit`" class="rounded-lg border border-[var(--border)] p-1.5 hover:border-[var(--primary)]" :title="t('invoices.edit')">
                                        <PencilSquareIcon class="h-4 w-4" />
                                    </Link>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!invoices.data.length">
                            <td colspan="7" class="px-5 py-16 text-center">
                                <InboxIcon class="mx-auto h-10 w-10 text-[var(--text-muted)]/50" />
                                <div class="mt-3 font-heading font-semibold">{{ t('invoices.no_invoices') }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Pagination :links="invoices.links" class="mt-5" />
    </AdminLayout>
</template>
