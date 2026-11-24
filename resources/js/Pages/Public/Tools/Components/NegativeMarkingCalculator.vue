<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { downloadBrandedPdf } from '@/utils/studentPdfExport';
import {
    CalculatorIcon,
    ArrowDownTrayIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const preset = ref('0.25');
const totalQuestions = ref(100);
const marksPerCorrect = ref(1);
const negativePerWrong = ref(0.25);
const correctCount = ref(68);
const wrongCount = ref(16);
const targetCutMark = ref(60);
const studentName = ref('');
const isExporting = ref(false);

function applyPreset(key) {
    preset.value = key;
    if (key === '0.25') {
        totalQuestions.value = 100;
        marksPerCorrect.value = 1;
        negativePerWrong.value = 0.25;
        targetCutMark.value = 60;
    } else if (key === '0.50') {
        totalQuestions.value = 200;
        marksPerCorrect.value = 1;
        negativePerWrong.value = 0.5;
        correctCount.value = 130;
        wrongCount.value = 24;
        targetCutMark.value = 115;
    } else if (key === '1.00') {
        totalQuestions.value = 100;
        marksPerCorrect.value = 1;
        negativePerWrong.value = 0.25;
    }
}

const result = computed(() => {
    const totalQ = Math.max(1, Number(totalQuestions.value) || 100);
    const mRight = Math.max(0.1, Number(marksPerCorrect.value) || 1);
    const mWrong = Math.max(0, Number(negativePerWrong.value) || 0);
    const c = Math.min(totalQ, Math.max(0, Number(correctCount.value) || 0));
    const w = Math.min(totalQ - c, Math.max(0, Number(wrongCount.value) || 0));
    const skipped = Math.max(0, totalQ - (c + w));

    const maxMarks = totalQ * mRight;
    const rawMarks = c * mRight;
    const penalty = w * mWrong;
    const netMarks = Math.max(0, +(rawMarks - penalty).toFixed(2));
    const attempted = c + w;
    const accuracy = attempted > 0 ? Math.round((c / attempted) * 100) : 0;
    const percentage = maxMarks > 0 ? +((netMarks / maxMarks) * 100).toFixed(1) : 0;
    const cutDiff = +(netMarks - (Number(targetCutMark.value) || 0)).toFixed(2);

    return {
        totalQ,
        c,
        w,
        skipped,
        attempted,
        maxMarks: +maxMarks.toFixed(2),
        rawMarks: +rawMarks.toFixed(2),
        penalty: +penalty.toFixed(2),
        netMarks,
        accuracy,
        percentage,
        cutDiff,
        isSafe: cutDiff >= 0,
    };
});

async function exportScorePdf() {
    if (isExporting.value) return;
    isExporting.value = true;
    try {
        const r = result.value;
        const bodyHtml = `
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:18px;">
                <div style="padding:14px;border:1px solid #cbd5e1;border-radius:12px;text-align:center;background:#f8fafc;">
                    <div style="font-size:11px;color:#64748b;">প্রাপ্ত নেট নম্বর (Net Score)</div>
                    <div style="font-size:26px;font-weight:800;color:#059669;">${r.netMarks} / ${r.maxMarks}</div>
                </div>
                <div style="padding:14px;border:1px solid #cbd5e1;border-radius:12px;text-align:center;background:#fef2f2;">
                    <div style="font-size:11px;color:#64748b;">নেগেটিভ কর্তন (Penalty)</div>
                    <div style="font-size:26px;font-weight:800;color:#dc2626;">-${r.penalty}</div>
                </div>
                <div style="padding:14px;border:1px solid #cbd5e1;border-radius:12px;text-align:center;background:#eff6ff;">
                    <div style="font-size:11px;color:#64748b;">সঠিকতার হার (Accuracy)</div>
                    <div style="font-size:26px;font-weight:800;color:#2563eb;">${r.accuracy}%</div>
                </div>
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">মোট প্রশ্ন</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;">${r.totalQ} টি</td></tr>
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">উত্তর প্রদান করা হয়েছে</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;">${r.attempted} টি (বাকি: ${r.skipped} টি)</td></tr>
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">সঠিক উত্তর</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;color:#059669;">${r.c} টি (+${r.rawMarks})</td></tr>
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">ভুল উত্তর (প্রতি ভুলে -${negativePerWrong.value})</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;color:#dc2626;">${r.w} টি (-${r.penalty})</td></tr>
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">লক্ষ্য কাট-মার্ক তুলনা (${targetCutMark.value})</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;">${r.isSafe ? `সেফ জোনে আছেন (+${r.cutDiff})` : `লক্ষ্য থেকে ${Math.abs(r.cutDiff)} নম্বর পিছিয়ে`}</td></tr>
            </table>
        `;
        await downloadBrandedPdf({
            title: 'ওএমআর ও নেগেটিভ মার্কিং স্কোর রিপোর্ট',
            subtitle: `নেগেটিভ মার্কিং হার: -${negativePerWrong.value} প্রতি ভুল উত্তরে`,
            studentName: studentName.value.trim(),
            bodyHtml,
            fileName: 'Sikhun_OMR_Score_Report.pdf',
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
                    <CalculatorIcon class="w-6 h-6 text-rose-500" />
                    <span>{{ en ? 'OMR & Negative Marking Score Calculator' : 'ওএমআর ও নেগেটিভ মার্কিং স্কোর ক্যালকুলেটর' }}</span>
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en
                        ? 'Simulate MCQ exam net score, negative penalty deduction, accuracy %, and target cut-mark gap.'
                        : 'যেকোনো MCQ পরীক্ষায় ভুল উত্তরের নেগেটিভ নম্বর বাদ দিয়ে আসল প্রাপ্ত নম্বর, অ্যাকুরেসি এবং কাট-মার্ক সেফ জোন হিসাব করুন।'
                    }}
                </p>
            </div>
            <button
                type="button"
                :disabled="isExporting"
                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold flex items-center gap-1.5 shadow-md transition shrink-0"
                @click="exportScorePdf"
            >
                <ArrowDownTrayIcon class="w-4 h-4" />
                <span>{{ isExporting ? (en ? 'Saving...' : 'তৈরি হচ্ছে...') : (en ? 'Download Score PDF' : 'স্কোর রিপোর্ট PDF') }}</span>
            </button>
        </div>

        <!-- Quick Presets -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-gray-500">{{ en ? 'Quick Standard:' : 'দ্রুত প্রিসেট:' }}</span>
            <button
                v-for="p in [
                    { key: '0.25', label: en ? '-0.25 Penalty (100 MCQ)' : '-০.২৫ নেগেটিভ (১০০ MCQ)' },
                    { key: '0.50', label: en ? '-0.50 Penalty (200 MCQ)' : '-০.৫০ নেগেটিভ (২০০ MCQ)' },
                    { key: 'custom', label: en ? 'Custom Setup' : 'কাস্টম সেটআপ' },
                ]"
                :key="p.key"
                type="button"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition"
                :class="preset === p.key ? 'bg-rose-600 text-white border-rose-600' : 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                @click="applyPreset(p.key)"
            >
                {{ p.label }}
            </button>
        </div>

        <!-- Inputs Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">{{ en ? 'Total MCQs' : 'মোট প্রশ্ন' }}</label>
                <input v-model.number="totalQuestions" type="number" min="1" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 font-mono font-bold text-sm" />
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 mb-1">{{ en ? 'Mark / Right' : 'সঠিকের নম্বর' }}</label>
                <input v-model.number="marksPerCorrect" type="number" step="0.25" min="0.25" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 font-mono font-bold text-sm" />
            </div>
            <div>
                <label class="block text-[11px] font-bold text-rose-600 dark:text-rose-400 mb-1">{{ en ? 'Penalty / Wrong' : 'প্রতি ভুলে কর্তন' }}</label>
                <input v-model.number="negativePerWrong" type="number" step="0.05" min="0" class="w-full px-3 py-2 rounded-xl bg-rose-500/5 border border-rose-500/30 font-mono font-bold text-sm text-rose-600 dark:text-rose-400" />
            </div>
            <div>
                <label class="block text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mb-1">{{ en ? 'Correct Answers' : 'সঠিক উত্তর' }}</label>
                <input v-model.number="correctCount" type="number" min="0" class="w-full px-3 py-2 rounded-xl bg-emerald-500/5 border border-emerald-500/30 font-mono font-bold text-sm text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <label class="block text-[11px] font-bold text-rose-600 dark:text-rose-400 mb-1">{{ en ? 'Wrong Answers' : 'ভুল উত্তর' }}</label>
                <input v-model.number="wrongCount" type="number" min="0" class="w-full px-3 py-2 rounded-xl bg-rose-500/5 border border-rose-500/30 font-mono font-bold text-sm text-rose-600 dark:text-rose-400" />
            </div>
            <div>
                <label class="block text-[11px] font-bold text-amber-600 dark:text-amber-400 mb-1">{{ en ? 'Target Cut-Mark' : 'টার্গেট কাট-মার্ক' }}</label>
                <input v-model.number="targetCutMark" type="number" min="0" class="w-full px-3 py-2 rounded-xl bg-amber-500/5 border border-amber-500/30 font-mono font-bold text-sm text-amber-600 dark:text-amber-400" />
            </div>
        </div>

        <!-- Result Dashboard -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 text-center">
                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 block">{{ en ? 'Final Net Score' : 'প্রাপ্ত নেট নম্বর' }}</span>
                <div class="text-3xl font-black font-mono text-emerald-600 dark:text-emerald-400 mt-1">{{ result.netMarks }}</div>
                <span class="text-[11px] text-gray-500">{{ en ? `Out of ${result.maxMarks} (${result.percentage}%)` : `মোট ${result.maxMarks} এর মধ্যে (${result.percentage}%)` }}</span>
            </div>

            <div class="p-5 rounded-2xl bg-rose-500/10 border border-rose-500/25 text-center">
                <span class="text-xs font-bold text-rose-700 dark:text-rose-400 block">{{ en ? 'Negative Marks Lost' : 'নেগেটিভ মার্ক কাটা গেছে' }}</span>
                <div class="text-3xl font-black font-mono text-rose-600 dark:text-rose-400 mt-1">-{{ result.penalty }}</div>
                <span class="text-[11px] text-gray-500">{{ en ? `${result.w} wrong answers` : `${result.w} টি ভুল উত্তরের জন্য` }}</span>
            </div>

            <div class="p-5 rounded-2xl bg-indigo-500/10 border border-indigo-500/25 text-center">
                <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400 block">{{ en ? 'Strike Accuracy' : 'উত্তরের সঠিকতার হার' }}</span>
                <div class="text-3xl font-black font-mono text-indigo-600 dark:text-indigo-400 mt-1">{{ result.accuracy }}%</div>
                <span class="text-[11px] text-gray-500">{{ en ? `${result.attempted} attempted • ${result.skipped} skipped` : `${result.attempted} টি দাগানো • ${result.skipped} টি ফাঁকা` }}</span>
            </div>

            <div
                class="p-5 rounded-2xl border text-center"
                :class="result.isSafe ? 'bg-amber-500/10 border-amber-500/30' : 'bg-gray-100 dark:bg-white/5 border-gray-200 dark:border-white/10'"
            >
                <span class="text-xs font-bold block">{{ en ? 'Cut-Mark Status' : 'কাট-মার্ক স্ট্যাটাস' }}</span>
                <div class="text-2xl font-black mt-1" :class="result.isSafe ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                    {{ result.isSafe ? (en ? 'SAFE ZONE ✓' : 'সেফ জোন ✓') : (en ? 'BELOW TARGET' : 'লক্ষ্যের নিচে') }}
                </div>
                <span class="text-[11px] text-gray-500">
                    {{ result.isSafe
                        ? (en ? `+${result.cutDiff} above cut-mark` : `কাট-মার্কের চেয়ে +${result.cutDiff} বেশি`)
                        : (en ? `Need +${Math.abs(result.cutDiff)} more marks` : `আরও ${Math.abs(result.cutDiff)} নম্বর প্রয়োজন`)
                    }}
                </span>
            </div>
        </div>

        <!-- Practice CTA -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#0e121d] border border-slate-200 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                <SparklesIcon class="w-5 h-5 text-amber-500 shrink-0" />
                <span>{{ en ? 'Want to reduce negative marking? Practice real past exam MCQs for free!' : 'নেগেটিভ মার্কিং কমাতে চান? বিগত বছরের ৩৮,০০০+ আসল MCQ ফ্রিতে অনুশীলন করুন!' }}</span>
            </div>
            <Link href="/questions" class="px-4 py-2 rounded-xl bg-[var(--primary)] text-white text-xs font-bold shrink-0 hover:opacity-90 transition">
                {{ en ? 'Practice Free MCQs →' : 'ফ্রি প্রশ্নব্যাংক দেখুন →' }}
            </Link>
        </div>
    </div>
</template>
