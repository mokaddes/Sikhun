<script setup>
import { ref, computed } from 'vue';
import { downloadBrandedPdf } from '@/utils/studentPdfExport';
import {
    AcademicCapIcon,
    ArrowDownTrayIcon,
    PlusIcon,
    TrashIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const mode = ref('board'); // 'board' | 'eligibility' | 'cgpa'
const stream = ref('science'); // 'science' | 'humanities' | 'business'
const studentName = ref('');
const isExporting = ref(false);

const streamPresets = {
    science: [
        { name_bn: 'বাংলা', name_en: 'Bangla', grade: 5.0 },
        { name_bn: 'ইংরেজি', name_en: 'English', grade: 5.0 },
        { name_bn: 'তথ্য ও যোগাযোগ প্রযুক্তি (ICT)', name_en: 'ICT', grade: 5.0 },
        { name_bn: 'পদার্থবিজ্ঞান', name_en: 'Physics', grade: 5.0 },
        { name_bn: 'রসায়ন', name_en: 'Chemistry', grade: 5.0 },
        { name_bn: 'উচ্চতর গণিত', name_en: 'Higher Math', grade: 5.0 },
    ],
    humanities: [
        { name_bn: 'বাংলা', name_en: 'Bangla', grade: 5.0 },
        { name_bn: 'ইংরেজি', name_en: 'English', grade: 4.0 },
        { name_bn: 'তথ্য ও যোগাযোগ প্রযুক্তি (ICT)', name_en: 'ICT', grade: 5.0 },
        { name_bn: 'পৌরনীতি ও সুশাসন', name_en: 'Civics & Good Governance', grade: 5.0 },
        { name_bn: 'অর্থনীতি', name_en: 'Economics', grade: 4.0 },
        { name_bn: 'ইতিহাস / যুক্তিবিদ্যা', name_en: 'History / Logic', grade: 5.0 },
    ],
    business: [
        { name_bn: 'বাংলা', name_en: 'Bangla', grade: 5.0 },
        { name_bn: 'ইংরেজি', name_en: 'English', grade: 4.0 },
        { name_bn: 'তথ্য ও যোগাযোগ প্রযুক্তি (ICT)', name_en: 'ICT', grade: 5.0 },
        { name_bn: 'হিসাববিজ্ঞান', name_en: 'Accounting', grade: 5.0 },
        { name_bn: 'ব্যবসায় সংগঠন ও ব্যবস্থাপনা', name_en: 'Business Management', grade: 5.0 },
        { name_bn: 'ফিন্যান্স, ব্যাংকিং ও বিমা', name_en: 'Finance & Banking', grade: 4.0 },
    ],
};

const subjects = ref(streamPresets.science.map((s) => ({ ...s })));
const fourthGrade = ref(5.0);

function changeStream(s) {
    stream.value = s;
    subjects.value = streamPresets[s].map((item) => ({ ...item }));
}

function addSubject() {
    subjects.value.push({ name_bn: `অতিরিক্ত বিষয় ${subjects.value.length + 1}`, name_en: `Subject ${subjects.value.length + 1}`, grade: 5.0 });
}

function removeSubject(idx) {
    if (subjects.value.length > 3) subjects.value.splice(idx, 1);
}

const boardResult = computed(() => {
    let sum = 0;
    let failed = false;
    for (const s of subjects.value) {
        const g = Number(s.grade);
        if (g === 0) failed = true;
        sum += g;
    }
    const bonus = Math.max(0, (Number(fourthGrade.value) || 0) - 2.0);
    if (failed) return { gpa: 0, letter: 'F', bonus: 0, withoutFourth: 0, passed: false };

    const withoutFourth = Math.min(5.0, +(sum / subjects.value.length).toFixed(2));
    const gpa = Math.min(5.0, +((sum + bonus) / subjects.value.length).toFixed(2));
    let letter = 'D';
    if (gpa >= 5.0) letter = 'A+';
    else if (gpa >= 4.0) letter = 'A';
    else if (gpa >= 3.5) letter = 'A-';
    else if (gpa >= 3.0) letter = 'B';
    else if (gpa >= 2.0) letter = 'C';

    return { gpa, letter, bonus: +bonus.toFixed(2), withoutFourth, passed: true };
});

// Eligibility Checker
const sscGpaInput = ref(5.0);
const hscGpaInput = ref(4.75);
const eligGroup = ref('science');

const eligibilityList = computed(() => {
    const s = Number(sscGpaInput.value) || 0;
    const h = Number(hscGpaInput.value) || 0;
    const total = +(s + h).toFixed(2);
    const g = eligGroup.value;

    const checks = [
        {
            name: 'ঢাকা বিশ্ববিদ্যালয় (DU)',
            req: g === 'science' ? 'মোট ৮.০০ (পৃথক ৩.৫০)' : g === 'business' ? 'মোট ৭.৫০ (পৃথক ৩.০০)' : 'মোট ৭.৫০ (পৃথক ৩.০০)',
            ok: g === 'science' ? (total >= 8.0 && s >= 3.5 && h >= 3.5) : (total >= 7.5 && s >= 3.0 && h >= 3.0),
        },
        {
            name: 'রাজশাহী বিশ্ববিদ্যালয় (RU)',
            req: g === 'science' ? 'মোট ৮.০০ (পৃথক ৩.৫০)' : g === 'business' ? 'মোট ৭.৫০ (পৃথক ৩.২৫)' : 'মোট ৭.০০ (পৃথক ৩.০০)',
            ok: g === 'science' ? (total >= 8.0 && s >= 3.5 && h >= 3.5) : g === 'business' ? (total >= 7.5 && s >= 3.25 && h >= 3.25) : (total >= 7.0 && s >= 3.0 && h >= 3.0),
        },
        {
            name: 'জিএসটি গুচ্ছ বিশ্ববিদ্যালয় (GST)',
            req: g === 'science' ? 'মোট ৮.০০ (পৃথক ৩.৫০)' : g === 'business' ? 'মোট ৬.৫০ (পৃথক ৩.০০)' : 'মোট ৬.০০ (পৃথক ৩.০০)',
            ok: g === 'science' ? (total >= 8.0 && s >= 3.5 && h >= 3.5) : g === 'business' ? (total >= 6.5 && s >= 3.0 && h >= 3.0) : (total >= 6.0 && s >= 3.0 && h >= 3.0),
        },
        ...(g === 'science' ? [
            {
                name: 'মেডিকেল ও ডেন্টাল (MBBS / BDS)',
                req: 'মোট ৯.০০ (পৃথক ৪.০০)',
                ok: total >= 9.0 && s >= 4.0 && h >= 4.0,
            },
            {
                name: 'কৃষি গুচ্ছ বিশ্ববিদ্যালয়',
                req: 'মোট ৮.৫০ (পৃথক ৪.০০)',
                ok: total >= 8.5 && s >= 4.0 && h >= 4.0,
            },
        ] : []),
    ];

    return { total, checks };
});

// Honours CGPA
const semesters = ref([
    { name: '১ম বর্ষ / সেমিস্টার', gpa: 3.5, credits: 30 },
    { name: '২য় বর্ষ / সেমিস্টার', gpa: 3.6, credits: 30 },
    { name: '৩য় বর্ষ / সেমিস্টার', gpa: 3.7, credits: 30 },
    { name: '৪র্থ বর্ষ / সেমিস্টার', gpa: 3.8, credits: 30 },
]);

const cgpaResult = computed(() => {
    let pts = 0;
    let cr = 0;
    for (const s of semesters.value) {
        pts += (Number(s.gpa) || 0) * (Number(s.credits) || 0);
        cr += Number(s.credits) || 0;
    }
    return cr > 0 ? +(pts / cr).toFixed(2) : 0;
});

async function exportGpaPdf() {
    if (isExporting.value) return;
    isExporting.value = true;
    try {
        const rows = subjects.value.map((s, i) => `
            <tr>
                <td style="padding:8px 10px;border:1px solid #cbd5e1;">${i + 1}. ${s.name_bn}</td>
                <td style="padding:8px 10px;border:1px solid #cbd5e1;text-align:center;font-weight:700;">${Number(s.grade).toFixed(2)}</td>
            </tr>
        `).join('');

        const bodyHtml = `
            <div style="padding:14px 18px;border:1px solid #10b981;background:#ecfdf5;border-radius:12px;display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <div>
                    <div style="font-size:12px;color:#047857;font-weight:700;">চূড়ান্ত বোর্ড জিপিএ (Final GPA)</div>
                    <div style="font-size:12px;color:#475569;">৪র্থ বিষয় ছাড়া: ${boardResult.value.withoutFourth.toFixed(2)} • ৪র্থ বিষয় বোনাস: +${boardResult.value.bonus}</div>
                </div>
                <div style="font-size:30px;font-weight:800;color:#059669;">GPA ${boardResult.value.gpa.toFixed(2)} (${boardResult.value.letter})</div>
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead><tr style="background:#f1f5f9;"><th style="padding:9px;border:1px solid #cbd5e1;text-align:left;">বিষয়ের নাম</th><th style="padding:9px;border:1px solid #cbd5e1;width:120px;">গ্রেড পয়েন্ট</th></tr></thead>
                <tbody>
                    ${rows}
                    <tr style="background:#fffbeb;"><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;">৪র্থ (ঐচ্ছিক) বিষয়</td><td style="padding:9px;border:1px solid #cbd5e1;text-align:center;font-weight:700;">${Number(fourthGrade.value).toFixed(2)} (বোনাস +${boardResult.value.bonus})</td></tr>
                </tbody>
            </table>
        `;
        await downloadBrandedPdf({
            title: 'একাডেমিক জিপিএ মার্কশিট সামারি',
            subtitle: `বিভাগ: ${stream.value.toUpperCase()}`,
            studentName: studentName.value.trim(),
            bodyHtml,
            fileName: 'Sikhun_GPA_Marksheet.pdf',
        });
    } finally {
        isExporting.value = false;
    }
}
</script>

<template>
    <div class="card p-5 sm:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-200 dark:border-white/10 pb-4 gap-3">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-extrabold flex items-center gap-2">
                    <AcademicCapIcon class="w-6 h-6 text-emerald-500" />
                    <span>{{ en ? 'Smart GPA, CGPA & Admission Eligibility' : 'জিপিএ ক্যালকুলেটর ও বিশ্ববিদ্যালয় যোগ্যতা যাচাই' }}</span>
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en
                        ? 'Calculate SSC/HSC GPA by group, check public university eligibility, or compute Honours CGPA.'
                        : 'বিজ্ঞান, মানবিক ও ব্যবসায় শিক্ষা বিভাগের GPA, ভার্সিটি আবেদনের যোগ্যতা এবং অনার্স CGPA হিসাব ও PDF ডাউনলোড করুন।'
                    }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="t in [
                        { key: 'board', label: en ? 'SSC / HSC GPA' : 'বোর্ড GPA' },
                        { key: 'eligibility', label: en ? 'Admission Eligibility' : 'ভার্সিটি যোগ্যতা যাচাই' },
                        { key: 'cgpa', label: en ? 'Honours CGPA' : 'অনার্স CGPA' },
                    ]"
                    :key="t.key"
                    type="button"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold border transition"
                    :class="mode === t.key ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-gray-100 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                    @click="mode = t.key"
                >
                    {{ t.label }}
                </button>
            </div>
        </div>

        <!-- 1. BOARD GPA -->
        <div v-if="mode === 'board'" class="space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <button
                        v-for="g in [
                            { key: 'science', label: en ? 'Science' : 'বিজ্ঞান' },
                            { key: 'humanities', label: en ? 'Humanities' : 'মানবিক' },
                            { key: 'business', label: en ? 'Business' : 'ব্যবসায় শিক্ষা' },
                        ]"
                        :key="g.key"
                        type="button"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition"
                        :class="stream === g.key ? 'bg-slate-900 dark:bg-amber-500 text-white dark:text-slate-950 border-transparent' : 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10'"
                        @click="changeStream(g.key)"
                    >
                        {{ g.label }}
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        v-model="studentName"
                        type="text"
                        :placeholder="en ? 'Student Name...' : 'শিক্ষার্থীর নাম...'"
                        class="px-3 py-1.5 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-xs w-40"
                    />
                    <button
                        type="button"
                        :disabled="isExporting"
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold flex items-center gap-1.5 shadow-sm"
                        @click="exportGpaPdf"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        <span>{{ en ? 'Download PDF' : 'মার্কশিট PDF' }}</span>
                    </button>
                </div>
            </div>

            <!-- GPA Banner -->
            <div class="p-6 rounded-3xl bg-emerald-500/10 border border-emerald-500/25 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase text-emerald-700 dark:text-emerald-400">
                        {{ en ? 'Final Calculated GPA' : 'হিসাবকৃত চূড়ান্ত জিপিএ' }} ({{ boardResult.letter }})
                    </span>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                        {{ en ? `Without 4th Subject: ${boardResult.withoutFourth.toFixed(2)} • 4th Bonus: +${boardResult.bonus}` : `৪র্থ বিষয় ছাড়া জিপিএ: ${boardResult.withoutFourth.toFixed(2)} • ৪র্থ বিষয় বোনাস: +${boardResult.bonus}` }}
                    </p>
                </div>
                <div class="text-4xl sm:text-5xl font-black font-mono text-emerald-600 dark:text-emerald-400">
                    {{ boardResult.gpa.toFixed(2) }}
                </div>
            </div>

            <!-- Subjects List -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div
                    v-for="(sub, idx) in subjects"
                    :key="idx"
                    class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 flex items-center justify-between gap-2"
                >
                    <input
                        v-model="sub.name_bn"
                        type="text"
                        class="bg-transparent text-xs font-bold text-gray-800 dark:text-gray-200 flex-1 focus:outline-none"
                    />
                    <div class="flex items-center gap-1.5">
                        <select v-model="sub.grade" class="bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 rounded-xl px-2.5 py-1 text-xs font-mono font-bold">
                            <option :value="5.0">A+ (5.00)</option>
                            <option :value="4.0">A (4.00)</option>
                            <option :value="3.5">A- (3.50)</option>
                            <option :value="3.0">B (3.00)</option>
                            <option :value="2.0">C (2.00)</option>
                            <option :value="1.0">D (1.00)</option>
                            <option :value="0.0">F (0.00)</option>
                        </select>
                        <button v-if="subjects.length > 3" type="button" class="text-gray-400 hover:text-rose-500 p-1" @click="removeSubject(idx)">
                            <TrashIcon class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <button type="button" class="px-3.5 py-2 rounded-xl border border-dashed border-gray-300 dark:border-white/15 text-xs font-bold flex items-center gap-1 hover:border-emerald-500" @click="addSubject">
                    <PlusIcon class="w-4 h-4" />
                    <span>{{ en ? 'Add Subject' : 'নতুন বিষয় যোগ করুন' }}</span>
                </button>

                <div class="flex items-center gap-3 p-3 rounded-2xl bg-amber-500/10 border border-amber-500/25">
                    <span class="text-xs font-bold text-amber-700 dark:text-amber-400">{{ en ? '4th Subject:' : '৪র্থ (ঐচ্ছিক) বিষয়:' }}</span>
                    <select v-model="fourthGrade" class="bg-white dark:bg-[#161a26] border border-amber-500/30 rounded-xl px-2.5 py-1 text-xs font-mono font-bold">
                        <option :value="5.0">A+ (5.00 → +3.00)</option>
                        <option :value="4.0">A (4.00 → +2.00)</option>
                        <option :value="3.5">A- (3.50 → +1.50)</option>
                        <option :value="3.0">B (3.00 → +1.00)</option>
                        <option :value="2.0">C (2.00 → 0.00)</option>
                        <option :value="0.0">F (0.00)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. UNIVERSITY ELIGIBILITY CHECKER -->
        <div v-else-if="mode === 'eligibility'" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                <div>
                    <label class="block text-xs font-bold mb-1">{{ en ? 'Group / Stream' : 'বিভাগ' }}</label>
                    <select v-model="eligGroup" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-xs font-bold">
                        <option value="science">{{ en ? 'Science' : 'বিজ্ঞান শাখা' }}</option>
                        <option value="humanities">{{ en ? 'Humanities' : 'মানবিক শাখা' }}</option>
                        <option value="business">{{ en ? 'Business Studies' : 'ব্যবসায় শিক্ষা শাখা' }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">SSC GPA</label>
                    <input v-model.number="sscGpaInput" type="number" step="0.01" min="1" max="5" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm font-bold" />
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">HSC GPA</label>
                    <input v-model.number="hscGpaInput" type="number" step="0.01" min="1" max="5" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm font-bold" />
                </div>
            </div>

            <div class="text-xs font-bold text-gray-500">
                {{ en ? `Combined Total GPA: ${eligibilityList.total}` : `আপনার মোট জিপিএ (SSC + HSC): ${eligibilityList.total}` }}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div
                    v-for="(u, i) in eligibilityList.checks"
                    :key="i"
                    class="p-4 rounded-2xl border flex items-center justify-between gap-3"
                    :class="u.ok ? 'bg-emerald-500/5 border-emerald-500/30' : 'bg-rose-500/5 border-rose-500/20'"
                >
                    <div>
                        <div class="font-bold text-sm text-gray-900 dark:text-white">{{ u.name }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ en ? 'General Req:' : 'সাধারণ শর্ত:' }} {{ u.req }}</div>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold shrink-0"
                        :class="u.ok ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400' : 'bg-rose-500/15 text-rose-700 dark:text-rose-400'"
                    >
                        <CheckCircleIcon v-if="u.ok" class="w-4 h-4" />
                        <XCircleIcon v-else class="w-4 h-4" />
                        <span>{{ u.ok ? (en ? 'Eligible' : 'সম্ভাব্য যোগ্য') : (en ? 'Short' : 'শর্ত পূরণ নয়') }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. HONOURS CGPA -->
        <div v-else class="space-y-4">
            <div class="p-6 rounded-3xl bg-indigo-500/10 border border-indigo-500/25 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase text-indigo-600 dark:text-indigo-400">{{ en ? 'Weighted CGPA' : 'চূড়ান্ত সিজিপিএ (CGPA)' }}</span>
                    <p class="text-xs text-gray-500 mt-1">{{ en ? 'Out of 4.00 scale' : '৪.০০ স্কেলে ক্রেডিট অনুযায়ী গড়' }}</p>
                </div>
                <div class="text-4xl font-black font-mono text-indigo-600 dark:text-indigo-400">{{ cgpaResult.toFixed(2) }}</div>
            </div>

            <div class="space-y-2.5">
                <div
                    v-for="(sem, idx) in semesters"
                    :key="idx"
                    class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 flex items-center justify-between flex-wrap gap-3"
                >
                    <span class="text-xs font-bold">{{ sem.name }}</span>
                    <div class="flex items-center gap-3 text-xs">
                        <label>GPA: <input v-model.number="sem.gpa" type="number" step="0.01" min="0" max="4" class="w-16 ml-1 px-2 py-1 rounded-lg bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono font-bold text-center" /></label>
                        <label>{{ en ? 'Credits:' : 'ক্রেডিট:' }} <input v-model.number="sem.credits" type="number" min="1" max="50" class="w-16 ml-1 px-2 py-1 rounded-lg bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono font-bold text-center" /></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
