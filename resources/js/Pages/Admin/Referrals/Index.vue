<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';

defineProps({ referrals: Object });
</script>

<template>
    <Head title="Referrals" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">Referrals</h1>
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">Referrer</th>
                        <th class="px-5 py-3 font-medium">Referee</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Reward</th>
                        <th class="px-5 py-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in referrals.data" :key="r.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">{{ r.referrer.name }} <span class="text-[var(--text-muted)] text-xs">{{ r.referrer.email }}</span></td>
                        <td class="px-5 py-3">{{ r.referee.name }} <span class="text-[var(--text-muted)] text-xs">{{ r.referee.email }}</span></td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium" :class="r.status === 'rewarded' ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">{{ r.status }}</span>
                        </td>
                        <td class="px-5 py-3">{{ r.referrer_reward ? `৳${r.referrer_reward} / ৳${r.referee_reward}` : '—' }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ new Date(r.created_at).toLocaleDateString() }}</td>
                    </tr>
                    <tr v-if="!referrals.data.length"><td colspan="5" class="px-5 py-10 text-center text-[var(--text-muted)]">No referrals yet.</td></tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="referrals.links" />
    </AdminLayout>
</template>
