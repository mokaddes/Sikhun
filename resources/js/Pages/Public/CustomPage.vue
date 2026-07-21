<script setup>
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';

const props = defineProps({ page: Object, seo: Object });
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <div class="max-w-3xl mx-auto px-5 py-16">
            <h1 class="font-heading text-3xl font-extrabold mb-6">{{ page.title }}</h1>
            <div class="prose prose-lg max-w-none text-[var(--text)]" v-html="page.content" />
        </div>
    </PublicLayout>
</template>
