<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ tickets: Object });
const { t } = useI18n();
const form = useForm({ subject: '', category: '', message: '' });
const categories = ['technical', 'billing', 'access', 'content', 'other'];

function createTicket() {
    form.post('/support/tickets', { preserveScroll: true, onSuccess: () => form.reset() });
}
</script>

<template>
    <Head :title="t('support.tickets')" />
    <StudentLayout>
        <div class="max-w-5xl mx-auto space-y-7">
            <header class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="font-heading text-2xl font-extrabold">{{ t('support.tickets') }}</h1>
                    <p class="mt-1 text-sm text-[var(--text-muted)]">{{ t('support.ticket_subtitle') }}</p>
                </div>
                <Link href="/feedback" class="text-sm font-semibold text-[var(--primary)]">{{ t('support.feedback_link') }}</Link>
            </header>

            <form @submit.prevent="createTicket" class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 space-y-4">
                <h2 class="font-heading font-bold">{{ t('support.new_ticket') }}</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-medium">{{ t('support.subject') }}
                        <input v-model="form.subject" maxlength="160" required class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-[var(--text)]" />
                        <span v-if="form.errors.subject" class="text-xs text-red-500">{{ form.errors.subject }}</span>
                    </label>
                    <label class="block text-sm font-medium">{{ t('support.category') }}
                        <select v-model="form.category" class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-[var(--text)]">
                            <option value="">{{ t('support.category_select') }}</option>
                            <option v-for="category in categories" :key="category" :value="category">{{ t(`support.category_${category}`) }}</option>
                        </select>
                    </label>
                </div>
                <label class="block text-sm font-medium">{{ t('support.message') }}
                    <textarea v-model="form.message" rows="4" maxlength="5000" required class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-[var(--text)]" />
                    <span v-if="form.errors.message" class="text-xs text-red-500">{{ form.errors.message }}</span>
                </label>
                <button :disabled="form.processing" class="rounded-lg bg-[var(--primary)] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">{{ t('support.create_ticket') }}</button>
            </form>

            <section class="space-y-3">
                <h2 class="font-heading text-lg font-bold">{{ t('support.your_tickets') }}</h2>
                <Link v-for="ticket in tickets.data" :key="ticket.id" :href="`/support/tickets/${ticket.id}`" class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
                    <div class="min-w-0">
                        <div class="font-semibold">{{ ticket.subject }}</div>
                        <div class="mt-1 max-w-xl truncate text-sm text-[var(--text-muted)]">{{ ticket.messages[0]?.body ?? '' }}</div>
                    </div>
                    <span class="rounded-full bg-[var(--surface2)] px-3 py-1 text-xs font-semibold">{{ t(`support.status_${ticket.status}`) }}</span>
                </Link>
                <div v-if="!tickets.data.length" class="rounded-xl border border-[var(--border)] py-10 text-center text-sm text-[var(--text-muted)]">{{ t('support.no_tickets') }}</div>
                <Pagination :links="tickets.links" />
            </section>
        </div>
    </StudentLayout>
</template>
