<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    SparklesIcon,
    ArrowLeftIcon,
    CheckCircleIcon,
    PencilSquareIcon,
    TrophyIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ essay: Object });
const { t, locale } = useI18n();
const essay = ref(props.essay);
const retrying = ref(false);
let pollInterval;

function poll() {
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(async () => {
        try {
            const { data } = await axios.get(`/essays/${essay.value.id}/status`);
            if (data.status !== 'grading') {
                essay.value = { ...essay.value, status: data.status, result: data.result };
                clearInterval(pollInterval);
            }
        } catch (e) {
            console.error(e);
        }
    }, 2500);
}

onMounted(() => { if (essay.value.status === 'grading') poll(); });
watch(() => props.essay, (value) => {
    essay.value = value;
    if (value.status === 'grading') poll();
    else clearInterval(pollInterval);
});
onBeforeUnmount(() => clearInterval(pollInterval));

function retryGrading() {
    retrying.value = true;
    router.post(`/essays/${essay.value.id}/retry`, {}, {
        onFinish: () => { retrying.value = false; },
    });
}
</script>

<template>
    <Head :title="t('essays.title')" />
    <StudentLayout>
        <div class="mx-auto max-w-3xl pb-16">
            <!-- Back navigation -->
            <div class="mb-4 flex items-center justify-between">
                <Link
                    href="/essays"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--text-muted)] hover:text-[var(--text)] transition-colors"
                >
                    <ArrowLeftIcon class="h-4 w-4" />
                    <span>{{ locale === 'en' ? 'Back to Essays' : 'সকল রচনা' }}</span>
                </Link>
            </div>

            <!-- Grading State -->
            <div
                v-if="essay.status === 'grading'"
                class="flex flex-col items-center justify-center rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-12 text-center shadow-sm"
            >
                <div class="relative mb-6">
                    <div class="h-16 w-16 animate-spin rounded-full border-4 border-[var(--accent)]/20 border-t-[var(--accent)]"></div>
                    <SparklesIcon class="absolute inset-0 m-auto h-7 w-7 text-[var(--accent)] animate-pulse" />
                </div>
                <h2 class="font-heading text-xl font-bold">
                    {{ t('essays.grading') }}
                </h2>
                <p class="mt-2 max-w-md text-sm text-[var(--text-muted)]">
                    {{ locale === 'en'
                        ? 'Analyzing grammar, structure, tone, and rubric criteria. Please wait a moment...'
                        : 'ব্যাকরণ, বাক্য গঠন, বানান ও রুব্রিক অনুসারে লেখাটি বিশ্লেষণ করা হচ্ছে। অনুগ্রহ করে কিছুক্ষণ অপেক্ষা করুন...' }}
                </p>
            </div>

            <!-- Failed State -->
            <div
                v-else-if="essay.status === 'failed'"
                class="rounded-2xl border border-rose-500/30 bg-rose-500/10 p-6 text-center text-rose-600 dark:text-rose-400"
            >
                <p class="font-bold">{{ locale === 'en' ? 'Grading failed' : 'মূল্যায়ন ব্যর্থ হয়েছে' }}</p>
                <p class="mt-1 text-sm text-[var(--text-muted)]">{{ locale === 'en' ? 'Your essay is saved. Retry grading without submitting it again.' : 'আপনার লেখাটি সংরক্ষিত আছে। আবার জমা না দিয়েই মূল্যায়ন পুনরায় চেষ্টা করুন।' }}</p>
                <button type="button" :disabled="retrying" class="btn btn-primary mt-4 inline-flex items-center gap-2 disabled:opacity-60" @click="retryGrading">
                    <ArrowPathIcon class="h-4 w-4" :class="retrying ? 'animate-spin' : ''" />
                    {{ retrying ? (locale === 'en' ? 'Retrying…' : 'আবার চেষ্টা হচ্ছে…') : (locale === 'en' ? 'Retry grading' : 'আবার মূল্যায়ন') }}
                </button>
            </div>

            <!-- Assessment Results -->
            <div v-else-if="essay.result" class="space-y-6">
                <!-- Score Header Card -->
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--accent)]/10 px-3 py-1 text-xs font-bold text-[var(--accent)] mb-2">
                                <TrophyIcon class="h-3.5 w-3.5" />
                                {{ locale === 'en' ? 'AI Evaluation Complete' : 'মূল্যায়ন সম্পন্ন' }}
                            </span>
                            <h1 class="font-heading text-2xl font-black tracking-tight text-[var(--text)]">
                                {{ t('essays.total_score') }}
                            </h1>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                {{ essay.grading_type?.replace('_', ' ').toUpperCase() }}
                            </p>
                        </div>

                        <div class="flex items-baseline gap-1 rounded-2xl bg-[var(--surface2)] px-6 py-4 border border-[var(--border)]">
                            <span class="font-heading text-4xl font-black tracking-tight text-[var(--accent)]">
                                {{ essay.result.total_score }}
                            </span>
                            <span class="text-lg font-bold text-[var(--text-muted)]">
                                / {{ essay.result.max_score }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Rubric Breakdown -->
                <div v-if="essay.result.breakdown" class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm space-y-5">
                    <h2 class="font-heading text-lg font-bold">
                        {{ locale === 'en' ? 'Criteria Breakdown' : 'মানদণ্ডভিত্তিক নম্বর বিভাজন' }}
                    </h2>
                    <div v-for="(item, key) in essay.result.breakdown" :key="key" class="space-y-1.5">
                        <div class="flex justify-between text-xs font-bold">
                            <span class="capitalize text-[var(--text)]">{{ key.replace('_', ' ') }}</span>
                            <span class="text-[var(--text-muted)]">{{ item.score }} / {{ item.max }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-[var(--surface2)] overflow-hidden">
                            <div class="h-full bg-[var(--accent)] rounded-full transition-all duration-500" :style="{ width: `${(item.score / item.max) * 100}%` }"></div>
                        </div>
                        <p class="text-xs text-[var(--text-muted)] leading-relaxed">{{ item.feedback }}</p>
                    </div>
                </div>

                <!-- Overall Feedback & Strengths/Improvements -->
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm space-y-6">
                    <div>
                        <h2 class="font-heading text-lg font-bold mb-2">
                            {{ locale === 'en' ? 'Overall Feedback' : 'সার্বিক মূল্যায়ন ও পর্যালোচনা' }}
                        </h2>
                        <p class="text-sm leading-relaxed text-[var(--text-muted)]">{{ essay.result.overall_feedback }}</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 text-xs pt-4 border-t border-[var(--border)]">
                        <!-- Strengths -->
                        <div class="rounded-2xl bg-emerald-500/10 p-4 border border-emerald-500/20">
                            <div class="font-bold text-emerald-600 dark:text-emerald-400 mb-2 flex items-center gap-1.5 text-sm">
                                <CheckCircleIcon class="h-4 w-4" />
                                <span>{{ t('essays.strengths') }}</span>
                            </div>
                            <ul class="text-[var(--text-muted)] space-y-1.5">
                                <li v-for="s in essay.result.strengths" :key="s" class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">✓</span>
                                    <span>{{ s }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Improvements -->
                        <div class="rounded-2xl bg-amber-500/10 p-4 border border-amber-500/20">
                            <div class="font-bold text-amber-600 dark:text-amber-400 mb-2 flex items-center gap-1.5 text-sm">
                                <span>💡</span>
                                <span>{{ t('essays.improvements') }}</span>
                            </div>
                            <ul class="text-[var(--text-muted)] space-y-1.5">
                                <li v-for="s in essay.result.improvements" :key="s" class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">→</span>
                                    <span>{{ s }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Original Submission Text (Collapsible/Preview) -->
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm">
                    <h3 class="font-heading text-sm font-bold text-[var(--text-muted)] mb-2 uppercase tracking-wider">
                        {{ locale === 'en' ? 'Your Submitted Essay' : 'আপনার জমা দেওয়া লেখা' }}
                    </h3>
                    <p class="text-xs leading-relaxed text-[var(--text)] whitespace-pre-wrap rounded-xl bg-[var(--surface2)]/60 p-4 border border-[var(--border)]">
                        {{ essay.essay_text }}
                    </p>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
