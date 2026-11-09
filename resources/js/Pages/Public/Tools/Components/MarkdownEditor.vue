<script setup>
import { ref, computed } from 'vue';
import {
    DocumentTextIcon,
    PrinterIcon,
    ClipboardDocumentIcon,
    CheckIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const defaultNotes = `# 📚 পদার্থবিজ্ঞান: আলোর প্রতিফলন (Lecture Notes)
---
**তারিখ:** ${new Date().toISOString().split('T')[0]} | **অধ্যায়:** ৮ম অধ্যায়

### ১. আলোর প্রতিফলনের সূত্রাবলি:
1. আপতিত রশ্মি, প্রতিফলিত রশ্মি এবং আপতন বিন্দুতে অঙ্কিত অভিলম্ব একই সমতলে থাকে।
2. আপতন কোণ ($i$) ও প্রতিফলন কোণ ($r$) সর্বদা সমান হয় ($i = r$)।

### ২. প্রতিবিম্বের বৈশিষ্ট্য:
* **বাস্তব প্রতিবিম্ব:** পর্দায় ফেলা যায়, সর্বদা উল্টো হয়।
* **অবাস্তব প্রতিবিম্ব:** পর্দায় ফেলা যায় না, সর্বদা সোজা হয়।

> **টিপস:** অবতল দর্পণে ফোকাস ও মেরুর মাঝে লক্ষ্যবস্তু রাখলে একমাত্র অবাস্তব ও সোজা বিম্ব গঠিত হয়।

\`\`\`
দর্পণ সমীকরণ: 1/u + 1/v = 1/f
বিবর্ধন: m = -v/u
\`\`\`
`;

const markdownInput = ref(defaultNotes);
const copied = ref(false);

const renderedHtml = computed(() => {
    return parseMarkdown(markdownInput.value);
});

function parseMarkdown(md) {
    if (!md) return '';
    let html = md
        // Escape HTML
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    // Code blocks ```code```
    html = html.replace(/```([\s\S]*?)```/g, (match, p1) => {
        return `<pre class="p-3 my-2 rounded-xl bg-gray-900 text-gray-100 font-mono text-xs overflow-x-auto leading-relaxed border border-white/10"><code>${p1.trim()}</code></pre>`;
    });

    // Inline code `code`
    html = html.replace(/`([^`]+)`/g, '<code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-white/10 text-rose-500 font-mono text-xs">$1</code>');

    // Headings #, ##, ###
    html = html.replace(/^### (.*$)/gim, '<h3 class="text-base font-bold text-gray-900 dark:text-white mt-4 mb-2">$1</h3>');
    html = html.replace(/^## (.*$)/gim, '<h2 class="text-lg font-extrabold text-gray-900 dark:text-white mt-5 mb-2 border-b border-gray-200 dark:border-white/10 pb-1">$1</h2>');
    html = html.replace(/^# (.*$)/gim, '<h1 class="text-xl font-extrabold text-gray-900 dark:text-white mt-2 mb-3">$1</h1>');

    // Horizontal Rule ---
    html = html.replace(/^---$/gim, '<hr class="my-3 border-gray-200 dark:border-white/10" />');

    // Blockquote >
    html = html.replace(/^\> (.*$)/gim, '<blockquote class="border-l-4 border-indigo-500 pl-3 my-2 text-xs italic text-gray-600 dark:text-gray-300 bg-indigo-500/5 py-1 rounded-r-lg">$1</blockquote>');

    // Bold **text**
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900 dark:text-white">$1</strong>');

    // Italic *text*
    html = html.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');

    // Unordered lists * or -
    html = html.replace(/^\s*[\*\-]\s+(.*$)/gim, '<li class="ml-4 list-disc text-gray-700 dark:text-gray-300">$1</li>');

    // Ordered lists 1.
    html = html.replace(/^\s*\d+\.\s+(.*$)/gim, '<li class="ml-4 list-decimal text-gray-700 dark:text-gray-300">$1</li>');

    // Line breaks to <br>
    html = html.replace(/\n/g, '<br/>');

    // Clean multiple br around headers and lists
    html = html.replace(/(<\/h[1-3]>|<hr[^>]*>|<\/pre>|<\/blockquote>|<br\/>\s*<li)/g, (match) => {
        if (match.startsWith('<br/>')) return match.replace('<br/>', '');
        return match;
    });

    return html;
}

function copyHtml() {
    navigator.clipboard.writeText(renderedHtml.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
}

function printNote() {
    window.print();
}

function clearNote() {
    markdownInput.value = '';
}
</script>

<template>
    <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-5">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5">
                    <DocumentTextIcon class="w-6 h-6 text-purple-500" />
                    {{ en ? 'Markdown Live Notes & PDF Exporter' : 'মার্কডাউন লাইভ নোট ও পিডিএফ এক্সপোর্টার' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Write formatted study notes with headings, math formulas, and print/save directly as PDF.' : 'হেডিং, বুলেট পয়েন্ট ও ফর্মুলা দিয়ে ক্লাসের নোট তৈরি করুন এবং সরাসরি প্রিন্ট বা PDF ডাউনলোড করুন।' }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="printNote"
                    class="btn btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5"
                >
                    <PrinterIcon class="w-4 h-4" />
                    <span>{{ en ? 'Print / Save PDF' : 'প্রিন্ট / PDF ডাউনলোড' }}</span>
                </button>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="mt-4 flex items-center justify-between gap-3 text-xs flex-wrap">
            <span class="text-gray-500">
                ✏️ {{ en ? 'Live split editor: Markdown on left, preview on right' : 'স্প্লিট ভিউ: বামে লিখুন, ডানে লাইভ প্রিভিউ দেখুন' }}
            </span>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="copyHtml"
                    class="btn btn-outline text-xs py-1 px-3 flex items-center gap-1"
                >
                    <CheckIcon v-if="copied" class="w-3.5 h-3.5 text-emerald-500" />
                    <ClipboardDocumentIcon v-else class="w-3.5 h-3.5" />
                    <span>{{ copied ? (en ? 'Copied HTML!' : 'HTML কপি হয়েছে!') : (en ? 'Copy HTML' : 'HTML কপি') }}</span>
                </button>
                <button
                    type="button"
                    @click="clearNote"
                    class="text-rose-500 hover:text-rose-700 text-xs px-2 py-1"
                >
                    {{ en ? 'Clear' : 'মুছুন' }}
                </button>
            </div>
        </div>

        <!-- Split Grid -->
        <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Left: Editor -->
            <div class="rounded-2xl border border-gray-300 dark:border-white/10 bg-gray-50 dark:bg-[#0e121d] overflow-hidden flex flex-col">
                <div class="px-4 py-2 bg-gray-100 dark:bg-[#121624] border-b border-gray-200 dark:border-white/5 text-[11px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    📝 {{ en ? 'Markdown Input' : 'মার্কডাউন ইনপুট' }}
                </div>
                <textarea
                    v-model="markdownInput"
                    rows="16"
                    :placeholder="en ? 'Type markdown here...' : 'এখানে নোট লিখুন...'"
                    class="w-full p-4 font-mono text-xs sm:text-sm bg-transparent border-0 text-gray-900 dark:text-white focus:outline-none focus:ring-0 leading-relaxed resize-y flex-1"
                ></textarea>
            </div>

            <!-- Right: Preview -->
            <div class="rounded-2xl border border-gray-300 dark:border-white/10 bg-white dark:bg-[#0e121d] overflow-hidden flex flex-col">
                <div class="px-4 py-2 bg-gray-100 dark:bg-[#121624] border-b border-gray-200 dark:border-white/5 text-[11px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                    👁️ {{ en ? 'Live Formatted Preview' : 'লাইভ ফরম্যাটেড প্রিভিউ' }}
                </div>
                <div
                    v-html="renderedHtml"
                    class="p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100 overflow-y-auto max-h-[420px] leading-relaxed select-text"
                ></div>
            </div>
        </div>
    </div>
</template>
