<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';

defineProps({ scheduled: Object });
</script>

<template>
    <Head title="Notifications" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">Notifications</h1>
            <Link href="/admin/notifications/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                New Broadcast
            </Link>
        </div>
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">Title</th>
                        <th class="px-5 py-3 font-medium">Audience</th>
                        <th class="px-5 py-3 font-medium">Scheduled For</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="n in scheduled.data" :key="n.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">{{ n.title }}</td>
                        <td class="px-5 py-3 uppercase text-xs text-[var(--text-muted)]">{{ n.target_audience }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ new Date(n.scheduled_for).toLocaleString() }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium" :class="n.sent_at ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                                {{ n.sent_at ? 'Sent' : 'Pending' }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="!scheduled.data.length"><td colspan="4" class="px-5 py-10 text-center text-[var(--text-muted)]">No broadcasts yet.</td></tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="scheduled.links" />
    </AdminLayout>
</template>
