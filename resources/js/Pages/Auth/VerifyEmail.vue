<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';

defineProps({ email: { type: String, default: '' } });

const { t } = useI18n();
const form = useForm({});

function resend() {
    form.post('/email/verification-notification', { preserveScroll: true });
}
</script>

<template>
    <Head :title="t('auth.verify_title')" />
    <PublicLayout>
        <div class="mx-auto max-w-md px-5 py-16">
            <h1 class="font-heading text-3xl font-extrabold mb-2">{{ t('auth.verify_title') }}</h1>
            <p class="text-[var(--text-muted)] mb-6">{{ t('auth.verify_subtitle') }}</p>

            <p class="mb-6 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-4 py-3 text-sm">
                <span class="text-[var(--text-muted)]">{{ t('common.email') }}:</span>
                <strong class="ml-1">{{ email }}</strong>
            </p>

            <button
                type="button"
                :disabled="form.processing"
                class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold transition-colors disabled:opacity-60"
                @click="resend"
            >
                {{ form.processing ? t('auth.submitting') : t('auth.resend_verification') }}
            </button>

            <p class="text-center text-sm text-[var(--text-muted)] mt-6">
                <Link href="/dashboard" class="text-[var(--primary)] font-medium">{{ t('auth.skip_for_now') }}</Link>
            </p>
        </div>
    </PublicLayout>
</template>
