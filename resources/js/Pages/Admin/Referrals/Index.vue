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
        <div class="rounded-xl border border-[#2a2a38] bg-[#111118] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#2a2a38] text-left text-[#7a7a9a]">
                        <th class="px-5 py-3 font-medium">Referrer</th>
                        <th class="px-5 py-3 font-medium">Referee</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Reward</th>
                        <th class="px-5 py-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in referrals.data" :key="r.id" class="border-b border-[#2a2a38] last:border-0">
                        <td class="px-5 py-3">{{ r.referrer.name }} <span class="text-[#7a7a9a] text-xs">{{ r.referrer.email }}</span></td>
                        <td class="px-5 py-3">{{ r.referee.name }} <span class="text-[#7a7a9a] text-xs">{{ r.referee.email }}</span></td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium" :class="r.status === 'rewarded' ? 'bg-[#00d4aa]/15 text-[#00d4aa]' : 'bg-[#7a7a9a]/15 text-[#7a7a9a]'">{{ r.status }}</span>
                        </td>
                        <td class="px-5 py-3">{{ r.referrer_reward ? `৳${r.referrer_reward} / ৳${r.referee_reward}` : '—' }}</td>
                        <td class="px-5 py-3 text-[#7a7a9a]">{{ new Date(r.created_at).toLocaleDateString() }}</td>
                    </tr>
                    <tr v-if="!referrals.data.length"><td colspan="5" class="px-5 py-10 text-center text-[#7a7a9a]">No referrals yet.</td></tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="referrals.links" />
    </AdminLayout>
</template>
