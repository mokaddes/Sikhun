<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

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

// Chapter tree: nest sections under their parent chapter.
const chapterTree = (() => {
    const tops = props.chapters.filter((c) => c.parent_id === null);
    return tops.map((top) => ({
        ...top,
        children: props.chapters.filter((c) => c.parent_id === top.id),
    }));
})();

const embeddingCoverage = props.stats.chunks > 0
    ? Math.round((props.stats.chunks_with_embeddings / props.stats.chunks) * 100)
    : 0;
</script>

<template>
    <Head :title="book.title" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="font-heading text-2xl font-extrabold">{{ book.title }}</h1>
                <p class="text-sm text-[var(--text-muted)] mt-1">{{ book.author?.name }} · {{ book.total_pages }} pages</p>
            </div>
            <div class="flex gap-3">
                <Link :href="`/admin/books/${book.id}/pdf`" class="px-4 py-2 rounded-lg bg-[var(--secondary)]/15 text-[var(--secondary)] text-sm font-medium hover:bg-[var(--secondary)]/25">
                    {{ book.pdf_path ? 'Replace PDF' : 'Upload PDF' }}
                </Link>
                <Link :href="`/admin/books/${book.id}/edit`" class="px-4 py-2 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-sm font-medium">
                    {{ t('common.edit') }}
                </Link>
                <button
                    v-if="book.processing_status === 'failed' || book.processing_status === 'completed'"
                    @click="retryProcessing"
                    :disabled="retrying"
                    class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60"
                >
                    {{ retrying ? '…' : (book.processing_status === 'failed' ? t('admin.books.retry_processing') : t('admin.books.reprocess')) }}
                </button>
            </div>
        </div>

        <!-- PDF Processing -->
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <h2 class="font-heading text-lg font-bold">{{ t('admin.books.pdf_processing') }}</h2>
                <span class="px-2.5 py-0.5 rounded text-xs font-semibold" :class="statusBadge(book.processing_status)">
                    {{ book.processing_status === 'completed' ? '✓ ' + t('admin.books.status_completed')
                        : book.processing_status === 'failed' ? '❌ ' + t('admin.books.status_failed')
                        : book.processing_status === 'processing' ? '⏳ ' + t('admin.books.status_processing')
                        : t('admin.books.status_pending') }}
                </span>
            </div>

            <p v-if="book.parser_name" class="text-xs text-[var(--text-muted)] mb-3">
                {{ t('admin.books.parser') }}: {{ book.parser_name }} {{ book.parser_version }}
                · {{ t('admin.books.parsed_at') }}: {{ book.parsed_at ?? '—' }}
            </p>

            <div v-if="book.processing_status === 'failed' && book.processing_error" class="rounded-lg bg-[var(--accent)]/10 border border-[var(--accent)]/30 p-4 mb-4">
                <p class="text-sm text-[var(--accent)] font-medium mb-1">{{ t('admin.books.processing_failed') }}</p>
                <pre class="text-xs whitespace-pre-wrap text-[var(--text-muted)]">{{ book.processing_error }}</pre>
                <button @click="retryProcessing" :disabled="retrying" class="mt-3 px-4 py-2 rounded-lg bg-[var(--accent)] text-white text-sm font-semibold disabled:opacity-60">
                    {{ t('admin.books.retry_processing') }}
                </button>
            </div>

            <div class="grid grid-cols-3 md:grid-cols-7 gap-4 text-center">
                <div><div class="text-2xl font-heading font-extrabold">{{ stats.pages }}</div><div class="text-xs text-[var(--text-muted)]">{{ t('admin.books.pages') }}</div></div>
                <div><div class="text-2xl font-heading font-extrabold">{{ stats.chapters }}</div><div class="text-xs text-[var(--text-muted)]">{{ t('admin.books.chapters') }}</div></div>
                <div><div class="text-2xl font-heading font-extrabold">{{ stats.tables }}</div><div class="text-xs text-[var(--text-muted)]">{{ t('admin.books.tables') }}</div></div>
                <div><div class="text-2xl font-heading font-extrabold">{{ stats.images }}</div><div class="text-xs text-[var(--text-muted)]">{{ t('admin.books.images') }}</div></div>
                <div><div class="text-2xl font-heading font-extrabold">{{ stats.formulas }}</div><div class="text-xs text-[var(--text-muted)]">{{ t('admin.books.formulas') }}</div></div>
                <div><div class="text-2xl font-heading font-extrabold">{{ stats.chunks }}</div><div class="text-xs text-[var(--text-muted)]">{{ t('admin.books.chunks') }}</div></div>
                <div><div class="text-2xl font-heading font-extrabold">{{ stats.elements }}</div><div class="text-xs text-[var(--text-muted)]">{{ t('admin.books.elements') }}</div></div>
            </div>
        </div>

        <!-- RAG -->
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 mb-6">
            <h2 class="font-heading text-lg font-bold mb-4">RAG</h2>
            <div class="flex items-center gap-4">
                <span class="px-2.5 py-0.5 rounded text-xs font-semibold"
                    :class="stats.chunks > 0 ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                    {{ stats.chunks > 0 ? '✓ ' + t('admin.books.indexed') : t('admin.books.status_pending') }}
                </span>
                <div class="flex-1">
                    <div class="h-2 rounded-full bg-[var(--surface2)] overflow-hidden">
                        <div class="h-full bg-[var(--primary)]" :style="{ width: embeddingCoverage + '%' }" />
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mt-1">
                        {{ t('admin.books.embeddings') }}: {{ stats.chunks_with_embeddings }} / {{ stats.chunks }} ({{ embeddingCoverage }}%)
                    </p>
                </div>
            </div>
        </div>

        <!-- Chapters -->
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading text-lg font-bold">{{ t('admin.books.chapters') }}</h2>
                <div class="flex gap-2">
                    <button
                        v-if="!editingPrices && chapters && chapters.length"
                        type="button"
                        @click="editingPrices = true"
                        class="px-3 py-1.5 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-xs font-semibold"
                    >
                        Edit Chapter Prices
                    </button>
                    <button
                        v-if="editingPrices"
                        type="button"
                        @click="editingPrices = false"
                        class="px-3 py-1.5 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-xs font-semibold"
                    >
                        Cancel
                    </button>
                    <button
                        v-if="editingPrices"
                        type="button"
                        @click="savePrices"
                        :disabled="chapterPriceForm.processing"
                        class="px-3 py-1.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-xs font-semibold disabled:opacity-60"
                    >
                        {{ chapterPriceForm.processing ? 'Saving...' : 'Save Prices' }}
                    </button>
                </div>
            </div>
            <div v-if="!chapterTree.length" class="text-sm text-[var(--text-muted)]">{{ t('common.no_results') }}</div>
            <ul v-else class="space-y-2 text-sm">
                <li v-for="chapter in chapterTree" :key="chapter.id" class="p-2 rounded-lg hover:bg-[var(--surface2)]/50">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="font-medium">{{ chapter.chapter_number ? chapter.chapter_number + ' — ' : '' }}{{ chapter.title }}</span>
                            <span class="text-xs text-[var(--text-muted)]">pp. {{ chapter.start_page ?? '?' }}–{{ chapter.end_page ?? '?' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <template v-if="editingPrices">
                                <span class="text-xs text-[var(--text-muted)]">৳</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="Price (৳)"
                                    v-model="chapterPriceForm.chapters.find((c) => c.id === chapter.id).price"
                                    class="w-24 px-2 py-1 text-xs rounded border border-[var(--border)] bg-[var(--surface2)]"
                                />
                            </template>
                            <span v-else-if="chapter.price !== null" class="text-xs px-2 py-0.5 rounded font-bold bg-[var(--primary)]/10 text-[var(--primary)]">৳{{ chapter.price }}</span>
                        </div>
                    </div>
                    <ul v-if="chapter.children.length" class="ml-6 mt-1 space-y-1 text-[var(--text-muted)]">
                        <li v-for="child in chapter.children" :key="child.id" class="flex items-center gap-2">
                            <span>{{ child.chapter_number ? child.chapter_number + ' ' : '' }}{{ child.title }}</span>
                            <span class="text-xs">pp. {{ child.start_page ?? '?' }}–{{ child.end_page ?? '?' }}</span>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </AdminLayout>
</template>
