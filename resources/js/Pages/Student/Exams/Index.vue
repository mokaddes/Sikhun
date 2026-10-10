<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';
import { ClipboardDocumentCheckIcon, PlusIcon, ArrowRightIcon, ClockIcon } from '@heroicons/vue/24/outline';

defineProps({ exams: Object });
const { t, locale } = useI18n();

const statusStyle = (status) => status === 'completed'
    ? 'bg-[var(--secondary)]/12 text-[var(--secondary)]'
    : status === 'in_progress'
        ? 'bg-[var(--primary)]/12 text-[var(--primary)]'
        : 'bg-[var(--text-muted)]/12 text-[var(--text-muted)]';
</script>

<template>
    <Head :title="t('exams.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <span class="eyebrow">{{ locale === 'en' ? 'Practice' : 'অনুশীলন' }}</span>
                <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ t('exams.title') }}</h1>
            </div>
            <Link href="/exams/create" class="btn btn-primary btn-sm">
                <PlusIcon class="h-4 w-4" /> {{ t('exams.new_exam') }}
            </Link>
        </header>

        <div v-if="exams.data.length" class="stagger space-y-3">
            <Link
                v-for="exam in exams.data"
                :key="exam.id"
                :href="exam.status === 'completed' ? `/exams/${exam.id}/result` : `/exams/${exam.id}`"
                class="card card-hover group flex items-center gap-4 rounded-2xl p-4"
            >
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[var(--secondary)]/10 text-[var(--secondary)]">
                    <ClipboardDocumentCheckIcon class="h-5 w-5" />
                </span>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-sm font-semibold">{{ exam.config?.type?.toUpperCase() }} · {{ exam.total }} {{ locale === 'en' ? 'questions' : 'প্রশ্ন' }}</div>
                    <div class="mt-0.5 flex items-center gap-2 text-xs text-[var(--text-muted)]">
                        <ClockIcon class="h-3.5 w-3.5" /> {{ new Date(exam.created_at).toLocaleDateString() }}
                        <span class="badge" :class="statusStyle(exam.status)">{{ exam.status.replace('_', ' ') }}</span>
                    </div>
                </div>
                <template v-if="exam.status === 'completed'">
                    <span class="font-heading text-lg font-bold" :class="exam.percentage >= 70 ? 'text-[var(--secondary)]' : exam.percentage >= 40 ? 'text-[var(--primary)]' : 'text-[var(--accent)]'">{{ exam.percentage }}%</span>
                </template>
                <ArrowRightIcon class="h-4 w-4 shrink-0 text-[var(--text-muted)] transition-transform group-hover:translate-x-1" />
            </Link>
        </div>
        <div v-else class="card flex flex-col items-center gap-3 rounded-2xl border-dashed p-12 text-center text-[var(--text-muted)]">
            <ClipboardDocumentCheckIcon class="h-10 w-10 opacity-40" />
            {{ t('exams.no_exams') }}
            <Link href="/exams/create" class="btn btn-primary btn-sm mt-1">{{ t('exams.new_exam') }}</Link>
        </div>
        <Pagination :links="exams.links" />
    </StudentLayout>
</template>
