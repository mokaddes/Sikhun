<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import FlashcardFlip from '@/Components/Flashcard/FlashcardFlip.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ set: Object });
const { t } = useI18n();

const currentIndex = ref(0);
const currentCard = computed(() => props.set.flashcards[currentIndex.value]);
const done = computed(() => currentIndex.value >= props.set.flashcards.length);

function handleReview({ card, result }) {
    router.post(`/flashcards/${props.set.id}/cards/${card.id}/review`, { result }, {
        preserveScroll: true,
        onSuccess: () => currentIndex.value++,
    });
}
</script>

<template>
    <Head :title="set.title" />
    <StudentLayout>
        <div class="flex items-center justify-between mb-6 max-w-md mx-auto">
            <h1 class="font-heading text-xl font-bold">{{ set.title }}</h1>
            <a :href="`/flashcards/${set.id}/pdf`" class="text-sm text-[var(--primary)] hover:underline">{{ t('flashcards.download_pdf') }}</a>
        </div>

        <div v-if="!set.flashcards.length" class="text-center text-[var(--text-muted)] py-12">
            {{ t('flashcards.generating') }}
        </div>
        <div v-else-if="done" class="text-center py-12">
            <div class="text-lg font-semibold mb-2">🎉 {{ set.flashcards.length }} / {{ set.flashcards.length }}</div>
            <button @click="currentIndex = 0" class="text-[var(--primary)] text-sm font-medium">Review again</button>
        </div>
        <div v-else>
            <div class="text-center text-sm text-[var(--text-muted)] mb-4">{{ currentIndex + 1 }} / {{ set.flashcards.length }}</div>
            <FlashcardFlip :key="currentCard.id" :card="currentCard" @review="handleReview" />
        </div>
    </StudentLayout>
</template>
