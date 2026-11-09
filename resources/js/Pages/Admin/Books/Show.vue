<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';
import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    ScissorsIcon,
    SparklesIcon,
    BookOpenIcon,
    CheckCircleIcon,
    XMarkIcon,
    ArrowPathIcon,
    ExclamationTriangleIcon,
    ChevronRightIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ book: Object, chapters: Array, stats: Object });
const { t } = useI18n();

const retryForm = useForm({});
const retrying = retryForm.processing;

const editingPrices = ref(false);
const chapterPriceForm = useForm({
    chapters: (props.chapters || []).map((c) => ({ id: c.id, price: c.price ?? '' })),
});

function savePrices() {
    chapterPriceForm.put(`/admin/books/${props.book.id}/chapters`, {
        preserveScroll: true,
        onSuccess: () => {
            editingPrices.value = false;
        },
    });
}

function retryProcessing() {
    retryForm.post(`/admin/books/${props.book.id}/retry-processing`, {
        preserveScroll: true,
    });
}

function statusBadge(status) {
    return {
        completed: 'bg-[var(--secondary)]/15 text-[var(--secondary)]',
        processing: 'bg-[var(--primary)]/15 text-[var(--primary)]',
        pending: 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]',
        failed: 'bg-[var(--accent)]/15 text-[var(--accent)]',
    }[status] ?? 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]';
}

const embeddingCoverage = props.stats.chunks > 0
    ? Math.round((props.stats.chunks_with_embeddings / props.stats.chunks) * 100)
    : 0;

// ==================== MANUAL CHAPTER DIVISION ====================

const BN_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

function toEnglishDigits(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[০-৯]/g, (d) => '০১২৩৪৫৬৭৮৯'.indexOf(d));
}

function toBengaliDigits(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[0-9]/g, (d) => BN_DIGITS[Number(d)]);
}

function parseChapterNumber(val) {
    if (val === null || val === undefined || val === '') return null;
    const eng = toEnglishDigits(String(val));
    const m = eng.match(/\d+/);
    return m ? parseInt(m[0], 10) : null;
}

// Sort chapters strictly by reading order (1, 2, 3...)
const sortedChapters = computed(() => {
    return [...(props.chapters || [])].sort((a, b) => {
        const pageA = (a.start_page != null && a.start_page > 0) ? Number(a.start_page) : 999999;
        const pageB = (b.start_page != null && b.start_page > 0) ? Number(b.start_page) : 999999;
        if (pageA !== pageB) return pageA - pageB;

        const numA = parseChapterNumber(a.chapter_number) ?? 999999;
        const numB = parseChapterNumber(b.chapter_number) ?? 999999;
        if (numA !== numB) return numA - numB;

        const sortA = Number(a.sort_order ?? 0);
        const sortB = Number(b.sort_order ?? 0);
        if (sortA !== sortB) return sortA - sortB;

        return (a.id || 0) - (b.id || 0);
    });
});

// Calculate total coverage
const totalBookPages = computed(() => Number(props.book.total_pages) || 1);

const coveredPagesCount = computed(() => {
    const pageSet = new Set();
    (props.chapters || []).forEach((c) => {
        const start = Math.max(1, Number(c.start_page) || 1);
        const end = Math.min(totalBookPages.value, Number(c.end_page) || start);
        for (let p = start; p <= end; p++) {
            pageSet.add(p);
        }
    });
    return pageSet.size;
});

const coveragePercent = computed(() => {
    if (!totalBookPages.value) return 0;
    return Math.min(100, Math.round((coveredPagesCount.value / totalBookPages.value) * 100));
});

// Chapter Modal State
const showChapterModal = ref(false);
const editingChapterId = ref(null);
const chapterForm = useForm({
    title: '',
    chapter_number: '',
    start_page: 1,
    end_page: 1,
    price: null,
});

function openAddChapterModal() {
    editingChapterId.value = null;
    const chapters = sortedChapters.value;
    let nextStart = 1;
    let maxNum = 0;
    let maxEndPage = 0;

    for (const c of chapters) {
        const end = Number(c.end_page || c.start_page || 0);
        if (end > maxEndPage) maxEndPage = end;
        const n = parseChapterNumber(c.chapter_number);
        if (n && n > maxNum) maxNum = n;
    }

    if (maxEndPage > 0) {
        nextStart = Math.min(maxEndPage + 1, totalBookPages.value);
    }
    const nextNum = maxNum > 0 ? maxNum + 1 : (chapters.length + 1);

    chapterForm.reset();
    chapterForm.title = `অধ্যায় ${nextNum}`;
    chapterForm.chapter_number = nextNum;
    chapterForm.start_page = nextStart;
    chapterForm.end_page = Math.min(nextStart + 30, totalBookPages.value);
    chapterForm.price = null;
    showChapterModal.value = true;
}

function openEditChapterModal(chapter) {
    editingChapterId.value = chapter.id;
    chapterForm.title = chapter.title;
    chapterForm.chapter_number = chapter.chapter_number ?? '';
    chapterForm.start_page = chapter.start_page || 1;
    chapterForm.end_page = chapter.end_page || chapter.start_page || 1;
    chapterForm.price = chapter.price !== null ? Number(chapter.price) : null;
    showChapterModal.value = true;
}

function saveChapter() {
    // Normalize chapter_number to integer if provided
    const parsedNum = parseChapterNumber(chapterForm.chapter_number);
    chapterForm.chapter_number = parsedNum !== null ? parsedNum : null;

    if (editingChapterId.value) {
        chapterForm.put(`/admin/books/${props.book.id}/chapters/${editingChapterId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                showChapterModal.value = false;
            },
        });
    } else {
        chapterForm.post(`/admin/books/${props.book.id}/chapters`, {
            preserveScroll: true,
            onSuccess: () => {
                showChapterModal.value = false;
            },
        });
    }
}

function deleteChapter(chapter) {
    if (!confirm(`Are you sure you want to delete "${chapter.title}"? Page assignments will be unlinked.`)) {
        return;
    }
    router.delete(`/admin/books/${props.book.id}/chapters/${chapter.id}`, {
        preserveScroll: true,
    });
}

// Quick Split Modal State
const showSplitModal = ref(false);
const splitForm = useForm({
    num_chapters: 4,
    naming_prefix: 'অধ্যায়',
});

function openSplitModal() {
    splitForm.num_chapters = Math.max(2, Math.min(10, Math.ceil(totalBookPages.value / 50)));
    splitForm.naming_prefix = 'অধ্যায়';
    showSplitModal.value = true;
}

function applyQuickSplit() {
    const total = totalBookPages.value;
    const count = Math.max(1, Math.min(total, Number(splitForm.num_chapters) || 1));
    const pagesPerChapter = Math.floor(total / count);
    const prefix = splitForm.naming_prefix.trim() || 'অধ্যায়';

    const generated = [];
    let curStart = 1;

    for (let i = 1; i <= count; i++) {
        const curEnd = i === count ? total : curStart + pagesPerChapter - 1;
        generated.push({
            title: `${prefix} ${i}`,
            chapter_number: i,
            start_page: curStart,
            end_page: curEnd,
            price: null,
        });
        curStart = curEnd + 1;
    }

    router.post(`/admin/books/${props.book.id}/chapters/sync`, {
        chapters: generated,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showSplitModal.value = false;
        },
    });
}
</script>

<template>
    <Head :title="book.title" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-1">
                    <Link href="/admin/books" class="hover:text-[var(--text)]">Books</Link>
                    <ChevronRightIcon class="h-3 w-3" />
                    <span>Details & Chapters</span>
                </div>
                <h1 class="font-heading text-2xl font-extrabold text-[var(--text)]">{{ book.title }}</h1>
                <p class="text-sm text-[var(--text-muted)] mt-0.5">
                    {{ book.author?.name ? `Author: ${book.author.name} · ` : '' }}
                    <span class="font-bold text-[var(--text)]">{{ totalBookPages }}</span> Pages Total
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <Link :href="`/admin/books/${book.id}/pdf`" class="px-4 py-2 rounded-xl bg-[var(--secondary)]/15 text-[var(--secondary)] text-sm font-semibold hover:bg-[var(--secondary)]/25 transition">
                    {{ book.pdf_path ? 'Replace PDF' : 'Upload PDF' }}
                </Link>
                <Link :href="`/admin/books/${book.id}/edit`" class="px-4 py-2 rounded-xl border border-[var(--border)] hover:bg-[var(--surface2)] text-sm font-semibold transition">
                    {{ t('common.edit') }}
                </Link>
                <button
                    v-if="book.processing_status === 'failed' || book.processing_status === 'completed'"
                    @click="retryProcessing"
                    :disabled="retrying"
                    class="px-4 py-2 rounded-xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-bold shadow-md shadow-orange-500/20 disabled:opacity-60 transition"
                >
                    {{ retrying ? 'Processing…' : (book.processing_status === 'failed' ? t('admin.books.retry_processing') : t('admin.books.reprocess')) }}
                </button>
            </div>
        </div>

        <!-- PDF Processing & OCR Summary -->
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 mb-6 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <h2 class="font-heading text-lg font-bold">{{ t('admin.books.pdf_processing') }}</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-bold" :class="statusBadge(book.processing_status)">
                        {{ book.processing_status === 'completed' ? '✓ ' + t('admin.books.status_completed')
                            : book.processing_status === 'failed' ? '❌ ' + t('admin.books.status_failed')
                            : book.processing_status === 'processing' ? '⏳ ' + t('admin.books.status_processing')
                            : t('admin.books.status_pending') }}
                    </span>
                </div>

                <div v-if="book.parser_name" class="text-xs text-[var(--text-muted)]">
                    Parser: <span class="font-semibold text-[var(--text)]">{{ book.parser_name }}</span>
                    · Parsed: {{ book.parsed_at ?? '—' }}
                </div>
            </div>

            <div v-if="book.processing_status === 'failed' && book.processing_error" class="rounded-xl bg-[var(--accent)]/10 border border-[var(--accent)]/30 p-4 mb-5">
                <p class="text-sm text-[var(--accent)] font-bold mb-1">{{ t('admin.books.processing_failed') }}</p>
                <pre class="text-xs whitespace-pre-wrap text-[var(--text-muted)]">{{ book.processing_error }}</pre>
                <button @click="retryProcessing" :disabled="retrying" class="mt-3 px-4 py-2 rounded-lg bg-[var(--accent)] text-white text-xs font-bold disabled:opacity-60">
                    {{ t('admin.books.retry_processing') }}
                </button>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3 text-center">
                <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 p-3">
                    <div class="text-2xl font-heading font-extrabold text-[var(--text)]">{{ stats.pages }}</div>
                    <div class="text-xs text-[var(--text-muted)] mt-0.5">{{ t('admin.books.pages') }}</div>
                </div>
                <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 p-3">
                    <div class="text-2xl font-heading font-extrabold text-[var(--primary)]">{{ stats.chapters }}</div>
                    <div class="text-xs text-[var(--text-muted)] mt-0.5">{{ t('admin.books.chapters') }}</div>
                </div>
                <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 p-3">
                    <div class="text-2xl font-heading font-extrabold text-[var(--text)]">{{ stats.tables }}</div>
                    <div class="text-xs text-[var(--text-muted)] mt-0.5">{{ t('admin.books.tables') }}</div>
                </div>
                <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 p-3">
                    <div class="text-2xl font-heading font-extrabold text-[var(--text)]">{{ stats.images }}</div>
                    <div class="text-xs text-[var(--text-muted)] mt-0.5">{{ t('admin.books.images') }}</div>
                </div>
                <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 p-3">
                    <div class="text-2xl font-heading font-extrabold text-[var(--text)]">{{ stats.formulas }}</div>
                    <div class="text-xs text-[var(--text-muted)] mt-0.5">{{ t('admin.books.formulas') }}</div>
                </div>
                <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 p-3">
                    <div class="text-2xl font-heading font-extrabold text-[var(--text)]">{{ stats.chunks }}</div>
                    <div class="text-xs text-[var(--text-muted)] mt-0.5">{{ t('admin.books.chunks') }}</div>
                </div>
                <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 p-3">
                    <div class="text-2xl font-heading font-extrabold text-[var(--text)]">{{ stats.elements }}</div>
                    <div class="text-xs text-[var(--text-muted)] mt-0.5">{{ t('admin.books.elements') }}</div>
                </div>
            </div>
        </div>

        <!-- RAG & Embedding Coverage -->
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 mb-6 shadow-xs">
            <h2 class="font-heading text-lg font-bold mb-3">RAG Retrieval & Embeddings</h2>
            <div class="flex items-center gap-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold shrink-0"
                    :class="stats.chunks > 0 ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                    {{ stats.chunks > 0 ? '✓ ' + t('admin.books.indexed') : t('admin.books.status_pending') }}
                </span>
                <div class="flex-1">
                    <div class="h-2.5 rounded-full bg-[var(--surface2)] overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-orange-500 to-emerald-500 transition-all duration-500" :style="{ width: embeddingCoverage + '%' }" />
                    </div>
                    <div class="flex justify-between items-center text-xs text-[var(--text-muted)] mt-1.5 font-medium">
                        <span>{{ t('admin.books.embeddings') }}: {{ stats.chunks_with_embeddings }} / {{ stats.chunks }} chunks</span>
                        <span class="font-bold text-[var(--text)]">{{ embeddingCoverage }}% Covered</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MANUAL CHAPTER DIVISION & MANAGEMENT SUITE -->
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 mb-8 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-[var(--border)] mb-5">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="font-heading text-xl font-extrabold text-[var(--text)]">Chapter Division & Management</h2>
                        <span class="px-2.5 py-0.5 rounded-full bg-[var(--primary)]/15 text-[var(--primary)] text-xs font-bold">
                            {{ sortedChapters.length }} Chapters
                        </span>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mt-1 max-w-xl">
                        Define and customize manual chapter boundaries (e.g. Chapter 1: 1–100, Chapter 2: 101–180).
                        All page text, chunks, and AI reader chat automatically synchronize to each page range.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <button
                        type="button"
                        @click="openSplitModal"
                        class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-[var(--border)] bg-[var(--surface2)] hover:border-[var(--primary)] text-xs font-bold text-[var(--text)] transition cursor-pointer"
                    >
                        <ScissorsIcon class="w-4 h-4 text-[var(--secondary)]" />
                        <span>Quick Split Tool</span>
                    </button>
                    <button
                        type="button"
                        @click="openAddChapterModal"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-xs font-bold text-white shadow-md shadow-orange-500/20 transition cursor-pointer"
                    >
                        <PlusIcon class="w-4 h-4" />
                        <span>Add Chapter</span>
                    </button>
                </div>
            </div>

            <!-- Visual Page Coverage Bar -->
            <div class="mb-6 p-4 rounded-xl border border-[var(--border)] bg-[var(--surface2)]/40">
                <div class="flex items-center justify-between text-xs font-bold mb-2">
                    <span class="text-[var(--text)] flex items-center gap-1.5">
                        <BookOpenIcon class="w-4 h-4 text-orange-500" />
                        <span>Book Page Coverage: {{ coveredPagesCount }} of {{ totalBookPages }} Pages ({{ coveragePercent }}%)</span>
                    </span>
                    <span v-if="coveredPagesCount < totalBookPages" class="text-amber-500 flex items-center gap-1">
                        <ExclamationTriangleIcon class="w-3.5 h-3.5" />
                        <span>{{ totalBookPages - coveredPagesCount }} pages unassigned</span>
                    </span>
                    <span v-else class="text-emerald-500 flex items-center gap-1">
                        <CheckCircleIcon class="w-3.5 h-3.5" />
                        <span>Full book covered</span>
                    </span>
                </div>

                <!-- Multi-segment visual progress bar -->
                <div class="h-4 rounded-lg bg-[var(--surface2)] overflow-hidden flex gap-0.5 p-0.5 border border-[var(--border)]">
                    <div
                        v-for="(c, idx) in sortedChapters"
                        :key="c.id"
                        class="h-full rounded-sm transition-all relative group cursor-pointer"
                        :class="[
                            idx % 4 === 0 ? 'bg-orange-500' : '',
                            idx % 4 === 1 ? 'bg-indigo-500' : '',
                            idx % 4 === 2 ? 'bg-emerald-500' : '',
                            idx % 4 === 3 ? 'bg-amber-500' : '',
                        ]"
                        :style="{ width: `${Math.max(1, (( (c.end_page || c.start_page || 1) - (c.start_page || 1) + 1 ) / totalBookPages) * 100)}%` }"
                        :title="`${c.chapter_number ? 'Ch ' + c.chapter_number + ': ' : ''}${c.title} (pp. ${c.start_page}-${c.end_page})`"
                        @click="openEditChapterModal(c)"
                    />
                </div>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-[11px] text-[var(--text-muted)]">
                    <div v-for="(c, idx) in sortedChapters.slice(0, 5)" :key="c.id" class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full"
                            :class="[
                                idx % 4 === 0 ? 'bg-orange-500' : '',
                                idx % 4 === 1 ? 'bg-indigo-500' : '',
                                idx % 4 === 2 ? 'bg-emerald-500' : '',
                                idx % 4 === 3 ? 'bg-amber-500' : '',
                            ]"
                        />
                        <span class="truncate max-w-[120px]">{{ c.title }}</span>
                        <span class="font-mono text-[10px]">({{ c.start_page }}–{{ c.end_page }})</span>
                    </div>
                    <span v-if="sortedChapters.length > 5" class="text-[10px] text-[var(--text-muted)] italic">
                        +{{ sortedChapters.length - 5 }} more
                    </span>
                </div>
            </div>

            <!-- Chapters Table -->
            <div v-if="!sortedChapters.length" class="text-center py-12 rounded-xl border border-dashed border-[var(--border)] bg-[var(--surface2)]/20">
                <BookOpenIcon class="w-10 h-10 text-[var(--text-muted)] mx-auto mb-2 opacity-50" />
                <p class="text-sm font-bold text-[var(--text)]">No chapters defined yet</p>
                <p class="text-xs text-[var(--text-muted)] mt-1 mb-4">You can manually add chapters or use the quick split tool to divide pages automatically.</p>
                <button
                    type="button"
                    @click="openAddChapterModal"
                    class="px-4 py-2 rounded-xl bg-[var(--primary)] text-white text-xs font-bold"
                >
                    Add Chapter 1
                </button>
            </div>

            <div v-else class="overflow-x-auto rounded-xl border border-[var(--border)]">
                <table class="w-full text-left text-xs text-[var(--text)]">
                    <thead class="border-b border-[var(--border)] bg-[var(--surface2)]/60 text-[10px] uppercase font-bold text-[var(--text-muted)]">
                        <tr>
                            <th class="py-3 px-4 w-16">#</th>
                            <th class="py-3 px-4">Chapter Title</th>
                            <th class="py-3 px-4">Page Range</th>
                            <th class="py-3 px-4">Pages Count</th>
                            <th class="py-3 px-4">Price (৳)</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr
                            v-for="c in sortedChapters"
                            :key="c.id"
                            class="hover:bg-[var(--surface2)]/40 transition"
                        >
                            <td class="py-3 px-4 font-mono font-bold text-[var(--text-muted)]">
                                {{ c.chapter_number ? `${toEnglishDigits(c.chapter_number)} (${toBengaliDigits(c.chapter_number)})` : '—' }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-sm text-[var(--text)]">{{ c.title }}</div>
                                <div class="text-[10px] text-[var(--text-muted)] font-mono">{{ c.slug }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-[var(--surface2)] border border-[var(--border)] font-mono text-xs font-bold">
                                    {{ c.start_page }} → {{ c.end_page }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-medium text-slate-600 dark:text-slate-300">
                                    {{ (c.end_page || c.start_page || 1) - (c.start_page || 1) + 1 }} pages
                                </span>
                            </td>
                            <td class="py-3 px-4 font-medium">
                                <span v-if="c.price !== null" class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold">
                                    ৳{{ c.price }}
                                </span>
                                <span v-else class="text-[var(--text-muted)]">—</span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <button
                                    type="button"
                                    @click="openEditChapterModal(c)"
                                    class="p-1.5 rounded-lg border border-[var(--border)] bg-[var(--surface)] hover:border-[var(--primary)] hover:text-[var(--primary)] transition cursor-pointer"
                                    title="Edit chapter"
                                >
                                    <PencilSquareIcon class="w-4 h-4" />
                                </button>
                                <button
                                    type="button"
                                    @click="deleteChapter(c)"
                                    class="p-1.5 rounded-lg border border-red-500/30 bg-red-500/10 text-red-500 hover:bg-red-500/20 transition cursor-pointer"
                                    title="Delete chapter"
                                >
                                    <TrashIcon class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: ADD / EDIT CHAPTER -->
        <Teleport to="body">
            <div
                v-if="showChapterModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
            >
                <div class="w-full max-w-md rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
                        <h3 class="font-heading text-lg font-bold text-[var(--text)]">
                            {{ editingChapterId ? 'Edit Chapter' : 'Add New Chapter' }}
                        </h3>
                        <button
                            type="button"
                            @click="showChapterModal = false"
                            class="p-1 rounded-lg text-[var(--text-muted)] hover:text-[var(--text)] cursor-pointer"
                        >
                            <XMarkIcon class="w-5 h-5" />
                        </button>
                    </div>

                    <form @submit.prevent="saveChapter" class="space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-[var(--text)] mb-1">Chapter Title *</label>
                            <input
                                v-model="chapterForm.title"
                                type="text"
                                placeholder="e.g. অধ্যায় ১: ভৌত জগত ও পরিমাপ"
                                required
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm focus:outline-none focus:border-[var(--primary)]"
                            />
                            <p v-if="chapterForm.errors.title" class="text-red-500 mt-1 text-[11px]">{{ chapterForm.errors.title }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-[var(--text)] mb-1">Chapter Number</label>
                                <input
                                    v-model="chapterForm.chapter_number"
                                    type="text"
                                    inputmode="numeric"
                                    placeholder="1, 2, 3... বা ১, ২, ৩..."
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm focus:outline-none focus:border-[var(--primary)]"
                                />
                                <p class="text-[11px] text-[var(--text-muted)] mt-0.5">বাংলা বা ইংরেজি সংখ্যা (যেমন: 1 বা ১)</p>
                            </div>
                            <div>
                                <label class="block font-bold text-[var(--text)] mb-1">Price (৳ Optional)</label>
                                <input
                                    v-model.number="chapterForm.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="e.g. 20"
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm focus:outline-none focus:border-[var(--primary)]"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-[var(--text)] mb-1">Start Page *</label>
                                <input
                                    v-model.number="chapterForm.start_page"
                                    type="number"
                                    min="1"
                                    :max="totalBookPages"
                                    required
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm font-mono font-bold focus:outline-none focus:border-[var(--primary)]"
                                />
                                <p v-if="chapterForm.errors.start_page" class="text-red-500 mt-1 text-[11px]">{{ chapterForm.errors.start_page }}</p>
                            </div>
                            <div>
                                <label class="block font-bold text-[var(--text)] mb-1">End Page *</label>
                                <input
                                    v-model.number="chapterForm.end_page"
                                    type="number"
                                    :min="chapterForm.start_page"
                                    :max="totalBookPages"
                                    required
                                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm font-mono font-bold focus:outline-none focus:border-[var(--primary)]"
                                />
                                <p v-if="chapterForm.errors.end_page" class="text-red-500 mt-1 text-[11px]">{{ chapterForm.errors.end_page }}</p>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-orange-500/10 border border-orange-500/20 text-[11px] text-orange-700 dark:text-orange-300">
                            <strong>Sync Guarantee:</strong> Saving this chapter will automatically attach pages {{ chapterForm.start_page }} to {{ chapterForm.end_page }} to this chapter for OCR text, RAG chunks, and AI reader chat.
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-[var(--border)]">
                            <button
                                type="button"
                                @click="showChapterModal = false"
                                class="px-4 py-2 rounded-xl border border-[var(--border)] hover:bg-[var(--surface2)] font-semibold cursor-pointer"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="chapterForm.processing"
                                class="px-5 py-2 rounded-xl bg-[var(--primary)] text-white font-bold disabled:opacity-60 cursor-pointer"
                            >
                                {{ chapterForm.processing ? 'Saving…' : (editingChapterId ? 'Update Chapter' : 'Create Chapter') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- MODAL: QUICK SPLIT TOOL -->
        <Teleport to="body">
            <div
                v-if="showSplitModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
            >
                <div class="w-full max-w-md rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
                        <div class="flex items-center gap-2">
                            <ScissorsIcon class="w-5 h-5 text-orange-500" />
                            <h3 class="font-heading text-lg font-bold text-[var(--text)]">Quick Split Book</h3>
                        </div>
                        <button
                            type="button"
                            @click="showSplitModal = false"
                            class="p-1 rounded-lg text-[var(--text-muted)] hover:text-[var(--text)] cursor-pointer"
                        >
                            <XMarkIcon class="w-5 h-5" />
                        </button>
                    </div>

                    <form @submit.prevent="applyQuickSplit" class="space-y-4 text-xs">
                        <p class="text-[var(--text-muted)] leading-relaxed">
                            Automatically divide all <strong>{{ totalBookPages }}</strong> pages into equal chapter ranges. Existing chapter mappings will be replaced with this clean division.
                        </p>

                        <div>
                            <label class="block font-bold text-[var(--text)] mb-1">Number of Chapters to Create</label>
                            <input
                                v-model.number="splitForm.num_chapters"
                                type="number"
                                min="2"
                                :max="Math.min(totalBookPages, 50)"
                                required
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm font-bold focus:outline-none focus:border-[var(--primary)]"
                            />
                            <p class="text-[11px] text-[var(--text-muted)] mt-1">
                                ~{{ Math.round(totalBookPages / (splitForm.num_chapters || 1)) }} pages per chapter
                            </p>
                        </div>

                        <div>
                            <label class="block font-bold text-[var(--text)] mb-1">Naming Prefix</label>
                            <input
                                v-model="splitForm.naming_prefix"
                                type="text"
                                placeholder="e.g. অধ্যায় or Chapter"
                                class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm focus:outline-none focus:border-[var(--primary)]"
                            />
                        </div>

                        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[11px] text-amber-700 dark:text-amber-300">
                            <strong>Note:</strong> You can edit any individual chapter's title, start page, or end page after splitting.
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-[var(--border)]">
                            <button
                                type="button"
                                @click="showSplitModal = false"
                                class="px-4 py-2 rounded-xl border border-[var(--border)] hover:bg-[var(--surface2)] font-semibold cursor-pointer"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold shadow-md shadow-orange-500/20 cursor-pointer"
                            >
                                Generate & Sync Chapters
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
