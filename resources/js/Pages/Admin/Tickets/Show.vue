<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ ticket: Object, admins: Array });
const { t, locale } = useI18n();
const replyForm = useForm({ message: '' });
const statusForm = useForm({ status: props.ticket.status, priority: props.ticket.priority, assigned_admin_id: props.ticket.assigned_admin_id ?? '' });

function reply() {
    replyForm.post(`/admin/tickets/${props.ticket.id}/messages`, { preserveScroll: true, onSuccess: () => replyForm.reset() });
}
function saveStatus() {
    statusForm.patch(`/admin/tickets/${props.ticket.id}`, { preserveScroll: true });
}
</script>

<template>
    <Head :title="ticket.subject" />
    <AdminLayout>
        <div class="mx-auto max-w-5xl space-y-5">
            <Link href="/admin/tickets" class="text-sm font-medium text-[var(--primary)]">← {{ t('admin.support.back_to_tickets') }}</Link>
            <header><h1 class="font-heading text-2xl font-extrabold">{{ ticket.subject }}</h1><p class="mt-1 text-sm text-[var(--text-muted)]">{{ ticket.student?.name }} · {{ ticket.student?.email }}</p></header>
            <form @submit.prevent="saveStatus" class="grid gap-3 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4 sm:grid-cols-4">
                <label class="text-xs font-semibold">{{ t('support.status') }}<select v-model="statusForm.status" class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm text-[var(--text)]"><option v-for="status in ['open', 'pending', 'resolved', 'closed']" :key="status" :value="status">{{ t(`support.status_${status}`) }}</option></select></label>
                <label class="text-xs font-semibold">{{ t('support.priority') }}<select v-model="statusForm.priority" class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm text-[var(--text)]"><option v-for="priority in ['low', 'normal', 'high', 'urgent']" :key="priority" :value="priority">{{ t(`support.priority_${priority}`) }}</option></select></label>
                <label class="text-xs font-semibold">{{ t('admin.support.assigned_to') }}<select v-model="statusForm.assigned_admin_id" class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm text-[var(--text)]"><option value="">{{ t('admin.support.unassigned') }}</option><option v-for="admin in admins" :key="admin.id" :value="admin.id">{{ admin.name }}</option></select></label>
                <button :disabled="statusForm.processing" class="self-end rounded-lg bg-[var(--primary)] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">{{ t('admin.support.save_ticket') }}</button>
            </form>
            <div class="grid gap-3 text-sm text-[var(--text-muted)] sm:grid-cols-2"><p>{{ t('admin.support.category') }}: {{ ticket.category || t('support.no_category') }}</p><p>{{ t('admin.support.sla_due') }}: {{ ticket.sla_due_at ? new Date(ticket.sla_due_at).toLocaleString(locale) : t('admin.support.no_sla') }}</p><p>{{ t('admin.support.linked_conversation') }}: {{ ticket.conversation_id || t('common.no') }}</p></div>
            <section class="space-y-3 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <article v-for="message in ticket.messages" :key="message.id" class="rounded-xl bg-[var(--surface2)] p-3">
                    <div class="mb-1 flex items-center justify-between text-xs text-[var(--text-muted)]"><span>{{ t(`support.sender_${message.sender_type}`) }}</span><time>{{ new Date(message.created_at).toLocaleString(locale) }}</time></div>
                    <p class="whitespace-pre-wrap text-sm">{{ message.body }}</p>
                </article>
            </section>
            <form @submit.prevent="reply" class="space-y-3">
                <label class="block text-sm font-medium">{{ t('support.reply') }}<textarea v-model="replyForm.message" rows="4" maxlength="5000" required class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-[var(--text)]" /></label>
                <span v-if="replyForm.errors.message" class="text-xs text-red-500">{{ replyForm.errors.message }}</span>
                <button :disabled="replyForm.processing" class="rounded-lg bg-[var(--primary)] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">{{ t('support.send_reply') }}</button>
            </form>
        </div>
    </AdminLayout>
</template>
