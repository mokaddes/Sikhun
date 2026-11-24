<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    stats: Object,
    collections: Array,
    subjects: Array,
    seo: Object,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');
const activeTab = ref('all');

const filteredCollections = computed(() => {
    if (activeTab.value === 'all') return props.collections || [];
    return (props.collections || []).filter((c) => c.segment === activeTab.value);
});
</script>

<template>
    <SeoHead v-if="seo" :seo="seo" />
    <PublicLayout>
        <section class="relative overflow-hidden border-b border-slate-200 dark:border-white/5 bg-gradient-to-br from-slate-50 via-white to-amber-50/40 dark:from-[#0b0f19] dark:via-[#111827] dark:to-[#171c28] py-12 md:py-16">
            <div class="max-w-7xl mx-auto px-4 md:px-6">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 text-xs font-bold mb-4">
                        <span>{{ en ? 'FREE PUBLIC QUESTION BANK' : 'ফ্রি পাবলিক প্রশ্নব্যাংক ও সমাধান' }}</span>
                        <span>•</span>
                        <span>{{ (stats?.total || 0).toLocaleString() }}+ MCQ</span>
                    </span>
                    <h1 class="font-heading text-3xl md:text-5xl font-extrabold text-slate-900 dark:text-white leading-tight">
                        {{ en ? 'Bangladesh Exam Question Bank & Solutions' : 'বিসিএস, বিশ্ববিদ্যালয় ভর্তি ও সরকারি চাকরির প্রশ্নব্যাংক' }}
                    </h1>
                    <p class="mt-4 text-slate-600 dark:text-slate-300 text-base md:text-lg leading-relaxed">
                        {{ en
                            ? 'Practice verified past MCQs from DU, RU, GST, BUET, BCS Preliminary, Primary Assistant Teacher (DPE), Bangladesh Bank, and NTRCA with instant answers and explanations.'
                            : 'ঢাকা বিশ্ববিদ্যালয়, রাজশাহী বিশ্ববিদ্যালয়, গুচ্ছ, বুয়েট, বিসিএস প্রিলিমিনারি, প্রাথমিক শিক্ষক নিয়োগ, বাংলাদেশ ব্যাংক ও NTRCA-এর বিগত বছরের আসল MCQ প্রশ্ন ও ব্যাখ্যা সরাসরি অনলাইনে ফ্রি প্র্যাকটিস করুন।'
                        }}
                    </p>
                </div>

                <!-- Live Stats Strip -->
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white/90 dark:bg-white/5 p-4">
                        <div class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white">{{ (stats?.total || 0).toLocaleString() }}+</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ en ? 'Total Verified MCQs' : 'মোট ভেরিফাইড MCQ' }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white/90 dark:bg-white/5 p-4">
                        <div class="text-2xl md:text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ (stats?.job || 0).toLocaleString() }}+</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ en ? 'BCS & Job MCQs' : 'বিসিএস ও চাকরি প্রশ্ন' }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white/90 dark:bg-white/5 p-4">
                        <div class="text-2xl md:text-3xl font-black text-amber-600 dark:text-amber-400">{{ (stats?.admission || 0).toLocaleString() }}+</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ en ? 'University Admission MCQs' : 'বিশ্ববিদ্যালয় ভর্তি প্রশ্ন' }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white/90 dark:bg-white/5 p-4">
                        <div class="text-2xl md:text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ subjects?.length || 0 }}+</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ en ? 'Subjects Covered' : 'বিষয়ভিত্তিক ক্যাটাগরি' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Exam Collections -->
        <section class="max-w-7xl mx-auto px-4 md:px-6 py-10 md:py-14">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-white">
                        {{ en ? 'Exam & Institution Question Banks' : 'পরীক্ষা ও প্রতিষ্ঠান ভিত্তিক প্রশ্নব্যাংক' }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        {{ en ? 'Choose an exam below to practice past questions for free.' : 'যেকোনো পরীক্ষা বেছে নিয়ে বিগত বছরের প্রশ্ন ও ব্যাখ্যা প্র্যাকটিস শুরু করুন।' }}
                    </p>
                </div>
                <div class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 p-1 rounded-xl border border-slate-200 dark:border-white/10 text-xs font-semibold">
                    <button
                        v-for="tab in [
                            { key: 'all', label: en ? 'All Exams' : 'সকল পরীক্ষা' },
                            { key: 'job', label: en ? 'BCS & Job' : 'বিসিএস ও চাকরি' },
                            { key: 'admission', label: en ? 'University Admission' : 'বিশ্ববিদ্যালয় ভর্তি' },
                        ]"
                        :key="tab.key"
                        :class="[
                            'px-3 py-1.5 rounded-lg transition',
                            activeTab === tab.key
                                ? 'bg-[var(--primary)] text-white shadow-xs'
                                : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'
                        ]"
                        @click="activeTab = tab.key"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <Link
                    v-for="col in filteredCollections"
                    :key="col.slug"
                    :href="`/questions/${col.slug}`"
                    class="group rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#171c28] p-6 shadow-xs hover:shadow-xl hover:border-[var(--primary)]/50 transition-all flex flex-col justify-between"
                >
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span
                                :class="[
                                    'px-2.5 py-0.5 rounded-full text-[11px] font-bold',
                                    col.segment === 'job'
                                        ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20'
                                        : 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20'
                                ]"
                            >
                                {{ col.segment === 'job' ? (en ? 'Job & BCS' : 'বিসিএস ও চাকরি') : (en ? 'Admission' : 'বিশ্ববিদ্যালয় ভর্তি') }}
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-300">
                                {{ (col.count || 0).toLocaleString() }} MCQ
                            </span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-slate-900 dark:text-white group-hover:text-[var(--primary)] transition">
                            {{ en ? col.title_en : col.title_bn }}
                        </h3>
                        <p class="mt-2 text-xs text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
                            {{ col.description_bn }}
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs font-bold text-[var(--primary)]">
                        <span>{{ en ? 'Practice Free MCQs' : 'ফ্রি প্র্যাকটিস ও সমাধান দেখুন' }}</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Subject-wise Hub -->
        <section v-if="subjects && subjects.length" class="border-t border-slate-200 dark:border-white/5 bg-slate-50/70 dark:bg-[#0e1320] py-12">
            <div class="max-w-7xl mx-auto px-4 md:px-6">
                <h2 class="font-heading text-2xl font-bold text-slate-900 dark:text-white mb-2">
                    {{ en ? 'Subject-Wise MCQ Question Bank' : 'বিষয়ভিত্তিক MCQ প্রশ্নব্যাংক' }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                    {{ en ? 'Master weak subjects by practicing topic-sorted past questions.' : 'নিজের দুর্বল বিষয়গুলোতে দক্ষতা বাড়াতে বিষয়ভিত্তিক বিগত বছরের প্রশ্ন প্র্যাকটিস করুন।' }}
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    <Link
                        v-for="sub in subjects"
                        :key="sub.id"
                        :href="`/questions/subject/${sub.slug}`"
                        class="rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#171c28] p-4 hover:border-[var(--primary)]/50 hover:shadow-md transition"
                    >
                        <div class="font-bold text-sm text-slate-900 dark:text-white truncate">
                            {{ en ? (sub.name_en || sub.name_bn) : (sub.name_bn || sub.name_en) }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-mono">
                            {{ (sub.questions_count || 0).toLocaleString() }} {{ en ? 'MCQs' : 'টি প্রশ্ন' }}
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Conversion CTA -->
        <section class="max-w-7xl mx-auto px-4 md:px-6 py-12">
            <div class="rounded-3xl bg-gradient-to-r from-[#1e293b] to-[#0f172a] dark:from-[#171c28] dark:to-[#0b0f19] border border-white/10 p-8 md:p-10 text-white flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="max-w-2xl">
                    <h2 class="font-heading text-2xl md:text-3xl font-extrabold">
                        {{ en ? 'Want Timed Mock Tests, Negative Marking & AI Tutor?' : 'টাইমড মক টেস্ট, নেগেটিভ মার্কিং ও AI ব্যাখ্যা চান?' }}
                    </h2>
                    <p class="mt-2 text-slate-300 text-sm md:text-base">
                        {{ en
                            ? 'Create a free Sikhun account to unlock full custom exams, Mistake Review ("আমার ভুলসমূহ"), and AI-powered explanations for all 38,499+ questions.'
                            : 'মাত্র ৩০ সেকেন্ডে ফ্রি অ্যাকাউন্ট খুলুন এবং ৩৮,৪৯৯+ প্রশ্নের ওপর কাস্টম মডেল টেস্ট, "আমার ভুলসমূহ" ট্র্যাকার এবং ২৪/৭ AI টিউটরের সহায়তা নিন।'
                        }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <Link href="/register" class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-sm transition shadow-lg">
                        {{ en ? 'Create Free Account' : 'ফ্রি অ্যাকাউন্ট খুলুন' }}
                    </Link>
                    <Link href="/question-bank" class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/15 text-white font-bold text-sm border border-white/15 transition">
                        {{ en ? 'Full Practice Mode' : 'ফুল এক্সাম মোড' }}
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
