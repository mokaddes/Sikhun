<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';

const form = useForm({
    type: 'admin_broadcast',
    title: '',
    body: '',
    action_url: '',
    target_audience: 'all',
    send_email: false,
    send_now: true,
    scheduled_for: '',
});

function submit() {
    form.post('/admin/notifications');
}
</script>

<template>
    <Head title="New Broadcast &amp; Group Offer" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">New Broadcast / Group Offer</h1>
        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5 text-[var(--text-muted)]">Type</label>
                    <select v-model="form.type" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)]">
                        <option value="admin_broadcast">General Announcement</option>
                        <option value="special_offer">Special Offer / Promotion</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5 text-[var(--text-muted)]">Target Audience</label>
                    <select v-model="form.target_audience" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)]">
                        <option value="all">All Students</option>
                        <option value="admission">Admission Students</option>
                        <option value="job">Job / BCS Students</option>
                        <option value="hsc">HSC Students</option>
                        <option value="ssc">SSC Students</option>
                        <option value="university">University Students</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[var(--text-muted)]">Title</label>
                <input v-model="form.title" type="text" required placeholder="e.g. বিশেষ ডিসকাউন্ট অফার!" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[var(--text-muted)]">Message</label>
                <textarea v-model="form.body" rows="4" required placeholder="Write notification &amp; push message..." class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)]"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[var(--text-muted)]">Clickable Link (Optional)</label>
                <input v-model="form.action_url" type="text" placeholder="e.g. /subscription/plans or https://sikhun.com/library" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)]" />
                <p class="mt-1 text-xs text-[var(--text-muted)]">When students click the push notification or bell alert, they will be taken directly to this link.</p>
            </div>
            <div class="space-y-2.5 rounded-lg border border-[var(--border)] bg-[var(--surface2)]/60 p-4">
                <label class="flex items-center gap-2.5 text-sm font-medium text-[var(--text)] cursor-pointer">
                    <input v-model="form.send_email" type="checkbox" class="rounded" />
                    <span>Also send Email to all matching students</span>
                </label>
                <label class="flex items-center gap-2.5 text-sm font-medium text-[var(--text)] cursor-pointer">
                    <input v-model="form.send_now" type="checkbox" class="rounded" />
                    <span>Send Immediately (In-App + Firebase Push)</span>
                </label>
            </div>
            <div v-if="!form.send_now">
                <label class="block text-sm font-medium mb-1.5 text-[var(--text-muted)]">Schedule For</label>
                <input v-model="form.scheduled_for" type="datetime-local" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)]" />
            </div>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? 'Processing...' : (form.send_now ? 'Send Broadcast Now' : 'Schedule Broadcast') }}
            </button>
        </form>
    </AdminLayout>
</template>
