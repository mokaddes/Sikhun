<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ settings: Object });
const { t } = useI18n();

const form = useForm({
    site_name: props.settings.site_name ?? '',
    site_tagline: props.settings.site_tagline ?? '',
    site_email: props.settings.site_email ?? '',
    site_phone: props.settings.site_phone ?? '',
    meta_title: props.settings.meta_title ?? '',
    meta_description: props.settings.meta_description ?? '',
    referrer_reward_amount: props.settings.referrer_reward_amount ?? 20,
    referee_reward_amount: props.settings.referee_reward_amount ?? 20,
    max_referral_per_month: props.settings.max_referral_per_month ?? 10,
    support_bot_system_prompt: props.settings.support_bot_system_prompt ?? '',
});

function submit() {
    form.put('/admin/settings');
}
</script>

<template>
    <Head :title="t('admin.settings.title')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('admin.settings.title') }}</h1>
        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.settings.site_name') }}</label>
                <input v-model="form.site_name" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.settings.site_tagline') }}</label>
                <input v-model="form.site_tagline" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.settings.site_email') }}</label>
                    <input v-model="form.site_email" type="email" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.settings.site_phone') }}</label>
                    <input v-model="form.site_phone" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Meta Title</label>
                <input v-model="form.meta_title" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Meta Description</label>
                <textarea v-model="form.meta_description" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Referrer Reward (৳)</label>
                    <input v-model="form.referrer_reward_amount" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Referee Reward (৳)</label>
                    <input v-model="form.referee_reward_amount" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Max Rewarded Referrals / Student / Month</label>
                <input v-model="form.max_referral_per_month" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Support Bot System Prompt</label>
                <textarea v-model="form.support_bot_system_prompt" rows="4" placeholder="You are a friendly support assistant for Sikhun.com..."
                    class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                <p class="text-xs text-[var(--text-muted)] mt-1">Controls how the floating support chat bot responds. Leave blank to use the default prompt.</p>
            </div>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
