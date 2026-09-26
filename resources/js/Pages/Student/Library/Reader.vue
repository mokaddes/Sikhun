<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import FlipReader from '@/Components/Reader/FlipReader.vue';
import ReaderChatPanel from '@/Components/Reader/ReaderChatPanel.vue';
import { BookOpenIcon } from '@heroicons/vue/24/solid';
import { useI18n } from '@/i18n';

const props = defineProps({ book: Object, accessiblePages: { type: Array, default: null }, chapters: { type: Array, default: () => [] }, pageUrls: { type: Object, default: () => null } });
const { t } = useI18n();

const currentPage = ref(1);
const chatOpen = ref(false);
const flipReader = ref(null);
const currentChapter = computed(() => props.chapters.find((chapter) => currentPage.value >= (chapter.start_page || 1) && currentPage.value <= (chapter.end_page || props.book.total_pages)) || null);
</script>

<template>
    <Head :title="book.title" />
    <StudentLayout>
        <Link :href="`/library/${book.slug}`" class="text-sm text-[var(--text-muted)] hover:text-[var(--text)] mb-4 inline-block">
            ← {{ t('reader.back_to_book') }}
        </Link>
        <h1 class="font-heading text-xl font-bold mb-6">{{ book.title }}</h1>

        <nav v-if="chapters.length" class="mb-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
            <h2 class="font-semibold mb-2">Contents</h2>
            <div class="flex flex-wrap gap-2">
                <button v-for="chapter in chapters" :key="chapter.id" @click="flipReader?.goToRealPage(chapter.start_page)"
                    class="rounded-lg border border-[var(--border)] px-3 py-2 text-left text-sm hover:border-[var(--primary)]">
                    {{ chapter.chapter_number ? `Chapter ${chapter.chapter_number}: ` : '' }}{{ chapter.title }}
                    <span class="block text-xs text-[var(--text-muted)]">Pages {{ chapter.start_page ?? '?' }}–{{ chapter.end_page ?? '?' }}</span>
                </button>
            </div>
            <p v-if="currentChapter" class="mt-2 text-xs text-[var(--text-muted)]">Reading: {{ currentChapter.title }}</p>
        </nav>

        <FlipReader
            ref="flipReader"
            :book-id="book.id"
            :total-pages="book.total_pages || 1"
            :accessible-pages="accessiblePages"
            :url-prefix="`/library/${book.id}/read`"
            :page-urls="pageUrls"
            @page-change="currentPage = $event"
        />

        <!-- Page-context chat — floats bottom-right over the reader -->
        <div class="fixed bottom-5 right-5 z-40 w-80 max-w-[calc(100vw-2.5rem)]">
            <div v-if="chatOpen" class="mb-3">
                <ReaderChatPanel
                    :chat-url="`/library/${book.id}/read/chat`"
                    :page="currentPage"
                    @close="chatOpen = false"
                />
            </div>
            <button
                v-else
                class="w-14 h-14 rounded-full bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white shadow-xl flex items-center justify-center transition-transform hover:scale-105"
                :aria-label="t('reader.chat_title')"
                :title="t('reader.chat_title')"
                @click="chatOpen = true"
            >
                <BookOpenIcon class="w-7 h-7" />
            </button>
        </div>
    </StudentLayout>
</template>
