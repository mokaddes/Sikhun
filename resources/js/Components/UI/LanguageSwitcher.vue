<script setup>
import { useI18n } from '@/i18n';

const { locale, switchLocale } = useI18n();
</script>

<template>
    <button
        type="button"
        @click="switchLocale(locale === 'bn' ? 'en' : 'bn')"
        :title="locale === 'bn' ? 'Switch to English' : 'বাংলায় দেখুন'"
        class="flex items-center justify-center h-9 px-3 rounded-lg
               bg-[var(--surface2)] hover:bg-[var(--border)]
               border border-[var(--border)]
               text-[var(--text)] text-sm font-semibold transition-colors duration-150"
    >
        {{ locale === 'bn' ? 'EN' : 'বাং' }}
    </button>
</template>
