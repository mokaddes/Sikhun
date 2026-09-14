<script setup>
import { watch, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';
import axios from 'axios';

const props = defineProps({ book: Object, authors: Array, publications: Array, categories: Array });
const { t } = useI18n();
const isEdit = !!props.book;

const form = useForm({
    title: props.book?.title ?? '',
    slug: props.book?.slug ?? '',
    description: props.book?.description ?? '',
    author_id: props.book?.author_id ?? null,
    publication_id: props.book?.publication_id ?? null,
    category_id: props.book?.category_id ?? null,
    subject: props.book?.subject ?? '',
    level: props.book?.level ?? 'hsc',
    price: props.book?.price ?? 0,
    is_free: props.book?.is_free ?? false,
    total_pages: props.book?.total_pages ?? 0,
    is_published: props.book?.is_published ?? false,
    is_premium_gift: props.book?.is_premium_gift ?? false,
    chapter_purchase_enabled: props.book?.chapter_purchase_enabled ?? false,
    cover_image: null,
    pdf_file: null,
    temp_pdf_path: null,
});

let slugTouched = isEdit;
watch(() => form.title, (val) => {
    if (!slugTouched) form.slug = val.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
});

const coverInput = ref(null);
const pdfInput = ref(null);

const pdfUploadProgress = ref(0);
const pdfUploading = ref(false);
const pdfUploaded = ref(false);
const pdfError = ref('');
const pdfFileName = ref('');

function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

/**
 * Turn a failed upload request into something worth showing an admin.
 *
 * Two cases used to collapse into one meaningless message: a request that
 * never reached Laravel (dropped connection, browser timeout) has no response
 * at all, and a deployment with APP_DEBUG=false may answer a failure with an
 * HTML error page instead of JSON, so there is no `message` field to read.
 * Falling back to the HTTP status — or to a plain-English cause — keeps the
 * real reason visible instead of blaming the merge step for everything.
 */
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

async function handlePdfSelect(e) {
    const file = e.target.files[0];
    if (!file) return;

    pdfError.value = '';
    pdfUploaded.value = false;
    form.temp_pdf_path = null;
    form.pdf_file = null;
    pdfFileName.value = file.name;

    await startChunkedUpload(file);
}

async function finalizeUpload(uploadId) {
    // Start the merge. Merging now runs in a queue job, so this request only
    // returns fast and can't be killed by a PHP/proxy timeout mid-concat.
    const res = await axios.post('/admin/books/merge-chunks', { upload_id: uploadId }, { timeout: 30000 });

    if (!res.data?.ok) {
        pdfError.value = res.data?.message || 'Failed to finalize upload.';
        return null;
    }

    // Retry of an upload a previous run already finished → file returned now.
    if (res.data?.temp_path) {
        return res.data.temp_path;
    }

    // Background merge — poll until done/error. Transient poll failures are
    // tolerated; the queued job always completes server-side.
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

async function startChunkedUpload(file) {
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

            // axios only rejects on transport errors, so a chunk the server
            // refused (422/419/500) resolved here and looked like success —
            // the upload then marched on to merge() with pieces missing and
            // the admin only ever saw the merge step's generic complaint.
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
        pdfUploaded.value = true;
    }

    pdfUploading.value = false;
}

function submit() {
    if (!pdfUploading.value && !pdfUploaded.value && !form.pdf_file && !isEdit) {
        // no pdf selected is fine — book will be created without a pdf
    }

    if (isEdit) {
        form.transform((data) => ({ ...data, _method: 'put' }))
            .post(`/admin/books/${props.book.id}`, { forceFormData: true });
    } else {
        form.post('/admin/books', { forceFormData: true });
    }
}
</script>

<template>
    <Head :title="isEdit ? t('admin.books.edit') : t('admin.books.new')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('admin.books.edit') : t('admin.books.new') }}</h1>

        <form @submit.prevent="submit" class="max-w-2xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('common.title') }}</label>
                <input v-model="form.title" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input v-model="form.slug" @input="slugTouched = true" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                <p v-if="form.errors.slug" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.slug }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.description') }}</label>
                <textarea v-model="form.description" rows="4" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.author') }}</label>
                    <select v-model="form.author_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option :value="null">—</option>
                        <option v-for="a in authors" :key="a.id" :value="a.id">{{ a.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.publication') }}</label>
                    <select v-model="form.publication_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option :value="null">—</option>
                        <option v-for="p in publications" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.category') }}</label>
                    <select v-model="form.category_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option :value="null">—</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.subject') }}</label>
                    <input v-model="form.subject" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('common.level') }}</label>
                    <select v-model="form.level" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option value="ssc">SSC</option>
                        <option value="hsc">HSC</option>
                        <option value="university">University</option>
                        <option value="job">Job</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('common.price') }} (৳)</label>
                    <input v-model="form.price" type="number" step="0.01" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
            </div>

            <div class="flex flex-wrap gap-6 text-sm">
                <label class="flex items-center gap-2"><input v-model="form.is_free" type="checkbox" class="rounded" /> {{ t('admin.books.is_free') }}</label>
                <label class="flex items-center gap-2"><input v-model="form.is_published" type="checkbox" class="rounded" /> {{ t('admin.books.is_published') }}</label>
                <label class="flex items-center gap-2"><input v-model="form.is_premium_gift" type="checkbox" class="rounded" /> Premium gift book</label>
                <label class="flex items-center gap-2"><input v-model="form.chapter_purchase_enabled" type="checkbox" class="rounded" /> {{ t('admin.books.chapter_purchase') }}</label>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.cover') }}</label>
                    <input ref="coverInput" @input="form.cover_image = coverInput.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                    <img v-if="book?.cover_image_url" :src="book.cover_image_url" class="mt-2 h-24 rounded-lg object-cover" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.pdf') }}</label>
                    <input
                        ref="pdfInput"
                        @change="handlePdfSelect"
                        type="file"
                        accept="application/pdf"
                        class="w-full text-sm"
                        :disabled="pdfUploading"
                    />
                    <p class="text-xs text-[var(--text-muted)] mt-1">{{ t('admin.books.pdf_note') }}</p>
                    <p v-if="book?.pdf_path && !pdfUploaded" class="text-xs text-[var(--secondary)] mt-1">&#10003; File already uploaded</p>

                    <div v-if="pdfUploading || pdfUploaded" class="mt-3">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-[var(--text-muted)]">
                                {{ pdfFileName }}
                            </span>
                            <span :class="pdfUploaded ? 'text-[var(--secondary)]' : 'text-[var(--text-muted)]'">
                                {{ pdfUploadProgress }}%
                            </span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-[var(--surface2)] overflow-hidden">
                            <div
                                class="h-full rounded-full transition-all duration-300 ease-out"
                                :class="pdfUploaded ? 'bg-[var(--secondary)]' : 'bg-[var(--primary)]'"
                                :style="{ width: pdfUploadProgress + '%' }"
                            ></div>
                        </div>
                        <p v-if="pdfUploading" class="text-[10px] text-[var(--text-muted)] mt-1">Uploading in background &mdash; you can fill other fields</p>
                        <p v-if="pdfUploaded" class="text-[10px] text-[var(--secondary)] mt-1">&#10003; PDF ready</p>
                    </div>

                    <p v-if="pdfError" class="text-xs text-[var(--accent)] mt-1">{{ pdfError }}</p>
                </div>
            </div>

            <button
                type="submit"
                :disabled="form.processing || pdfUploading"
                class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60"
            >
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
