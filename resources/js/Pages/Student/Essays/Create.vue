<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import {
    SparklesIcon,
    ArrowLeftIcon,
    PencilSquareIcon,
    DocumentTextIcon,
} from '@heroicons/vue/24/outline';

const { t, locale } = useI18n();
const form = useForm({ grading_type: 'general', essay_text: '' });

const rubricOptions = [
    { id: 'hsc_bangla', labelEn: 'HSC Bengali Essay', labelBn: 'এইচএসসি বাংলা রচনা', descEn: 'Formal Bengali structure & grammar', descBn: 'ভূমিকা, বিষয়বস্তু, উপসংহার ও ভাষার শুদ্ধতা' },
    { id: 'hsc_english', labelEn: 'HSC English Essay', labelBn: 'এইচএসসি ইংরেজি রচনা', descEn: 'Coherence, vocabulary & grammar', descBn: 'ইংরেজি ব্যাকরণ, বাক্য গঠন ও শব্দচয়ন' },
    { id: 'general', labelEn: 'General Writing', labelBn: 'সাধারণ রচনা / অনুচ্ছেদ', descEn: 'General clarity and readability', descBn: 'যেকোনো অনুচ্ছেদ বা লেখার সাবলীলতা ও মান' },
    { id: 'custom_rubric', labelEn: 'Custom Rubric', labelBn: 'কাস্টম রুব্রিক', descEn: 'In-depth analytical assessment', descBn: 'গভীর বিশ্লেষণধর্মী মূল্যায়ন' },
];

const wordCount = computed(() => {
    const text = form.essay_text.trim();
    if (!text) return 0;
    return text.split(/\s+/).filter(Boolean).length;
});

function submit() {
    form.post('/essays');
}
</script>

<template>
    <Head :title="t('essays.new_essay')" />
    <StudentLayout>
        <div class="mx-auto max-w-2xl pb-12">
            <!-- Back navigation -->
            <div class="mb-6 flex items-center justify-between">
                <Link
                    href="/essays"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--text)] transition-colors"
                >
                    <ArrowLeftIcon class="h-4 w-4" />
                    <span>{{ locale === 'en' ? 'Back to Essays' : 'সকল রচনায় ফিরে যান' }}</span>
                </Link>
            </div>

            <!-- Header Card -->
            <div class="mb-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 flex items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-[var(--accent)]/10 px-3 py-1 text-xs font-semibold text-[var(--accent)] mb-2">
                        <SparklesIcon class="h-3.5 w-3.5" />
                        {{ locale === 'en' ? 'AI Writing Evaluator' : 'এআই রচনা মূল্যায়নকারী' }}
                    </div>
                    <h1 class="font-heading text-2xl font-black tracking-tight sm:text-3xl">
                        {{ t('essays.new_essay') }}
                    </h1>
                    <p class="mt-1 text-sm text-[var(--text-muted)]">
                        {{ locale === 'en'
                            ? 'Submit your written work to get instant line-by-line feedback and rubric scoring.'
                            : 'আপনার লেখা জমা দিন এবং নির্ভুল বানান, বাক্য গঠন ও সামগ্রিক স্কোরের বিশ্লেষণ পান।' }}
                    </p>
                </div>
                <div class="hidden sm:grid h-14 w-14 place-items-center rounded-2xl bg-[var(--surface2)] text-[var(--accent)] shrink-0">
                    <PencilSquareIcon class="h-8 w-8" />
                </div>
            </div>

            <!-- Form Card (Centered) -->
            <form @submit.prevent="submit" class="space-y-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 sm:p-8 shadow-sm">
                <!-- Grading Rubric Selector -->
                <div>
                    <label class="block text-sm font-bold text-[var(--text)] mb-2">
                        {{ t('essays.grading_type') }}
                    </label>
                    <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                        <button
                            v-for="rub in rubricOptions"
                            :key="rub.id"
                            type="button"
                            class="flex flex-col items-start rounded-xl border p-3.5 text-left transition-all"
                            :class="form.grading_type === rub.id
                                ? 'border-[var(--accent)] bg-[var(--accent)]/8 ring-1 ring-[var(--accent)]'
                                : 'border-[var(--border)] bg-[var(--surface2)]/40 hover:bg-[var(--surface2)]'"
                            @click="form.grading_type = rub.id"
                        >
                            <span class="font-heading text-xs font-bold" :class="form.grading_type === rub.id ? 'text-[var(--accent)]' : 'text-[var(--text)]'">
                                {{ locale === 'en' ? rub.labelEn : rub.labelBn }}
                            </span>
                            <span class="mt-1 text-[11px] text-[var(--text-muted)] line-clamp-1">
                                {{ locale === 'en' ? rub.descEn : rub.descBn }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Essay Textarea -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-bold text-[var(--text)]">
                            {{ t('essays.essay_text') }}
                        </label>
                        <span class="text-xs font-semibold text-[var(--text-muted)]">
                            {{ wordCount }} {{ locale === 'en' ? 'words' : 'শব্দ' }}
                        </span>
                    </div>
                    <textarea
                        v-model="form.essay_text"
                        rows="12"
                        required
                        :placeholder="locale === 'en' ? 'Paste or write your essay or paragraphs here...' : 'আপনার রচনা, অনুচ্ছেদ বা বর্ণনামূলক লেখাটি এখানে লিখুন বা পেস্ট করুন...'"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] p-4 text-sm font-normal leading-relaxed focus:border-[var(--accent)] focus:bg-[var(--surface)] focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20"
                    ></textarea>
                    <p v-if="form.errors.essay_text" class="mt-1 text-xs text-rose-500">{{ form.errors.essay_text }}</p>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing || !form.essay_text.trim()"
                        class="btn btn-primary w-full justify-center gap-2 rounded-xl py-3.5 text-base font-bold shadow-lg shadow-[var(--primary)]/20 transition-all hover:scale-[1.01] active:scale-[0.99] disabled:opacity-60"
                    >
                        <SparklesIcon class="h-5 w-5" />
                        <span>{{ form.processing ? t('essays.grading') : t('essays.submit') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </StudentLayout>
</template>
