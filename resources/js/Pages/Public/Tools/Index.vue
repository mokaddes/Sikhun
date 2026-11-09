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
    ClockIcon,
    CodeBracketIcon,
    CommandLineIcon,
    ArrowLeftIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    BookOpenIcon,
    CheckCircleIcon,
    CpuChipIcon,
} from '@heroicons/vue/24/outline';

// Import New Study & Tech Tool Components
import PomodoroTimer from './Components/PomodoroTimer.vue';
import StudyPlanner from './Components/StudyPlanner.vue';
import FormulaConverter from './Components/FormulaConverter.vue';
import CodeFormatter from './Components/CodeFormatter.vue';
import JsonViewer from './Components/JsonViewer.vue';
import MarkdownEditor from './Components/MarkdownEditor.vue';
import RegexTester from './Components/RegexTester.vue';

const props = defineProps({
    recentJobs: Array,
});

const { t, locale } = useI18n();
const en = computed(() => locale.value === 'en');

// Active view: 'catalog' | 'pomodoro' | 'routine' | 'formulas' | 'code_formatter' | 'json_viewer' | 'markdown' | 'regex' | 'age' | 'resizer' | 'calculator' | 'gpa' | 'text'
const activeTab = ref('catalog');
const categoryFilter = ref('all'); // 'all' | 'study' | 'tech' | 'career'
const searchQuery = ref('');
const copied = ref(false);

// ==========================================
// TOOLS DIRECTORY DATA
// ==========================================
const toolsList = [
    // --- Study & Academic Tools ---
    {
        id: 'pomodoro',
        category: 'study',
        titleBn: 'পোমোডোরো স্টাডি টাইমার',
        titleEn: 'Pomodoro Study Timer',
        descBn: '২৫ মিনিট ফোকাস স্টাডি ও ৫ মিনিট ব্রেক সাইকেল, সাথে ব্যাকগ্রাউন্ড রেইন অ্যাম্বিয়েন্স সাউন্ড।',
        descEn: '25-minute focus intervals with ambient study sounds to boost focus and productivity.',
        icon: ClockIcon,
        badgeBn: 'জনপ্রিয় ফোকাস',
        badgeEn: 'Popular Focus',
        badgeColor: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        color: 'text-rose-500',
    },
    {
        id: 'routine',
        category: 'study',
        titleBn: 'দৈনিক পড়ার রুটিন ও প্ল্যানার',
        titleEn: 'Daily Study Routine & Planner',
        descBn: 'সকাল থেকে রাত পড়ার সময়সূচি তৈরি করুন এবং ১-ক্লিকে সুন্দর লেআউটে প্রিন্ট বা PDF ডাউনলোড করুন।',
        descEn: 'Build personal study routines with target hours and 1-click print/save as PDF.',
        icon: CalendarDaysIcon,
        badgeBn: 'প্রিন্ট রেডি',
        badgeEn: 'Print Ready',
        badgeColor: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
        color: 'text-indigo-500',
    },
    {
        id: 'formulas',
        category: 'study',
        titleBn: 'বিজ্ঞান-গণিত সূত্র ও একক কনভার্টার',
        titleEn: 'Formulas & Unit Converter',
        descBn: 'পদার্থ, রসায়ন ও গণিতের গুরুত্বপূর্ণ সকল সমীকরণ এবং যেকোনো পরিমাপের একক কনভার্টার।',
        descEn: 'Physics, Chemistry, and Math essential formula sheet with reactive unit conversions.',
        icon: CalculatorIcon,
        badgeBn: 'একাডেমিক',
        badgeEn: 'Academic',
        badgeColor: 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border-cyan-500/20',
        color: 'text-cyan-500',
    },
    {
        id: 'gpa',
        category: 'study',
        titleBn: 'এসএসসি/এইচএসসি GPA ও CGPA ক্যালকুলেটর',
        titleEn: 'GPA & CGPA Calculator',
        descBn: '৪র্থ বিষয়সহ মোট গ্রেড পয়েন্ট হিসাব এবং অনার্স বা ডিপ্লোমার সেমিস্টার সিজিপিএ বের করার টুল।',
        descEn: 'Calculate SSC/HSC GPA with 4th subject and university honors semester CGPA.',
        icon: AcademicCapIcon,
        badgeBn: 'বোর্ড স্ট্যান্ডার্ড',
        badgeEn: 'Board Standard',
        badgeColor: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        color: 'text-emerald-500',
    },

    // --- Tech & Developer Tools ---
    {
        id: 'code_formatter',
        category: 'tech',
        titleBn: 'কোড ফরম্যাটার ও বিউটিফায়ার',
        titleEn: 'Code Formatter & Beautifier',
        descBn: 'HTML, CSS, JavaScript, JSON এবং SQL কোড ১-ক্লিকে নিখুঁত ইন্ডেন্টেশনে সাজিয়ে নিন।',
        descEn: 'Format, beautify, and minify HTML, CSS, JavaScript, JSON, and SQL instantly.',
        icon: CodeBracketIcon,
        badgeBn: 'কোডিং টুল',
        badgeEn: 'Coding Tool',
        badgeColor: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        color: 'text-emerald-500',
    },
    {
        id: 'json_viewer',
        category: 'tech',
        titleBn: 'JSON ভ্যালিডেটর ও প্রিটিফায়ার',
        titleEn: 'JSON Validator & Viewer',
        descBn: 'ওয়েব ও এপিআই ডেভেলপমেন্টের জন্য জেসন কোড যাচাই, এরর ডিটেকশন ও ২-স্পেস ফরম্যাটিং।',
        descEn: 'Validate JSON payloads, catch syntax errors with line numbers, and minify/prettify.',
        icon: SparklesIcon,
        badgeBn: 'ডেভেলপার',
        badgeEn: 'Developer',
        badgeColor: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        color: 'text-amber-500',
    },
    {
        id: 'markdown',
        category: 'tech',
        titleBn: 'মার্কডাউন নোট ও PDF এক্সপোর্টার',
        titleEn: 'Markdown Notes to PDF',
        descBn: 'স্প্লিট এডিটরে লাইভ মার্কডাউন নোট লিখুন, কোড ও ফর্মুলা সাজান এবং ১-ক্লিকে PDF প্রিন্ট নিন।',
        descEn: 'Write notes in markdown with instant preview and export directly to clean PDF.',
        icon: DocumentTextIcon,
        badgeBn: 'নোটবুক',
        badgeEn: 'Notebook',
        badgeColor: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
        color: 'text-purple-500',
    },
    {
        id: 'regex',
        category: 'tech',
        titleBn: 'রেজেক্স টেস্টার ও চিটশিট',
        titleEn: 'Regex Tester & Cheatsheet',
        descBn: 'রেগুলার এক্সপ্রেশন লাইভ টেস্ট, বিডি মোবাইল/ইমেইল ভ্যালিডেশন এবং রেজেক্স চিটশিট।',
        descEn: 'Test regular expressions against real text with BD mobile & email patterns.',
        icon: CommandLineIcon,
        badgeBn: 'ভ্যালিডেশন',
        badgeEn: 'Validation',
        badgeColor: 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20',
        color: 'text-orange-500',
    },
    {
        id: 'text',
        category: 'tech',
        titleBn: 'ওয়ার্ড কাউন্টার ও টেক্সট কনভার্টার',
        titleEn: 'Word Counter & Case Tools',
        descBn: 'রিয়েল-টাইম মোট শব্দ, বর্ণ, পড়ার সময় গণনা এবং UPPERCASE/lowercase কনভার্সন।',
        descEn: 'Real-time word, character, and sentence count with instant case transformations.',
        icon: DocumentTextIcon,
        badgeBn: 'ইউটিলিটি',
        badgeEn: 'Utility',
        badgeColor: 'bg-pink-500/10 text-pink-600 dark:text-pink-400 border-pink-500/20',
        color: 'text-pink-500',
    },

    // --- Career & Online Application Tools ---
    {
        id: 'age',
        category: 'career',
        titleBn: 'সাধারণ ও চাকরির বয়স ক্যালকুলেটর',
        titleEn: 'General & Job Age Calculator',
        descBn: 'বছর, মাস, দিন, মোট সপ্তাহ ও পরবর্তী জন্মদিনের কাউন্টডাউন এবং সরকারি চাকরির বয়স যোগ্যতা।',
        descEn: 'Exact age in years, months, days, countdown to next birthday, and govt job limits.',
        icon: CalendarDaysIcon,
        badgeBn: 'জনপ্রিয়',
        badgeEn: 'Popular',
        badgeColor: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        color: 'text-amber-500',
    },
    {
        id: 'resizer',
        category: 'career',
        titleBn: 'চাকরির ছবি ও স্বাক্ষর রিসাইজার',
        titleEn: 'Job Photo & Signature Resizer',
        descBn: 'টেলিটক ও সরকারি চাকরির আবেদনের জন্য ৩০০x৩০০ ছবি (১০০KB) ও ৩০০x৮০ স্বাক্ষর (৬০KB) রূপান্তর।',
        descEn: 'Resize photos (300x300, <100KB) and signatures (300x80, <60KB) for Teletalk govt jobs.',
        icon: PhotoIcon,
        badgeBn: 'সরকারি আবেদন',
        badgeEn: 'Govt Job App',
        badgeColor: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
        color: 'text-blue-500',
    },
    {
        id: 'calculator',
        category: 'career',
        titleBn: 'ইন্টারঅ্যাক্টিভ সাধারণ ক্যালকুলেটর',
        titleEn: 'Standard Calculator',
        descBn: 'ক্যালকুলেটরের বোতাম চেপে বা কিবোর্ড টাইপ করে দ্রুত গাণিতিক হিসাব সম্পন্ন করার টুল।',
        descEn: 'Keyboard & click-friendly standard desktop calculator for day-to-day calculations.',
        icon: CalculatorIcon,
        badgeBn: 'ক্যালকুলেটর',
        badgeEn: 'Calculator',
        badgeColor: 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
        color: 'text-teal-500',
    },
];

const filteredTools = computed(() => {
    return toolsList.filter(t => {
        const matchesCategory = categoryFilter.value === 'all' || t.category === categoryFilter.value;
        if (!searchQuery.value.trim()) return matchesCategory;
        const q = searchQuery.value.toLowerCase();
        return matchesCategory && (
            t.titleBn.toLowerCase().includes(q) ||
            t.titleEn.toLowerCase().includes(q) ||
            t.descBn.toLowerCase().includes(q) ||
            t.descEn.toLowerCase().includes(q)
        );
    });
});

function openTool(toolId) {
    activeTab.value = toolId;
    window.scrollTo({ top: 380, behavior: 'smooth' });
}

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam && toolsList.some(t => t.id === tabParam)) {
        activeTab.value = tabParam;
    }
});

// ==========================================
// TOOL: GENERAL AGE CALCULATOR
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
    const text = en.value
        ? `My Age: ${ageResult.value.years} years, ${ageResult.value.months} months, ${ageResult.value.days} days (Total ${ageResult.value.totalDays} days). Calculation date: ${targetDate.value}`
        : `আমার বয়স: ${ageResult.value.years} বছর, ${ageResult.value.months} মাস, ${ageResult.value.days} দিন (মোট ${ageResult.value.totalDays} দিন)। হিসাবের তারিখ: ${targetDate.value}`;
    navigator.clipboard.writeText(text);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
}

// ==========================================
// TOOL: INTERACTIVE CALCULATOR
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

function calcOp(op) {
    calcEquation.value = calcDisplay.value + ' ' + op + ' ';
    calcDisplay.value = '0';
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

function calcEqual() {
    try {
        const fullExpr = (calcEquation.value + calcDisplay.value).replace(/×/g, '*').replace(/÷/g, '/');
        const res = Function(`'use strict'; return (${fullExpr})`)();
        calcDisplay.value = String(Math.round(res * 10000000) / 10000000);
        calcEquation.value = '';
    } catch (e) {
        calcDisplay.value = 'Error';
    }
}

// Keyboard shortcuts for calculator
function handleKeydown(e) {
    if (activeTab.value !== 'calculator') return;
    if ((e.key >= '0' && e.key <= '9') || e.key === '.') {
        calcInput(e.key);
    } else if (['+', '-', '*', '/'].includes(e.key)) {
        const opMap = { '*': '×', '/': '÷' };
        calcOp(opMap[e.key] || e.key);
    } else if (e.key === 'Enter' || e.key === '=') {
        calcEqual();
    } else if (e.key === 'Backspace') {
        calcBackspace();
    } else if (e.key === 'Escape') {
        calcClear();
    }
}

onMounted(() => { window.addEventListener('keydown', handleKeydown); });
onUnmounted(() => { window.removeEventListener('keydown', handleKeydown); });

// ==========================================
// TOOL: RESIZER
// ==========================================
const resizerMode = ref('photo'); // 'photo' (300x300) | 'signature' (300x80)
const resizerFile = ref(null);
const resizerPreview = ref(null);
const resizerLoading = ref(false);
const resizerError = ref('');
const resizerResult = ref(null);

function onFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;
    resizerFile.value = file;
    resizerError.value = '';
    resizerResult.value = null;

    const reader = new FileReader();
    reader.onload = (evt) => {
        resizerPreview.value = evt.target.result;
    };
    reader.readAsDataURL(file);
}

function processResize() {
    if (!resizerFile.value) {
        resizerError.value = en.value ? 'Please select an image file first.' : 'প্রথমে একটি ছবি বা স্বাক্ষরের ফাইল নির্বাচন করুন।';
        return;
    }

    resizerLoading.value = true;
    resizerError.value = '';

    const targetW = 300;
    const targetH = resizerMode.value === 'photo' ? 300 : 80;
    const maxKb = resizerMode.value === 'photo' ? 100 : 60;

    const img = new Image();
    img.src = resizerPreview.value;
    img.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = targetW;
        canvas.height = targetH;
        const ctx = canvas.getContext('2d');

        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, targetW, targetH);
        ctx.drawImage(img, 0, 0, targetW, targetH);

        let quality = 0.92;
        let dataUrl = canvas.toDataURL('image/jpeg', quality);

        while (dataUrl.length * (3 / 4) > maxKb * 1024 && quality > 0.3) {
            quality -= 0.05;
            dataUrl = canvas.toDataURL('image/jpeg', quality);
        }

        const actualKb = Math.round((dataUrl.length * (3 / 4)) / 1024);

        resizerResult.value = {
            url: dataUrl,
            width: targetW,
            height: targetH,
            sizeKb: actualKb,
            qualityPercent: Math.round(quality * 100),
            name: `${resizerMode.value}_${targetW}x${targetH}.jpg`,
        };
        resizerLoading.value = false;
    };

    img.onerror = () => {
        resizerError.value = en.value ? 'Could not process image file.' : 'ছবিটি প্রসেস করা সম্ভব হয়নি। অন্য একটি ছবি নির্বাচন করুন।';
        resizerLoading.value = false;
    };
}

function downloadResized() {
    if (!resizerResult.value) return;
    const a = document.createElement('a');
    a.href = resizerResult.value.url;
    a.download = resizerResult.value.name;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// ==========================================
// TOOL: GPA & CGPA CALCULATOR
// ==========================================
const gpaType = ref('ssc_hsc'); // 'ssc_hsc' | 'honours'
const sscGroup = ref('science'); // 'science' | 'commerce' | 'humanities'

const sscSubjects = ref([
    { name_bn: 'বাংলা ১ম ও ২য়', name_en: 'Bangla', grade: 5.0 },
    { name_bn: 'ইংরেজি ১ম ও ২য়', name_en: 'English', grade: 5.0 },
    { name_bn: 'গণিত', name_en: 'Mathematics', grade: 5.0 },
    { name_bn: 'পদার্থবিজ্ঞান', name_en: 'Physics', grade: 5.0 },
    { name_bn: 'রসায়ন', name_en: 'Chemistry', grade: 5.0 },
    { name_bn: 'জীববিজ্ঞান', name_en: 'Biology', grade: 5.0 },
    { name_bn: 'বাংলাদেশ ও বিশ্বপরিচয় / আইসিটি', name_en: 'BGS / ICT', grade: 5.0 },
]);

const fourthSubjectGrade = ref(5.0);

const sscCalculatedGpa = computed(() => {
    let sum = 0;
    for (const sub of sscSubjects.value) {
        const val = parseFloat(sub.grade);
        if (val === 0) return 0.0;
        sum += val;
    }
    const fourthVal = parseFloat(fourthSubjectGrade.value);
    const bonus = Math.max(0, fourthVal - 2.0);
    const totalGpa = (sum + bonus) / sscSubjects.value.length;
    return Math.min(5.00, Math.round(totalGpa * 100) / 100);
});

// Honours CGPA
const honoursSemesters = ref([
    { name_bn: '১ম বর্ষ / সেমিস্টার', name_en: '1st Year / Semester', gpa: 3.50, credits: 30 },
    { name_bn: '২য় বর্ষ / সেমিস্টার', name_en: '2nd Year / Semester', gpa: 3.60, credits: 30 },
    { name_bn: '৩য় বর্ষ / সেমিস্টার', name_en: '3rd Year / Semester', gpa: 3.70, credits: 30 },
    { name_bn: '৪র্থ বর্ষ / সেমিস্টার', name_en: '4th Year / Semester', gpa: 3.80, credits: 30 },
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
// TOOL: WORD & CHARACTER COUNTER
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
    <Head :title="en ? 'Student Online Tools Hub | 100% Free Study & Tech Utilities' : 'স্টুডেন্ট অনলাইন টুলস হাব | ১০০% ফ্রি স্টাডি ও টেক ইউটিলিটি - শিখুন'" />

    <PublicLayout>
        <!-- Hero Header -->
        <header class="bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-[#111622] dark:via-[#0d111a] dark:to-[var(--bg)] border-b border-gray-200 dark:border-white/5 py-12 md:py-16">
            <div class="max-w-6xl mx-auto px-4 md:px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-[var(--primary)]/15 border border-[var(--primary)]/30 text-[var(--primary)] text-xs font-bold uppercase tracking-wider mb-3">
                    <SparklesIcon class="w-4 h-4" /> {{ en ? '100% Free Online Student & Tech Utilities (No Login Required)' : '১০০% ফ্রি অনলাইন স্টুডেন্ট ও টেক টুলস (কোনো লগইন ছাড়াই)' }}
                </span>
                <h1 class="font-heading text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    {{ en ? 'Student & Tech' : 'স্টাডি ও টেক অনলাইন' }} <span class="text-gradient">{{ en ? 'Tools Hub' : 'টুলস হাব' }}</span>
                </h1>
                <p class="mt-3 max-w-2xl mx-auto text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ en ? 'Pomodoro focus timer, study planner, formula sheet, code formatter, JSON validator, regex tester, GPA & age calculators—free in one place.' : 'পোমোডোরো স্টাডি টাইমার, পড়ার রুটিন মেকার, সূত্র ও একক কনভার্টার, কোড ফরম্যাটার, জেসন ভ্যালিডেটর, রেজেক্স ও জিপিএ ক্যালকুলেটর—পড়াশোনা ও প্রযুক্তির সকল ফ্রি টুলস এক জায়গায়।' }}
                </p>

                <!-- Search Input Bar -->
                <div class="mt-6 max-w-xl mx-auto relative">
                    <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-4 top-3" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="en ? 'Search any tool by name, topic, or keyword...' : 'টুল খুঁজুন... (যেমন: পোমোডোরো, রুটিন, কোড, জেসন, বয়স, ছবি)'"
                        class="w-full pl-11 pr-4 py-2.5 rounded-2xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-xs sm:text-sm text-gray-900 dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                    />
                </div>

                <!-- Tool Category Switcher Tabs -->
                <div class="mt-6 flex items-center justify-center gap-2 flex-wrap">
                    <button
                        type="button"
                        @click="categoryFilter = 'all'; activeTab = 'catalog'"
                        class="px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 border"
                        :class="categoryFilter === 'all' && activeTab === 'catalog' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-md shadow-[var(--primary)]/20' : 'bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10'"
                    >
                        <span>🌟</span> {{ en ? 'All Tools Catalog' : 'সবগুলো টুলস ক্যাটালগ' }}
                    </button>

                    <button
                        type="button"
                        @click="categoryFilter = 'study'; activeTab = 'catalog'"
                        class="px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 border"
                        :class="categoryFilter === 'study' && activeTab === 'catalog' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-600/20' : 'bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10'"
                    >
                        <span>🎓</span> {{ en ? 'Study & Focus' : 'শিক্ষা ও পড়াশোনা' }}
                    </button>

                    <button
                        type="button"
                        @click="categoryFilter = 'tech'; activeTab = 'catalog'"
                        class="px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 border"
                        :class="categoryFilter === 'tech' && activeTab === 'catalog' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10'"
                    >
                        <span>💻</span> {{ en ? 'Tech & Coding' : 'টেক ও কোডিং' }}
                    </button>

                    <button
                        type="button"
                        @click="categoryFilter = 'career'; activeTab = 'catalog'"
                        class="px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 border"
                        :class="categoryFilter === 'career' && activeTab === 'catalog' ? 'bg-amber-600 text-white border-amber-600 shadow-md shadow-amber-600/20' : 'bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10'"
                    >
                        <span>💼</span> {{ en ? 'Career & Utilities' : 'ক্যারিয়ার ও ইউটিলিটি' }}
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 md:px-6 py-10">
            <!-- ========================================== -->
            <!-- BREADCRUMB / BACK TO CATALOG WHEN TOOL ACTIVE -->
            <!-- ========================================== -->
            <div v-if="activeTab !== 'catalog'" class="mb-6 flex items-center justify-between gap-3">
                <button
                    type="button"
                    @click="activeTab = 'catalog'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 transition"
                >
                    <ArrowLeftIcon class="w-3.5 h-3.5" />
                    <span>{{ en ? 'Back to All Tools Catalog' : '← সকল টুলসের ক্যাটালগে ফিরে যান' }}</span>
                </button>

                <!-- Quick Switcher to Other Tools -->
                <div class="hidden sm:flex items-center gap-2 text-xs">
                    <span class="text-gray-500">{{ en ? 'Switch Tool:' : 'দ্রুত পরিবর্তন:' }}</span>
                    <select
                        v-model="activeTab"
                        class="bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 rounded-xl px-2.5 py-1 text-xs text-gray-900 dark:text-white"
                    >
                        <option v-for="t in toolsList" :key="t.id" :value="t.id">
                            {{ en ? t.titleEn : t.titleBn }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 0. CATALOG CARDS GRID VIEW -->
            <!-- ========================================== -->
            <section v-if="activeTab === 'catalog'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>
                        {{ en ? `Showing ${filteredTools.length} free utilities` : `${filteredTools.length}টি ১০০% ফ্রি টুলস প্রদর্শিত হচ্ছে` }}
                    </span>
                    <span v-if="categoryFilter !== 'all'" class="font-semibold text-[var(--primary)]">
                        {{ en ? `Filtered by ${categoryFilter}` : 'ক্যাটাগরি ফিল্টার সক্রিয়' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div
                        v-for="tool in filteredTools"
                        :key="tool.id"
                        @click="openTool(tool.id)"
                        class="card p-6 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] hover:border-[var(--primary)]/50 transition-all duration-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 cursor-pointer flex flex-col justify-between group"
                    >
                        <div>
                            <!-- Top Badge Strip -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border" :class="tool.badgeColor">
                                    {{ en ? tool.badgeEn : tool.badgeBn }}
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">
                                    {{ en ? '100% Free' : '১০০% ফ্রি' }}
                                </span>
                            </div>

                            <!-- Icon & Title -->
                            <div class="flex items-start gap-3.5 mb-2">
                                <div class="p-2.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5 shrink-0 group-hover:scale-110 transition">
                                    <component :is="tool.icon" class="w-6 h-6" :class="tool.color" />
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-base text-gray-900 dark:text-white group-hover:text-[var(--primary)] transition">
                                        {{ en ? tool.titleEn : tool.titleBn }}
                                    </h3>
                                    <span class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">
                                        {{ tool.category === 'study' ? 'Study Tool' : (tool.category === 'tech' ? 'Developer Tool' : 'Career Tool') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Description -->
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-2 line-clamp-2 leading-relaxed">
                                {{ en ? tool.descEn : tool.descBn }}
                            </p>
                        </div>

                        <!-- Open Action -->
                        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-xs font-bold text-[var(--primary)]">
                            <span>{{ en ? 'Open Tool' : 'ব্যবহার করুন' }}</span>
                            <span class="group-hover:translate-x-1 transition">→</span>
                        </div>
                    </div>
                </div>

                <div v-if="!filteredTools.length" class="card p-12 text-center text-gray-500 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26]">
                    <p class="text-sm font-semibold">{{ en ? 'No tools matched your search.' : 'আপনার সার্চের সাথে কোনো টুল পাওয়া যায়নি।' }}</p>
                    <button type="button" @click="searchQuery = ''; categoryFilter = 'all'" class="btn btn-outline text-xs mt-3">
                        {{ en ? 'Clear Search' : 'সার্চ ক্লিয়ার করুন' }}
                    </button>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- 1. POMODORO STUDY TIMER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'pomodoro'">
                <PomodoroTimer :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 2. DAILY STUDY ROUTINE & PLANNER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'routine'">
                <StudyPlanner :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 3. FORMULA SHEET & UNIT CONVERTER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'formulas'">
                <FormulaConverter :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 4. CODE FORMATTER & BEAUTIFIER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'code_formatter'">
                <CodeFormatter :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 5. JSON VALIDATOR & VIEWER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'json_viewer'">
                <JsonViewer :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 6. MARKDOWN NOTES TO PDF -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'markdown'">
                <MarkdownEditor :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 7. REGEX TESTER & CHEATSHEET -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'regex'">
                <RegexTester :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 8. GENERAL AGE CALCULATOR -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'age'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
                    <h2 class="font-heading text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-200 dark:border-white/10 pb-4">
                        <CalendarDaysIcon class="w-6 h-6 text-amber-500 dark:text-amber-400" />
                        {{ en ? 'General Age Calculator' : 'সাধারণ বয়স ক্যালকুলেটর (Age Calculator)' }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-2">
                        {{ en ? 'Calculate your exact age in years, months, and days from birth date to any specific date.' : 'আপনার জন্মতারিখ থেকে আজকের দিন বা যে কোনো নির্দিষ্ট তারিখ পর্যন্ত নির্ভুল বয়স (বছর, মাস, দিন) হিসাব করুন।' }}
                    </p>

                    <!-- Date Inputs Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ en ? 'Date of Birth (DOB):' : 'জন্মতারিখ:' }}
                            </label>
                            <input
                                v-model="dob"
                                type="date"
                                class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ en ? 'Calculate Age At Date:' : 'যে তারিখ পর্যন্ত হিসাব করবেন:' }}
                            </label>
                            <input
                                v-model="targetDate"
                                type="date"
                                class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                            />
                        </div>
                    </div>

                    <!-- Result Card Display -->
                    <div v-if="ageResult" class="p-6 rounded-3xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/20 text-gray-900 dark:text-white space-y-4">
                        <div class="flex items-center justify-between border-b border-amber-500/20 pb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                {{ en ? 'Calculation Result' : 'বয়সের বিস্তারিত বিবরণ' }}
                            </span>
                            <button
                                type="button"
                                @click="copyAgeSummary"
                                class="btn btn-outline text-xs py-1 px-3 flex items-center gap-1 text-amber-600 dark:text-amber-400 border-amber-500/30"
                            >
                                <CheckIcon v-if="copied" class="w-3.5 h-3.5 text-emerald-500" />
                                <ClipboardDocumentIcon v-else class="w-3.5 h-3.5" />
                                <span>{{ copied ? (en ? 'Copied' : 'কপি হয়েছে') : (en ? 'Copy Result' : 'ফলাফল কপি') }}</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-3 gap-3 text-center py-2">
                            <div class="p-3 rounded-2xl bg-white dark:bg-[#0e121d] border border-amber-500/20">
                                <span class="font-heading text-3xl md:text-4xl font-extrabold text-amber-500">{{ ageResult.years }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block font-semibold mt-1">{{ en ? 'Years' : 'বছর' }}</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-white dark:bg-[#0e121d] border border-amber-500/20">
                                <span class="font-heading text-3xl md:text-4xl font-extrabold text-amber-500">{{ ageResult.months }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block font-semibold mt-1">{{ en ? 'Months' : 'মাস' }}</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-white dark:bg-[#0e121d] border border-amber-500/20">
                                <span class="font-heading text-3xl md:text-4xl font-extrabold text-amber-500">{{ ageResult.days }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block font-semibold mt-1">{{ en ? 'Days' : 'দিন' }}</span>
                            </div>
                        </div>

                        <!-- Secondary Breakdown -->
                        <div class="pt-3 border-t border-amber-500/15 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-center">
                            <div class="p-2 rounded-xl bg-white dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 block">{{ en ? 'Total Months' : 'মোট মাস' }}</span>
                                <strong class="font-mono text-sm text-gray-900 dark:text-white">{{ ageResult.totalMonths }}</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-white dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 block">{{ en ? 'Total Weeks' : 'মোট সপ্তাহ' }}</span>
                                <strong class="font-mono text-sm text-gray-900 dark:text-white">{{ ageResult.totalWeeks }}</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-white dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 block">{{ en ? 'Total Days' : 'মোট দিন' }}</span>
                                <strong class="font-mono text-sm text-gray-900 dark:text-white">{{ ageResult.totalDays }}</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-white dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 block">{{ en ? 'Next Birthday In' : 'পরবর্তী জন্মদিন' }}</span>
                                <strong class="font-mono text-sm text-emerald-500">{{ ageResult.daysToNextBday }} {{ en ? 'days' : 'দিন পর' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- 9. PHOTO & SIGNATURE RESIZER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'resizer'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
                    <h2 class="font-heading text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-200 dark:border-white/10 pb-4">
                        <PhotoIcon class="w-6 h-6 text-blue-500 dark:text-blue-400" />
                        {{ en ? 'Job Application Photo & Signature Resizer' : 'চাকরির ছবি ও স্বাক্ষর রিসাইজার (Teletalk Spec)' }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-2">
                        {{ en ? 'Automatically crop and resize passport photos to 300x300 px (<100KB) and signature to 300x80 px (<60KB) for government and private job applications.' : 'টেলিটক ও সরকারি চাকরির আবেদনের নির্দিষ্ট মাপে ছবি (৩০০x৩০০ পিক্সেল, ১০০ KB এর কম) এবং স্বাক্ষর (৩০০x৮০ পিক্সেল, ৬০ KB এর কম) স্বয়ংক্রিয়ভাবে রূপান্তর ও ডাউনলোড করুন।' }}
                    </p>

                    <!-- Mode Toggle -->
                    <div class="flex items-center gap-3 my-6">
                        <button
                            type="button"
                            @click="resizerMode = 'photo'; resizerResult = null"
                            class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition border"
                            :class="resizerMode === 'photo' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-600/20' : 'bg-gray-100 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                        >
                            📷 {{ en ? 'Photo (300 x 300 px)' : 'পাসপোর্ট ছবি (৩০০x৩০০ পিক্সেল)' }}
                        </button>
                        <button
                            type="button"
                            @click="resizerMode = 'signature'; resizerResult = null"
                            class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition border"
                            :class="resizerMode === 'signature' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-600/20' : 'bg-gray-100 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                        >
                            ✍️ {{ en ? 'Signature (300 x 80 px)' : 'স্বাক্ষর (৩০০x৮০ পিক্সেল)' }}
                        </button>
                    </div>

                    <!-- File Upload Input Area -->
                    <div class="border-2 border-dashed border-gray-300 dark:border-white/15 rounded-3xl p-8 text-center bg-gray-50 dark:bg-[#0e121d] transition hover:border-blue-500">
                        <input
                            type="file"
                            accept="image/*"
                            id="resizerFileInput"
                            class="hidden"
                            @change="onFileSelect"
                        />
                        <label for="resizerFileInput" class="cursor-pointer block">
                            <PhotoIcon class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                            <span class="btn btn-primary text-xs py-2 px-4 inline-block mb-2">
                                {{ en ? 'Choose Image File' : 'ছবি নির্বাচন করুন' }}
                            </span>
                            <p class="text-xs text-gray-500">
                                {{ en ? 'PNG, JPG, JPEG files supported. High quality canvas conversion.' : 'যেকোনো সাইজের জেপিজি বা পিএনজি ছবি আপলোড করুন।' }}
                            </p>
                        </label>
                    </div>

                    <!-- Selected Preview & Action -->
                    <div v-if="resizerPreview" class="mt-6 p-6 rounded-3xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <div class="shrink-0 border border-gray-300 dark:border-white/20 p-1 bg-white rounded-xl">
                                <img
                                    :src="resizerPreview"
                                    alt="Preview"
                                    class="object-contain max-h-40 max-w-40 rounded"
                                />
                            </div>

                            <div class="flex-1 text-center sm:text-left space-y-2">
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white">
                                    {{ resizerFile.name }}
                                </h4>
                                <p class="text-xs text-gray-500">
                                    {{ en ? 'Target Specification:' : 'প্রত্যাশিত সাইজ:' }}
                                    <strong class="text-blue-500 font-mono">{{ resizerMode === 'photo' ? '300x300 px (<100KB)' : '300x80 px (<60KB)' }}</strong>
                                </p>

                                <button
                                    type="button"
                                    @click="processResize"
                                    :disabled="resizerLoading"
                                    class="btn btn-primary text-xs py-2 px-5 mt-2 flex items-center gap-2"
                                >
                                    <ArrowPathIcon v-if="resizerLoading" class="w-4 h-4 animate-spin" />
                                    <span>{{ en ? 'Convert Now' : 'নির্দিষ্ট মাপে রূপান্তর করুন' }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Result Download -->
                        <div v-if="resizerResult" class="mt-6 pt-6 border-t border-gray-200 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <img
                                    :src="resizerResult.url"
                                    alt="Resized Result"
                                    class="border border-emerald-500 p-0.5 bg-white rounded shadow"
                                    :style="`width: ${resizerMode === 'photo' ? '80px' : '150px'}; height: ${resizerMode === 'photo' ? '80px' : '40px'};`"
                                />
                                <div class="text-xs">
                                    <span class="text-emerald-500 font-bold block">✓ {{ en ? 'Successfully Resized!' : 'সফলভাবে রিসাইজ সম্পন্ন!' }}</span>
                                    <span class="text-gray-500 font-mono">{{ resizerResult.width }}x{{ resizerResult.height }} px • {{ resizerResult.sizeKb }} KB</span>
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="downloadResized"
                                class="btn btn-primary text-xs py-2 px-4 flex items-center gap-1.5"
                            >
                                <ArrowDownTrayIcon class="w-4 h-4" />
                                <span>{{ en ? 'Download File' : 'ডাউনলোড করুন' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- 10. INTERACTIVE STANDARD CALCULATOR -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'calculator'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl max-w-xl mx-auto">
                    <h2 class="font-heading text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-200 dark:border-white/10 pb-4">
                        <CalculatorIcon class="w-6 h-6 text-teal-500 dark:text-teal-400" />
                        {{ en ? 'Interactive Standard Calculator' : 'ইন্টারঅ্যাক্টিভ সাধারণ ক্যালকুলেটর (Calculator)' }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-2">
                        {{ en ? 'Supports both mouse clicks and keyboard typing (Numbers, +, -, *, /, Enter, Backspace).' : 'স্ক্রিনের বোতাম চেপে অথবা সরাসরি কিবোর্ড দিয়ে দ্রুত যোগ, বিয়োগ, গুণ, ভাগ ও শতকরা হিসাব করুন।' }}
                    </p>

                    <!-- Calculator Screen -->
                    <div class="my-6 p-4 rounded-3xl bg-gray-900 text-white font-mono text-right shadow-inner border border-gray-800">
                        <div class="text-xs text-gray-400 min-h-[18px] truncate">
                            {{ calcEquation }}
                        </div>
                        <div class="text-3xl md:text-4xl font-extrabold tracking-wider overflow-x-auto whitespace-nowrap scrollbar-none py-1">
                            {{ calcDisplay }}
                        </div>
                    </div>

                    <!-- Calculator Keypad Grid -->
                    <div class="grid grid-cols-4 gap-2.5 text-sm sm:text-base font-bold font-mono">
                        <button type="button" @click="calcClear" class="p-3.5 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 hover:bg-rose-500/20 transition">C</button>
                        <button type="button" @click="calcBackspace" class="p-3.5 rounded-2xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 transition">⌫</button>
                        <button type="button" @click="calcOp('%')" class="p-3.5 rounded-2xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 transition">%</button>
                        <button type="button" @click="calcOp('÷')" class="p-3.5 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 transition">÷</button>

                        <button type="button" @click="calcInput('7')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">7</button>
                        <button type="button" @click="calcInput('8')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">8</button>
                        <button type="button" @click="calcInput('9')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">9</button>
                        <button type="button" @click="calcOp('×')" class="p-3.5 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 transition">×</button>

                        <button type="button" @click="calcInput('4')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">4</button>
                        <button type="button" @click="calcInput('5')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">5</button>
                        <button type="button" @click="calcInput('6')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">6</button>
                        <button type="button" @click="calcOp('-')" class="p-3.5 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 transition">-</button>

                        <button type="button" @click="calcInput('1')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">1</button>
                        <button type="button" @click="calcInput('2')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">2</button>
                        <button type="button" @click="calcInput('3')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">3</button>
                        <button type="button" @click="calcOp('+')" class="p-3.5 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 transition">+</button>

                        <button type="button" @click="calcInput('0')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition col-span-2">0</button>
                        <button type="button" @click="calcInput('.')" class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">.</button>
                        <button type="button" @click="calcEqual" class="p-3.5 rounded-2xl bg-[var(--primary)] text-white hover:opacity-90 shadow-lg shadow-[var(--primary)]/25 transition">=</button>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- 11. GPA & CGPA CALCULATOR -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'gpa'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
                    <h2 class="font-heading text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-200 dark:border-white/10 pb-4">
                        <AcademicCapIcon class="w-6 h-6 text-emerald-500 dark:text-emerald-400" />
                        {{ en ? 'SSC / HSC GPA & Honours CGPA Calculator' : 'এসএসসি / এইচএসসি GPA ও বিশ্ববিদ্যালয় CGPA ক্যালকুলেটর' }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-2">
                        {{ en ? 'Accurate GPA calculation with 4th subject bonus points for Board exams and Credit-weighted semester CGPA.' : '৪র্থ বিষয়ের অতিরিক্ত পয়েন্ট (২.০ এর উপরের পয়েন্ট) যোগ করে বোর্ড পরীক্ষার সঠিক জিপিএ এবং সেমিস্টার ক্রেডিট অনুযায়ী সিজিপিএ বের করুন।' }}
                    </p>

                    <!-- GPA Mode Toggle -->
                    <div class="flex items-center gap-3 my-6">
                        <button
                            type="button"
                            @click="gpaType = 'ssc_hsc'"
                            class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition border"
                            :class="gpaType === 'ssc_hsc' ? 'bg-emerald-600 text-white border-emerald-600 shadow-lg shadow-emerald-600/20' : 'bg-gray-100 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                        >
                            🎓 {{ en ? 'SSC / HSC Board GPA (with 4th Sub)' : 'এসএসসি / এইচএসসি বোর্ড GPA' }}
                        </button>
                        <button
                            type="button"
                            @click="gpaType = 'honours'"
                            class="px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition border"
                            :class="gpaType === 'honours' ? 'bg-emerald-600 text-white border-emerald-600 shadow-lg shadow-emerald-600/20' : 'bg-gray-100 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                        >
                            🏛️ {{ en ? 'Honours / University CGPA' : 'অনার্স / ভার্সিটি সিজিপিএ (CGPA)' }}
                        </button>
                    </div>

                    <!-- SSC / HSC GPA Layout -->
                    <div v-if="gpaType === 'ssc_hsc'" class="space-y-6">
                        <div class="p-6 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                            <div>
                                <span class="text-xs uppercase tracking-wider font-bold text-emerald-600 dark:text-emerald-400 block mb-1">
                                    {{ en ? 'Calculated Board GPA' : 'হিসাবকৃত চূড়ান্ত জিপিএ (GPA)' }}
                                </span>
                                <p class="text-xs text-gray-600 dark:text-gray-300">
                                    {{ sscCalculatedGpa === 5.00 ? (en ? 'Golden A+ Achieved!' : 'অভিনন্দন! জিপিএ ৫.০০ (A+)') : (en ? 'Result Calculated' : '৪র্থ বিষয়ের বোনাস পয়েন্ট অন্তর্ভুক্ত') }}
                                </p>
                            </div>
                            <div class="font-heading text-4xl sm:text-5xl font-extrabold text-emerald-600 dark:text-emerald-400">
                                {{ sscCalculatedGpa.toFixed(2) }}
                            </div>
                        </div>

                        <!-- Subjects Grade Table -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div
                                v-for="(sub, idx) in sscSubjects"
                                :key="idx"
                                class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 flex items-center justify-between"
                            >
                                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                    {{ en ? sub.name_en : sub.name_bn }}
                                </span>
                                <select
                                    v-model="sub.grade"
                                    class="bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 rounded-xl px-2.5 py-1 text-xs text-gray-900 dark:text-white font-mono font-bold"
                                >
                                    <option :value="5.0">A+ (5.00)</option>
                                    <option :value="4.0">A (4.00)</option>
                                    <option :value="3.5">A- (3.50)</option>
                                    <option :value="3.0">B (3.00)</option>
                                    <option :value="2.0">C (2.00)</option>
                                    <option :value="1.0">D (1.00)</option>
                                    <option :value="0.0">F (0.00)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 4th Subject Card -->
                        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-amber-700 dark:text-amber-400 block">
                                    {{ en ? '4th (Optional) Subject Grade:' : '৪র্থ (ঐচ্ছিক) বিষয়ের গ্রেড:' }}
                                </span>
                                <span class="text-[11px] text-gray-500">
                                    {{ en ? 'Points above 2.00 will be added as bonus' : '২.০০ এর উপরের পয়েন্ট যোগ হবে' }}
                                </span>
                            </div>
                            <select
                                v-model="fourthSubjectGrade"
                                class="bg-white dark:bg-[#161a26] border border-amber-500/30 rounded-xl px-2.5 py-1 text-xs text-amber-700 dark:text-amber-400 font-mono font-bold"
                            >
                                <option :value="5.0">A+ (5.00) → +3.00</option>
                                <option :value="4.0">A (4.00) → +2.00</option>
                                <option :value="3.5">A- (3.50) → +1.50</option>
                                <option :value="3.0">B (3.00) → +1.00</option>
                                <option :value="2.0">C (2.00) → 0.00</option>
                                <option :value="0.0">F (0.00)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Honours CGPA Layout -->
                    <div v-else class="space-y-6">
                        <div class="p-6 rounded-3xl bg-indigo-500/10 border border-indigo-500/20 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                            <div>
                                <span class="text-xs uppercase tracking-wider font-bold text-indigo-600 dark:text-indigo-400 block mb-1">
                                    {{ en ? 'Cumulative GPA (CGPA)' : 'চূড়ান্ত সিজিপিএ (CGPA)' }}
                                </span>
                                <p class="text-xs text-gray-600 dark:text-gray-300">
                                    {{ en ? 'Credit-hour weighted average across all completed semesters.' : 'সকল সেমিস্টারের ক্রেডিট আওয়ার অনুযায়ী ওয়েটেড গড় হিসাব।' }}
                                </p>
                            </div>
                            <div class="font-heading text-4xl sm:text-5xl font-extrabold text-indigo-600 dark:text-indigo-400">
                                {{ honoursCgpa.toFixed(2) }}
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="(sem, idx) in honoursSemesters"
                                :key="idx"
                                class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 flex items-center justify-between gap-4 flex-wrap"
                            >
                                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                    {{ en ? sem.name_en : sem.name_bn }}
                                </span>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1.5 text-xs">
                                        <span class="text-gray-500">{{ en ? 'GPA:' : 'জিপিএ:' }}</span>
                                        <input
                                            v-model.number="sem.gpa"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="4"
                                            class="w-16 px-2 py-1 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-center font-mono font-bold text-xs text-gray-900 dark:text-white"
                                        />
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs">
                                        <span class="text-gray-500">{{ en ? 'Credits:' : 'ক্রেডিট:' }}</span>
                                        <input
                                            v-model.number="sem.credits"
                                            type="number"
                                            min="1"
                                            max="40"
                                            class="w-16 px-2 py-1 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-center font-mono font-bold text-xs text-gray-900 dark:text-white"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ========================================== -->
            <!-- 12. WORD & CHARACTER COUNTER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'text'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
                    <h2 class="font-heading text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-200 dark:border-white/10 pb-4">
                        <DocumentTextIcon class="w-6 h-6 text-pink-500 dark:text-pink-400" />
                        {{ en ? 'Word Counter & Text Tools' : 'ওয়ার্ড কাউন্টার ও টেক্সট কনভার্টার (Word Counter)' }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-2">
                        {{ en ? 'Real-time word count, characters, sentences, and estimated reading time for essays and applications.' : 'রচনা, আবেদনপত্র বা সিভি লেখার সময় রিয়েল-টাইমে মোট শব্দ, বর্ণ, বাক্য ও পড়ার আনুমানিক সময় যাচাই করুন।' }}
                    </p>

                    <!-- Textarea -->
                    <div class="my-5">
                        <textarea
                            v-model="textInput"
                            rows="8"
                            :placeholder="en ? 'Type or paste your text here...' : 'এখানে আপনার টেক্সট পেস্ট করুন অথবা লিখুন...'"
                            class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)] leading-relaxed"
                        ></textarea>
                    </div>

                    <!-- Stats Counter Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-5 text-center">
                        <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 block font-bold">{{ en ? 'WORDS' : 'শব্দ (WORDS)' }}</span>
                            <strong class="font-mono text-xl text-gray-900 dark:text-white">{{ textStats.words }}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 block font-bold">{{ en ? 'CHARACTERS' : 'বর্ণ (CHARACTERS)' }}</span>
                            <strong class="font-mono text-xl text-amber-500 dark:text-amber-400">{{ textStats.charCount }}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 block font-bold">{{ en ? 'NO SPACES' : 'স্পেস ছাড়া বর্ণ' }}</span>
                            <strong class="font-mono text-xl text-emerald-500 dark:text-emerald-400">{{ textStats.charNoSpaces }}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 block font-bold">{{ en ? 'SENTENCES' : 'মোট বাক্য' }}</span>
                            <strong class="font-mono text-xl text-purple-500 dark:text-purple-400">{{ textStats.sentences }}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 col-span-2 sm:col-span-1">
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 block font-bold">{{ en ? 'READING TIME' : 'পড়ার সময়' }}</span>
                            <strong class="font-mono text-xl text-blue-500 dark:text-blue-400">{{ textStats.readingTimeMins }} {{ en ? 'min' : 'মিনিট' }}</strong>
                        </div>
                    </div>

                    <!-- Quick Transformation Buttons -->
                    <div class="flex items-center gap-2 flex-wrap pt-3 border-t border-gray-200 dark:border-white/10">
                        <button type="button" @click="convertText('upper')" class="btn btn-outline text-xs py-1.5 px-3">UPPERCASE</button>
                        <button type="button" @click="convertText('lower')" class="btn btn-outline text-xs py-1.5 px-3">lowercase</button>
                        <button type="button" @click="convertText('title')" class="btn btn-outline text-xs py-1.5 px-3">Title Case</button>
                        <button type="button" @click="convertText('clear')" class="btn btn-outline text-xs py-1.5 px-3 text-rose-500 border-rose-500/30">{{ en ? 'Clear' : 'মুছে ফেলুন' }}</button>
                    </div>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>
