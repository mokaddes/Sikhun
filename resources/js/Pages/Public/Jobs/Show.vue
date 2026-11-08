<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';
import {
    BriefcaseIcon,
    BuildingOfficeIcon,
    CalendarDaysIcon,
    BanknotesIcon,
    UserGroupIcon,
    MapPinIcon,
    AcademicCapIcon,
    CalculatorIcon,
    ArrowTopRightOnSquareIcon,
    ShareIcon,
    CheckCircleIcon,
    ClockIcon,
    ArrowLeftIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    job: Object,
    relatedJobs: Array,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');
const copied = ref(false);

function copyLink() {
    navigator.clipboard.writeText(window.location.href);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
}

function shareFacebook() {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`, '_blank');
}

function shareWhatsApp() {
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(props.job.title + ' ' + window.location.href)}`, '_blank');
}

function formatDate(d) {
    if (!d) return en.value ? 'Soon' : 'শীঘ্রই';
    return new Date(d).toLocaleDateString(en.value ? 'en-US' : 'bn-BD', { year: 'numeric', month: 'long', day: 'numeric' });
}

function getDaysRemaining(d) {
    if (!d) return null;
    const diff = new Date(d).getTime() - new Date().getTime();
    const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
    return days > 0 ? days : 0;
}
</script>

<template>
    <Head :title="`${job.title} - ${en ? 'Job Circular 2026' : 'চাকরির সার্কুলার ২০২৬'}`" />
    <PublicLayout>
        <!-- Breadcrumbs -->
        <div class="border-b border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-[#0e1320] py-3 text-xs text-gray-500 dark:text-gray-400">
            <div class="max-w-7xl mx-auto px-4 md:px-6 flex items-center gap-2">
                <Link href="/" class="hover:text-[var(--primary)] transition">{{ en ? 'Home' : 'হোম' }}</Link>
                <span>/</span>
                <Link href="/jobs" class="hover:text-[var(--primary)] transition">{{ en ? 'Jobs' : 'চাকরির খবর' }}</Link>
                <span>/</span>
                <span class="text-gray-900 dark:text-gray-200 truncate max-w-sm">{{ job.title }}</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 md:px-6 py-8 md:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left Main Circular Column (8 Cols) -->
                <main class="lg:col-span-8 space-y-6">
                    <!-- Header Card -->
                    <div class="rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 md:p-8 shadow-xl text-gray-900 dark:text-white">
                        <div class="flex items-center gap-2 text-xs font-semibold text-[var(--primary)] mb-2">
                            <BuildingOfficeIcon class="w-4 h-4 shrink-0" />
                            <span>{{ job.organization }}</span>
                        </div>

                        <h1 class="font-heading text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white leading-snug">
                            {{ job.title }}
                        </h1>

                        <!-- Status & Deadline Strip -->
                        <div class="mt-4 flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100 dark:border-white/5 text-xs">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 font-bold">
                                <ClockIcon class="w-3.5 h-3.5" />
                                <span>{{ en ? 'Application Deadline:' : 'আবেদনের শেষ সময়:' }} {{ formatDate(job.deadline) }}</span>
                                <span v-if="getDaysRemaining(job.deadline)" class="ml-1 text-[10px] px-1.5 py-0.2 rounded-full bg-rose-500/20 font-mono">
                                    ({{ en ? `${getDaysRemaining(job.deadline)} days left` : `বাকি ${getDaysRemaining(job.deadline)} দিন` }})
                                </span>
                            </span>

                            <span v-if="job.published_date" class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <CalendarDaysIcon class="w-3.5 h-3.5 text-gray-400" />
                                <span>{{ en ? 'Published Date:' : 'প্রকাশের তারিখ:' }} {{ formatDate(job.published_date) }}</span>
                            </span>
                        </div>

                        <!-- Top Apply Button Row -->
                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a
                                v-if="job.apply_url"
                                :href="job.apply_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex-1 sm:flex-initial px-6 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-bold shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 transition"
                            >
                                <span>{{ en ? 'Apply Online' : 'সরাসরি আবেদন করুন (Apply Online)' }}</span>
                                <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                            </a>

                            <!-- Share Buttons -->
                            <div class="flex items-center gap-2">
                                <button
                                    @click="shareFacebook"
                                    type="button"
                                    class="p-3 rounded-xl bg-gray-100 dark:bg-[#202738] hover:bg-blue-600 hover:text-white text-gray-700 dark:text-white transition text-xs"
                                    :title="en ? 'Share on Facebook' : 'ফেসবুকে শেয়ার করুন'"
                                >
                                    <span class="font-bold">f</span>
                                </button>
                                <button
                                    @click="shareWhatsApp"
                                    type="button"
                                    class="p-3 rounded-xl bg-gray-100 dark:bg-[#202738] hover:bg-emerald-600 hover:text-white text-gray-700 dark:text-white transition text-xs"
                                    :title="en ? 'Share on WhatsApp' : 'হোয়াটসঅ্যাপে শেয়ার করুন'"
                                >
                                    <span>WA</span>
                                </button>
                                <button
                                    @click="copyLink"
                                    type="button"
                                    class="px-3 py-3 rounded-xl bg-gray-100 dark:bg-[#202738] hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-white transition text-xs flex items-center gap-1"
                                    :title="en ? 'Copy Link' : 'লিংক কপি করুন'"
                                >
                                    <ShareIcon class="w-4 h-4" />
                                    <span>{{ copied ? (en ? 'Copied!' : 'কপি হয়েছে!') : (en ? 'Copy' : 'কপি') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Table (Like Students.bd) -->
                    <div class="rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 shadow-xl overflow-hidden text-gray-900 dark:text-white">
                        <h2 class="font-heading text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <BriefcaseIcon class="w-5 h-5 text-[var(--primary)]" />
                            <span>{{ en ? 'Circular Summary (At a Glance)' : 'সার্কুলারের সংক্ষিপ্ত তথ্য (At a Glance)' }}</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#202738]/80 border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 dark:text-gray-400 block mb-0.5">{{ en ? 'Organization Name:' : 'প্রতিষ্ঠানের নাম:' }}</span>
                                <strong class="text-gray-900 dark:text-white text-sm">{{ job.organization }}</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#202738]/80 border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 dark:text-gray-400 block mb-0.5">{{ en ? 'Job Title / Position:' : 'পদের নাম:' }}</span>
                                <strong class="text-gray-900 dark:text-white text-sm">{{ job.title }}</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#202738]/80 border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 dark:text-gray-400 block mb-0.5">{{ en ? 'Total Vacancies:' : 'মোট পদসংখ্যা:' }}</span>
                                <strong class="text-emerald-600 dark:text-emerald-400 text-sm">{{ job.vacancy || (en ? 'See Circular' : 'সার্কুলার দেখুন') }}</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#202738]/80 border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 dark:text-gray-400 block mb-0.5">{{ en ? 'Salary Scale:' : 'বেতন স্কেল:' }}</span>
                                <strong class="text-gray-900 dark:text-white text-sm">{{ job.salary || (en ? 'Per National Pay Scale' : 'জাতীয় বেতন স্কেল অনুযায়ী') }}</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#202738]/80 border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 dark:text-gray-400 block mb-0.5">{{ en ? 'Educational Requirement:' : 'শিক্ষাগত যোগ্যতা:' }}</span>
                                <strong class="text-gray-900 dark:text-white text-sm">{{ job.education_requirement || (en ? 'Graduate / Equivalent' : 'স্নাতক / সমমান') }}</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#202738]/80 border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 dark:text-gray-400 block mb-0.5">{{ en ? 'Job Location:' : 'কর্মস্থল:' }}</span>
                                <strong class="text-gray-900 dark:text-white text-sm">{{ job.location || (en ? 'Bangladesh' : 'ঢাকা, বাংলাদেশ') }}</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#202738]/80 border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 dark:text-gray-400 block mb-0.5">{{ en ? 'Deadline:' : 'আবেদনের শেষ তারিখ:' }}</span>
                                <strong class="text-rose-600 dark:text-rose-400 text-sm">{{ formatDate(job.deadline) }}</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#202738]/80 border border-gray-200 dark:border-white/5">
                                <span class="text-gray-500 dark:text-gray-400 block mb-0.5">{{ en ? 'Official Source:' : 'অফিসিয়াল ওয়েবসাইট:' }}</span>
                                <strong class="text-[var(--primary)] text-sm truncate block">{{ job.source_name || 'Official Portal' }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Full Job Description & Instructions -->
                    <div class="rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 md:p-8 shadow-xl text-gray-900 dark:text-white">
                        <h2 class="font-heading text-lg font-bold text-gray-900 dark:text-white mb-4">
                            {{ en ? 'Full Circular & Application Instructions' : 'বিস্তারিত বিজ্ঞপ্তি ও আবেদনের নিয়মাবলী' }}
                        </h2>

                        <div class="prose dark:prose-invert max-w-none text-sm text-gray-700 dark:text-gray-300 leading-relaxed space-y-4 whitespace-pre-line">
                            {{ job.description }}
                        </div>

                        <!-- Application Instructions -->
                        <div v-if="job.application_instructions" class="mt-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-900 dark:text-amber-200">
                            <strong class="block font-bold text-amber-700 dark:text-amber-300 mb-1">{{ en ? 'How to Apply Guidelines:' : 'আবেদন করার নির্দেশিকা:' }}</strong>
                            <p>{{ job.application_instructions }}</p>
                        </div>

                        <!-- Big Apply Banner at bottom -->
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <h3 class="font-heading font-bold text-gray-900 dark:text-white text-base">{{ en ? 'Ready to Apply?' : 'আবেদন করতে চান?' }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ en ? 'Fill out the form directly on the official recruitment portal.' : 'অফিসিয়াল নিয়োগ পোর্টালে সরাসরি ফরম পূরণ করুন।' }}</p>
                            </div>
                            <a
                                v-if="job.apply_url"
                                :href="job.apply_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-full sm:w-auto px-7 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-bold shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2"
                            >
                                <span>{{ en ? 'Apply Online Now' : 'অনলাইনে আবেদন করুন' }}</span>
                                <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                </main>

                <!-- Right Sidebar Column (4 Cols) -->
                <aside class="lg:col-span-4 space-y-6">
                    <!-- Quick Age Calculator Box -->
                    <div class="rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 shadow-xl text-gray-900 dark:text-white">
                        <div class="flex items-center gap-2 text-xs font-bold text-amber-500 dark:text-amber-400 mb-2">
                            <CalculatorIcon class="w-5 h-5" />
                            <span>{{ en ? 'Age Eligibility Tool' : 'বয়স যাচাই টুল' }}</span>
                        </div>
                        <h3 class="font-heading font-bold text-gray-900 dark:text-white text-base">
                            {{ en ? 'Are You Eligible for this Circular?' : 'এই সার্কুলারে আপনার বয়স কত?' }}
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 leading-relaxed">
                            {{ en ? 'Calculate if your age is within 30 years (general) or 32 years (quota) relative to circular deadline.' : 'বিসিএস বা সরকারি চাকরির জন্য আপনার বয়স ৩০ বছরের মধ্যে আছে কিনা এখনই হিসাব করে নিন।' }}
                        </p>
                        <Link
                            href="/calculators"
                            class="mt-4 flex w-full items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-gray-100 dark:bg-[#202738] hover:bg-gray-200 dark:hover:bg-[#283248] text-gray-900 dark:text-white text-xs font-bold border border-gray-200 dark:border-white/10 transition"
                        >
                            <span>{{ en ? 'Open Age Calculator' : 'বয়স ক্যালকুলেটর খুলুন' }}</span>
                            <span>→</span>
                        </Link>
                    </div>

                    <!-- Job Preparation Bundle Promo -->
                    <div class="rounded-3xl border border-gray-200 dark:border-[var(--primary)]/30 bg-white dark:bg-gradient-to-b dark:from-[#1b1f2d] dark:to-[#121622] p-6 shadow-xl text-gray-900 dark:text-white">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-[var(--primary)]/15 text-[var(--primary)] text-[10px] font-bold uppercase tracking-wider mb-2">
                            Sikhun Job Bundle
                        </span>
                        <h3 class="font-heading font-bold text-gray-900 dark:text-white text-base">
                            {{ en ? 'Complete AI Exam Preparation' : 'চাকরি পরীক্ষার সম্পূর্ণ এআই প্রস্তুতি' }}
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 leading-relaxed">
                            {{ en ? 'Prepare with 20 years of BCS & Bank question banks, unlimited daily model tests, and written feedback.' : 'বিসিএস, ব্যাংক ও সরকারি চাকরি পরীক্ষার বিগত ২০ বছরের প্রশ্নব্যাংক এবং ডেইলি মডেল টেস্ট দিয়ে প্রস্তুতি নিন।' }}
                        </p>
                        <ul class="my-4 space-y-1.5 text-xs text-gray-700 dark:text-gray-300">
                            <li class="flex items-center gap-2">
                                <CheckCircleIcon class="w-4 h-4 text-emerald-500 shrink-0" />
                                <span>{{ en ? 'BCS Preliminary Solved Question Bank' : 'বিসিএস প্রিলিমিনারি প্রশ্নব্যাংক' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <CheckCircleIcon class="w-4 h-4 text-emerald-500 shrink-0" />
                                <span>{{ en ? 'Bank Math & English Analytical Solver' : 'ব্যাংক জবস ম্যাথ ও ইংলিশ সলভার' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <CheckCircleIcon class="w-4 h-4 text-emerald-500 shrink-0" />
                                <span>{{ en ? 'Written Paper AI Feedback & Essay Review' : 'লিখিত খাতার এআই মূল্যায়ন' }}</span>
                            </li>
                        </ul>
                        <Link
                            href="/plans"
                            class="flex w-full items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-xs font-bold transition shadow-lg shadow-[var(--primary)]/20"
                        >
                            <span>{{ en ? 'View Job Bundle Plans' : 'বান্ডেল প্ল্যান দেখুন' }}</span>
                            <span>→</span>
                        </Link>
                    </div>

                    <!-- Related Jobs -->
                    <div v-if="relatedJobs?.length" class="rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] p-6 shadow-xl text-gray-900 dark:text-white">
                        <h3 class="font-heading font-bold text-gray-900 dark:text-white text-base mb-4">
                            {{ en ? 'Related Job Openings' : 'সম্পর্কিত অন্যান্য নিয়োগ বিজ্ঞপ্তি' }}
                        </h3>
                        <div class="space-y-3">
                            <Link
                                v-for="rel in relatedJobs"
                                :key="rel.id"
                                :href="`/jobs/${rel.slug}`"
                                class="block p-3 rounded-2xl bg-gray-50 dark:bg-[#202738]/60 hover:bg-gray-100 dark:hover:bg-[#202738] border border-gray-200 dark:border-white/5 transition group"
                            >
                                <span class="text-[11px] font-semibold text-[var(--primary)] block truncate">{{ rel.organization }}</span>
                                <h4 class="text-xs font-bold text-gray-900 dark:text-white group-hover:text-[var(--primary)] transition line-clamp-2 mt-0.5">
                                    {{ rel.title }}
                                </h4>
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 block mt-1">{{ en ? 'Ends:' : 'শেষ:' }} {{ formatDate(rel.deadline) }}</span>
                            </Link>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </PublicLayout>
</template>
