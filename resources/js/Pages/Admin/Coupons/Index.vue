<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
import { useI18n } from '@/i18n';
import {
    PlusIcon,
    TicketIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

defineProps({ coupons: Object, filters: Object });
const { t, locale } = useI18n();

function windowLabel(c) {
    if (c.duration_days) return `${c.duration_days} ${locale.value === 'en' ? 'days from use' : 'দিন ব্যবহারের পর'}`;
    if (c.starts_at && c.ends_at) {
        return `${new Date(c.starts_at).toLocaleDateString()} → ${new Date(c.ends_at).toLocaleDateString()}`;
    }
    return '—';
}

function getCouponActions(c) {
    return [
        { label: t('common.edit'), href: `/admin/coupons/${c.id}/edit`, icon: PencilSquareIcon },
        { divider: true },
        { label: t('common.delete'), href: `/admin/coupons/${c.id}`, method: 'delete', danger: true, icon: TrashIcon },
    ];
}
</script>

<template>
    <Head title="Coupons" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold">Coupons</h1>
                    <span v-if="coupons?.data?.length !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                        {{ coupons.data.length }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Grant students full access without a package or for special promotions.' : 'শিক্ষার্থীদের জন্য ডিসকাউন্ট ও স্পেশাল অ্যাক্সেস কুপন তৈরি ও পরিচালনা করুন।' }}
                </p>
            </div>
            <Link href="/admin/coupons/create" class="btn btn-primary self-start sm:self-auto gap-2">
                <PlusIcon class="h-4 w-4" />
                {{ t('common.create') }}
            </Link>
        </div>

        <!-- Modern Datatable -->
        <div class="card overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-5 py-3.5">Name</th>
                            <th class="px-5 py-3.5">Code</th>
                            <th class="px-5 py-3.5">Assigned To</th>
                            <th class="px-5 py-3.5">Access Window</th>
                            <th class="px-5 py-3.5">Uses</th>
                            <th class="px-5 py-3.5">{{ t('common.status') }}</th>
                            <th class="px-5 py-3.5 text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr
                            v-for="c in coupons.data"
                            :key="c.id"
                            class="group transition-colors hover:bg-[var(--surface2)]/40"
                        >
                            <td class="px-5 py-4 font-semibold text-[var(--text)]">
                                {{ c.name }}
                            </td>

                            <td class="px-5 py-4">
                                <span class="rounded-md bg-[var(--surface2)] px-2.5 py-1 font-mono text-xs font-bold text-[var(--primary)] border border-[var(--border)]">
                                    {{ c.code ?? '—' }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-[var(--text-muted)]">
                                {{ c.student ? `${c.student.name} (${c.student.email})` : (locale === 'en' ? 'Anyone' : 'যে কেউ') }}
                            </td>

                            <td class="px-5 py-4 text-xs text-[var(--text-muted)]">
                                {{ windowLabel(c) }}
                            </td>

                            <td class="px-5 py-4 font-mono text-xs text-[var(--text-muted)]">
                                {{ c.max_uses ? `${c.uses_count} / ${c.max_uses}` : c.uses_count }}
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    :class="c.is_active ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full" :class="c.is_active ? 'bg-[var(--secondary)]' : 'bg-[var(--text-muted)]'"></span>
                                    {{ c.is_active ? t('common.active') : t('common.inactive') }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="`/admin/coupons/${c.id}/edit`"
                                        class="hidden sm:inline-flex items-center gap-1 rounded-lg border border-transparent px-2.5 py-1 text-xs font-semibold text-[var(--primary)] hover:border-[var(--primary)]/20 hover:bg-[var(--primary)]/10 transition-colors"
                                    >
                                        {{ t('common.edit') }}
                                    </Link>
                                    <TableDropdown :items="getCouponActions(c)" align="right" />
                                </div>
                            </td>
                        </tr>

                        <!-- Empty state -->
                        <tr v-if="!coupons.data || !coupons.data.length">
                            <td colspan="7" class="px-5 py-16 text-center">
                                <TicketIcon class="mx-auto h-10 w-10 text-[var(--text-muted)]/50" />
                                <div class="mt-3 font-heading font-semibold text-base">{{ t('common.no_results') }}</div>
                                <p class="mt-1 text-xs text-[var(--text-muted)]">{{ locale === 'en' ? 'No coupons created yet.' : 'কোনো কুপন তৈরি করা হয়নি।' }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>