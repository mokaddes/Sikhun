<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';

const form = useForm({ title: '', body: '', target_audience: 'all', scheduled_for: '' });

function submit() {
    form.post('/admin/notifications');
}
</script>

<template>
    <Head title="New Broadcast" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">New Broadcast</h1>
        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[#2a2a38] bg-[#111118] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[#9a9ab8]">Title</label>
                <input v-model="form.title" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[#18181f] border border-[#2a2a38] text-white" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[#9a9ab8]">Message</label>
                <textarea v-model="form.body" rows="4" required class="w-full px-4 py-2.5 rounded-lg bg-[#18181f] border border-[#2a2a38] text-white"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[#9a9ab8]">Audience</label>
                <select v-model="form.target_audience" class="w-full px-4 py-2.5 rounded-lg bg-[#18181f] border border-[#2a2a38] text-white">
                    <option value="all">All Students</option>
                    <option value="ssc">SSC</option><option value="hsc">HSC</option>
                    <option value="university">University</option><option value="job">Job</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[#9a9ab8]">Schedule For (optional — leave blank to send within 5 min)</label>
                <input v-model="form.scheduled_for" type="datetime-local" class="w-full px-4 py-2.5 rounded-lg bg-[#18181f] border border-[#2a2a38] text-white" />
            </div>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[#6c63ff] hover:bg-[#5b53ee] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? 'Scheduling...' : 'Schedule Broadcast' }}
            </button>
        </form>
    </AdminLayout>
</template>
