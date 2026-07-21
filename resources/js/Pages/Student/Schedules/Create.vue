<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const { t } = useI18n();
const form = useForm({
    exam_date: '', subjects: '', weak_subjects: '', hours_per_day: 4, style: 'balanced', include_weekends: true,
});

function submit() {
    form.transform((data) => ({
        ...data,
        subjects: data.subjects.split(',').map((s) => s.trim()).filter(Boolean),
        weak_subjects: data.weak_subjects.split(',').map((s) => s.trim()).filter(Boolean),
    })).post('/schedules');
}
</script>

<template>
    <Head :title="t('schedules.new_schedule')" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('schedules.new_schedule') }}</h1>
        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('schedules.exam_date') }}</label>
                <input v-model="form.exam_date" type="date" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('schedules.subjects') }}</label>
                <input v-model="form.subjects" type="text" required placeholder="পদার্থবিজ্ঞান, রসায়ন, গণিত" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('schedules.weak_subjects') }}</label>
                <input v-model="form.weak_subjects" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('schedules.hours_per_day') }}</label>
                    <input v-model="form.hours_per_day" type="number" min="1" max="16" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('schedules.style') }}</label>
                    <select v-model="form.style" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option value="balanced">{{ t('schedules.style_balanced') }}</option>
                        <option value="intensive">{{ t('schedules.style_intensive') }}</option>
                        <option value="relaxed">{{ t('schedules.style_relaxed') }}</option>
                    </select>
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm"><input v-model="form.include_weekends" type="checkbox" class="rounded" /> {{ t('schedules.include_weekends') }}</label>
            <button type="submit" :disabled="form.processing" class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold disabled:opacity-60">
                {{ form.processing ? t('schedules.generating') : t('schedules.generate') }}
            </button>
        </form>
    </StudentLayout>
</template>
