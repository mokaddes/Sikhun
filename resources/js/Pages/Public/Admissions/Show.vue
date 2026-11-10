<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    notice: Object,
    relatedNotices: Array,
});

const { locale } = useI18n();
const en = computed(() => locale.value === 'en');
const imageModalOpen = ref(false);

const timer = ref({ days: '00', hours: '00', minutes: '00', seconds: '00' });
const isExpired = ref(false);
let timerInterval = null;

const targetDate = computed(() => {
    if (props.notice.target_countdown_iso) {
        return new Date(props.notice.target_countdown_iso);
    }
    if (props.notice.application_deadline) {
        return new Date(props.notice.application_deadline);
    }
    return null;
});

const countdownTitle = computed(() => {
    if (props.notice.application_status === 'upcoming') {
        return en.value ? 'Application Starting In' : 'আবেদন শুরু হতে বাকি';
    }
    if (isExpired.value || props.notice.application_status === 'closed') {
        return en.value ? 'Application Closed' : 'আবেদনের সময়সীমা শেষ';
    }
    return en.value ? 'Application Deadline In' : 'আবেদনের শেষ সময় বাকি';
});

const countdownSubTitle = computed(() => {
    if (props.notice.application_status === 'upcoming') {
        return 'Application Starting In';
    }
    return 'Application Deadline Countdown';
});

function updateTimer() {
    if (!targetDate.value) return;
    const now = new Date().getTime();
    const distance = targetDate.value.getTime() - now;

    if (distance <= 0) {
        timer.value = { days: '00', hours: '00', minutes: '00', seconds: '00' };
        if (props.notice.application_status === 'open') {
            isExpired.value = true;
        }
        return;
    }

    const d = Math.floor(distance / (1000 * 60 * 60 * 24));
    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const s = Math.floor((distance % (1000 * 60)) / 1000);

    timer.value = {
        days: d.toString().padStart(2, '0'),
        hours: h.toString().padStart(2, '0'),
        minutes: m.toString().padStart(2, '0'),
        seconds: s.toString().padStart(2, '0'),
    };
}

onMounted(() => {
    updateTimer();
    timerInterval = setInterval(updateTimer, 1000);
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});

function formatDate(dateStr) {
    if (!dateStr) return en.value ? 'To be announced' : 'ঘোষণা সাপেক্ষে';
    const d = new Date(dateStr);
    return d.toLocaleDateString(en.value ? 'en-US' : 'bn-BD', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function formatDateTimeBn(dt) {
    if (!dt) return en.value ? 'TBA' : 'ঘোষণা সাপেক্ষে';
    const d = new Date(dt);
    return d.toLocaleString(en.value ? 'en-US' : 'bn-BD', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

const typeLabel = computed(() => {
    switch (props.notice.university_type) {
        case 'public': return en.value ? 'Public University' : 'পাবলিক বিশ্ববিদ্যালয়';
        case 'guccho': return en.value ? 'Guccho Cluster' : 'গুচ্ছ বিশ্ববিদ্যালয়';
        case 'private': return en.value ? 'Private University' : 'বেসরকারি বিশ্ববিদ্যালয়';
        default: return en.value ? 'University' : 'বিশ্ববিদ্যালয়';
    }
});
</script>

<template>
    <PublicLayout>
        <Head>
            <title>{{ notice.title }} | Sikhun Admission Portal</title>
            <meta name="description" :content="notice.meta_description || notice.title" />
            <meta property="og:title" :content="notice.title" />
            <meta property="og:description" :content="notice.meta_description || notice.title" />
            <meta v-if="notice.notice_image_url" property="og:image" :content="notice.notice_image_url" />
        </Head>

        <div class="min-h-screen bg-[var(--bg)] text-[var(--text)] pb-20">
            <!-- Breadcrumbs -->
            <div class="border-b border-gray-200 dark:border-white/5 bg-white/50 dark:bg-[#121620]/50 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto px-4 md:px-6 py-3 flex items-center gap-2 text-xs text-[var(--text-muted)]">
                    <Link href="/" class="hover:text-orange-500 transition">{{ en ? 'Home' : 'হোম' }}</Link>
                    <span>/</span>
                    <Link href="/admission-notice" class="hover:text-orange-500 transition">{{ en ? 'Admission Notices' : 'ভর্তি বিজ্ঞপ্তি' }}</Link>
                    <span>/</span>
                    <span class="truncate max-w-xs text-gray-900 dark:text-white font-medium">{{ notice.institution_name }}</span>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 md:px-6 py-8 md:py-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Main Article Column (8 cols) -->
                    <main class="lg:col-span-8">
                        <article class="bg-white dark:bg-[#171c28] rounded-3xl border border-gray-200 dark:border-white/5 p-6 md:p-10 shadow-sm dark:shadow-none">
                            <!-- Category Badge & Year -->
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20">
                                    {{ typeLabel }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300">
                                    {{ en ? 'Session:' : 'শিক্ষাবর্ষ:' }} {{ notice.academic_year }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-auto flex items-center gap-1">
                                    <span class="material-symbols-outlined !text-xs">visibility</span>
                                    <span>{{ notice.views_count }} {{ en ? 'views' : 'বার দেখা হয়েছে' }}</span>
                                </span>
                            </div>

                            <!-- Institution & Title -->
                            <div class="mb-6">
                                <span class="text-sm font-bold text-orange-500 dark:text-orange-400 block mb-1">
                                    {{ notice.institution_name }}
                                </span>
                                <h1 class="text-2xl md:text-4xl font-extrabold font-heading text-gray-900 dark:text-white leading-tight">
                                    {{ notice.title }}
                                </h1>
                            </div>

                            <!-- Real-Time Countdown Hero Banner (Matching official portal admission.eis.du.ac.bd) -->
                            <div class="my-6 rounded-3xl overflow-hidden border border-blue-500/25 bg-gradient-to-br from-[#0c1938] via-[#101e42] to-[#0c1630] text-white p-6 md:p-8 shadow-xl relative">
                                <div class="pointer-events-none absolute -top-20 left-1/2 -translate-x-1/2 w-80 h-80 bg-blue-500/15 rounded-full blur-3xl"></div>

                                <div class="relative z-10 text-center max-w-xl mx-auto">
                                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold mb-2">
                                        <span class="material-symbols-outlined !text-sm">hourglass_top</span>
                                        <span>{{ countdownTitle }}</span>
                                    </div>

                                    <div class="text-[11px] font-semibold text-blue-200/80 uppercase tracking-widest mb-3">
                                        {{ countdownSubTitle }}
                                    </div>

                                    <!-- 4-Block Live Countdown Timer (দিন : ঘণ্টা : মিনিট : সেকেন্ড) -->
                                    <div v-if="!isExpired" class="flex items-center justify-center gap-2 sm:gap-4 my-4 font-mono">
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-2xl bg-[#172554]/95 border border-blue-400/40 flex items-center justify-center text-xl sm:text-3xl font-black text-white shadow-inner">
                                                {{ timer.days }}
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-blue-300 mt-1.5">{{ en ? 'Days' : 'দিন' }}</span>
                                        </div>
                                        <span class="text-xl sm:text-3xl font-bold text-blue-400 -mt-5">:</span>
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-2xl bg-[#172554]/95 border border-blue-400/40 flex items-center justify-center text-xl sm:text-3xl font-black text-white shadow-inner">
                                                {{ timer.hours }}
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-blue-300 mt-1.5">{{ en ? 'Hours' : 'ঘণ্টা' }}</span>
                                        </div>
                                        <span class="text-xl sm:text-3xl font-bold text-blue-400 -mt-5">:</span>
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-2xl bg-[#172554]/95 border border-blue-400/40 flex items-center justify-center text-xl sm:text-3xl font-black text-white shadow-inner">
                                                {{ timer.minutes }}
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-blue-300 mt-1.5">{{ en ? 'Minutes' : 'মিনিট' }}</span>
                                        </div>
                                        <span class="text-xl sm:text-3xl font-bold text-blue-400 -mt-5">:</span>
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-2xl bg-[#172554]/95 border border-blue-400/40 flex items-center justify-center text-xl sm:text-3xl font-black text-white shadow-inner">
                                                {{ timer.seconds }}
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-blue-300 mt-1.5">{{ en ? 'Seconds' : 'সেকেন্ড' }}</span>
                                        </div>
                                    </div>
                                    <div v-else class="my-3 py-2 px-4 rounded-xl bg-red-500/20 border border-red-500/40 text-red-300 font-bold text-xs inline-block">
                                        {{ en ? 'Application Deadline Has Passed' : 'এই সার্কুলারের আবেদনের সময়সীমা শেষ হয়েছে' }}
                                    </div>

                                    <!-- 2 Official Date Boxes (Matching Image 1) -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6 pt-5 border-t border-blue-500/20 text-left">
                                        <div class="p-3.5 rounded-2xl bg-[#142145]/90 border border-blue-400/25">
                                            <span class="text-[11px] font-bold text-blue-300 uppercase block mb-1">
                                                {{ en ? 'Application Starting' : 'আবেদন শুরু' }}
                                            </span>
                                            <div class="text-xs sm:text-sm font-extrabold text-white">
                                                {{ notice.application_start_text || formatDateTimeBn(notice.application_start) }}
                                            </div>
                                        </div>
                                        <div class="p-3.5 rounded-2xl bg-[#142145]/90 border border-blue-400/25">
                                            <span class="text-[11px] font-bold text-blue-300 uppercase block mb-1">
                                                {{ en ? 'Application Deadline' : 'শেষ তারিখ' }}
                                            </span>
                                            <div class="text-xs sm:text-sm font-extrabold text-red-400">
                                                {{ notice.application_deadline_text || formatDateTimeBn(notice.application_deadline) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Key Overview Summary Box -->
                            <div class="my-6 p-6 rounded-2xl bg-gradient-to-br from-orange-500/5 to-amber-500/5 border border-orange-500/20 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block mb-1">{{ en ? 'Application Deadline' : 'আবেদনের শেষ তারিখ' }}</span>
                                    <span class="font-bold text-red-500 text-sm flex items-center gap-1">
                                        <span class="material-symbols-outlined !text-base">alarm</span>
                                        {{ notice.application_deadline_text || formatDate(notice.application_deadline) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block mb-1">{{ en ? 'Minimum GPA' : 'ন্যূনতম জিপিএ' }}</span>
                                    <span class="font-bold text-gray-900 dark:text-white text-sm">
                                        {{ notice.total_gpa_required || (en ? 'See Circular' : 'সার্কুলার দেখুন') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block mb-1">{{ en ? 'Application Fee' : 'আবেদন ফি' }}</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                        {{ notice.application_fee || (en ? 'Mentioned in circular' : 'বিজ্ঞপ্তি দেখুন') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Official Circular Notice Image Section -->
                            <div v-if="notice.notice_image_url" class="my-8">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-orange-500 !text-base">image</span>
                                        <span>{{ en ? 'Official Notice Image / Scan' : 'অফিসিয়াল সার্কুলার ইমেজ / নোটিশ' }}</span>
                                    </h3>
                                    <button
                                        @click="imageModalOpen = true"
                                        class="text-xs font-bold text-orange-500 hover:text-orange-600 flex items-center gap-1"
                                    >
                                        <span class="material-symbols-outlined !text-xs">zoom_in</span>
                                        <span>{{ en ? 'View Fullscreen' : 'বড় করে দেখুন' }}</span>
                                    </button>
                                </div>
                                <div
                                    @click="imageModalOpen = true"
                                    class="cursor-pointer group relative overflow-hidden rounded-2xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-black/20"
                                >
                                    <img
                                        :src="notice.notice_image_url"
                                        :alt="notice.title"
                                        class="w-full max-h-[460px] object-contain mx-auto group-hover:scale-[1.02] transition-transform duration-300"
                                    />
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-sm font-bold gap-2">
                                        <span class="material-symbols-outlined">zoom_in</span>
                                        <span>{{ en ? 'Click to Zoom' : 'সম্পূর্ণ নোটিশ জুম করে দেখতে ক্লিক করুন' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Blog-style Body Content -->
                            <div
                                class="prose prose-slate dark:prose-invert max-w-none text-sm md:text-base leading-relaxed my-8"
                                v-html="notice.description"
                            />

                            <!-- Units & Faculties Section -->
                            <div v-if="notice.units?.length" class="my-8 pt-6 border-t border-gray-200 dark:border-white/10">
                                <h3 class="text-base md:text-lg font-bold font-heading text-gray-900 dark:text-white mb-4">
                                    {{ en ? 'Units, Faculties & Exam Schedule' : 'ইউনিট ও অনুষদভিত্তিক পরীক্ষার সময়সূচি' }}
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse border border-gray-200 dark:border-white/10 text-xs md:text-sm">
                                        <thead>
                                            <tr class="bg-gray-50 dark:bg-white/5 text-left text-gray-700 dark:text-gray-300">
                                                <th class="p-3 border border-gray-200 dark:border-white/10">{{ en ? 'Unit' : 'ইউনিট' }}</th>
                                                <th class="p-3 border border-gray-200 dark:border-white/10">{{ en ? 'Faculty / Group' : 'অনুষদ / শাখা' }}</th>
                                                <th class="p-3 border border-gray-200 dark:border-white/10">{{ en ? 'Min GPA' : 'ন্যূনতম জিপিএ' }}</th>
                                                <th class="p-3 border border-gray-200 dark:border-white/10">{{ en ? 'Exam Date' : 'পরীক্ষার তারিখ' }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(unit, idx) in notice.units" :key="idx" class="border-b border-gray-200 dark:border-white/5">
                                                <td class="p-3 font-bold text-gray-900 dark:text-white border border-gray-200 dark:border-white/10">{{ unit.name || unit }}</td>
                                                <td class="p-3 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-white/10">{{ unit.faculty || '-' }}</td>
                                                <td class="p-3 font-semibold text-blue-600 dark:text-blue-400 border border-gray-200 dark:border-white/10">{{ unit.min_gpa || '-' }}</td>
                                                <td class="p-3 font-semibold text-red-500 border border-gray-200 dark:border-white/10">{{ unit.exam_date || '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Source Attribution Credit -->
                            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-gray-500 dark:text-gray-400 gap-2">
                                <div>
                                    <span v-if="notice.source_name">
                                        {{ en ? 'Source / Credit: ' : 'বিজ্ঞপ্তির সূত্র: ' }}
                                        <strong class="text-gray-700 dark:text-gray-300">{{ notice.source_name }}</strong>
                                    </span>
                                </div>
                                <a
                                    v-if="notice.source_url"
                                    :href="notice.source_url"
                                    target="_blank"
                                    class="text-orange-500 hover:underline flex items-center gap-1"
                                >
                                    <span>{{ en ? 'Original Source Link' : 'মূল সার্কুলার লিংক' }}</span>
                                    <span class="material-symbols-outlined !text-xs">open_in_new</span>
                                </a>
                            </div>
                        </article>
                    </main>

                    <!-- Sidebar Column (4 cols) -->
                    <aside class="lg:col-span-4 space-y-6">
                        <!-- Quick Action Card -->
                        <div class="bg-white dark:bg-[#171c28] rounded-3xl border border-gray-200 dark:border-white/5 p-6 shadow-sm dark:shadow-none sticky top-24">
                            <h3 class="text-base font-bold font-heading text-gray-900 dark:text-white mb-4">
                                {{ en ? 'Apply & Guidelines' : 'আবেদন ও নিয়মাবলী' }}
                            </h3>

                            <div class="space-y-3">
                                <a
                                    v-if="notice.apply_url"
                                    :href="notice.apply_url"
                                    target="_blank"
                                    class="w-full py-3.5 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm shadow-lg shadow-orange-500/20 transition flex items-center justify-center gap-2"
                                >
                                    <span class="material-symbols-outlined !text-base">touch_app</span>
                                    <span>{{ en ? 'Apply Online Now' : 'অনলাইনে আবেদন করুন' }}</span>
                                    <span class="material-symbols-outlined !text-sm">open_in_new</span>
                                </a>

                                <a
                                    v-if="notice.official_website_url"
                                    :href="notice.official_website_url"
                                    target="_blank"
                                    class="w-full py-3 px-4 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 font-semibold text-xs transition flex items-center justify-center gap-2"
                                >
                                    <span class="material-symbols-outlined !text-sm">language</span>
                                    <span>{{ en ? 'Official Website' : 'অফিসিয়াল ওয়েবসাইট' }}</span>
                                </a>
                            </div>

                            <!-- Preparation Bundle Suggestion -->
                            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-white/5">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-orange-500 block mb-2">
                                    {{ en ? 'RECOMMENDED PREPARATION' : 'ভর্তি প্রস্তুতি সহায়ক' }}
                                </span>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 leading-relaxed">
                                    {{ en ? 'Access digitised textbooks, previous years question bank, and AI revision companion on Sikhun.' : 'শিখুনে ডিজিটাল পাঠ্যবই, বিগত বছরের প্রশ্নব্যাংক ও এআই টিউটর দিয়ে ভর্তি পরীক্ষার চূড়ান্ত প্রস্তুতি নিন।' }}
                                </p>
                                <Link
                                    href="/library"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-orange-500 hover:text-orange-600"
                                >
                                    <span>{{ en ? 'Explore Textbooks & Materials' : 'পাঠ্যবই ও প্রশ্নব্যাংক দেখুন' }}</span>
                                    <span>→</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Related Circulars -->
                        <div v-if="relatedNotices?.length" class="bg-white dark:bg-[#171c28] rounded-3xl border border-gray-200 dark:border-white/5 p-6 shadow-sm dark:shadow-none">
                            <h3 class="text-sm font-bold font-heading text-gray-900 dark:text-white mb-4">
                                {{ en ? 'Other Admission Notices' : 'অন্যান্য ভর্তি বিজ্ঞপ্তি' }}
                            </h3>
                            <div class="space-y-4">
                                <div
                                    v-for="rel in relatedNotices"
                                    :key="rel.id"
                                    class="pb-4 border-b border-gray-100 dark:border-white/5 last:border-0 last:pb-0"
                                >
                                    <span class="text-[10px] font-bold text-orange-500 block mb-1">
                                        {{ rel.institution_name }}
                                    </span>
                                    <Link :href="`/admission-notice/${rel.slug}`">
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-white hover:text-orange-500 transition line-clamp-2">
                                            {{ rel.title }}
                                        </h4>
                                    </Link>
                                    <span class="text-[11px] text-red-500 mt-1 block">
                                        {{ en ? 'Deadline:' : 'শেষ তারিখ:' }} {{ formatDate(rel.application_deadline) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <!-- Lightbox Modal for Notice Image -->
            <div
                v-if="imageModalOpen"
                class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4"
                @click.self="imageModalOpen = false"
            >
                <div class="relative max-w-5xl max-h-[90vh] bg-[#121620] rounded-2xl p-4 overflow-auto border border-white/10">
                    <button
                        @click="imageModalOpen = false"
                        class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg"
                    >
                        ✕
                    </button>
                    <img
                        :src="notice.notice_image_url"
                        :alt="notice.title"
                        class="max-w-full max-h-[82vh] object-contain mx-auto rounded-lg"
                    />
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
