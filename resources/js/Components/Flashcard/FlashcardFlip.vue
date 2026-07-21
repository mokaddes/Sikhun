<script setup>
import { ref } from 'vue';
import { useI18n } from '@/i18n';

const props = defineProps({ card: Object });
const emit = defineEmits(['review']);
const { t } = useI18n();

const flipped = ref(false);

function review(result) {
    emit('review', { card: props.card, result });
    flipped.value = false;
}
</script>

<template>
    <div class="flex flex-col items-center">
        <div class="relative w-full max-w-md h-64 cursor-pointer" style="perspective: 1000px" @click="flipped = !flipped">
            <div class="relative w-full h-full transition-transform duration-500" :style="{ transformStyle: 'preserve-3d', transform: flipped ? 'rotateY(180deg)' : 'none' }">
                <div class="absolute inset-0 rounded-xl border border-[var(--border)] bg-[var(--surface)] flex items-center justify-center p-6 text-center" style="backface-visibility: hidden">
                    <div>
                        <div class="font-medium">{{ card.front }}</div>
                        <div class="text-xs text-[var(--text-muted)] mt-4">{{ t('flashcards.flip_hint') }}</div>
                    </div>
                </div>
                <div class="absolute inset-0 rounded-xl border border-[var(--primary)] bg-[var(--primary)]/5 flex items-center justify-center p-6 text-center"
                    style="backface-visibility: hidden; transform: rotateY(180deg)">
                    <div class="font-medium">{{ card.back }}</div>
                </div>
            </div>
        </div>

        <div v-if="flipped" class="flex gap-3 mt-5">
            <button @click="review('review_again')" class="px-5 py-2.5 rounded-lg border border-[var(--accent)] text-[var(--accent)] text-sm font-semibold">
                {{ t('flashcards.review_again') }}
            </button>
            <button @click="review('known')" class="px-5 py-2.5 rounded-lg bg-[var(--secondary)] text-white text-sm font-semibold">
                {{ t('flashcards.known') }}
            </button>
        </div>
    </div>
</template>
