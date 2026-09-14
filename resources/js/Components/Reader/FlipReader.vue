<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { useI18n } from '@/i18n';

const props = defineProps({
    bookId: { type: Number, required: true },
    totalPages: { type: Number, required: true },
    // null = unrestricted (full access); array = the only pages the
    // student's chapter purchases cover. Server re-validates every page
    // request anyway — this list only drives navigation affordances.
    accessiblePages: { type: Array, default: null },
});

const { t } = useI18n();
const currentPage = ref(1);
const imageUrl = ref(null);
const loading = ref(true);
const direction = ref('next'); // drives the CSS transition direction

const canPrev = computed(() => currentPage.value > 1);
const canNext = computed(() => currentPage.value < props.totalPages);

// When chapter-gated, jump to the first accessible page on load.
onMounted(() => {
    if (props.accessiblePages?.length) {
        currentPage.value = props.accessiblePages[0];
    }
    loadPage(currentPage.value);
    window.addEventListener('keydown', onKeydown);
});

async function loadPage(page) {
    loading.value = true;
    try {
        const { data } = await axios.get(`/library/${props.bookId}/read/page/${page}/url`);
        imageUrl.value = data.url;
    } catch (e) {
        // 403 on a gated page — the server is the authority, not this list.
        imageUrl.value = null;
    } finally {
        loading.value = false;
    }
}

function pageAccessible(page) {
    return props.accessiblePages === null || props.accessiblePages.includes(page);
}

function next() {
    if (!canNext.value) return;
    // Skip over pages outside the student's owned chapters.
    let page = currentPage.value;
    do { page++; } while (page < props.totalPages && !pageAccessible(page));
    if (!pageAccessible(page)) return;
    direction.value = 'next';
    currentPage.value = page;
    loadPage(currentPage.value);
}

function prev() {
    if (!canPrev.value) return;
    let page = currentPage.value;
    do { page--; } while (page > 1 && !pageAccessible(page));
    if (!pageAccessible(page)) return;
    direction.value = 'prev';
    currentPage.value = page;
    loadPage(currentPage.value);
}

function onKeydown(e) {
    if (e.key === 'ArrowRight') next();
    if (e.key === 'ArrowLeft') prev();
}

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
