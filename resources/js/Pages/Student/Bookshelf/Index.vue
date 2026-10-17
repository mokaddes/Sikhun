<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    BookOpenIcon,
    TrashIcon,
    ChatBubbleLeftRightIcon,
    CloudArrowUpIcon,
    XMarkIcon,
    SparklesIcon,
    ClockIcon,
    ArrowRightIcon,
    CheckCircleIcon,
    DocumentTextIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    shelves: { type: Array, default: () => [] },
    myBooks: { type: Array, default: () => [] },
});

const { t, locale } = useI18n();

const showUploadModal = ref(false);
const title = ref('');
const description = ref('');
const pdfFile = ref(null);
const enableRag = ref(true);
const uploading = ref(false);
const uploadError = ref('');

function onPickFile(e) {
    const file = e.target.files[0] ?? null;
    uploadError.value = '';
    if (file && file.size > 50 * 1024 * 1024) {
        uploadError.value = locale.value === 'en' ? 'File size exceeds 50MB limit.' : 'ফাইলের সাইজ ৫০ মেগাবাইটের বেশি হতে পারবে না।';
        pdfFile.value = null;
        return;
    }
    pdfFile.value = file;
}

function uploadBook() {
    if (uploading.value || !pdfFile.value || !title.value.trim()) return;

    const form = new FormData();
    form.append('title', title.value.trim());
    if (description.value.trim()) form.append('description', description.value.trim());
    form.append('pdf', pdfFile.value);
    form.append('rag_enabled', enableRag.value ? '1' : '0');

    uploading.value = true;
    router.post('/my-books', form, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            title.value = '';
            description.value = '';
            pdfFile.value = null;
            enableRag.value = true;
            uploadError.value = '';
            showUploadModal.value = false;
            const input = document.getElementById('my-book-pdf-modal');
            if (input) input.value = '';
        },
        onError: (errors) => {
            uploadError.value = Object.values(errors)[0] || 'Upload failed.';
        },
        onFinish: () => (uploading.value = false),
    });
}

function askAi(myBook) {
    router.post('/ai/chat', { my_book_id: myBook.id });
}

function removeBook(myBook, event) {
    event.preventDefault();
    event.stopPropagation();
    if (confirm(t('my_books.delete_confirm'))) {
        router.delete(`/my-books/${myBook.id}`, { preserveScroll: true });
    }
}

function statusLabel(myBook) {
    return t(`my_books.status_${myBook.processing_status}`) || myBook.processing_status;
}

const statusBadge = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
        case 'failed':
            return 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
        default:
            return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 animate-pulse';
    }
};
</script>

<template>
    <Head :title="t('bookshelf.title')" />
    <StudentLayout>
        <!-- Header Section -->
        <header class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <span class="eyebrow">{{ locale === 'en' ? 'Your Personal Collection' : 'আপনার ব্যক্তিগত সংগ্রহ' }}</span>
                <h1 class="mt-1 font-heading text-2xl font-black tracking-tight sm:text-3xl">
                    {{ t('bookshelf.title') }}
                </h1>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en'
                        ? 'Read your enrolled library books or study from your own uploaded notes & PDFs.'
                        : 'আপনার লাইব্রেরির বইগুলো পড়ুন অথবা নিজের আপলোড করা নোট ও পিডিএফ থেকে প্রস্তুতি নিন।' }}
                </p>
            </div>

            <!-- Upload Modal Trigger Button -->
            <button
                type="button"
                @click="showUploadModal = true"
                class="btn btn-primary inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-bold shadow-md shadow-[var(--primary)]/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
            >
                <CloudArrowUpIcon class="h-5 w-5" />
                <span>{{ t('my_books.add_own') }}</span>
            </button>
        </header>

        <!-- SECTION 1: My Uploaded Books & Notes -->
        <section class="mb-10">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-heading text-lg font-bold flex items-center gap-2">
                    <DocumentTextIcon class="h-5 w-5 text-[var(--primary)]" />
                    <span>{{ t('my_books.my_notes') }}</span>
                    <span v-if="myBooks.length" class="text-xs font-semibold text-[var(--text-muted)] bg-[var(--surface2)] px-2 py-0.5 rounded-full border border-[var(--border)]">
                        {{ myBooks.length }}
                    </span>
                </h2>
            </div>

            <!-- Uploaded Books Grid -->
            <div v-if="myBooks && myBooks.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="myBook in myBooks"
                    :key="myBook.id"
                    class="group relative flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 transition-all duration-200 hover:-translate-y-1 hover:border-[var(--primary)]/40 hover:shadow-lg hover:shadow-[var(--shadow-color)]"
                >
                    <div>
                        <!-- Top Row: Icon, Title & Delete -->
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-2.5">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[var(--primary)]/10 text-[var(--primary)]">
                                    <BookOpenIcon class="h-5 w-5" />
                                </span>
                                <h3 class="truncate font-heading text-base font-bold text-[var(--text)] group-hover:text-[var(--primary)] transition-colors">
                                    {{ myBook.title }}
                                </h3>
                            </div>

                            <!-- Delete Option -->
                            <button
                                type="button"
                                :title="t('common.delete')"
                                :aria-label="t('common.delete')"
                                class="shrink-0 rounded-lg p-1.5 text-[var(--text-muted)] hover:bg-rose-500/10 hover:text-rose-500 transition-colors"
                                @click="removeBook(myBook, $event)"
                            >
                                <TrashIcon class="h-4 w-4" />
                            </button>
                        </div>

                        <!-- Status & Badges -->
                        <div class="mb-3 flex flex-wrap items-center gap-2 text-xs">
                            <span class="rounded-lg px-2.5 py-0.5 font-semibold text-[11px]" :class="statusBadge(myBook.processing_status)">
                                {{ statusLabel(myBook) }}
                            </span>
                            <span v-if="myBook.total_pages" class="text-[var(--text-muted)]">
                                {{ myBook.total_pages }} {{ t('book_show.pages') }}
                            </span>
                            <span
                                v-if="myBook.rag_enabled"
                                class="inline-flex items-center gap-1 rounded-md bg-[var(--secondary)]/10 px-2 py-0.5 text-[10px] font-bold text-[var(--secondary)] border border-[var(--secondary)]/20"
                                :title="locale === 'en' ? 'AI Q&A Ready' : 'এআই প্রশ্নোত্তর সক্রিয়'"
                            >
                                <SparklesIcon class="h-3 w-3" />
                                RAG
                            </span>
                            <span v-if="myBook.last_page" class="rounded-md bg-[var(--surface2)] px-2 py-0.5 text-[11px] font-medium text-[var(--text-muted)] border border-[var(--border)]">
                                📖 {{ locale === 'en' ? `Page ${myBook.last_page}` : `পৃষ্ঠা ${myBook.last_page}` }}
                            </span>
                        </div>

                        <p v-if="myBook.processing_error" class="mb-3 break-words rounded-xl bg-rose-500/10 p-2.5 text-xs text-rose-500 border border-rose-500/20">
                            {{ myBook.processing_error }}
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 flex items-center gap-2 border-t border-[var(--border)]/70 pt-4">
                        <Link
                            :href="`/my-books/${myBook.id}/read`"
                            class="btn btn-outline btn-sm flex-1 justify-center rounded-xl text-xs font-bold"
                        >
                            {{ t('bookshelf.continue_reading') }}
                        </Link>
                        <button
                            v-if="myBook.rag_enabled"
                            type="button"
                            @click="askAi(myBook)"
                            class="btn btn-primary btn-sm inline-flex items-center gap-1.5 rounded-xl text-xs font-bold shadow-sm"
                        >
                            <ChatBubbleLeftRightIcon class="h-3.5 w-3.5" />
                            <span>{{ t('my_books.ask_ai') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty Uploaded Books -->
            <div
                v-else
                class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-[var(--border)] bg-[var(--surface)] p-8 text-center text-sm text-[var(--text-muted)]"
            >
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[var(--surface2)] text-[var(--text-muted)] mb-3">
                    <CloudArrowUpIcon class="h-6 w-6 opacity-60" />
                </div>
                <p class="font-medium text-[var(--text)]">{{ t('my_books.no_notes') }}</p>
                <p class="mt-1 text-xs text-[var(--text-muted)] max-w-sm">
                    {{ locale === 'en' ? 'Upload your own PDF lecture sheets, slides or chapters to read with Turn.js and chat with AI.' : 'আপনার লেকচার শিট, স্লাইড বা বইয়ের পিডিএফ আপলোড করে সুন্দরভাবে পড়ুন এবং এআই এর সহায়তা নিন।' }}
                </p>
                <button
                    type="button"
                    @click="showUploadModal = true"
                    class="btn btn-primary btn-sm mt-4 rounded-xl font-bold"
                >
                    <CloudArrowUpIcon class="h-4 w-4" />
                    <span>{{ t('my_books.add_own') }}</span>
                </button>
            </div>
        </section>

        <!-- SECTION 2: Library Books on the Shelf -->
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-heading text-lg font-bold flex items-center gap-2">
                    <BookOpenIcon class="h-5 w-5 text-[var(--secondary)]" />
                    <span>{{ t('bookshelf.library_books') }}</span>
                    <span v-if="shelves.length" class="text-xs font-semibold text-[var(--text-muted)] bg-[var(--surface2)] px-2 py-0.5 rounded-full border border-[var(--border)]">
                        {{ shelves.length }}
                    </span>
                </h2>
                <Link href="/library" class="text-xs font-semibold text-[var(--primary)] hover:underline inline-flex items-center gap-1">
                    <span>{{ locale === 'en' ? 'Explore Library' : 'লাইব্রেরি দেখুন' }}</span>
                    <ArrowRightIcon class="h-3.5 w-3.5" />
                </Link>
            </div>

            <!-- Library Books Grid (Full Card Clickable!) -->
            <div v-if="shelves && shelves.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4">
                <Link
                    v-for="shelf in shelves"
                    :key="shelf.id"
                    :href="`/library/${shelf.book.id}/read`"
                    class="card card-hover group block overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] transition-all duration-200 hover:-translate-y-1 hover:border-[var(--secondary)]/40 hover:shadow-lg hover:shadow-[var(--shadow-color)]"
                >
                    <!-- Cover Image Container -->
                    <div class="relative aspect-[3/4] w-full overflow-hidden bg-[var(--surface2)]">
                        <img
                            v-if="shelf.book.cover_image_url"
                            :src="shelf.book.cover_image_url"
                            :alt="shelf.book.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center text-[var(--text-muted)]">
                            <BookOpenIcon class="h-12 w-12 opacity-30" />
                        </div>

                        <!-- Progress / Last Read Badge overlay -->
                        <div v-if="shelf.last_read_page && shelf.last_read_page > 1" class="absolute bottom-2 left-2 right-2 rounded-lg bg-black/75 px-2.5 py-1 text-[11px] font-bold text-white backdrop-blur-md">
                            📖 {{ locale === 'en' ? `Page ${shelf.last_read_page}` : `পৃষ্ঠা ${shelf.last_read_page}` }}
                            <span v-if="shelf.book.total_pages" class="text-white/70"> / {{ shelf.book.total_pages }}</span>
                        </div>
                    </div>

                    <!-- Card Details -->
                    <div class="p-4">
                        <h3 class="line-clamp-2 font-heading text-sm font-bold leading-snug text-[var(--text)] group-hover:text-[var(--secondary)] transition-colors">
                            {{ shelf.book.title }}
                        </h3>

                        <div class="mt-3 flex items-center justify-between text-xs font-semibold text-[var(--primary)]">
                            <span>{{ t('bookshelf.continue_reading') }}</span>
                            <ArrowRightIcon class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" />
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Empty Library Books -->
            <div
                v-else
                class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-[var(--border)] bg-[var(--surface)] p-12 text-center text-[var(--text-muted)]"
            >
                <div class="grid h-14 w-14 place-items-center rounded-2xl bg-[var(--surface2)] text-[var(--text-muted)] mb-3">
                    <BookOpenIcon class="h-7 w-7 opacity-50" />
                </div>
                <p class="font-medium text-[var(--text)]">{{ t('bookshelf.empty') }}</p>
                <p class="mt-1 text-xs text-[var(--text-muted)] max-w-sm">
                    {{ locale === 'en' ? 'Explore our rich collection of textbooks and guides, and add them to your shelf.' : 'আমাদের সমৃদ্ধ পাঠ্যবই ও গাইড সংগ্রহ ঘুরে দেখুন এবং পড়তে শুরু করুন।' }}
                </p>
                <Link href="/library" class="btn btn-primary btn-sm mt-4 rounded-xl font-bold">
                    {{ t('nav.library') }} →
                </Link>
            </div>
        </section>

        <!-- UPLOAD MODAL -->
        <Teleport to="body">
            <div
                v-if="showUploadModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                @click.self="showUploadModal = false"
            >
                <div class="relative w-full max-w-lg rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-2xl transition-all">
                    <!-- Close button -->
                    <button
                        type="button"
                        class="icon-btn absolute right-5 top-5 h-8 w-8 rounded-full"
                        @click="showUploadModal = false"
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </button>

                    <!-- Modal Header -->
                    <div class="mb-6 flex items-center gap-3">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-[var(--primary)]/10 text-[var(--primary)]">
                            <CloudArrowUpIcon class="h-6 w-6" />
                        </span>
                        <div>
                            <h2 class="font-heading text-lg font-bold text-[var(--text)]">
                                {{ t('my_books.upload_modal_title') }}
                            </h2>
                            <p class="text-xs text-[var(--text-muted)]">
                                {{ locale === 'en' ? 'Upload PDF up to 50MB for personal reading & AI study.' : 'ব্যক্তিগত পড়াশোনা ও এআই সহায়তার জন্য ৫০ মেগাবাইট পর্যন্ত পিডিএফ আপলোড করুন।' }}
                            </p>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <form @submit.prevent="uploadBook" class="space-y-5">
                        <!-- Book Title -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">
                                {{ t('common.title') }} *
                            </label>
                            <input
                                v-model="title"
                                type="text"
                                required
                                :placeholder="t('my_books.title_placeholder')"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm font-medium focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                            />
                        </div>

                        <!-- Description (Optional) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">
                                {{ locale === 'en' ? 'Description (Optional)' : 'বিবরণ (ঐচ্ছিক)' }}
                            </label>
                            <textarea
                                v-model="description"
                                rows="2"
                                :placeholder="locale === 'en' ? 'Brief note or topic overview...' : 'নোট বা বিষয়ের সংক্ষিপ্ত পরিচিতি...'"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2 text-sm focus:border-[var(--primary)] focus:bg-[var(--surface)] focus:outline-none"
                            ></textarea>
                        </div>

                        <!-- PDF File Input -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">
                                PDF File *
                            </label>
                            <input
                                id="my-book-pdf-modal"
                                type="file"
                                accept="application/pdf"
                                required
                                @change="onPickFile"
                                class="w-full text-xs text-[var(--text-muted)] file:mr-3 file:cursor-pointer file:rounded-xl file:border-0 file:bg-[var(--primary)] file:px-4 file:py-2.5 file:text-xs file:font-bold file:text-white hover:file:bg-[var(--primary-hover)]"
                            />
                            <p v-if="pdfFile" class="mt-1 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                ✓ {{ pdfFile.name }} ({{ (pdfFile.size / (1024 * 1024)).toFixed(1) }} MB)
                            </p>
                            <p v-if="uploadError" class="mt-1 text-xs text-rose-500 font-medium">
                                {{ uploadError }}
                            </p>
                        </div>

                        <!-- RAG Option Switch / Checkbox -->
                        <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/60 p-4">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input
                                    v-model="enableRag"
                                    type="checkbox"
                                    class="mt-0.5 h-4 w-4 rounded text-[var(--primary)] focus:ring-[var(--primary)]"
                                />
                                <div class="text-xs">
                                    <span class="block font-bold text-[var(--text)] flex items-center gap-1.5">
                                        <SparklesIcon class="h-3.5 w-3.5 text-[var(--secondary)]" />
                                        {{ t('my_books.enable_rag') }}
                                    </span>
                                    <span class="mt-0.5 block text-[var(--text-muted)] leading-relaxed">
                                        {{ t('my_books.enable_rag_hint') }}
                                    </span>
                                </div>
                            </label>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button
                                type="button"
                                class="btn btn-secondary text-xs rounded-xl px-4 py-2.5 font-bold"
                                @click="showUploadModal = false"
                            >
                                {{ locale === 'en' ? 'Cancel' : 'বাতিল' }}
                            </button>
                            <button
                                type="submit"
                                :disabled="uploading || !title.trim() || !pdfFile"
                                class="btn btn-primary text-xs rounded-xl px-5 py-2.5 font-bold shadow-md shadow-[var(--primary)]/20 disabled:opacity-50"
                            >
                                <CloudArrowUpIcon class="h-4 w-4" />
                                <span>{{ uploading ? (locale === 'en' ? 'Uploading...' : 'আপলোড হচ্ছে...') : t('my_books.upload_btn') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </StudentLayout>
</template>
