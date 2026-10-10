<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { CalendarDaysIcon, PlusIcon, ArrowRightIcon } from '@heroicons/vue/24/outline';

defineProps({ schedules: Array });
const { t, locale } = useI18n();
</script>

<template>
    <Head :title="t('schedules.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <span class="eyebrow">{{ locale === 'en' ? 'Practice' : 'অনুশীলন' }}</span>
                <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ t('schedules.title') }}</h1>
            </div>
            <Link href="/schedules/create" class="btn btn-primary btn-sm">
                <PlusIcon class="h-4 w-4" /> {{ t('schedules.new_schedule') }}
            </Link>
        </header>

        <div v-if="schedules.length" class="stagger space-y-3">
            <Link v-for="s in schedules" :key="s.id" :href="`/schedules/${s.id}`" class="card card-hover group flex items-center gap-4 rounded-2xl p-4">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[var(--tertiary)]/10 text-[var(--tertiary)]">
                    <CalendarDaysIcon class="h-5 w-5" />
                </span>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-sm font-semibold">{{ t('schedules.exam_date') }}: {{ new Date(s.exam_date).toLocaleDateString() }}</div>
                    <div class="mt-0.5 truncate text-xs text-[var(--text-muted)]">{{ s.config?.subjects?.join(', ') }}</div>
                </div>
                <span class="badge shrink-0" :class="s.status === 'active' ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                    {{ s.status }}
                </span>
                <ArrowRightIcon class="h-4 w-4 shrink-0 text-[var(--text-muted)] transition-transform group-hover:translate-x-1" />
            </Link>
        </div>
        <div v-else class="card flex flex-col items-center gap-3 rounded-2xl border-dashed p-12 text-center text-[var(--text-muted)]">
            <CalendarDaysIcon class="h-10 w-10 opacity-40" />
            {{ t('schedules.no_schedules') }}
            <Link href="/schedules/create" class="btn btn-primary btn-sm mt-1">{{ t('schedules.new_schedule') }}</Link>
        </div>
    </StudentLayout>
</template>
