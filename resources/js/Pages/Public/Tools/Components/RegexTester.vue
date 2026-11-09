<script setup>
import { ref, computed } from 'vue';
import {
    SparklesIcon,
    ClipboardDocumentIcon,
    CheckIcon,
    CommandLineIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const regexPattern = ref('^(?:\\+8801|01)[3-9]\\d{8}$');
const regexFlags = ref('gm');
const testText = ref('01712345678\n01987654321\n+8801811223344\n01234567890 (invalid)\n01555555555');
const copied = ref(false);

const presets = [
    { label: '📱 বিডি মোবাইল নম্বর', pattern: '^(?:\\+8801|01)[3-9]\\d{8}$', flags: 'gm', sample: '01712345678\n01811223344\n01987654321\n123456 (invalid)' },
    { label: '✉️ ইমেইল অ্যাড্রেস', pattern: '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$', flags: 'gm', sample: 'support@sikhun.com\nstudent101@gmail.com\ninvalid-email@\nhello.world@buet.ac.bd' },
    { label: '🌐 ওয়েবসাইট URL', pattern: 'https?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)', flags: 'gm', sample: 'https://sikhun.com\nhttps://facebook.com/groups\nhttp://educationboard.gov.bd\nnot-a-link' },
    { label: '🔒 স্ট্রং পাসওয়ার্ড (৮+ বর্ণ, বড়/ছোট/সংখ্যা)', pattern: '^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}$', flags: 'gm', sample: 'Pass@1234 (valid)\nweakpass (invalid)\nSikhun2026# (valid)' },
    { label: '🇧🇩 বাংলা টেক্সট ফিল্টার', pattern: '[\\u0980-\\u09FF]+', flags: 'g', sample: 'বাংলা আমাদের মাতৃভাষা। Hello World 2026!' },
];

function applyPreset(p) {
    regexPattern.value = p.pattern;
    regexFlags.value = p.flags;
    testText.value = p.sample;
}

const matchResult = computed(() => {
    if (!regexPattern.value) return { count: 0, matches: [], error: null };
    try {
        const regex = new RegExp(regexPattern.value, regexFlags.value);
        const matches = [];
        const lines = testText.value.split('\n');

        lines.forEach((line, lineIdx) => {
            if (line.match(regex)) {
                matches.push({ lineIdx: lineIdx + 1, text: line });
            }
        });
        return { count: matches.length, matches, error: null };
    } catch (e) {
        return { count: 0, matches: [], error: e.message };
    }
});

function copyRegex() {
    navigator.clipboard.writeText(`/${regexPattern.value}/${regexFlags.value}`);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
}
</script>

<template>
    <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-5">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5">
                    <CommandLineIcon class="w-6 h-6 text-orange-500" />
                    {{ en ? 'Regex Tester & Pattern Cheatsheet' : 'রেজেক্স টেস্টার ও প্যাটার্ন চিটশিট (Regex Tester)' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Test regular expressions against real text with quick BD presets and syntax helpers.' : 'রেগুলার এক্সপ্রেশন যাচাই করুন, বিডি মোবাইল ও ইমেইল ভ্যালিডেশন টেস্ট এবং প্রয়োজনীয় চিটশিট।' }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span
                    v-if="!matchResult.error"
                    class="px-3.5 py-1.5 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-orange-600 dark:text-orange-400 text-xs font-bold"
                >
                    {{ matchResult.count }} {{ en ? 'Matches Found' : 'টি ম্যাচ পাওয়া গেছে' }}
                </span>
            </div>
        </div>

        <!-- Presets Bar -->
        <div class="mt-5 flex items-center gap-2 flex-wrap text-xs">
            <span class="text-gray-500 font-semibold">{{ en ? 'Common Presets:' : 'প্রস্তুত রেজেক্স প্যাটার্ন:' }}</span>
            <button
                v-for="(p, idx) in presets"
                :key="idx"
                type="button"
                @click="applyPreset(p)"
                class="px-3 py-1 rounded-xl bg-orange-500/10 text-orange-700 dark:text-orange-400 border border-orange-500/20 font-bold hover:bg-orange-500/20 transition"
            >
                {{ p.label }}
            </button>
        </div>

        <!-- Regex Input Box -->
        <div class="mt-5 p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ en ? 'Regular Expression & Flags:' : 'রেগুলার এক্সপ্রেশন প্যাটার্ন ও ফ্ল্যাগ:' }}
            </label>
            <div class="flex items-center gap-2 font-mono text-sm">
                <span class="text-gray-400 text-lg font-bold">/</span>
                <input
                    v-model="regexPattern"
                    type="text"
                    placeholder="e.g. ^[0-9]+$"
                    class="flex-1 px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-orange-600 dark:text-orange-400 font-bold focus:outline-none focus:ring-2 focus:ring-orange-500"
                />
                <span class="text-gray-400 text-lg font-bold">/</span>
                <input
                    v-model="regexFlags"
                    type="text"
                    placeholder="gm"
                    class="w-16 px-2.5 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-center font-bold text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"
                />
                <button
                    type="button"
                    @click="copyRegex"
                    class="btn btn-outline text-xs py-2 px-3 flex items-center gap-1"
                >
                    <CheckIcon v-if="copied" class="w-3.5 h-3.5 text-emerald-500" />
                    <ClipboardDocumentIcon v-else class="w-3.5 h-3.5" />
                    <span>{{ copied ? 'Copied' : 'Copy' }}</span>
                </button>
            </div>

            <div v-if="matchResult.error" class="mt-2 text-xs text-rose-500 font-mono">
                ⚠️ {{ matchResult.error }}
            </div>
        </div>

        <!-- Test Text Grid -->
        <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ en ? 'Test Strings (Each line is tested):' : 'টেস্ট স্ট্রিং (প্রতিটি লাইন যাচাই হবে):' }}
                </label>
                <textarea
                    v-model="testText"
                    rows="8"
                    placeholder="Enter test text here..."
                    class="w-full p-3 font-mono text-xs rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-orange-500 leading-relaxed"
                ></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ en ? 'Matched Results:' : 'ম্যাচ হওয়া ফলাফল:' }}
                </label>
                <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 min-h-[160px] max-h-[190px] overflow-y-auto font-mono text-xs space-y-1.5">
                    <div
                        v-for="(m, idx) in matchResult.matches"
                        :key="idx"
                        class="p-2 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-700 dark:text-orange-300 flex items-center justify-between"
                    >
                        <span>{{ m.text }}</span>
                        <span class="text-[10px] text-gray-400">Line {{ m.lineIdx }}</span>
                    </div>
                    <div v-if="!matchResult.matches.length" class="text-gray-400 text-center py-6">
                        {{ en ? 'No matches found.' : 'কোনো ম্যাচ পাওয়া যায়নি।' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Cheatsheet Accordion -->
        <div class="mt-6 pt-5 border-t border-gray-200 dark:border-white/10">
            <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 mb-3">
                📖 {{ en ? 'Regex Quick Cheatsheet' : 'রেগুলার এক্সপ্রেশন কুইক চিটশিট' }}
            </h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px] font-mono">
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <strong class="text-orange-500">^</strong> : স্ট্রিংয়ের শুরু
                </div>
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <strong class="text-orange-500">$</strong> : স্ট্রিংয়ের শেষ
                </div>
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <strong class="text-orange-500">\d</strong> : যেকোনো সংখ্যা (0-9)
                </div>
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <strong class="text-orange-500">\w</strong> : বর্ণ বা সংখ্যা (A-Z, 0-9)
                </div>
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <strong class="text-orange-500">+</strong> : ১ বা ততোধিক বার
                </div>
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <strong class="text-orange-500">*</strong> : ০ বা ততোধিক বার
                </div>
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <strong class="text-orange-500">?</strong> : অপশনাল (০ বা ১ বার)
                </div>
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <strong class="text-orange-500">[a-z]</strong> : ছোট হাতের বর্ণ
                </div>
            </div>
        </div>
    </div>
</template>
