<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ books: Array });
const { t } = useI18n();

const form = useForm({
    source_type: 'topic',
    source_book_id: null,
    source_text: '',
    type: 'mcq',
    count: 10,
    duration: 20,
    mode: 'practice',
});

function submit() {
    form.post('/exams');
}
</script>

<template>
    <Head :title="t('exams.new_exam')" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('exams.new_exam') }}</h1>

        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('exams.source') }}</label>
                <select v-model="form.source_type" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                    <option value="topic">{{ t('exams.source_topic') }}</option>
                    <option value="paragraph">{{ t('exams.source_paragraph') }}</option>
                    <option value="book" :disabled="!books.length">{{ t('exams.source_book') }}</option>
                </select>
            </div>

            <div v-if="form.source_type === 'book'">
                <select v-model="form.source_book_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                    <option :value="null">—</option>
                    <option v-for="b in books" :key="b.id" :value="b.id">{{ b.title }}</option>
                </select>
            </div>
            <div v-else>
                <textarea v-model="form.source_text" rows="4" :placeholder="form.source_type === 'topic' ? 'e.g. নিউটনের গতিসূত্র' : ''"
                    class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                <p v-if="form.errors.source_text" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.source_text }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('exams.question_type') }}</label>
                    <select v-model="form.type" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option value="mcq">MCQ</option><option value="true_false">True/False</option>
                        <option value="short">Short Answer</option><option value="fill_blank">Fill in the Blank</option>
                        <option value="cq">Creative Question</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('exams.count') }}</label>
                    <input v-model="form.count" type="number" min="5" max="50" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('exams.duration') }}</label>
                    <select v-model="form.duration" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option :value="0">{{ t('exams.no_limit') }}</option>
                        <option v-for="d in [10, 15, 20, 30, 45, 60, 90, 120]" :key="d" :value="d">{{ d }} min</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('exams.mode') }}</label>
                    <select v-model="form.mode" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option value="practice">{{ t('exams.mode_practice') }}</option>
                        <option value="exam">{{ t('exams.mode_exam') }}</option>
                    </select>
                </div>
            </div>

            <button type="submit" :disabled="form.processing" class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold disabled:opacity-60">
                {{ form.processing ? t('exams.generating') : t('exams.generate') }}
            </button>
        </form>
    </StudentLayout>
</template>
