<script setup>
import { ref, onUnmounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';
import {
    GlobeAltIcon,
    MagnifyingGlassIcon,
    BoltIcon,
    GiftIcon,
    PhotoIcon,
    CheckCircleIcon,
    ArrowPathIcon,
    InformationCircleIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ settings: Object });
const { t, locale } = useI18n();

const testingSocial = ref(false);
const syncingKnowledge = ref(false);

function testPagePilot() {
    testingSocial.value = true;
    router.post('/admin/settings/test-pagepilot', {}, {
        onFinish: () => {
            testingSocial.value = false;
        }
    });
}

function syncKnowledge() {
    if (syncingKnowledge.value) return;
    syncingKnowledge.value = true;
    router.post('/admin/settings/sync-pagepilot-knowledge', {}, {
        preserveScroll: true,
        onFinish: () => {
            syncingKnowledge.value = false;
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
    seo_admission_image: null,
    seo_job_image: null,
    seo_blog_image: null,
    remove_site_logo: false,
    remove_site_favicon: false,
    remove_seo_image: false,
    remove_seo_admission_image: false,
    remove_seo_job_image: false,
    remove_seo_blog_image: false,
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
const admissionSeoPreview = ref(props.settings.seo_admission_image_url ?? null);
const jobSeoPreview = ref(props.settings.seo_job_image_url ?? null);
const blogSeoPreview = ref(props.settings.seo_blog_image_url ?? null);

const createdUrls = [];

function handleFileChange(event, field, previewRef, removeField) {
    const file = event.target.files?.[0];
    if (!file) return;

    form[field] = file;
    form[removeField] = false;

    const url = URL.createObjectURL(file);
    createdUrls.push(url);
    previewRef.value = url;
}

function removeFile(field, previewRef, removeField) {
    form[field] = null;
    form[removeField] = true;
    previewRef.value = null;
}

onUnmounted(() => {
    createdUrls.forEach((url) => URL.revokeObjectURL(url));
});

function submit() {
    form.post('/admin/settings', {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Head :title="t('admin.settings.title')" />
    <AdminLayout>
        <div class="mx-auto max-w-6xl pb-24">
            <!-- Header -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="font-heading text-2xl font-extrabold text-[var(--text)] sm:text-3xl">
                        {{ locale === 'bn' ? 'সিস্টেম ও সাইট সেটিংস' : 'System & Site Settings' }}
                    </h1>
                    <p class="mt-1 text-sm text-[var(--text-muted)]">
                        {{ locale === 'bn' ? 'ব্র্যান্ডিং, সোশ্যাল শেয়ারিং, এসইও এবং ব্যাকএন্ড অটোমেশন পরিচালনা করুন।' : 'Manage branding, SEO, social sharing, and automated background jobs.' }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="submit"
                        :disabled="form.processing"
                        class="btn btn-primary px-6 py-2.5 font-bold shadow-md inline-flex items-center gap-2"
                    >
                        <ArrowPathIcon v-if="form.processing" class="h-4 w-4 animate-spin" />
                        <span>{{ form.processing ? (locale === 'bn' ? 'সংরক্ষণ হচ্ছে...' : 'Saving...') : (locale === 'bn' ? 'পরিবর্তন সংরক্ষণ করুন' : 'Save Changes') }}</span>
                    </button>
                </div>
            </div>

            <!-- Flash Success Message -->
            <div v-if="$page.props.flash?.success" class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                <CheckCircleIcon class="h-5 w-5 shrink-0" />
                <span>{{ $page.props.flash.success }}</span>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- CARD 1: Site Identity & Branding -->
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center gap-3 border-b border-[var(--border)] pb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[var(--primary)]/10 text-[var(--primary)]">
                            <GlobeAltIcon class="h-6 w-6" />
                        </div>
                        <div>
                            <h2 class="font-heading text-lg font-bold text-[var(--text)]">
                                {{ locale === 'bn' ? 'সাইট পরিচয় ও ব্র্যান্ডিং' : 'Site Identity & Branding' }}
                            </h2>
                            <p class="text-xs text-[var(--text-muted)]">
                                {{ locale === 'bn' ? 'সাইটের বাংলা ও ইংরেজি নাম, ট্যাগলাইন, যোগাযোগ এবং লোগো ও ফেভিকন।' : 'Platform names, taglines, contact info, and visual marks.' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        <!-- English Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'ওয়েবসাইটের নাম (English)' : 'Website Name (English)' }}
                            </label>
                            <input
                                v-model="form.site_name"
                                type="text"
                                required
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                placeholder="Sikhun.com"
                            />
                        </div>

                        <!-- Bangla Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'ওয়েবসাইটের নাম (বাংলা)' : 'Website Name (Bangla)' }}
                            </label>
                            <input
                                v-model="form.site_name_bn"
                                type="text"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                placeholder="শিখুন"
                            />
                        </div>

                        <!-- English Tagline -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'ট্যাগলাইন (English)' : 'Tagline (English)' }}
                            </label>
                            <input
                                v-model="form.site_tagline"
                                type="text"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                placeholder="YOUR AI STUDY DESK"
                            />
                        </div>

                        <!-- Bangla Tagline -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'ট্যাগলাইন (বাংলা)' : 'Tagline (Bangla)' }}
                            </label>
                            <input
                                v-model="form.site_tagline_bn"
                                type="text"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                placeholder="আপনার এআই পড়ার টেবিল"
                            />
                        </div>

                        <!-- Site Email -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'অফিসিয়াল ইমেইল' : 'Official Support Email' }}
                            </label>
                            <input
                                v-model="form.site_email"
                                type="email"
                                required
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                placeholder="support@sikhun.com"
                            />
                        </div>

                        <!-- Site Phone -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'যোগাযোগের ফোন নম্বর' : 'Support Phone' }}
                            </label>
                            <input
                                v-model="form.site_phone"
                                type="text"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                placeholder="+880 1700-000000"
                            />
                        </div>
                    </div>

                    <!-- Live Brand Header Preview -->
                    <div class="mt-8 rounded-2xl border border-[var(--border)] bg-[var(--surface2)] p-5">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">
                                {{ locale === 'bn' ? 'হেডারে লোগো ও ব্র্যান্ডের লাইভ প্রিভিউ' : 'Live Header Brand Preview' }}
                            </span>
                            <span class="text-[11px] text-[var(--text-muted)]">
                                {{ locale === 'bn' ? 'লাইট ও ডার্ক উভয় থিমে যাচাই করুন' : 'Light & Dark renders' }}
                            </span>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="flex min-h-24 items-center gap-3.5 rounded-2xl border border-stone-200 bg-[#fffefa] px-5 py-4 shadow-xs">
                                <img :src="logoPreview || '/images/sikhun-mark.svg'" :alt="`${form.site_name} logo`" class="h-12 w-12 shrink-0 object-contain" />
                                <div class="min-w-0">
                                    <div class="truncate font-heading text-lg font-bold text-stone-900">{{ form.site_name || 'Sikhun.com' }}</div>
                                    <div class="truncate text-[10px] font-semibold tracking-wider text-[#ea580c] uppercase">{{ form.site_tagline || 'YOUR AI STUDY DESK' }}</div>
                                </div>
                            </div>
                            <div class="flex min-h-24 items-center gap-3.5 rounded-2xl border border-stone-800 bg-[#1e1c18] px-5 py-4 shadow-xs">
                                <img :src="logoPreview || '/images/sikhun-mark.svg'" :alt="`${form.site_name_bn} logo`" class="h-12 w-12 shrink-0 object-contain" />
                                <div class="min-w-0">
                                    <div class="truncate font-heading text-lg font-bold text-stone-100">{{ form.site_name_bn || 'শিখুন' }}</div>
                                    <div class="truncate text-[10px] font-medium text-[#fb923c]">{{ form.site_tagline_bn || 'আপনার এআই পড়ার টেবিল' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logo and Favicon Uploads -->
                    <div class="mt-6 grid gap-6 sm:grid-cols-2">
                        <!-- Site Logo -->
                        <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/50 p-4">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'সাইটের লোগো (SVG / PNG)' : 'Site Logo (SVG / PNG)' }}
                            </label>
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-[var(--border)] bg-[var(--surface)] p-2">
                                    <img v-if="logoPreview" :src="logoPreview" alt="Logo preview" class="h-full w-full object-contain" />
                                    <PhotoIcon v-else class="h-8 w-8 text-[var(--text-muted)]" />
                                </div>
                                <div class="flex-1 space-y-2">
                                    <input
                                        type="file"
                                        accept="image/svg+xml,image/png,image/jpeg,image/webp"
                                        @change="handleFileChange($event, 'site_logo', logoPreview, 'remove_site_logo')"
                                        class="block w-full text-xs text-[var(--text-muted)] file:mr-3 file:rounded-lg file:border-0 file:bg-[var(--primary)] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:opacity-90"
                                    />
                                    <button
                                        v-if="logoPreview"
                                        type="button"
                                        @click="removeFile('site_logo', logoPreview, 'remove_site_logo')"
                                        class="text-xs text-rose-500 hover:underline inline-flex items-center gap-1"
                                    >
                                        <TrashIcon class="h-3 w-3" /> {{ locale === 'bn' ? 'মুছে ফেলুন' : 'Remove' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Favicon -->
                        <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/50 p-4">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'ফেভিকন (SVG / ICO / PNG)' : 'Favicon (SVG / ICO / PNG)' }}
                            </label>
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-[var(--border)] bg-[var(--surface)] p-2">
                                    <img v-if="faviconPreview" :src="faviconPreview" alt="Favicon preview" class="h-8 w-8 object-contain" />
                                    <PhotoIcon v-else class="h-8 w-8 text-[var(--text-muted)]" />
                                </div>
                                <div class="flex-1 space-y-2">
                                    <input
                                        type="file"
                                        accept="image/svg+xml,image/x-icon,image/png,image/vnd.microsoft.icon"
                                        @change="handleFileChange($event, 'site_favicon', faviconPreview, 'remove_site_favicon')"
                                        class="block w-full text-xs text-[var(--text-muted)] file:mr-3 file:rounded-lg file:border-0 file:bg-[var(--primary)] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:opacity-90"
                                    />
                                    <button
                                        v-if="faviconPreview"
                                        type="button"
                                        @click="removeFile('site_favicon', faviconPreview, 'remove_site_favicon')"
                                        class="text-xs text-rose-500 hover:underline inline-flex items-center gap-1"
                                    >
                                        <TrashIcon class="h-3 w-3" /> {{ locale === 'bn' ? 'মুছে ফেলুন' : 'Remove' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: SEO & Social Sharing Images -->
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center gap-3 border-b border-[var(--border)] pb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-500">
                            <MagnifyingGlassIcon class="h-6 w-6" />
                        </div>
                        <div>
                            <h2 class="font-heading text-lg font-bold text-[var(--text)]">
                                {{ locale === 'bn' ? 'এসইও এবং সামাজিক মাধ্যম প্রিভিউ' : 'SEO & Social Share Cards' }}
                            </h2>
                            <p class="text-xs text-[var(--text-muted)]">
                                {{ locale === 'bn' ? 'গুগল সার্চ ও ফেসবুক/টুইটার কার্ডের জন্য মেটা ও ১২০০x৬৩০ ডাইনামিক ব্যানার।' : 'OpenGraph images (1200x630) for Facebook, WhatsApp & Google preview cards.' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'গ্লোবাল মেটা টাইটেল' : 'Default Meta Title' }}
                            </label>
                            <input
                                v-model="form.meta_title"
                                type="text"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                placeholder="শিখুন - আপনার এআই পড়ার টেবিল | SSC, HSC, Admission & Job Preparation"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'গ্লোবাল মেটা ডেসক্রিপশন' : 'Default Meta Description' }}
                            </label>
                            <textarea
                                v-model="form.meta_description"
                                rows="2"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                placeholder="শিখুন হলো বাংলাদেশের প্রথম এআই-চালিত স্মার্ট লার্নিং প্ল্যাটফর্ম..."
                            ></textarea>
                        </div>

                        <!-- 4 Dedicated OG Image Cards -->
                        <div class="pt-2">
                            <div class="mb-4 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">
                                {{ locale === 'bn' ? 'ক্যাটাগরি-ভিত্তিক সোশ্যাল শেয়ারিং ব্যানার (1200 x 630 px)' : 'Category-Specific OpenGraph Images (1200 x 630 px)' }}
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <!-- 1. Global OG -->
                                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4">
                                    <div class="font-semibold text-xs text-[var(--text)] mb-1">
                                        {{ locale === 'bn' ? '১. মূল ওয়েবসাইট সোশ্যাল ব্যানার' : '1. Global OG Image' }}
                                    </div>
                                    <div class="text-[11px] text-[var(--text-muted)] mb-3">হোমপেজ ও সাধারণ পেজের জন্য</div>
                                    <div class="aspect-video w-full overflow-hidden rounded-xl border border-[var(--border)] bg-[var(--surface)] flex items-center justify-center mb-3">
                                        <img v-if="seoPreview" :src="seoPreview" alt="SEO preview" class="h-full w-full object-cover" />
                                        <PhotoIcon v-else class="h-10 w-10 text-[var(--text-muted)]" />
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/png,image/jpeg,image/webp"
                                        @change="handleFileChange($event, 'seo_image', seoPreview, 'remove_seo_image')"
                                        class="block w-full text-xs text-[var(--text-muted)] file:mr-2 file:rounded-lg file:border-0 file:bg-stone-700 file:px-2.5 file:py-1 file:text-xs file:font-medium file:text-white"
                                    />
                                    <button
                                        v-if="seoPreview"
                                        type="button"
                                        @click="removeFile('seo_image', seoPreview, 'remove_seo_image')"
                                        class="mt-2 text-xs text-rose-500 hover:underline inline-flex items-center gap-1"
                                    >
                                        <TrashIcon class="h-3 w-3" /> {{ locale === 'bn' ? 'মুছে ফেলুন' : 'Remove' }}
                                    </button>
                                </div>

                                <!-- 2. Admission OG -->
                                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4">
                                    <div class="font-semibold text-xs text-[var(--text)] mb-1">
                                        {{ locale === 'bn' ? '২. ভর্তি বিজ্ঞপ্তি সোশ্যাল ব্যানার' : '2. Admission Notices OG' }}
                                    </div>
                                    <div class="text-[11px] text-[var(--text-muted)] mb-3">সকল ভার্সিটি সার্কুলার শেয়ারের জন্য</div>
                                    <div class="aspect-video w-full overflow-hidden rounded-xl border border-[var(--border)] bg-[var(--surface)] flex items-center justify-center mb-3">
                                        <img v-if="admissionSeoPreview" :src="admissionSeoPreview" alt="Admission preview" class="h-full w-full object-cover" />
                                        <PhotoIcon v-else class="h-10 w-10 text-[var(--text-muted)]" />
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/png,image/jpeg,image/webp"
                                        @change="handleFileChange($event, 'seo_admission_image', admissionSeoPreview, 'remove_seo_admission_image')"
                                        class="block w-full text-xs text-[var(--text-muted)] file:mr-2 file:rounded-lg file:border-0 file:bg-stone-700 file:px-2.5 file:py-1 file:text-xs file:font-medium file:text-white"
                                    />
                                    <button
                                        v-if="admissionSeoPreview"
                                        type="button"
                                        @click="removeFile('seo_admission_image', admissionSeoPreview, 'remove_seo_admission_image')"
                                        class="mt-2 text-xs text-rose-500 hover:underline inline-flex items-center gap-1"
                                    >
                                        <TrashIcon class="h-3 w-3" /> {{ locale === 'bn' ? 'মুছে ফেলুন' : 'Remove' }}
                                    </button>
                                </div>

                                <!-- 3. Jobs OG -->
                                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4">
                                    <div class="font-semibold text-xs text-[var(--text)] mb-1">
                                        {{ locale === 'bn' ? '৩. চাকরির খবর সোশ্যাল ব্যানার' : '3. Job Circulars OG' }}
                                    </div>
                                    <div class="text-[11px] text-[var(--text-muted)] mb-3">সরকারি ও বেসরকারি চাকরির জন্য</div>
                                    <div class="aspect-video w-full overflow-hidden rounded-xl border border-[var(--border)] bg-[var(--surface)] flex items-center justify-center mb-3">
                                        <img v-if="jobSeoPreview" :src="jobSeoPreview" alt="Job preview" class="h-full w-full object-cover" />
                                        <PhotoIcon v-else class="h-10 w-10 text-[var(--text-muted)]" />
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/png,image/jpeg,image/webp"
                                        @change="handleFileChange($event, 'seo_job_image', jobSeoPreview, 'remove_seo_job_image')"
                                        class="block w-full text-xs text-[var(--text-muted)] file:mr-2 file:rounded-lg file:border-0 file:bg-stone-700 file:px-2.5 file:py-1 file:text-xs file:font-medium file:text-white"
                                    />
                                    <button
                                        v-if="jobSeoPreview"
                                        type="button"
                                        @click="removeFile('seo_job_image', jobSeoPreview, 'remove_seo_job_image')"
                                        class="mt-2 text-xs text-rose-500 hover:underline inline-flex items-center gap-1"
                                    >
                                        <TrashIcon class="h-3 w-3" /> {{ locale === 'bn' ? 'মুছে ফেলুন' : 'Remove' }}
                                    </button>
                                </div>

                                <!-- 4. Blog OG -->
                                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4">
                                    <div class="font-semibold text-xs text-[var(--text)] mb-1">
                                        {{ locale === 'bn' ? '৪. ব্লগ ও আর্টিকেল সোশ্যাল ব্যানার' : '4. Blog & Articles OG' }}
                                    </div>
                                    <div class="text-[11px] text-[var(--text-muted)] mb-3">টিউটোরিয়াল ও ব্লগের জন্য</div>
                                    <div class="aspect-video w-full overflow-hidden rounded-xl border border-[var(--border)] bg-[var(--surface)] flex items-center justify-center mb-3">
                                        <img v-if="blogSeoPreview" :src="blogSeoPreview" alt="Blog preview" class="h-full w-full object-cover" />
                                        <PhotoIcon v-else class="h-10 w-10 text-[var(--text-muted)]" />
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/png,image/jpeg,image/webp"
                                        @change="handleFileChange($event, 'seo_blog_image', blogSeoPreview, 'remove_seo_blog_image')"
                                        class="block w-full text-xs text-[var(--text-muted)] file:mr-2 file:rounded-lg file:border-0 file:bg-stone-700 file:px-2.5 file:py-1 file:text-xs file:font-medium file:text-white"
                                    />
                                    <button
                                        v-if="blogSeoPreview"
                                        type="button"
                                        @click="removeFile('seo_blog_image', blogSeoPreview, 'remove_seo_blog_image')"
                                        class="mt-2 text-xs text-rose-500 hover:underline inline-flex items-center gap-1"
                                    >
                                        <TrashIcon class="h-3 w-3" /> {{ locale === 'bn' ? 'মুছে ফেলুন' : 'Remove' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Social Automation & AI (PagePilotBD, FB Auto-Publish) -->
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center justify-between border-b border-[var(--border)] pb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-500">
                                <BoltIcon class="h-6 w-6" />
                            </div>
                            <div>
                                <h2 class="font-heading text-lg font-bold text-[var(--text)]">
                                    {{ locale === 'bn' ? 'অটোমেশন ও ফেসবুক এআই শেয়ারিং' : 'Social Automation & AI Integration' }}
                                </h2>
                                <p class="text-xs text-[var(--text-muted)]">
                                    {{ locale === 'bn' ? 'PagePilotBD ইন্টিগ্রেশন, ফেসবুক পেজে অটো-পোস্টিং এবং এআই প্রম্পট।' : 'PagePilotBD webhook, automatic Facebook posting, and AI limits.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="syncKnowledge"
                                :disabled="syncingKnowledge"
                                class="btn btn-secondary text-xs px-3.5 py-1.5 font-bold inline-flex items-center gap-1.5"
                                title="ভর্তি, চাকরি ও ব্লগ পোস্টসমূহ পেজপাইলট নলেজ বেসে সিঙ্ক করুন"
                            >
                                <ArrowPathIcon class="h-3.5 w-3.5" :class="{ 'animate-spin': syncingKnowledge }" />
                                <span>{{ syncingKnowledge ? 'নলেজ সিঙ্ক হচ্ছে...' : 'নলেজ বেস সিঙ্ক' }}</span>
                            </button>
                            <button
                                type="button"
                                @click="testPagePilot"
                                :disabled="testingSocial"
                                class="btn btn-outline text-xs px-3.5 py-1.5 font-semibold inline-flex items-center gap-1.5"
                            >
                                <ArrowPathIcon v-if="testingSocial" class="h-3.5 w-3.5 animate-spin" />
                                <span>{{ testingSocial ? (locale === 'bn' ? 'টেস্ট চলছে...' : 'Testing...') : (locale === 'bn' ? 'টেস্ট পোস্ট পাঠান' : 'Test Webhook') }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 space-y-6">
                        <!-- Auto-publish Toggle -->
                        <div class="flex items-center justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/50 p-4">
                            <div>
                                <div class="font-bold text-sm text-[var(--text)]">
                                    {{ locale === 'bn' ? 'ফেসবুকে স্বয়ংক্রিয় প্রকাশনা (Auto-Publish)' : 'Auto-Publish to Facebook' }}
                                </div>
                                <div class="text-xs text-[var(--text-muted)] mt-0.5">
                                    {{ locale === 'bn' ? 'নতুন ভর্তি বিজ্ঞপ্তি বা চাকরির খবর প্রকাশ হলেই পেইজে স্বয়ংক্রিয়ভাবে শেয়ার হবে।' : 'Automatically trigger PagePilotBD webhook when new notice/job is approved.' }}
                                </div>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input v-model="form.pagepilot_auto_publish" type="checkbox" class="peer sr-only" />
                                <div class="peer h-6 w-11 rounded-full bg-stone-300 dark:bg-stone-700 after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-[var(--primary)] peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- PagePilot Webhook URL -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                    PagePilotBD Webhook URL
                                </label>
                                <input
                                    v-model="form.pagepilot_webhook_url"
                                    type="text"
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-xs text-[var(--text)] font-mono focus:border-[var(--primary)] focus:outline-none"
                                />
                            </div>

                            <!-- PagePilot API Key -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                    PagePilotBD API Token
                                </label>
                                <input
                                    v-model="form.pagepilot_api_key"
                                    type="password"
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-xs text-[var(--text)] font-mono focus:border-[var(--primary)] focus:outline-none"
                                />
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- AI Daily Limit -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                    {{ locale === 'bn' ? 'দৈনিক এআই সারসংক্ষেপ জেনারেশন লিমিট' : 'Daily AI Scraper Limit' }}
                                </label>
                                <input
                                    v-model.number="form.blog_daily_ai_limit"
                                    type="number"
                                    min="1"
                                    max="500"
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                />
                            </div>

                            <!-- Blog Credit Enabled -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                    {{ locale === 'bn' ? 'সোর্স ক্রেডিট প্রদর্শন' : 'Show Source Credit in Posts' }}
                                </label>
                                <select
                                    v-model="form.blog_credit_enabled"
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                >
                                    <option :value="true">{{ locale === 'bn' ? 'হ্যাঁ (সক্রিয়)' : 'Enabled' }}</option>
                                    <option :value="false">{{ locale === 'bn' ? 'না (নিষ্ক্রিয়)' : 'Disabled' }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- System Prompt for AI Assistant -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'সাপোর্ট বট ও স্টাডি অ্যাসিস্ট্যান্ট সিস্টেম প্রম্পট' : 'AI Study Assistant System Prompt' }}
                            </label>
                            <textarea
                                v-model="form.support_bot_system_prompt"
                                rows="3"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-xs text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                placeholder="You are Sikhun AI Desk Assistant..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: Referrals & Wallet Rewards -->
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center gap-3 border-b border-[var(--border)] pb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-500">
                            <GiftIcon class="h-6 w-6" />
                        </div>
                        <div>
                            <h2 class="font-heading text-lg font-bold text-[var(--text)]">
                                {{ locale === 'bn' ? 'রেফারেল ও ওয়ালেট বোনাস সেটিংস' : 'Referrals & Wallet Rewards' }}
                            </h2>
                            <p class="text-xs text-[var(--text-muted)]">
                                {{ locale === 'bn' ? 'শিক্ষার্থীদের রেফারেল বোনাস, মাসিক সীমা এবং রিওয়ার্ড টিয়ার।' : 'Rewards credited to student wallets when inviting peers.' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-6 sm:grid-cols-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'রেফারারের বোনাস (টাকা)' : 'Referrer Reward (BDT)' }}
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-2.5 text-sm font-bold text-[var(--text-muted)]">৳</span>
                                <input
                                    v-model.number="form.referrer_reward_amount"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] pl-8 pr-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'নতুন শিক্ষার্থীর বোনাস (টাকা)' : 'Referee Welcome (BDT)' }}
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-2.5 text-sm font-bold text-[var(--text-muted)]">৳</span>
                                <input
                                    v-model.number="form.referee_reward_amount"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] pl-8 pr-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                                {{ locale === 'bn' ? 'প্রতি মাসে সর্বোচ্চ রেফারেল' : 'Max Referrals / Month' }}
                            </label>
                            <input
                                v-model.number="form.max_referral_per_month"
                                type="number"
                                min="1"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- Sticky Footer Save Bar -->
                <div class="sticky bottom-4 z-20 flex items-center justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)]/95 p-4 backdrop-blur shadow-lg">
                    <div class="text-xs text-[var(--text-muted)] flex items-center gap-2">
                        <InformationCircleIcon class="h-4 w-4 text-[var(--primary)]" />
                        <span>{{ locale === 'bn' ? 'পরিবর্তনগুলো সাথে সাথে সাইট ও সোশ্যাল শেয়ার প্রিভিউতে কার্যকর হবে।' : 'Changes apply immediately across the website and social crawlers.' }}</span>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn btn-primary px-8 py-2.5 font-bold shadow-md inline-flex items-center gap-2"
                    >
                        <ArrowPathIcon v-if="form.processing" class="h-4 w-4 animate-spin" />
                        <span>{{ form.processing ? (locale === 'bn' ? 'সংরক্ষণ হচ্ছে...' : 'Saving...') : (locale === 'bn' ? 'পরিবর্তন সংরক্ষণ করুন' : 'Save Changes') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
