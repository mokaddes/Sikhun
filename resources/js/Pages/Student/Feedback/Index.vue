<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ feedback: Object });
const { t } = useI18n();
const form = useForm({ type: 'feedback', rating: '', message: '' });

function submit() {
    form.post('/feedback', { preserveScroll: true, onSuccess: () => form.reset('rating', 'message') });
}
</script>

<template>
    <Head :title="t('support.feedback_title')" />
    <StudentLayout>
        <div class="mx-auto max-w-4xl space-y-7">
            <header><h1 class="font-heading text-2xl font-extrabold">{{ t('support.feedback_title') }}</h1><p class="mt-1 text-sm text-[var(--text-muted)]">{{ t('support.feedback_subtitle') }}</p></header>
            <form @submit.prevent="submit" class="space-y-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <label class="block text-sm font-medium">{{ t('support.feedback_type') }}
                    <select v-model="form.type" class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-[var(--text)]">
                        <option value="feedback">{{ t('support.type_feedback') }}</option>
                        <option value="question_report">{{ t('support.type_question_report') }}</option>
                    </select>
                </label>
                <label class="block text-sm font-medium">{{ t('support.rating') }}
                    <select v-model="form.rating" class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-[var(--text)]">
                        <option value="">{{ t('support.rating_optional') }}</option>
                        <option v-for="rating in [1, 2, 3, 4, 5]" :key="rating" :value="rating">{{ rating }}</option>
                    </select>
                </label>
                <label class="block text-sm font-medium">{{ t('support.message') }}
                    <textarea v-model="form.message" rows="4" maxlength="5000" required class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-[var(--text)]" />
                    <span v-if="form.errors.message" class="text-xs text-red-500">{{ form.errors.message }}</span>
                </label>
                <button :disabled="form.processing" class="rounded-lg bg-[var(--primary)] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">{{ t('support.submit_feedback') }}</button>
            </form>
            <section class="space-y-3">
                <h2 class="font-heading text-lg font-bold">{{ t('support.your_feedback') }}</h2>
                <article v-for="item in feedback.data" :key="item.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
                    <div class="mb-2 flex items-center justify-between gap-2 text-xs text-[var(--text-muted)]"><span>{{ t(`support.type_${item.type}`) }}</span><span>{{ t(`support.feedback_status_${item.status}`) }}</span></div>
                    <p class="whitespace-pre-wrap text-sm">{{ item.message }}</p>
                </article>
                <div v-if="!feedback.data.length" class="py-8 text-center text-sm text-[var(--text-muted)]">{{ t('support.no_feedback') }}</div>
                <Pagination :links="feedback.links" />
            </section>
        </div>
    </StudentLayout>
</template>
