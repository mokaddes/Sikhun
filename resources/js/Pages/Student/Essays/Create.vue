<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const { t } = useI18n();
const form = useForm({ grading_type: 'general', essay_text: '' });

function submit() {
    form.post('/essays');
}
</script>

<template>
    <Head :title="t('essays.new_essay')" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('essays.new_essay') }}</h1>
        <form @submit.prevent="submit" class="max-w-2xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('essays.grading_type') }}</label>
                <select v-model="form.grading_type" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                    <option value="hsc_bangla">HSC Bengali Essay</option>
                    <option value="hsc_english">HSC English Essay</option>
                    <option value="general">General Writing</option>
                    <option value="custom_rubric">Custom Rubric</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('essays.essay_text') }}</label>
                <textarea v-model="form.essay_text" rows="12" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                <p v-if="form.errors.essay_text" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.essay_text }}</p>
            </div>
            <button type="submit" :disabled="form.processing" class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold disabled:opacity-60">
                {{ form.processing ? t('essays.grading') : t('essays.submit') }}
            </button>
        </form>
    </StudentLayout>
</template>
