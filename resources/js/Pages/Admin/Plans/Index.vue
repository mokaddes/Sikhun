<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
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
} from '@heroicons/vue/24/outline';

defineProps({ plans: Array });
const { t, locale } = useI18n();

const syncing = ref(false);

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

function getSegmentLabel(seg) {
    const map = {
        all: 'সকল শিক্ষার্থী (All)',
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
                    {{ locale === 'en' ? 'Create, configure and manage Admission, Job & Custom subscription tiers.' : 'ভর্তি, চাকরি, এইচএসসি ও কাস্টম সাবস্ক্রিপশন প্ল্যানসমূহ পরিচালনা করুন।' }}
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

        <!-- Plans Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="plan in plans"
                :key="plan.id"
                class="card card-hover relative flex flex-col justify-between overflow-hidden rounded-2xl border border-[var(--border)] p-6 transition-all"
            >
                <div>
                    <!-- Top header -->
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="mb-1.5 inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[10px] font-bold" :class="getSegmentColor(plan.target_segment)">
                                <TagIcon class="h-3 w-3" />
                                <span>{{ getSegmentLabel(plan.target_segment) }}</span>
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
                    <div class="my-5 flex items-baseline gap-1 border-b border-[var(--border)] pb-5">
                        <span class="font-heading text-3xl font-extrabold text-[var(--text)]">৳{{ plan.price_monthly }}</span>
                        <span class="text-xs text-[var(--text-muted)] font-medium">/ {{ locale === 'en' ? 'month' : 'মাস' }}</span>
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

        <div v-if="!plans || !plans.length" class="card rounded-2xl border border-dashed border-[var(--border)] p-12 text-center">
            <CreditCardIcon class="mx-auto h-12 w-12 text-[var(--text-muted)]/40" />
            <div class="mt-3 font-heading font-semibold text-lg">{{ t('common.no_results') }}</div>
            <p class="mt-1 text-sm text-[var(--text-muted)]">
                {{ locale === 'en' ? 'No subscription plans have been created yet.' : 'এখনো কোনো সাবস্ক্রিপশন প্ল্যান তৈরি করা হয়নি।' }}
            </p>
        </div>
    </AdminLayout>
</template>
