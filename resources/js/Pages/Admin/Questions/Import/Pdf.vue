<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import axios from 'axios';
import {
    ArrowUpTrayIcon,
    DocumentTextIcon,
    SparklesIcon,
    ArrowLeftIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    AcademicCapIcon,
    ArrowPathIcon,
    TrashIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    batches: Array,
    sources: Array,
});

const isDragging = ref(false);
const fileInput = ref(null);
const selectedFileName = ref('');
const fileSizeFormatted = ref('');

// Chunked upload states
const uploadProgress = ref(0);
const isUploading = ref(false);
const isMerging = ref(false);
const uploadReady = ref(false);
const uploadError = ref('');
const currentChunk = ref(0);
const totalChunksCount = ref(0);

const form = useForm({
    pdf_file: null,
    temp_pdf_path: null,
    title: '',
    segment: 'admission',
    origin_tag: 'DU-24',
    institution: '',
    year: new Date().getFullYear(),
    source_id: '',
});

function formatBytes(bytes) {
    if (!bytes) return '—';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

async function finalizeUpload(uploadId) {
    isMerging.value = true;
    try {
        const res = await axios.post('/admin/question-import/merge-chunks', { upload_id: uploadId }, { timeout: 35000 });

        if (!res.data?.ok) {
            uploadError.value = res.data?.message || 'ফাইল মার্জ করতে সমস্যা হয়েছে।';
            return null;
        }

        if (res.data?.temp_path) {
            return res.data.temp_path;
        }

        // Poll merge status
        for (let attempt = 0; attempt < 60; attempt++) {
            await new Promise((r) => setTimeout(r, 1500));
            try {
                const status = await axios.get(`/admin/question-import/merge-chunks/status/${uploadId}`, { timeout: 10000 });
                if (status.data?.status === 'done' && status.data?.ok) {
                    return status.data.temp_path;
                }
                if (status.data?.status === 'error') {
                    uploadError.value = status.data?.message || 'ফাইল মার্জিং ব্যর্থ হয়েছে।';
                    return null;
                }
            } catch (e) {
                // Network blip, keep polling
            }
        }

        uploadError.value = 'সার্ভার রেসপন্স দিতে সময় নিচ্ছে। অনুগ্রহ করে আবার চেষ্টা করুন।';
        return null;
    } catch (err) {
        uploadError.value = err?.response?.data?.message || err?.message || 'ফাইল একত্রীকরণ ব্যর্থ হয়েছে।';
        return null;
    } finally {
        isMerging.value = false;
    }
}

async function startUpload(file) {
    if (!file) return;

    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        uploadError.value = 'শুধুমাত্র PDF ফাইল আপলোড করা যাবে।';
        return;
    }

    uploadError.value = '';
    uploadReady.value = false;
    form.temp_pdf_path = null;
    form.pdf_file = null;
    selectedFileName.value = file.name;
    fileSizeFormatted.value = formatBytes(file.size);

    if (!form.title) {
        form.title = file.name.replace(/\.[^/.]+$/, '');
    }

    // Use chunked upload for all files (reliable 5MB chunks)
    isUploading.value = true;
    uploadProgress.value = 0;

    const CHUNK_SIZE = 5 * 1024 * 1024;
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    totalChunksCount.value = totalChunks;
    const uploadId = crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2);

    try {
        for (let i = 0; i < totalChunks; i++) {
            currentChunk.value = i + 1;
            const start = i * CHUNK_SIZE;
            const end = Math.min(file.size, start + CHUNK_SIZE);
            const chunk = file.slice(start, end);

            const formData = new FormData();
            formData.append('file', chunk, file.name);
            formData.append('upload_id', uploadId);
            formData.append('chunk_index', i);
            formData.append('total_chunks', totalChunks);
            formData.append('filename', file.name);

            const res = await axios.post('/admin/question-import/upload-chunk', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
                timeout: 60000,
            });

            if (!res.data?.ok) {
                throw new Error('সার্ভারে ফাইল চাঙ্ক আপলোড ব্যর্থ হয়েছে।');
            }

            uploadProgress.value = Math.round(((i + 1) / totalChunks) * 100);
        }

        // Finalize merge
        const tempPath = await finalizeUpload(uploadId);
        if (!tempPath) {
            throw new Error(uploadError.value || 'আপলোড করা ফাইল মার্জ করতে সমস্যা হয়েছে।');
        }

        form.temp_pdf_path = tempPath;
        uploadReady.value = true;
    } catch (err) {
        uploadError.value = err?.response?.data?.message || err?.message || 'আপলোড ব্যর্থ হয়েছে। আবার চেষ্টা করুন।';
        uploadReady.value = false;
    } finally {
        isUploading.value = false;
    }
}

function handleFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        startUpload(file);
    }
}

function handleDrop(event) {
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    if (file) {
        startUpload(file);
    }
}

function submit() {
    if (!form.temp_pdf_path && !form.pdf_file) {
        uploadError.value = 'অনুগ্রহ করে প্রথমে একটি PDF ফাইল আপলোড করুন।';
        return;
    }

    form.post('/admin/question-import/pdf', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('pdf_file', 'temp_pdf_path', 'title');
            selectedFileName.value = '';
            fileSizeFormatted.value = '';
            uploadReady.value = false;
            uploadProgress.value = 0;
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
    });
}

const reprocessId = ref(null);
function reprocessBatch(batchId) {
    reprocessId.value = batchId;
    router.post(`/admin/question-import/reprocess/${batchId}`, {}, {
        preserveScroll: true,
        onFinish: () => {
            reprocessId.value = null;
        },
    });
}

function deleteBatch(batchId) {
    if (confirm(`ব্যাচ #${batchId} সম্পূর্ণ মুছে ফেলতে চান?`)) {
        router.delete(`/admin/question-import/batch/${batchId}`, {
            preserveScroll: true,
        });
    }
}

// Auto-refresh when any batch is in processing/pending
let pollInterval = null;
onMounted(() => {
    pollInterval = setInterval(() => {
        const hasActive = props.batches?.some((b) => b.status === 'processing' || b.status === 'pending');
        if (hasActive) {
            router.reload({ only: ['batches'] });
        }
    }, 5000);
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});
</script>

<template>
    <AdminLayout>
        <Head title="পিডিএফ প্রশ্ন ও OCR ইমপোর্ট (PDF Question Ingestion)" />

        <div class="max-w-5xl mx-auto space-y-6 pb-12">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <Link
                        href="/admin/question-bank"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-primary mb-2 transition"
                    >
                        <ArrowLeftIcon class="w-4 h-4" />
                        <span>প্রশ্ন ভাণ্ডারে ফিরে যান</span>
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        পিডিএফ প্রশ্ন ও OCR ইমপোর্ট (PDF OCR Ingestion)
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        সংগৃহীত প্রশ্নপত্রের পিডিএফ আপলোড করুন। বইয়ের মতো নির্ভরযোগ্য Poppler + Tesseract OCR ও AI দিয়ে প্রশ্ন, অপশন, সঠিক উত্তর ও সমাধান তৈরি করা হবে।
                    </p>
                </div>

                <button
                    type="button"
                    @click="router.reload({ only: ['batches'] })"
                    class="p-2 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition"
                    title="রিলোড করুন"
                >
                    <ArrowPathIcon class="w-4 h-4" />
                </button>
            </div>

            <!-- Upload Card -->
            <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Drop Zone -->
                    <div
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop"
                        class="border-2 border-dashed rounded-2xl p-8 text-center transition-all cursor-pointer relative overflow-hidden"
                        :class="[
                            isDragging ? 'border-primary bg-primary/5' : 'border-gray-200 dark:border-gray-700 hover:border-primary/50 bg-gray-50/50 dark:bg-gray-900/30',
                            isUploading || isMerging ? 'pointer-events-none opacity-90' : ''
                        ]"
                        @click="!isUploading && !isMerging && $refs.fileInput.click()"
                    >
                        <input
                            ref="fileInput"
                            type="file"
                            accept="application/pdf"
                            class="hidden"
                            @change="handleFileChange"
                        />

                        <!-- Upload Progress Bar (Active during upload) -->
                        <div v-if="isUploading || isMerging" class="space-y-4 max-w-md mx-auto py-4">
                            <div class="w-12 h-12 mx-auto rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <ArrowPathIcon class="w-6 h-6 animate-spin" />
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ isMerging ? 'ফাইল একত্রীকরণ ও প্রস্তুতি চলছে...' : `চাঙ্ক আপলোড হচ্ছে (${currentChunk}/${totalChunksCount})` }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ selectedFileName }} ({{ fileSizeFormatted }})
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                <div
                                    class="bg-gradient-to-r from-primary to-indigo-600 h-2.5 rounded-full transition-all duration-300"
                                    :style="{ width: `${uploadProgress}%` }"
                                ></div>
                            </div>
                            <div class="text-xs font-semibold text-primary">
                                {{ isMerging ? 'সার্ভারে ভ্যালিডেশন হচ্ছে...' : `${uploadProgress}% সম্পন্ন` }}
                            </div>
                        </div>

                        <!-- Normal State -->
                        <div v-else class="flex flex-col items-center justify-center space-y-3">
                            <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <ArrowUpTrayIcon class="w-7 h-7" />
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ selectedFileName ? selectedFileName : 'প্রশ্নপত্রের PDF ফাইল ড্র্যাগ করে ছাড়ুন অথবা ক্লিক করে আপলোড করুন' }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    বড় সাইজের স্ক্যান করা PDF ফাইলও নির্ভরযোগ্য চাঙ্কড আপলোডের মাধ্যমে নির্বিঘ্নে আপলোড হবে
                                </div>
                            </div>

                            <span v-if="uploadReady" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <CheckCircleIcon class="w-4 h-4" />
                                <span>{{ selectedFileName }} ({{ fileSizeFormatted }}) প্রস্তুত!</span>
                            </span>
                        </div>
                    </div>

                    <!-- Upload Error Alert -->
                    <div v-if="uploadError" class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-700 dark:text-rose-300 flex items-start gap-2">
                        <ExclamationTriangleIcon class="w-4 h-4 flex-shrink-0 mt-0.5" />
                        <div>{{ uploadError }}</div>
                    </div>

                    <!-- Metadata Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Batch Title -->
                        <div class="sm:col-span-2 lg:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">ব্যাচ / প্রশ্নপত্রের শিরোনাম</label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="উদাঃ সহকারী যুব উন্নয়ন কর্মকর্তা প্রশ্নসমাধান"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Segment -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">সেগমেন্ট *</label>
                            <select
                                v-model="form.segment"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="job">সরকারি চাকরি ও ব্যাংক (Job)</option>
                                <option value="admission">বিশ্ববিদ্যালয় ভর্তি (Admission)</option>
                            </select>
                        </div>

                        <!-- Origin Tag -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">উৎস ট্যাগ (Origin Tag) *</label>
                            <input
                                v-model="form.origin_tag"
                                type="text"
                                required
                                placeholder="BCS-46, DYD-24, COMB-BANK-24, DU-24"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white uppercase font-bold"
                            />
                        </div>

                        <!-- Institution -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">প্রতিষ্ঠান / বিশ্ববিদ্যালয়</label>
                            <input
                                v-model="form.institution"
                                type="text"
                                placeholder="উদাঃ যুব উন্নয়ন অধিদপ্তর"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Year -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">বছর (Year)</label>
                            <input
                                v-model.number="form.year"
                                type="number"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Source Registry link -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">সোর্স রেজিস্ট্রি (ঐচ্ছিক)</label>
                            <select
                                v-model="form.source_id"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white"
                            >
                                <option value="">কোনো সোর্স নির্দিষ্ট নেই</option>
                                <option v-for="s in sources" :key="s.id" :value="s.id">
                                    {{ s.name }} ({{ s.institution || 'জাতীয়' }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Pipeline Info Note -->
                    <div class="p-4 bg-primary/[0.04] dark:bg-primary/[0.08] rounded-xl border border-primary/20 text-xs text-gray-700 dark:text-gray-300 space-y-1">
                        <div class="font-bold text-primary flex items-center gap-1.5">
                            <SparklesIcon class="w-4 h-4" />
                            <span>বুক আপলোডের মতো শক্তিশালী Poppler + Tesseract OCR পাইপলাইন:</span>
                        </div>
                        <p class="leading-relaxed text-gray-600 dark:text-gray-400">
                            পিডিএফ আপলোড সফল হওয়ার পর ব্যাকগ্রাউন্ডে মাল্টি-কোর Poppler + Tesseract (বাংলা+ইংরেজি) ইঞ্জিন স্বয়ংক্রিয়ভাবে স্ক্যান করা ছবি থেকে টেক্সট ও সূত্র রিড করবে। এরপর AI প্রতিটি প্রশ্ন, ৪টি অপশন, সঠিক উত্তর ও পুঙ্খানুপুঙ্খ ব্যাখ্যা তৈরি করে সরাসরি প্রশ্ন ব্যাংকে যুক্ত করবে।
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="submit"
                            :disabled="(!form.temp_pdf_path && !form.pdf_file) || form.processing || isUploading || isMerging"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary to-indigo-600 text-white text-xs font-bold hover:opacity-95 shadow-md shadow-primary/20 disabled:opacity-50 transition active:scale-95 flex items-center gap-2"
                        >
                            <SparklesIcon class="w-4 h-4" :class="{ 'animate-spin': form.processing }" />
                            <span>{{ form.processing ? 'ব্যাচ কিউতে যুক্ত হচ্ছে...' : 'পিডিএফ প্রসেস ও প্রশ্ন ব্যাংকে যুক্ত করুন' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ingestion History Batches -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <ClockIcon class="w-4 h-4 text-primary" />
                        <span>সাম্প্রতিক পিডিএফ ও ইমপোর্ট ব্যাচ হিস্ট্রি</span>
                    </h3>
                    <div class="text-xs text-gray-400">
                        মোট ব্যাচ: {{ batches?.length || 0 }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/75 dark:bg-gray-900/50 text-gray-500 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-5 py-3">ব্যাচ আইডি ও শিরোনাম</th>
                                <th class="px-4 py-3">উৎস ও ট্যাগ</th>
                                <th class="px-4 py-3 text-center">নিষ্কাশিত প্রশ্ন</th>
                                <th class="px-4 py-3 text-center">সফল প্রশ্ন</th>
                                <th class="px-4 py-3">স্ট্যাটাস</th>
                                <th class="px-5 py-3">তারিখ</th>
                                <th class="px-4 py-3 text-right">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                            <tr v-for="b in batches" :key="b.id" class="hover:bg-gray-50/60 dark:hover:bg-gray-700/20">
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-gray-900 dark:text-white">#{{ b.id }} {{ b.title }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">টাইপ: {{ b.batch_type }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-block px-2 py-0.5 rounded font-black text-[10px] bg-primary/10 text-primary">
                                        {{ b.meta?.origin_tag || 'PDF' }}
                                    </span>
                                    <span v-if="b.source" class="text-gray-400 text-[11px] ml-1">
                                        ({{ b.source.name }})
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold text-gray-800 dark:text-gray-200">
                                    {{ b.parsed_count || 0 }}
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ b.success_count || 0 }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="space-y-1">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold"
                                            :class="{
                                                'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300': b.status === 'completed',
                                                'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300': b.status === 'processing',
                                                'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300': b.status === 'failed',
                                                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': b.status === 'pending',
                                            }"
                                        >
                                            <span v-if="b.status === 'processing'" class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                            {{ b.status }}
                                        </span>
                                        <div v-if="b.error_log" class="text-[10px] text-rose-500 dark:text-rose-400 max-w-xs truncate" :title="b.error_log">
                                            {{ b.error_log }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-400 text-[11px] whitespace-nowrap">
                                    {{ new Date(b.created_at).toLocaleDateString('bn-BD') }}
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button
                                            v-if="b.status === 'failed' || b.status === 'pending'"
                                            type="button"
                                            @click="reprocessBatch(b.id)"
                                            :disabled="reprocessId === b.id"
                                            class="p-1.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 text-primary transition disabled:opacity-50"
                                            title="পুনরায় প্রসেস করুন"
                                        >
                                            <ArrowPathIcon class="w-3.5 h-3.5" :class="{ 'animate-spin': reprocessId === b.id }" />
                                        </button>
                                        <button
                                            type="button"
                                            @click="deleteBatch(b.id)"
                                            class="p-1.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-rose-500 hover:text-rose-700 transition"
                                            title="মুছে ফেলুন"
                                        >
                                            <TrashIcon class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!batches || batches.length === 0">
                                <td colspan="7" class="text-center py-8 text-gray-400">
                                    এখনো কোনো পিডিএফ আপলোড করা হয়নি।
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
