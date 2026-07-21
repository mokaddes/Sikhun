<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

defineProps({ exams: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('exams.title')" />
    <StudentLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('exams.title') }}</h1>
            <Link href="/exams/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('exams.new_exam') }}
            </Link>
        </div>

        <div v-if="exams.data.length" class="space-y-2">
            <Link v-for="exam in exams.data" :key="exam.id" :href="exam.status === 'completed' ? `/exams/${exam.id}/result` : `/exams/${exam.id}`"
                class="flex items-center justify-between rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
                <div>
                    <div class="font-medium text-sm">{{ exam.config?.type?.toUpperCase() }} · {{ exam.total }} questions</div>
                    <div class="text-xs text-[var(--text-muted)]">{{ new Date(exam.created_at).toLocaleDateString() }} · {{ exam.status }}</div>
                </div>
                <div v-if="exam.status === 'completed'" class="font-heading font-bold">{{ exam.score }}/{{ exam.total }}</div>
            </Link>
        </div>
        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
            {{ t('exams.no_exams') }}
        </div>
        <Pagination :links="exams.links" />
    </StudentLayout>
</template>
