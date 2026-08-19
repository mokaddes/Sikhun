<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import JsonLd from './JsonLd.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    seo: { type: Object, required: true },
});

const { locale } = useI18n();
const site = usePage().props.site;

// Per-page og:image wins; otherwise fall back to the admin-configured
// social share image so every page still emits a shareable image.
const ogImage = computed(() => props.seo.og_image || site?.seo_image_url || null);
const siteName = computed(() => site?.name || 'Sikhun.com');
</script>

<template>
    <Head>
        <title>{{ seo.title }}</title>
        <meta name="description" :content="seo.description" />
        <meta v-if="seo.keywords" name="keywords" :content="seo.keywords" />
        <link rel="canonical" :href="seo.canonical" />

        <!-- Open Graph -->
        <meta property="og:title" :content="seo.title" />
        <meta property="og:description" :content="seo.description" />
        <meta v-if="ogImage" property="og:image" :content="ogImage" />
        <meta property="og:url" :content="seo.canonical" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" :content="siteName" />
        <meta property="og:locale" :content="locale === 'bn' ? 'bn_BD' : 'en_US'" />

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="seo.title" />
        <meta name="twitter:description" :content="seo.description" />
        <meta v-if="ogImage" name="twitter:image" :content="ogImage" />

        <!-- Bilingual site: point crawlers at both language versions -->
        <link rel="alternate" hreflang="bn" :href="seo.canonical" />
        <link rel="alternate" hreflang="en" :href="seo.canonical" />
        <link rel="alternate" hreflang="x-default" :href="seo.canonical" />
    </Head>

    <JsonLd v-if="seo.json_ld" :data="seo.json_ld" />
    <JsonLd v-if="seo.breadcrumb" :data="seo.breadcrumb" />
</template>
