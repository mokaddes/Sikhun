<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';
import { RectangleStackIcon, PlusIcon } from '@heroicons/vue/24/outline';

defineProps({ sets: Array });
const { t, locale } = useI18n();
</script>

<template>
    <Head :title="t('flashcards.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <span class="eyebrow">{{ locale === 'en' ? 'Practice' : 'অনুশীলন' }}</span>
                <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ t('flashcards.title') }}</h1>
            </div>
            <Link href="/flashcards/create" class="btn btn-primary btn-sm">
                <PlusIcon class="h-4 w-4" /> {{ t('flashcards.new_set') }}
            </Link>
        </header>

        <div v-if="sets.length" class="stagger grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="set in sets" :key="set.id" class="card card-hover group flex flex-col rounded-2xl p-5">
                <span class="mb-3 grid h-11 w-11 place-items-center rounded-xl bg-[var(--tertiary)]/10 text-[var(--tertiary)]">
                    <RectangleStackIcon class="h-5 w-5" />
                </span>
                <Link :href="`/flashcards/${set.id}`" class="font-semibold leading-snug transition-colors group-hover:text-[var(--secondary)]">{{ set.title }}</Link>
                <div class="mt-1 mb-4 text-xs text-[var(--text-muted)]">{{ set.flashcards_count }} {{ t('flashcards.cards_count') }}</div>
                <div class="mt-auto">
                    <ConfirmButton :href="`/flashcards/${set.id}`" method="delete" />
                </div>
            </div>
        </div>
        <div v-else class="card flex flex-col items-center gap-3 rounded-2xl border-dashed p-12 text-center text-[var(--text-muted)]">
            <RectangleStackIcon class="h-10 w-10 opacity-40" />
            {{ t('flashcards.no_sets') }}
            <Link href="/flashcards/create" class="btn btn-primary btn-sm mt-1">{{ t('flashcards.new_set') }}</Link>
        </div>
    </StudentLayout>
</template>
