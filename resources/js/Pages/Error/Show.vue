<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

const props = defineProps({ status: Number });
const { t } = useI18n();

const messages = {
    403: { title: '403', body: t('errors.forbidden') },
    404: { title: '404', body: t('errors.not_found') },
    419: { title: '419', body: t('errors.expired') },
    429: { title: '429', body: t('errors.too_many') },
    500: { title: '500', body: t('errors.server') },
    503: { title: '503', body: t('errors.unavailable') },
};

const message = messages[props.status] ?? messages[500];
</script>

<template>
    <Head :title="message.title" />
    <div class="min-h-screen flex items-center justify-center bg-[var(--bg)] text-[var(--text)] px-5">
        <div class="text-center max-w-sm">
            <div class="font-heading text-6xl font-extrabold text-[var(--primary)] mb-4">{{ message.title }}</div>
            <p class="text-[var(--text-muted)] mb-8">{{ message.body }}</p>
            <Link href="/" class="inline-flex items-center px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold">
                {{ t('errors.go_home') }}
            </Link>
        </div>
    </div>
</template>
