<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ scope: { type: String, default: 'student' } });
const { t } = useI18n();
const isAdmin = props.scope === 'admin';

const form = useForm({ email: '' });
const endpoint = isAdmin ? '/admin/forgot-password' : '/forgot-password';
const loginHref = isAdmin ? '/admin/login' : '/login';

function submit() {
    form.post(endpoint, { preserveScroll: true });
}
</script>

<template>
    <Head :title="t('auth.forgot_title')" />
    <PublicLayout>
        <div class="mx-auto max-w-md px-5 py-16">
            <h1 class="font-heading text-3xl font-extrabold mb-2">{{ t('auth.forgot_title') }}</h1>
            <p class="text-[var(--text-muted)] mb-8">{{ t('auth.forgot_subtitle') }}</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label for="forgot-email" class="block text-sm font-medium mb-1.5">{{ t('common.email') }}</label>
                    <input
                        id="forgot-email"
                        name="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        :aria-invalid="!!form.errors.email"
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                    />
                    <p v-if="form.errors.email" class="text-[var(--accent)] text-sm mt-1.5">{{ form.errors.email }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold transition-colors disabled:opacity-60"
                >
                    {{ form.processing ? t('auth.submitting') : t('auth.send_reset_link') }}
                </button>
            </form>

            <p class="text-center text-sm text-[var(--text-muted)] mt-6">
                <Link :href="loginHref" class="text-[var(--primary)] font-medium">{{ t('auth.back_to_login') }}</Link>
            </p>
        </div>
    </PublicLayout>
</template>
