<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
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
    BanknotesIcon,
    DocumentDuplicateIcon,
} from '@heroicons/vue/24/outline';

// Import Tool Components
import PomodoroTimer from './Components/PomodoroTimer.vue';
import StudyPlanner from './Components/StudyPlanner.vue';
import FormulaConverter from './Components/FormulaConverter.vue';
import CodeFormatter from './Components/CodeFormatter.vue';
import JsonViewer from './Components/JsonViewer.vue';
import MarkdownEditor from './Components/MarkdownEditor.vue';
import RegexTester from './Components/RegexTester.vue';
import ImageToPdf from './Components/ImageToPdf.vue';
import PdfToText from './Components/PdfToText.vue';
import CurrencyConverter from './Components/CurrencyConverter.vue';

const props = defineProps({
    recentJobs: Array,
    seo: Object,
});

const { t, locale } = useI18n();
const en = computed(() => locale.value === 'en');

// Active view: 'catalog' | toolId
const activeTab = ref('catalog');
const categoryFilter = ref('all'); // all | study | documents | everyday | tech
const searchQuery = ref('');
const copied = ref(false);
const categoryLabels = computed(() => ({
    study: en.value ? 'Study & exams' : 'পড়াশোনা ও পরীক্ষা',
    documents: en.value ? 'Documents & images' : 'নথি ও ছবি',
    everyday: en.value ? 'Everyday calculators' : 'দৈনন্দিন ক্যালকুলেটর',
    tech: en.value ? 'Coding & notes' : 'কোডিং ও নোট',
}));

// ==========================================
// Student tools first, then everyday document and technical utilities.
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
        descBn: 'পড়ার সময়সূচি তৈরি করুন এবং ১-ক্লিকে চমৎকার A4 শিটে প্রিন্ট বা PDF হিসেবে সংরক্ষণ করুন।',
        descEn: 'Build personal study routines with target hours and 1-click clean A4 PDF export.',
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

    // --- PDF & Document Tools ---
    {
        id: 'image_to_pdf',
        category: 'documents',
        titleBn: 'ছবি থেকে পিডিএফ কনভার্টার',
        titleEn: 'Image to PDF Converter',
        descBn: 'একাধিক ছবি (JPG, PNG, WebP) যুক্ত করে ব্রাউজারে ১-ক্লিকে চমৎকার A4 PDF তৈরি ও ডাউনলোড করুন।',
        descEn: 'Combine multiple JPG, PNG, or WebP images into a single clean PDF in browser.',
        icon: PhotoIcon,
        badgeBn: 'নতুন পিডিএফ',
        badgeEn: 'New PDF',
        badgeColor: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
        color: 'text-indigo-500',
    },
    {
        id: 'pdf_to_text',
        category: 'documents',
        titleBn: 'পিডিএফ থেকে টেক্সট এক্সট্রাক্টর',
        titleEn: 'PDF to Text Extractor',
        descBn: 'যেকোনো পিডিএফ ফাইল থেকে সব লেখা আলাদা করুন, ওয়ার্ড কাউন্ট দেখুন বা .TXT ফাইল ডাউনলোড করুন।',
        descEn: 'Extract readable text from PDF documents, copy, or download as TXT file in browser.',
        icon: DocumentDuplicateIcon,
        badgeBn: 'নতুন এক্সট্রাক্ট',
        badgeEn: 'New Extract',
        badgeColor: 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
        color: 'text-teal-500',
    },
    {
        id: 'resizer',
        category: 'documents',
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
        descBn: 'স্প্লিট এডিটরে লাইভ মার্কডাউন নোট লিখুন এবং পরিচ্ছন্ন মার্জিত A4 পেপারে PDF সেভ করুন।',
        descEn: 'Write notes in markdown with instant preview and export directly to clean A4 PDF.',
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
        descBn: 'রিয়েল-টাইমে মোট শব্দ, বর্ণ, পড়ার সময় গণনা এবং UPPERCASE/lowercase কনভার্সন।',
        descEn: 'Real-time word, character, and sentence count with instant case transformations.',
        icon: DocumentTextIcon,
        badgeBn: 'ইউটিলিটি',
        badgeEn: 'Utility',
        badgeColor: 'bg-pink-500/10 text-pink-600 dark:text-pink-400 border-pink-500/20',
        color: 'text-pink-500',
    },

    // --- Currency & Finance / Utilities ---
    {
        id: 'currency',
        category: 'everyday',
        titleBn: 'লাইভ কারেন্সি ও মুদ্রা কনভার্টার',
        titleEn: 'Live Currency Converter',
        descBn: 'বাংলাদেশী টাকা (BDT), ইউএস ডলার ($), সৌদি রিয়াল (SAR), দিরহাম, ইউরো, পাউন্ডের লাইভ রেট কনভার্টার।',
        descEn: 'Real-time exchange rate converter for BDT, USD, SAR, AED, EUR, GBP, MYR, INR.',
        icon: BanknotesIcon,
        badgeBn: 'নতুন এফএক্স',
        badgeEn: 'New FX',
        badgeColor: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        color: 'text-emerald-500',
    },
    {
        id: 'age',
        category: 'everyday',
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
        id: 'calculator',
        category: 'everyday',
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

const toolPriority = ['gpa', 'routine', 'pomodoro', 'formulas', 'image_to_pdf', 'resizer', 'pdf_to_text', 'age', 'calculator', 'currency', 'text', 'markdown', 'code_formatter', 'json_viewer', 'regex'];

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
    }).sort((a, b) => toolPriority.indexOf(a.id) - toolPriority.indexOf(b.id));
});

async function openTool(toolId) {
    activeTab.value = toolId;
    await nextTick();
    document.querySelector('#tools-workspace')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
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

// ==========================================
// TOOL: RESIZER
// ==========================================
const resizerMode = ref('photo');
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
const gpaType = ref('ssc_hsc');
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
    <SeoHead v-if="seo" :seo="seo" />
    <Head v-else :title="en ? 'Student & Tech Online Tools Hub | 100% Free Utilities - Sikhun' : 'স্টুডেন্ট ও টেক অনলাইন টুলস হাব | ১০০% ফ্রি ইউটিলিটি - শিখুন'" />

    <PublicLayout>
        <!-- Hero Header -->
        <header class="tools-hero border-b border-[var(--border)] py-9 sm:py-12">
            <div class="mx-auto max-w-6xl px-4 text-left md:px-6">
                <span class="tools-eyebrow">{{ en ? 'A practical desk for students' : 'শিক্ষার্থীদের দরকারি সহায়তা' }}</span>
                <h1 class="mt-2 font-heading text-3xl font-bold tracking-tight text-[var(--text)] md:text-5xl">
                    {{ en ? 'Study tools, in the right order.' : 'পড়াশোনার দরকারি টুলস, এক জায়গায়।' }}
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)] sm:text-base">
                    {{ en ? 'Start with grades, a study plan, or a focus session. Find document and everyday utilities below.' : 'GPA হিসাব, পড়ার পরিকল্পনা বা মনোযোগী একটি সেশন দিয়ে শুরু করুন। এরপর নথি ও দৈনন্দিন কাজের টুলস খুঁজে নিন।' }}
                </p>

                <!-- Search Input Bar -->
                <div class="relative mt-6 max-w-2xl">
                    <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-4 top-3" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="en ? 'What do you need to do? Search tools…' : 'কোন কাজটি করতে চান? টুল খুঁজুন…'"
                        aria-label="Search student tools"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] py-3 pl-11 pr-4 text-sm text-[var(--text)] shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                    />
                </div>

                <!-- Tool Category Switcher Tabs -->
                <div class="mt-5 flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="categoryFilter = 'all'; activeTab = 'catalog'"
                        class="px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 border"
                        :class="categoryFilter === 'all' && activeTab === 'catalog' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-sm' : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--surface2)]'"
                    >
                        {{ en ? `All tools (${toolsList.length})` : `সব টুলস (${toolsList.length})` }}
                    </button>

                    <button
                        type="button"
                        @click="categoryFilter = 'study'; activeTab = 'catalog'"
                        class="px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 border"
                        :class="categoryFilter === 'study' && activeTab === 'catalog' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-sm' : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--surface2)]'"
                    >
                        {{ en ? 'Study & exams' : 'পড়াশোনা ও পরীক্ষা' }}
                    </button>

                    <button
                        type="button"
                        @click="categoryFilter = 'documents'; activeTab = 'catalog'"
                        class="px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 border"
                        :class="categoryFilter === 'documents' && activeTab === 'catalog' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-sm' : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--surface2)]'"
                    >
                        {{ en ? 'Documents & images' : 'নথি ও ছবি' }}
                    </button>

                    <button
                        type="button"
                        @click="categoryFilter = 'tech'; activeTab = 'catalog'"
                        class="px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 border"
                        :class="categoryFilter === 'tech' && activeTab === 'catalog' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-sm' : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--surface2)]'"
                    >
                        {{ en ? 'Coding & notes' : 'কোডিং ও নোট' }}
                    </button>

                    <button
                        type="button"
                        @click="categoryFilter = 'everyday'; activeTab = 'catalog'"
                        class="px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 border"
                        :class="categoryFilter === 'everyday' && activeTab === 'catalog' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-sm' : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--surface2)]'"
                    >
                        {{ en ? 'Everyday calculators' : 'দৈনন্দিন ক্যালকুলেটর' }}
                    </button>
                </div>
            </div>
        </header>

        <main id="tools-workspace" class="max-w-6xl mx-auto scroll-mt-24 px-4 py-8 md:px-6 md:py-10">
            <section v-if="activeTab === 'catalog'" class="tools-priority mb-10">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
                    <div>
                        <p class="tools-eyebrow">{{ en ? 'Start here' : 'এখান থেকে শুরু করুন' }}</p>
                        <h2 class="mt-1 text-xl font-semibold text-[var(--text)] sm:text-2xl">{{ en ? 'Your next useful step' : 'এখন আপনার যে কাজটি দরকার' }}</h2>
                    </div>
                    <Link href="/question-bank" class="tools-link">{{ en ? 'Practice questions' : 'প্রশ্ন অনুশীলন' }} →</Link>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <Link href="/results" class="tools-shortcut"><span class="tools-shortcut-index">01</span><strong>{{ en ? 'Check exam results' : 'পরীক্ষার ফল দেখুন' }}</strong><span>{{ en ? 'Find a published result' : 'প্রকাশিত ফলাফল খুঁজুন' }} →</span></Link>
                    <button type="button" class="tools-shortcut text-left" @click="openTool('gpa')"><span class="tools-shortcut-index">02</span><strong>{{ en ? 'Calculate GPA' : 'GPA হিসাব করুন' }}</strong><span>{{ en ? 'SSC, HSC & honours' : 'SSC, HSC ও অনার্স' }} →</span></button>
                    <button type="button" class="tools-shortcut text-left" @click="openTool('routine')"><span class="tools-shortcut-index">03</span><strong>{{ en ? 'Plan a study routine' : 'পড়ার রুটিন সাজান' }}</strong><span>{{ en ? 'Make a plan for your week' : 'সপ্তাহের পরিকল্পনা করুন' }} →</span></button>
                    <button type="button" class="tools-shortcut text-left" @click="openTool('pomodoro')"><span class="tools-shortcut-index">04</span><strong>{{ en ? 'Start a focus session' : 'মনোযোগী সেশন শুরু করুন' }}</strong><span>{{ en ? 'Study with a simple timer' : 'সহজ টাইমারে পড়ুন' }} →</span></button>
                </div>
            </section>

            <!-- Breadcrumb Navigation when a tool is active -->
            <div v-if="activeTab !== 'catalog'" class="mb-6 flex items-center justify-between gap-3">
                <button
                    type="button"
                    @click="activeTab = 'catalog'"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 transition"
                >
                    <ArrowLeftIcon class="w-4 h-4" />
                    <span>{{ en ? 'Back to All Tools Catalog' : '← সকল টুলসের ক্যাটালগে ফিরে যান' }}</span>
                </button>

                <!-- Quick Switcher Dropdown -->
                <div class="hidden sm:flex items-center gap-2 text-xs">
                    <span class="text-gray-500">{{ en ? 'Switch Tool:' : 'দ্রুত পরিবর্তন:' }}</span>
                    <select
                        v-model="activeTab"
                        class="bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-1.5 text-xs text-gray-900 dark:text-white font-semibold"
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
                        {{ en ? `Showing ${filteredTools.length} free tools` : `${filteredTools.length}টি ১০০% ফ্রি ইউটিলিটি টুলস` }}
                    </span>
                    <span v-if="categoryFilter !== 'all'" class="font-semibold text-[var(--primary)] uppercase tracking-wider">
                        {{ categoryLabels[categoryFilter] }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div
                        v-for="tool in filteredTools"
                        :key="tool.id"
                        @click="openTool(tool.id)"
                        class="tool-card card p-5 sm:p-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] hover:border-[var(--primary)]/40 transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 cursor-pointer flex flex-col justify-between group text-left"
                        role="button"
                        tabindex="0"
                        @keydown.enter.prevent="openTool(tool.id)"
                        @keydown.space.prevent="openTool(tool.id)"
                    >
                        <div>
                            <!-- Top Badge Strip -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="tool-badge px-2.5 py-0.5 rounded-full text-[10px] font-bold border" :class="tool.badgeColor">
                                    {{ en ? tool.badgeEn : tool.badgeBn }}
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">
                                    {{ en ? '100% Free' : '১০০% ফ্রি' }}
                                </span>
                            </div>

                            <!-- Icon & Title -->
                            <div class="flex items-start gap-3.5 mb-2">
                                <div class="p-2.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5 shrink-0 group-hover:scale-110 transition">
                                    <component :is="tool.icon" class="tool-icon w-6 h-6" :class="tool.color" />
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-base text-gray-900 dark:text-white group-hover:text-[var(--primary)] transition">
                                        {{ en ? tool.titleEn : tool.titleBn }}
                                    </h3>
                                    <span class="text-[10px] text-[var(--text-muted)] uppercase tracking-widest font-semibold">
                                        {{ categoryLabels[tool.category] || tool.category }}
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
            <!-- 4. IMAGE TO PDF CONVERTER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'image_to_pdf'">
                <ImageToPdf :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 5. PDF TO TEXT EXTRACTOR -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'pdf_to_text'">
                <PdfToText :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 6. LIVE CURRENCY CONVERTER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'currency'">
                <CurrencyConverter :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 7. CODE FORMATTER & BEAUTIFIER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'code_formatter'">
                <CodeFormatter :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 8. JSON VALIDATOR & VIEWER -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'json_viewer'">
                <JsonViewer :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 9. MARKDOWN NOTES TO PDF -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'markdown'">
                <MarkdownEditor :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 10. REGEX TESTER & CHEATSHEET -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'regex'">
                <RegexTester :en="en" />
            </section>

            <!-- ========================================== -->
            <!-- 11. GENERAL AGE CALCULATOR -->
            <!-- ========================================== -->
            <section v-else-if="activeTab === 'age'" class="space-y-6">
                <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
                    <h2 class="font-heading text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-200 dark:border-white/10 pb-4">
                        <CalendarDaysIcon class="w-6 h-6 text-amber-500 dark:text-amber-400" />
                        {{ en ? 'General & Job Age Calculator' : 'সাধারণ ও চাকরির বয়স ক্যালকুলেটর (Age Calculator)' }}
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
            <!-- 12. PHOTO & SIGNATURE RESIZER -->
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
            <!-- 13. INTERACTIVE STANDARD CALCULATOR -->
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
            <!-- 14. GPA & CGPA CALCULATOR -->
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
            <!-- 15. WORD & CHARACTER COUNTER -->
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

<style>
.tools-hero { background: linear-gradient(180deg, var(--surface2), var(--bg)); }
.tools-eyebrow { color: var(--primary); font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
.tools-link { color: var(--primary); font-size: .82rem; font-weight: 650; }
.tools-shortcut { display: flex; min-height: 142px; flex-direction: column; align-items: flex-start; gap: .45rem; border: 1px solid var(--border); border-radius: 1rem; background: var(--surface); padding: 1rem; color: var(--text); transition: transform .18s ease, border-color .18s ease, background .18s ease; }
.tools-shortcut:hover { transform: translateY(-2px); border-color: color-mix(in srgb, var(--primary) 38%, var(--border)); background: color-mix(in srgb, var(--primary) 5%, var(--surface)); }
.tools-shortcut:focus-visible, .tool-card:focus-visible { outline: 2px solid var(--primary); outline-offset: 3px; }
.tools-shortcut-index { color: var(--primary); font-size: .7rem; font-weight: 700; letter-spacing: .12em; }
.tools-shortcut strong { font-size: .95rem; line-height: 1.4; }
.tools-shortcut > span:last-child { margin-top: auto; color: var(--text-muted); font-size: .76rem; }
.tool-card .tool-badge { border-color: var(--border) !important; background: var(--surface2) !important; color: var(--text-muted) !important; }
.tool-card .tool-icon { color: var(--primary) !important; }
@media (max-width: 640px) { .tools-shortcut { min-height: 122px; } }
</style>
