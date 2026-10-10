<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';
import { BanknotesIcon, ArrowDownLeftIcon, ArrowUpRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ balance: [String, Number], transactions: Object });
const { t, locale } = useI18n();

const form = useForm({ amount: 500, method: 'zinipay', transaction_reference: '' });

function submit() {
    form.post('/wallet/recharge', { onSuccess: () => form.reset('transaction_reference') });
}
</script>

<template>
    <Head :title="t('wallet.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6">
            <span class="eyebrow">{{ locale === 'en' ? 'Account' : 'অ্যাকাউন্ট' }}</span>
            <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ t('wallet.title') }}</h1>
        </header>

        <div class="card anim-rise sheen mb-6 flex items-center gap-4 rounded-2xl bg-gradient-to-br from-[var(--secondary)]/12 via-[var(--surface)] to-[var(--primary)]/10 p-6">
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-[var(--primary)]/15 text-[var(--primary)]">
                <BanknotesIcon class="h-7 w-7" />
            </span>
            <div>
                <div class="text-sm text-[var(--text-muted)]">{{ t('wallet.current_balance') }}</div>
                <div class="font-heading text-3xl font-extrabold">৳{{ balance }}</div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="card rounded-2xl p-6">
                <h2 class="mb-4 font-heading text-lg font-bold">{{ t('wallet.recharge') }}</h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="label">{{ t('wallet.amount') }}</label>
                        <input v-model="form.amount" type="number" min="50" step="10" class="input" />
                    </div>
                    <div>
                        <label class="label">{{ t('wallet.choose_method') }}</label>
                        <select v-model="form.method" class="input">
                            <option value="zinipay">{{ t('wallet.zinipay') }}</option>
                            <option value="manual">{{ t('wallet.manual_transfer') }}</option>
                        </select>
                    </div>
                    <div v-if="form.method === 'manual'">
                        <label class="label">{{ t('wallet.transaction_ref') }}</label>
                        <input v-model="form.transaction_reference" type="text" class="input" />
                        <p v-if="form.errors.transaction_reference" class="mt-1 text-xs text-[var(--accent)]">{{ form.errors.transaction_reference }}</p>
                    </div>
                    <button type="submit" :disabled="form.processing" class="btn btn-primary w-full">
                        {{ form.processing ? t('common.saving') : t('wallet.submit') }}
                    </button>
                </form>
            </div>

            <div class="card rounded-2xl p-6">
                <h2 class="mb-4 font-heading text-lg font-bold">{{ t('wallet.transactions') }}</h2>
                <ul v-if="transactions.data.length" class="space-y-2">
                    <li v-for="tx in transactions.data" :key="tx.id" class="flex items-center justify-between gap-3 border-b border-[var(--border)] py-2.5 text-sm last:border-0">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full" :class="tx.type === 'credit' ? 'bg-[var(--secondary)]/10 text-[var(--secondary)]' : 'bg-[var(--accent)]/10 text-[var(--accent)]'">
                                <ArrowDownLeftIcon v-if="tx.type === 'credit'" class="h-4 w-4" />
                                <ArrowUpRightIcon v-else class="h-4 w-4" />
                            </span>
                            <div class="min-w-0">
                                <div class="truncate font-medium capitalize">{{ tx.category.replace(/_/g, ' ') }}</div>
                                <div class="text-xs text-[var(--text-muted)]">{{ new Date(tx.created_at).toLocaleDateString() }}</div>
                            </div>
                        </div>
                        <div class="shrink-0 font-semibold" :class="tx.type === 'credit' ? 'text-[var(--secondary)]' : 'text-[var(--accent)]'">
                            {{ tx.type === 'credit' ? '+' : '-' }}৳{{ tx.amount }}
                        </div>
                    </li>
                </ul>
                <p v-else class="text-sm text-[var(--text-muted)]">{{ t('wallet.no_transactions') }}</p>
                <Pagination :links="transactions.links" />
            </div>
        </div>
    </StudentLayout>
</template>
