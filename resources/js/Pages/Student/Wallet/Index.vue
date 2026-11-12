<script setup>
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';
import { BanknotesIcon, ArrowDownLeftIcon, ArrowUpRightIcon, ClockIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    balance: [String, Number],
    transactions: Object,
    pendingOrders: Array,
});
const { t, locale, money } = useI18n();

const form = useForm({ amount: 500, method: 'mhpay', transaction_reference: '' });

function submit() {
    form.post('/wallet/recharge', { onSuccess: () => form.reset('transaction_reference') });
}

// Verification form state for pending order
const verifyingOrder = ref(null);
const verifyForm = useForm({
    transaction_id: '',
});

function openVerify(order) {
    verifyingOrder.value = order;
    verifyForm.transaction_id = order.gateway_transaction_id || '';
}

function submitVerify() {
    if (!verifyingOrder.value) return;
    verifyForm.post(`/wallet/orders/${verifyingOrder.value.id}/verify`, {
        onSuccess: () => {
            verifyingOrder.value = null;
        },
    });
}
</script>

<template>
    <Head :title="t('wallet.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6">
            <span class="eyebrow">{{ locale === 'en' ? 'Account' : 'অ্যাকাউন্ট' }}</span>
            <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ t('wallet.title') }}</h1>
        </header>

        <!-- Pending Order Alert -->
        <section
            v-if="pendingOrders && pendingOrders.length"
            class="anim-rise mb-6 space-y-3"
        >
            <div
                v-for="order in pendingOrders"
                :key="order.id"
                class="rounded-2xl border border-amber-500/40 bg-gradient-to-r from-amber-500/15 via-amber-500/10 to-[var(--surface)] p-5 shadow-sm"
            >
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-start gap-3.5">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-amber-500/20 text-xl text-amber-600 dark:text-amber-400">
                            ⏳
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-heading font-bold text-[var(--text)]">
                                    {{ locale === 'en' ? 'Pending Payment Order' : 'পেমেন্ট অপেক্ষমাণ রয়েছে' }}
                                </span>
                                <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[11px] font-bold text-amber-700 dark:text-amber-300">
                                    #{{ order.order_number }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-[var(--text)] leading-relaxed">
                                {{ locale === 'en'
                                    ? `You have a ৳${money(order.amount, 0)} payment pending. Verify your transaction ID to complete payment or contact support.`
                                    : `আপনার একটি ৳${money(order.amount, 0)} পেমেন্ট অপেক্ষমাণ রয়েছে। পেমেন্ট সম্পন্ন করতে ট্রানজ্যাকশন আইডি যাচাই করুন অথবা সহায়তা পেতে যোগাযোগ করুন।`
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <button
                            @click="openVerify(order)"
                            class="btn btn-primary btn-sm rounded-xl px-4 py-2 text-xs font-bold shadow-xs"
                        >
                            {{ locale === 'en' ? 'Verify TrxID' : 'ট্রানজ্যাকশন আইডি যাচাই' }}
                        </button>
                        <Link
                            href="/support/tickets"
                            class="btn btn-secondary btn-sm rounded-xl px-3.5 py-2 text-xs font-semibold"
                        >
                            {{ locale === 'en' ? 'Contact Support' : 'সহায়তা পেতে যোগাযোগ করুন' }}
                        </Link>
                    </div>
                </div>

                <!-- Inline Verification Form when toggled -->
                <div v-if="verifyingOrder?.id === order.id" class="mt-4 pt-4 border-t border-amber-500/30">
                    <form @submit.prevent="submitVerify" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <div class="flex-1">
                            <input
                                v-model="verifyForm.transaction_id"
                                type="text"
                                placeholder="বিকাশ / নগদ / রকেট TrxID দিন (e.g. BL92XK49QA)"
                                class="input text-xs py-2 font-mono w-full"
                                required
                            />
                            <p v-if="verifyForm.errors.transaction_id" class="mt-1 text-xs text-rose-500">
                                {{ verifyForm.errors.transaction_id }}
                            </p>
                        </div>
                        <button
                            type="submit"
                            :disabled="verifyForm.processing"
                            class="btn btn-primary btn-sm shrink-0 px-4 py-2 font-bold text-xs"
                        >
                            {{ verifyForm.processing ? 'যাচাই হচ্ছে...' : 'জমা দিন' }}
                        </button>
                        <button
                            type="button"
                            @click="verifyingOrder = null"
                            class="btn btn-secondary btn-sm shrink-0 px-3 py-2 text-xs"
                        >
                            বাতিল
                        </button>
                    </form>
                </div>
            </div>
        </section>

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
                            <option value="mhpay">MHPay — bKash / Nagad / Rocket</option>
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
