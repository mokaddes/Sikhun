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
            if (Array.isArray(parsed) && parsed.length) slots.value = parsed;
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

function toggleDone(slot) {
    slot.done = !slot.done;
    saveRoutine();
}

function printRoutine() {
    window.print();
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
                    {{ en ? 'Daily Study Routine & Planner Maker' : 'দৈনিক পড়ার রুটিন ও প্ল্যানার মেকার' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Plan your study hours, track completed topics, and print/save your personalized routine as PDF.' : 'প্রতিদিনের পড়ার সময় ভাগ করুন, সম্পন্ন বিষয় টিক দিন এবং ১-ক্লিকে রুটিন প্রিন্ট বা PDF ডাউনলোড করুন।' }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap">
                <button
                    type="button"
                    @click="printRoutine"
                    class="btn btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5"
                >
                    <PrinterIcon class="w-4 h-4" />
                    <span>{{ en ? 'Print / Save as PDF' : 'প্রিন্ট / PDF ডাউনলোড' }}</span>
                </button>
            </div>
        </div>

        <!-- Preset Templates Selector -->
        <div class="mt-5 flex items-center gap-2 flex-wrap text-xs">
            <span class="text-gray-500 font-semibold">{{ en ? 'Quick Templates:' : 'প্রস্তুত রুটিন লোড করুন:' }}</span>
            <button
                type="button"
                @click="loadTemplate('hsc')"
                class="px-3 py-1 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 font-bold hover:bg-indigo-500/20 transition"
            >
                🎓 {{ en ? 'HSC/SSC Board Routine' : 'এইচএসসি/এসএসসি বোর্ড রুটিন' }}
            </button>
            <button
                type="button"
                @click="loadTemplate('admission')"
                class="px-3 py-1 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 font-bold hover:bg-purple-500/20 transition"
            >
                🏛️ {{ en ? 'University Admission' : 'ভার্সিটি ভর্তি পরীক্ষা' }}
            </button>
            <button
                type="button"
                @click="loadTemplate('bcs')"
                class="px-3 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold hover:bg-emerald-500/20 transition"
            >
                💼 {{ en ? 'BCS & Govt Job Routine' : 'বিসিএস ও সরকারি চাকরি' }}
            </button>
        </div>

        <!-- Student Meta Inputs -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ en ? "Student Name (Optional):" : 'শিক্ষার্থীর নাম:' }}
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
        <div class="mt-6 overflow-x-auto rounded-2xl border border-gray-200 dark:border-white/10 print:border-black">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100 dark:bg-[#0e121d] border-b border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 font-bold">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">✓</th>
                        <th class="py-3 px-3">{{ en ? 'Time Slot' : 'সময়' }}</th>
                        <th class="py-3 px-3">{{ en ? 'Subject / Paper' : 'বিষয়' }}</th>
                        <th class="py-3 px-3">{{ en ? 'Topic & Target' : 'অধ্যায় ও পড়ার লক্ষ্য' }}</th>
                        <th class="py-3 px-3 w-20 text-center">{{ en ? 'Hours' : 'ঘণ্টা' }}</th>
                        <th class="py-3 px-3 w-16 text-center print:hidden">{{ en ? 'Action' : 'মুছুন' }}</th>
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
                        <td class="py-3 px-3 text-center print:hidden">
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
                        <td colspan="6" class="py-6 text-center text-gray-500">
                            {{ en ? 'No study slots added yet. Add one below or load a template!' : 'কোনো পড়ার স্লট যুক্ত করা নেই। নিচে নতুন স্লট যুক্ত করুন অথবা উপরের টেমপ্লেট লোড করুন।' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add New Slot Form -->
        <div class="mt-6 p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 print:hidden">
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
