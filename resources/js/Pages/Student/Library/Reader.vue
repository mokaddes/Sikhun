<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import FlipReader from '@/Components/Reader/FlipReader.vue';
import ReaderChatPanel from '@/Components/Reader/ReaderChatPanel.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ book: Object, accessiblePages: { type: Array, default: null }, chapters: { type: Array, default: () => [] } });
const { t } = useI18n();

const currentPage = ref(1);
const chatOpen = ref(false);
</script>

<template>
    <Head :title="book.title" />
    <StudentLayout>
        <Link :href="`/library/${book.slug}`" class="text-sm text-[var(--text-muted)] hover:text-[var(--text)] mb-4 inline-block">
            ← {{ t('reader.back_to_book') }}
        </Link>
        <h1 class="font-heading text-xl font-bold mb-6">{{ book.title }}</h1>

        <FlipReader
            :book-id="book.id"
            :total-pages="book.total_pages || 1"
            :accessible-pages="accessiblePages"
            :url-prefix="`/library/${book.id}/read`"
            @page-change="currentPage = $event"
        />

        <!-- Page-context chat — sits where the old support bot used to float -->
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
                class="w-14 h-14 rounded-full bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white shadow-xl flex items-center justify-center text-2xl"
                @click="chatOpen = true"
            >
                💬
            </button>
        </div>
    </StudentLayout>
</template>