<script setup>
import { watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';
import { SparklesIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    plan: Object,
    providers: { type: Array, default: () => [] },
});
const { t } = useI18n();
const isEdit = !!props.plan;

const form = useForm({
    name: props.plan?.name ?? '',
    slug: props.plan?.slug ?? '',
    badge_text: props.plan?.badge_text ?? '',
    target_segment: props.plan?.target_segment ?? 'all',
    duration_months: props.plan?.duration_months ?? 1,
    price_total: props.plan?.price_total ?? (props.plan?.price_monthly ? Number(props.plan.price_monthly) : null),
    description: props.plan?.description ?? '',
    price_monthly: props.plan?.price_monthly ?? 0,
    ai_chat_minutes: props.plan?.ai_chat_minutes ?? 60,
    ai_vision_limit: props.plan?.ai_vision_limit ?? 20,
    upload_book_limit: props.plan?.upload_book_limit ?? 3,
    monthly_reupload_limit: props.plan?.monthly_reupload_limit ?? 5,
    ai_exam_count: props.plan?.ai_exam_count ?? 20,
    ai_explanation_count: props.plan?.ai_explanation_count ?? 50,
    trial_ai_minutes: props.plan?.trial_ai_minutes ?? 10,
    features: props.plan?.features ?? '',
    ai_provider_id: props.plan?.ai_provider_id ?? null,
    ai_model: props.plan?.ai_model ?? '',
    is_active: props.plan?.is_active ?? true,
    is_free: props.plan?.is_free ?? false,
});

let slugTouched = isEdit;
watch(() => form.name, (val) => {
    if (!slugTouched) form.slug = val.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
});

// Auto-sync monthly and total price when duration changes
watch([() => form.price_total, () => form.duration_months], ([total, months]) => {
    if (total && months > 0 && (!form.price_monthly || form.price_monthly == Math.round((total / months) * 100) / 100)) {
        form.price_monthly = Math.round((total / months) * 100) / 100;
    }
});

function submit() {
    isEdit ? form.put(`/admin/plans/${props.plan.id}`) : form.post('/admin/plans');
}
</script>

<template>
    <Head :title="isEdit ? t('common.edit') : t('admin.plans.new')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('common.edit') : t('admin.plans.new') }}</h1>
        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('common.name') }}</label>
                    <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Badge Text (যেমন: 3 Months Crash, 20% Mega Savings)</label>
                    <input v-model="form.badge_text" type="text" placeholder="e.g. 3 Months Crash Course" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Slug</label>
                    <input v-model="form.slug" @input="slugTouched = true" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Target Segment (টার্গেট সেগমেন্ট)</label>
                    <select v-model="form.target_segment" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] font-semibold text-sm">
                        <option value="all">🌐 All Students / General (সকল শিক্ষার্থী)</option>
                        <option value="admission">🎓 University Admission (বিশ্ববিদ্যালয় ভর্তি)</option>
                        <option value="job">💼 BCS & Govt Jobs (বিসিএস ও চাকরি)</option>
                        <option value="hsc">📘 HSC (উচ্চ মাধ্যমিক)</option>
                        <option value="ssc">📗 SSC (মাধ্যমিক)</option>
                        <option value="custom">⚡ Custom / Special Plan (কাস্টম প্যাকেজ)</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Duration in Months (মেয়াদ - মাস)</label>
                    <input v-model.number="form.duration_months" type="number" min="1" max="60" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] font-bold" />
                    <span class="text-[11px] text-[var(--text-muted)]">১ মাস, ২ মাস, ৩ মাস বা ৬ মাস ইত্যাদি</span>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Total Package Price (প্যাকেজের মোট মূল্য ৳)</label>
                    <input v-model.number="form.price_total" type="number" step="0.01" min="0" placeholder="e.g. 450" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] font-bold text-emerald-600 dark:text-emerald-400" />
                    <span class="text-[11px] text-[var(--text-muted)]">৩ মাস বা ৬ মাসের বান্ডেল মোট মূল্য (যেমনঃ ৳৪৫০ বা ৳৮৫০)</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Description</label>
                <textarea v-model="form.description" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
            </div>

            <!-- Limits & Pricing Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.price_monthly') }} (মাসিক সমতুল্য ৳)</label>
                    <input v-model="form.price_monthly" type="number" step="0.01" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    <span class="text-[11px] text-[var(--text-muted)]">মাসিক সমতুল্য রেট (মোট মূল্য / মেয়াদ)</span>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.trial_minutes') }}</label>
                    <input v-model="form.trial_ai_minutes" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.ai_chat_minutes') }}</label>
                    <input v-model="form.ai_chat_minutes" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.ai_vision_limit') }}</label>
                    <input v-model="form.ai_vision_limit" type="number" min="0" placeholder="e.g. 50" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    <span class="text-[11px] text-[var(--text-muted)]">Image/photo problem solving quota</span>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.upload_book_limit') }}</label>
                    <input v-model="form.upload_book_limit" type="number" min="1" placeholder="e.g. 3" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    <span class="text-[11px] text-[var(--text-muted)]">Max active concurrent books on shelf</span>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.monthly_reupload_limit') }}</label>
                    <input v-model="form.monthly_reupload_limit" type="number" min="1" placeholder="e.g. 10" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    <span class="text-[11px] text-[var(--text-muted)]">Max uploads / re-uploads per month</span>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.ai_exam_count') }}</label>
                    <input v-model="form.ai_exam_count" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    <span class="text-[11px] text-[var(--text-muted)]">Monthly AI exam sessions</span>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">AI Explanation Limit (এআই ব্যাখ্যা লিমিট)</label>
                    <input v-model="form.ai_explanation_count" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    <span class="text-[11px] text-[var(--text-muted)]">Monthly question explanations allowed</span>
                </div>
            </div>

            <!-- AI Model Tier (Plan-Wise AI Model Selection) -->
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4 space-y-3">
                <div class="flex items-center gap-2">
                    <SparklesIcon class="h-4 w-4 text-[var(--primary)]" />
                    <h3 class="text-sm font-bold text-[var(--text)]">AI Model Tier (Plan-Wise)</h3>
                </div>
                <p class="text-xs text-[var(--text-muted)]">
                    Assign which AI Provider and model will serve students subscribed to this plan (e.g. Basic Plan uses Gemini 1.5 Flash / GPT-4o-mini, Pro Plan uses Claude 3.5 Sonnet / GPT-4o).
                </p>

                <div>
                    <label class="block text-xs font-semibold mb-1">Assigned AI Provider</label>
                    <select v-model="form.ai_provider_id" class="w-full px-3.5 py-2 text-sm rounded-lg bg-[var(--surface)] border border-[var(--border)]">
                        <option :value="null">System Default (Use task-level default AI provider)</option>
                        <option v-for="prov in providers" :key="prov.id" :value="prov.id">
                            {{ prov.name }} — {{ prov.model_name }} ({{ prov.type }})
                        </option>
                    </select>
                </div>

                <div v-if="form.ai_provider_id">
                    <label class="block text-xs font-semibold mb-1">Custom Model Name (Optional Override)</label>
                    <input
                        v-model="form.ai_model"
                        type="text"
                        placeholder="Leave empty to use provider's configured model"
                        class="w-full px-3.5 py-2 text-sm rounded-lg bg-[var(--surface)] border border-[var(--border)] font-mono text-xs"
                    />
                    <p class="text-[11px] text-[var(--text-muted)] mt-1">
                        Optional: Specify a model name if you want this plan to use a different model under this provider.
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.features') }}</label>
                <textarea v-model="form.features" rows="4" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
            </div>

            <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded" /> {{ t('common.active') }}</label>
            <label class="flex items-center gap-2 text-sm"><input v-model="form.is_free" type="checkbox" class="rounded" /> {{ t('admin.plans.is_free') }}</label>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
