<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';

const { t } = useI18n();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    type: 'hsc',
});

const types = computed(() => [
    { value: 'ssc', label: t('auth.type_ssc') },
    { value: 'hsc', label: t('auth.type_hsc') },
    { value: 'university', label: t('auth.type_university') },
    { value: 'job', label: t('auth.type_job') },
]);

function submit() {
    form.post('/register', { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>

<template>
    <Head :title="t('auth.register_title')" />
    <PublicLayout>
        <div class="max-w-md mx-auto px-5 py-16">
            <h1 class="font-heading text-3xl font-extrabold mb-2">{{ t('auth.register_title') }}</h1>
            <p class="text-[var(--text-muted)] mb-8">{{ t('auth.register_subtitle') }}</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('auth.full_name') }}</label>
                    <input v-model="form.name" type="text" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                    <p v-if="form.errors.name" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('common.email') }}</label>
                    <input v-model="form.email" type="email" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                    <p v-if="form.errors.email" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('auth.i_am_a') }}</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            v-for="tOpt in types" :key="tOpt.value" type="button"
                            @click="form.type = tOpt.value"
                            class="px-3 py-2.5 rounded-lg border text-sm font-medium transition-colors"
                            :class="form.type === tOpt.value
                                ? 'bg-[var(--primary)] border-[var(--primary)] text-white'
                                : 'bg-[var(--surface)] border-[var(--border)] text-[var(--text-muted)]'"
                        >
                            {{ tOpt.label }}
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('auth.password') }}</label>
                    <input v-model="form.password" type="password" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                    <p v-if="form.errors.password" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('auth.confirm_password') }}</label>
                    <input v-model="form.password_confirmation" type="password" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold transition-colors disabled:opacity-60">
                    {{ form.processing ? t('auth.submitting') : t('auth.sign_up') }}
                </button>
            </form>

            <p class="text-center text-sm text-[var(--text-muted)] mt-6">
                {{ t('auth.already_have_account') }} <Link href="/login" class="text-[var(--primary)] font-medium">{{ t('auth.sign_in') }}</Link>
            </p>
        </div>
    </PublicLayout>
</template>
