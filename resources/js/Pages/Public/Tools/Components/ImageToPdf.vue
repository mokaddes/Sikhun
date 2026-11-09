<script setup>
import { ref, computed } from 'vue';
import { jsPDF } from 'jspdf';
import {
    PhotoIcon,
    ArrowDownTrayIcon,
    TrashIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    DocumentDuplicateIcon,
    PlusIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const imageList = ref([]);
const isConverting = ref(false);
const progressPercent = ref(0);
const pageSize = ref('a4'); // 'a4' | 'fit'
const orientation = ref('portrait'); // 'portrait' | 'landscape'
const marginSize = ref(10); // 0 | 10 | 20 mm
const pdfFileName = ref('Sikhun_Document.pdf');

function onFileSelect(e) {
    const files = Array.from(e.target.files || []);
    if (!files.length) return;

    files.forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = (evt) => {
            const img = new Image();
            img.onload = () => {
                imageList.value.push({
                    id: Date.now() + Math.random(),
                    name: file.name,
                    sizeKb: Math.round(file.size / 1024),
                    dataUrl: evt.target.result,
                    width: img.width,
                    height: img.height,
                });
            };
            img.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function removeImage(idx) {
    imageList.value.splice(idx, 1);
}

function clearAll() {
    imageList.value = [];
}

async function convertToPdf() {
    if (!imageList.value.length || isConverting.value) return;

    isConverting.value = true;
    progressPercent.value = 10;

    try {
        const isLandscape = orientation.value === 'landscape';
        const doc = new jsPDF({
            orientation: isLandscape ? 'landscape' : 'portrait',
            unit: 'mm',
            format: 'a4',
        });

        const pageWidth = isLandscape ? 297 : 210;
        const pageHeight = isLandscape ? 210 : 297;
        const margin = marginSize.value;
        const availableW = pageWidth - margin * 2;
        const availableH = pageHeight - margin * 2;

        for (let i = 0; i < imageList.value.length; i++) {
            if (i > 0) doc.addPage('a4', isLandscape ? 'landscape' : 'portrait');

            const item = imageList.value[i];

            // Calculate scaled dimensions keeping aspect ratio
            const imgRatio = item.width / item.height;
            let drawW = availableW;
            let drawH = availableW / imgRatio;

            if (drawH > availableH) {
                drawH = availableH;
                drawW = availableH * imgRatio;
            }

            // Center image on page
            const x = margin + (availableW - drawW) / 2;
            const y = margin + (availableH - drawH) / 2;

            // Determine image format
            let format = 'JPEG';
            if (item.dataUrl.startsWith('data:image/png')) format = 'PNG';
            else if (item.dataUrl.startsWith('data:image/webp')) format = 'WEBP';

            doc.addImage(item.dataUrl, format, x, y, drawW, drawH, undefined, 'FAST');
            progressPercent.value = Math.round(((i + 1) / imageList.value.length) * 90);
        }

        const name = pdfFileName.value.trim().endsWith('.pdf') ? pdfFileName.value.trim() : `${pdfFileName.value.trim()}.pdf`;
        doc.save(name || 'Sikhun_Document.pdf');
        progressPercent.value = 100;
        setTimeout(() => {
            isConverting.value = false;
            progressPercent.value = 0;
        }, 1500);
    } catch (e) {
        alert(props.en ? 'Failed to generate PDF: ' + e.message : 'পিডিএফ তৈরিতে সমস্যা হয়েছে: ' + e.message);
        isConverting.value = false;
    }
}
</script>

<template>
    <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-5">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5">
                    <PhotoIcon class="w-6 h-6 text-indigo-500" />
                    {{ en ? 'Image to PDF Converter' : 'ছবি থেকে পিডিএফ কনভার্টার (Image to PDF)' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Combine multiple JPG, PNG, or WebP images into a single clean PDF document completely in your browser.' : 'একাধিক ছবি (জেপিজি, পিএনজি) যুক্ত করে ১-ক্লিকে চমৎকার এ৪ সাইজের পিডিএফ ফাইল তৈরি ও ডাউনলোড করুন।' }}
                </p>
            </div>

            <!-- Free Badge -->
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold">
                    🔒 {{ en ? '100% Client-Side (No Uploads)' : '১০০% ব্রাউজারে নিরাপদ (সার্ভারে আপলোড হয় না)' }}
                </span>
            </div>
        </div>

        <!-- Options Bar -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
            <div>
                <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ en ? 'Page Orientation:' : 'পাতার ধরন (Orientation):' }}
                </label>
                <select
                    v-model="orientation"
                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-xs"
                >
                    <option value="portrait">{{ en ? 'Portrait (Vertical A4)' : 'পোর্ট্রেট (খাড়া A4)' }}</option>
                    <option value="landscape">{{ en ? 'Landscape (Horizontal A4)' : 'ল্যান্ডস্কেপ (আড়াআড়ি A4)' }}</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ en ? 'Margin (Border):' : 'পাতার মার্জিন:' }}
                </label>
                <select
                    v-model.number="marginSize"
                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-xs"
                >
                    <option :value="0">{{ en ? 'No Margin (Full Bleed)' : 'মার্জিন ছাড়া (Full Page)' }}</option>
                    <option :value="10">{{ en ? 'Standard (10mm)' : 'স্ট্যান্ডার্ড (১০ মি.মি.)' }}</option>
                    <option :value="20">{{ en ? 'Wide Margin (20mm)' : 'প্রশস্ত মার্জিন (২০ মি.মি.)' }}</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ en ? 'Output File Name:' : 'পিডিএফ ফাইলের নাম:' }}
                </label>
                <input
                    v-model="pdfFileName"
                    type="text"
                    placeholder="Sikhun_Document.pdf"
                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-xs font-mono"
                />
            </div>
        </div>

        <!-- Drag & Drop / Upload Area -->
        <div class="mt-6 border-2 border-dashed border-gray-300 dark:border-white/15 rounded-3xl p-8 text-center bg-gray-50 dark:bg-[#0e121d] hover:border-indigo-500 transition">
            <input
                type="file"
                accept="image/*"
                multiple
                id="imageToPdfInput"
                class="hidden"
                @change="onFileSelect"
            />
            <label for="imageToPdfInput" class="cursor-pointer block">
                <PhotoIcon class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                <span class="btn btn-primary text-xs py-2 px-5 inline-block mb-2 shadow-sm">
                    📁 {{ en ? 'Select Images (Multiple)' : 'ছবি নির্বাচন করুন (এক বা একাধিক)' }}
                </span>
                <p class="text-xs text-gray-500">
                    {{ en ? 'Select 1 or more JPG, PNG, WebP images to merge into a single PDF.' : 'একক বা একাধিক ছবি সিলেক্ট করুন, এক ক্লিকে সব ছবি মিলিয়ে একটি পিডিএফ তৈরি হবে।' }}
                </p>
            </label>
        </div>

        <!-- Selected Images Gallery Grid -->
        <div v-if="imageList.length" class="mt-6 space-y-4">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-gray-700 dark:text-gray-300">
                    {{ en ? `Selected Images (${imageList.length} Pages)` : `নির্বাচিত ছবিসমূহ (${imageList.length}টি পাতা)` }}
                </span>
                <button
                    type="button"
                    @click="clearAll"
                    class="text-rose-500 hover:text-rose-700 text-xs font-semibold"
                >
                    {{ en ? 'Remove All' : 'সবগুলো মুছুন' }}
                </button>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                <div
                    v-for="(item, idx) in imageList"
                    :key="item.id"
                    class="relative rounded-2xl border border-gray-200 dark:border-white/10 overflow-hidden bg-gray-100 dark:bg-[#0e121d] group p-1"
                >
                    <img :src="item.dataUrl" class="w-full h-28 object-cover rounded-xl" />
                    <div class="p-1 text-[10px] text-gray-500 dark:text-gray-400 flex items-center justify-between">
                        <span class="truncate font-mono">P. {{ idx + 1 }}</span>
                        <span>{{ item.sizeKb }}KB</span>
                    </div>

                    <!-- Delete Single Button -->
                    <button
                        type="button"
                        @click="removeImage(idx)"
                        class="absolute top-2 right-2 bg-rose-600 text-white p-1 rounded-full opacity-80 hover:opacity-100 transition shadow"
                        title="Remove"
                    >
                        <TrashIcon class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

            <!-- Convert & Download Button -->
            <div class="pt-4 border-t border-gray-200 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-xs text-gray-500">
                    📄 {{ en ? `Will generate a ${imageList.length}-page A4 PDF.` : `মোট ${imageList.length} পৃষ্ঠার A4 পিডিএফ তৈরি হবে।` }}
                </span>

                <button
                    type="button"
                    @click="convertToPdf"
                    :disabled="isConverting"
                    class="btn btn-primary text-xs py-3 px-6 w-full sm:w-auto flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/25"
                >
                    <ArrowPathIcon v-if="isConverting" class="w-4 h-4 animate-spin" />
                    <ArrowDownTrayIcon v-else class="w-4 h-4" />
                    <span>{{ isConverting ? (en ? `Generating PDF (${progressPercent}%)...` : `পিডিএফ তৈরি হচ্ছে (${progressPercent}%)...`) : (en ? 'Convert & Download PDF' : 'পিডিএফ তৈরি ও ডাউনলোড করুন') }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
