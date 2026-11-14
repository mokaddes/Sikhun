<script setup>
import { ref, onUnmounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ settings: Object });
const { t, locale } = useI18n();

const testingSocial = ref(false);

function testPagePilot() {
    testingSocial.value = true;
    router.post('/admin/settings/test-pagepilot', {}, {
        onFinish: () => {
            testingSocial.value = false;
        }
    });
}

const form = useForm({
    site_name: props.settings.site_name ?? '',
    site_name_bn: props.settings.site_name_bn ?? 'শিখুন',
    site_tagline: props.settings.site_tagline ?? 'YOUR AI STUDY DESK',
    site_tagline_bn: props.settings.site_tagline_bn ?? 'আপনার এআই পড়ার টেবিল',
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
    referral_reward_tiers: props.settings.referral_reward_tiers ?? '',
    support_bot_system_prompt: props.settings.support_bot_system_prompt ?? '',
    blog_digest_email: props.settings.blog_digest_email ?? props.settings.site_email ?? '',
    blog_credit_enabled: props.settings.blog_credit_enabled === undefined ? true : Boolean(Number(props.settings.blog_credit_enabled) || props.settings.blog_credit_enabled === 'true' || props.settings.blog_credit_enabled === true),
    blog_daily_ai_limit: props.settings.blog_daily_ai_limit ?? 25,
    pagepilot_webhook_url: props.settings.pagepilot_webhook_url ?? 'https://pagepilotbd.com/api/v1/pages/1299709296552706/trigger',
    pagepilot_api_key: props.settings.pagepilot_api_key ?? 'pp_sec_44350dm0rtZ3kshrDTYM9CcmXS1VaPH2y2sh',
    pagepilot_auto_publish: props.settings.pagepilot_auto_publish === undefined ? true : Boolean(Number(props.settings.pagepilot_auto_publish) || props.settings.pagepilot_auto_publish === 'true' || props.settings.pagepilot_auto_publish === true),
});

const logoPreview = ref(props.settings.site_logo_url ?? null);
const faviconPreview = ref(props.settings.site_favicon_url ?? null);
const seoPreview = ref(props.settings.seo_image_url ?? null);

function releasePreview(preview) {
    if (preview.value?.startsWith('blob:')) URL.revokeObjectURL(preview.value);
}

function onFile(field, preview, event) {
    const file = event.target.files[0];
    if (file) {
        releasePreview(preview);
        form[field] = file;
        form['remove_' + field] = false;
        preview.value = URL.createObjectURL(file);
    }
}

function removeImage(field) {
  form[field] = null;
  form['remove_' + field] = true;

  // Reset the correct ref dynamically
  const preview = field === 'site_logo' ? logoPreview : field === 'site_favicon' ? faviconPreview : seoPreview;
  releasePreview(preview);
  preview.value = null;
}

onUnmounted(() => [logoPreview, faviconPreview, seoPreview].forEach(releasePreview));

function submit() {
  form.transform((data) => {
    const clean = { ...data };

    // Add method spoofing for Laravel
    clean._method = 'put';

    // Strip non-File objects so untreated existing images aren't corrupted
    ['site_logo', 'site_favicon', 'seo_image'].forEach((key) => {
      if (!(clean[key] instanceof File)) {
        delete clean[key];
      }
    });
    return clean;
  }).post('/admin/settings'); // Changed from .put() to .post()
}
</script>

<template>
    <Head :title="t('admin.settings.title')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('admin.settings.title') }}</h1>
        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ locale === 'bn' ? 'ওয়েবসাইটের নাম (English)' : 'Website name (English)' }}</label>
                <input v-model="form.site_name" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ locale === 'bn' ? 'ওয়েবসাইটের নাম (বাংলা)' : 'Website name (Bangla)' }}</label>
                <input v-model="form.site_name_bn" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ locale === 'bn' ? 'ট্যাগলাইন (English)' : 'Tagline (English)' }}</label>
                <input v-model="form.site_tagline" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ locale === 'bn' ? 'ট্যাগলাইন (বাংলা)' : 'Tagline (Bangla)' }}</label>
                <input v-model="form.site_tagline_bn" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
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

                <div class="mb-5 rounded-xl border border-[var(--border)] bg-[var(--surface2)] p-4">
                    <div class="mb-3 text-xs font-semibold uppercase tracking-wide text-[var(--text-muted)]">{{ locale === 'bn' ? 'সাইটে লোগোটি এমন দেখাবে' : 'Brand preview' }}</div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="flex min-h-24 items-center gap-3 rounded-xl border border-[var(--border)] bg-[#fffefa] px-4 py-3 text-[#29251f]">
                            <img :src="logoPreview || '/images/sikhun-mark.svg'" :alt="`${form.site_name} logo`" class="h-12 w-12 shrink-0 object-contain" />
                            <div class="min-w-0"><div class="truncate font-serif-classic text-base font-bold">{{ form.site_name || 'Sikhun.com' }}</div><div class="mt-1 truncate text-[10px] tracking-[.12em] text-[#4a3fd6]">{{ form.site_tagline || 'YOUR AI STUDY DESK' }}</div></div>
                        </div>
                        <div class="flex min-h-24 items-center gap-3 rounded-xl border border-[#38342c] bg-[#211f1a] px-4 py-3 text-[#eee9df]">
                            <img :src="logoPreview || '/images/sikhun-mark.svg'" :alt="`${form.site_name_bn} logo`" class="h-12 w-12 shrink-0 object-contain" />
                            <div class="min-w-0"><div class="truncate font-bangla-classic text-base font-bold">{{ form.site_name_bn || 'শিখুন' }}</div><div class="mt-1 truncate text-[10px] text-[#d2ad7c]">{{ form.site_tagline_bn || 'আপনার এআই পড়ার টেবিল' }}</div></div>
                        </div>
                    </div>
                    <p class="mt-3 text-xs leading-relaxed text-[var(--text-muted)]">{{ locale === 'bn' ? 'স্বচ্ছ ব্যাকগ্রাউন্ডসহ বর্গাকার SVG বা PNG লোগো আপলোড করুন। সাইটের নাম ও ট্যাগলাইন লোগোর পাশে দেখানো হবে।' : 'Upload a square SVG or PNG mark with a transparent background. The site name and tagline will appear beside it.' }}
                        <a href="/images/sikhun-mark.svg" download="sikhun-mark.svg" class="ml-1 font-semibold text-[var(--primary)] underline underline-offset-2">{{ locale === 'bn' ? 'শিখুন লোগো ডাউনলোড' : 'Download the Sikhun mark' }}</a>
                    </p>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.settings.site_logo') }}</label>
                        <div class="flex items-center gap-4">
                            <img v-if="logoPreview" :src="logoPreview" :alt="t('admin.settings.site_logo')" @error="logoPreview = null" class="h-12 w-12 object-contain rounded-lg bg-[var(--surface2)] p-1" />
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
                            <img v-if="faviconPreview" :src="faviconPreview" :alt="t('admin.settings.site_favicon')" @error="faviconPreview = null" class="h-10 w-10 object-contain rounded-lg bg-[var(--surface2)] p-1" />
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
                            <img v-if="seoPreview" :src="seoPreview" :alt="t('admin.settings.seo_image')" @error="seoPreview = null" class="h-14 w-24 object-cover rounded-lg bg-[var(--surface2)] p-1" />
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
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.settings.referral_reward_tiers') }}</label>
                <textarea v-model="form.referral_reward_tiers" rows="4" placeholder='[{"milestone":5,"referrer_reward":30,"referee_reward":20}]'
                    class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                <p class="text-xs text-[var(--text-muted)] mt-1">{{ t('admin.settings.referral_reward_tiers_hint') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Support Bot Tone / Persona</label>
                <textarea v-model="form.support_bot_system_prompt" rows="4" placeholder="e.g. Be warm and encouraging, use simple Bangla, keep replies under 3 sentences."
                    class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                <p class="text-xs text-[var(--text-muted)] mt-1">
                    Controls <em>how</em> the floating support bot writes. The bot is permanently restricted to
                    Sikhun.com topics (library, courses, plans, payments, account, AI tools) and declines anything
                    else — this field cannot widen that scope. Leave blank for the default tone.
                </p>
            </div>
            <div class="border-t border-[var(--border)] pt-5 space-y-4">
                <div class="text-sm font-semibold">Automated Blog Settings</div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Blog Draft Digest Email</label>
                    <input v-model="form.blog_digest_email" type="email" placeholder="admin@sikhun.com" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    <p class="text-xs text-[var(--text-muted)] mt-1">Receives the draft count digest email after each daily blog scrape run.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Daily AI Blog Generation Limit</label>
                    <input v-model="form.blog_daily_ai_limit" type="number" min="1" max="200" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="form.blog_credit_enabled" type="checkbox" class="rounded" />
                    Append source credit link at the bottom of AI-rewritten blog posts
                </label>
            </div>
            <div class="border-t border-[var(--border)] pt-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold flex items-center gap-2">
                            <span>ফেসবুক অটো-পোস্ট ইন্টিগ্রেশন (PagePilotBD API)</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">Active</span>
                        </div>
                        <p class="text-xs text-[var(--text-muted)] mt-0.5">নতুন ভর্তি বিজ্ঞপ্তি, চাকরির খবর ও ব্লগ পাবলিশ হলে সরাসরি ফেসবুক পেজে ফটো কার্ড সহ পোস্ট ও প্রথম কমেন্টে লিঙ্ক যাবে।</p>
                    </div>
                    <button type="button" @click="testPagePilot" :disabled="testingSocial" class="px-3.5 py-1.5 rounded-lg border border-[var(--border)] bg-[var(--surface2)] hover:bg-[var(--surface)] text-xs font-bold transition disabled:opacity-50">
                        {{ testingSocial ? 'টেস্টিং...' : '🧪 টেস্ট পোস্ট পাঠান' }}
                    </button>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">PagePilotBD Webhook Endpoint URL</label>
                    <input v-model="form.pagepilot_webhook_url" type="url" placeholder="https://pagepilotbd.com/api/v1/pages/1299709296552706/trigger" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] font-mono text-xs" />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">PagePilotBD API Secret Key</label>
                    <input v-model="form.pagepilot_api_key" type="password" placeholder="pp_sec_..." class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] font-mono text-xs" />
                </div>

                <label class="flex items-center gap-2.5 p-3 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm cursor-pointer">
                    <input v-model="form.pagepilot_auto_publish" type="checkbox" class="rounded text-[var(--primary)]" />
                    <span class="font-medium">ভর্তি বিজ্ঞপ্তি, চাকরির খবর বা ব্লগ পোস্ট পাবলিশ হওয়ার সাথে সাথে স্বয়ংক্রিয়ভাবে ফেসবুক পেজে পোস্ট ও প্রথম কমেন্ট করুন</span>
                </label>
            </div>

            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
