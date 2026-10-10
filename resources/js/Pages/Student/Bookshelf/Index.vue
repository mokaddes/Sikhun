<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { BookOpenIcon, TrashIcon, ChatBubbleLeftRightIcon, CloudArrowUpIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ shelves: Array, myBooks: Array });
const { t, locale } = useI18n();

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
        <header class="anim-rise mb-6">
            <span class="eyebrow">{{ locale === 'en' ? 'Your collection' : 'আপনার সংগ্রহ' }}</span>
            <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ t('bookshelf.title') }}</h1>
        </header>

        <!-- Upload your own book / notes PDF -->
        <div class="card anim-rise mb-8 rounded-2xl p-5">
            <div class="mb-4 flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[var(--primary)]/10 text-[var(--primary)]">
                    <CloudArrowUpIcon class="h-5 w-5" />
                </span>
                <div>
                    <h2 class="font-heading font-bold">{{ t('my_books.add_own') }}</h2>
                    <p class="text-sm text-[var(--text-muted)]">{{ t('my_books.add_own_hint') }}</p>
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-[1fr_1fr_auto] md:items-end">
                <div>
                    <label class="label">{{ t('common.title') }}</label>
                    <input v-model="title" type="text" :placeholder="t('my_books.title_placeholder')" class="input" />
                </div>
                <div>
                    <label class="label">PDF</label>
                    <input id="my-book-pdf" type="file" accept="application/pdf" @change="onPickFile"
                        class="w-full text-sm text-[var(--text-muted)] file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-[var(--primary)] file:px-3 file:py-2 file:text-white" />
                </div>
                <button @click="uploadBook" :disabled="uploading || !title.trim() || !pdfFile" class="btn btn-primary md:mb-0.5">
                    {{ uploading ? t('common.saving') : t('my_books.upload') }}
                </button>
            </div>
        </div>

        <!-- My uploaded books -->
        <h2 class="mb-4 font-heading text-lg font-bold">{{ t('my_books.my_notes') }}</h2>
        <div v-if="myBooks.length" class="stagger mb-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="myBook in myBooks" :key="myBook.id" class="card card-hover flex flex-col rounded-2xl p-4">
                <div class="mb-2 flex items-start justify-between gap-2">
                    <div class="flex min-w-0 items-center gap-2">
                        <BookOpenIcon class="h-5 w-5 shrink-0 text-[var(--primary)]" />
                        <span class="truncate text-sm font-medium">{{ myBook.title }}</span>
                    </div>
                    <button :title="t('common.delete')" class="shrink-0 text-[var(--text-muted)] transition hover:text-[var(--accent)]" @click="removeBook(myBook, $event)">
                        <TrashIcon class="h-4 w-4" />
                    </button>
                </div>

                <div class="mb-3">
                    <span class="badge" :class="statusStyle(myBook.processing_status)">{{ statusLabel(myBook) }}</span>
                    <span v-if="myBook.total_pages" class="ml-2 text-xs text-[var(--text-muted)]">{{ myBook.total_pages }} {{ t('book_show.pages') }}</span>
                </div>
                <p v-if="myBook.processing_error" class="mb-3 break-words text-xs text-red-500">{{ myBook.processing_error }}</p>

                <div class="mt-auto flex gap-2">
                    <Link :href="`/my-books/${myBook.id}/read`" class="btn btn-outline btn-sm flex-1">
                        {{ t('bookshelf.continue_reading') }}
                    </Link>
                    <button @click="askAi(myBook)" title="Ask AI about this book" class="btn btn-sm btn-primary">
                        <ChatBubbleLeftRightIcon class="h-4 w-4" /> {{ t('my_books.ask_ai') }}
                    </button>
                </div>
            </div>
        </div>
        <div v-else class="card mb-10 flex flex-col items-center gap-2 rounded-2xl border-dashed p-8 text-center text-sm text-[var(--text-muted)]">
            <BookOpenIcon class="h-9 w-9 opacity-40" />
            {{ t('my_books.no_notes') }}
        </div>

        <!-- Library books on the shelf -->
        <h2 class="mb-4 font-heading text-lg font-bold">{{ t('bookshelf.library_books') }}</h2>
        <div v-if="shelves.length" class="stagger grid grid-cols-2 gap-5 md:grid-cols-4">
            <div v-for="shelf in shelves" :key="shelf.id" class="card card-hover group overflow-hidden rounded-2xl">
                <div class="aspect-[3/4] overflow-hidden bg-[var(--surface2)]">
                    <img v-if="shelf.book.cover_image_url" :src="shelf.book.cover_image_url" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                    <div v-else class="flex h-full w-full items-center justify-center text-[var(--text-muted)]">
                        <BookOpenIcon class="h-10 w-10 opacity-40" />
                    </div>
                </div>
                <div class="p-4">
                    <div class="mb-2 line-clamp-2 text-sm font-medium">{{ shelf.book.title }}</div>
                    <Link :href="`/library/${shelf.book.id}/read`" class="text-sm font-medium text-[var(--primary)] hover:underline">
                        {{ t('bookshelf.continue_reading') }} →
                    </Link>
                </div>
            </div>
        </div>
        <div v-else class="card flex flex-col items-center gap-2 rounded-2xl border-dashed p-12 text-center text-[var(--text-muted)]">
            {{ t('bookshelf.empty') }}
            <Link href="/library" class="btn btn-outline btn-sm mt-2">{{ t('nav.library') }} →</Link>
        </div>
    </StudentLayout>
</template>
