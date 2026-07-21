<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

defineProps({ schedules: Array });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('schedules.title')" />
    <StudentLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('schedules.title') }}</h1>
            <Link href="/schedules/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('schedules.new_schedule') }}
            </Link>
        </div>

        <div v-if="schedules.length" class="space-y-2">
            <Link v-for="s in schedules" :key="s.id" :href="`/schedules/${s.id}`" class="flex items-center justify-between rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
                <div>
                    <div class="font-medium text-sm">{{ t('schedules.exam_date') }}: {{ new Date(s.exam_date).toLocaleDateString() }}</div>
                    <div class="text-xs text-[var(--text-muted)]">{{ s.config?.subjects?.join(', ') }}</div>
                </div>
                <span class="text-xs px-2 py-0.5 rounded font-medium" :class="s.status === 'active' ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                    {{ s.status }}
                </span>
            </Link>
        </div>
        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
            {{ t('schedules.no_schedules') }}
        </div>
    </StudentLayout>
</template>
