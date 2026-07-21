<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { useI18n } from '@/i18n';

const props = defineProps({
    bookId: { type: Number, required: true },
    totalPages: { type: Number, required: true },
});

const { t } = useI18n();
const currentPage = ref(1);
const imageUrl = ref(null);
const loading = ref(true);
const direction = ref('next'); // drives the CSS transition direction

const canPrev = computed(() => currentPage.value > 1);
const canNext = computed(() => currentPage.value < props.totalPages);

async function loadPage(page) {
    loading.value = true;
    try {
        const { data } = await axios.get(`/library/${props.bookId}/read/page/${page}/url`);
        imageUrl.value = data.url;
    } finally {
        loading.value = false;
    }
}

function next() {
    if (!canNext.value) return;
    direction.value = 'next';
    currentPage.value++;
    loadPage(currentPage.value);
}

function prev() {
    if (!canPrev.value) return;
    direction.value = 'prev';
    currentPage.value--;
    loadPage(currentPage.value);
}

function onKeydown(e) {
    if (e.key === 'ArrowRight') next();
    if (e.key === 'ArrowLeft') prev();
}

onMounted(() => {
    loadPage(currentPage.value);
    window.addEventListener('keydown', onKeydown);
});
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown));
</script>

<template>
    <div class="flex flex-col items-center select-none">
        <div class="relative w-full max-w-lg aspect-[4/5.6] rounded-xl overflow-hidden border border-[var(--border)] bg-[var(--surface2)] shadow-xl">
            <div v-if="loading" class="absolute inset-0 flex items-center justify-center text-[var(--text-muted)] text-sm">
                {{ t('reader.loading') }}
            </div>
            <Transition :name="direction === 'next' ? 'flip-next' : 'flip-prev'" mode="out-in">
                <img
                    v-if="!loading && imageUrl"
                    :key="currentPage"
                    :src="imageUrl"
                    :alt="`Page ${currentPage}`"
                    class="absolute inset-0 w-full h-full object-contain bg-white"
                    @contextmenu.prevent
                    draggable="false"
                />
            </Transition>

            <button v-if="canPrev" @click="prev" class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center">
                ‹
            </button>
            <button v-if="canNext" @click="next" class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center">
                ›
            </button>
        </div>

        <div class="mt-4 text-sm text-[var(--text-muted)]">
            {{ t('reader.page_of', { current: currentPage, total: totalPages }) }}
        </div>
    </div>
</template>

<style scoped>
/* Lightweight page-turn feel without a jQuery dependency (Turn.js requires
   jQuery, which conflicts with Vite's ESM-only pipeline) — a perspective
   rotate reads as "turning" convincingly enough at this size. */
.flip-next-enter-active, .flip-next-leave-active,
.flip-prev-enter-active, .flip-prev-leave-active {
    transition: transform 0.25s ease, opacity 0.25s ease;
}
.flip-next-enter-from { transform: rotateY(-8deg) translateX(20px); opacity: 0; }
.flip-next-leave-to { transform: rotateY(8deg) translateX(-20px); opacity: 0; }
.flip-prev-enter-from { transform: rotateY(8deg) translateX(-20px); opacity: 0; }
.flip-prev-leave-to { transform: rotateY(-8deg) translateX(20px); opacity: 0; }
</style>
