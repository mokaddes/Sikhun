<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import {
    ArrowUpTrayIcon,
    DocumentTextIcon,
    SparklesIcon,
    ArrowLeftIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    AcademicCapIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    batches: Array,
    sources: Array,
});

const isDragging = ref(false);
const selectedFileName = ref('');

const form = useForm({
    pdf_file: null,
    title: '',
    segment: 'admission',
    origin_tag: 'DU-24',
    institution: '',
    year: new Date().getFullYear(),
    source_id: '',
});

function handleFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        form.pdf_file = file;
        selectedFileName.value = file.name;
        if (!form.title) {
            form.title = file.name.replace(/\.[^/.]+$/, '');
        }
    }
}

function handleDrop(event) {
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    if (file && file.type === 'application/pdf') {
        form.pdf_file = file;
        selectedFileName.value = file.name;
        if (!form.title) {
            form.title = file.name.replace(/\.[^/.]+$/, '');
        }
    }
}

function submit() {
    form.post('/admin/question-import/pdf', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('pdf_file', 'title');
            selectedFileName.value = '';
        },
    });
}
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
                        সংগৃহীত প্রশ্নপত্রের পিডিএফ আপলোড করুন। AI স্বয়ংক্রিয়ভাবে প্রশ্ন, অপশন, সঠিক উত্তর ও সমাধান তৈরি করে প্রশ্ন ব্যাংকে যোগ করবে।
                    </p>
                </div>
            </div>

            <!-- Upload Card -->
            <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Drop Zone -->
                    <div
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop"
                        class="border-2 border-dashed rounded-2xl p-8 text-center transition-all cursor-pointer"
                        :class="isDragging ? 'border-primary bg-primary/5' : 'border-gray-200 dark:border-gray-700 hover:border-primary/50 bg-gray-50/50 dark:bg-gray-900/30'"
                        @click="$refs.fileInput.click()"
                    >
                        <input
                            ref="fileInput"
                            type="file"
                            accept="application/pdf"
                            class="hidden"
                            @change="handleFileChange"
                        />

                        <div class="flex flex-col items-center justify-center space-y-3">
                            <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <ArrowUpTrayIcon class="w-7 h-7" />
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ selectedFileName ? selectedFileName : 'প্রশ্নপত্রের PDF ফাইল ড্র্যাগ করে ছাড়ুন অথবা ক্লিক করে আপলোড করুন' }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    সর্বোচ্চ ২৫ মেগাবাইট (MB) পর্যন্ত PDF ফাইল গ্রহণযোগ্য
                                </div>
                            </div>
                            <span v-if="selectedFileName" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <CheckCircleIcon class="w-4 h-4" />
                                <span>ফাইল সিলেক্ট করা হয়েছে</span>
                            </span>
                        </div>
                    </div>

                    <!-- Metadata Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Batch Title -->
                        <div class="sm:col-span-2 lg:col-span-1">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">ব্যাচ / প্রশ্নপত্রের শিরোনাম</label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="উদাঃ DU A Unit 2024-25 Question Paper"
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
                                <option value="admission">বিশ্ববিদ্যালয় ভর্তি (Admission)</option>
                                <option value="job">সরকারি চাকরি ও ব্যাংক (Job)</option>
                            </select>
                        </div>

                        <!-- Origin Tag -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">উৎস ট্যাগ (Origin Tag) *</label>
                            <input
                                v-model="form.origin_tag"
                                type="text"
                                required
                                placeholder="DU-24, RU-23, BCS-46"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white uppercase font-bold"
                            />
                        </div>

                        <!-- Institution -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">প্রতিষ্ঠান / বিশ্ববিদ্যালয়</label>
                            <input
                                v-model="form.institution"
                                type="text"
                                placeholder="উদাঃ University of Dhaka"
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
                            <span>AI অটোমেটেড পাইপলাইন যেভাবে কাজ করে:</span>
                        </div>
                        <p class="leading-relaxed text-gray-600 dark:text-gray-400">
                            পিডিএফ আপলোড করার পর সিস্টেম টেক্সট এক্সট্রাক্ট করবে, প্রতিটি প্রশ্ন শনাক্ত করবে, অপশন আলাদা করবে, সঠিক উত্তর চিহ্নিত করবে, গাণিতিক সূত্র KaTeX LaTeX ফরম্যাটে কনভার্ট করবে এবং প্রতিটি প্রশ্নের জন্য পুঙ্খানুপুঙ্খ ব্যাখ্যা তৈরি করে সরাসরি প্রশ্ন ব্যাংকে যুক্ত করবে।
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="submit"
                            :disabled="!form.pdf_file || form.processing"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary to-indigo-600 text-white text-xs font-bold hover:opacity-95 shadow-md shadow-primary/20 disabled:opacity-50 transition active:scale-95 flex items-center gap-2"
                        >
                            <SparklesIcon class="w-4 h-4" :class="{ 'animate-spin': form.processing }" />
                            <span>{{ form.processing ? 'OCR ও AI প্রশ্ন প্রসেস হচ্ছে...' : 'পিডিএফ প্রসেস ও প্রশ্ন ব্যাংকে যুক্ত করুন' }}</span>
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
                                <th class="px-5 py-3 text-right">তারিখ</th>
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
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300': b.status === 'completed',
                                            'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300': b.status === 'processing',
                                            'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300': b.status === 'failed',
                                            'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': b.status === 'pending',
                                        }"
                                    >
                                        {{ b.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right text-gray-400 text-[11px] whitespace-nowrap">
                                    {{ new Date(b.created_at).toLocaleDateString('bn-BD') }}
                                </td>
                            </tr>
                            <tr v-if="!batches || batches.length === 0">
                                <td colspan="6" class="text-center py-8 text-gray-400">
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
