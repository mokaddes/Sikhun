<script setup>
import { computed } from 'vue';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

defineProps({ page: Object, seo: Object });
const { locale } = useI18n();

const steps = computed(() => locale.value === 'en' ? [
    { icon: '📚', title: 'Browse & Read', text: 'Explore free and paid books, and read them safely in our watermarked digital reader.' },
    { icon: '🤖', title: 'Ask AI', text: 'Chat with AI about anything in your book, grounded in the actual text.' },
    { icon: '📝', title: 'Test Yourself', text: 'Generate a practice or timed exam on any topic, book, or pasted text.' },
    { icon: '🗂️', title: 'Review with Flashcards', text: 'Turn any content into flashcards with built-in spaced repetition.' },
    { icon: '🏆', title: 'Track Progress', text: 'Climb the leaderboard and watch your subject-wise performance improve.' },
] : [
    { icon: '📚', title: 'ব্রাউজ করুন ও পড়ুন', text: 'ফ্রি ও পেইড বই দেখুন এবং আমাদের ওয়াটারমার্কড ডিজিটাল রিডারে নিরাপদে পড়ুন।' },
    { icon: '🤖', title: 'AI-কে জিজ্ঞাসা করুন', text: 'আপনার বইয়ের যেকোনো বিষয়ে AI-এর সাথে চ্যাট করুন, বইয়ের প্রকৃত কনটেন্ট অনুযায়ী উত্তর পাবেন।' },
    { icon: '📝', title: 'নিজেকে যাচাই করুন', text: 'যেকোনো বিষয়, বই বা টেক্সট থেকে অনুশীলন বা সময়ভিত্তিক পরীক্ষা তৈরি করুন।' },
    { icon: '🗂️', title: 'ফ্ল্যাশকার্ড দিয়ে রিভিশন', text: 'যেকোনো কনটেন্টকে ফ্ল্যাশকার্ডে রূপান্তর করুন — স্পেসড রিপিটিশন সহ।' },
    { icon: '🏆', title: 'অগ্রগতি ট্র্যাক করুন', text: 'লিডারবোর্ডে এগিয়ে যান এবং বিষয়ভিত্তিক উন্নতি দেখুন।' },
]);
</script>

<template>
    <SeoHead :seo="seo" />
    <PublicLayout>
        <section class="max-w-4xl mx-auto px-5 pt-16 pb-10 text-center">
            <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-[var(--secondary)] to-[var(--primary)] flex items-center justify-center text-3xl">🛠️</div>
            <h1 class="font-heading text-3xl md:text-4xl font-extrabold mb-3">{{ page.title }}</h1>
        </section>

        <!-- Numbered step timeline -->
        <section class="max-w-2xl mx-auto px-5 pb-16">
            <div v-for="(step, i) in steps" :key="step.title" class="relative pl-16 pb-10 last:pb-0">
                <div v-if="i < steps.length - 1" class="absolute left-6 top-12 bottom-0 w-px bg-[var(--border)]" />
                <div class="absolute left-0 top-0 w-12 h-12 rounded-full bg-[var(--surface)] border-2 border-[var(--primary)] flex items-center justify-center text-xl">
                    {{ step.icon }}
                </div>
                <div class="font-heading font-bold text-lg mb-1">{{ i + 1 }}. {{ step.title }}</div>
                <p class="text-[var(--text-muted)]">{{ step.text }}</p>
            </div>
        </section>

        <section class="max-w-3xl mx-auto px-5 pb-20">
            <div class="prose prose-lg dark:prose-invert max-w-none" v-html="page.content" />
        </section>
    </PublicLayout>
</template>
