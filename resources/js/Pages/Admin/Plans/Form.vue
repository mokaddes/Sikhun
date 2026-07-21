<script setup>
import { watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ plan: Object, books: Array });
const { t } = useI18n();
const isEdit = !!props.plan;

const form = useForm({
    name: props.plan?.name ?? '',
    slug: props.plan?.slug ?? '',
    description: props.plan?.description ?? '',
    price_monthly: props.plan?.price_monthly ?? 0,
    ai_chat_minutes: props.plan?.ai_chat_minutes ?? 60,
    ai_exam_count: props.plan?.ai_exam_count ?? 20,
    trial_ai_minutes: props.plan?.trial_ai_minutes ?? 10,
    features: props.plan?.features ?? '',
    gift_book_ids: props.plan?.gift_book_ids ?? [],
    is_active: props.plan?.is_active ?? true,
});

let slugTouched = isEdit;
watch(() => form.name, (val) => {
    if (!slugTouched) form.slug = val.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
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
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('common.name') }}</label>
                <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input v-model="form.slug" @input="slugTouched = true" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Description</label>
                <textarea v-model="form.description" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.price_monthly') }} (৳)</label>
                    <input v-model="form.price_monthly" type="number" step="0.01" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
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
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.ai_exam_count') }}</label>
                    <input v-model="form.ai_exam_count" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.plans.features') }}</label>
                <textarea v-model="form.features" rows="4" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Gift Books</label>
                <div class="max-h-40 overflow-y-auto rounded-lg border border-[var(--border)] p-3 space-y-1">
                    <label v-for="b in books" :key="b.id" class="flex items-center gap-2 text-sm">
                        <input type="checkbox" :value="b.id" v-model="form.gift_book_ids" class="rounded" /> {{ b.title }}
                    </label>
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded" /> {{ t('common.active') }}</label>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
