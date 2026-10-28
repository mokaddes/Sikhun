<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { CreditCardIcon, CheckIcon, SparklesIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ plans: Array, activeSubscription: Object });
const { t, locale } = useI18n();

const months = ref(1);

function subscribe(plan, method) {
    router.post('/plans/purchase', { plan_id: plan.id, months: months.value, payment_method: method });
}
</script>

<template>
    <Head :title="t('plans_page.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6">
            <span class="eyebrow">{{ locale === 'en' ? 'Account' : 'অ্যাকাউন্ট' }}</span>
            <h1 class="mt-1 flex items-center gap-2 font-heading text-2xl font-extrabold sm:text-3xl">
                <CreditCardIcon class="h-7 w-7 text-[var(--primary)]" /> {{ t('plans_page.title') }}
            </h1>
        </header>

        <div v-if="activeSubscription" class="mb-6 rounded-2xl border border-[var(--secondary)]/30 bg-[var(--secondary)]/10 px-4 py-3 text-sm">
            {{ t('plans_page.current_plan') }}: <strong>{{ activeSubscription.plan.name }}</strong> —
            {{ t('plans_page.active_until') }} {{ new Date(activeSubscription.expires_at).toLocaleDateString() }}
        </div>

        <div class="mb-6 flex items-center gap-3">
            <label class="text-sm font-medium">{{ t('plans_page.months') }}:</label>
            <select v-model="months" class="input" style="width:auto">
                <option v-for="m in [1, 3, 6, 12]" :key="m" :value="m">{{ m }}</option>
            </select>
        </div>

        <div class="stagger grid gap-5 md:grid-cols-3">
            <div
                v-for="(plan, i) in plans"
                :key="plan.id"
                class="card card-hover relative flex flex-col rounded-3xl p-6"
                :class="i === 1 ? 'ring-2 ring-[var(--primary)]/50' : ''"
            >
                <span v-if="i === 1" class="badge absolute -top-3 left-6 bg-[var(--primary)] text-white shadow-sm">
                    <SparklesIcon class="h-3.5 w-3.5" /> {{ locale === 'en' ? 'Popular' : 'জনপ্রিয়' }}
                </span>
                <div class="font-heading text-lg font-bold">{{ plan.name }}</div>
                <p class="mb-4 mt-1 text-sm text-[var(--text-muted)]">{{ plan.description }}</p>
                <div class="mb-5 font-heading text-3xl font-extrabold">
                    ৳{{ plan.price_monthly }}<span class="text-sm font-normal text-[var(--text-muted)]">{{ t('plans_page.per_month') }}</span>
                </div>

                <!-- Plan Highlights / Quotas -->
                <div class="mb-4 grid grid-cols-2 gap-2 text-xs">
                    <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium">
                        <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">💬 AI Chat</span>
                        <span class="font-bold text-[var(--text)]">{{ plan.ai_chat_minutes }} {{ locale === 'en' ? 'mins/mo' : 'মিনিট/মাস' }}</span>
                    </div>
                    <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium">
                        <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">📸 Vision Solver</span>
                        <span class="font-bold text-[var(--text)]">{{ plan.ai_vision_limit || 0 }} {{ locale === 'en' ? 'images/mo' : 'টি ছবি/মাস' }}</span>
                    </div>
                    <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium">
                        <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">📚 Book Slots</span>
                        <span class="font-bold text-[var(--text)]">{{ plan.upload_book_limit || 1 }} {{ locale === 'en' ? 'slots' : 'টি বই' }}</span>
                    </div>
                    <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium">
                        <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">🔄 Re-uploads</span>
                        <span class="font-bold text-[var(--text)]">{{ plan.monthly_reupload_limit || 2 }} {{ locale === 'en' ? '/mo' : 'বার/মাস' }}</span>
                    </div>
                </div>
                <ul class="mb-6 flex-1 space-y-2 text-sm text-[var(--text-muted)]">
                    <li v-for="f in plan.features" :key="f" class="flex items-start gap-2">
                        <CheckIcon class="mt-0.5 h-4 w-4 shrink-0 text-[var(--secondary)]" /> {{ f }}
                    </li>
                </ul>
                <div class="space-y-2">
                    <button @click="subscribe(plan, 'wallet')" class="btn btn-primary w-full">
                        {{ t('plans_page.subscribe') }} — {{ t('book_show.pay_with_wallet') }}
                    </button>
                    <button @click="subscribe(plan, 'zinipay')" class="btn btn-outline w-full">
                        {{ t('book_show.pay_with_gateway') }}
                    </button>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
