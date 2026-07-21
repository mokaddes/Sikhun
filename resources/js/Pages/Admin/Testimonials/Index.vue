<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';

defineProps({ testimonials: Array });
</script>

<template>
    <Head title="Testimonials" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="font-heading text-2xl font-extrabold">Testimonials</h1>
                <p class="text-sm text-[#7a7a9a] mt-1">Shown on the homepage "What Our Students Say" section — hidden entirely when empty.</p>
            </div>
            <Link href="/admin/testimonials/create" class="px-4 py-2 rounded-lg bg-[#e8935c] hover:bg-[#dc8148] text-white text-sm font-semibold">
                Add Testimonial
            </Link>
        </div>

        <div class="rounded-xl border border-[#2a2a38] bg-[#111118] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#2a2a38] text-left text-[#7a7a9a]">
                        <th class="px-5 py-3 font-medium">Student</th>
                        <th class="px-5 py-3 font-medium">Quote</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="t in testimonials" :key="t.id" class="border-b border-[#2a2a38] last:border-0">
                        <td class="px-5 py-3">
                            <div class="font-medium">{{ t.student_name }}</div>
                            <div class="text-xs text-[#7a7a9a]">{{ t.student_role }}</div>
                        </td>
                        <td class="px-5 py-3 text-[#9a9ab8] max-w-md truncate">{{ t.quote }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium" :class="t.is_published ? 'bg-[#00d4aa]/15 text-[#00d4aa]' : 'bg-[#7a7a9a]/15 text-[#7a7a9a]'">
                                {{ t.is_published ? 'Published' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <Link :href="`/admin/testimonials/${t.id}/edit`" class="text-sm font-medium text-[#e8935c] hover:underline">Edit</Link>
                            <ConfirmButton :href="`/admin/testimonials/${t.id}`" method="delete" />
                        </td>
                    </tr>
                    <tr v-if="!testimonials.length">
                        <td colspan="4" class="px-5 py-10 text-center text-[#7a7a9a]">
                            No testimonials yet — the homepage section stays hidden until you add at least one real one.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
