<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { BookOpenIcon, TrashIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ shelves: Array, myBooks: Array });
const { t } = useI18n();

const title = ref('');
const description = ref('');
const pdfFile = ref(null);
const uploading = ref(false);

function onPickFile(e) {
    pdfFile.value = e.target.files[0] ?? null;
}

function uploadBook() {
    if (uploading.value || !pdfFile.value || !title.value.trim()) return;

    const form = new FormData();
    form.append('title', title.value.trim());
    if (description.value.trim()) form.append('description', description.value.trim());
    form.append('pdf', pdfFile.value);

    uploading.value = true;
    router.post('/my-books', form, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            title.value = '';
            description.value = '';
            pdfFile.value = null;
            document.getElementById('my-book-pdf').value = '';
        },
        onFinish: () => (uploading.value = false),
    });
}

function askAi(myBook) {
    router.post('/ai/chat', { my_book_id: myBook.id });
}

function removeBook(myBook, event) {
    event.preventDefault();
    if (confirm(t('my_books.delete_confirm'))) {
        router.delete(`/my-books/${myBook.id}`, { preserveScroll: true });
    }
}

function statusLabel(myBook) {
    return t(`my_books.status_${myBook.processing_status}`) || myBook.processing_status;
}

const statusStyle = (status) =>
    status === 'completed'
        ? 'bg-[var(--secondary)]/10 text-[var(--secondary)]'
        : status === 'failed'
            ? 'bg-red-500/10 text-red-500'
            : 'bg-amber-500/10 text-amber-600';
</script>

<template>
    <Head :title="t('bookshelf.title')" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('bookshelf.title') }}</h1>

        <!-- Upload your own book / notes PDF -->
        <div class="mb-10 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5">
            <h2 class="font-heading text-lg font-bold mb-1">{{ t('my_books.add_own') }}</h2>
            <p class="text-sm text-[var(--text-muted)] mb-4">{{ t('my_books.add_own_hint') }}</p>

            <div class="grid md:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold uppercase text-[var(--text-muted)] mb-1.5">{{ t('common.title') }}</label>
                    <input v-model="title" type="text" :placeholder="t('my_books.title_placeholder')"
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/40" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-[var(--text-muted)] mb-1.5">PDF</label>
                    <input id="my-book-pdf" type="file" accept="application/pdf" @change="onPickFile"
                        class="w-full text-sm file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-[var(--primary)] file:text-white file:cursor-pointer text-[var(--text-muted)]" />
                </div>
                <button
                    @click="uploadBook"
                    :disabled="uploading || !title.trim() || !pdfFile"
                    class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold disabled:opacity-60"
                >
                    {{ uploading ? t('common.saving') : t('my_books.upload') }}
                </button>
            </div>
        </div>

        <!-- My uploaded books -->
        <h2 class="font-heading text-lg font-bold mb-4">{{ t('my_books.my_notes') }}</h2>
        <div v-if="myBooks.length" class="mb-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="myBook in myBooks" :key="myBook.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <BookOpenIcon class="w-5 h-5 shrink-0 text-[var(--primary)]" />
                        <span class="font-medium text-sm truncate">{{ myBook.title }}</span>
                    </div>
                    <button :title="t('common.delete')" class="shrink-0 text-[var(--text-muted)] hover:text-[var(--accent)]" @click="removeBook(myBook, $event)">
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>

                <div class="mb-3">
                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium" :class="statusStyle(myBook.processing_status)">
                        {{ statusLabel(myBook) }}
                    </span>
                    <span v-if="myBook.total_pages" class="ml-2 text-xs text-[var(--text-muted)]">{{ myBook.total_pages }} {{ t('book_show.pages') }}</span>
                </div>
                <p v-if="myBook.processing_error" class="text-xs text-red-500 mb-3 break-words">{{ myBook.processing_error }}</p>

                <div class="flex gap-2">
                    <Link :href="`/my-books/${myBook.id}/read`" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-xs font-semibold hover:border-[var(--primary)]">
                        {{ t('bookshelf.continue_reading') }}
                    </Link>
                    <button
                        @click="askAi(myBook)"
                        title="Ask AI about this book"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-[var(--primary)]/10 text-[var(--primary)] border border-[var(--secondary)]/20 text-xs font-semibold hover:bg-[var(--primary)]/20"
                    >
                        <ChatBubbleLeftRightIcon class="w-4 h-4" />
                        {{ t('my_books.ask_ai') }}
                    </button>
                </div>
            </div>
        </div>
        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-8 mb-10 text-center text-sm text-[var(--text-muted)]">
            {{ t('my_books.no_notes') }}
        </div>

        <!-- Library books on the shelf -->
        <h2 class="font-heading text-lg font-bold mb-4">{{ t('bookshelf.library_books') }}</h2>
        <div v-if="shelves.length" class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <div v-for="shelf in shelves" :key="shelf.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-hidden">
                <div class="aspect-[3/4] bg-[var(--surface2)] flex items-center justify-center text-[var(--text-muted)] text-xs overflow-hidden">
                    <img v-if="shelf.book.cover_image_url" :src="shelf.book.cover_image_url" class="w-full h-full object-cover" />
                    <span v-else>{{ t('home.no_cover') }}</span>
                </div>
                <div class="p-4">
                    <div class="font-medium text-sm mb-2 line-clamp-2">{{ shelf.book.title }}</div>
                    <Link :href="`/library/${shelf.book.id}/read`" class="text-sm font-medium text-[var(--primary)] hover:underline">
                        {{ t('bookshelf.continue_reading') }} →
                    </Link>
                </div>
            </div>
        </div>
        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
            {{ t('bookshelf.empty') }}
            <div class="mt-4">
                <Link href="/library" class="text-[var(--primary)] font-medium">{{ t('nav.library') }} →</Link>
            </div>
        </div>
    </StudentLayout>
</template>