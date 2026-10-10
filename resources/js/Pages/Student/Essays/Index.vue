<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';
import { PencilSquareIcon, PlusIcon, ArrowRightIcon } from '@heroicons/vue/24/outline';

defineProps({ essays: Object });
const { t, locale } = useI18n();
</script>

<template>
    <Head :title="t('essays.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <span class="eyebrow">{{ locale === 'en' ? 'Practice' : 'অনুশীলন' }}</span>
                <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ t('essays.title') }}</h1>
            </div>
            <Link href="/essays/create" class="btn btn-primary btn-sm">
                <PlusIcon class="h-4 w-4" /> {{ t('essays.new_essay') }}
            </Link>
        </header>

        <div v-if="essays.data.length" class="stagger space-y-3">
            <Link v-for="e in essays.data" :key="e.id" :href="`/essays/${e.id}`" class="card card-hover group flex items-center gap-4 rounded-2xl p-4">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[var(--accent)]/10 text-[var(--accent)]">
                    <PencilSquareIcon class="h-5 w-5" />
                </span>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-sm font-semibold">{{ e.grading_type.replace('_', ' ').toUpperCase() }}</div>
                    <div class="mt-0.5 text-xs text-[var(--text-muted)]">{{ new Date(e.created_at).toLocaleDateString() }} · {{ e.status }}</div>
                </div>
                <span v-if="e.result" class="font-heading font-bold">{{ e.result.total_score }}<span class="text-sm text-[var(--text-muted)]">/{{ e.result.max_score }}</span></span>
                <ArrowRightIcon class="h-4 w-4 shrink-0 text-[var(--text-muted)] transition-transform group-hover:translate-x-1" />
            </Link>
        </div>
        <div v-else class="card flex flex-col items-center gap-3 rounded-2xl border-dashed p-12 text-center text-[var(--text-muted)]">
            <PencilSquareIcon class="h-10 w-10 opacity-40" />
            {{ t('essays.no_essays') }}
            <Link href="/essays/create" class="btn btn-primary btn-sm mt-1">{{ t('essays.new_essay') }}</Link>
        </div>
        <Pagination :links="essays.links" />
    </StudentLayout>
</template>
