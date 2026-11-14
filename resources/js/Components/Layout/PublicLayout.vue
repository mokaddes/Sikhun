<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import ThemeToggle from '@/Components/UI/ThemeToggle.vue';
import LanguageSwitcher from '@/Components/UI/LanguageSwitcher.vue';
import SupportWidget from '@/Components/Support/SupportWidget.vue';
import BrandLogo from '@/Components/BrandLogo.vue';

const { t, locale } = useI18n();
const mobileOpen = ref(false);
const display = ref({ text: 1, buttons: 1 });
const student = usePage().props.auth?.student;
const site = usePage().props.site;

onMounted(() => {
    try {
        const saved = JSON.parse(localStorage.getItem('sikhun_display') || '{}');
        display.value = { text: Math.min(1.2, Math.max(0.9, Number(saved.text) || 1)), buttons: Math.min(1.2, Math.max(0.85, Number(saved.buttons) || 1)) };
        applyDisplay();
    } catch (_) { /* use defaults */ }
});

function applyDisplay() {
    document.documentElement.style.setProperty('--text-scale', display.value.text);
    document.documentElement.style.setProperty('--button-scale', display.value.buttons);
    localStorage.setItem('sikhun_display', JSON.stringify(display.value));
}

function adjustDisplay(key, amount) {
    const bounds = key === 'text' ? [0.9, 1.2] : [0.85, 1.2];
    display.value[key] = Math.min(bounds[1], Math.max(bounds[0], +(display.value[key] + amount).toFixed(2)));
    applyDisplay();
}

const nav = computed(() => [
    { label: t('nav.courses'), href: '/courses' },
    { label: t('nav.library'), href: '/library' },
    {
        label: locale.value === 'bn' ? 'ভর্তি ২০২৬' : 'Admissions 2026',
        href: '/admission-notice',
        badge: locale.value === 'bn' ? 'চলছে' : 'Live',
        highlight: true,
    },
    { label: locale.value === 'bn' ? 'চাকরির খবর' : 'Job News', href: '/job-news' },
    { label: locale.value === 'bn' ? 'টুলস' : 'Tools', href: '/tools' },
    { label: t('nav.blog'), href: '/blog' },
    { label: t('plans_page.title'), href: '/plans' },
]);
</script>

<template>
    <div :lang="locale" class="public-shell min-h-screen flex flex-col bg-[var(--bg)] text-[var(--text)] transition-colors duration-200">
        <!-- Classical Nav Header -->
        <header class="public-header sticky top-0 z-40 border-b border-slate-200/80 dark:border-white/5 bg-white/95 dark:bg-[#0b0f19]/95 text-slate-900 dark:text-[#dfe2f1] backdrop-blur-xl shadow-xs transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 md:px-6 h-16 xl:h-18 flex items-center justify-between gap-4">
                <BrandLogo href="/" />

                <nav class="hidden xl:flex items-center gap-1 text-sm font-medium">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        :class="[
                            'px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5',
                            item.highlight
                                ? 'text-orange-600 dark:text-orange-400 font-semibold bg-orange-500/10 hover:bg-orange-500/15 border border-orange-500/30 shadow-xs'
                                : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-white/5'
                        ]"
                    >
                        <span>{{ item.label }}</span>
                        <span
                            v-if="item.badge"
                            class="px-1.5 py-0.5 text-[9.5px] font-black rounded-full bg-gradient-to-r from-rose-500 to-amber-500 text-white shadow-xs animate-pulse tracking-wide"
                        >
                            {{ item.badge }}
                        </span>
                    </Link>
                </nav>

                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-1 rounded-full bg-slate-100 dark:bg-[#171b26] border border-slate-200/80 dark:border-white/5 p-1">
                        <LanguageSwitcher />
                    </div>
                    <ThemeToggle />
                    <template v-if="student">
                        <Link href="/dashboard" class="btn btn-primary hidden sm:inline-flex h-9 px-4 text-xs font-bold rounded-xl">
                            {{ t('nav.dashboard') }}
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="btn btn-ghost hidden md:inline-flex h-9 px-3 text-xs text-slate-700 dark:text-[#dfe2f1] hover:text-slate-900 dark:hover:text-white">{{ t('nav.login') }}</Link>
                        <Link href="/register" class="btn btn-primary h-9 px-4 text-xs font-bold rounded-xl shadow-xs">{{ locale === 'bn' ? 'ফ্রি শুরু করুন' : 'Get Started Free' }}</Link>
                    </template>
                    <button
                        class="xl:hidden flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-white/5 text-slate-800 dark:text-white"
                        :aria-label="mobileOpen ? 'Close menu' : 'Open menu'"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <transition name="fade">
                <div v-if="mobileOpen" class="xl:hidden border-t border-slate-200 dark:border-white/10 px-5 py-4 bg-white dark:bg-[#111827] text-slate-900 dark:text-slate-100 shadow-xl">
                    <div class="flex flex-col gap-1 text-sm">
                        <Link
                            v-for="item in nav"
                            :key="item.href"
                            :href="item.href"
                            :class="[
                                'px-3 py-2 rounded-lg flex items-center justify-between font-medium',
                                item.highlight ? 'text-orange-600 dark:text-orange-400 font-semibold bg-orange-500/10' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5'
                            ]"
                            @click="mobileOpen = false"
                        >
                            <span>{{ item.label }}</span>
                            <span
                                v-if="item.badge"
                                class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-500/15 text-rose-500 dark:text-rose-400 border border-rose-500/20"
                            >
                                {{ item.badge }}
                            </span>
                        </Link>
                        <Link href="/contact" class="px-3 py-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 font-medium" @click="mobileOpen = false">{{ t('nav.contact') }}</Link>
                        <div class="my-2 border-t border-slate-200 dark:border-white/10"></div>
                        <div class="flex items-center gap-2 pt-1">
                            <LanguageSwitcher />
                            <Link v-if="!student" href="/login" class="flex-1 btn btn-outline h-9 text-xs" @click="mobileOpen = false">{{ t('nav.login') }}</Link>
                        </div>
                    </div>
                </div>
            </transition>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="public-footer border-t border-slate-200/80 dark:border-white/5 bg-slate-50/90 dark:bg-[#0c101c] text-slate-800 dark:text-[#dfe2f1] transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-5 md:px-6 py-10 md:py-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 xl:gap-12 pb-8">
                    <div class="sm:col-span-2 flex flex-col gap-4">
                        <BrandLogo href="/" size="normal" />
                        <p class="text-xs sm:text-sm leading-relaxed text-slate-600 dark:text-slate-400 max-w-sm">
                            {{ locale === 'bn' ? 'বই, মেন্টর পরিচালিত কোর্স এবং AI-সহায়ক স্টাডি টুল—আপনার পড়াশোনার প্রতিটি ধাপকে আরও কার্যকর ও সহজ করতে এক জায়গায়।' : 'Books, mentor-led courses, and AI study tools—together in one focused space to make every step of learning clearer.' }}
                        </p>
                        <div class="inline-flex items-center gap-2.5 rounded-full border border-orange-500/20 bg-orange-500/10 dark:bg-orange-500/15 px-3.5 py-1.5 text-xs font-semibold text-orange-700 dark:text-orange-300 w-fit shadow-xs">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span>{{ t('footer.badge') }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white mb-3">{{ locale === 'bn' ? 'প্ল্যাটফর্ম' : 'Platform' }}</div>
                        <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                            <li><Link href="/courses" class="hover:text-orange-500 transition-colors">{{ t('courses_page.title') }}</Link></li>
                            <li><Link href="/library" class="hover:text-orange-500 transition-colors">{{ t('nav.library') }}</Link></li>
                            <li><Link href="/question-bank" class="hover:text-orange-500 transition-colors">{{ locale === 'bn' ? 'প্রশ্নব্যাংক' : 'Question Bank' }}</Link></li>
                            <li><Link href="/admission-notice" class="hover:text-orange-500 transition-colors">{{ locale === 'bn' ? 'ভর্তি নোটিশ ২০২৬' : 'Admissions 2026' }}</Link></li>
                            <li><Link href="/job-news" class="hover:text-orange-500 transition-colors">{{ locale === 'bn' ? 'চাকরির খবর' : 'Job News' }}</Link></li>
                            <li><Link href="/tools" class="hover:text-orange-500 transition-colors">{{ locale === 'bn' ? 'স্টুডেন্ট টুলস' : 'Student Tools' }}</Link></li>
                            <li><Link href="/plans" class="hover:text-orange-500 transition-colors">{{ t('plans_page.title') }}</Link></li>
                        </ul>
                    </div>
                    <div>
                        <div class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white mb-3">{{ locale === 'bn' ? 'শিখুন সম্পর্কে' : 'About' }}</div>
                        <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                            <li><Link href="/p/how-it-works" class="hover:text-orange-500 transition-colors">{{ t('nav.how_it_works') }}</Link></li>
                            <li><Link href="/p/about" class="hover:text-orange-500 transition-colors">{{ t('nav.about') }}</Link></li>
                            <li><Link href="/p/faq" class="hover:text-orange-500 transition-colors">{{ t('nav.faq') }}</Link></li>
                            <li><Link href="/contact" class="hover:text-orange-500 transition-colors">{{ t('nav.contact') }}</Link></li>
                        </ul>
                    </div>
                    <div>
                        <div class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white mb-3">{{ locale === 'bn' ? 'যোগাযোগ' : 'Contact' }}</div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-3">{{ locale === 'bn' ? 'যেকোনো সহায়তায় আমাদের সাথে যোগাযোগ করুন।' : 'Questions about learning with Sikhun? Our team is here to help.' }}</p>
                        <Link href="/contact" class="inline-flex items-center gap-1.5 text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline">
                            <span>{{ locale === 'bn' ? 'যোগাযোগ পেজ' : 'Contact our team' }}</span>
                            <span>→</span>
                        </Link>
                    </div>
                </div>
                <div class="pt-5 border-t border-slate-200 dark:border-white/10 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400">
                    <span>© {{ new Date().getFullYear() }} {{ site?.name || 'Sikhun.com' }} — {{ t('footer.rights') }}</span>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <Link href="/p/terms" class="hover:text-slate-900 dark:hover:text-white">{{ t('footer.terms') }}</Link>
                        <Link href="/p/privacy" class="hover:text-slate-900 dark:hover:text-white">{{ t('footer.privacy') }}</Link>
                        <Link href="/p/faq" class="hover:text-slate-900 dark:hover:text-white">{{ locale === 'bn' ? 'সহায়তা' : 'Help' }}</Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <SupportWidget />
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.18s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
