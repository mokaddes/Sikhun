<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import FlashcardFlip from '@/Components/Flashcard/FlashcardFlip.vue';
import { useI18n } from '@/i18n';
import { ArrowPathIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ set: Object });
const { t, locale } = useI18n();

const currentIndex = ref(0);
const currentCard = computed(() => props.set.flashcards[currentIndex.value]);
const done = computed(() => currentIndex.value >= props.set.flashcards.length);
const generationStatus = computed(() => props.set.generation_status ?? 'completed');
const retrying = ref(false);
let pollInterval;

function poll() {
    pollInterval = setInterval(() => {
        router.reload({ only: ['set'], preserveScroll: true });
    }, 2500);
}

function stopPollingIfReady(status) {
    if (status !== 'generating' && pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
}

onMounted(() => {
    if (generationStatus.value === 'generating') poll();
});
onBeforeUnmount(() => clearInterval(pollInterval));
watch(generationStatus, stopPollingIfReady);

function retryGeneration() {
    retrying.value = true;
    router.post(`/flashcards/${props.set.id}/retry`, {}, {
        onFinish: () => { retrying.value = false; },
    });
}

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

        <div v-if="generationStatus === 'generating'" class="text-center text-[var(--text-muted)] py-12">
            {{ t('flashcards.generating') }}
        </div>
        <div v-else-if="generationStatus === 'failed'" class="mx-auto max-w-lg rounded-2xl border border-rose-500/30 bg-rose-500/10 p-8 text-center">
            <p class="font-semibold text-rose-600 dark:text-rose-400">{{ locale === 'en' ? 'Flashcard generation failed' : 'ফ্ল্যাশকার্ড তৈরি করা যায়নি' }}</p>
            <p class="mt-2 text-sm text-[var(--text-muted)]">{{ locale === 'en' ? 'Your source and settings are saved. You can retry without submitting them again.' : 'আপনার উৎস ও সেটিংস সংরক্ষিত আছে। আবার জমা না দিয়েই চেষ্টা করতে পারবেন।' }}</p>
            <button type="button" :disabled="retrying" class="btn btn-primary mt-5 inline-flex items-center gap-2 disabled:opacity-60" @click="retryGeneration">
                <ArrowPathIcon class="h-4 w-4" :class="retrying ? 'animate-spin' : ''" />
                {{ retrying ? (locale === 'en' ? 'Retrying…' : 'আবার চেষ্টা হচ্ছে…') : (locale === 'en' ? 'Retry generation' : 'আবার তৈরি করুন') }}
            </button>
        </div>
        <div v-else-if="!set.flashcards.length" class="text-center text-[var(--text-muted)] py-12">
            {{ locale === 'en' ? 'No flashcards were generated.' : 'কোনো ফ্ল্যাশকার্ড তৈরি হয়নি।' }}
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
