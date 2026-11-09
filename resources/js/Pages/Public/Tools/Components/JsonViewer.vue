<script setup>
import { ref, computed } from 'vue';
import {
    ClipboardDocumentIcon,
    CheckIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    XCircleIcon,
    SparklesIcon,
    EyeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const jsonInput = ref('{\n  "app": "Sikhun",\n  "version": 2.0,\n  "features": ["ai_chat", "question_bank", "job_circulars"],\n  "active": true,\n  "stats": {\n    "students": 15000,\n    "books": 42\n  }\n}');
const copied = ref(false);
const viewMode = ref('formatted'); // 'formatted' | 'tree'
const searchKey = ref('');

const parseStatus = computed(() => {
    const raw = jsonInput.value.trim();
    if (!raw) return { valid: true, error: null, data: null };
    try {
        const parsed = JSON.parse(raw);
        return { valid: true, error: null, data: parsed };
    } catch (e) {
        return { valid: false, error: e.message, data: null };
    }
});

function prettify() {
    if (!parseStatus.value.valid) return;
    try {
        jsonInput.value = JSON.stringify(parseStatus.value.data, null, 2);
    } catch (e) {}
}

function minify() {
    if (!parseStatus.value.valid) return;
    try {
        jsonInput.value = JSON.stringify(parseStatus.value.data);
    } catch (e) {}
}

function copyJson() {
    navigator.clipboard.writeText(jsonInput.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
}

function clearJson() {
    jsonInput.value = '';
}
</script>

<template>
    <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-5">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5">
                    <span class="font-mono text-xl text-amber-500">{ }</span>
                    {{ en ? 'JSON Validator & Interactive Viewer' : 'জেসন ভ্যালিডেটর ও ভিউয়ার (JSON Validator)' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Validate, format, minify, and inspect JSON payloads for API and web development.' : 'ওয়েব ও অ্যাপ ডেভেলপমেন্টের জন্য জেসন কোড বা API রেসপন্স যাচাই, ফরম্যাটিং ও রঙিন ভিজুয়ালাইজেশন।' }}
                </p>
            </div>

            <!-- Validation Status Badge -->
            <div class="flex items-center gap-2">
                <span
                    v-if="parseStatus.valid && jsonInput.trim()"
                    class="px-3.5 py-1.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center gap-1.5"
                >
                    <CheckCircleIcon class="w-4 h-4" />
                    {{ en ? 'Valid JSON' : 'সঠিক জেসন (Valid JSON)' }}
                </span>
                <span
                    v-else-if="!parseStatus.valid"
                    class="px-3.5 py-1.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold flex items-center gap-1.5"
                >
                    <XCircleIcon class="w-4 h-4" />
                    {{ en ? 'Invalid JSON' : 'ভুল জেসন (Invalid)' }}
                </span>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="mt-5 flex items-center justify-between gap-3 flex-wrap text-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <button
                    type="button"
                    @click="prettify"
                    class="btn btn-primary text-xs py-1.5 px-3 flex items-center gap-1"
                >
                    <SparklesIcon class="w-3.5 h-3.5" />
                    <span>{{ en ? 'Prettify (2 Space)' : 'সাজান (Prettify)' }}</span>
                </button>
                <button
                    type="button"
                    @click="minify"
                    class="btn btn-outline text-xs py-1.5 px-3"
                >
                    {{ en ? 'Minify' : 'মিনিফাই' }}
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="copyJson"
                    class="btn btn-outline text-xs py-1.5 px-3 flex items-center gap-1 text-emerald-600 dark:text-emerald-400 border-emerald-500/30"
                >
                    <CheckIcon v-if="copied" class="w-3.5 h-3.5" />
                    <ClipboardDocumentIcon v-else class="w-3.5 h-3.5" />
                    <span>{{ copied ? (en ? 'Copied!' : 'কপি হয়েছে!') : (en ? 'Copy' : 'কপি করুন') }}</span>
                </button>
                <button
                    type="button"
                    @click="clearJson"
                    class="text-rose-500 hover:text-rose-700 text-xs px-2 py-1"
                >
                    {{ en ? 'Clear' : 'মুছুন' }}
                </button>
            </div>
        </div>

        <!-- Error Message Bar -->
        <div v-if="!parseStatus.valid" class="mt-3 p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-600 dark:text-rose-400 font-mono">
            ⚠️ {{ parseStatus.error }}
        </div>

        <!-- Main JSON Area -->
        <div class="mt-4 rounded-2xl border border-gray-300 dark:border-white/10 overflow-hidden bg-gray-50 dark:bg-[#0e121d]">
            <textarea
                v-model="jsonInput"
                rows="14"
                :placeholder="en ? 'Paste your JSON string here...' : 'এখানে আপনার JSON কোড পেস্ট করুন...'"
                class="w-full p-4 font-mono text-xs sm:text-sm bg-transparent border-0 text-gray-900 dark:text-white focus:outline-none focus:ring-0 leading-relaxed resize-y"
            ></textarea>

            <div class="px-4 py-2 bg-gray-100 dark:bg-[#121624] border-t border-gray-200 dark:border-white/5 flex items-center justify-between text-[11px] text-gray-500 font-mono">
                <span>{{ en ? 'JSON FORMAT' : 'জেসন ফরম্যাট' }}</span>
                <span>{{ jsonInput.length }} {{ en ? 'bytes' : 'বাইট' }}</span>
            </div>
        </div>
    </div>
</template>
