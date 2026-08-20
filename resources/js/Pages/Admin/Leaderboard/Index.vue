<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import Pagination from '@/Components/UI/Pagination.vue';

defineProps({ entries: Object });
</script>

<template>
    <Head title="Leaderboard Entries" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">Leaderboard Entries</h1>
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">Student</th>
                        <th class="px-5 py-3 font-medium">Type</th>
                        <th class="px-5 py-3 font-medium">Score</th>
                        <th class="px-5 py-3 font-medium">Week/Month/Year</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="e in entries.data" :key="e.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">{{ e.student.name }}</td>
                        <td class="px-5 py-3 uppercase text-xs text-[var(--text-muted)]">{{ e.student_type }}</td>
                        <td class="px-5 py-3">{{ e.score }}/{{ e.total }} ({{ e.percentage }}%)</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">W{{ e.week_number }} / M{{ e.month_number }} / {{ e.year }}</td>
                        <td class="px-5 py-3 text-right">
                            <ConfirmButton :href="`/admin/leaderboard/${e.id}`" method="delete" label="Remove" />
                        </td>
                    </tr>
                    <tr v-if="!entries.data.length"><td colspan="5" class="px-5 py-10 text-center text-[var(--text-muted)]">No entries yet.</td></tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="entries.links" />
    </AdminLayout>
</template>
