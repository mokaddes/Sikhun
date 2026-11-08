<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';
import {
    AcademicCapIcon,
    SparklesIcon,
    DevicePhoneMobileIcon,
    ArrowTopRightOnSquareIcon,
    ClipboardDocumentCheckIcon,
    CheckCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    recentJobs: Array,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');

const activeTab = ref('hsc'); // 'ssc' | 'hsc' | 'nu'

const form = ref({
    exam: 'hsc',
    year: '2026',
    board: 'dhaka',
    roll: '',
    reg: '',
});

const boards = [
    { key: 'dhaka', label: 'ঢাকা (Dhaka)', code: 'DHA' },
    { key: 'chittagong', label: 'চট্টগ্রাম (Chittagong)', code: 'CHI' },
    { key: 'rajshahi', label: 'রাজশাহী (Rajshahi)', code: 'RAJ' },
    { key: 'comilla', label: 'কুমিল্লা (Comilla)', code: 'COM' },
    { key: 'jessore', label: 'যশোর (Jessore)', code: 'JES' },
    { key: 'barisal', label: 'বরিশাল (Barisal)', code: 'BAR' },
    { key: 'sylhet', label: 'সিলেট (Sylhet)', code: 'SYL' },
    { key: 'dinajpur', label: 'দিনাজপুর (Dinajpur)', code: 'DIN' },
    { key: 'mymensingh', label: 'ময়মনসিংহ (Mymensingh)', code: 'MYM' },
    { key: 'madrasah', label: 'মাদ্রাসা (Madrasah)', code: 'MAD' },
    { key: 'technical', label: 'কারিগরি (Technical)', code: 'TEC' },
];

function checkOfficialServer(serverNum) {
    if (serverNum === 1) {
        window.open('http://www.educationboardresults.gov.bd/', '_blank');
    } else if (serverNum === 2) {
        window.open('https://eboardresults.com/v2/home', '_blank');
    } else {
        window.open('http://www.nu.ac.bd/results/', '_blank');
    }
}
</script>

<template>
    <Head :title="en ? 'Board Exam Result Checker - SSC, HSC & National University Results' : 'বোর্ড পরীক্ষার রেজাল্ট চেকার - SSC, HSC ও জাতীয় বিশ্ববিদ্যালয় ফলাফল'" />
    <PublicLayout>
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-b from-gray-50 via-white to-gray-50 dark:from-[#0b0f19] dark:via-[#111827] dark:to-[var(--bg)] border-b border-gray-200 dark:border-white/5 py-12 md:py-16">
            <div class="pointer-events-none absolute -top-32 left-1/2 -translate-x-1/2 h-96 w-96 rounded-full bg-blue-500/15 blur-[120px]"></div>

            <div class="max-w-7xl mx-auto px-4 md:px-6 relative z-10">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/15 border border-blue-500/30 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4">
                        <SparklesIcon class="w-4 h-4" />
                        {{ en ? 'Official Online & Fast SMS Result Portals' : 'অনলাইন ও এসএমএস রেজাল্ট পোর্টাল' }}
                    </span>
                    <h1 class="font-heading text-3xl md:text-5xl font-black text-gray-900 dark:text-white leading-tight">
                        {{ en ? 'Board Exam' : 'বোর্ড পরীক্ষার' }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-500">{{ en ? 'Result Checker' : 'রেজাল্ট চেকার' }}</span>
                    </h1>
                    <p class="mt-3 text-sm md:text-base text-gray-600 dark:text-[#a2aabc] leading-relaxed">
                        {{ en ? 'Direct official servers and instant SMS guidelines for SSC, HSC, Alim, Dakhil, and National University results.' : 'এসএসসি, এইচএসসি, আলিম, দাখিল ও জাতীয় বিশ্ববিদ্যালয়ের ফলাফল দ্রুত জানার নির্ভুল মাধ্যম। সরাসরি অফিসিয়াল সার্ভার ও এসএমএস গাইডেন্স।' }}
                    </p>
                </div>

                <!-- Tabs -->
                <div class="mt-8 flex flex-wrap gap-3">
                    <button
                        @click="activeTab = 'hsc'"
                        class="px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'hsc' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-lg shadow-[var(--primary)]/20' : 'bg-white dark:bg-[#1a2030] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/20'"
                    >
                        <AcademicCapIcon class="w-4 h-4" />
                        <span>{{ en ? 'HSC / Alim / Equivalent Result' : 'HSC / আলিম / সমমান রেজাল্ট' }}</span>
                    </button>
                    <button
                        @click="activeTab = 'ssc'"
                        class="px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'ssc' ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-lg shadow-[var(--primary)]/20' : 'bg-white dark:bg-[#1a2030] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/20'"
                    >
                        <AcademicCapIcon class="w-4 h-4" />
                        <span>{{ en ? 'SSC / Dakhil Result' : 'SSC / দাখিল রেজাল্ট' }}</span>
                    </button>
                    <button
                        @click="activeTab = 'nu'"
                        class="px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 border"
                        :class="activeTab === 'nu' ? 'bg-emerald-600 text-white border-emerald-500 shadow-lg shadow-emerald-600/20' : 'bg-white dark:bg-[#1a2030] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/5 hover:border-gray-300 dark:hover:border-white/20'"
                    >
                        <AcademicCapIcon class="w-4 h-4" />
                        <span>{{ en ? 'National University (NU) Degree / Honours' : 'জাতীয় বিশ্ববিদ্যালয় (NU) ডিগ্রি / অনার্স' }}</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 md:px-6 py-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left Query / Server Gateways Box -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Fast Official Server Portals -->
                    <div class="rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 md:p-8 shadow-xl space-y-5 text-gray-900 dark:text-white">
                        <div>
                            <h2 class="font-heading text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <AcademicCapIcon class="w-5 h-5 text-[var(--primary)]" />
                                <span>{{ en ? 'Check Result via Official Servers' : 'অফিসিয়াল সার্ভারে রেজাল্ট দেখুন' }}</span>
                            </h2>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ en ? 'Direct portals of Bangladesh Inter-Education Board Coordination Sub-Committee. Server 1 recommended for marksheets.' : 'বাংলাদেশ আন্তঃশিক্ষা বোর্ড এবং শিক্ষা মন্ত্রণালয়ের অফিসিয়াল সার্ভার লিংক। মার্কশিটসহ রেজাল্ট দেখার জন্য সার্ভার-১ সুপারিশকৃত।' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <button
                                @click="checkOfficialServer(2)"
                                class="p-4 rounded-2xl bg-gray-50 dark:bg-gradient-to-br dark:from-[#1d263b] dark:to-[#161d2d] hover:border-[var(--primary)] border border-gray-200 dark:border-white/10 text-left transition group shadow-sm"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">{{ en ? 'Server 1 (Recommended)' : 'সার্ভার ১ (রেকমেন্ডেড)' }}</span>
                                    <ArrowTopRightOnSquareIcon class="w-4 h-4 text-gray-400 group-hover:text-[var(--primary)] transition" />
                                </div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-sm group-hover:text-[var(--primary)] transition">eboardresults.com</h3>
                                <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ en ? 'Fastest gateway for subject-wise marksheets and grade points.' : 'মার্কশিট ও গ্রেড পয়েন্টসহ বিস্তারিত ফলাফল দেখার দ্রুততম সার্ভার।' }}</p>
                            </button>

                            <button
                                @click="checkOfficialServer(1)"
                                class="p-4 rounded-2xl bg-gray-50 dark:bg-gradient-to-br dark:from-[#1d263b] dark:to-[#161d2d] hover:border-[var(--primary)] border border-gray-200 dark:border-white/10 text-left transition group shadow-sm"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-0.5 rounded-full bg-blue-500/15 text-blue-600 dark:text-blue-400 text-[10px] font-bold">{{ en ? 'Server 2 (Central Board)' : 'সার্ভার ২ (মূল বোর্ড)' }}</span>
                                    <ArrowTopRightOnSquareIcon class="w-4 h-4 text-gray-400 group-hover:text-[var(--primary)] transition" />
                                </div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-sm group-hover:text-[var(--primary)] transition">educationboardresults.gov.bd</h3>
                                <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ en ? 'Central database portal of the Ministry of Education.' : 'শিক্ষা মন্ত্রণালয়ের কেন্দ্রীয় অফিসিয়াল ফলাফল ডাটাবেস।' }}</p>
                            </button>
                        </div>

                        <!-- NU Gateway if on NU Tab -->
                        <div v-if="activeTab === 'nu'" class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30">
                            <h4 class="font-bold text-emerald-800 dark:text-emerald-300 text-xs mb-1">{{ en ? 'National University Official Result Portal' : 'জাতীয় বিশ্ববিদ্যালয়ের অফিসিয়াল রেজাল্ট পোর্টাল' }}</h4>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-200/80 mb-3">{{ en ? 'Direct gateway for Honours, Degree Pass, and Masters exams.' : 'অনার্স, ডিগ্রি পাস, প্রিলিমিনারি ও মাস্টার্সের ফলাফল দেখতে ক্লিক করুন।' }}</p>
                            <button
                                @click="checkOfficialServer(3)"
                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5"
                            >
                                <span>{{ en ? 'Go to nu.ac.bd portal' : 'nu.ac.bd রেজাল্ট পোর্টালে যান' }}</span>
                                <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>

                    <!-- SMS Result Checking Guide -->
                    <div class="rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 md:p-8 shadow-xl space-y-4 text-gray-900 dark:text-white">
                        <div class="flex items-center gap-2 text-gray-900 dark:text-white">
                            <DevicePhoneMobileIcon class="w-5 h-5 text-amber-500 dark:text-amber-400" />
                            <h3 class="font-heading font-bold text-base">{{ en ? 'How to Check Result via Mobile SMS' : 'মোবাইলে এসএমএসের (SMS) মাধ্যমে রেজাল্ট জানার নিয়ম' }}</h3>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ en ? 'If online servers are under high load, get results immediately by sending an SMS from any mobile operator:' : 'ইন্টারনেট ট্রাফিক বা সার্ভার ডাউন থাকলে যেকোনো মোবাইল অপারেটরের মেসেজ অপশনে গিয়ে এসএমএস পাঠিয়ে মুহূর্তেই রেজাল্ট পাওয়া যায়:' }}
                        </p>

                        <!-- Code Display Box -->
                        <div class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0e1320] border border-gray-300 dark:border-white/10 font-mono text-xs space-y-3 text-gray-900 dark:text-white">
                            <div v-if="activeTab === 'hsc'">
                                <span class="text-gray-600 dark:text-gray-400 block text-[11px] font-sans">{{ en ? 'HSC Format:' : 'এইচএসসি (HSC) ফরম্যাট:' }}</span>
                                <div class="text-amber-600 dark:text-amber-300 font-bold text-sm select-all">HSC &lt;Space&gt; DHA &lt;Space&gt; [Roll] &lt;Space&gt; 2026</div>
                                <span class="text-gray-600 dark:text-gray-400 block text-[11px] font-sans mt-0.5">{{ en ? 'Send to' : 'পাঠিয়ে দিন' }} <strong>16222</strong> ({{ en ? 'e.g.' : 'যেমন:' }} HSC DHA 123456 2026)</span>
                            </div>
                            <div v-else-if="activeTab === 'ssc'">
                                <span class="text-gray-600 dark:text-gray-400 block text-[11px] font-sans">{{ en ? 'SSC Format:' : 'এসএসসি (SSC) ফরম্যাট:' }}</span>
                                <div class="text-amber-600 dark:text-amber-300 font-bold text-sm select-all">SSC &lt;Space&gt; DHA &lt;Space&gt; [Roll] &lt;Space&gt; 2026</div>
                                <span class="text-gray-600 dark:text-gray-400 block text-[11px] font-sans mt-0.5">{{ en ? 'Send to' : 'পাঠিয়ে দিন' }} <strong>16222</strong></span>
                            </div>
                            <div v-else>
                                <span class="text-gray-600 dark:text-gray-400 block text-[11px] font-sans">{{ en ? 'National University Honours Format:' : 'জাতীয় বিশ্ববিদ্যালয় (NU) অনার্স ফরম্যাট:' }}</span>
                                <div class="text-emerald-600 dark:text-emerald-300 font-bold text-sm select-all">NU &lt;Space&gt; H4 &lt;Space&gt; [Registration]</div>
                                <span class="text-gray-600 dark:text-gray-400 block text-[11px] font-sans mt-0.5">{{ en ? 'Send to' : 'পাঠিয়ে দিন' }} <strong>16222</strong></span>
                            </div>
                        </div>

                        <!-- Board Code Reference -->
                        <div>
                            <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 block mb-2">{{ en ? '3-Letter Education Board Codes:' : 'শিক্ষা বোর্ডের ৩ অক্ষরের কোডসমূহ:' }}</span>
                            <div class="flex flex-wrap gap-1.5 text-[11px]">
                                <span v-for="b in boards" :key="b.key" class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-[#202738] text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-white/5">
                                    {{ b.code }} = {{ en ? b.key.toUpperCase() : b.label.split(' ')[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Grading Scale Table & Admission Promotion -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- Grading Scale Table -->
                    <div class="rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 shadow-xl text-gray-900 dark:text-white">
                        <h3 class="font-heading font-bold text-gray-900 dark:text-white text-base mb-3 flex items-center gap-2">
                            <ClipboardDocumentCheckIcon class="w-5 h-5 text-emerald-500 dark:text-emerald-400" />
                            <span>{{ en ? 'Bangladesh Grading System Scale' : 'বাংলাদেশের গ্রেডিং সিস্টেম স্কেল' }}</span>
                        </h3>

                        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/5 text-xs">
                            <table class="w-full text-left">
                                <thead class="bg-gray-100 dark:bg-[#202738] text-gray-600 dark:text-gray-400 text-[11px] uppercase">
                                    <tr>
                                        <th class="p-2.5">{{ en ? 'Marks Range' : 'নম্বর সীমা' }}</th>
                                        <th class="p-2.5">{{ en ? 'Letter Grade' : 'লেটার গ্রেড' }}</th>
                                        <th class="p-2.5">{{ en ? 'Grade Point' : 'গ্রেড পয়েন্ট (GPA)' }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-white/5 text-gray-700 dark:text-gray-300">
                                    <tr class="bg-white dark:bg-[#171c28]">
                                        <td class="p-2.5 font-bold text-gray-900 dark:text-white">80 - 100</td>
                                        <td class="p-2.5 text-emerald-600 dark:text-emerald-400 font-bold">A+</td>
                                        <td class="p-2.5 font-bold">5.00</td>
                                    </tr>
                                    <tr class="bg-gray-50 dark:bg-[#1c2233]">
                                        <td class="p-2.5">70 - 79</td>
                                        <td class="p-2.5 text-blue-600 dark:text-blue-400 font-bold">A</td>
                                        <td class="p-2.5">4.00</td>
                                    </tr>
                                    <tr class="bg-white dark:bg-[#171c28]">
                                        <td class="p-2.5">60 - 69</td>
                                        <td class="p-2.5 text-teal-600 dark:text-teal-400 font-bold">A-</td>
                                        <td class="p-2.5">3.50</td>
                                    </tr>
                                    <tr class="bg-gray-50 dark:bg-[#1c2233]">
                                        <td class="p-2.5">50 - 59</td>
                                        <td class="p-2.5 text-amber-600 dark:text-amber-400 font-bold">B</td>
                                        <td class="p-2.5">3.00</td>
                                    </tr>
                                    <tr class="bg-white dark:bg-[#171c28]">
                                        <td class="p-2.5">40 - 49</td>
                                        <td class="p-2.5 text-orange-600 dark:text-orange-400 font-bold">C</td>
                                        <td class="p-2.5">2.00</td>
                                    </tr>
                                    <tr class="bg-gray-50 dark:bg-[#1c2233]">
                                        <td class="p-2.5">33 - 39</td>
                                        <td class="p-2.5 text-rose-600 dark:text-rose-400 font-bold">D</td>
                                        <td class="p-2.5">1.00</td>
                                    </tr>
                                    <tr class="bg-white dark:bg-[#171c28]">
                                        <td class="p-2.5 font-bold text-rose-600 dark:text-rose-400">0 - 32</td>
                                        <td class="p-2.5 text-rose-600 font-black">F</td>
                                        <td class="p-2.5 text-rose-600 dark:text-rose-400 font-bold">0.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Admission Preparation Promo Banner -->
                    <div class="rounded-3xl border border-gray-200 dark:border-[var(--primary)]/30 bg-white dark:bg-gradient-to-b dark:from-[#1c1f2d] dark:to-[#121622] p-6 shadow-xl text-gray-900 dark:text-white">
                        <span class="inline-block px-3 py-1 rounded-full bg-[var(--primary)]/15 text-[var(--primary)] text-xs font-bold uppercase tracking-wider mb-2">
                            {{ en ? 'HSC & Admission Special' : 'HSC ও অ্যাডমিশন স্পেশাল' }}
                        </span>
                        <h3 class="font-heading font-bold text-gray-900 dark:text-white text-lg">
                            {{ en ? 'Ready for University Admission after Results?' : 'রেজাল্টের পর ভর্তি পরীক্ষার জন্য প্রস্তুত তো?' }}
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-2 leading-relaxed">
                            {{ en ? 'Prepare with 25 years of solved question banks for BUET, Medical, and DU, plus unlimited realistic AI model tests.' : 'বুয়েট, মেডিকেল ও ঢাকা বিশ্ববিদ্যালয় ভর্তি পরীক্ষার বিগত ২৫ বছরের প্রশ্নব্যাংক এবং আনলিমিটেড AI মডেল টেস্ট নিয়ে তৈরি শিখুন ৩ ও ৬ মাসের অ্যাডমিশন বান্ডেল।' }}
                        </p>

                        <Link
                            href="/plans"
                            class="mt-5 flex items-center justify-center gap-2 w-full px-5 py-3.5 rounded-2xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-xs font-bold shadow-lg shadow-[var(--primary)]/20 transition"
                        >
                            <span>{{ en ? 'View Admission Bundles' : 'অ্যাডমিশন প্রিপারেশন বান্ডেল প্ল্যান দেখুন' }}</span>
                            <span>→</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
