<script setup>
import { computed } from 'vue';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

defineProps({ page: Object, seo: Object });
const { t, locale } = useI18n();

const stats = computed(() => [
    { value: '৭+', label: locale.value === 'en' ? 'AI Features' : 'AI ফিচার' },
    { value: '৪', label: locale.value === 'en' ? 'Student Levels' : 'শিক্ষার্থীর ধরন' },
    { value: 'EN/বাং', label: locale.value === 'en' ? 'Bilingual Platform' : 'দ্বিভাষিক প্ল্যাটফর্ম' },
    { value: '২৪/৭', label: locale.value === 'en' ? 'AI Support' : 'AI সাপোর্ট' },
]);
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <!-- Hero -->
        <section class="relative overflow-hidden border-b border-[var(--border)]">
            <div class="absolute inset-0 bg-gradient-to-br from-[var(--primary)]/10 via-transparent to-[var(--secondary)]/10 pointer-events-none" />
            <div class="max-w-4xl mx-auto px-5 pt-16 pb-14 text-center relative">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-[var(--primary)] to-[var(--secondary)] flex items-center justify-center text-3xl">
                    🎓
                </div>
                <h1 class="font-heading text-3xl md:text-4xl font-extrabold mb-3">{{ page.title }}</h1>
                <p class="text-[var(--text-muted)] max-w-xl mx-auto">
                    {{ locale === 'en' ? 'Built in Bangladesh, for Bangladeshi students.' : 'বাংলাদেশে তৈরি, বাংলাদেশের শিক্ষার্থীদের জন্য।' }}
                </p>
            </div>
        </section>

        <!-- Stats strip -->
        <section class="max-w-4xl mx-auto px-5 -mt-1 py-10 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="s in stats" :key="s.label" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5 text-center">
                <div class="font-heading text-2xl font-extrabold text-[var(--primary)] mb-1">{{ s.value }}</div>
                <div class="text-xs text-[var(--text-muted)]">{{ s.label }}</div>
            </div>
        </section>

        <!-- Rich content from the database, styled by @tailwindcss/typography -->
        <section class="max-w-3xl mx-auto px-5 pb-20">
            <div class="prose prose-lg dark:prose-invert max-w-none" v-html="page.content" />
        </section>
    </PublicLayout>
</template>
