<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';

const { t } = useI18n();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', { onFinish: () => form.reset('password') });
}
</script>

<template>
    <Head :title="t('auth.login_title')" />
    <PublicLayout>
        <div class="max-w-md mx-auto px-5 py-16">
            <h1 class="font-heading text-3xl font-extrabold mb-2">{{ t('auth.login_title') }}</h1>
            <p class="text-[var(--text-muted)] mb-8">{{ t('auth.login_subtitle') }}</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('common.email') }}</label>
                    <input v-model="form.email" type="email" required autofocus
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('auth.password') }}</label>
                    <input v-model="form.password" type="password" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                </div>

                <p v-if="form.errors.email" class="text-[var(--accent)] text-sm">{{ form.errors.email }}</p>

                <label class="flex items-center gap-2 text-sm text-[var(--text-muted)]">
                    <input v-model="form.remember" type="checkbox" class="rounded" />
                    {{ t('auth.remember_me') }}
                </label>

                <button type="submit" :disabled="form.processing"
                    class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold transition-colors disabled:opacity-60">
                    {{ form.processing ? t('auth.submitting') : t('auth.sign_in') }}
                </button>
            </form>

            <p class="text-center text-sm text-[var(--text-muted)] mt-6">
                {{ t('auth.no_account') }} <Link href="/register" class="text-[var(--primary)] font-medium">{{ t('auth.sign_up') }}</Link>
            </p>
        </div>
    </PublicLayout>
</template>
