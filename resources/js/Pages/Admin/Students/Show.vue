<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ student: Object, plans: Array });
const { t } = useI18n();

const walletForm = useForm({ type: 'credit', amount: '', notes: '' });
function submitWallet() {
    walletForm.post(`/admin/students/${props.student.id}/wallet-adjust`, {
        preserveScroll: true,
        onSuccess: () => walletForm.reset(),
    });
}

const subForm = useForm({ plan_id: props.plans[0]?.id ?? null, months: 1 });
function submitSubscription() {
    subForm.post(`/admin/students/${props.student.id}/assign-subscription`, { preserveScroll: true });
}
</script>

<template>
    <Head :title="t('admin.students.profile')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-1">{{ student.name }}</h1>
        <p class="text-[var(--text-muted)] mb-8">{{ student.email }} · {{ student.type?.toUpperCase() }}</p>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Wallet -->
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-heading text-lg font-bold">Wallet — ৳{{ student.wallet_balance }}</h2>
                </div>

                <form @submit.prevent="submitWallet" class="space-y-3 mb-6">
                    <div class="flex gap-2">
                        <select v-model="walletForm.type" class="px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                            <option value="credit">{{ t('admin.students.credit') }}</option>
                            <option value="debit">{{ t('admin.students.debit') }}</option>
                        </select>
                        <input v-model="walletForm.amount" type="number" step="0.01" min="0.01" :placeholder="t('admin.students.amount')" class="flex-1 px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    </div>
                    <input v-model="walletForm.notes" type="text" :placeholder="t('admin.students.reason')" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <button type="submit" :disabled="walletForm.processing" class="px-4 py-2 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold disabled:opacity-60">
                        {{ t('admin.students.wallet_adjust') }}
                    </button>
                </form>

                <div class="text-sm font-medium mb-2">Recent Transactions</div>
                <ul class="space-y-1.5 max-h-56 overflow-y-auto text-sm">
                    <li v-for="tx in student.wallet_transactions" :key="tx.id" class="flex justify-between text-[var(--text-muted)]">
                        <span>{{ tx.category }}</span>
                        <span :class="tx.type === 'credit' ? 'text-[var(--secondary)]' : 'text-[var(--accent)]'">
                            {{ tx.type === 'credit' ? '+' : '-' }}৳{{ tx.amount }}
                        </span>
                    </li>
                    <li v-if="!student.wallet_transactions.length" class="text-[var(--text-muted)]">No transactions yet.</li>
                </ul>
            </div>

            <!-- Subscription -->
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">Subscription</h2>

                <div v-if="student.active_subscription" class="mb-6 text-sm">
                    <div class="font-medium">{{ student.active_subscription.plan.name }}</div>
                    <div class="text-[var(--text-muted)]">Expires: {{ new Date(student.active_subscription.expires_at).toLocaleDateString() }}</div>
                    <div class="text-[var(--text-muted)]">AI chat minutes left: {{ student.active_subscription.ai_chat_minutes_remaining }}</div>
                </div>
                <div v-else class="mb-6 text-sm text-[var(--text-muted)]">No active subscription.</div>

                <form @submit.prevent="submitSubscription" class="space-y-3">
                    <select v-model="subForm.plan_id" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                        <option v-for="p in plans" :key="p.id" :value="p.id">{{ p.name }} — ৳{{ p.price_monthly }}/mo</option>
                    </select>
                    <input v-model="subForm.months" type="number" min="1" max="24" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <button type="submit" :disabled="subForm.processing" class="px-4 py-2 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold disabled:opacity-60">
                        {{ t('admin.students.assign_subscription') }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
