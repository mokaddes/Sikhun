<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';
import {
    RectangleStackIcon,
    PlusIcon,
    SparklesIcon,
    PlayIcon,
    ClockIcon,
    ArrowRightIcon,
} from '@heroicons/vue/24/outline';

defineProps({ sets: Array });
const { t, locale } = useI18n();
</script>

<template>
    <Head :title="t('flashcards.title')" />
    <StudentLayout>
        <!-- Header Section -->
        <header class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--tertiary)]/10 px-3 py-1 text-xs font-semibold text-[var(--tertiary)]">
                    <SparklesIcon class="h-3.5 w-3.5" />
                    {{ locale === 'en' ? 'Active Recall & Spaced Repetition' : 'সক্রিয় স্মৃতিচর্চা ও স্পেসড রিপিটিশন' }}
                </span>
                <h1 class="mt-2 font-heading text-2xl font-black tracking-tight sm:text-3xl">
                    {{ t('flashcards.title') }}
                </h1>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en'
                        ? 'Master key concepts with AI-powered interactive flashcard decks.'
                        : 'এআই প্রস্তুতকৃত ইন্টারেক্টিভ ফ্ল্যাশকার্ড দিয়ে গুরুত্বপূর্ণ বিষয়সমূহ সহজে মুখস্থ করুন।' }}
                </p>
            </div>
            <Link
                href="/flashcards/create"
                class="btn btn-primary inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-bold shadow-md shadow-[var(--primary)]/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
            >
                <PlusIcon class="h-5 w-5" />
                <span>{{ t('flashcards.new_set') }}</span>
            </Link>
        </header>

        <!-- Flashcards Grid Cards -->
        <div v-if="sets && sets.length" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="set in sets"
                :key="set.id"
                class="group relative flex flex-col justify-between rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 transition-all duration-200 hover:-translate-y-1 hover:border-[var(--tertiary)]/40 hover:shadow-lg hover:shadow-[var(--shadow-color)]"
            >
                <div>
                    <!-- Top Icon & Count -->
                    <div class="flex items-center justify-between mb-4">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-[var(--tertiary)]/10 text-[var(--tertiary)] group-hover:scale-105 transition-transform">
                            <RectangleStackIcon class="h-6 w-6" />
                        </span>
                        <span class="inline-flex items-center rounded-full bg-[var(--surface2)] px-3 py-1 text-xs font-bold text-[var(--text-muted)] border border-[var(--border)]">
                            {{ set.flashcards_count || set.flashcards?.length || 0 }} {{ t('flashcards.cards_count') }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h3 class="font-heading text-lg font-bold tracking-tight text-[var(--text)] line-clamp-2 group-hover:text-[var(--tertiary)] transition-colors">
                        {{ set.title }}
                    </h3>

                    <!-- Created Date -->
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-[var(--text-muted)]">
                        <ClockIcon class="h-3.5 w-3.5" />
                        <span>{{ new Date(set.created_at).toLocaleDateString() }}</span>
                    </div>
                </div>

                <!-- Bottom Actions -->
                <div class="mt-6 border-t border-[var(--border)]/70 pt-4 flex items-center justify-between gap-3">
                    <Link
                        :href="`/flashcards/${set.id}`"
                        class="btn btn-primary flex-1 justify-center gap-2 rounded-xl py-2.5 text-xs font-bold shadow-sm"
                    >
                        <PlayIcon class="h-4 w-4" />
                        <span>{{ locale === 'en' ? 'Study Now' : 'পড়াশোনা শুরু' }}</span>
                    </Link>
                    <ConfirmButton
                        :href="`/flashcards/${set.id}`"
                        method="delete"
                        class="text-rose-500 hover:text-rose-600 hover:bg-rose-500/10 p-2 rounded-xl transition-colors"
                    />
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-[var(--border)] bg-[var(--surface)] p-12 text-center"
        >
            <div class="grid h-16 w-16 place-items-center rounded-2xl bg-[var(--surface2)] text-[var(--text-muted)] mb-4">
                <RectangleStackIcon class="h-8 w-8 opacity-60" />
            </div>
            <h3 class="font-heading text-lg font-bold">
                {{ t('flashcards.no_sets') }}
            </h3>
            <p class="mt-1 max-w-sm text-sm text-[var(--text-muted)]">
                {{ locale === 'en'
                    ? 'Create your first flashcard deck to boost retention and memorize faster.'
                    : 'এখনো কোনো ফ্ল্যাশকার্ড তৈরি করা হয়নি। দ্রুত মুখস্থ ও অনুশীলনের জন্য নতুন সেট তৈরি করুন।' }}
            </p>
            <Link href="/flashcards/create" class="btn btn-primary mt-5 inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-md">
                <PlusIcon class="h-4 w-4" />
                {{ t('flashcards.new_set') }}
            </Link>
        </div>
    </StudentLayout>
</template>
