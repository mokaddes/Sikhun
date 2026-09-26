<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import FlipReader from '@/Components/Reader/FlipReader.vue';
import ReaderChatPanel from '@/Components/Reader/ReaderChatPanel.vue';
import { BookOpenIcon } from '@heroicons/vue/24/solid';
import { useI18n } from '@/i18n';

const props = defineProps({ myBook: Object, pageUrls: { type: Object, default: () => null } });
const { t } = useI18n();

const currentPage = ref(1);
const chatOpen = ref(false);
const ready = props.myBook.processing_status === 'completed';
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
            @page-change="currentPage = $event"
        />

        <div class="fixed bottom-5 right-5 z-40 w-80 max-w-[calc(100vw-2.5rem)]">
            <div v-if="chatOpen" class="mb-3">
                <ReaderChatPanel
                    :chat-url="`/my-books/${myBook.id}/read/chat`"
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