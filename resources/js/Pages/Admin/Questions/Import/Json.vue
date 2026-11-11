<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import {
    ArrowUpTrayIcon,
    DocumentTextIcon,
    SparklesIcon,
    ArrowLeftIcon,
    CheckCircleIcon,
    ClockIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    FolderArrowDownIcon,
    InformationCircleIcon,
    ArrowPathIcon,
    ServerStackIcon,
    CheckBadgeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: Object,
    serverFiles: Array,
    recentExams: Array,
});

const isDragging = ref(false);
const selectedFileName = ref('');
const activeTab = ref('upload'); // 'upload' | 'server'
const importingServerFile = ref(null);

const form = useForm({
    json_file: null,
    segment: 'auto',
    status: 'active',
});

function handleFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        form.json_file = file;
        selectedFileName.value = file.name;
    }
}

function handleDrop(event) {
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    if (file && (file.type === 'application/json' || file.name.endsWith('.json'))) {
        form.json_file = file;
        selectedFileName.value = file.name;
    }
}

function submitUpload() {
    if (!form.json_file) return;
    form.post('/admin/question-import/json/upload', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('json_file');
            selectedFileName.value = '';
        },
    });
}

function importServerFile(file) {
    if (importingServerFile.value) return;
    importingServerFile.value = file.name;
    router.post('/admin/question-import/json/server-file', {
        filename: file.name,
        segment: 'auto',
        status: 'active',
    }, {
        preserveScroll: true,
        onFinish: () => {
            importingServerFile.value = null;
        },
    });
}
</script>

<template>
    <AdminLayout>
        <Head title="বাল্ক প্রশ্ন জেসন ইমপোর্ট (Bulk Question JSON Import)" />

        <div class="max-w-6xl mx-auto space-y-6 pb-16">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <Link
                        href="/admin/question-bank"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-white mb-2 transition"
                    >
                        <ArrowLeftIcon class="w-4 h-4" />
                        <span>প্রশ্ন ভাণ্ডারে ফিরে যান</span>
                    </Link>
                    <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                        <ArrowUpTrayIcon class="w-7 h-7 text-orange-500" />
                        <span>বাল্ক প্রশ্ন জেসন ইমপোর্ট (Bulk JSON Import)</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-400 mt-1">
                        এক ক্লিকে হাজার হাজার পূর্ববর্তী বছরের পরীক্ষার প্রশ্ন, উত্তর অপশন ও সমাধান যুক্ত করুন।
                    </p>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <div class="card p-4 border-white/5 bg-[var(--surface)]">
                    <div class="text-[11px] text-gray-400 font-semibold uppercase">মোট সংগৃহীত প্রশ্ন</div>
                    <div class="text-2xl font-extrabold text-white mt-1">{{ stats.total_questions?.toLocaleString() || 0 }}</div>
                </div>
                <div class="card p-4 border-white/5 bg-[var(--surface)]">
                    <div class="text-[11px] text-gray-400 font-semibold uppercase">পরীক্ষা / সার্কুলার সংখ্যা</div>
                    <div class="text-2xl font-extrabold text-orange-400 mt-1">{{ stats.distinct_exams?.toLocaleString() || 0 }}</div>
                </div>
                <div class="card p-4 border-white/5 bg-[var(--surface)]">
                    <div class="text-[11px] text-gray-400 font-semibold uppercase">মোট বিষয় সংখ্যা</div>
                    <div class="text-2xl font-extrabold text-blue-400 mt-1">{{ stats.total_subjects || 0 }}</div>
                </div>
                <div class="card p-4 border-white/5 bg-[var(--surface)]">
                    <div class="text-[11px] text-gray-400 font-semibold uppercase">চাকরি ও বিসিএস প্রশ্ন</div>
                    <div class="text-2xl font-extrabold text-emerald-400 mt-1">{{ stats.job_questions?.toLocaleString() || 0 }}</div>
                </div>
                <div class="card p-4 border-white/5 bg-[var(--surface)]">
                    <div class="text-[11px] text-gray-400 font-semibold uppercase">বিশ্ববিদ্যালয় ভর্তি প্রশ্ন</div>
                    <div class="text-2xl font-extrabold text-purple-400 mt-1">{{ stats.admission_questions?.toLocaleString() || 0 }}</div>
                </div>
            </div>

            <!-- Tabs: Upload New JSON vs Server Repository -->
            <div class="flex items-center gap-2 border-b border-white/10 pb-2">
                <button
                    type="button"
                    @click="activeTab = 'upload'"
                    :class="[
                        'px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2',
                        activeTab === 'upload'
                            ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20'
                            : 'bg-white/5 text-gray-400 hover:text-white'
                    ]"
                >
                    <ArrowUpTrayIcon class="w-4 h-4" />
                    <span>নতুন জেসন ফাইল আপলোড</span>
                </button>

                <button
                    type="button"
                    @click="activeTab = 'server'"
                    :class="[
                        'px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2',
                        activeTab === 'server'
                            ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20'
                            : 'bg-white/5 text-gray-400 hover:text-white'
                    ]"
                >
                    <ServerStackIcon class="w-4 h-4" />
                    <span>সার্ভার রিপোজিটরি ফাইল ({{ serverFiles?.length || 0 }})</span>
                </button>
            </div>

            <!-- Tab 1: Upload New JSON File -->
            <div v-if="activeTab === 'upload'" class="card p-6 sm:p-8 space-y-6 border border-white/5 bg-[var(--surface)]">
                <form @submit.prevent="submitUpload" class="space-y-6">
                    <!-- Drop Zone -->
                    <div
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop"
                        class="border-2 border-dashed rounded-3xl p-8 sm:p-12 text-center transition-all cursor-pointer relative"
                        :class="isDragging ? 'border-orange-500 bg-orange-500/10' : 'border-white/15 hover:border-orange-500/50 bg-white/[0.01]'"
                    >
                        <input
                            type="file"
                            accept=".json,application/json"
                            @change="handleFileChange"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        />

                        <div class="flex flex-col items-center justify-center space-y-3 pointer-events-none">
                            <div class="w-16 h-16 rounded-2xl bg-orange-500/10 text-orange-400 flex items-center justify-center">
                                <DocumentTextIcon class="w-8 h-8" />
                            </div>

                            <div v-if="selectedFileName" class="space-y-1">
                                <p class="text-sm font-bold text-white">{{ selectedFileName }}</p>
                                <p class="text-xs text-emerald-400 font-semibold">ফাইল প্রস্তুত — নিচে অপশন বেছে নিয়ে আপলোড চাপুন</p>
                            </div>
                            <div v-else class="space-y-1">
                                <p class="text-sm font-semibold text-white">
                                    জেসন ফাইল টেনে এনে ছেড়ে দিন, অথবা <span class="text-orange-400 underline">ব্রাউজ করুন</span>
                                </p>
                                <p class="text-xs text-gray-400">
                                    সর্বোচ্চ ফাইল সাইজ: ৫০ মেগাবাইট (.json ফরম্যাট)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="form.errors.json_file" class="text-xs text-rose-400">
                        {{ form.errors.json_file }}
                    </div>

                    <!-- Config Options -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-300 uppercase mb-1.5">
                                টার্গেট ক্যাটাগরি / সেগমেন্ট
                            </label>
                            <select v-model="form.segment" class="input w-full text-xs">
                                <option value="auto">অটো-ডিটেক্ট (পরীক্ষার নাম অনুযায়ী স্বয়ংক্রিয়)</option>
                                <option value="job">চাকরি ও বিসিএস (Job & BCS Prep)</option>
                                <option value="admission">বিশ্ববিদ্যালয় ভর্তি (University Admission)</option>
                                <option value="hsc">এইচএসসি (HSC Academic)</option>
                                <option value="ssc">এসএসসি (SSC Academic)</option>
                            </select>
                            <span class="text-[10px] text-gray-400 mt-1 block">
                                অটো-ডিটেক্ট মোডে পরীক্ষার শিরোনামের কীওয়ার্ড দেখে সেগমেন্ট নির্ধারিত হয়।
                            </span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-300 uppercase mb-1.5">
                                ডিফল্ট স্ট্যাটাস
                            </label>
                            <select v-model="form.status" class="input w-full text-xs">
                                <option value="active">সরাসরি সক্রিয় করুন (Active - লাইভ শিক্ষার্থীদের জন্য)</option>
                                <option value="draft">ড্রাফট হিসেবে রাখুন (Draft - পরবর্তীতে রিভিউ ও অনুমোদন)</option>
                            </select>
                            <span class="text-[10px] text-gray-400 mt-1 block">
                                সক্রিয় করলে শিক্ষার্থীরা তাৎক্ষণিকভাবে এক্সাম ও প্র্যাকটিসে প্রশ্নগুলো পাবে।
                            </span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/5">
                        <button
                            type="submit"
                            :disabled="form.processing || !form.json_file"
                            class="btn btn-primary px-8 py-3 text-xs sm:text-sm font-bold flex items-center gap-2"
                        >
                            <ArrowPathIcon v-if="form.processing" class="w-4 h-4 animate-spin" />
                            <ArrowUpTrayIcon v-else class="w-4 h-4" />
                            <span>{{ form.processing ? 'ইমপোর্ট প্রসেসিং হচ্ছে...' : 'ইমপোর্ট শুরু করুন' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 2: Server Repository Files (qsn-json/) -->
            <div v-else-if="activeTab === 'server'" class="space-y-4">
                <div class="card p-5 border border-white/5 bg-[var(--surface)] flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-white flex items-center gap-2">
                            <FolderArrowDownIcon class="w-5 h-5 text-orange-400" />
                            <span>সার্ভার ডিরেক্টরি রিপোজিটরি (`qsn-json/`)</span>
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">
                            সার্ভারে সংরক্ষিত মাস্টার প্রশ্ন ফাইলসমূহ। যেকোনো ফাইলে ক্লিক করে এক ক্লিকে সম্পূর্ণ প্রশ্নভাণ্ডার ডাটাবেজে সিঙ্ক করুন।
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                        v-for="file in serverFiles"
                        :key="file.name"
                        class="card p-5 border border-white/5 bg-[var(--surface)] rounded-2xl flex flex-col justify-between hover:border-orange-500/40 transition group"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-500/10 text-orange-400 border border-orange-500/20">
                                    {{ file.size_mb }} MB
                                </span>
                                <span class="text-[10px] text-gray-400 font-mono">JSON Master</span>
                            </div>

                            <h3 class="font-bold text-white text-sm line-clamp-2 group-hover:text-orange-400 transition mb-3">
                                {{ file.name }}
                            </h3>

                            <div class="space-y-1.5 text-xs text-gray-300 p-3 rounded-xl bg-white/[0.02] border border-white/5 mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400 text-[11px]">অন্তর্ভুক্ত পরীক্ষা:</span>
                                    <strong class="text-white">{{ file.exams_count ? file.exams_count + ' টি' : 'একাধিক' }}</strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400 text-[11px]">আনুমানিক প্রশ্ন:</span>
                                    <strong class="text-emerald-400">{{ file.questions_count ? file.questions_count.toLocaleString() + ' টি' : 'সহস্রাধিক' }}</strong>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="importServerFile(file)"
                            :disabled="importingServerFile === file.name"
                            class="w-full btn btn-secondary text-xs font-bold py-2.5 flex items-center justify-center gap-2 hover:border-orange-500/50"
                        >
                            <ArrowPathIcon v-if="importingServerFile === file.name" class="w-4 h-4 animate-spin text-orange-400" />
                            <CheckBadgeIcon v-else class="w-4 h-4 text-emerald-400" />
                            <span>{{ importingServerFile === file.name ? 'ইমপোর্ট হচ্ছে...' : '১-ক্লিকে সিঙ্ক / ইমপোর্ট' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- JSON Schema Format Guide -->
            <div class="card p-6 border border-white/5 bg-[var(--surface)] space-y-4">
                <div class="flex items-center gap-2 text-white font-bold text-sm">
                    <InformationCircleIcon class="w-5 h-5 text-blue-400" />
                    <span>সমর্থিত জেসন ফরম্যাট ও স্বয়ংক্রিয় প্রসেসিং গাইড (JSON Schema Guide)</span>
                </div>
                <p class="text-xs text-gray-400 leading-relaxed">
                    সিস্টেমটি স্বয়ংক্রিয়ভাবে পরীক্ষার শিরোনাম থেকে <strong>সাল (Year)</strong> ও <strong>সেট নম্বর</strong> শনাক্ত করে। 
                    অপশনের শুরুতে <code class="text-orange-400">A)</code>, <code class="text-orange-400">B)</code> অথবা বাংলা <code class="text-orange-400">ক)</code>, <code class="text-orange-400">খ)</code> থাকলে তা স্বয়ংক্রিয়ভাবে স্ট্রাকচার্ড কী-তে কনভার্ট হয়ে যায়।
                    ডুপ্লিকেট প্রশ্নসমূহ স্বয়ংক্রিয়ভাবে স্কিপ করা হয় যাতে ডেটাবেজে অতিরিক্ত রিডানড্যান্ট ডেটা তৈরি না হয়।
                </p>

                <div class="p-4 rounded-2xl bg-black/40 border border-white/5 font-mono text-[11px] text-gray-300 overflow-x-auto leading-relaxed">
<pre>{
  "title": "Chorcha Crawled Question Papers Master Export",
  "exams": [
    {
      "title": "50th BCS Preli Question Paper",
      "series": "bcs-preli-question-bank",
      "questions": [
        {
          "index": 1,
          "question": "জলবায়ু পরিবর্তন সংক্রান্ত জাতিসংঘ ফ্রেমওয়ার্ক কনভেনশন...",
          "options": ["A) কোপেনহেগেন", "B) প্যারিস", "C) বার্লিন", "D) বন"],
          "answer": "C) বার্লিন",
          "explanation": "সঠিক উত্তর: (C) বার্লিন। বিষয়: সাধারণ জ্ঞান।",
          "subject": "সাধারণ জ্ঞান"
        }
      ]
    }
  ]
}</pre>
                </div>
            </div>

            <!-- Recently Imported Exams -->
            <div v-if="recentExams?.length" class="card p-6 border border-white/5 bg-[var(--surface)] space-y-4">
                <h2 class="text-sm font-bold text-white flex items-center gap-2">
                    <ClockIcon class="w-4 h-4 text-orange-400" />
                    <span>প্রশ্নভাণ্ডারের সাম্প্রতিক পরীক্ষা ও সার্কুলার সমূহ</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    <div
                        v-for="(exam, idx) in recentExams"
                        :key="idx"
                        class="p-3 rounded-xl bg-white/[0.02] border border-white/5 text-xs flex items-center justify-between gap-2"
                    >
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-white block truncate" :title="exam.exam_name">{{ exam.exam_name }}</span>
                            <span class="text-[10px] text-gray-400 mt-0.5 block">ট্যাগ: {{ exam.origin_tag || 'STANDARD' }}</span>
                        </div>
                        <span v-if="exam.year" class="px-2 py-0.5 rounded-md bg-white/5 text-orange-400 text-[10px] font-bold shrink-0">
                            {{ exam.year }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
