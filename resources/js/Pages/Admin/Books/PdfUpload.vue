<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';
import axios from 'axios';

const props = defineProps({ book: Object, current: Object });
const { t } = useI18n();

const fileInput = ref(null);
const pdfFileName = ref('');
const pdfUploadProgress = ref(0);
const pdfUploading = ref(false);
const pdfReady = ref(false);
const pdfError = ref('');

const form = useForm({ temp_pdf_path: null });

function formatBytes(bytes) {
    if (!bytes) return '—';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function statusBadge(status) {
    return {
        completed: 'bg-[var(--secondary)]/15 text-[var(--secondary)]',
        processing: 'bg-[var(--primary)]/15 text-[var(--primary)]',
        pending: 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]',
        failed: 'bg-[var(--accent)]/15 text-[var(--accent)]',
    }[status] ?? 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]';
}

function describeUploadError(err, fallback) {
    const data = err?.response?.data;
    const message = typeof data === 'string'
        ? data.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 200)
        : data?.message;

    if (message) return message;
    if (err?.response) return `The server returned HTTP ${err.response.status}.`;
    if (err?.code === 'ECONNABORTED' || err?.code === 'ETIMEDOUT') {
        return 'Timed out waiting for the server. Please try again.';
    }
    return fallback;
}

async function finalizeUpload(uploadId) {
    const res = await axios.post('/admin/books/merge-chunks', { upload_id: uploadId }, { timeout: 30000 });

    if (!res.data?.ok) {
        pdfError.value = res.data?.message || 'Failed to finalize upload.';
        return null;
    }

    if (res.data?.temp_path) {
        return res.data.temp_path;
    }

    for (let attempt = 0; attempt < 300; attempt++) {
        await new Promise((r) => setTimeout(r, 2000));

        try {
            const status = await axios.get(`/admin/books/merge-chunks/status/${uploadId}`, { timeout: 15000 });

            if (status.data?.status === 'done' && status.data?.ok) {
                return status.data.temp_path;
            }

            if (status.data?.status === 'error') {
                pdfError.value = status.data?.message || 'Failed to finalize upload.';
                return null;
            }
        } catch (err) {
            // Transient network blip — keep polling instead of failing the upload.
        }
    }

    pdfError.value = 'Timed out waiting for the server to finalize. Please try again.';
    return null;
}

async function handleFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;

    pdfError.value = '';
    pdfReady.value = false;
    form.temp_pdf_path = null;
    pdfFileName.value = file.name;

    pdfUploading.value = true;
    pdfUploadProgress.value = 0;

    const CHUNK_SIZE = 5 * 1024 * 1024;
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    const uploadId = crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2);

    for (let i = 0; i < totalChunks; i++) {
        const start = i * CHUNK_SIZE;
        const end = Math.min(start + CHUNK_SIZE, file.size);
        const chunk = file.slice(start, end);

        const formData = new FormData();
        formData.append('file', chunk, file.name);
        formData.append('upload_id', uploadId);
        formData.append('chunk_index', i);
        formData.append('total_chunks', totalChunks);
        formData.append('filename', file.name);

        try {
            const res = await axios.post('/admin/books/upload-chunk', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
                timeout: 120000,
            });

            if (!res.data?.ok) {
                pdfError.value = res.data?.message || `Chunk ${i + 1} was rejected by the server.`;
                pdfUploading.value = false;
                return;
            }
        } catch (err) {
            pdfError.value = describeUploadError(err, 'Upload failed. Please try again.');
            pdfUploading.value = false;
            return;
        }

        pdfUploadProgress.value = Math.round(((i + 1) / totalChunks) * 90);
    }

    const tempPath = await finalizeUpload(uploadId);

    if (tempPath) {
        form.temp_pdf_path = tempPath;
        pdfUploadProgress.value = 100;
        pdfReady.value = true;
    }

    pdfUploading.value = false;
}

function submit() {
    form.post(`/admin/books/${props.book.id}/pdf`, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`PDF — ${book.title}`" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <Link href="/admin/books" class="text-sm text-[var(--primary)] hover:underline">← Books</Link>
                <h1 class="font-heading text-2xl font-extrabold mt-1">{{ book.title }}</h1>
                <p class="text-sm text-[var(--text-muted)] mt-1">
                    {{ book.author?.name }} · {{ book.total_pages }} pages
                    · <span :class="book.pdf_path ? 'text-[var(--secondary)]' : 'text-[var(--text-muted)]'">{{ book.pdf_path ? 'PDF attached' : 'No PDF yet' }}</span>
                </p>
            </div>
            <Link :href="`/admin/books/${book.id}`" class="px-4 py-2 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-sm font-medium">
                Book details
            </Link>
        </div>

        <!-- Processing status -->
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

            <p class="text-sm text-[var(--text-muted)]">
                {{ book.processing_status === 'failed'
                    ? 'Uploading a new PDF below will replace the failed file and start a fresh run.'
                    : 'Upload your PDF and press “Upload & Start Processing”. It is processed in the background and may take a few minutes for large files — you can leave this page and check the book page for progress.' }}
            </p>

            <div v-if="book.processing_status === 'failed' && book.processing_error" class="rounded-lg bg-[var(--accent)]/10 border border-[var(--accent)]/30 p-4 mt-4">
                <pre class="text-xs whitespace-pre-wrap text-[var(--text-muted)]">{{ book.processing_error }}</pre>
            </div>
        </div>

        <!-- Upload -->
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <h2 class="font-heading text-lg font-bold mb-4">Upload PDF</h2>

            <p v-if="current" class="text-sm text-[var(--text-muted)] mb-4">
                Current file: <span class="font-medium text-[var(--text-strong)]">{{ current.filename }}</span> ({{ formatBytes(current.size) }})
                — uploading a new file replaces it.
            </p>

            <div>
                <input
                    ref="fileInput"
                    @change="handleFileSelect"
                    type="file"
                    accept="application/pdf"
                    class="w-full text-sm"
                    :disabled="pdfUploading"
                />
                <p class="text-xs text-[var(--text-muted)] mt-1">
                    Up to ~500 MB via 5 MB chunks. The file stays on the private disk.
                </p>
            </div>

            <div v-if="pdfUploading || pdfReady" class="mt-4">
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-medium text-[var(--text-muted)]">{{ pdfFileName }}</span>
                    <span :class="pdfReady ? 'text-[var(--secondary)]' : 'text-[var(--text-muted)]'">{{ pdfUploadProgress }}%</span>
                </div>
                <div class="w-full h-2 rounded-full bg-[var(--surface2)] overflow-hidden">
                    <div
                        class="h-full rounded-full transition-all duration-300 ease-out"
                        :class="pdfReady ? 'bg-[var(--secondary)]' : 'bg-[var(--primary)]'"
                        :style="{ width: pdfUploadProgress + '%' }"
                    ></div>
                </div>
                <p v-if="pdfUploading" class="text-[10px] text-[var(--text-muted)] mt-1">Uploading in the background…</p>
                <p v-if="pdfReady" class="text-[10px] text-[var(--secondary)] mt-1">✓ PDF staged. Click the button below to start processing.</p>
            </div>

            <p v-if="pdfError" class="text-xs text-[var(--accent)] mt-2">{{ pdfError }}</p>
            <p v-if="form.errors.temp_pdf_path" class="text-xs text-[var(--accent)] mt-2">{{ form.errors.temp_pdf_path }}</p>

            <div class="mt-5 flex items-center gap-4">
                <button
                    type="button"
                    @click="submit"
                    :disabled="!pdfReady || pdfUploading || form.processing"
                    class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-50"
                >
                    {{ form.processing ? 'Starting…' : (book.pdf_path ? 'Replace & Reprocess PDF' : 'Upload & Start Processing') }}
                </button>
                <p class="text-xs text-[var(--text-muted)]">
                    Submitting queues a background job (takes a few minutes). Chunks in temp/ are swept automatically.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>