<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ ticket: Object });
const { t, locale } = useI18n();
const form = useForm({ message: '' });

function reply() {
    form.post(`/support/tickets/${props.ticket.id}/messages`, { preserveScroll: true, onSuccess: () => form.reset() });
}
</script>

<template>
    <Head :title="ticket.subject" />
    <StudentLayout>
        <div class="mx-auto max-w-4xl space-y-5">
            <Link href="/support/tickets" class="text-sm font-medium text-[var(--primary)]">← {{ t('support.back_to_tickets') }}</Link>
            <header class="flex flex-wrap items-start justify-between gap-3">
                <div><h1 class="font-heading text-2xl font-extrabold">{{ ticket.subject }}</h1><p class="mt-1 text-sm text-[var(--text-muted)]">{{ ticket.category || t('support.no_category') }}</p></div>
                <span class="rounded-full bg-[var(--surface2)] px-3 py-1 text-xs font-semibold">{{ t(`support.status_${ticket.status}`) }}</span>
            </header>
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div v-for="message in ticket.messages" :key="message.id" class="mb-4 last:mb-0">
                    <div class="mb-1 flex items-center justify-between text-xs text-[var(--text-muted)]"><span>{{ t(`support.sender_${message.sender_type}`) }}</span><time>{{ new Date(message.created_at).toLocaleString(locale) }}</time></div>
                    <p class="whitespace-pre-wrap rounded-xl bg-[var(--surface2)] p-3 text-sm">{{ message.body }}</p>
                </div>
            </div>
            <form v-if="['open', 'pending'].includes(ticket.status)" @submit.prevent="reply" class="space-y-3">
                <label class="block text-sm font-medium">{{ t('support.reply') }}
                    <textarea v-model="form.message" rows="4" maxlength="5000" required class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-[var(--text)]" />
                    <span v-if="form.errors.message" class="text-xs text-red-500">{{ form.errors.message }}</span>
                </label>
                <button :disabled="form.processing" class="rounded-lg bg-[var(--primary)] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">{{ t('support.send_reply') }}</button>
            </form>
            <p v-else class="rounded-xl bg-[var(--surface2)] p-4 text-sm text-[var(--text-muted)]">{{ t('support.ticket_closed_hint') }}</p>
        </div>
    </StudentLayout>
</template>
