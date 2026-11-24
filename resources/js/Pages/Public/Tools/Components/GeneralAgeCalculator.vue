<script setup>
import { ref, computed } from 'vue';
import { downloadBrandedPdf } from '@/utils/studentPdfExport';
import {
    CalendarDaysIcon,
    ClipboardDocumentIcon,
    CheckIcon,
    ArrowDownTrayIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const dob = ref('2002-01-01');
const targetDate = ref(new Date().toISOString().split('T')[0]);
const personName = ref('');
const copied = ref(false);
const isExporting = ref(false);

const weekDaysBn = ['রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার', 'শনিবার'];
const weekDaysEn = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

const ageResult = computed(() => {
    if (!dob.value || !targetDate.value) return null;
    const start = new Date(dob.value);
    const end = new Date(targetDate.value);
    if (isNaN(start.getTime()) || isNaN(end.getTime()) || end < start) return null;

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
    const totalHours = totalDays * 24;

    const nextBday = new Date(end.getFullYear(), start.getMonth(), start.getDate());
    if (nextBday < end) nextBday.setFullYear(end.getFullYear() + 1);
    const daysToNextBday = Math.ceil((nextBday.getTime() - end.getTime()) / (1000 * 60 * 60 * 24));

    const dayIndex = start.getDay();

    return {
        years: y,
        months: m,
        days: d,
        totalMonths,
        totalWeeks,
        totalDays,
        totalHours,
        daysToNextBday,
        bornDayBn: weekDaysBn[dayIndex],
        bornDayEn: weekDaysEn[dayIndex],
    };
});

function copySummary() {
    if (!ageResult.value) return;
    const r = ageResult.value;
    const txt = props.en
        ? `Age: ${r.years} years, ${r.months} months, ${r.days} days (Total ${r.totalDays} days) as of ${targetDate.value}`
        : `বয়স: ${r.years} বছর, ${r.months} মাস, ${r.days} দিন (মোট ${r.totalDays} দিন) — হিসাবের তারিখ: ${targetDate.value}`;
    navigator.clipboard.writeText(txt);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 1800);
}

async function downloadAgePdf() {
    if (!ageResult.value || isExporting.value) return;
    isExporting.value = true;
    try {
        const r = ageResult.value;
        const bodyHtml = `
            <div style="padding:18px;border:1px solid #f59e0b;background:#fffbeb;border-radius:14px;text-align:center;margin-bottom:16px;">
                <div style="font-size:12px;color:#b45309;font-weight:700;">নির্ভুল বয়স (Exact Age)</div>
                <div style="font-size:28px;font-weight:800;color:#0f172a;margin-top:4px;">${r.years} বছর ${r.months} মাস ${r.days} দিন</div>
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">জন্মতারিখ (Date of Birth)</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;">${dob.value} (${r.bornDayBn})</td></tr>
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">হিসাবের তারিখ (Reference Date)</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;">${targetDate.value}</td></tr>
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">মোট মাস</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;">${r.totalMonths.toLocaleString()} মাস</td></tr>
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">মোট সপ্তাহ</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;">${r.totalWeeks.toLocaleString()} সপ্তাহ</td></tr>
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">মোট দিন</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;">${r.totalDays.toLocaleString()} দিন</td></tr>
                <tr><td style="padding:9px;border:1px solid #cbd5e1;">পরবর্তী জন্মদিন</td><td style="padding:9px;border:1px solid #cbd5e1;font-weight:700;">আর ${r.daysToNextBday} দিন বাকি</td></tr>
            </table>
        `;
        await downloadBrandedPdf({
            title: 'বয়স হিসাব রিপোর্ট (Age Calculation Summary)',
            subtitle: `জন্মতারিখ: ${dob.value} থেকে ${targetDate.value} পর্যন্ত`,
            studentName: personName.value.trim(),
            bodyHtml,
            fileName: 'Sikhun_Age_Report.pdf',
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
                    <CalendarDaysIcon class="w-6 h-6 text-amber-500" />
                    <span>{{ en ? 'Age Calculator' : 'বয়স ক্যালকুলেটর (Age Calculator)' }}</span>
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en
                        ? 'Calculate exact age in years, months, days, weeks, born weekday, and next birthday countdown.'
                        : 'জন্মতারিখ থেকে আজকের দিন বা যেকোনো নির্দিষ্ট তারিখ পর্যন্ত নির্ভুল বছর, মাস, দিন, জন্মবার ও পরবর্তী জন্মদিনের হিসাব করুন।'
                    }}
                </p>
            </div>
            <button
                v-if="ageResult"
                type="button"
                :disabled="isExporting"
                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold flex items-center gap-1.5 shadow-sm shrink-0"
                @click="downloadAgePdf"
            >
                <ArrowDownTrayIcon class="w-4 h-4" />
                <span>{{ isExporting ? (en ? 'Saving...' : 'তৈরি হচ্ছে...') : (en ? 'Download PDF' : 'বয়স রিপোর্ট PDF') }}</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold mb-1.5">{{ en ? 'Date of Birth:' : 'জন্মতারিখ:' }}</label>
                <input v-model="dob" type="date" class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-sm font-semibold" />
            </div>
            <div>
                <label class="block text-xs font-bold mb-1.5">{{ en ? 'Age at the Date of:' : 'যে তারিখ পর্যন্ত হিসাব করবেন:' }}</label>
                <input v-model="targetDate" type="date" class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-sm font-semibold" />
            </div>
            <div>
                <label class="block text-xs font-bold mb-1.5">{{ en ? 'Name (Optional for PDF):' : 'নাম (PDF-এর জন্য ঐচ্ছিক):' }}</label>
                <input v-model="personName" type="text" :placeholder="en ? 'Enter name...' : 'আপনার নাম...'" class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-sm" />
            </div>
        </div>

        <div v-if="ageResult" class="p-6 rounded-3xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/25 space-y-5">
            <div class="flex items-center justify-between border-b border-amber-500/20 pb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                    {{ en ? `Born on ${ageResult.bornDayEn}` : `জন্মবার: ${ageResult.bornDayBn}` }}
                </span>
                <button type="button" class="px-3 py-1 rounded-xl border border-amber-500/30 text-xs font-bold text-amber-700 dark:text-amber-400 flex items-center gap-1" @click="copySummary">
                    <CheckIcon v-if="copied" class="w-3.5 h-3.5 text-emerald-500" />
                    <ClipboardDocumentIcon v-else class="w-3.5 h-3.5" />
                    <span>{{ copied ? (en ? 'Copied' : 'কপি হয়েছে') : (en ? 'Copy' : 'কপি করুন') }}</span>
                </button>
            </div>

            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="p-4 rounded-2xl bg-white dark:bg-[#0e121d] border border-amber-500/20">
                    <span class="font-heading text-3xl md:text-4xl font-extrabold text-amber-500">{{ ageResult.years }}</span>
                    <span class="text-xs text-gray-500 block font-bold mt-1">{{ en ? 'Years' : 'বছর' }}</span>
                </div>
                <div class="p-4 rounded-2xl bg-white dark:bg-[#0e121d] border border-amber-500/20">
                    <span class="font-heading text-3xl md:text-4xl font-extrabold text-amber-500">{{ ageResult.months }}</span>
                    <span class="text-xs text-gray-500 block font-bold mt-1">{{ en ? 'Months' : 'মাস' }}</span>
                </div>
                <div class="p-4 rounded-2xl bg-white dark:bg-[#0e121d] border border-amber-500/20">
                    <span class="font-heading text-3xl md:text-4xl font-extrabold text-amber-500">{{ ageResult.days }}</span>
                    <span class="text-xs text-gray-500 block font-bold mt-1">{{ en ? 'Days' : 'দিন' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-center pt-2">
                <div class="p-3 rounded-xl bg-white dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <span class="text-gray-500 block">{{ en ? 'Total Months' : 'মোট মাস' }}</span>
                    <strong class="font-mono text-sm">{{ ageResult.totalMonths.toLocaleString() }}</strong>
                </div>
                <div class="p-3 rounded-xl bg-white dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <span class="text-gray-500 block">{{ en ? 'Total Weeks' : 'মোট সপ্তাহ' }}</span>
                    <strong class="font-mono text-sm">{{ ageResult.totalWeeks.toLocaleString() }}</strong>
                </div>
                <div class="p-3 rounded-xl bg-white dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <span class="text-gray-500 block">{{ en ? 'Total Days' : 'মোট দিন' }}</span>
                    <strong class="font-mono text-sm">{{ ageResult.totalDays.toLocaleString() }}</strong>
                </div>
                <div class="p-3 rounded-xl bg-white dark:bg-[#0e121d] border border-gray-200 dark:border-white/5">
                    <span class="text-gray-500 block">{{ en ? 'Next Birthday' : 'পরবর্তী জন্মদিন' }}</span>
                    <strong class="font-mono text-sm text-emerald-500">{{ ageResult.daysToNextBday }} {{ en ? 'days' : 'দিন পর' }}</strong>
                </div>
            </div>
        </div>
    </div>
</template>
