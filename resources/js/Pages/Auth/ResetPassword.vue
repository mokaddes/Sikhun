<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
    scope: { type: String, default: 'student' },
});

const { t } = useI18n();
const isAdmin = props.scope === 'admin';
const endpoint = isAdmin ? '/admin/reset-password' : '/reset-password';
const loginHref = isAdmin ? '/admin/login' : '/login';

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(endpoint, { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>

<template>
    <Head :title="t('auth.reset_title')" />
    <PublicLayout>
        <div class="mx-auto max-w-md px-5 py-16">
            <h1 class="font-heading text-3xl font-extrabold mb-2">{{ t('auth.reset_title') }}</h1>
            <p class="text-[var(--text-muted)] mb-8">{{ t('auth.reset_subtitle') }}</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label for="reset-email" class="block text-sm font-medium mb-1.5">{{ t('common.email') }}</label>
                    <input
                        id="reset-email"
                        name="email"
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="username"
                        :aria-invalid="!!form.errors.email"
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                    />
                </div>

                <div>
                    <label for="reset-password" class="block text-sm font-medium mb-1.5">{{ t('auth.new_password') }}</label>
                    <input
                        id="reset-password"
                        name="password"
                        v-model="form.password"
                        type="password"
                        required
                        autofocus
                        minlength="8"
                        autocomplete="new-password"
                        :aria-invalid="!!form.errors.password"
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                    />
                </div>

                <div>
                    <label for="reset-password-confirmation" class="block text-sm font-medium mb-1.5">{{ t('auth.confirm_password') }}</label>
                    <input
                        id="reset-password-confirmation"
                        name="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                    />
                </div>

                <p v-if="form.errors.email" class="text-[var(--accent)] text-sm">{{ form.errors.email }}</p>
                <p v-if="form.errors.password" class="text-[var(--accent)] text-sm">{{ form.errors.password }}</p>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold transition-colors disabled:opacity-60"
                >
                    {{ form.processing ? t('auth.submitting') : t('auth.reset_password') }}
                </button>
            </form>

            <p class="text-center text-sm text-[var(--text-muted)] mt-6">
                <Link :href="loginHref" class="text-[var(--primary)] font-medium">{{ t('auth.back_to_login') }}</Link>
            </p>
        </div>
    </PublicLayout>
</template>
