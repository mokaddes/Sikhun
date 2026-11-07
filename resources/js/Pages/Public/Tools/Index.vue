<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';
import {
    CalendarDaysIcon,
    CalculatorIcon,
    PhotoIcon,
    AcademicCapIcon,
    DocumentTextIcon,
    SparklesIcon,
    ArrowDownTrayIcon,
    ArrowPathIcon,
    ClipboardDocumentIcon,
    CheckIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    recentJobs: Array,
});

const { t, locale } = useI18n();

const activeTab = ref('age'); // age | calculator | resizer | gpa | text
const copied = ref(false);

// ==========================================
// TOOL 1: GENERAL AGE CALCULATOR
// ==========================================
const dob = ref('2000-01-01');
const targetDate = ref(new Date().toISOString().split('T')[0]);

const ageResult = computed(() => {
    if (!dob.value || !targetDate.value) return null;
    const start = new Date(dob.value);
    const end = new Date(targetDate.value);
    if (end < start) return null;

    let y = end.getFullYear() - start.getFullYear();
    let m = end.getMonth() - start.getMonth();
    let d = end.getDate() - start.getDate();

    if (d < 0) {
        m -= 1;
        const prevMonth = new Date(end.getFullYear(), end.getMonth(), 0);
        d += prevMonth.getDate();
    }
    if (m < 0) {
        y -= 1;
        m += 12;
    }

    const diffMs = end.getTime() - start.getTime();
    const totalDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const totalWeeks = Math.floor(totalDays / 7);
    const totalMonths = y * 12 + m;

    // Next birthday countdown
    const nextBday = new Date(end.getFullYear(), start.getMonth(), start.getDate());
    if (nextBday < end) {
        nextBday.setFullYear(end.getFullYear() + 1);
    }
    const daysToNextBday = Math.ceil((nextBday.getTime() - end.getTime()) / (1000 * 60 * 60 * 24));

    return {
        years: y,
        months: m,
        days: d,
        totalMonths,
        totalWeeks,
        totalDays,
        daysToNextBday,
    };
});

function copyAgeSummary() {
    if (!ageResult.value) return;
    const text = `আমার বয়স: ${ageResult.value.years} বছর, ${ageResult.value.months} মাস, ${ageResult.value.days} দিন (মোট ${ageResult.value.totalDays} দিন)। হিসাবের তারিখ: ${targetDate.value}`;
    navigator.clipboard.writeText(text);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
}

// ==========================================
// TOOL 2: INTERACTIVE SCIENTIFIC/STANDARD CALCULATOR
// ==========================================
const calcDisplay = ref('0');
const calcEquation = ref('');
const calcMemory = ref(0);

function calcInput(char) {
    if (calcDisplay.value === '0' && !isNaN(char)) {
        calcDisplay.value = String(char);
    } else {
        calcDisplay.value += String(char);
    }
}

function calcClear() {
    calcDisplay.value = '0';
    calcEquation.value = '';
}

function calcBackspace() {
    if (calcDisplay.value.length > 1) {
        calcDisplay.value = calcDisplay.value.slice(0, -1);
    } else {
        calcDisplay.value = '0';
    }
}

function calcOperator(op) {
    calcEquation.value = calcDisplay.value + ' ' + op + ' ';
    calcDisplay.value = '0';
}

function calcEquals() {
    try {
        const expr = (calcEquation.value + calcDisplay.value).replace(/×/g, '*').replace(/÷/g, '/');
        // Sanitized arithmetic evaluation
        const sanitized = expr.replace(/[^0-9+\-*/().]/g, '');
        const res = Function(`'use strict'; return (${sanitized})`)();
        calcEquation.value = expr + ' =';
        calcDisplay.value = String(Math.round(res * 10000000) / 10000000);
    } catch {
        calcDisplay.value = 'Error';
    }
}

function calcFunc(fn) {
    const val = parseFloat(calcDisplay.value) || 0;
    if (fn === 'sqrt') {
        calcDisplay.value = String(Math.sqrt(val));
    } else if (fn === 'sq') {
        calcDisplay.value = String(val * val);
    } else if (fn === 'recip') {
        calcDisplay.value = String(val !== 0 ? 1 / val : 'Error');
    } else if (fn === 'neg') {
        calcDisplay.value = String(val * -1);
    } else if (fn === 'percent') {
        calcDisplay.value = String(val / 100);
    }
}

// ==========================================
// TOOL 3: TELETALK & ADMISSION PHOTO & SIGNATURE RESIZER
// ==========================================
const resizerPreset = ref('photo300'); // photo300 | sig300 | custom
const customWidth = ref(300);
const customHeight = ref(300);
const customMaxKb = ref(100);

const originalImage = ref(null);
const originalFile = ref(null);
const resizedDataUrl = ref(null);
const resizedFileSizeKb = ref(0);
const isProcessingImage = ref(false);

const currentWidth = computed(() => {
    if (resizerPreset.value === 'photo300') return 300;
    if (resizerPreset.value === 'sig300') return 300;
    return customWidth.value;
});

const currentHeight = computed(() => {
    if (resizerPreset.value === 'photo300') return 300;
    if (resizerPreset.value === 'sig300') return 80;
    return customHeight.value;
});

const currentMaxKb = computed(() => {
    if (resizerPreset.value === 'photo300') return 100;
    if (resizerPreset.value === 'sig300') return 60;
    return customMaxKb.value;
});

function handleImageUpload(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    originalFile.value = file;
    const reader = new FileReader();
    reader.onload = (event) => {
        const img = new Image();
        img.onload = () => {
            originalImage.value = img;
            processImage();
        };
        img.src = event.target.result;
    };
    reader.readAsDataURL(file);
}

function processImage() {
    if (!originalImage.value) return;
    isProcessingImage.value = true;

    setTimeout(() => {
        const canvas = document.createElement('canvas');
        canvas.width = currentWidth.value;
        canvas.height = currentHeight.value;
        const ctx = canvas.getContext('2d');

        // Draw image stretched or fitted to exact dimension as required by Teletalk/Admissions
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(originalImage.value, 0, 0, canvas.width, canvas.height);

        // Compress quality iteratively to meet target max KB
        let quality = 0.95;
        let dataUrl = canvas.toDataURL('image/jpeg', quality);
        let sizeKb = Math.round((dataUrl.length * 3) / 4 / 1024);

        while (sizeKb > currentMaxKb.value && quality > 0.1) {
            quality -= 0.05;
            dataUrl = canvas.toDataURL('image/jpeg', quality);
            sizeKb = Math.round((dataUrl.length * 3) / 4 / 1024);
        }

        resizedDataUrl.value = dataUrl;
        resizedFileSizeKb.value = sizeKb;
        isProcessingImage.value = false;
    }, 50);
}

watch([resizerPreset, customWidth, customHeight, customMaxKb], () => {
    if (originalImage.value) {
        processImage();
    }
});

// ==========================================
// TOOL 4: GPA & CGPA CALCULATOR
// ==========================================
const gpaMode = ref('ssc_hsc'); // ssc_hsc | honours
const sscSubjects = ref([
    { name: 'বাংলা (Bangla)', grade: '5.0' },
    { name: 'ইংরেজি (English)', grade: '5.0' },
    { name: 'গণিত (Mathematics)', grade: '5.0' },
    { name: 'পদার্থ/হিসাব/ইতিহাস', grade: '5.0' },
    { name: 'রসায়ন/ব্যবসায়/পৌরনীতি', grade: '5.0' },
    { name: 'জীববিজ্ঞান/অর্থনীতি', grade: '5.0' },
    { name: 'বাংলাদেশ ও বিশ্বপরিচয়', grade: '5.0' },
]);
const fourthSubjectGrade = ref('5.0');

const sscCalculatedGpa = computed(() => {
    let sum = 0;
    for (const sub of sscSubjects.value) {
        const val = parseFloat(sub.grade);
        if (val === 0) return 0.0; // Fail in any main subject leads to fail
        sum += val;
    }
    const fourthVal = parseFloat(fourthSubjectGrade.value);
    const bonus = Math.max(0, fourthVal - 2.0); // Points above 2.0 count as bonus
    const totalGpa = (sum + bonus) / sscSubjects.value.length;
    return Math.min(5.00, Math.round(totalGpa * 100) / 100);
});

// Honours CGPA
const honoursSemesters = ref([
    { name: '১ম বর্ষ / সেমিস্টার', gpa: 3.50, credits: 30 },
    { name: '২য় বর্ষ / সেমিস্টার', gpa: 3.60, credits: 30 },
    { name: '৩য় বর্ষ / সেমিস্টার', gpa: 3.70, credits: 30 },
    { name: '৪র্থ বর্ষ / সেমিস্টার', gpa: 3.80, credits: 30 },
]);

const honoursCgpa = computed(() => {
    let totalPoints = 0;
    let totalCredits = 0;
    for (const s of honoursSemesters.value) {
        const g = parseFloat(s.gpa) || 0;
        const c = parseFloat(s.credits) || 0;
        totalPoints += g * c;
        totalCredits += c;
    }
    if (totalCredits === 0) return 0.0;
    return Math.round((totalPoints / totalCredits) * 100) / 100;
});

// ==========================================
// TOOL 5: WORD & CHARACTER COUNTER
// ==========================================
const textInput = ref('');

const textStats = computed(() => {
    const raw = textInput.value;
    const charCount = raw.length;
    const charNoSpaces = raw.replace(/\s/g, '').length;
    const words = raw.trim() ? raw.trim().split(/\s+/).length : 0;
    const sentences = raw.trim() ? (raw.match(/[\.\?!।]|\n+/g) || []).length : 0;
    const readingTimeMins = Math.ceil(words / 180);
    return {
        charCount,
        charNoSpaces,
        words,
        sentences: Math.max(sentences, words > 0 ? 1 : 0),
        readingTimeMins,
    };
});

function convertText(mode) {
    if (mode === 'upper') {
        textInput.value = textInput.value.toUpperCase();
    } else if (mode === 'lower') {
        textInput.value = textInput.value.toLowerCase();
    } else if (mode === 'title') {
        textInput.value = textInput.value.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
    } else if (mode === 'clear') {
        textInput.value = '';
    }
}
</script>

<template>
    <Head title="স্টুডেন্ট অনলাইন টুলস হাব | Sikhun.com" />

    <PublicLayout>
        <!-- Hero Header -->
        <header class="bg-gradient-to-b from-[#111622] via-[#0d111a] to-[var(--bg)] border-b border-white/5 py-12 md:py-16">
            <div class="max-w-6xl mx-auto px-4 md:px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-[var(--primary)]/15 border border-[var(--primary)]/30 text-[var(--primary)] text-xs font-bold uppercase tracking-wider mb-3">
                    <SparklesIcon class="w-4 h-4" /> ১০০% ফ্রি অনলাইন স্টুডেন্ট টুলস (লগইন ছাড়াই)
                </span>
                <h1 class="font-heading text-3xl md:text-5xl font-extrabold text-white tracking-tight">
                    স্টুডেন্ট অনলাইন <span class="text-gradient">টুলস হাব</span>
                </h1>
                <p class="mt-3 max-w-2xl mx-auto text-xs sm:text-sm text-gray-300 leading-relaxed">
                    বয়স হিসাব, ছবি ও স্বাক্ষর রিসাইজার, সাধারণ ক্যালকুলেটর, জিপিএ ও টেক্সট কনভার্টার—পড়াশোনা ও আবেদনের সকল গুরুত্বপূর্ণ ডিজিটাল ইউটিলিটি এক জায়গায়।
                </p>

                <!-- Tool Category Switcher Tabs -->
                <div class="mt-8 flex items-center justify-center gap-2 flex-wrap">
                    <button
                        type="button"
                        @click="activeTab = 'age'"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'age' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-lg shadow-[var(--primary)]/20' : 'bg-white/5 text-gray-300 border-white/10 hover:bg-white/10'"
                    >
                        <CalendarDaysIcon class="w-4 h-4" /> সাধারণ বয়স ক্যালকুলেটর
                    </button>

                    <button
                        type="button"
                        @click="activeTab = 'resizer'"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'resizer' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-lg shadow-[var(--primary)]/20' : 'bg-white/5 text-gray-300 border-white/10 hover:bg-white/10'"
                    >
                        <PhotoIcon class="w-4 h-4" /> ছবি ও স্বাক্ষর রিসাইজার
                    </button>

                    <button
                        type="button"
                        @click="activeTab = 'calculator'"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'calculator' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-lg shadow-[var(--primary)]/20' : 'bg-white/5 text-gray-300 border-white/10 hover:bg-white/10'"
                    >
                        <CalculatorIcon class="w-4 h-4" /> সাধারণ ক্যালকুলেটর
                    </button>

                    <button
                        type="button"
                        @click="activeTab = 'gpa'"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'gpa' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-lg shadow-[var(--primary)]/20' : 'bg-white/5 text-gray-300 border-white/10 hover:bg-white/10'"
                    >
                        <AcademicCapIcon class="w-4 h-4" /> GPA ও CGPA ক্যালকুলেটর
                    </button>

                    <button
                        type="button"
                        @click="activeTab = 'text'"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'text' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-lg shadow-[var(--primary)]/20' : 'bg-white/5 text-gray-300 border-white/10 hover:bg-white/10'"
                    >
                        <DocumentTextIcon class="w-4 h-4" /> ওয়ার্ড কাউন্টার ও টেক্সট
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 md:px-6 py-10">
            <!-- ========================================== -->
            <!-- 1. GENERAL AGE CALCULATOR -->
            <!-- ========================================== -->
            <section v-if="activeTab === 'age'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-white/10 bg-[#161a26] shadow-xl">
                    <h2 class="font-heading text-xl md:text-2xl font-bold text-white flex items-center gap-2 border-b border-white/10 pb-4">
                        <CalendarDaysIcon class="w-6 h-6 text-amber-400" />
                        সাধারণ বয়স ক্যালকুলেটর (Age Calculator)
                    </h2>
                    <p class="text-xs text-gray-300 mt-2">
                        আপনার জন্মতারিখ প্রদান করে যেকোনো তারিখের সাপেক্ষে বছর, মাস ও দিন নির্ভুলভাবে বের করুন।
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 my-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1.5">আপনার জন্মতারিখ (Date of Birth)</label>
                            <input
                                v-model="dob"
                                type="date"
                                class="w-full px-4 py-3 rounded-2xl bg-[#0e121d] border border-white/10 text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1.5">হিসাবের তারিখ (Target Date)</label>
                            <input
                                v-model="targetDate"
                                type="date"
                                class="w-full px-4 py-3 rounded-2xl bg-[#0e121d] border border-white/10 text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                            />
                            <span class="text-[10px] text-gray-400 mt-1 block">ডিফল্টভাবে আজকের তারিখ নির্ধারিত থাকে।</span>
                        </div>
                    </div>

                    <!-- Age Result Cards -->
                    <div v-if="ageResult" class="p-6 rounded-2xl bg-[#0e121d] border border-white/10 space-y-5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">বয়সের পূর্ণ ফলাফল:</span>
                            <button
                                type="button"
                                @click="copyAgeSummary"
                                class="btn btn-outline text-xs py-1.5 px-3 flex items-center gap-1.5 font-bold"
                            >
                                <ClipboardDocumentIcon class="w-4 h-4" />
                                {{ copied ? 'কপি হয়েছে!' : 'ফলাফল কপি করুন' }}
                            </button>
                        </div>

                        <!-- 3 Big Numbers -->
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div class="p-4 rounded-2xl bg-[#1c2233] border border-white/10">
                                <div class="font-heading text-3xl md:text-5xl font-black text-white">{{ ageResult.years }}</div>
                                <div class="text-xs text-gray-400 font-bold mt-1">বছর (Years)</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-[#1c2233] border border-white/10">
                                <div class="font-heading text-3xl md:text-5xl font-black text-amber-400">{{ ageResult.months }}</div>
                                <div class="text-xs text-gray-400 font-bold mt-1">মাস (Months)</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-[#1c2233] border border-white/10">
                                <div class="font-heading text-3xl md:text-5xl font-black text-emerald-400">{{ ageResult.days }}</div>
                                <div class="text-xs text-gray-400 font-bold mt-1">দিন (Days)</div>
                            </div>
                        </div>

                        <!-- Detailed Breakdown Stats -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-white/10 text-xs">
                            <div class="p-3 rounded-xl bg-[#161a26] text-center">
                                <span class="text-[10px] text-gray-400 block">মোট মাস</span>
                                <strong class="font-mono text-white text-sm">{{ ageResult.totalMonths }} মাস</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-[#161a26] text-center">
                                <span class="text-[10px] text-gray-400 block">মোট সপ্তাহ</span>
                                <strong class="font-mono text-white text-sm">{{ ageResult.totalWeeks }} সপ্তাহ</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-[#161a26] text-center">
                                <span class="text-[10px] text-gray-400 block">মোট দিন</span>
                                <strong class="font-mono text-white text-sm">{{ ageResult.totalDays }} দিন</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-[#161a26] text-center">
                                <span class="text-[10px] text-gray-400 block">পরবর্তী জন্মদিন বাকি</span>
                                <strong class="font-mono text-amber-400 text-sm">{{ ageResult.daysToNextBday }} দিন</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- 2. PHOTO & SIGNATURE RESIZER (TELETALK & ADMISSION) -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'resizer'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-white/10 bg-[#161a26] shadow-xl">
                    <h2 class="font-heading text-xl md:text-2xl font-bold text-white flex items-center gap-2 border-b border-white/10 pb-4">
                        <PhotoIcon class="w-6 h-6 text-emerald-400" />
                        ছবি ও স্বাক্ষর রিসাইজার (Teletalk Photo & Signature Resizer)
                    </h2>
                    <p class="text-xs text-gray-300 mt-2">
                        সরকারি চাকরি ও বিশ্ববিদ্যালয়ের অনলাইন আবেদনের জন্য টেলিটক নির্ধারিত মাপ (৩০০×৩০০ ও ৩০০×৮০) এবং নির্দিষ্ট কেবিতে (১০০ KB / ৬০ KB) স্বয়ংক্রিয়ভাবে রিসাইজ ও কম্প্রেস করুন।
                    </p>

                    <!-- Presets Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 my-6">
                        <button
                            type="button"
                            @click="resizerPreset = 'photo300'"
                            class="p-4 rounded-2xl border text-left transition-all"
                            :class="resizerPreset === 'photo300' ? 'border-[var(--primary)] bg-[var(--primary)]/10 ring-2 ring-[var(--primary)]/30' : 'border-white/10 bg-[#0e121d] hover:border-white/20'"
                        >
                            <div class="text-xs font-bold text-white">টেলিটক ও চাকরি ছবি</div>
                            <div class="text-[11px] text-gray-400 mt-1">300 × 300 px (সর্বোচ্চ 100 KB)</div>
                        </button>

                        <button
                            type="button"
                            @click="resizerPreset = 'sig300'"
                            class="p-4 rounded-2xl border text-left transition-all"
                            :class="resizerPreset === 'sig300' ? 'border-[var(--primary)] bg-[var(--primary)]/10 ring-2 ring-[var(--primary)]/30' : 'border-white/10 bg-[#0e121d] hover:border-white/20'"
                        >
                            <div class="text-xs font-bold text-white">টেলিটক ও চাকরি স্বাক্ষর</div>
                            <div class="text-[11px] text-gray-400 mt-1">300 × 80 px (সর্বোচ্চ 60 KB)</div>
                        </button>

                        <button
                            type="button"
                            @click="resizerPreset = 'custom'"
                            class="p-4 rounded-2xl border text-left transition-all"
                            :class="resizerPreset === 'custom' ? 'border-[var(--primary)] bg-[var(--primary)]/10 ring-2 ring-[var(--primary)]/30' : 'border-white/10 bg-[#0e121d] hover:border-white/20'"
                        >
                            <div class="text-xs font-bold text-white">কাস্টম সাইজ</div>
                            <div class="text-[11px] text-gray-400 mt-1">নিজের মতো Width, Height ও KB</div>
                        </button>
                    </div>

                    <!-- Custom Inputs if Custom Preset -->
                    <div v-if="resizerPreset === 'custom'" class="p-4 rounded-2xl bg-[#0e121d] border border-white/10 grid grid-cols-3 gap-3 mb-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-300 mb-1">প্রস্থ (Width px)</label>
                            <input v-model.number="customWidth" type="number" class="input w-full text-xs py-2" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-300 mb-1">উচ্চতা (Height px)</label>
                            <input v-model.number="customHeight" type="number" class="input w-full text-xs py-2" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-300 mb-1">সর্বোচ্চ সাইজ (Max KB)</label>
                            <input v-model.number="customMaxKb" type="number" class="input w-full text-xs py-2" />
                        </div>
                    </div>

                    <!-- File Dropzone -->
                    <div class="p-8 rounded-3xl border-2 border-dashed border-white/20 bg-[#0e121d] text-center space-y-3">
                        <PhotoIcon class="w-12 h-12 text-gray-400 mx-auto" />
                        <div class="text-sm font-bold text-white">আপনার ছবি বা স্বাক্ষরের ফাইল নির্বাচন করুন</div>
                        <p class="text-xs text-gray-400">PNG, JPG অথবা WEBP ফরম্যাট সমর্থিত। আপনার ব্রাউজারে সম্পূর্ণ নিরাপদে রিসাইজ হবে।</p>
                        <label class="btn btn-primary inline-flex items-center gap-2 text-xs py-2.5 px-6 font-bold cursor-pointer">
                            <span>কম্পিউটার বা ফোন থেকে ছবি বেছে নিন</span>
                            <input type="file" accept="image/*" @change="handleImageUpload" class="hidden" />
                        </label>
                    </div>

                    <!-- Preview & Download Section -->
                    <div v-if="resizedDataUrl" class="mt-8 p-6 rounded-2xl bg-[#0e121d] border border-white/10 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-6 flex-wrap">
                            <div class="p-2 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center">
                                <img :src="resizedDataUrl" alt="Resized Preview" class="max-h-48 object-contain rounded" />
                            </div>
                            <div class="space-y-1 text-xs">
                                <div class="text-emerald-400 font-bold flex items-center gap-1.5">
                                    <CheckIcon class="w-4 h-4" /> রিসাইজ ও কম্প্রেস সম্পন্ন হয়েছে
                                </div>
                                <div class="text-gray-300">মাপ: <strong>{{ currentWidth }} × {{ currentHeight }} পিক্সেল</strong></div>
                                <div class="text-gray-300">ফাইল সাইজ: <strong>{{ resizedFileSizeKb }} KB</strong> (অনুমোদিত: সর্বোচ্চ {{ currentMaxKb }} KB)</div>
                                <div class="text-[11px] text-gray-400">ফরম্যাট: JPEG (টেলিটক ও বিশ্ববিদ্যালয় গৃহীত)</div>
                            </div>
                        </div>

                        <div>
                            <a
                                :href="resizedDataUrl"
                                :download="`sikhun-${resizerPreset}-${currentWidth}x${currentHeight}.jpg`"
                                class="btn btn-primary text-xs py-3 px-6 font-bold flex items-center gap-2 shadow-lg shadow-[var(--primary)]/20"
                            >
                                <ArrowDownTrayIcon class="w-4 h-4" /> রিসাইজড ছবি ডাউনলোড করুন
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- 3. INTERACTIVE GENERAL & SCIENTIFIC CALCULATOR -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'calculator'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-white/10 bg-[#161a26] shadow-xl max-w-xl mx-auto">
                    <h2 class="font-heading text-xl md:text-2xl font-bold text-white flex items-center gap-2 border-b border-white/10 pb-4">
                        <CalculatorIcon class="w-6 h-6 text-purple-400" />
                        সাধারণ ও ডিজিটাল ক্যালকুলেটর (Calculator)
                    </h2>

                    <!-- LCD Screen -->
                    <div class="my-5 p-4 rounded-2xl bg-[#0b0e17] border border-white/10 text-right space-y-1 shadow-inner">
                        <div class="text-xs font-mono text-gray-400 h-5 truncate">{{ calcEquation }}</div>
                        <div class="font-mono text-3xl sm:text-4xl font-extrabold text-white tracking-tight truncate">{{ calcDisplay }}</div>
                    </div>

                    <!-- Keypad Grid -->
                    <div class="grid grid-cols-4 gap-2.5">
                        <button type="button" @click="calcClear" class="p-3.5 rounded-xl bg-rose-500/20 text-rose-400 hover:bg-rose-500/30 font-bold text-sm transition">C</button>
                        <button type="button" @click="calcBackspace" class="p-3.5 rounded-xl bg-white/5 text-gray-300 hover:bg-white/10 font-bold text-sm transition">⌫</button>
                        <button type="button" @click="calcFunc('percent')" class="p-3.5 rounded-xl bg-white/5 text-gray-300 hover:bg-white/10 font-bold text-sm transition">%</button>
                        <button type="button" @click="calcOperator('÷')" class="p-3.5 rounded-xl bg-amber-500/20 text-amber-400 hover:bg-amber-500/30 font-bold text-base transition">÷</button>

                        <button type="button" @click="calcFunc('sqrt')" class="p-3 rounded-xl bg-white/5 text-gray-300 hover:bg-white/10 font-mono text-xs transition">√x</button>
                        <button type="button" @click="calcFunc('sq')" class="p-3 rounded-xl bg-white/5 text-gray-300 hover:bg-white/10 font-mono text-xs transition">x²</button>
                        <button type="button" @click="calcFunc('recip')" class="p-3 rounded-xl bg-white/5 text-gray-300 hover:bg-white/10 font-mono text-xs transition">1/x</button>
                        <button type="button" @click="calcOperator('×')" class="p-3.5 rounded-xl bg-amber-500/20 text-amber-400 hover:bg-amber-500/30 font-bold text-base transition">×</button>

                        <button type="button" @click="calcInput('7')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">7</button>
                        <button type="button" @click="calcInput('8')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">8</button>
                        <button type="button" @click="calcInput('9')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">9</button>
                        <button type="button" @click="calcOperator('-')" class="p-3.5 rounded-xl bg-amber-500/20 text-amber-400 hover:bg-amber-500/30 font-bold text-base transition">−</button>

                        <button type="button" @click="calcInput('4')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">4</button>
                        <button type="button" @click="calcInput('5')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">5</button>
                        <button type="button" @click="calcInput('6')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">6</button>
                        <button type="button" @click="calcOperator('+')" class="p-3.5 rounded-xl bg-amber-500/20 text-amber-400 hover:bg-amber-500/30 font-bold text-base transition">+</button>

                        <button type="button" @click="calcInput('1')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">1</button>
                        <button type="button" @click="calcInput('2')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">2</button>
                        <button type="button" @click="calcInput('3')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">3</button>
                        <button type="button" @click="calcEquals" class="row-span-2 p-3.5 rounded-xl bg-[var(--primary)] text-white hover:bg-[var(--primary-hover)] font-bold text-lg flex items-center justify-center shadow-md transition">=</button>

                        <button type="button" @click="calcFunc('neg')" class="p-3.5 rounded-xl bg-white/5 text-gray-300 hover:bg-white/10 font-bold text-sm transition">±</button>
                        <button type="button" @click="calcInput('0')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">0</button>
                        <button type="button" @click="calcInput('.')" class="p-3.5 rounded-xl bg-[#1f2434] text-white hover:bg-[#282e42] font-bold text-base transition">.</button>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- 4. ACADEMIC GPA & CGPA CALCULATOR -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'gpa'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-white/10 bg-[#161a26] shadow-xl">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4 flex-wrap gap-3">
                        <h2 class="font-heading text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                            <AcademicCapIcon class="w-6 h-6 text-blue-400" />
                            GPA ও CGPA ক্যালকুলেটর
                        </h2>

                        <div class="inline-flex rounded-xl bg-[#0e121d] p-1 border border-white/10">
                            <button
                                type="button"
                                @click="gpaMode = 'ssc_hsc'"
                                :class="gpaMode === 'ssc_hsc' ? 'bg-[var(--primary)] text-white font-bold' : 'text-gray-400'"
                                class="px-3 py-1.5 rounded-lg text-xs transition"
                            >
                                SSC / HSC GPA
                            </button>
                            <button
                                type="button"
                                @click="gpaMode = 'honours'"
                                :class="gpaMode === 'honours' ? 'bg-[var(--primary)] text-white font-bold' : 'text-gray-400'"
                                class="px-3 py-1.5 rounded-lg text-xs transition"
                            >
                                বিশ্ববিদ্যালয় / NU CGPA
                            </button>
                        </div>
                    </div>

                    <!-- SSC/HSC GPA Mode -->
                    <div v-if="gpaMode === 'ssc_hsc'" class="space-y-5 my-6">
                        <div class="p-4 rounded-2xl bg-[#0e121d] border border-white/10 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-gray-400 block">হিসাবকৃত মোট জিপিএ (৪র্থ বিষয় সহ):</span>
                                <div class="font-heading text-3xl md:text-4xl font-black text-amber-400 mt-0.5">
                                    {{ sscCalculatedGpa.toFixed(2) }} <span class="text-xs font-normal text-gray-400">/ 5.00</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold px-3 py-1 rounded-full" :class="sscCalculatedGpa >= 5.0 ? 'bg-amber-500/20 text-amber-400' : 'bg-emerald-500/20 text-emerald-400'">
                                {{ sscCalculatedGpa >= 5.0 ? 'GPA 5.00 (A+)' : sscCalculatedGpa >= 4.0 ? 'Grade A' : sscCalculatedGpa >= 3.5 ? 'Grade A-' : 'Passed' }}
                            </span>
                        </div>

                        <!-- Main Subjects Table -->
                        <div class="space-y-2">
                            <div class="text-xs font-bold text-white">আবশ্যিক বিষয়সমূহের গ্রেড পয়েন্ট:</div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div v-for="(sub, idx) in sscSubjects" :key="idx" class="p-3 rounded-xl bg-[#0e121d] border border-white/10 flex items-center justify-between">
                                    <span class="text-xs text-gray-300 truncate mr-2">{{ sub.name }}</span>
                                    <select v-model="sub.grade" class="px-3 py-1.5 rounded-lg bg-[#1a1f2e] border border-white/10 text-white text-xs">
                                        <option value="5.0">A+ (5.0)</option>
                                        <option value="4.0">A (4.0)</option>
                                        <option value="3.5">A- (3.5)</option>
                                        <option value="3.0">B (3.0)</option>
                                        <option value="2.0">C (2.0)</option>
                                        <option value="1.0">D (1.0)</option>
                                        <option value="0.0">F (0.0)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 4th Subject -->
                        <div class="p-4 rounded-2xl bg-[#0e121d] border border-white/10 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-white block">৪র্থ ঐচ্ছিক বিষয় (Optional 4th Subject):</span>
                                <span class="text-[11px] text-gray-400">নিয়মানুযায়ী ২.০ পয়েন্টের অতিরিক্ত অংশ বোনাস হিসেবে মোট জিপিএতে যোগ হয়।</span>
                            </div>
                            <select v-model="fourthSubjectGrade" class="px-3 py-1.5 rounded-lg bg-[#1a1f2e] border border-white/10 text-white text-xs">
                                <option value="5.0">A+ (5.0 - ৩ বোনাস পয়েন্ট)</option>
                                <option value="4.0">A (4.0 - ২ বোনাস পয়েন্ট)</option>
                                <option value="3.5">A- (3.5 - ১.৫ বোনাস পয়েন্ট)</option>
                                <option value="3.0">B (3.0 - ১ বোনাস পয়েন্ট)</option>
                                <option value="2.0">C (2.0 - কোনো বোনাস নেই)</option>
                                <option value="0.0">F (0.0)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Honours CGPA Mode -->
                    <div v-else class="space-y-5 my-6">
                        <div class="p-4 rounded-2xl bg-[#0e121d] border border-white/10 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-gray-400 block">জাতীয় বিশ্ববিদ্যালয় / অনার্স সিজিপিএ:</span>
                                <div class="font-heading text-3xl md:text-4xl font-black text-emerald-400 mt-0.5">
                                    {{ honoursCgpa.toFixed(2) }} <span class="text-xs font-normal text-gray-400">/ 4.00</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400">
                                {{ honoursCgpa >= 3.75 ? 'First Class (Distinction)' : honoursCgpa >= 3.0 ? 'First Class' : 'Second Class' }}
                            </span>
                        </div>

                        <div class="space-y-3">
                            <div v-for="(sem, idx) in honoursSemesters" :key="idx" class="p-4 rounded-2xl bg-[#0e121d] border border-white/10 grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
                                <span class="text-xs font-bold text-white">{{ sem.name }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-gray-400">জিপিএ:</span>
                                    <input v-model.number="sem.gpa" type="number" step="0.01" min="0" max="4" class="input w-full text-xs py-1.5" />
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-gray-400">ক্রেডিট:</span>
                                    <input v-model.number="sem.credits" type="number" step="1" min="0" class="input w-full text-xs py-1.5" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- 5. WORD & CHARACTER COUNTER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'text'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-white/10 bg-[#161a26] shadow-xl">
                    <h2 class="font-heading text-xl md:text-2xl font-bold text-white flex items-center gap-2 border-b border-white/10 pb-4">
                        <DocumentTextIcon class="w-6 h-6 text-pink-400" />
                        ওয়ার্ড কাউন্টার ও টেক্সট কনভার্টার (Word Counter)
                    </h2>
                    <p class="text-xs text-gray-300 mt-2">
                        রচনা, আবেদনপত্র বা সিভি লেখার সময় রিয়েল-টাইমে মোট শব্দ, বর্ণ, বাক্য ও পড়ার আনুমানিক সময় যাচাই করুন।
                    </p>

                    <!-- Textarea -->
                    <div class="my-5">
                        <textarea
                            v-model="textInput"
                            rows="8"
                            placeholder="এখানে আপনার টেক্সট পেস্ট করুন অথবা লিখুন..."
                            class="w-full p-4 rounded-2xl bg-[#0e121d] border border-white/10 text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] leading-relaxed"
                        ></textarea>
                    </div>

                    <!-- Stats Counter Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-5 text-center">
                        <div class="p-3.5 rounded-2xl bg-[#0e121d] border border-white/10">
                            <span class="text-[10px] text-gray-400 block font-bold">শব্দ (WORDS)</span>
                            <strong class="font-mono text-xl text-white">{{ textStats.words }}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-[#0e121d] border border-white/10">
                            <span class="text-[10px] text-gray-400 block font-bold">বর্ণ (CHARACTERS)</span>
                            <strong class="font-mono text-xl text-amber-400">{{ textStats.charCount }}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-[#0e121d] border border-white/10">
                            <span class="text-[10px] text-gray-400 block font-bold">স্পেস ছাড়া বর্ণ</span>
                            <strong class="font-mono text-xl text-emerald-400">{{ textStats.charNoSpaces }}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-[#0e121d] border border-white/10">
                            <span class="text-[10px] text-gray-400 block font-bold">মোট বাক্য</span>
                            <strong class="font-mono text-xl text-purple-400">{{ textStats.sentences }}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-[#0e121d] border border-white/10 col-span-2 sm:col-span-1">
                            <span class="text-[10px] text-gray-400 block font-bold">পড়ার সময়</span>
                            <strong class="font-mono text-xl text-blue-400">{{ textStats.readingTimeMins }} মিনিট</strong>
                        </div>
                    </div>

                    <!-- Quick Transformation Buttons -->
                    <div class="flex items-center gap-2 flex-wrap pt-3 border-t border-white/10">
                        <button type="button" @click="convertText('upper')" class="btn btn-outline text-xs py-1.5 px-3">UPPERCASE</button>
                        <button type="button" @click="convertText('lower')" class="btn btn-outline text-xs py-1.5 px-3">lowercase</button>
                        <button type="button" @click="convertText('title')" class="btn btn-outline text-xs py-1.5 px-3">Title Case</button>
                        <button type="button" @click="convertText('clear')" class="btn btn-outline text-xs py-1.5 px-3 text-rose-400 border-rose-500/30">মুছে ফেলুন</button>
                    </div>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>
