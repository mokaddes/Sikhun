<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import FlipReader from '@/Components/Reader/FlipReader.vue';
import ReaderChatPanel from '@/Components/Reader/ReaderChatPanel.vue';
import { BookOpenIcon } from '@heroicons/vue/24/solid';
import { useI18n } from '@/i18n';

const props = defineProps({ book: Object, accessiblePages: { type: Array, default: null }, chapters: { type: Array, default: () => [] }, pageUrls: { type: Object, default: () => null }, initialPage: { type: Number, default: 1 } });
const { t } = useI18n();

const page = usePage();
const csrfToken = computed(() => page.props.csrf_token ?? '');

const currentPage = ref(props.initialPage || 1);
const chatOpen = ref(false);
const flipReader = ref(null);
const currentChapter = computed(() => props.chapters.find((chapter) => currentPage.value >= (chapter.start_page || 1) && currentPage.value <= (chapter.end_page || props.book.total_pages)) || null);

// Persist the reading position (debounced) so the reader resumes here next
// time. Uses a plain XHR rather than an Inertia visit so saving never
// interrupts the flipbook.
let saveTimer = 0;
function onPageChange(pageNumber) {
    currentPage.value = pageNumber;
    window.clearTimeout(saveTimer);
    saveTimer = window.setTimeout(() => {
        axios
            .post(`/library/${props.book.id}/read/progress`, { page: pageNumber }, { headers: { 'X-CSRF-TOKEN': csrfToken.value } })
            .catch(() => {});
    }, 900);
}
</script>

<template>
    <Head :title="book.title" />
    <StudentLayout>
        <Link :href="`/library/${book.slug}`" class="text-sm text-[var(--text-muted)] hover:text-[var(--text)] mb-4 inline-block">
            ← {{ t('reader.back_to_book') }}
        </Link>
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="font-heading text-xl font-bold">{{ book.title }}</h1>
                <p v-if="currentChapter" class="text-xs text-[var(--text-muted)] mt-0.5">
                    Reading: <span class="text-[var(--primary)] font-medium">{{ currentChapter.chapter_number ? `Chapter ${currentChapter.chapter_number}: ` : '' }}{{ currentChapter.title }}</span>
                </p>
            </div>
            <div v-if="chapters.length" class="text-xs text-[var(--text-muted)] flex items-center gap-1.5">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>{{ chapters.length }} chapters indexed</span>
            </div>
        </div>

        <FlipReader
            ref="flipReader"
            :book-id="book.id"
            :total-pages="book.total_pages || 1"
            :accessible-pages="accessiblePages"
            :url-prefix="`/library/${book.id}/read`"
            :page-urls="pageUrls"
            :initial-page="initialPage"
            :chapters="chapters"
            @page-change="onPageChange"
        />

        <!-- Page-context chat — pinned to bottom-right corner -->
        <div class="fixed bottom-6 right-6 z-40 flex flex-col items-end pointer-events-none">
            <div v-if="chatOpen" class="mb-3 w-80 sm:w-96 max-w-[calc(100vw-3rem)] pointer-events-auto">
                <ReaderChatPanel
                    :chat-url="`/library/${book.id}/read/chat`"
                    :page="currentPage"
                    @close="chatOpen = false"
                />
            </div>
            <button
                v-else
                class="pointer-events-auto w-14 h-14 rounded-full bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white shadow-2xl flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95"
                :aria-label="t('reader.chat_title')"
                :title="t('reader.chat_title')"
                @click="chatOpen = true"
            >
                <BookOpenIcon class="w-7 h-7" />
            </button>
        </div>
    </StudentLayout>
</template>
