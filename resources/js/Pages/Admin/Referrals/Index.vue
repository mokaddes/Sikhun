<script setup>
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ referrals: Object, funnel: Object });
const { t, n } = useI18n();

function review(referral, decision) {
    router.post(`/admin/referrals/${referral.id}/review`, { decision });
}

function statusLabel(status) {
    return t(`admin.referrals_page.${status}`);
}
</script>

<template>
    <Head :title="t('admin.referrals_page.title')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('admin.referrals_page.title') }}</h1>

        <div class="mb-6 grid grid-cols-3 gap-4">
            <div v-for="key in ['click', 'registered', 'paid']" :key="key" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
                <div class="text-sm text-[var(--text-muted)]">{{ t(`admin.referrals_page.${key === 'registered' ? 'registrations' : key === 'click' ? 'clicks' : 'paid'}`) }}</div>
                <div class="mt-1 font-heading text-2xl font-bold">{{ n(funnel?.[key] ?? 0) }}</div>
            </div>
        </div>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">{{ t('admin.referrals_page.referrer') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.referrals_page.referee') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.referrals_page.status') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.referrals_page.review') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.referrals_page.signals') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.referrals_page.reward') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.referrals_page.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in referrals.data" :key="r.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">{{ r.referrer?.name }} <span class="text-[var(--text-muted)] text-xs">{{ r.referrer?.email }}</span></td>
                        <td class="px-5 py-3">{{ r.referee?.name }} <span class="text-[var(--text-muted)] text-xs">{{ r.referee?.email }}</span></td>
                        <td class="px-5 py-3">{{ statusLabel(r.status) }}</td>
                        <td class="px-5 py-3">
                            <div>{{ statusLabel(r.fraud_status || 'clear') }}</div>
                            <div v-if="r.fraud_status === 'held'" class="mt-2 flex gap-2">
                                <button type="button" @click="review(r, 'approve')" class="rounded bg-[var(--secondary)]/15 px-2 py-1 text-xs text-[var(--secondary)]">{{ t('admin.referrals_page.approve') }}</button>
                                <button type="button" @click="review(r, 'reject')" class="rounded bg-[var(--accent)]/15 px-2 py-1 text-xs text-[var(--accent)]">{{ t('admin.referrals_page.reject') }}</button>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span v-if="!r.risk_signals?.length">—</span>
                            <span v-else>{{ r.risk_signals.map(signal => t(`admin.referrals_page.${signal}`)).join(', ') }}</span>
                        </td>
                        <td class="px-5 py-3">{{ r.referrer_reward ? `৳${r.referrer_reward} / ৳${r.referee_reward}` : '—' }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ r.registered_at ? new Date(r.registered_at).toLocaleDateString() : new Date(r.created_at).toLocaleDateString() }}</td>
                    </tr>
                    <tr v-if="!referrals.data.length"><td colspan="7" class="px-5 py-10 text-center text-[var(--text-muted)]">{{ t('admin.referrals_page.empty') }}</td></tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="referrals.links" />
    </AdminLayout>
</template>
