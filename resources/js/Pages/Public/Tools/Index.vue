<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
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
    ClockIcon,
    CodeBracketIcon,
    CommandLineIcon,
    ArrowLeftIcon,
    MagnifyingGlassIcon,
    BanknotesIcon,
    DocumentDuplicateIcon,
    ArrowDownTrayIcon,
    Squares2X2Icon,
} from '@heroicons/vue/24/outline';

// 8 Smart Primary Student Tools
import FormulaConverter from './Components/FormulaConverter.vue';
import SmartGpaCalculator from './Components/SmartGpaCalculator.vue';
import StudyPlanner from './Components/StudyPlanner.vue';
import PhotoSignatureStudio from './Components/PhotoSignatureStudio.vue';
import GeneralAgeCalculator from './Components/GeneralAgeCalculator.vue';
import NegativeMarkingCalculator from './Components/NegativeMarkingCalculator.vue';
import ImageToPdf from './Components/ImageToPdf.vue';
import PomodoroTimer from './Components/PomodoroTimer.vue';

// Other Utilities Category Tools
import PdfToText from './Components/PdfToText.vue';
import MarkdownEditor from './Components/MarkdownEditor.vue';
import CurrencyConverter from './Components/CurrencyConverter.vue';
import CodeFormatter from './Components/CodeFormatter.vue';
import JsonViewer from './Components/JsonViewer.vue';
import RegexTester from './Components/RegexTester.vue';

const props = defineProps({
    initialTool: { type: String, default: 'catalog' },
    recentJobs: Array,
    seo: Object,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');

const activeTab = ref(props.initialTool || 'catalog');
// Default to 'student' so visitors see the 8 smart student tools first!
const categoryFilter = ref('student');
const searchQuery = ref('');

const slugByToolId = {
    formulas: 'formula-sheet',
    gpa: 'gpa-calculator',
    routine: 'study-routine',
    resizer: 'photo-resizer',
    age: 'age-calculator',
    negative_marking: 'negative-marking',
    image_to_pdf: 'image-to-pdf',
    pomodoro: 'pomodoro-timer',
};

const categoryLabels = computed(() => ({
    student: en.value ? 'Smart Student Tools' : 'স্মার্ট স্টুডেন্ট টুলস',
    other: en.value ? 'Other Utilities' : 'অন্যান্য ইউটিলিটি',
}));

const toolsList = [
    // ================= 8 PRIMARY STUDENT-FRIENDLY TOOLS =================
    {
        id: 'formulas',
        category: 'student',
        hasPdf: true,
        titleBn: 'বিজ্ঞান ও গণিত সূত্র ব্যাংক এবং একক কনভার্টার',
        titleEn: 'Science & Math Formula Bank + Unit Converter',
        descBn: 'SSC, HSC ও চাকরি পরীক্ষার অধ্যায়ভিত্তিক সূত্রাবলি, লাইভ সলভার, একক কনভার্টার এবং ১-ক্লিকে A4 সূত্র শিট PDF ডাউনলোড।',
        descEn: 'Chapter-wise Physics, Math & Chemistry formulas, live solver, unit converter, and 1-click A4 Cheat Sheet PDF.',
        icon: CalculatorIcon,
        badgeBn: 'সূত্র ব্যাংক + PDF',
        badgeEn: 'Formulas + PDF',
        color: 'text-cyan-500',
    },
    {
        id: 'gpa',
        category: 'student',
        hasPdf: true,
        titleBn: 'জিপিএ ক্যালকুলেটর ও ভার্সিটি যোগ্যতা যাচাই',
        titleEn: 'GPA / CGPA & University Eligibility Checker',
        descBn: 'বিজ্ঞান, মানবিক ও ব্যবসায় শিক্ষা বিভাগের ৪র্থ বিষয়সহ GPA, পাবলিক ভার্সিটি যোগ্যতা যাচাই, অনার্স CGPA ও মার্কশিট PDF।',
        descEn: 'SSC/HSC group GPA with 4th subject, public university eligibility check, Honours CGPA & PDF marksheet.',
        icon: AcademicCapIcon,
        badgeBn: 'মার্কশিট PDF',
        badgeEn: 'Marksheet PDF',
        color: 'text-emerald-500',
    },
    {
        id: 'routine',
        category: 'student',
        hasPdf: true,
        titleBn: 'দৈনিক পড়ার রুটিন ও প্ল্যানার মেকার',
        titleEn: 'Smart Daily Study Routine & Planner',
        descBn: 'বোর্ড, ভর্তি বা চাকরির রেডি টেমপ্লেট দিয়ে পড়ার সময়সূচি সাজান এবং দেয়ালে টানানোর উপযোগী A4 রুটিন PDF ডাউনলোড করুন।',
        descEn: 'Build daily study routines from smart presets and download a clean A4 printable PDF.',
        icon: CalendarDaysIcon,
        badgeBn: 'রুটিন PDF',
        badgeEn: 'Routine PDF',
        color: 'text-indigo-500',
    },
    {
        id: 'resizer',
        category: 'student',
        hasPdf: false,
        titleBn: 'ছবি ও স্বাক্ষর স্টুডিও (Photo & Signature Resizer Pro)',
        titleEn: 'Photo & Signature Resizer Pro',
        descBn: 'যেকোনো অনলাইন ফরম বা ডকুমেন্টের জন্য ছবি (৩০০×৩০০), স্বাক্ষর (৩০০×৮০), পাসপোর্ট বা কাস্টম মাপে রিসাইজ ও ব্যাকগ্রাউন্ড পরিবর্তন।',
        descEn: 'Resize photos (300×300), signatures (300×80), passport or custom dimensions with background color & KB control.',
        icon: PhotoIcon,
        badgeBn: 'ইমেজ স্টুডিও',
        badgeEn: 'Image Studio',
        color: 'text-blue-500',
    },
    {
        id: 'age',
        category: 'student',
        hasPdf: true,
        titleBn: 'বয়স ক্যালকুলেটর (Age Calculator)',
        titleEn: 'Age Calculator',
        descBn: 'জন্মতারিখ থেকে নির্ভুল বছর, মাস, দিন, মোট সপ্তাহ, জন্মবার ও পরবর্তী জন্মদিনের হিসাব এবং ১-ক্লিকে বয়স রিপোর্ট PDF।',
        descEn: 'Exact age in years, months, days, total weeks, born weekday, next birthday countdown & PDF summary.',
        icon: CalendarDaysIcon,
        badgeBn: 'রিপোর্ট PDF',
        badgeEn: 'Summary PDF',
        color: 'text-amber-500',
    },
    {
        id: 'negative_marking',
        category: 'student',
        hasPdf: true,
        titleBn: 'ওএমআর ও নেগেটিভ মার্কিং স্কোর ক্যালকুলেটর',
        titleEn: 'OMR & Negative Marking Calculator',
        descBn: 'MCQ পরীক্ষায় ভুল উত্তরের নেগেটিভ নম্বর (-০.২৫ বা -০.৫০) বাদ দিয়ে আসল প্রাপ্ত নম্বর, অ্যাকুরেসি ও কাট-মার্ক যাচাই।',
        descEn: 'Calculate net MCQ score after -0.25 or -0.50 negative marking, accuracy %, cut-mark status & PDF report.',
        icon: SparklesIcon,
        badgeBn: 'এক্সাম স্কোর PDF',
        badgeEn: 'Exam Score PDF',
        color: 'text-rose-500',
    },
    {
        id: 'image_to_pdf',
        category: 'student',
        hasPdf: true,
        titleBn: 'নোটের ছবি থেকে পিডিএফ মেকার',
        titleEn: 'Notes & Image to PDF Maker',
        descBn: 'খাতার নোট বা একাধিক ছবি (JPG, PNG, WebP) যুক্ত করে ব্রাউজারে ১-ক্লিকে পরিচ্ছন্ন A4 স্টাডি নোট PDF তৈরি ও ডাউনলোড করুন।',
        descEn: 'Combine handwritten note photos or images into a clean A4 PDF directly in your browser.',
        icon: PhotoIcon,
        badgeBn: 'ডাইরেক্ট PDF',
        badgeEn: 'Direct PDF',
        color: 'text-purple-500',
    },
    {
        id: 'pomodoro',
        category: 'student',
        hasPdf: false,
        titleBn: 'পোমোডোরো ফোকাস টাইমার ও স্টাডি সাউন্ড',
        titleEn: 'Pomodoro Focus Timer & Study Sound',
        descBn: '২৫ মিনিট মনোযোগী পড়াশোনা ও ৫ মিনিট ব্রেক সাইকেল, সাথে প্রশান্তিদায়ক বৃষ্টির শব্দ ও ফোকাস সেশন ট্র্যাকার।',
        descEn: '25-minute deep focus cycles with soothing rain ambience and completed session tracking.',
        icon: ClockIcon,
        badgeBn: 'ডিপ ফোকাস',
        badgeEn: 'Deep Focus',
        color: 'text-rose-500',
    },

    // ================= OTHER UTILITIES CATEGORY =================
    {
        id: 'markdown',
        category: 'other',
        hasPdf: true,
        titleBn: 'মার্কডাউন নোট ও PDF এক্সপোর্টার',
        titleEn: 'Markdown Notes to PDF',
        descBn: 'লাইভ মার্কডাউন এডিটরে ডিজিটাল নোট লিখুন এবং A4 পেপারে প্রিন্ট বা PDF সংরক্ষণ করুন।',
        descEn: 'Write formatted notes in markdown and export to clean A4 PDF.',
        icon: DocumentTextIcon,
        badgeBn: 'নোটবুক',
        badgeEn: 'Notebook',
        color: 'text-purple-500',
    },
    {
        id: 'pdf_to_text',
        category: 'other',
        hasPdf: false,
        titleBn: 'পিডিএফ থেকে টেক্সট এক্সট্রাক্টর',
        titleEn: 'PDF to Text Extractor',
        descBn: 'যেকোনো পিডিএফ ফাইল থেকে সব লেখা আলাদা করুন, কপি করুন বা .TXT ফাইল ডাউনলোড করুন।',
        descEn: 'Extract readable text from PDF documents right inside your browser.',
        icon: DocumentDuplicateIcon,
        badgeBn: 'ইউটিলিটি',
        badgeEn: 'Utility',
        color: 'text-teal-500',
    },
    {
        id: 'text',
        category: 'other',
        hasPdf: false,
        titleBn: 'ওয়ার্ড কাউন্টার ও টেক্সট কনভার্টার',
        titleEn: 'Word Counter & Text Case Tools',
        descBn: 'রচনা বা আবেদনের মোট শব্দ, বর্ণ, বাক্য ও পড়ার সময় গণনা এবং কেস রূপান্তর।',
        descEn: 'Count words, characters, sentences, reading time, and convert text case.',
        icon: DocumentTextIcon,
        badgeBn: 'টেক্সট টুল',
        badgeEn: 'Text Tool',
        color: 'text-pink-500',
    },
    {
        id: 'currency',
        category: 'other',
        hasPdf: false,
        titleBn: 'লাইভ কারেন্সি ও মুদ্রা কনভার্টার',
        titleEn: 'Live Currency Converter',
        descBn: 'বাংলাদেশি টাকা (BDT), ডলার, রিয়াল, দিরহাম, ইউরো ও পাউন্ডের লাইভ এক্সচেঞ্জ রেট।',
        descEn: 'Exchange rate converter for BDT, USD, SAR, AED, EUR, GBP, and INR.',
        icon: BanknotesIcon,
        badgeBn: 'কারেন্সি',
        badgeEn: 'Currency',
        color: 'text-emerald-500',
    },
    {
        id: 'code_formatter',
        category: 'other',
        hasPdf: false,
        titleBn: 'কোড ফরম্যাটার ও বিউটিফায়ার',
        titleEn: 'Code Formatter & Beautifier',
        descBn: 'HTML, CSS, JavaScript, JSON এবং SQL কোড ১-ক্লিকে সুন্দর ইন্ডেন্টেশনে সাজিয়ে নিন।',
        descEn: 'Format and beautify HTML, CSS, JavaScript, JSON, and SQL code.',
        icon: CodeBracketIcon,
        badgeBn: 'কোডিং',
        badgeEn: 'Coding',
        color: 'text-emerald-500',
    },
    {
        id: 'json_viewer',
        category: 'other',
        hasPdf: false,
        titleBn: 'JSON ভ্যালিডেটর ও ভিউয়ার',
        titleEn: 'JSON Validator & Viewer',
        descBn: 'জেসন ডাটা যাচাই, সিনট্যাক্স এরর ডিটেকশন এবং প্রিটি ফরম্যাটিং।',
        descEn: 'Validate JSON payloads and prettify or minify instantly.',
        icon: Squares2X2Icon,
        badgeBn: 'ডেভেলপার',
        badgeEn: 'Developer',
        color: 'text-amber-500',
    },
    {
        id: 'regex',
        category: 'other',
        hasPdf: false,
        titleBn: 'রেজেক্স টেস্টার ও চিটশিট',
        titleEn: 'Regex Tester & Cheatsheet',
        descBn: 'রেগুলার এক্সপ্রেশন লাইভ টেস্ট ও প্যাটার্ন ভ্যালিডেশন।',
        descEn: 'Test regular expressions with live match highlighting.',
        icon: CommandLineIcon,
        badgeBn: 'কোডিং',
        badgeEn: 'Coding',
        color: 'text-orange-500',
    },
];

const studentToolsCount = toolsList.filter((t) => t.category === 'student').length;
const otherToolsCount = toolsList.filter((t) => t.category === 'other').length;

const filteredTools = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    return toolsList.filter((t) => {
        // When user types a search query, search across ALL categories seamlessly
        const matchesCat = q ? true : (categoryFilter.value === 'all' || t.category === categoryFilter.value);
        if (!q) return matchesCat;
        return (
            t.titleBn.toLowerCase().includes(q) ||
            t.titleEn.toLowerCase().includes(q) ||
            t.descBn.toLowerCase().includes(q) ||
            t.descEn.toLowerCase().includes(q)
        );
    });
});

async function openTool(toolId) {
    activeTab.value = toolId;
    const slug = slugByToolId[toolId];
    if (typeof window !== 'undefined') {
        const nextUrl = slug ? `/tools/${slug}` : `/tools?tab=${toolId}`;
        window.history.pushState({}, '', nextUrl);
    }
    await nextTick();
    document.querySelector('#tools-workspace')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function backToCatalog() {
    activeTab.value = 'catalog';
    if (typeof window !== 'undefined') {
        window.history.pushState({}, '', '/tools');
    }
}

onMounted(() => {
    if (props.initialTool && props.initialTool !== 'catalog') {
        activeTab.value = props.initialTool;
        return;
    }
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam && toolsList.some((t) => t.id === tabParam)) {
        activeTab.value = tabParam;
    }
});

// Word Counter inline tool state
const textInput = ref('');
const textStats = computed(() => {
    const raw = textInput.value;
    const charCount = raw.length;
    const charNoSpaces = raw.replace(/\s/g, '').length;
    const words = raw.trim() ? raw.trim().split(/\s+/).length : 0;
    const sentences = raw.trim() ? (raw.match(/[\.\?!।]|\n+/g) || []).length : 0;
    return {
        charCount,
        charNoSpaces,
        words,
        sentences: Math.max(sentences, words > 0 ? 1 : 0),
        readingTimeMins: Math.ceil(words / 180),
    };
});

function convertText(mode) {
    if (mode === 'upper') textInput.value = textInput.value.toUpperCase();
    else if (mode === 'lower') textInput.value = textInput.value.toLowerCase();
    else if (mode === 'title') textInput.value = textInput.value.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.slice(1).toLowerCase());
    else if (mode === 'clear') textInput.value = '';
}
</script>

<template>
    <SeoHead v-if="seo" :seo="seo" />
    <Head v-else :title="en ? 'Smart Student Tools Hub — Formula PDF, GPA & Converters | Sikhun' : 'স্মার্ট স্টুডেন্ট টুলস হাব — সূত্র ব্যাংক PDF, GPA ও রুটিন | শিখুন'" />

    <PublicLayout>
        <!-- Hero Header -->
        <header class="tools-hero border-b border-[var(--border)] py-9 sm:py-12">
            <div class="mx-auto max-w-6xl px-4 text-left md:px-6">
                <span class="tools-eyebrow">{{ en ? 'SMART ACADEMIC DESK • PRINT & PDF READY' : 'শিক্ষার্থীদের স্মার্ট ডেস্ক • ১-ক্লিক PDF ডাউনলোড সুবিধাসহ' }}</span>
                <h1 class="mt-2 font-heading text-3xl font-extrabold tracking-tight text-[var(--text)] md:text-5xl">
                    {{ en ? 'Smart Student Tools & PDF Makers' : 'পড়াশোনা ও পরীক্ষার স্মার্ট টুলস হাব' }}
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)] sm:text-base">
                    {{ en
                        ? 'Download custom Science & Math formula cheat-sheet PDFs, check GPA & admission eligibility, resize photos & signatures, or plan your daily routine.'
                        : 'বিজ্ঞান ও গণিতের অধ্যায়ভিত্তিক সূত্রের PDF চিটশিট তৈরি করুন, GPA ও ভার্সিটি যোগ্যতা যাচাই করুন, পড়ার রুটিন সাজান এবং ছবি-স্বাক্ষর রিসাইজ করুন—সবই ১০০% ফ্রি।'
                    }}
                </p>

                <!-- Search & Category Switcher -->
                <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all border"
                            :class="categoryFilter === 'student' && activeTab === 'catalog'
                                ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-sm'
                                : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--surface2)]'"
                            @click="categoryFilter = 'student'; backToCatalog()"
                        >
                            🎓 {{ en ? `Student Tools (${studentToolsCount})` : `স্টুডেন্ট টুলস (${studentToolsCount})` }}
                        </button>

                        <button
                            type="button"
                            class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all border"
                            :class="categoryFilter === 'other' && activeTab === 'catalog'
                                ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-sm'
                                : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--surface2)]'"
                            @click="categoryFilter = 'other'; backToCatalog()"
                        >
                            🛠️ {{ en ? `Other Utilities (${otherToolsCount})` : `অন্যান্য ইউটিলিটি (${otherToolsCount})` }}
                        </button>

                        <button
                            type="button"
                            class="px-3.5 py-2.5 rounded-2xl text-xs sm:text-sm font-semibold transition-all border"
                            :class="categoryFilter === 'all' && activeTab === 'catalog'
                                ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-sm'
                                : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--surface2)]'"
                            @click="categoryFilter = 'all'; backToCatalog()"
                        >
                            {{ en ? `All (${toolsList.length})` : `সবগুলো (${toolsList.length})` }}
                        </button>
                    </div>

                    <div class="relative w-full md:w-72">
                        <MagnifyingGlassIcon class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            :placeholder="en ? 'Search any tool...' : 'যেকোনো টুল খুঁজুন...'"
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] py-2.5 pl-10 pr-4 text-xs sm:text-sm text-[var(--text)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                            @focus="activeTab = 'catalog'"
                        />
                    </div>
                </div>
            </div>
        </header>

        <main id="tools-workspace" class="max-w-6xl mx-auto scroll-mt-24 px-4 py-8 md:px-6 md:py-10">
            <!-- Featured Quick Launch Row (Student View) -->
            <section v-if="activeTab === 'catalog' && categoryFilter === 'student' && !searchQuery" class="mb-10">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
                    <div>
                        <p class="tools-eyebrow">{{ en ? 'MOST POPULAR' : 'সবচেয়ে জনপ্রিয়' }}</p>
                        <h2 class="mt-1 text-xl font-bold text-[var(--text)] sm:text-2xl">
                            {{ en ? 'Generate & Download Instant Study Sheets' : 'এক ক্লিকেই প্রয়োজনীয় স্টাডি শিট ও হিসাব' }}
                        </h2>
                    </div>
                    <Link href="/questions" class="tools-link">{{ en ? '38,000+ Free Past MCQs' : '৩৮,০০০+ ফ্রি MCQ প্রশ্নব্যাংক' }} →</Link>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <button type="button" class="tools-shortcut text-left" @click="openTool('formulas')">
                        <span class="tools-shortcut-index">01 • PDF SHEET</span>
                        <strong>{{ en ? 'Science & Math Formula Bank' : 'বিজ্ঞান ও গণিত সূত্র ব্যাংক' }}</strong>
                        <span>{{ en ? 'Formulas + Unit Converter + PDF' : 'অধ্যায়ভিত্তিক সূত্র + কনভার্টার + PDF' }} →</span>
                    </button>
                    <button type="button" class="tools-shortcut text-left" @click="openTool('gpa')">
                        <span class="tools-shortcut-index">02 • GPA & ADMISSION</span>
                        <strong>{{ en ? 'GPA & University Eligibility' : 'GPA ও ভার্সিটি যোগ্যতা যাচাই' }}</strong>
                        <span>{{ en ? 'SSC, HSC, Honours & PDF Card' : 'SSC, HSC, অনার্স ও মার্কশিট PDF' }} →</span>
                    </button>
                    <button type="button" class="tools-shortcut text-left" @click="openTool('resizer')">
                        <span class="tools-shortcut-index">03 • IMAGE STUDIO</span>
                        <strong>{{ en ? 'Photo & Signature Studio' : 'ছবি ও স্বাক্ষর স্টুডিও' }}</strong>
                        <span>{{ en ? '300×300, 300×80 & Custom KB' : '৩০০×৩০০, ৩০০×৮০ ও কাস্টম সাইজ' }} →</span>
                    </button>
                    <Link href="/results" class="tools-shortcut">
                        <span class="tools-shortcut-index">04 • RESULTS</span>
                        <strong>{{ en ? 'Exam Result Checker' : 'বোর্ড ও ভর্তি রেজাল্ট অনুসন্ধান' }}</strong>
                        <span>{{ en ? 'Fast board result lookup' : 'দ্রুত ফলাফল দেখুন' }} →</span>
                    </Link>
                </div>
            </section>

            <!-- Active Tool Navigation Bar -->
            <div v-if="activeTab !== 'catalog'" class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-white/10 transition"
                    @click="backToCatalog"
                >
                    <ArrowLeftIcon class="w-4 h-4" />
                    <span>{{ en ? 'Back to All Student Tools' : 'সকল স্টুডেন্ট টুলসে ফিরে যান' }}</span>
                </button>

                <div class="flex items-center gap-2 text-xs">
                    <span class="text-gray-500 hidden sm:inline">{{ en ? 'Switch Tool:' : 'অন্য টুল খুলুন:' }}</span>
                    <select
                        :value="activeTab"
                        class="bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-bold"
                        @change="openTool($event.target.value)"
                    >
                        <optgroup :label="en ? 'Student Tools' : 'স্টুডেন্ট টুলস'">
                            <option v-for="t in toolsList.filter(i => i.category === 'student')" :key="t.id" :value="t.id">
                                {{ en ? t.titleEn : t.titleBn }}
                            </option>
                        </optgroup>
                        <optgroup :label="en ? 'Other Utilities' : 'অন্যান্য ইউটিলিটি'">
                            <option v-for="t in toolsList.filter(i => i.category === 'other')" :key="t.id" :value="t.id">
                                {{ en ? t.titleEn : t.titleBn }}
                            </option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <!-- CATALOG GRID -->
            <section v-if="activeTab === 'catalog'" class="space-y-5">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span class="font-semibold">
                        {{ categoryFilter === 'student' && !searchQuery
                            ? (en ? '8 Essential Student & Exam Tools' : 'শিক্ষার্থীদের সবচেয়ে প্রয়োজনীয় ৮টি স্মার্ট টুলস')
                            : (en ? `Showing ${filteredTools.length} tools` : `মোট ${filteredTools.length}টি ফ্রি টুলস দেখানো হচ্ছে`)
                        }}
                    </span>
                    <button
                        v-if="categoryFilter === 'student'"
                        type="button"
                        class="font-bold text-[var(--primary)] hover:underline"
                        @click="categoryFilter = 'other'"
                    >
                        {{ en ? 'See Other General & Coding Utilities →' : 'অন্যান্য ইউটিলিটি ও কোডিং টুলস দেখুন →' }}
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div
                        v-for="tool in filteredTools"
                        :key="tool.id"
                        role="button"
                        tabindex="0"
                        class="tool-card card p-5 sm:p-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] hover:border-[var(--primary)]/50 transition-all duration-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 cursor-pointer flex flex-col justify-between group text-left"
                        @click="openTool(tool.id)"
                        @keydown.enter.prevent="openTool(tool.id)"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[var(--surface2)] border border-[var(--border)] text-[var(--text-muted)]">
                                    {{ en ? tool.badgeEn : tool.badgeBn }}
                                </span>
                                <span
                                    v-if="tool.hasPdf"
                                    class="inline-flex items-center gap-1 text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full"
                                >
                                    <ArrowDownTrayIcon class="w-3 h-3" />
                                    <span>PDF Download</span>
                                </span>
                                <span
                                    v-else
                                    class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full"
                                >
                                    {{ en ? '100% Free' : '১০০% ফ্রি' }}
                                </span>
                            </div>

                            <div class="flex items-start gap-3.5 mb-2">
                                <div class="p-2.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/5 shrink-0 group-hover:scale-110 transition">
                                    <component :is="tool.icon" class="w-6 h-6" :class="tool.color" />
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-base text-gray-900 dark:text-white group-hover:text-[var(--primary)] transition leading-snug">
                                        {{ en ? tool.titleEn : tool.titleBn }}
                                    </h3>
                                    <span class="text-[10px] text-[var(--text-muted)] uppercase tracking-wider font-semibold">
                                        {{ categoryLabels[tool.category] }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-2 line-clamp-3 leading-relaxed">
                                {{ en ? tool.descEn : tool.descBn }}
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-xs font-bold text-[var(--primary)]">
                            <span>{{ en ? 'Open Tool' : 'টুলটি ব্যবহার করুন' }}</span>
                            <span class="group-hover:translate-x-1 transition">→</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ACTIVE TOOL VIEWS -->
            <section v-else-if="activeTab === 'formulas'"><FormulaConverter :en="en" /></section>
            <section v-else-if="activeTab === 'gpa'"><SmartGpaCalculator :en="en" /></section>
            <section v-else-if="activeTab === 'routine'"><StudyPlanner :en="en" /></section>
            <section v-else-if="activeTab === 'resizer'"><PhotoSignatureStudio :en="en" /></section>
            <section v-else-if="activeTab === 'age'"><GeneralAgeCalculator :en="en" /></section>
            <section v-else-if="activeTab === 'negative_marking'"><NegativeMarkingCalculator :en="en" /></section>
            <section v-else-if="activeTab === 'image_to_pdf'"><ImageToPdf :en="en" /></section>
            <section v-else-if="activeTab === 'pomodoro'"><PomodoroTimer :en="en" /></section>

            <!-- OTHER UTILITIES -->
            <section v-else-if="activeTab === 'markdown'"><MarkdownEditor :en="en" /></section>
            <section v-else-if="activeTab === 'pdf_to_text'"><PdfToText :en="en" /></section>
            <section v-else-if="activeTab === 'currency'"><CurrencyConverter :en="en" /></section>
            <section v-else-if="activeTab === 'code_formatter'"><CodeFormatter :en="en" /></section>
            <section v-else-if="activeTab === 'json_viewer'"><JsonViewer :en="en" /></section>
            <section v-else-if="activeTab === 'regex'"><RegexTester :en="en" /></section>
            <section v-else-if="activeTab === 'text'">
                <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl space-y-5">
                    <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2 border-b border-gray-200 dark:border-white/10 pb-4">
                        <DocumentTextIcon class="w-6 h-6 text-pink-500" />
                        <span>{{ en ? 'Word Counter & Text Case Tools' : 'ওয়ার্ড কাউন্টার ও টেক্সট কনভার্টার' }}</span>
                    </h2>
                    <textarea
                        v-model="textInput"
                        rows="7"
                        :placeholder="en ? 'Type or paste your text here...' : 'এখানে আপনার লেখা পেস্ট করুন বা লিখুন...'"
                        class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                    />
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center">
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                            <span class="text-[10px] text-gray-500 block font-bold">{{ en ? 'WORDS' : 'শব্দ' }}</span>
                            <strong class="font-mono text-xl">{{ textStats.words }}</strong>
                        </div>
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                            <span class="text-[10px] text-gray-500 block font-bold">{{ en ? 'CHARS' : 'বর্ণ' }}</span>
                            <strong class="font-mono text-xl text-amber-500">{{ textStats.charCount }}</strong>
                        </div>
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                            <span class="text-[10px] text-gray-500 block font-bold">{{ en ? 'NO SPACES' : 'স্পেস ছাড়া' }}</span>
                            <strong class="font-mono text-xl text-emerald-500">{{ textStats.charNoSpaces }}</strong>
                        </div>
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                            <span class="text-[10px] text-gray-500 block font-bold">{{ en ? 'SENTENCES' : 'বাক্য' }}</span>
                            <strong class="font-mono text-xl text-purple-500">{{ textStats.sentences }}</strong>
                        </div>
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                            <span class="text-[10px] text-gray-500 block font-bold">{{ en ? 'READ TIME' : 'পড়ার সময়' }}</span>
                            <strong class="font-mono text-xl text-blue-500">{{ textStats.readingTimeMins }}m</strong>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline text-xs py-1.5 px-3" @click="convertText('upper')">UPPERCASE</button>
                        <button type="button" class="btn btn-outline text-xs py-1.5 px-3" @click="convertText('lower')">lowercase</button>
                        <button type="button" class="btn btn-outline text-xs py-1.5 px-3" @click="convertText('title')">Title Case</button>
                        <button type="button" class="btn btn-outline text-xs py-1.5 px-3 text-rose-500" @click="convertText('clear')">{{ en ? 'Clear' : 'মুছে ফেলুন' }}</button>
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
.tools-shortcut { display: flex; min-height: 138px; flex-direction: column; align-items: flex-start; gap: .45rem; border: 1px solid var(--border); border-radius: 1rem; background: var(--surface); padding: 1rem; color: var(--text); transition: transform .18s ease, border-color .18s ease, background .18s ease; }
.tools-shortcut:hover { transform: translateY(-2px); border-color: color-mix(in srgb, var(--primary) 38%, var(--border)); background: color-mix(in srgb, var(--primary) 5%, var(--surface)); }
.tools-shortcut-index { color: var(--primary); font-size: .68rem; font-weight: 800; letter-spacing: .1em; }
.tools-shortcut strong { font-size: .95rem; line-height: 1.4; }
.tools-shortcut > span:last-child { margin-top: auto; color: var(--text-muted); font-size: .76rem; }
@media (max-width: 640px) { .tools-shortcut { min-height: 118px; } }
</style>
