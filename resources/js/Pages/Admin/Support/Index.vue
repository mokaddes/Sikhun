<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';

const props = defineProps({ conversations: Object, filters: Object });

function setStatus(status) {
    router.get('/admin/support', { status }, { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Support Conversations" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">Support Conversations</h1>

        <div class="flex gap-2 mb-5">
            <button @click="setStatus('')" class="px-3 py-1.5 rounded-lg text-sm" :class="!filters.status ? 'bg-[#6c63ff] text-white' : 'bg-[#111118] text-[#9a9ab8]'">All</button>
            <button @click="setStatus('open')" class="px-3 py-1.5 rounded-lg text-sm" :class="filters.status === 'open' ? 'bg-[#6c63ff] text-white' : 'bg-[#111118] text-[#9a9ab8]'">Open</button>
            <button @click="setStatus('resolved')" class="px-3 py-1.5 rounded-lg text-sm" :class="filters.status === 'resolved' ? 'bg-[#6c63ff] text-white' : 'bg-[#111118] text-[#9a9ab8]'">Resolved</button>
        </div>

        <div class="space-y-2">
            <Link v-for="c in conversations.data" :key="c.id" :href="`/admin/support/${c.id}`"
                class="flex items-center justify-between rounded-xl border border-[#2a2a38] bg-[#111118] p-4">
                <div>
                    <div class="font-medium text-sm">{{ c.student?.name ?? 'Guest visitor' }}</div>
                    <div class="text-xs text-[#7a7a9a] truncate max-w-md">{{ c.messages[0]?.message ?? 'No messages yet' }}</div>
                </div>
                <span class="px-2 py-0.5 rounded text-xs font-medium" :class="c.status === 'open' ? 'bg-[#00d4aa]/15 text-[#00d4aa]' : 'bg-[#7a7a9a]/15 text-[#7a7a9a]'">{{ c.status }}</span>
            </Link>
            <div v-if="!conversations.data.length" class="text-center text-[#7a7a9a] py-10">No conversations yet.</div>
        </div>
        <Pagination :links="conversations.links" />
    </AdminLayout>
</template>
