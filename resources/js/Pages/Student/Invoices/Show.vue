<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import CouponInput from '@/Components/Checkout/CouponInput.vue';
import { useI18n } from '@/i18n';
import { ArrowLeftIcon, PrinterIcon, CreditCardIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ invoice: Object });

const { t, locale, money, digits } = useI18n();

const showPayModal = ref(false);
const appliedCoupon = ref(null);
const effectiveTotal = computed(() => (appliedCoupon.value ? Number(appliedCoupon.value.final_amount || 0) : Number(props.invoice?.total || 0)));

const payForm = useForm({
    payment_method: 'mhpay',
    coupon_code: '',
});

function submitPayment() {
    payForm.coupon_code = appliedCoupon.value?.code || '';
    if (effectiveTotal.value <= 0) {
        payForm.payment_method = 'coupon';
    } else if (payForm.payment_method === 'coupon') {
        payForm.payment_method = 'mhpay';
    }

    payForm.post(`/invoices/${props.invoice.id}/pay`, {
        onSuccess: () => {
            showPayModal.value = false;
        },
    });
}

const statusClass = (status) => ({
    paid: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
    sent: 'bg-sky-500/15 text-sky-600 dark:text-sky-400',
    overdue: 'bg-red-500/15 text-red-600 dark:text-red-400',
    void: 'bg-[var(--surface2)] text-[var(--text-muted)]',
    draft: 'bg-amber-500/15 text-amber-600 dark:text-amber-400',
}[status] ?? 'bg-[var(--surface2)] text-[var(--text-muted)]');

const fmtDate = (value) => (value ? new Date(value).toLocaleDateString(locale.value === 'bn' ? 'bn-BD' : 'en-GB') : '—');
</script>

<template>
    <Head :title="invoice.invoice_number" />
    <StudentLayout>
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <Link href="/invoices" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--primary)]">
                <ArrowLeftIcon class="h-4 w-4" />
                {{ t('invoices.back') }}
            </Link>
            <div class="flex items-center gap-2">
                <button
                    v-if="invoice.status === 'sent' || invoice.status === 'overdue' || invoice.status === 'draft'"
                    type="button"
                    @click="showPayModal = true"
                    class="btn bg-emerald-600 hover:bg-emerald-700 text-white inline-flex items-center gap-2 rounded-xl text-sm font-bold px-4 py-2"
                >
                    <CreditCardIcon class="h-4 w-4" />
                    <span>{{ locale === 'bn' ? 'পরিশোধ করুন' : 'Pay Now' }} (৳{{ money(invoice.total) }})</span>
                </button>
                <a :href="`/invoices/${invoice.id}/print`" target="_blank" rel="noopener" class="btn btn-primary inline-flex items-center gap-2 rounded-xl">
                    <PrinterIcon class="h-4 w-4" />
                    {{ t('invoices.print') }}
                </a>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPayModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-[var(--surface)] p-6 shadow-2xl border border-[var(--border)]">
                <div class="flex items-center justify-between pb-4 border-b border-[var(--border)]">
                    <h3 class="font-heading text-lg font-bold">
                        {{ locale === 'bn' ? 'ইনভয়েস পরিশোধ' : 'Pay Invoice' }} #{{ invoice.invoice_number }}
                    </h3>
                    <button type="button" @click="showPayModal = false" class="text-[var(--text-muted)] hover:text-[var(--text)]">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

                <div class="my-4 p-4 rounded-xl bg-[var(--surface2)]/60 text-sm space-y-2">
                    <div class="flex justify-between">
                        <span class="text-[var(--text-muted)]">{{ locale === 'bn' ? 'মূল প্রদেয়:' : 'Invoice Total:' }}</span>
                        <span class="font-semibold">৳{{ money(invoice.total) }}</span>
                    </div>
                    <div v-if="appliedCoupon" class="flex justify-between text-emerald-600 dark:text-emerald-400">
                        <span>{{ locale === 'bn' ? `কুপন ছাড় (${appliedCoupon.code}):` : `Coupon (${appliedCoupon.code}):` }}</span>
                        <span class="font-bold">−৳{{ money(appliedCoupon.discount_amount) }}</span>
                    </div>
                    <div class="flex justify-between pt-1.5 border-t border-[var(--border)]">
                        <span class="font-bold text-[var(--text)]">{{ locale === 'bn' ? 'সর্বমোট প্রদেয়:' : 'Total Payable:' }}</span>
                        <span class="font-bold text-base text-[var(--primary)]">৳{{ money(effectiveTotal) }}</span>
                    </div>
                </div>

                <form @submit.prevent="submitPayment" class="space-y-4">
                    <CouponInput
                        v-model="appliedCoupon"
                        target-type="invoice"
                        :target-id="invoice.id"
                        :subtotal="Number(invoice.total || 0)"
                        compact
                    />

                    <div v-if="effectiveTotal > 0">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                            {{ locale === 'bn' ? 'পেমেন্ট মাধ্যম বেছে নিন' : 'Select Payment Method' }}
                        </label>
                        <div class="space-y-2">
                            <label
                                v-for="gw in ($page.props.site?.payment_methods || [{ key: 'mhpay', label: 'MHPay (bKash / Nagad / Rocket)', description: 'bKash, Nagad, Rocket & Upay via MHPay' }])"
                                :key="gw.key"
                                class="flex items-center gap-3 p-3.5 rounded-xl border border-[var(--border)] cursor-pointer hover:bg-[var(--surface2)]"
                                :class="payForm.payment_method === gw.key ? 'ring-2 ring-[var(--primary)] border-[var(--primary)]' : ''">
                                <input type="radio" :value="gw.key" v-model="payForm.payment_method" class="sr-only" />
                                <div class="flex-1">
                                    <div class="font-semibold text-sm">{{ gw.label }}</div>
                                    <div class="text-xs text-[var(--text-muted)]">{{ gw.description }}</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3.5 rounded-xl border border-[var(--border)] cursor-pointer hover:bg-[var(--surface2)]"
                                :class="payForm.payment_method === 'wallet' ? 'ring-2 ring-[var(--primary)] border-[var(--primary)]' : ''">
                                <input type="radio" value="wallet" v-model="payForm.payment_method" class="sr-only" />
                                <div class="flex-1">
                                    <div class="font-semibold text-sm">{{ locale === 'bn' ? 'ওয়ালেট ব্যালেন্স' : 'Wallet Balance' }}</div>
                                    <div class="text-xs text-[var(--text-muted)]">{{ locale === 'bn' ? 'আপনার বর্তমান ব্যালেন্স থেকে কাটা হবে' : 'Deduct from your available wallet balance' }}</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[var(--border)]">
                        <button type="button" @click="showPayModal = false" class="btn btn-secondary text-sm">
                            {{ locale === 'bn' ? 'বাতিল' : 'Cancel' }}
                        </button>
                        <button type="submit" :disabled="payForm.processing" class="btn btn-primary text-sm font-bold">
                            {{ payForm.processing
                                ? (locale === 'bn' ? 'প্রক্রিয়াকরণ হচ্ছে...' : 'Processing...')
                                : (effectiveTotal <= 0
                                    ? (locale === 'bn' ? 'ফ্রিতে পরিশোধ নিশ্চিত করুন' : 'Complete Free with Coupon')
                                    : (locale === 'bn' ? 'এখনই পরিশোধ করুন' : 'Proceed to Pay')) }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card anim-rise overflow-hidden rounded-2xl">
            <div class="border-b border-[var(--border)] bg-[var(--surface2)]/40 px-6 py-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-[.12em] text-[var(--text-muted)]">{{ t('invoices.title') }}</div>
                        <div class="mt-1 font-mono text-lg font-extrabold">{{ invoice.invoice_number }}</div>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide" :class="statusClass(invoice.status)">
                        {{ t(`invoices.${invoice.status}`) }}
                    </span>
                </div>
            </div>

            <div class="grid gap-6 px-6 py-6 sm:grid-cols-2">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ t('invoices.billed_to') }}</div>
                    <div class="mt-1 font-semibold">{{ invoice.student?.name }}</div>
                    <div class="text-sm text-[var(--text-muted)]">{{ invoice.student?.email }}</div>
                </div>
                <div class="sm:text-right">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ t('invoices.invoice_details') }}</div>
                    <div class="mt-1 text-sm text-[var(--text-muted)]">
                        {{ t('invoices.issued') }}: <strong class="text-[var(--text)]">{{ fmtDate(invoice.issued_at) }}</strong><br>
                        {{ t('invoices.due') }}: <strong class="text-[var(--text)]">{{ fmtDate(invoice.due_at) }}</strong>
                        <template v-if="invoice.period_start">
                            <br>{{ t('invoices.period') }}: {{ fmtDate(invoice.period_start) }} – {{ fmtDate(invoice.period_end) }}
                        </template>
                        <template v-if="invoice.order">
                            <br>{{ t('invoices.order') }}: <span class="font-mono text-xs">{{ invoice.order.order_number }}</span>
                        </template>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-y border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-6 py-3">{{ t('invoices.description') }}</th>
                            <th class="px-6 py-3 text-right">{{ t('invoices.qty') }}</th>
                            <th class="px-6 py-3 text-right">{{ t('invoices.unit_price') }}</th>
                            <th class="px-6 py-3 text-right">{{ t('invoices.line_total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr v-for="item in invoice.items" :key="item.id">
                            <td class="px-6 py-3.5">{{ item.description }}</td>
                            <td class="px-6 py-3.5 text-right">{{ digits(Number(item.qty)) }}</td>
                            <td class="px-6 py-3.5 text-right">৳{{ money(item.unit_price) }}</td>
                            <td class="px-6 py-3.5 text-right font-semibold">৳{{ money(item.amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end px-6 py-5">
                <div class="w-full max-w-xs space-y-1.5 text-sm">
                    <div class="flex justify-between"><span class="text-[var(--text-muted)]">{{ t('invoices.subtotal') }}</span><span>৳{{ money(invoice.subtotal) }}</span></div>
                    <div v-if="Number(invoice.discount) > 0" class="flex justify-between"><span class="text-[var(--text-muted)]">{{ t('invoices.discount') }}</span><span>−৳{{ money(invoice.discount) }}</span></div>
                    <div v-if="Number(invoice.vat) > 0" class="flex justify-between"><span class="text-[var(--text-muted)]">{{ t('invoices.vat') }}</span><span>৳{{ money(invoice.vat) }}</span></div>
                    <div class="flex justify-between border-t border-[var(--border)] pt-2 font-heading text-base font-extrabold"><span>{{ t('invoices.total') }}</span><span>৳{{ money(invoice.total) }}</span></div>
                </div>
            </div>

            <div v-if="invoice.notes" class="border-t border-[var(--border)] px-6 py-4 text-sm text-[var(--text-muted)]">
                <strong class="text-[var(--text)]">{{ t('invoices.notes') }}:</strong> {{ invoice.notes }}
            </div>
        </div>
    </StudentLayout>
</template>
