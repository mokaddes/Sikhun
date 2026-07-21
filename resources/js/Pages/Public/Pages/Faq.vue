<script setup>
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

defineProps({ page: Object, seo: Object });
const { t } = useI18n();
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <section class="max-w-3xl mx-auto px-5 pt-16 pb-10 text-center">
            <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-[var(--accent)] to-[var(--primary)] flex items-center justify-center text-3xl">❓</div>
            <h1 class="font-heading text-3xl md:text-4xl font-extrabold mb-3">{{ page.title }}</h1>
        </section>

        <!--
            The DB content is a series of <details><summary>Question</summary>
            <p>Answer</p></details> blocks (see the rich text editor in
            /admin/pages) — plain semantic HTML, natively collapsible with
            zero JS, styled below into a proper accordion look.
        -->
        <section class="max-w-2xl mx-auto px-5 pb-20">
            <div class="faq-accordion space-y-3" v-html="page.content" />
        </section>
    </PublicLayout>
</template>

<style scoped>
.faq-accordion :deep(details) {
    border: 1px solid var(--border);
    background: var(--surface);
    border-radius: 0.75rem;
    padding: 1rem 1.25rem;
}
.faq-accordion :deep(summary) {
    cursor: pointer;
    font-weight: 600;
    list-style: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.faq-accordion :deep(summary::-webkit-details-marker) {
    display: none;
}
.faq-accordion :deep(summary)::after {
    content: '+';
    font-size: 1.25rem;
    color: var(--primary);
    transition: transform 0.15s ease;
    margin-left: 1rem;
    flex-shrink: 0;
}
.faq-accordion :deep(details[open] summary)::after {
    transform: rotate(45deg);
}
.faq-accordion :deep(details p) {
    margin-top: 0.75rem;
    color: var(--text-muted);
    line-height: 1.6;
}
</style>
