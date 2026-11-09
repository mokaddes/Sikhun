<script setup>
import { ref, computed } from 'vue';
import {
    DocumentTextIcon,
    ArrowDownTrayIcon,
    ClipboardDocumentIcon,
    CheckIcon,
    ArrowPathIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const fileName = ref('');
const fileSizeKb = ref(0);
const isExtracting = ref(false);
const extractedText = ref('');
const pageCount = ref(0);
const copied = ref(false);
const errorMsg = ref('');

function onFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;

    if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
        errorMsg.value = props.en ? 'Please select a valid PDF file.' : 'অনুগ্রহ করে একটি সঠিক পিডিএফ ফাইল নির্বাচন করুন।';
        return;
    }

    fileName.value = file.name;
    fileSizeKb.value = Math.round(file.size / 1024);
    errorMsg.value = '';
    extractedText.value = '';
    isExtracting.value = true;

    const reader = new FileReader();
    reader.onload = function(evt) {
        try {
            const buffer = evt.target.result;
            const textDecoder = new TextDecoder('latin1');
            const rawPdf = textDecoder.decode(buffer);

            // Estimate pages from /Type /Page
            const pageMatches = rawPdf.match(/\/Type\s*\/Page\b/g);
            pageCount.value = pageMatches ? pageMatches.length : 1;

            // Extract text from text blocks between BT and ET
            const textBlocks = [];
            const btEtRegex = /BT[\s\S]*?ET/g;
            let match;

            while ((match = btEtRegex.exec(rawPdf)) !== null) {
                const block = match[0];
                // Extract strings in parentheses (Text) Tj or [(Text)] TJ
                const strRegex = /\(([^)]*)\)\s*(?:Tj|'|")/g;
                let strMatch;
                let blockText = '';
                while ((strMatch = strRegex.exec(block)) !== null) {
                    // Unescape standard PDF octal / characters
                    let unescaped = strMatch[1]
                        .replace(/\\([0-7]{3})/g, (m, oct) => String.fromCharCode(parseInt(oct, 8)))
                        .replace(/\\[nrtbf]/g, ' ')
                        .replace(/\\(.)/g, '$1');
                    blockText += unescaped + ' ';
                }

                // Also match array of strings in TJ
                const tjArrayRegex = /\[([^\]]*)\]\s*TJ/g;
                let tjMatch;
                while ((tjMatch = tjArrayRegex.exec(block)) !== null) {
                    const innerStrs = tjMatch[1].match(/\(([^)]*)\)/g) || [];
                    innerStrs.forEach(s => {
                        const clean = s.slice(1, -1)
                            .replace(/\\([0-7]{3})/g, (m, oct) => String.fromCharCode(parseInt(oct, 8)))
                            .replace(/\\(.)/g, '$1');
                        blockText += clean + ' ';
                    });
                }

                if (blockText.trim()) {
                    textBlocks.push(blockText.trim());
                }
            }

            if (textBlocks.length > 0) {
                extractedText.value = textBlocks.join('\n\n');
            } else {
                // If scanned image PDF without embedded text layer
                extractedText.value = props.en
                    ? '[Note: This PDF appears to be a scanned image or protected document without an embedded text layer.]'
                    : '[দ্রষ্টব্য: এই পিডিএফ ফাইলটি সম্ভবত একটি স্ক্যান করা ছবি অথবা পাসওয়ার্ড প্রোটেক্টেড, যার ভেতর কোনো এমবেডেড টেক্সট লেয়ার নেই।]';
            }
            isExtracting.value = false;
        } catch (e) {
            errorMsg.value = (props.en ? 'Error parsing PDF: ' : 'পিডিএফ পড়ার সময় সমস্যা: ') + e.message;
            isExtracting.value = false;
        }
    };
    reader.readAsArrayBuffer(file);
}

const stats = computed(() => {
    const raw = extractedText.value.trim();
    if (!raw) return { words: 0, chars: 0 };
    const words = raw.split(/\s+/).length;
    const chars = raw.length;
    return { words, chars };
});

function copyText() {
    navigator.clipboard.writeText(extractedText.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
}

function downloadTxt() {
    const blob = new Blob([extractedText.value], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${fileName.value.replace(/\.pdf$/i, '')}_extracted.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function clearAll() {
    fileName.value = '';
    extractedText.value = '';
    pageCount.value = 0;
}
</script>

<template>
    <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-5">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5">
                    <DocumentTextIcon class="w-6 h-6 text-teal-500" />
                    {{ en ? 'PDF to Text Extractor' : 'পিডিএফ থেকে টেক্সট এক্সট্রাক্টর (PDF to Text)' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Extract all readable text, copy, or save as TXT file directly in browser without uploading anywhere.' : 'যেকোনো পিডিএফ ফাইল থেকে সব লেখা বা টেক্সট আলাদা করুন, কপি করুন বা টেক্সট (.TXT) ফাইল হিসেবে সেভ করুন।' }}
                </p>
            </div>

            <span class="px-3.5 py-1.5 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-600 dark:text-teal-400 text-xs font-bold">
                🔒 {{ en ? '100% Private in Browser' : '১০০% ব্রাউজারে নিরাপদ' }}
            </span>
        </div>

        <!-- File Upload Area -->
        <div class="mt-6 border-2 border-dashed border-gray-300 dark:border-white/15 rounded-3xl p-8 text-center bg-gray-50 dark:bg-[#0e121d] hover:border-teal-500 transition">
            <input
                type="file"
                accept=".pdf,application/pdf"
                id="pdfToTextInput"
                class="hidden"
                @change="onFileSelect"
            />
            <label for="pdfToTextInput" class="cursor-pointer block">
                <DocumentTextIcon class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                <span class="btn btn-primary text-xs py-2 px-5 inline-block mb-2 shadow-sm">
                    📄 {{ en ? 'Select PDF File' : 'পিডিএফ ফাইল নির্বাচন করুন' }}
                </span>
                <p class="text-xs text-gray-500">
                    {{ en ? 'Instant text extraction. No file size limits.' : 'তাৎক্ষণিক টেক্সট এক্সট্রাকশন—কোনো সার্ভার আপলোড ছাড়া।' }}
                </p>
            </label>
        </div>

        <!-- Error Message -->
        <div v-if="errorMsg" class="mt-4 p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-600 dark:text-rose-400 font-mono">
            {{ errorMsg }}
        </div>

        <!-- Extracted Text Display Area -->
        <div v-if="extractedText" class="mt-6 space-y-4">
            <!-- Meta Bar -->
            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 flex items-center justify-between flex-wrap gap-3 text-xs">
                <div>
                    <strong class="text-gray-900 dark:text-white">{{ fileName }}</strong>
                    <span class="text-gray-500 ml-2">({{ fileSizeKb }} KB • {{ pageCount }} {{ en ? 'Pages' : 'পৃষ্ঠা' }})</span>
                </div>
                <div class="flex items-center gap-3 font-mono text-[11px] text-gray-500">
                    <span>{{ stats.words }} {{ en ? 'words' : 'শব্দ' }}</span>
                    <span>•</span>
                    <span>{{ stats.chars }} {{ en ? 'characters' : 'বর্ণ' }}</span>
                </div>
            </div>

            <!-- Text Area -->
            <div class="rounded-2xl border border-gray-300 dark:border-white/10 overflow-hidden bg-gray-50 dark:bg-[#0e121d]">
                <textarea
                    v-model="extractedText"
                    rows="14"
                    class="w-full p-4 font-mono text-xs sm:text-sm bg-transparent border-0 text-gray-900 dark:text-white focus:outline-none focus:ring-0 leading-relaxed resize-y"
                ></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="copyText"
                        class="btn btn-outline text-xs py-2 px-3.5 flex items-center gap-1.5 text-teal-600 dark:text-teal-400 border-teal-500/30"
                    >
                        <CheckIcon v-if="copied" class="w-4 h-4 text-emerald-500" />
                        <ClipboardDocumentIcon v-else class="w-4 h-4" />
                        <span>{{ copied ? (en ? 'Copied Text!' : 'টেক্সট কপি হয়েছে!') : (en ? 'Copy Text' : 'টেক্সট কপি করুন') }}</span>
                    </button>

                    <button
                        type="button"
                        @click="downloadTxt"
                        class="btn btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        <span>{{ en ? 'Download .TXT File' : '.TXT ফাইল ডাউনলোড' }}</span>
                    </button>
                </div>

                <button
                    type="button"
                    @click="clearAll"
                    class="text-rose-500 hover:text-rose-700 text-xs px-2 py-1"
                >
                    {{ en ? 'Clear' : 'মুছুন' }}
                </button>
            </div>
        </div>
    </div>
</template>
