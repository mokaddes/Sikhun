<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    CreditCardIcon,
    CheckIcon,
    SparklesIcon,
    BoltIcon,
    CpuChipIcon,
    ShieldCheckIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ plans: Array, activeSubscription: Object });
const { t, locale } = useI18n();

const months = ref(1);

function isCurrentPlan(plan) {
    return props.activeSubscription?.plan_id === plan.id;
}

function getPlanBadge(plan) {
    const price = Number(plan.price_monthly);
    if (plan.slug === 'pro' || price === 199) {
        return { text: locale.value === 'en' ? 'Most Popular' : 'জনপ্রিয়', color: 'bg-[var(--primary)] text-white' };
    }
    if (plan.slug === 'elite' || price === 399) {
        return { text: locale.value === 'en' ? 'Top Accuracy' : 'সর্বোচ্চ নির্ভুলতা', color: 'bg-gradient-to-r from-amber-500 to-orange-500 text-white' };
    }
    if (plan.slug === 'starter' || price === 99) {
        return { text: locale.value === 'en' ? 'Best Value' : 'বেস্ট ভ্যালু', color: 'bg-emerald-600 text-white' };
    }
    if (plan.is_free || price === 0) {
        return { text: locale.value === 'en' ? 'Free / Trial' : 'ফ্রিমিয়াম', color: 'bg-slate-600 text-white' };
    }
    return null;
}

function getAiModelLabel(plan) {
    const price = Number(plan.price_monthly);
    if (plan.slug === 'elite' || price >= 350) {
        return { name: 'GPT-6.1 Super Model', desc: locale.value === 'en' ? 'Ultra precision & deep reasoning' : 'টপ-টায়ার হাই-রিজনিং ও নির্ভুলতা' };
    }
    if (plan.slug === 'pro' || price >= 150) {
        return { name: 'Blue Minds / Smart AI', desc: locale.value === 'en' ? 'Advanced reasoning for math & science' : 'উন্নত বিজ্ঞান ও গাণিতিক রিজনিং' };
    }
    if (plan.slug === 'starter' || price > 0) {
        return { name: 'DeepSeek V4 Flash', desc: locale.value === 'en' ? 'High speed Bengali responses' : 'হাই-স্পিড বাংলা দ্রুত সমাধান' };
    }
    return { name: 'DeepSeek Flash Lite', desc: locale.value === 'en' ? 'Standard speed reading' : 'স্ট্যান্ডার্ড স্পিড রিডিং অ্যাসিস্ট্যান্ট' };
}

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
            <p class="mt-1 text-sm text-[var(--text-muted)]">
                {{ locale === 'en' ? 'Choose the plan that fits your study goals. Powered by tiered AI models.' : 'আপনার লক্ষ্য অনুযায়ী প্ল্যান বেছে নিন। প্রতিটি প্ল্যানে যুক্ত রয়েছে স্পেশালাইজড এআই মডেল।' }}
            </p>
        </header>

        <div v-if="activeSubscription" class="mb-6 rounded-2xl border border-[var(--secondary)]/30 bg-[var(--secondary)]/10 px-4 py-3 text-sm flex items-center justify-between flex-wrap gap-2">
            <span>
                {{ t('plans_page.current_plan') }}: <strong class="text-[var(--text)]">{{ activeSubscription.plan.name }}</strong> —
                {{ t('plans_page.active_until') }} {{ new Date(activeSubscription.expires_at).toLocaleDateString() }}
            </span>
            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold">
                <CheckIcon class="w-3.5 h-3.5" /> সক্রিয়
            </span>
        </div>

        <div v-else-if="$page.props.auth?.student?.is_in_trial" class="mb-6 flex items-center justify-between gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-300">
            <span class="flex items-center gap-2">
                <SparklesIcon class="h-4 w-4 text-emerald-500 shrink-0" />
                <span>{{ locale === 'en' ? `You are enjoying a 7-day free trial (${$page.props.auth.student.trial_days_remaining} day${$page.props.auth.student.trial_days_remaining === 1 ? '' : 's'} remaining). Select a plan to continue after your trial!` : `আপনি বর্তমানে ৭ দিনের ফ্রি ট্রায়ালে রয়েছেন (বাকি ${$page.props.auth.student.trial_days_remaining} দিন)। ট্রায়াল শেষে অবিরাম পড়তে এখনই প্ল্যান বেছে নিন!` }}</span>
            </span>
        </div>

        <!-- Duration Picker -->
        <div class="mb-6 flex items-center gap-3 bg-[var(--surface)] border border-[var(--border)] p-3 rounded-2xl max-w-sm shadow-xs">
            <label class="text-sm font-semibold shrink-0">{{ t('plans_page.months') }}:</label>
            <select v-model="months" class="input flex-1 py-1.5 text-sm" style="width:auto">
                <option :value="1">{{ locale === 'en' ? '1 Month (Monthly)' : '১ মাস (মাসিক)' }}</option>
                <option :value="3">{{ locale === 'en' ? '3 Months (Exam Special)' : '৩ মাস (পরীক্ষা স্পেশাল)' }}</option>
                <option :value="6">{{ locale === 'en' ? '6 Months (Admission Pass)' : '৬ মাস (অ্যাডমিশন পাস)' }}</option>
                <option :value="12">{{ locale === 'en' ? '12 Months (Full Year)' : '১২ মাস (সম্পূর্ণ বছর)' }}</option>
            </select>
        </div>

        <!-- 4 Plans Grid -->
        <div class="stagger grid gap-5 grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 items-stretch">
            <div
                v-for="plan in plans"
                :key="plan.id"
                class="card card-hover relative flex flex-col rounded-3xl p-5 border transition-all duration-200"
                :class="[
                    (plan.slug === 'pro' || Number(plan.price_monthly) === 199) ? 'ring-2 ring-[var(--primary)] border-[var(--primary)]/50 shadow-lg shadow-[var(--primary)]/10' : 'border-[var(--border)]',
                    isCurrentPlan(plan) ? 'bg-[var(--primary)]/5 dark:bg-[var(--primary)]/10' : ''
                ]"
            >
                <!-- Top Badge -->
                <span
                    v-if="getPlanBadge(plan)"
                    class="badge absolute -top-3 left-6 shadow-sm text-[11px] font-bold px-3 py-0.5 rounded-full"
                    :class="getPlanBadge(plan).color"
                >
                    <SparklesIcon v-if="plan.slug === 'pro' || Number(plan.price_monthly) === 199" class="h-3.5 w-3.5 mr-1 inline" />
                    <BoltIcon v-else-if="plan.slug === 'elite' || Number(plan.price_monthly) === 399" class="h-3.5 w-3.5 mr-1 inline" />
                    {{ getPlanBadge(plan).text }}
                </span>

                <div class="font-heading text-lg font-bold text-[var(--text)] mt-1">{{ plan.name }}</div>
                <p class="mb-3 mt-1 text-xs text-[var(--text-muted)] line-clamp-2 min-h-8">{{ plan.description }}</p>

                <!-- Pricing Display -->
                <div class="mb-4">
                    <div class="flex items-baseline gap-1 font-heading text-3xl font-extrabold text-[var(--text)]">
                        ৳{{ Number(plan.price_monthly) === 0 ? '০' : Number(plan.price_monthly) * (months > 1 ? months : 1) }}
                        <span class="text-xs font-normal text-[var(--text-muted)]">
                            / {{ months > 1 ? `${months} মাস` : t('plans_page.per_month') }}
                        </span>
                    </div>
                    <div v-if="months > 1 && Number(plan.price_monthly) > 0" class="text-[11px] text-[var(--text-muted)] mt-0.5">
                        (৳{{ Number(plan.price_monthly) }} × {{ months }} মাস)
                    </div>
                </div>

                <!-- AI Model Tier Pill -->
                <div class="mb-4 rounded-xl bg-[var(--surface2)]/70 border border-[var(--border)] p-2.5">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-[var(--primary)]">
                        <CpuChipIcon class="w-4 h-4 shrink-0" />
                        <span class="truncate">{{ getAiModelLabel(plan).name }}</span>
                    </div>
                    <p class="text-[11px] text-[var(--text-muted)] mt-0.5">
                        {{ getAiModelLabel(plan).desc }}
                    </p>
                </div>

                <!-- Plan Highlights / Quotas -->
                <div class="mb-4 grid grid-cols-2 gap-2 text-xs">
                    <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium border border-[var(--border)]/50">
                        <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">💬 AI চ্যাট</span>
                        <span class="font-bold text-[var(--text)]">{{ plan.ai_chat_minutes }} {{ locale === 'en' ? 'mins/mo' : 'মিনিট/মাস' }}</span>
                    </div>
                    <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium border border-[var(--border)]/50">
                        <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">📸 Vision Solver</span>
                        <span class="font-bold text-[var(--text)]">{{ plan.ai_vision_limit || 0 }} {{ locale === 'en' ? 'images/mo' : 'টি ছবি/মাস' }}</span>
                    </div>
                    <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium border border-[var(--border)]/50">
                        <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">📚 বই স্লট</span>
                        <span class="font-bold text-[var(--text)]">{{ plan.upload_book_limit || 1 }} {{ locale === 'en' ? 'slots' : 'টি বই' }}</span>
                    </div>
                    <div class="rounded-xl bg-[var(--surface2)] px-2.5 py-1.5 font-medium border border-[var(--border)]/50">
                        <span class="block text-[10px] text-[var(--text-muted)] font-bold uppercase">🔄 রি-আপলোড</span>
                        <span class="font-bold text-[var(--text)]">{{ plan.monthly_reupload_limit || 1 }} {{ locale === 'en' ? '/mo' : 'বার/মাস' }}</span>
                    </div>
                </div>

                <!-- Feature List -->
                <ul class="mb-5 flex-1 space-y-2 text-xs text-[var(--text-muted)] border-t border-[var(--border)] pt-3">
                    <li v-for="f in plan.features" :key="f" class="flex items-start gap-2">
                        <CheckIcon class="mt-0.5 h-3.5 w-3.5 shrink-0 text-emerald-500 font-bold" />
                        <span class="leading-tight">{{ f }}</span>
                    </li>
                </ul>

                <!-- Action Buttons -->
                <div class="space-y-2 mt-auto">
                    <!-- Free Plan Actions -->
                    <template v-if="plan.is_free || Number(plan.price_monthly) === 0">
                        <div v-if="isCurrentPlan(plan)" class="w-full py-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold text-center border border-emerald-500/30">
                            ✓ বর্তমান সক্রিয় প্ল্যান
                        </div>
                        <button
                            v-else
                            @click="subscribe(plan, 'free')"
                            class="btn btn-outline w-full py-2.5 text-xs font-bold hover:bg-[var(--primary)] hover:text-white hover:border-[var(--primary)] transition-all"
                        >
                            {{ locale === 'en' ? 'Start Free Plan' : 'বিনামূল্যে শুরু করুন' }}
                        </button>
                    </template>

                    <!-- Paid Plan Actions -->
                    <template v-else>
                        <button
                            @click="subscribe(plan, 'mhpay')"
                            class="btn btn-primary w-full shadow-md shadow-pink-500/20 bg-gradient-to-r from-rose-600 to-pink-600 border-0 text-xs py-2.5 font-bold"
                        >
                            {{ locale === 'en' ? 'Pay with MFS (bKash / Nagad)' : 'বিকাশ / নগদ / রকেটে সাবস্ক্রাইব' }}
                        </button>
                        <button
                            @click="subscribe(plan, 'wallet')"
                            class="btn btn-outline w-full text-xs py-2"
                        >
                            {{ t('book_show.pay_with_wallet') }}
                        </button>
                        <button
                            @click="subscribe(plan, 'zinipay')"
                            class="btn btn-outline w-full text-[11px] py-1.5 opacity-70 hover:opacity-100"
                        >
                            {{ t('book_show.pay_with_gateway') }} (ZiniPay)
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
