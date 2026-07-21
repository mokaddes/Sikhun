<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';

const { t } = useI18n();
const form = useForm({ name: '', email: '', subject: '', message: '' });

function submit() {
    form.post('/contact', { preserveScroll: true, onSuccess: () => form.reset('subject', 'message') });
}
</script>

<template>
    <Head :title="t('nav.contact')" />
    <PublicLayout>
        <div class="max-w-xl mx-auto px-5 py-16">
            <h1 class="font-heading text-3xl font-extrabold mb-2">{{ t('contact_page.title') }}</h1>
            <p class="text-[var(--text-muted)] mb-8">{{ t('contact_page.subtitle') }}</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('common.name') }}</label>
                        <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('common.email') }}</label>
                        <input v-model="form.email" type="email" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)]" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('contact_page.subject') }}</label>
                    <input v-model="form.subject" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('contact_page.message') }}</label>
                    <textarea v-model="form.message" rows="6" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)]"></textarea>
                    <p v-if="form.errors.message" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.message }}</p>
                </div>
                <button type="submit" :disabled="form.processing" class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold disabled:opacity-60">
                    {{ form.processing ? t('common.saving') : t('contact_page.send') }}
                </button>
            </form>
        </div>
    </PublicLayout>
</template>
