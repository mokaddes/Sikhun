<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { ArrowLeftIcon, ArrowRightIcon, CheckCircleIcon, SparklesIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ progress: Object, completedAt: String, freeBooks: Array });
const { t } = useI18n();
const currentStep = ref(Math.min(4, Math.max(1, Number(props.progress?.current_step ?? 1))));
const form = useForm({
    goal: props.progress?.goal ?? '',
    class_level: props.progress?.class_level ?? '',
    subject: props.progress?.subject ?? '',
    book_id: props.progress?.book_id ?? '',
});
const steps = computed(() => [
    t('onboarding.goal_title'),
    t('onboarding.class_title'),
    t('onboarding.book_title'),
    t('onboarding.question_title'),
]);

function saveStep() {
    form.transform((data) => ({ ...data, step: currentStep.value })).post('/onboarding/step', {
        preserveScroll: true,
        onSuccess: () => { currentStep.value = Math.min(4, currentStep.value + 1); },
    });
}

function skipStep() {
    router.post('/onboarding/step', { step: currentStep.value, skip: true }, {
        preserveScroll: true,
        onSuccess: () => { currentStep.value = Math.min(4, currentStep.value + 1); },
    });
}

function skipAll() {
    router.post('/onboarding/skip');
}
</script>

<template>
    <Head :title="t('onboarding.title')" />
    <StudentLayout>
        <main class="mx-auto max-w-2xl py-6">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <span class="eyebrow">{{ t('onboarding.eyebrow') }}</span>
                    <h1 class="mt-1 font-heading text-2xl font-extrabold">{{ t('onboarding.title') }}</h1>
                </div>
                <button class="text-sm font-medium text-[var(--text-muted)] underline" @click="skipAll">
                    {{ t('onboarding.skip_all') }}
                </button>
            </div>

            <div class="mb-6 grid grid-cols-4 gap-2" :aria-label="t('onboarding.progress')">
                <div v-for="(title, index) in steps" :key="title" class="h-2 rounded-full" :class="index + 1 <= currentStep ? 'bg-[var(--primary)]' : 'bg-[var(--surface2)]'"></div>
            </div>
            <p class="mb-2 text-sm text-[var(--text-muted)]">{{ t('onboarding.step') }} {{ currentStep }} / 4 · {{ steps[currentStep - 1] }}</p>

            <section class="card rounded-3xl p-6 sm:p-8">
                <template v-if="currentStep === 1">
                    <h2 class="mb-2 font-heading text-xl font-bold">{{ t('onboarding.goal_title') }}</h2>
                    <p class="mb-5 text-sm text-[var(--text-muted)]">{{ t('onboarding.goal_hint') }}</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label v-for="goal in ['hsc', 'ssc', 'admission', 'job']" :key="goal" class="cursor-pointer rounded-xl border p-4 transition" :class="form.goal === goal ? 'border-[var(--primary)] bg-[var(--primary)]/10' : 'border-[var(--border)]'">
                            <input v-model="form.goal" type="radio" name="goal" :value="goal" class="sr-only" />
                            <span class="font-semibold">{{ t(`onboarding.goals.${goal}`) }}</span>
                        </label>
                    </div>
                    <p v-if="form.errors.goal" class="mt-3 text-sm text-red-600">{{ form.errors.goal }}</p>
                </template>

                <template v-else-if="currentStep === 2">
                    <h2 class="mb-2 font-heading text-xl font-bold">{{ t('onboarding.class_title') }}</h2>
                    <p class="mb-5 text-sm text-[var(--text-muted)]">{{ t('onboarding.class_hint') }}</p>
                    <div class="space-y-4">
                        <label class="block text-sm font-medium">{{ t('onboarding.class_label') }}
                            <input v-model="form.class_level" class="input mt-1 w-full" maxlength="80" :placeholder="t('onboarding.class_placeholder')" />
                        </label>
                        <p v-if="form.errors.class_level" class="-mt-3 text-sm text-red-600">{{ form.errors.class_level }}</p>
                        <label class="block text-sm font-medium">{{ t('onboarding.subject_label') }}
                            <input v-model="form.subject" class="input mt-1 w-full" maxlength="120" :placeholder="t('onboarding.subject_placeholder')" />
                        </label>
                        <p v-if="form.errors.subject" class="-mt-3 text-sm text-red-600">{{ form.errors.subject }}</p>
                    </div>
                </template>

                <template v-else-if="currentStep === 3">
                    <h2 class="mb-2 font-heading text-xl font-bold">{{ t('onboarding.book_title') }}</h2>
                    <p class="mb-5 text-sm text-[var(--text-muted)]">{{ t('onboarding.book_hint') }}</p>
                    <label class="block text-sm font-medium">{{ t('onboarding.choose_book') }}
                        <select v-model="form.book_id" class="input mt-1 w-full">
                            <option value="">{{ t('onboarding.no_book') }}</option>
                            <option v-for="book in freeBooks" :key="book.id" :value="book.id">{{ book.title }}{{ book.subject ? ` · ${book.subject}` : '' }}</option>
                        </select>
                    </label>
                    <Link href="/bookshelf" class="mt-4 inline-flex text-sm font-semibold text-[var(--primary)] hover:underline">{{ t('onboarding.upload_book') }} →</Link>
                </template>

                <template v-else>
                    <div class="mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-[var(--primary)]/10 text-[var(--primary)]"><SparklesIcon class="h-6 w-6" /></div>
                    <h2 class="mb-2 font-heading text-xl font-bold">{{ t('onboarding.question_title') }}</h2>
                    <p class="mb-5 text-sm text-[var(--text-muted)]">{{ t('onboarding.question_hint') }}</p>
                    <div v-if="progress?.first_question_completed" class="flex items-center gap-2 rounded-xl bg-[var(--secondary)]/10 p-4 text-sm font-medium text-[var(--secondary)]">
                        <CheckCircleIcon class="h-5 w-5" /> {{ t('onboarding.question_done') }}
                    </div>
                    <Link v-else href="/ai/chat" class="btn btn-primary inline-flex items-center gap-2">{{ t('onboarding.ask_first_question') }} <ArrowRightIcon class="h-4 w-4" /></Link>
                </template>

                <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-[var(--border)] pt-5">
                    <button v-if="currentStep > 1" type="button" class="btn btn-outline" @click="currentStep--"><ArrowLeftIcon class="h-4 w-4" /> {{ t('onboarding.back') }}</button>
                    <span v-else></span>
                    <div class="flex items-center gap-3">
                        <button v-if="currentStep < 4" type="button" class="text-sm font-medium text-[var(--text-muted)] underline" @click="skipStep">{{ t('onboarding.skip_step') }}</button>
                        <button v-if="currentStep < 4" type="button" class="btn btn-primary" :disabled="form.processing" @click="saveStep">{{ t('onboarding.continue') }} <ArrowRightIcon class="h-4 w-4" /></button>
                        <button v-else type="button" class="btn btn-primary" @click="skipStep">{{ t('onboarding.finish') }}</button>
                    </div>
                </div>
            </section>

            <p class="mt-4 text-center text-xs text-[var(--text-muted)]">{{ t('onboarding.optional') }}</p>
        </main>
    </StudentLayout>
</template>
