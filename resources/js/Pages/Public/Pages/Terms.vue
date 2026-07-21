<script setup>
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

defineProps({ page: Object, seo: Object });
const { locale } = useI18n();
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <section class="max-w-3xl mx-auto px-5 pt-16 pb-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="w-10 h-10 rounded-lg bg-[var(--surface2)] flex items-center justify-center text-xl">📄</span>
                <h1 class="font-heading text-2xl md:text-3xl font-extrabold">{{ page.title }}</h1>
            </div>
            <p class="text-sm text-[var(--text-muted)]">
                {{ locale === 'en' ? 'Last updated: July 2026' : 'সর্বশেষ হালনাগাদ: জুলাই ২০২৬' }}
            </p>
        </section>

        <section class="max-w-3xl mx-auto px-5 pb-20">
            <div class="legal-content prose prose-lg dark:prose-invert max-w-none" v-html="page.content" />
        </section>
    </PublicLayout>
</template>

<style scoped>
/* Numbered section styling — matches the h3-per-clause structure written
   into this page's seeded content (see CustomPageSeeder). */
.legal-content :deep(h3) {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-top: 1px solid var(--border);
    padding-top: 1.5rem;
    margin-top: 2rem;
}
.legal-content :deep(h3:first-child) {
    border-top: none;
    padding-top: 0;
    margin-top: 0;
}
</style>
