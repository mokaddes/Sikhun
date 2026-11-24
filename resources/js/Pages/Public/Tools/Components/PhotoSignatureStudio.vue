<script setup>
import { ref, computed } from 'vue';
import {
    PhotoIcon,
    ArrowDownTrayIcon,
    ArrowPathIcon,
    CheckCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const preset = ref('photo_300'); // 'photo_300' | 'signature_300x80' | 'passport' | 'custom'
const customW = ref(300);
const customH = ref(300);
const maxKb = ref(100);
const bgColor = ref('#FFFFFF');
const fitMode = ref('cover'); // 'cover' | 'contain'

const selectedFile = ref(null);
const previewUrl = ref(null);
const processing = ref(false);
const output = ref(null);
const errorMsg = ref('');

const presets = [
    { key: 'photo_300', labelBn: 'স্ট্যান্ডার্ড ছবি (৩০০×৩০০ px)', labelEn: 'Standard Photo (300×300 px)', w: 300, h: 300, kb: 100, fit: 'cover' },
    { key: 'signature_300x80', labelBn: 'স্বাক্ষর (৩০০×৮০ px)', labelEn: 'Signature (300×80 px)', w: 300, h: 80, kb: 60, fit: 'contain' },
    { key: 'passport', labelBn: 'পাসপোর্ট সাইজ (৪১৩×৫৩১ px)', labelEn: 'Passport Size (413×531 px)', w: 413, h: 531, kb: 150, fit: 'cover' },
    { key: 'custom', labelBn: 'কাস্টম মাপ (Custom Size)', labelEn: 'Custom Dimensions', w: 400, h: 400, kb: 120, fit: 'cover' },
];

function selectPreset(p) {
    preset.value = p.key;
    customW.value = p.w;
    customH.value = p.h;
    maxKb.value = p.kb;
    fitMode.value = p.fit;
    if (previewUrl.value) processImage();
}

const activeSpec = computed(() => ({
    w: Math.max(20, Math.min(2400, Number(customW.value) || 300)),
    h: Math.max(20, Math.min(2400, Number(customH.value) || 300)),
    kb: Math.max(10, Math.min(2048, Number(maxKb.value) || 100)),
}));

function onFileChange(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    selectedFile.value = file;
    errorMsg.value = '';
    output.value = null;

    const reader = new FileReader();
    reader.onload = (ev) => {
        previewUrl.value = ev.target.result;
        processImage();
    };
    reader.readAsDataURL(file);
}

function processImage() {
    if (!previewUrl.value) return;
    processing.value = true;
    errorMsg.value = '';

    const { w: targetW, h: targetH, kb: limitKb } = activeSpec.value;
    const img = new Image();
    img.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = targetW;
        canvas.height = targetH;
        const ctx = canvas.getContext('2d');

        ctx.fillStyle = bgColor.value;
        ctx.fillRect(0, 0, targetW, targetH);

        const imgRatio = img.width / img.height;
        const targetRatio = targetW / targetH;
        let drawW = targetW;
        let drawH = targetH;
        let offsetX = 0;
        let offsetY = 0;

        if (fitMode.value === 'cover') {
            if (imgRatio > targetRatio) {
                drawH = targetH;
                drawW = targetH * imgRatio;
                offsetX = (targetW - drawW) / 2;
            } else {
                drawW = targetW;
                drawH = targetW / imgRatio;
                offsetY = (targetH - drawH) / 2;
            }
        } else {
            if (imgRatio > targetRatio) {
                drawW = targetW;
                drawH = targetW / imgRatio;
                offsetY = (targetH - drawH) / 2;
            } else {
                drawH = targetH;
                drawW = targetH * imgRatio;
                offsetX = (targetW - drawW) / 2;
            }
        }

        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(img, offsetX, offsetY, drawW, drawH);

        let quality = 0.94;
        let dataUrl = canvas.toDataURL('image/jpeg', quality);
        while ((dataUrl.length * 0.75) / 1024 > limitKb && quality > 0.2) {
            quality -= 0.05;
            dataUrl = canvas.toDataURL('image/jpeg', quality);
        }

        const sizeKb = Math.max(1, Math.round((dataUrl.length * 0.75) / 1024));
        output.value = {
            url: dataUrl,
            width: targetW,
            height: targetH,
            sizeKb,
            withinLimit: sizeKb <= limitKb,
            fileName: `sikhun_${preset.value}_${targetW}x${targetH}.jpg`,
        };
        processing.value = false;
    };
    img.onerror = () => {
        errorMsg.value = props.en ? 'Could not process this image file.' : 'ছবিটি প্রসেস করা সম্ভব হয়নি। অন্য ছবি নির্বাচন করুন।';
        processing.value = false;
    };
    img.src = previewUrl.value;
}

function downloadOutput() {
    if (!output.value) return;
    const a = document.createElement('a');
    a.href = output.value.url;
    a.download = output.value.fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>

<template>
    <div class="card p-5 sm:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl space-y-6">
        <div class="border-b border-gray-200 dark:border-white/10 pb-4">
            <h2 class="font-heading text-xl md:text-2xl font-extrabold flex items-center gap-2">
                <PhotoIcon class="w-6 h-6 text-blue-500" />
                <span>{{ en ? 'Photo & Signature Resizer Studio' : 'ছবি ও স্বাক্ষর স্টুডিও (Photo & Signature Resizer Pro)' }}</span>
            </h2>
            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                {{ en
                    ? 'Resize, fit, change background, and compress photos & signatures to exact pixel dimensions and KB size right in your browser.'
                    : 'যেকোনো অনলাইন ফরম বা ডকুমেন্টের জন্য ছবি ও স্বাক্ষর নির্দিষ্ট পিক্সেল, ব্যাকগ্রাউন্ড রঙ এবং কিলোবাইট (KB) সাইজে রূপান্তর ও ডাউনলোড করুন।'
                }}
            </p>
        </div>

        <!-- Presets -->
        <div class="flex flex-wrap gap-2">
            <button
                v-for="p in presets"
                :key="p.key"
                type="button"
                class="px-4 py-2 rounded-xl text-xs font-bold border transition"
                :class="preset === p.key ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                @click="selectPreset(p)"
            >
                {{ en ? p.labelEn : p.labelBn }}
            </button>
        </div>

        <!-- Dimension & Background Controls -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
            <div>
                <label class="block text-[11px] font-bold mb-1">{{ en ? 'Width (px)' : 'প্রস্থ (px)' }}</label>
                <input v-model.number="customW" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-xs font-bold" @change="processImage" />
            </div>
            <div>
                <label class="block text-[11px] font-bold mb-1">{{ en ? 'Height (px)' : 'উচ্চতা (px)' }}</label>
                <input v-model.number="customH" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-xs font-bold" @change="processImage" />
            </div>
            <div>
                <label class="block text-[11px] font-bold mb-1">{{ en ? 'Max Size (KB)' : 'সর্বোচ্চ সাইজ (KB)' }}</label>
                <input v-model.number="maxKb" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-xs font-bold" @change="processImage" />
            </div>
            <div>
                <label class="block text-[11px] font-bold mb-1">{{ en ? 'Fit Mode' : 'ফিট মোড' }}</label>
                <select v-model="fitMode" class="w-full px-2.5 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-xs font-bold" @change="processImage">
                    <option value="cover">{{ en ? 'Auto Crop (Fill)' : 'অটো ক্রপ (ভরাট)' }}</option>
                    <option value="contain">{{ en ? 'Fit Full Image' : 'সম্পূর্ণ ফিট' }}</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-[11px] font-bold mb-1">{{ en ? 'Background' : 'ব্যাকগ্রাউন্ড রঙ' }}</label>
                <div class="flex items-center gap-1.5">
                    <button
                        v-for="c in ['#FFFFFF', '#E0F2FE', '#DBEAFE', '#F1F5F9']"
                        :key="c"
                        type="button"
                        class="h-8 w-8 rounded-lg border-2 transition"
                        :style="{ backgroundColor: c }"
                        :class="bgColor === c ? 'border-blue-600 scale-105' : 'border-gray-300'"
                        @click="bgColor = c; processImage()"
                    />
                </div>
            </div>
        </div>

        <!-- Upload Box -->
        <div class="border-2 border-dashed border-gray-300 dark:border-white/15 rounded-3xl p-8 text-center bg-gray-50/70 dark:bg-[#0e121d] hover:border-blue-500 transition">
            <input id="studioFileInput" type="file" accept="image/*" class="hidden" @change="onFileChange" />
            <label for="studioFileInput" class="cursor-pointer block">
                <PhotoIcon class="w-11 h-11 text-blue-500 mx-auto mb-2" />
                <span class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-extrabold inline-block shadow-sm">
                    {{ en ? 'Select Photo or Signature' : 'ছবি বা স্বাক্ষর নির্বাচন করুন' }}
                </span>
                <p class="text-xs text-gray-500 mt-2">
                    {{ en ? '100% private — processed locally inside your browser.' : '১০০% নিরাপদ — ছবি কোনো সার্ভারে আপলোড হয় না, ব্রাউজারেই প্রসেস হয়।' }}
                </p>
            </label>
        </div>

        <p v-if="errorMsg" class="text-xs text-rose-500 font-bold">{{ errorMsg }}</p>

        <!-- Result Preview -->
        <div v-if="output" class="p-6 rounded-3xl bg-emerald-500/5 border border-emerald-500/25 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center gap-5">
                <div class="p-2 bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <img
                        :src="output.url"
                        alt="Processed Output"
                        class="max-h-36 max-w-52 object-contain rounded"
                    />
                </div>
                <div class="text-center sm:text-left space-y-1">
                    <div class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 text-xs font-extrabold">
                        <CheckCircleIcon class="w-4 h-4" />
                        <span>{{ en ? 'Ready to Download!' : 'ডাউনলোডের জন্য প্রস্তুত!' }}</span>
                    </div>
                    <div class="text-sm font-mono font-bold text-gray-900 dark:text-white">
                        {{ output.width }} × {{ output.height }} px • {{ output.sizeKb }} KB
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ en ? `Target limit: ≤ ${activeSpec.kb} KB` : `নির্ধারিত সীমা: ≤ ${activeSpec.kb} KB` }}
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-white/15 text-xs font-bold flex items-center gap-1" @click="processImage">
                    <ArrowPathIcon class="w-4 h-4" :class="{ 'animate-spin': processing }" />
                    <span>{{ en ? 'Refresh' : 'পুনরায়' }}</span>
                </button>
                <button type="button" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold flex items-center gap-1.5 shadow-md" @click="downloadOutput">
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    <span>{{ en ? 'Download JPG' : 'ছবি ডাউনলোড করুন' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
