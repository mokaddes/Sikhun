<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ balance: [String, Number], transactions: Object });
const { t } = useI18n();

const form = useForm({ amount: 500, method: 'sslcommerz', transaction_reference: '' });

function submit() {
    form.post('/wallet/recharge', { onSuccess: () => form.reset('transaction_reference') });
}
</script>

<template>
    <Head :title="t('wallet.title')" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-1">{{ t('wallet.title') }}</h1>
        <p class="text-[var(--text-muted)] mb-8">{{ t('wallet.current_balance') }}: <span class="font-bold text-[var(--text)]">৳{{ balance }}</span></p>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">{{ t('wallet.recharge') }}</h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('wallet.amount') }}</label>
                        <input v-model="form.amount" type="number" min="50" step="10" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('wallet.choose_method') }}</label>
                        <select v-model="form.method" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                            <option value="sslcommerz">{{ t('wallet.sslcommerz') }}</option>
                            <option value="manual">{{ t('wallet.manual_transfer') }}</option>
                        </select>
                    </div>
                    <div v-if="form.method === 'manual'">
                        <label class="block text-sm font-medium mb-1.5">{{ t('wallet.transaction_ref') }}</label>
                        <input v-model="form.transaction_reference" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                        <p v-if="form.errors.transaction_reference" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.transaction_reference }}</p>
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold disabled:opacity-60">
                        {{ form.processing ? t('common.saving') : t('wallet.submit') }}
                    </button>
                </form>
            </div>

            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">{{ t('wallet.transactions') }}</h2>
                <ul v-if="transactions.data.length" class="space-y-2">
                    <li v-for="tx in transactions.data" :key="tx.id" class="flex justify-between text-sm border-b border-[var(--border)] pb-2 last:border-0">
                        <div>
                            <div class="font-medium">{{ tx.category.replace(/_/g, ' ') }}</div>
                            <div class="text-xs text-[var(--text-muted)]">{{ new Date(tx.created_at).toLocaleDateString() }}</div>
                        </div>
                        <div :class="tx.type === 'credit' ? 'text-[var(--secondary)]' : 'text-[var(--accent)]'" class="font-semibold">
                            {{ tx.type === 'credit' ? '+' : '-' }}৳{{ tx.amount }}
                        </div>
                    </li>
                </ul>
                <p v-else class="text-[var(--text-muted)] text-sm">{{ t('wallet.no_transactions') }}</p>
                <Pagination :links="transactions.links" />
            </div>
        </div>
    </StudentLayout>
</template>
