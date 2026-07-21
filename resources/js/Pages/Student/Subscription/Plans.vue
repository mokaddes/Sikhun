<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ plans: Array, activeSubscription: Object });
const { t } = useI18n();

const months = ref(1);

function subscribe(plan, method) {
    router.post('/plans/purchase', { plan_id: plan.id, months: months.value, payment_method: method });
}
</script>

<template>
    <Head :title="t('plans_page.title')" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-2">{{ t('plans_page.title') }}</h1>

        <div v-if="activeSubscription" class="mb-8 px-4 py-3 rounded-lg bg-[var(--secondary)]/10 border border-[var(--secondary)]/30 text-sm">
            {{ t('plans_page.current_plan') }}: <strong>{{ activeSubscription.plan.name }}</strong> —
            {{ t('plans_page.active_until') }} {{ new Date(activeSubscription.expires_at).toLocaleDateString() }}
        </div>

        <div class="mb-6 flex items-center gap-3">
            <label class="text-sm font-medium">{{ t('plans_page.months') }}:</label>
            <select v-model="months" class="px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm">
                <option v-for="m in [1, 3, 6, 12]" :key="m" :value="m">{{ m }}</option>
            </select>
        </div>

        <div class="grid md:grid-cols-3 gap-5">
            <div v-for="plan in plans" :key="plan.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 flex flex-col">
                <div class="font-heading text-lg font-bold mb-1">{{ plan.name }}</div>
                <p class="text-sm text-[var(--text-muted)] mb-4">{{ plan.description }}</p>
                <div class="text-3xl font-heading font-extrabold mb-5">
                    ৳{{ plan.price_monthly }}<span class="text-sm font-normal text-[var(--text-muted)]">{{ t('plans_page.per_month') }}</span>
                </div>
                <ul class="text-sm text-[var(--text-muted)] space-y-1.5 mb-6 flex-1">
                    <li v-for="f in plan.features" :key="f">✓ {{ f }}</li>
                </ul>
                <div class="space-y-2">
                    <button @click="subscribe(plan, 'wallet')" class="w-full py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold text-sm">
                        {{ t('plans_page.subscribe') }} — {{ t('book_show.pay_with_wallet') }}
                    </button>
                    <button @click="subscribe(plan, 'sslcommerz')" class="w-full py-2.5 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] font-semibold text-sm">
                        {{ t('book_show.pay_with_gateway') }}
                    </button>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
