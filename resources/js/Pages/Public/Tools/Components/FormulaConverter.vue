<script setup>
import { ref, computed } from 'vue';
import {
    SparklesIcon,
    MagnifyingGlassIcon,
    ClipboardDocumentIcon,
    CheckIcon,
    ArrowPathIcon,
    CalculatorIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const subTab = ref('formulas'); // 'formulas' | 'converter'
const formulaSubject = ref('physics'); // 'physics' | 'math' | 'chemistry'
const formulaSearch = ref('');
const copiedIdx = ref(null);

const formulas = [
    // PHYSICS
    { subject: 'physics', topic: 'গতিবিদ্যা (Kinematics)', name: 'গতির ১ম সমীকরণ', expr: 'v = u + at', desc: 'শেষ বেগ, আদি বেগ, ত্বরণ ও সময়' },
    { subject: 'physics', topic: 'গতিবিদ্যা (Kinematics)', name: 'গতির ২য় সমীকরণ', expr: 's = ut + (1/2)at²', desc: 'অতিক্রান্ত দূরত্ব' },
    { subject: 'physics', topic: 'গতিবিদ্যা (Kinematics)', name: 'গতির ৩য় সমীকরণ', expr: 'v² = u² + 2as', desc: 'দূরত্ব ও বেগের সম্পর্ক' },
    { subject: 'physics', topic: 'নিউটনীয় বলবিদ্যা (Dynamics)', name: 'বলের সমীকরণ', expr: 'F = ma', desc: 'বল = ভর × ত্বরণ' },
    { subject: 'physics', topic: 'নিউটনীয় বলবিদ্যা (Dynamics)', name: 'ভরবেগ সংরক্ষণ', expr: 'm₁u₁ + m₂u₂ = m₁v₁ + m₂v₂', desc: 'সংঘর্ষের আগে ও পরের মোট ভরবেগ' },
    { subject: 'physics', topic: 'কাজ, ক্ষমতা ও শক্তি (Work & Energy)', name: 'কাজের সমীকরণ', expr: 'W = F · s · cos(θ)', desc: 'বল ও সরণের মধ্যবর্তী কোণ θ হলে' },
    { subject: 'physics', topic: 'কাজ, ক্ষমতা ও শক্তি (Work & Energy)', name: 'গতিশক্তি (Kinetic Energy)', expr: 'Ek = (1/2)mv²', desc: 'বস্তুর গতির কারণে প্রাপ্ত শক্তি' },
    { subject: 'physics', topic: 'কাজ, ক্ষমতা ও শক্তি (Work & Energy)', name: 'বিভব শক্তি (Potential Energy)', expr: 'Ep = mgh', desc: 'উচ্চতার জন্য সঞ্চিত শক্তি' },
    { subject: 'physics', topic: 'চলতড়িৎ (Current Electricity)', name: 'ওহমের সূত্র', expr: 'V = I · R', desc: 'বিভব পার্থক্য, তড়িৎ প্রবাহ ও রোধ' },
    { subject: 'physics', topic: 'চলতড়িৎ (Current Electricity)', name: 'তড়িৎ ক্ষমতা', expr: 'P = V · I = I²R = V²/R', desc: 'ওয়াট এককে তড়িৎ ক্ষমতা' },
    { subject: 'physics', topic: 'মহাকর্ষ ও অভিকর্ষ (Gravitation)', name: 'মহাকর্ষ সূত্র', expr: 'F = G · (m₁ · m₂) / r²', desc: 'মহাকর্ষীয় ধ্রুবক G = 6.673×10⁻¹¹ N·m²/kg²' },

    // MATH
    { subject: 'math', topic: 'বীজগণিত (Algebra)', name: 'বর্গের বিস্তার', expr: '(a + b)² = a² + 2ab + b²', desc: 'বর্গের সাধারণ সূত্র' },
    { subject: 'math', topic: 'বীজগণিত (Algebra)', name: 'বর্গ বিয়োগ', expr: 'a² - b² = (a + b)(a - b)', desc: 'দুটি বর্গের অন্তর' },
    { subject: 'math', topic: 'বীজগণিত (Algebra)', name: 'ঘন বিস্তার', expr: '(a + b)³ = a³ + 3a²b + 3ab² + b³', desc: 'ঘনের সূত্র' },
    { subject: 'math', topic: 'দ্বিঘাত সমীকরণ (Quadratic)', name: 'মূল নির্ণয়ের সূত্র', expr: 'x = [-b ± √(b² - 4ac)] / 2a', desc: 'ax² + bx + c = 0 সমীকরণের সমাধান' },
    { subject: 'math', topic: 'ত্রিকোণমিতি (Trigonometry)', name: 'পিথাগোরাস অভেদ', expr: 'sin²θ + cos²θ = 1', desc: 'মৌলিক ত্রিকোণমিতিক সম্পর্ক' },
    { subject: 'math', topic: 'ত্রিকোণমিতি (Trigonometry)', name: 'ট্যান ও সাইন সম্পর্ক', expr: 'tanθ = sinθ / cosθ ; 1 + tan²θ = sec²θ', desc: 'ট্যানজেন্ট ও সেকেন্ট সম্পর্ক' },
    { subject: 'math', topic: 'ত্রিকোণমিতি (Trigonometry)', name: 'দ্বিগুণ কোণ', expr: 'sin(2θ) = 2sinθ·cosθ ; cos(2θ) = cos²θ - sin²θ', desc: 'ডাবল অ্যাঙ্গেল সমীকরণ' },
    { subject: 'math', topic: 'পরিমিতি (Mensuration)', name: 'বৃত্তের ক্ষেত্রফল ও পরিধি', expr: 'Area = πr²  |  Circumference = 2πr', desc: 'বৃত্তের ব্যাসার্ধ r হলে' },
    { subject: 'math', topic: 'পরিমিতি (Mensuration)', name: 'সিলিন্ডারের আয়তন', expr: 'Volume = πr²h  |  Surface = 2πr(r + h)', desc: 'ব্যাসার্ধ r এবং উচ্চতা h' },
    { subject: 'math', topic: 'পরিমিতি (Mensuration)', name: 'গোলকের আয়তন', expr: 'Volume = (4/3)πr³  |  Area = 4πr²', desc: 'গোলকের ব্যাসার্ধ r হলে' },

    // CHEMISTRY
    { subject: 'chemistry', topic: 'মোল ও গ্যাস (Mole & Gas)', name: 'মোল সংখ্যা গণনা', expr: 'n = w / M = V / 22.4 = N / 6.023×10²³', desc: 'ভর, আনবিক ভর, STP-তে আয়তন ও কণা সংখ্যা' },
    { subject: 'chemistry', topic: 'মোল ও গ্যাস (Mole & Gas)', name: 'আদর্শ গ্যাস সমীকরণ', expr: 'PV = nRT', desc: 'R = 0.0821 L·atm/(mol·K) = 8.314 J/(mol·K)' },
    { subject: 'chemistry', topic: 'মোল ও গ্যাস (Mole & Gas)', name: 'বয়েল ও চার্লসের সমন্বয়', expr: '(P₁V₁) / T₁ = (P₂V₂) / T₂', desc: 'চাপ, আয়তন ও কেলভিন তাপমাত্রার পরিবর্তন' },
    { subject: 'chemistry', topic: 'দ্রবণ ও রাসায়নিক গণনা (Solution)', name: 'মোলারিটি গণনা', expr: 'S = (1000 · w) / (M · V[mL])', desc: 'মোলার ঘনমাত্রা (mol/L)' },
    { subject: 'chemistry', topic: 'অম্ল-ক্ষার ও দ্রবণ (Acid-Base)', name: 'pH পরিমাপ', expr: 'pH = -log₁₀[H⁺]  |  pH + pOH = 14', desc: '২৫°C তাপমাত্রায় বিশুদ্ধ পানিতে' },
    { subject: 'chemistry', topic: 'রাসায়নিক তাপগতিবিদ্যা (Thermodynamics)', name: 'এনথালপি পরিবর্তন', expr: 'ΔH = ΣH(products) - ΣH(reactants)', desc: 'বিক্রিয়ার মোট তাপ পরিবর্তন' },
];

const filteredFormulas = computed(() => {
    return formulas.filter(f => {
        const matchesSubj = f.subject === formulaSubject.value;
        if (!formulaSearch.value.trim()) return matchesSubj;
        const q = formulaSearch.value.toLowerCase();
        return matchesSubj && (
            f.name.toLowerCase().includes(q) ||
            f.expr.toLowerCase().includes(q) ||
            f.topic.toLowerCase().includes(q) ||
            f.desc.toLowerCase().includes(q)
        );
    });
});

function copyFormula(expr, idx) {
    navigator.clipboard.writeText(expr);
    copiedIdx.value = idx;
    setTimeout(() => { copiedIdx.value = null; }, 2000);
}

// ==========================================
// INTERACTIVE UNIT CONVERTER
// ==========================================
const converterType = ref('length'); // length | weight | temp | data | speed
const convInput = ref(1);
const convFrom = ref('m');
const convTo = ref('km');

const unitOptions = computed(() => {
    switch (converterType.value) {
        case 'length':
            return [
                { key: 'm', label: 'Meter (মি.)', factor: 1 },
                { key: 'km', label: 'Kilometer (কি.মি.)', factor: 1000 },
                { key: 'cm', label: 'Centimeter (সে.মি.)', factor: 0.01 },
                { key: 'mm', label: 'Millimeter (মি.মি.)', factor: 0.001 },
                { key: 'mile', label: 'Mile (মাইল)', factor: 1609.344 },
                { key: 'ft', label: 'Foot (ফুট)', factor: 0.3048 },
                { key: 'inch', label: 'Inch (ইঞ্চি)', factor: 0.0254 },
            ];
        case 'weight':
            return [
                { key: 'kg', label: 'Kilogram (কেজি)', factor: 1 },
                { key: 'g', label: 'Gram (গ্রাম)', factor: 0.001 },
                { key: 'mg', label: 'Milligram (মি.গ্রা.)', factor: 0.000001 },
                { key: 'lb', label: 'Pound (পাউন্ড)', factor: 0.45359237 },
                { key: 'ton', label: 'Metric Ton (টন)', factor: 1000 },
            ];
        case 'data':
            return [
                { key: 'B', label: 'Bytes (B)', factor: 1 },
                { key: 'KB', label: 'Kilobytes (KB)', factor: 1024 },
                { key: 'MB', label: 'Megabytes (MB)', factor: 1024 * 1024 },
                { key: 'GB', label: 'Gigabytes (GB)', factor: 1024 * 1024 * 1024 },
                { key: 'TB', label: 'Terabytes (TB)', factor: 1024 * 1024 * 1024 * 1024 },
            ];
        case 'speed':
            return [
                { key: 'ms', label: 'm/s (মিটার/সেকেন্ড)', factor: 1 },
                { key: 'kmh', label: 'km/h (কিমি/ঘণ্টা)', factor: 0.277778 },
                { key: 'mph', label: 'mph (মাইল/ঘণ্টা)', factor: 0.44704 },
            ];
        case 'temp':
            return [
                { key: 'c', label: 'Celsius (°C)' },
                { key: 'f', label: 'Fahrenheit (°F)' },
                { key: 'k', label: 'Kelvin (K)' },
            ];
        default:
            return [];
    }
});

function handleTypeChange() {
    const opts = unitOptions.value;
    if (opts.length >= 2) {
        convFrom.value = opts[0].key;
        convTo.value = opts[1].key;
    }
}

const convertedValue = computed(() => {
    const val = Number(convInput.value);
    if (isNaN(val)) return '0';

    if (converterType.value === 'temp') {
        let celsius = val;
        if (convFrom.value === 'f') celsius = (val - 32) * (5 / 9);
        else if (convFrom.value === 'k') celsius = val - 273.15;

        if (convTo.value === 'c') return celsius.toFixed(2);
        if (convTo.value === 'f') return (celsius * (9 / 5) + 32).toFixed(2);
        if (convTo.value === 'k') return (celsius + 273.15).toFixed(2);
        return celsius.toFixed(2);
    }

    const fromUnit = unitOptions.value.find(u => u.key === convFrom.value);
    const toUnit = unitOptions.value.find(u => u.key === convTo.value);
    if (!fromUnit || !toUnit) return '0';

    const baseVal = val * fromUnit.factor;
    const res = baseVal / toUnit.factor;

    if (Math.abs(res) < 0.0001 && res !== 0) return res.toExponential(4);
    return Number(res.toFixed(6)).toString();
});
</script>

<template>
    <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
        <!-- Subtab Switcher -->
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-white/10 pb-5 flex-wrap gap-4">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5">
                    <CalculatorIcon class="w-6 h-6 text-cyan-500" />
                    {{ en ? 'Science Formula Sheet & Unit Converter' : 'বিজ্ঞান ও গণিত সূত্রাবলি এবং একক কনভার্টার' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Essential formulas for Physics, Math, Chemistry, plus a multi-unit live converter.' : 'পদার্থ, গণিত ও রসায়নের গুরুত্বপূর্ণ সমীকরণ এবং যেকোনো এককের রিয়েল-টাইম কনভার্টার।' }}
                </p>
            </div>

            <!-- Tabs -->
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="subTab = 'formulas'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition border"
                    :class="subTab === 'formulas' ? 'bg-cyan-500 text-white border-cyan-500 shadow-md shadow-cyan-500/25' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10'"
                >
                    📜 {{ en ? 'Formula Sheet' : 'গুরুত্বপূর্ণ সূত্রাবলি' }}
                </button>
                <button
                    type="button"
                    @click="subTab = 'converter'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition border"
                    :class="subTab === 'converter' ? 'bg-cyan-500 text-white border-cyan-500 shadow-md shadow-cyan-500/25' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10'"
                >
                    🔄 {{ en ? 'Unit Converter' : 'একক কনভার্টার' }}
                </button>
            </div>
        </div>

        <!-- ==================== 1. FORMULAS VIEW ==================== -->
        <div v-if="subTab === 'formulas'" class="mt-6 space-y-6">
            <!-- Subject Switcher & Search Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <button
                        type="button"
                        @click="formulaSubject = 'physics'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border"
                        :class="formulaSubject === 'physics' ? 'bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 border-cyan-500/40' : 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400'"
                    >
                        ⚡ {{ en ? 'Physics' : 'পদার্থবিজ্ঞান' }}
                    </button>
                    <button
                        type="button"
                        @click="formulaSubject = 'math'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border"
                        :class="formulaSubject === 'math' ? 'bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 border-cyan-500/40' : 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400'"
                    >
                        📐 {{ en ? 'Mathematics' : 'গণিত ও ত্রিকোণমিতি' }}
                    </button>
                    <button
                        type="button"
                        @click="formulaSubject = 'chemistry'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition border"
                        :class="formulaSubject === 'chemistry' ? 'bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 border-cyan-500/40' : 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400'"
                    >
                        🧪 {{ en ? 'Chemistry' : 'রসায়নবিজ্ঞান' }}
                    </button>
                </div>

                <div class="relative w-full sm:w-64">
                    <MagnifyingGlassIcon class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" />
                    <input
                        v-model="formulaSearch"
                        type="text"
                        :placeholder="en ? 'Search formulas...' : 'সূত্র খুঁজুন...'"
                        class="w-full pl-9 pr-3 py-1.5 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-cyan-500"
                    />
                </div>
            </div>

            <!-- Formulas Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                    v-for="(item, idx) in filteredFormulas"
                    :key="idx"
                    class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 hover:border-cyan-500/40 transition group relative"
                >
                    <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400 mb-1.5">
                        <span class="font-bold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider">{{ item.topic }}</span>
                        <button
                            type="button"
                            @click="copyFormula(item.expr, idx)"
                            class="text-gray-400 hover:text-cyan-500 transition p-1"
                            title="Copy formula"
                        >
                            <CheckIcon v-if="copiedIdx === idx" class="w-4 h-4 text-emerald-500" />
                            <ClipboardDocumentIcon v-else class="w-4 h-4" />
                        </button>
                    </div>

                    <h4 class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm mb-2">
                        {{ item.name }}
                    </h4>

                    <!-- Math Expression Block -->
                    <div class="p-3 rounded-xl bg-white dark:bg-[#161a26] border border-gray-200 dark:border-white/5 font-mono text-cyan-700 dark:text-cyan-300 font-bold text-sm tracking-wide overflow-x-auto">
                        {{ item.expr }}
                    </div>

                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-2">
                        {{ item.desc }}
                    </p>
                </div>
            </div>
        </div>

        <!-- ==================== 2. UNIT CONVERTER VIEW ==================== -->
        <div v-else class="mt-6 space-y-6">
            <!-- Category Selector -->
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <span class="text-gray-500 font-semibold">{{ en ? 'Quantity:' : 'এককের ধরন:' }}</span>
                <button
                    v-for="cat in [
                        { key: 'length', label: en ? 'Length' : 'দৈর্ঘ্য' },
                        { key: 'weight', label: en ? 'Weight' : 'ভর / ওজন' },
                        { key: 'temp', label: en ? 'Temperature' : 'তাপমাত্রা' },
                        { key: 'data', label: en ? 'Data Storage' : 'ডিজিটাল ডাটা' },
                        { key: 'speed', label: en ? 'Speed' : 'গতিবেগ' },
                    ]"
                    :key="cat.key"
                    type="button"
                    @click="converterType = cat.key; handleTypeChange()"
                    class="px-3 py-1.5 rounded-xl font-bold transition border"
                    :class="converterType === cat.key ? 'bg-cyan-500 text-white border-cyan-500' : 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                >
                    {{ cat.label }}
                </button>
            </div>

            <!-- Conversion Inputs Grid -->
            <div class="p-6 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <!-- From -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                        {{ en ? 'From Value & Unit:' : 'প্রারম্ভিক মান ও একক:' }}
                    </label>
                    <div class="flex items-center gap-2">
                        <input
                            v-model.number="convInput"
                            type="number"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-base font-mono font-bold text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500"
                        />
                        <select
                            v-model="convFrom"
                            class="w-36 px-3 py-2.5 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-xs font-semibold text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500"
                        >
                            <option v-for="u in unitOptions" :key="u.key" :value="u.key">{{ u.label }}</option>
                        </select>
                    </div>
                </div>

                <!-- To Result -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                        {{ en ? 'Converted Value & Unit:' : 'রূপান্তরিত ফলাফল:' }}
                    </label>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 px-4 py-2.5 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-base font-mono font-bold text-cyan-600 dark:text-cyan-400 overflow-x-auto whitespace-nowrap">
                            {{ convertedValue }}
                        </div>
                        <select
                            v-model="convTo"
                            class="w-36 px-3 py-2.5 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-xs font-semibold text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500"
                        >
                            <option v-for="u in unitOptions" :key="u.key" :value="u.key">{{ u.label }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
