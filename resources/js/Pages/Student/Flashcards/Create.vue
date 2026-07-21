<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

defineProps({ books: Array });
const { t } = useI18n();

const form = useForm({ title: '', source_book_id: null, source_text: '', count: 15 });

function submit() {
    form.post('/flashcards');
}
</script>

<template>
    <Head :title="t('flashcards.new_set')" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('flashcards.new_set') }}</h1>
        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('flashcards.set_title') }}</label>
                <input v-model="form.title" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div v-if="books.length">
                <label class="block text-sm font-medium mb-1.5">{{ t('exams.source_book') }}</label>
                <select v-model="form.source_book_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                    <option :value="null">—</option>
                    <option v-for="b in books" :key="b.id" :value="b.id">{{ b.title }}</option>
                </select>
            </div>
            <div v-if="!form.source_book_id">
                <label class="block text-sm font-medium mb-1.5">{{ t('flashcards.source_text') }}</label>
                <textarea v-model="form.source_text" rows="5" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                <p v-if="form.errors.source_text" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.source_text }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('flashcards.count') }}</label>
                <input v-model="form.count" type="number" min="5" max="30" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <button type="submit" :disabled="form.processing" class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold disabled:opacity-60">
                {{ form.processing ? t('flashcards.generating') : t('flashcards.generate') }}
            </button>
        </form>
    </StudentLayout>
</template>
