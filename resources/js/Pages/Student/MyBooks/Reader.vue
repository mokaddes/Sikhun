<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import FlipReader from '@/Components/Reader/FlipReader.vue';
import ReaderChatPanel from '@/Components/Reader/ReaderChatPanel.vue';
import { BookOpenIcon } from '@heroicons/vue/24/solid';
import { useI18n } from '@/i18n';

const props = defineProps({ myBook: Object, pageUrls: { type: Object, default: () => null }, initialPage: { type: Number, default: 1 } });
const { t } = useI18n();

const page = usePage();
const csrfToken = computed(() => page.props.csrf_token ?? '');

const currentPage = ref(props.initialPage || 1);
const chatOpen = ref(false);
const ready = props.myBook.processing_status === 'completed';

// Persist the reading position (debounced) so the reader resumes here next
// time — a plain XHR keeps saving from interrupting the flipbook.
let saveTimer = 0;
function onPageChange(pageNumber) {
    currentPage.value = pageNumber;
    window.clearTimeout(saveTimer);
    saveTimer = window.setTimeout(() => {
        axios
            .post(`/my-books/${props.myBook.id}/read/progress`, { page: pageNumber }, { headers: { 'X-CSRF-TOKEN': csrfToken.value } })
            .catch(() => {});
    }, 900);
}
</script>

<template>
    <Head :title="myBook.title" />
    <StudentLayout>
        <Link href="/bookshelf" class="text-sm text-[var(--text-muted)] hover:text-[var(--text)] mb-4 inline-block">
            ← {{ t('bookshelf.back_to_shelf') }}
        </Link>
        <h1 class="font-heading text-xl font-bold mb-6">{{ myBook.title }}</h1>

        <div v-if="!ready" class="mb-6 rounded-xl border border-amber-500/40 bg-amber-500/10 px-4 py-3 text-sm">
            {{ t('my_books.processing_hint') }}
        </div>

        <FlipReader
            :book-id="myBook.id"
            :total-pages="Math.max(myBook.total_pages || 1, 1)"
            :url-prefix="`/my-books/${myBook.id}/read`"
            :page-urls="pageUrls"
            :initial-page="initialPage"
            @page-change="onPageChange"
        />

        <!-- Page-context chat — pinned to bottom-right corner -->
        <div class="fixed bottom-6 right-6 z-40 flex flex-col items-end pointer-events-none">
            <div v-if="chatOpen" class="mb-3 w-80 sm:w-96 max-w-[calc(100vw-3rem)] pointer-events-auto">
                <ReaderChatPanel
                    :chat-url="`/my-books/${myBook.id}/read/chat`"
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