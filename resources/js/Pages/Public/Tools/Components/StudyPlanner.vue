<script setup>
import { ref, computed, onMounted } from 'vue';
import {
    CalendarDaysIcon,
    PlusIcon,
    TrashIcon,
    PrinterIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    SparklesIcon,
    ShieldCheckIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const studentName = ref('');
const examTarget = ref('HSC 2026 / BCS Preparation');
const currentDate = new Date().toLocaleDateString('bn-BD', { year: 'numeric', month: 'long', day: 'numeric' });

const slots = ref([
    { id: 1, time: '06:30 AM - 08:30 AM', subject: 'পদার্থবিজ্ঞান / গণিত', topic: 'অধ্যায় ৩: থিওরি ও গাণিতিক সমস্যা', hours: 2, done: false },
    { id: 2, time: '09:30 AM - 11:30 AM', subject: 'রসায়ন / ইংরেজি', topic: 'গুরুত্বপূর্ণ সূত্র ও ভোকাবুলারি রিভিশন', hours: 2, done: false },
    { id: 3, time: '03:00 PM - 05:00 PM', subject: 'সাধারণ জ্ঞান / বায়োলজি', topic: 'বিগত বছরের বোর্ড প্রশ্ন সমাধান', hours: 2, done: false },
    { id: 4, time: '07:30 PM - 10:00 PM', subject: 'মডেল টেস্ট ও অনুশীলন', topic: '১ সেট পূর্ণাঙ্গ প্রশ্ন সমাধান ও সেলফ টেস্ট', hours: 2.5, done: false },
    { id: 5, time: '10:30 PM - 11:30 PM', subject: 'দিনের রিভিশন ও পরদিনের পরিকল্পনা', topic: 'ভুল হওয়া প্রশ্নগুলো ডায়েরিতে নোট করা', hours: 1, done: false },
]);

const newSlot = ref({
    time: '06:00 PM - 07:00 PM',
    subject: '',
    topic: '',
    hours: 1,
});

onMounted(() => {
    const saved = localStorage.getItem('sikhun_study_routine');
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed)) slots.value = parsed;
        } catch (e) {}
    }
    const savedName = localStorage.getItem('sikhun_student_name');
    if (savedName) studentName.value = savedName;
    const savedTarget = localStorage.getItem('sikhun_exam_target');
    if (savedTarget) examTarget.value = savedTarget;
});

function saveRoutine() {
    localStorage.setItem('sikhun_study_routine', JSON.stringify(slots.value));
    localStorage.setItem('sikhun_student_name', studentName.value);
    localStorage.setItem('sikhun_exam_target', examTarget.value);
}

const totalPlannedHours = computed(() => {
    return slots.value.reduce((acc, s) => acc + (Number(s.hours) || 0), 0);
});

const completedHours = computed(() => {
    return slots.value
        .filter(s => s.done)
        .reduce((acc, s) => acc + (Number(s.hours) || 0), 0);
});

function addSlot() {
    if (!newSlot.value.subject.trim()) return;
    slots.value.push({
        id: Date.now(),
        time: newSlot.value.time,
        subject: newSlot.value.subject,
        topic: newSlot.value.topic || 'সাধারণ পড়াশোনা',
        hours: Number(newSlot.value.hours) || 1,
        done: false,
    });
    newSlot.value.subject = '';
    newSlot.value.topic = '';
    saveRoutine();
}

function removeSlot(id) {
    slots.value = slots.value.filter(s => s.id !== id);
    saveRoutine();
}

function clearAllSlots() {
    if (confirm(props.en ? 'Clear all slots and start with a blank routine?' : 'সবগুলো স্লট মুছে একদম খালি রুটিন দিয়ে শুরু করতে চান?')) {
        slots.value = [];
        saveRoutine();
    }
}

function toggleDone(slot) {
    slot.done = !slot.done;
    saveRoutine();
}

function printRoutine() {
    const printWindow = window.open('', '_blank', 'width=850,height=950');
    if (!printWindow) {
        window.print();
        return;
    }
    const rowsHtml = slots.value.map((s, idx) => `
        <tr style="${s.done ? 'background-color: #f0fdf4;' : ''}">
            <td style="padding: 10px; border: 1px solid #e5e7eb; text-align: center; font-weight: bold;">${idx + 1}</td>
            <td style="padding: 10px; border: 1px solid #e5e7eb; font-family: monospace;">${s.time}</td>
            <td style="padding: 10px; border: 1px solid #e5e7eb; font-weight: bold;">${s.subject}</td>
            <td style="padding: 10px; border: 1px solid #e5e7eb;">${s.topic}</td>
            <td style="padding: 10px; border: 1px solid #e5e7eb; text-align: center; font-family: monospace;">${s.hours}h</td>
            <td style="padding: 10px; border: 1px solid #e5e7eb; text-align: center;">${s.done ? '✓ Done' : '○ Pending'}</td>
        </tr>
    `).join('');

    const htmlContent = `
        <!DOCTYPE html>
        <html lang="bn">
        <head>
            <meta charset="utf-8">
            <title>Daily Study Routine - ${studentName.value || 'Sikhun'}</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600;700&display=swap" rel="stylesheet">
            <style>
                @page { size: A4 portrait; margin: 15mm; }
                body { font-family: 'Anek Bangla', 'Segoe UI', system-ui, sans-serif; color: #111827; background: #fff; margin: 0; padding: 15px; }
                .header-box { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #4f46e5; padding-bottom: 12px; margin-bottom: 16px; }
                h1 { margin: 0; font-size: 20pt; color: #1e1b4b; }
                .meta-strip { margin: 12px 0 16px; display: flex; gap: 20px; font-size: 11pt; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10.5pt; }
                th { background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 10px; text-align: left; font-weight: bold; }
                .stats-footer { margin-top: 20px; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: 11pt; font-weight: bold; }
                .brand { font-size: 9pt; color: #94a3b8; text-align: right; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="header-box">
                <div>
                    <h1>📅 দৈনিক পড়ার রুটিন ও স্টাডি প্ল্যানার</h1>
                    <div style="font-size: 11pt; color: #4f46e5; font-weight: bold; margin-top: 4px;">${examTarget.value || 'Exam Preparation'}</div>
                </div>
                <div style="text-align: right; font-size: 10pt; color: #64748b;">
                    <div>তারিখ: ${currentDate}</div>
                    ${studentName.value ? `<div style="font-weight: bold; color: #0f172a; margin-top: 2px;">শিক্ষার্থী: ${studentName.value}</div>` : ''}
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">নং</th>
                        <th style="width: 160px;">সময়সূচি</th>
                        <th style="width: 140px;">বিষয়</th>
                        <th>অধ্যায় ও পড়ার লক্ষ্য</th>
                        <th style="width: 70px; text-align: center;">ঘণ্টা</th>
                        <th style="width: 80px; text-align: center;">স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody>
                    ${rowsHtml}
                </tbody>
            </table>

            <div class="stats-footer">
                <div>মোট পড়ার লক্ষ্য: ${totalPlannedHours.value} ঘণ্টা</div>
                <div>সম্পন্ন হয়েছে: ${completedHours.value} ঘণ্টা</div>
                <div>অগ্রগতি: ${totalPlannedHours.value > 0 ? Math.round((completedHours.value / totalPlannedHours.value) * 100) : 0}%</div>
            </div>

            <div class="brand">Sikhun.com — বাংলাদেশের ১ম AI-চালিত শিক্ষা প্ল্যাটফর্ম</div>

            <script>
                window.onload = function() {
                    window.focus();
                    window.print();
                };
            <\/script>
        </body>
        </html>
    `;
    printWindow.document.open();
    printWindow.document.write(htmlContent);
    printWindow.document.close();
}

function loadTemplate(type) {
    if (type === 'hsc') {
        slots.value = [
            { id: 1, time: '06:00 AM - 08:30 AM', subject: 'উচ্চতর গণিত', topic: 'ফাংশন ও ইন্টিগ্রেশন প্র্যাকটিস', hours: 2.5, done: false },
            { id: 2, time: '10:00 AM - 12:30 PM', subject: 'পদার্থবিজ্ঞান', topic: 'তড়িৎ ও আলো অধ্যায়ের টেস্ট পেপার', hours: 2.5, done: false },
            { id: 3, time: '03:30 PM - 05:00 PM', subject: 'রসায়ন', topic: 'জৈব রসায়ন রিঅ্যাকশন নোট ও মুখস্থ', hours: 1.5, done: false },
            { id: 4, time: '07:30 PM - 10:00 PM', subject: 'ইংরেজি ও বাংলা', topic: 'রাইটিং পার্ট ও গ্রামার অনুশীলন', hours: 2.5, done: false },
            { id: 5, time: '10:30 PM - 11:30 PM', subject: 'ডেইলি মডেল টেস্ট', topic: '১টি পূর্ণাঙ্গ সিকিউ ও এমসিকিউ টেস্ট', hours: 1, done: false },
        ];
        examTarget.value = 'HSC Board Exam Mission A+';
    } else if (type === 'bcs') {
        slots.value = [
            { id: 1, time: '06:30 AM - 08:30 AM', subject: 'বাংলাদেশ বিষয়াবলি', topic: 'মুক্তিযুদ্ধ ও সংবিধান অংশের রিভিশন', hours: 2, done: false },
            { id: 2, time: '09:30 AM - 11:30 AM', subject: 'ইংরেজি সাহিত্য ও গ্রামার', topic: 'প্রিভিয়াস ইয়ার কোয়েশ্চন সলভ', hours: 2, done: false },
            { id: 3, time: '03:00 PM - 05:00 PM', subject: 'গাণিতিক যুক্তি ও মানসিক দক্ষতা', topic: 'শর্টকাট নিয়ম ও ম্যাথ সমাধান', hours: 2, done: false },
            { id: 4, time: '07:00 PM - 09:30 PM', subject: 'আন্তর্জাতিক বিষয়াবলি ও বিজ্ঞান', topic: 'সাম্প্রতিক তথ্য ও আইসিটি রিভিশন', hours: 2.5, done: false },
            { id: 5, time: '10:00 PM - 11:30 PM', subject: '২০০ নম্বরের পূর্ণ প্রিলি টেস্ট', topic: 'সময় ধরে ওএমআর টেস্ট অনুশীলন', hours: 1.5, done: false },
        ];
        examTarget.value = 'BCS Preliminary & Govt Job Mission';
    } else if (type === 'admission') {
        slots.value = [
            { id: 1, time: '06:00 AM - 09:00 AM', subject: 'কোর বিষয় (মেজর)', topic: 'প্রশ্নব্যাংক এনালাইসিস ও টাইপ অনুযায়ী সমাধান', hours: 3, done: false },
            { id: 2, time: '10:30 AM - 01:00 PM', subject: 'ফর্মুলা ও শর্টকাট মেমরি', topic: 'ক্যালকুলেটর ছাড়া দ্রুত হিসাবের কৌশল', hours: 2.5, done: false },
            { id: 3, time: '03:30 PM - 05:30 PM', subject: 'সেকেন্ড সাবজেক্ট প্র্যাকটিস', topic: 'টপিকভিত্তিক ৫০টি জটিল এমসিকিউ সলভ', hours: 2, done: false },
            { id: 4, time: '07:30 PM - 10:30 PM', subject: 'বিশ্ববিদ্যালয় প্রশ্নব্যাংক', topic: 'ঢাবি/বুয়েট/মেডিকেল বিগত ১০ বছরের প্রশ্ন', hours: 3, done: false },
        ];
        examTarget.value = 'University Admission Mission';
    }
    saveRoutine();
}
</script>

<template>
    <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
        <!-- Header Strip -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-5">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5">
                    <CalendarDaysIcon class="w-6 h-6 text-indigo-500" />
                    {{ en ? 'Daily Study Routine & Planner' : 'দৈনিক পড়ার রুটিন ও প্ল্যানার মেকার' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Build your personal daily study routine with target hours, track progress, and print/save clean A4 PDF.' : 'প্রতিদিনের পড়ার সময় ভাগ করুন, সম্পন্ন বিষয় টিক দিন এবং ১-ক্লিকে চমৎকার A4 শিটে রুটিন প্রিন্ট বা PDF ডাউনলোড করুন।' }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap">
                <button
                    type="button"
                    @click="printRoutine"
                    class="btn btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 shadow-md shadow-indigo-500/25"
                >
                    <PrinterIcon class="w-4 h-4" />
                    <span>{{ en ? 'Print / Save as PDF' : 'প্রিন্ট / PDF ডাউনলোড' }}</span>
                </button>
            </div>
        </div>

        <!-- Privacy & LocalStorage Guarantee Banner -->
        <div class="mt-4 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-800 dark:text-emerald-300 flex items-start sm:items-center gap-2.5">
            <ShieldCheckIcon class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
            <div class="leading-relaxed">
                <strong>{{ en ? '100% Private to your Device:' : '🔒 ১০০% ব্যক্তিগত ও সুরক্ষিত:' }}</strong>
                {{ en ? 'This routine is stored strictly inside your browser (Local Storage). It is never sent to the server or visible to any other user.' : 'এই রুটিনটি সম্পূর্ণ আপনার নিজস্ব ডিভাইসের ব্রাউজার মেমরিতে (Local Storage) সংরক্ষিত। এটি কোনো সার্ভারে জমা হয় না এবং অন্য কোনো শিক্ষার্থী বা ব্যক্তি কখনোই আপনার রুটিন দেখতে পাবে না।' }}
            </div>
        </div>

        <!-- Preset Templates Selector & Clear Button -->
        <div class="mt-5 flex items-center justify-between gap-2 flex-wrap text-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-gray-500 font-semibold">{{ en ? 'Sample Templates:' : 'নমুনা টেমপ্লেট:' }}</span>
                <button
                    type="button"
                    @click="loadTemplate('hsc')"
                    class="px-3 py-1 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 font-bold hover:bg-indigo-500/20 transition"
                >
                    🎓 {{ en ? 'HSC/SSC Board' : 'এইচএসসি/এসএসসি' }}
                </button>
                <button
                    type="button"
                    @click="loadTemplate('admission')"
                    class="px-3 py-1 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 font-bold hover:bg-purple-500/20 transition"
                >
                    🏛️ {{ en ? 'Varsity Admission' : 'ভার্সিটি ভর্তি' }}
                </button>
                <button
                    type="button"
                    @click="loadTemplate('bcs')"
                    class="px-3 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold hover:bg-emerald-500/20 transition"
                >
                    💼 {{ en ? 'BCS & Govt Job' : 'বিসিএস ও চাকরি' }}
                </button>
            </div>

            <!-- Clear / Start Blank -->
            <button
                type="button"
                @click="clearAllSlots"
                class="px-3 py-1 rounded-xl text-rose-500 hover:text-rose-700 border border-rose-500/20 hover:bg-rose-500/10 transition font-semibold"
            >
                🗑️ {{ en ? 'Start with Blank Routine' : 'খালি রুটিন দিয়ে শুরু করুন' }}
            </button>
        </div>

        <!-- Student Meta Inputs -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ en ? "Student Name (Optional):" : 'শিক্ষার্থীর নাম (ঐচ্ছিক):' }}
                </label>
                <input
                    v-model="studentName"
                    type="text"
                    @input="saveRoutine"
                    :placeholder="en ? 'e.g. Tanvir Ahmed' : 'যেমন: তানভীর আহমেদ'"
                    class="w-full px-4 py-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-xs sm:text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ en ? 'Exam Target / Goal:' : 'পরীক্ষার লক্ষ্য বা টার্গেট:' }}
                </label>
                <input
                    v-model="examTarget"
                    type="text"
                    @input="saveRoutine"
                    :placeholder="en ? 'e.g. HSC 2026 GPA 5.00' : 'যেমন: এইচএসসি ২০২৬ জিপিএ ৫.০০ / ঢাবি ভর্তি'"
                    class="w-full px-4 py-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-xs sm:text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="p-3.5 rounded-2xl bg-indigo-500/5 border border-indigo-500/15 text-center">
                <span class="text-[10px] text-gray-500 dark:text-gray-400 font-bold block">{{ en ? 'PLANNED HOURS' : 'মোট পড়ার লক্ষ্য' }}</span>
                <strong class="font-mono text-xl text-indigo-600 dark:text-indigo-400">{{ totalPlannedHours }} {{ en ? 'hrs' : 'ঘণ্টা' }}</strong>
            </div>
            <div class="p-3.5 rounded-2xl bg-emerald-500/5 border border-emerald-500/15 text-center">
                <span class="text-[10px] text-gray-500 dark:text-gray-400 font-bold block">{{ en ? 'COMPLETED' : 'সম্পন্ন হয়েছে' }}</span>
                <strong class="font-mono text-xl text-emerald-600 dark:text-emerald-400">{{ completedHours }} {{ en ? 'hrs' : 'ঘণ্টা' }}</strong>
            </div>
            <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-center col-span-2 sm:col-span-1">
                <span class="text-[10px] text-gray-500 dark:text-gray-400 font-bold block">{{ en ? 'PROGRESS' : 'অগ্রগতি' }}</span>
                <strong class="font-mono text-xl text-amber-500">
                    {{ totalPlannedHours > 0 ? Math.round((completedHours / totalPlannedHours) * 100) : 0 }}%
                </strong>
            </div>
        </div>

        <!-- Schedule Table -->
        <div class="mt-6 overflow-x-auto rounded-2xl border border-gray-200 dark:border-white/10">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100 dark:bg-[#0e121d] border-b border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 font-bold">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">✓</th>
                        <th class="py-3 px-3">{{ en ? 'Time Slot' : 'সময়' }}</th>
                        <th class="py-3 px-3">{{ en ? 'Subject / Paper' : 'বিষয়' }}</th>
                        <th class="py-3 px-3">{{ en ? 'Topic & Target' : 'অধ্যায় ও পড়ার লক্ষ্য' }}</th>
                        <th class="py-3 px-3 w-20 text-center">{{ en ? 'Hours' : 'ঘণ্টা' }}</th>
                        <th class="py-3 px-3 w-16 text-center">{{ en ? 'Action' : 'মুছুন' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    <tr
                        v-for="slot in slots"
                        :key="slot.id"
                        class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition"
                        :class="slot.done ? 'bg-emerald-500/5 line-through opacity-75' : ''"
                    >
                        <td class="py-3 px-3 text-center">
                            <input
                                type="checkbox"
                                :checked="slot.done"
                                @change="toggleDone(slot)"
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                            />
                        </td>
                        <td class="py-3 px-3 font-mono font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                            {{ slot.time }}
                        </td>
                        <td class="py-3 px-3 font-semibold text-gray-900 dark:text-white">
                            {{ slot.subject }}
                        </td>
                        <td class="py-3 px-3 text-gray-600 dark:text-gray-300">
                            {{ slot.topic }}
                        </td>
                        <td class="py-3 px-3 text-center font-mono font-bold text-indigo-600 dark:text-indigo-400">
                            {{ slot.hours }}h
                        </td>
                        <td class="py-3 px-3 text-center">
                            <button
                                type="button"
                                @click="removeSlot(slot.id)"
                                class="text-rose-500 hover:text-rose-700 p-1 transition"
                            >
                                <TrashIcon class="w-4 h-4" />
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!slots.length">
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            {{ en ? 'Your routine is currently blank. Add your study slots below or load a template!' : 'আপনার রুটিন বর্তমানে সম্পূর্ণ খালি। নিচের ফর্ম দিয়ে নিজের পছন্দমতো পড়ার সময় যুক্ত করুন।' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add New Slot Form -->
        <div class="mt-6 p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
            <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-1.5">
                <PlusIcon class="w-4 h-4 text-indigo-500" />
                {{ en ? 'Add New Study Slot' : 'নতুন পড়ার স্লট যুক্ত করুন' }}
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <label class="block text-gray-500 mb-1">{{ en ? 'Time:' : 'সময়:' }}</label>
                    <input
                        v-model="newSlot.time"
                        type="text"
                        placeholder="07:00 PM - 08:30 PM"
                        class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white"
                    />
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">{{ en ? 'Subject:' : 'বিষয়:' }}</label>
                    <input
                        v-model="newSlot.subject"
                        type="text"
                        :placeholder="en ? 'e.g. ICT' : 'যেমন: আইসিটি / গণিত'"
                        class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white"
                    />
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">{{ en ? 'Topic / Chapter:' : 'অধ্যায় বা টার্গেট:' }}</label>
                    <input
                        v-model="newSlot.topic"
                        type="text"
                        :placeholder="en ? 'e.g. Chapter 4' : 'যেমন: অধ্যায় ৪ ও এমসিকিউ'"
                        class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white"
                    />
                </div>
                <div class="flex items-end gap-2">
                    <div class="w-20">
                        <label class="block text-gray-500 mb-1">{{ en ? 'Hours:' : 'ঘণ্টা:' }}</label>
                        <input
                            v-model.number="newSlot.hours"
                            type="number"
                            step="0.5"
                            min="0.5"
                            max="6"
                            class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-center font-mono text-gray-900 dark:text-white"
                        />
                    </div>
                    <button
                        type="button"
                        @click="addSlot"
                        class="btn btn-primary text-xs py-2 px-4 flex-1 h-[38px] flex items-center justify-center gap-1"
                    >
                        <PlusIcon class="w-4 h-4" />
                        <span>{{ en ? 'Add' : 'যুক্ত করুন' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
