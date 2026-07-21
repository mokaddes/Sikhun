<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

defineProps({ sets: Array });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('flashcards.title')" />
    <StudentLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('flashcards.title') }}</h1>
            <Link href="/flashcards/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('flashcards.new_set') }}
            </Link>
        </div>

        <div v-if="sets.length" class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div v-for="set in sets" :key="set.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <Link :href="`/flashcards/${set.id}`" class="font-medium text-sm block mb-2">{{ set.title }}</Link>
                <div class="text-xs text-[var(--text-muted)] mb-3">{{ set.flashcards_count }} {{ t('flashcards.cards_count') }}</div>
                <ConfirmButton :href="`/flashcards/${set.id}`" method="delete" />
            </div>
        </div>
        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
            {{ t('flashcards.no_sets') }}
        </div>
    </StudentLayout>
</template>
