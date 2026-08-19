<script setup>
import { ref } from 'vue';
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
    site_logo: null,
    site_favicon: null,
    seo_image: null,
    remove_site_logo: false,
    remove_site_favicon: false,
    remove_seo_image: false,
    meta_title: props.settings.meta_title ?? '',
    meta_description: props.settings.meta_description ?? '',
    referrer_reward_amount: props.settings.referrer_reward_amount ?? 20,
    referee_reward_amount: props.settings.referee_reward_amount ?? 20,
    max_referral_per_month: props.settings.max_referral_per_month ?? 10,
    support_bot_system_prompt: props.settings.support_bot_system_prompt ?? '',
});

const logoPreview = ref(props.settings.site_logo_url ?? null);
const faviconPreview = ref(props.settings.site_favicon_url ?? null);
const seoPreview = ref(props.settings.seo_image_url ?? null);

function onFile(field, preview, event) {
    const file = event.target.files[0];
    if (file) {
        form[field] = file;
        form['remove_' + field] = false;
        preview.value = URL.createObjectURL(file);
    }
}

function removeImage(field, preview) {
    form[field] = null;
    form['remove_' + field] = true;
    preview.value = null;
}

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

            <div class="border-t border-[var(--border)] pt-5">
                <div class="text-sm font-semibold mb-4">{{ t('admin.settings.branding_section') }}</div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.settings.site_logo') }}</label>
                        <div class="flex items-center gap-4">
                            <img v-if="logoPreview" :src="logoPreview" :alt="t('admin.settings.site_logo')" class="h-12 w-auto object-contain rounded-lg bg-[var(--surface2)] p-1" />
                            <div v-else class="h-12 w-24 rounded-lg bg-[var(--surface2)] border border-dashed border-[var(--border)] flex items-center justify-center text-xs text-[var(--text-muted)]">{{ t('admin.settings.no_image') }}</div>
                            <div class="flex-1 space-y-2">
                                <input type="file" accept=".png,.jpg,.jpeg,.webp,.svg" @change="onFile('site_logo', logoPreview, $event)" class="block w-full text-sm text-[var(--text-muted)] file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[var(--surface2)] file:text-sm file:font-medium" />
                                <button v-if="logoPreview" type="button" @click="removeImage('site_logo', logoPreview)" class="text-xs text-[var(--accent)] hover:underline">{{ t('admin.settings.remove') }}</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.settings.site_favicon') }}</label>
                        <div class="flex items-center gap-4">
                            <img v-if="faviconPreview" :src="faviconPreview" :alt="t('admin.settings.site_favicon')" class="h-10 w-10 object-contain rounded-lg bg-[var(--surface2)] p-1" />
                            <div v-else class="h-10 w-24 rounded-lg bg-[var(--surface2)] border border-dashed border-[var(--border)] flex items-center justify-center text-xs text-[var(--text-muted)]">{{ t('admin.settings.no_image') }}</div>
                            <div class="flex-1 space-y-2">
                                <input type="file" accept=".png,.jpg,.jpeg,.webp,.svg,.ico" @change="onFile('site_favicon', faviconPreview, $event)" class="block w-full text-sm text-[var(--text-muted)] file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[var(--surface2)] file:text-sm file:font-medium" />
                                <button v-if="faviconPreview" type="button" @click="removeImage('site_favicon', faviconPreview)" class="text-xs text-[var(--accent)] hover:underline">{{ t('admin.settings.remove') }}</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.settings.seo_image') }}</label>
                        <div class="flex items-center gap-4">
                            <img v-if="seoPreview" :src="seoPreview" :alt="t('admin.settings.seo_image')" class="h-14 w-24 object-cover rounded-lg bg-[var(--surface2)] p-1" />
                            <div v-else class="h-14 w-24 rounded-lg bg-[var(--surface2)] border border-dashed border-[var(--border)] flex items-center justify-center text-xs text-[var(--text-muted)]">{{ t('admin.settings.no_image') }}</div>
                            <div class="flex-1 space-y-2">
                                <input type="file" accept=".png,.jpg,.jpeg,.webp" @change="onFile('seo_image', seoPreview, $event)" class="block w-full text-sm text-[var(--text-muted)] file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[var(--surface2)] file:text-sm file:font-medium" />
                                <button v-if="seoPreview" type="button" @click="removeImage('seo_image', seoPreview)" class="text-xs text-[var(--accent)] hover:underline">{{ t('admin.settings.remove') }}</button>
                                <p class="text-xs text-[var(--text-muted)]">{{ t('admin.settings.seo_image_hint') }}</p>
                            </div>
                        </div>
                    </div>
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