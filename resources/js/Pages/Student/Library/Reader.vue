<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import FlipReader from '@/Components/Reader/FlipReader.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ book: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="book.title" />
    <StudentLayout>
        <Link :href="`/library/${book.slug}`" class="text-sm text-[var(--text-muted)] hover:text-[var(--text)] mb-4 inline-block">
            ← {{ t('reader.back_to_book') }}
        </Link>
        <h1 class="font-heading text-xl font-bold mb-6">{{ book.title }}</h1>

        <FlipReader :book-id="book.id" :total-pages="book.total_pages || 1" />
    </StudentLayout>
</template>
