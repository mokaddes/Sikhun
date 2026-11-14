<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
import { useI18n } from '@/i18n';
import {
    PlusIcon,
    CreditCardIcon,
    CheckIcon,
    PencilSquareIcon,
    TrashIcon,
    UsersIcon,
    SparklesIcon,
    ArrowPathIcon,
    TagIcon,
    ClockIcon,
    AdjustmentsHorizontalIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    plans: Array,
    customPlanConfig: Object,
});

const { t, locale } = useI18n();

const activeTab = ref('all'); // all, general, admission, job, academic, custom_builder
const syncing = ref(false);

const customForm = useForm({
    custom_plan_enabled: props.customPlanConfig?.custom_plan_enabled ?? true,
    custom_plan_base_fee: props.customPlanConfig?.custom_plan_base_fee ?? 35,
    custom_plan_min_monthly: props.customPlanConfig?.custom_plan_min_monthly ?? 149,
    custom_plan_rate_minute: props.customPlanConfig?.custom_plan_rate_minute ?? 0.22,
    custom_plan_rate_image: props.customPlanConfig?.custom_plan_rate_image ?? 1.10,
    custom_plan_rate_book: props.customPlanConfig?.custom_plan_rate_book ?? 14.0,
    custom_plan_rate_reupload: props.customPlanConfig?.custom_plan_rate_reupload ?? 5.0,
    custom_plan_rate_quiz: props.customPlanConfig?.custom_plan_rate_quiz ?? 2.5,
    custom_plan_rate_explanation: props.customPlanConfig?.custom_plan_rate_explanation ?? 1.0,
});

function saveCustomRates() {
    customForm.post('/admin/plans/custom-config', {
        preserveScroll: true,
    });
}

function syncPagePilot() {
    if (syncing.value) return;
    syncing.value = true;
    router.post('/admin/plans/sync-pagepilot', {}, {
        preserveScroll: true,
        onFinish: () => {
            syncing.value = false;
        },
    });
}

const filteredPlans = computed(() => {
    if (!props.plans) return [];
    if (activeTab.value === 'all') return props.plans;
    if (activeTab.value === 'general') return props.plans.filter(p => p.target_segment === 'all' || !p.target_segment || p.target_segment === 'custom');
    if (activeTab.value === 'admission') return props.plans.filter(p => p.target_segment === 'admission');
    if (activeTab.value === 'job') return props.plans.filter(p => p.target_segment === 'job');
    if (activeTab.value === 'academic') return props.plans.filter(p => ['hsc', 'ssc'].includes(p.target_segment));
    return props.plans;
});

const tabCounts = computed(() => {
    const list = props.plans || [];
    return {
        all: list.length,
        general: list.filter(p => p.target_segment === 'all' || !p.target_segment || p.target_segment === 'custom').length,
        admission: list.filter(p => p.target_segment === 'admission').length,
        job: list.filter(p => p.target_segment === 'job').length,
        academic: list.filter(p => ['hsc', 'ssc'].includes(p.target_segment)).length,
    };
});

function getSegmentLabel(seg) {
    const map = {
        all: 'সকল শিক্ষার্থী (General)',
        admission: 'বিশ্ববিদ্যালয় ভর্তি (Admission)',
        job: 'বিসিএস ও চাকরি (Job)',
        hsc: 'উচ্চ মাধ্যমিক (HSC)',
        ssc: 'মাধ্যমিক (SSC)',
        custom: 'কাস্টম প্যাকেজ (Custom)',
    };
    return map[seg] || seg || 'সকল শিক্ষার্থী';
}

function getSegmentColor(seg) {
    const map = {
        all: 'bg-slate-500/10 text-slate-700 dark:text-slate-300 border-slate-500/20',
        admission: 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20',
        job: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
        hsc: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        ssc: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
        custom: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
    };
    return map[seg] || 'bg-slate-500/10 text-slate-700 border-slate-500/20';
}

function getPlanActions(plan) {
    return [
        { label: t('common.edit'), href: `/admin/plans/${plan.id}/edit`, icon: PencilSquareIcon },
        { divider: true },
        { label: t('common.delete'), href: `/admin/plans/${plan.id}`, method: 'delete', danger: true, icon: TrashIcon },
    ];
}
</script>

<template>
    <Head :title="t('admin.plans.title')" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.plans.title') }}</h1>
                    <span v-if="plans?.length !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                        {{ plans.length }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Manage Admission, Job, General and Custom Subscription Bundles & Rates.' : 'ভর্তি প্রস্তুতি, চাকরি, জেনারেল ও কাস্টম সাবস্ক্রিপশন প্ল্যানসমূহ পরিচালনা করুন।' }}
                </p>
            </div>
            <div class="flex items-center gap-2.5 self-start sm:self-auto">
                <button
                    type="button"
                    @click="syncPagePilot"
                    :disabled="syncing"
                    class="btn btn-secondary inline-flex items-center gap-1.5 text-xs font-bold"
                    title="PagePilotBD ক্যাটালগে সকল প্ল্যান ও প্রোডাক্ট সিঙ্ক করুন"
                >
                    <ArrowPathIcon class="h-4 w-4" :class="{ 'animate-spin': syncing }" />
                    <span>{{ syncing ? 'সিঙ্ক হচ্ছে...' : 'পেজপাইলটে প্ল্যান সিঙ্ক' }}</span>
                </button>
                <Link href="/admin/plans/create" class="btn btn-primary gap-2 text-xs font-bold">
                    <PlusIcon class="h-4 w-4" />
                    {{ t('admin.plans.new') }}
                </Link>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="mb-6 flex flex-wrap items-center gap-2 border-b border-[var(--border)] pb-3">
            <button
                type="button"
                @click="activeTab = 'all'"
                class="flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition cursor-pointer"
                :class="activeTab === 'all' ? 'bg-[var(--primary)] text-white shadow-xs' : 'bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
            >
                <span>সকল প্ল্যান</span>
                <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="activeTab === 'all' ? 'bg-white/20' : 'bg-[var(--surface)]'">{{ tabCounts.all }}</span>
            </button>
            <button
                type="button"
                @click="activeTab = 'general'"
                class="flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition cursor-pointer"
                :class="activeTab === 'general' ? 'bg-[var(--primary)] text-white shadow-xs' : 'bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
            >
                <span>🌐 জেনারেল প্ল্যান</span>
                <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="activeTab === 'general' ? 'bg-white/20' : 'bg-[var(--surface)]'">{{ tabCounts.general }}</span>
            </button>
            <button
                type="button"
                @click="activeTab = 'admission'"
                class="flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition cursor-pointer"
                :class="activeTab === 'admission' ? 'bg-orange-600 text-white shadow-xs' : 'bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
            >
                <span>🎓 ভর্তি পরীক্ষা বান্ডেল</span>
                <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="activeTab === 'admission' ? 'bg-white/20' : 'bg-[var(--surface)]'">{{ tabCounts.admission }}</span>
            </button>
            <button
                type="button"
                @click="activeTab = 'job'"
                class="flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition cursor-pointer"
                :class="activeTab === 'job' ? 'bg-blue-600 text-white shadow-xs' : 'bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
            >
                <span>💼 বিসিএস ও চাকরি বান্ডেল</span>
                <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="activeTab === 'job' ? 'bg-white/20' : 'bg-[var(--surface)]'">{{ tabCounts.job }}</span>
            </button>
            <button
                type="button"
                @click="activeTab = 'academic'"
                class="flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition cursor-pointer"
                :class="activeTab === 'academic' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]'"
            >
                <span>📘 HSC / SSC</span>
                <span class="rounded-full px-1.5 py-0.2 text-[10px]" :class="activeTab === 'academic' ? 'bg-white/20' : 'bg-[var(--surface)]'">{{ tabCounts.academic }}</span>
            </button>
            <button
                type="button"
                @click="activeTab = 'custom_builder'"
                class="flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-bold transition cursor-pointer border border-amber-500/30"
                :class="activeTab === 'custom_builder' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-500/10 text-amber-700 dark:text-amber-400 hover:bg-amber-500/20'"
            >
                <AdjustmentsHorizontalIcon class="h-4 w-4" />
                <span>⚡ কাস্টম প্ল্যান বিল্ডার রেট</span>
            </button>
        </div>

        <!-- Custom Plan Builder Configuration Section -->
        <div v-if="activeTab === 'custom_builder'" class="mb-8">
            <div class="card rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 md:p-8 max-w-4xl">
                <div class="flex items-start justify-between gap-4 mb-6 pb-4 border-b border-[var(--border)]">
                    <div>
                        <h2 class="font-heading text-xl font-bold text-[var(--text)] flex items-center gap-2">
                            <AdjustmentsHorizontalIcon class="h-6 w-6 text-amber-500" />
                            <span>কাস্টম প্ল্যান বিল্ডার ইউনিট রেট সেটিংস (Custom Plan Configuration)</span>
                        </h2>
                        <p class="text-xs text-[var(--text-muted)] mt-1">
                            শিক্ষার্থীরা যখন তাদের পছন্দমতো মিনিট, ছবি বা বই সিলেক্ট করে কাস্টম প্ল্যান তৈরি করবে, তখন নিচের রেট অনুযায়ী হিসাব হবে।
                        </p>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" v-model="customForm.custom_plan_enabled" class="rounded border-[var(--border)] text-[var(--primary)] focus:ring-[var(--primary)]" />
                        <span class="text-xs font-bold text-[var(--text)]">বিল্ডার সক্রিয় রাখুন</span>
                    </label>
                </div>

                <form @submit.prevent="saveCustomRates" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="p-4 rounded-2xl bg-[var(--surface2)] border border-[var(--border)]">
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">প্ল্যাটফর্ম বেস ফি (Base Fee ৳)</label>
                            <input v-model.number="customForm.custom_plan_base_fee" type="number" step="0.5" min="0" class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] font-bold text-sm" />
                            <span class="text-[10px] text-[var(--text-muted)] mt-1 block">প্রতিটি কাস্টম অর্ডারের সর্বনিম্ন বেস চার্জ</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[var(--surface2)] border border-[var(--border)]">
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">সর্বনিম্ন মাসিক বিল (Min Monthly ৳)</label>
                            <input v-model.number="customForm.custom_plan_min_monthly" type="number" step="1" min="0" class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] font-bold text-sm" />
                            <span class="text-[10px] text-[var(--text-muted)] mt-1 block">কাস্টম প্ল্যানের সর্বনিম্ন গ্রহণযোগ্য বিল</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[var(--surface2)] border border-[var(--border)]">
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">AI চ্যাট প্রতি মিনিট রেট (৳)</label>
                            <input v-model.number="customForm.custom_plan_rate_minute" type="number" step="0.01" min="0" class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] font-bold text-sm" />
                            <span class="text-[10px] text-[var(--text-muted)] mt-1 block">প্রতি ১ মিনিট এআই চ্যাটের মূল্য (যেমন: 0.22)</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[var(--surface2)] border border-[var(--border)]">
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">AI ভিশন / ইমেজ প্রতি রেট (৳)</label>
                            <input v-model.number="customForm.custom_plan_rate_image" type="number" step="0.05" min="0" class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] font-bold text-sm" />
                            <span class="text-[10px] text-[var(--text-muted)] mt-1 block">প্রতি ১টি গণিত/চিত্র বিশ্লেষণের মূল্য</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[var(--surface2)] border border-[var(--border)]">
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">বই আপলোড স্লট প্রতি রেট (৳)</label>
                            <input v-model.number="customForm.custom_plan_rate_book" type="number" step="0.5" min="0" class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] font-bold text-sm" />
                            <span class="text-[10px] text-[var(--text-muted)] mt-1 block">প্রতি ১টি বই আপলোড স্লটের মূল্য</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[var(--surface2)] border border-[var(--border)]">
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">বই রি-আপলোড প্রতি রেট (৳)</label>
                            <input v-model.number="customForm.custom_plan_rate_reupload" type="number" step="0.5" min="0" class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] font-bold text-sm" />
                            <span class="text-[10px] text-[var(--text-muted)] mt-1 block">মাসিক প্রতি রি-আপলোড পরিবর্তনের মূল্য</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[var(--surface2)] border border-[var(--border)]">
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">AI মডেল টেস্ট প্রতি রেট (৳)</label>
                            <input v-model.number="customForm.custom_plan_rate_quiz" type="number" step="0.1" min="0" class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] font-bold text-sm" />
                            <span class="text-[10px] text-[var(--text-muted)] mt-1 block">প্রতি ১টি কাস্টম মডেল টেস্টের মূল্য</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-[var(--surface2)] border border-[var(--border)]">
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">AI প্রশ্ন ব্যাখ্যা প্রতি রেট (৳)</label>
                            <input v-model.number="customForm.custom_plan_rate_explanation" type="number" step="0.1" min="0" class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] font-bold text-sm" />
                            <span class="text-[10px] text-[var(--text-muted)] mt-1 block">প্রতি ১টি বিস্তারিত ব্যাখ্যা কোটার মূল্য</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-[var(--border)]">
                        <button
                            type="submit"
                            :disabled="customForm.processing"
                            class="btn btn-primary px-6 py-2.5 text-xs font-bold shadow-md shadow-[var(--primary)]/20"
                        >
                            {{ customForm.processing ? 'সংরক্ষণ হচ্ছে...' : 'কাস্টম প্ল্যান রেট সংরক্ষণ করুন' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Plans Grid -->
        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="plan in filteredPlans"
                :key="plan.id"
                class="card card-hover relative flex flex-col justify-between overflow-hidden rounded-2xl border border-[var(--border)] p-6 transition-all"
            >
                <div>
                    <!-- Top header -->
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="mb-1.5 flex flex-wrap items-center gap-1.5">
                                <span class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[10px] font-bold" :class="getSegmentColor(plan.target_segment)">
                                    <TagIcon class="h-3 w-3" />
                                    <span>{{ getSegmentLabel(plan.target_segment) }}</span>
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-[var(--surface2)] px-2 py-0.5 text-[10px] font-bold text-[var(--text)]">
                                    <ClockIcon class="h-3 w-3 text-[var(--primary)]" />
                                    <span>{{ (plan.duration_months || 1) > 1 ? plan.duration_months + ' মাস মেয়াদ' : '১ মাস' }}</span>
                                </span>
                            </div>

                            <div v-if="plan.badge_text" class="mb-1 text-[11px] font-bold text-amber-600 dark:text-amber-400">
                                ⭐ {{ plan.badge_text }}
                            </div>

                            <h3 class="font-heading text-lg font-bold text-[var(--text)]">{{ plan.name }}</h3>
                            <div class="mt-0.5 flex items-center gap-1.5 text-xs text-[var(--text-muted)]">
                                <UsersIcon class="h-3.5 w-3.5 text-[var(--secondary)]" />
                                <span>{{ plan.subscriptions_count }} {{ locale === 'en' ? 'subscribers' : 'জন গ্রাহক' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                :class="plan.is_active ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'"
                            >
                                <span class="h-1.5 w-1.5 rounded-full" :class="plan.is_active ? 'bg-[var(--secondary)]' : 'bg-[var(--text-muted)]'"></span>
                                {{ plan.is_active ? t('common.active') : t('common.inactive') }}
                            </span>
                            <TableDropdown :items="getPlanActions(plan)" align="right" />
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="my-5 border-b border-[var(--border)] pb-4">
                        <div v-if="(plan.duration_months || 1) > 1" class="flex items-baseline gap-1.5">
                            <span class="font-heading text-3xl font-extrabold text-[var(--text)]">৳{{ plan.price_total || plan.price_monthly }}</span>
                            <span class="text-xs text-[var(--text-muted)] font-bold">/ মোট ({{ plan.duration_months }} মাস)</span>
                        </div>
                        <div v-else class="flex items-baseline gap-1">
                            <span class="font-heading text-3xl font-extrabold text-[var(--text)]">৳{{ plan.price_monthly }}</span>
                            <span class="text-xs text-[var(--text-muted)] font-medium">/ {{ locale === 'en' ? 'month' : 'মাস' }}</span>
                        </div>
                        <div v-if="(plan.duration_months || 1) > 1" class="text-[11px] text-[var(--text-muted)] mt-1">
                            মাসিক সমতুল্য প্রায় ৳{{ plan.price_monthly }}/মাস
                        </div>
                    </div>

                    <!-- Quota Badges Grid -->
                    <div class="mb-4 grid grid-cols-2 gap-2 text-[11px]">
                        <div class="rounded-lg bg-[var(--surface2)] px-2.5 py-1.5 font-medium text-[var(--text-muted)]">
                            <span class="block text-[10px] uppercase font-bold text-[var(--text)]">💬 AI Chat</span>
                            {{ plan.ai_chat_minutes }} {{ locale === 'en' ? 'mins' : 'মিনিট' }}
                        </div>
                        <div class="rounded-lg bg-[var(--surface2)] px-2.5 py-1.5 font-medium text-[var(--text-muted)]">
                            <span class="block text-[10px] uppercase font-bold text-[var(--text)]">📸 Vision Chat</span>
                            {{ plan.ai_vision_limit || 0 }} {{ locale === 'en' ? 'images' : 'টি ছবি' }}
                        </div>
                        <div class="rounded-lg bg-[var(--surface2)] px-2.5 py-1.5 font-medium text-[var(--text-muted)]">
                            <span class="block text-[10px] uppercase font-bold text-[var(--text)]">✍️ AI Exams</span>
                            {{ plan.ai_exam_count }} {{ locale === 'en' ? 'exams' : 'টি পরীক্ষা' }}
                        </div>
                        <div class="rounded-lg bg-[var(--surface2)] px-2.5 py-1.5 font-medium text-[var(--text-muted)]">
                            <span class="block text-[10px] uppercase font-bold text-[var(--text)]">💡 AI Explanations</span>
                            {{ plan.ai_explanation_count || 50 }} {{ locale === 'en' ? 'questions' : 'টি ব্যাখ্যা' }}
                        </div>
                        <div class="col-span-2 rounded-lg bg-[var(--surface2)] px-2.5 py-1.5 font-medium text-[var(--text-muted)]">
                            <span class="block text-[10px] uppercase font-bold text-[var(--text)]">📚 Upload Slots</span>
                            {{ plan.upload_book_limit || 1 }} {{ locale === 'en' ? 'books' : 'টি বই' }} ({{ plan.monthly_reupload_limit || 2 }}/mo)
                        </div>
                    </div>

                    <!-- AI Model Tier Badge -->
                    <div class="mb-4 flex items-center gap-1.5 rounded-xl bg-[var(--surface2)] px-3 py-1.5 text-xs">
                        <SparklesIcon class="h-3.5 w-3.5 text-[var(--primary)] shrink-0" />
                        <span class="text-[var(--text-muted)] text-[11px] font-medium">AI Model:</span>
                        <span v-if="plan.ai_provider" class="font-semibold text-[var(--text)] text-[11px] truncate">
                            {{ plan.ai_provider.name }}
                            <span v-if="plan.ai_model" class="text-[var(--primary)] font-mono ml-1">({{ plan.ai_model }})</span>
                            <span v-else class="text-[var(--text-muted)] font-mono ml-1">({{ plan.ai_provider.model_name }})</span>
                        </span>
                        <span v-else class="text-[11px] text-[var(--text-muted)] italic">
                            System Default
                        </span>
                    </div>

                    <!-- Features -->
                    <ul class="space-y-2.5 text-xs text-[var(--text-muted)]">
                        <li v-for="f in plan.features" :key="f" class="flex items-start gap-2">
                            <CheckIcon class="mt-0.5 h-3.5 w-3.5 shrink-0 text-[var(--secondary)]" />
                            <span class="leading-relaxed">{{ f }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Footer button -->
                <div class="mt-6 pt-4 border-t border-[var(--border)] flex items-center justify-between">
                    <Link
                        :href="`/admin/plans/${plan.id}/edit`"
                        class="text-xs font-semibold text-[var(--primary)] hover:underline inline-flex items-center gap-1"
                    >
                        <PencilSquareIcon class="h-3.5 w-3.5" />
                        {{ t('common.edit') }}
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="activeTab !== 'custom_builder' && (!filteredPlans || !filteredPlans.length)" class="card rounded-2xl border border-dashed border-[var(--border)] p-12 text-center">
            <CreditCardIcon class="mx-auto h-12 w-12 text-[var(--text-muted)]/40" />
            <div class="mt-3 font-heading font-semibold text-lg">{{ t('common.no_results') }}</div>
            <p class="mt-1 text-sm text-[var(--text-muted)]">
                এই ক্যাটাগরিতে এখনো কোনো প্ল্যান তৈরি করা হয়নি।
            </p>
            <Link href="/admin/plans/create" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-[var(--primary)] px-4 py-2 text-xs font-bold text-white shadow-md">
                <PlusIcon class="h-4 w-4" />
                নতুন প্ল্যান তৈরি করুন
            </Link>
        </div>
    </AdminLayout>
</template>
