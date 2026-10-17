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
    { label: t('nav.blog'), href: '/blog' },
    { label: locale.value === 'bn' ? 'AI স্টাডি হাব' : 'AI Study Hub', href: '/#ai-study-hub' },
    { label: t('plans_page.title'), href: '/#pricing' },
    { label: t('nav.about'), href: '/p/about' },
]);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[var(--bg)] text-[var(--text)]">
        <header class="public-header sticky top-0 z-40 border-b border-white/5 bg-[#0b0f19]/90 text-[#dfe2f1] backdrop-blur-xl shadow-[0_1px_12px_rgba(0,0,0,.12)]">
            <div class="max-w-7xl mx-auto px-4 md:px-6 h-16 xl:h-20 flex items-center justify-between gap-4">
                <BrandLogo href="/" />

                <nav class="hidden xl:flex items-center gap-1 text-sm font-medium text-[#a2aabc]">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="px-3 py-2 rounded-lg hover:text-white hover:bg-white/5 transition-all"
                    >{{ item.label }}</Link>
                </nav>

                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-1 rounded-full bg-[#171b26] p-1">
                        <LanguageSwitcher />
                    </div>
                    <ThemeToggle />
                    <template v-if="student">
                        <Link href="/dashboard" class="btn btn-primary hidden sm:inline-flex h-10 px-5 text-[13px]">
                            {{ t('nav.dashboard') }}
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="btn btn-ghost hidden md:inline-flex h-10 px-4 text-[#dfe2f1]">{{ t('nav.login') }}</Link>
                        <Link href="/register" class="btn btn-primary h-10 px-5 rounded-full shadow-[0_0_22px_rgba(249,115,22,.25)]">{{ locale === 'bn' ? 'ফ্রি শুরু করুন' : 'Get Started Free' }}</Link>
                    </template>
                    <button
                        class="xl:hidden flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white"
                        :aria-label="mobileOpen ? 'Close menu' : 'Open menu'"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <transition name="fade">
                <div v-if="mobileOpen" class="xl:hidden border-t border-white/10 px-5 py-4 bg-[#111827] text-[var(--text)]">
                    <div class="flex flex-col gap-1 text-sm">
                        <Link
                            v-for="item in nav"
                            :key="item.href"
                            :href="item.href"
                            class="nav-link text-[var(--text-muted)] hover:text-[var(--text)]"
                            @click="mobileOpen = false"
                        >{{ item.label }}</Link>
                        <Link href="/contact" class="nav-link text-[var(--text-muted)] hover:text-[var(--text)]" @click="mobileOpen = false">{{ t('nav.contact') }}</Link>
                        <div class="my-2 dot-seam"></div>
                        <Link v-if="!student" href="/login" class="btn btn-outline" @click="mobileOpen = false">{{ t('nav.login') }}</Link>
                    </div>
                </div>
            </transition>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="public-footer border-t border-white/5 bg-[#111827] text-[#dfe2f1]">
            <div class="max-w-7xl mx-auto px-5 md:px-6 py-14 md:py-16">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 xl:gap-14 pb-12">
                    <div class="sm:col-span-2 flex flex-col gap-5">
                        <BrandLogo href="/" compact size="text-lg" />
                        <p class="text-sm leading-relaxed text-[#a2aabc] max-w-sm">{{ locale === 'bn' ? 'বই, মেন্টর পরিচালিত কোর্স এবং AI-সহায়ক স্টাডি টুল—আপনার শেখার প্রতিটি ধাপকে আরও পরিষ্কার ও সহজ করতে এক জায়গায়।' : 'Books, mentor-led courses, and AI-supported study tools—together in one focused space to make every step of learning clearer.' }}</p>
                        <div class="flex items-center gap-2 text-xs font-mono text-[#a2aabc]"><span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>{{ locale === 'bn' ? 'আপনার শেখার যাত্রা এখান থেকেই শুরু' : 'YOUR LEARNING JOURNEY STARTS HERE' }}</div>
                    </div>
                    <div>
                        <div class="font-semibold mb-4">{{ locale === 'bn' ? 'প্ল্যাটফর্ম' : 'Platform' }}</div>
                        <ul class="space-y-3 text-sm text-[#a2aabc]"><li><Link href="/courses" class="hover:text-[var(--primary)] transition-colors">{{ t('courses_page.title') }}</Link></li><li><Link href="/library" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.library') }}</Link></li><li><Link href="/blog" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.blog') }}</Link></li><li><Link href="/#ai-study-hub" class="hover:text-[var(--primary)] transition-colors">{{ locale === 'bn' ? 'AI স্টাডি হাব' : 'AI Study Hub' }}</Link></li><li><Link href="/#pricing" class="hover:text-[var(--primary)] transition-colors">{{ t('plans_page.title') }}</Link></li></ul>
                    </div>
                    <div>
                        <div class="font-semibold mb-4">{{ locale === 'bn' ? 'আরও জানুন' : 'Explore Sikhun' }}</div>
                        <ul class="space-y-3 text-sm text-[#a2aabc]"><li><Link href="/p/how-it-works" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.how_it_works') }}</Link></li><li><Link href="/p/about" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.about') }}</Link></li><li><Link href="/p/faq" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.faq') }}</Link></li><li><Link href="/contact" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.contact') }}</Link></li></ul>
                    </div>
                    <div>
                        <div class="font-semibold mb-4">{{ locale === 'bn' ? 'যোগাযোগ রাখুন' : 'Stay Connected' }}</div>
                        <p class="text-sm text-[#a2aabc] leading-relaxed mb-4">{{ locale === 'bn' ? 'প্ল্যাটফর্ম সম্পর্কে জানতে বা সহায়তা পেতে আমাদের সাথে যোগাযোগ করুন।' : 'Questions about learning with Sikhun? Our team is here to help.' }}</p>
                        <Link href="/contact" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--primary)] hover:text-[#ffb690]">{{ locale === 'bn' ? 'যোগাযোগ করুন' : 'Contact our team' }} <span>→</span></Link>
                    </div>
                </div>
                <div class="pt-6 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-5">
                    <span class="text-xs text-[#a2aabc]">© {{ new Date().getFullYear() }} {{ site?.name || 'Sikhun.com' }} — {{ t('footer.rights') }}</span>
                    <div class="flex flex-wrap items-center justify-center gap-4 text-xs text-[#a2aabc]"><Link href="/p/terms" class="hover:text-white">{{ t('footer.terms') }}</Link><Link href="/p/privacy" class="hover:text-white">{{ t('footer.privacy') }}</Link><Link href="/p/faq" class="hover:text-white">{{ locale === 'bn' ? 'নিরাপত্তা ও সহায়তা' : 'Help & safety' }}</Link></div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-5 pb-6 flex flex-wrap items-center justify-center gap-3 text-xs text-[#a2aabc]">
                <span>{{ locale === 'bn' ? 'দেখার আকার' : 'Display size' }}</span>
                <div class="inline-flex items-center gap-1 rounded-full border border-[var(--border)] p-1">
                    <span class="px-2">{{ locale === 'bn' ? 'লেখা' : 'Text' }}</span>
                    <button class="display-control" aria-label="Decrease text size" @click="adjustDisplay('text', -0.05)">A−</button>
                    <button class="display-control" aria-label="Increase text size" @click="adjustDisplay('text', 0.05)">A+</button>
                </div>
                <div class="inline-flex items-center gap-1 rounded-full border border-[var(--border)] p-1">
                    <span class="px-2">{{ locale === 'bn' ? 'বাটন' : 'Buttons' }}</span>
                    <button class="display-control" aria-label="Decrease button size" @click="adjustDisplay('buttons', -0.05)">−</button>
                    <button class="display-control" aria-label="Increase button size" @click="adjustDisplay('buttons', 0.05)">+</button>
                </div>
            </div>
        </footer>
    </div>
    <SupportWidget />


</template>
<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.18s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.display-control { min-width: 2rem; height: 2rem; border-radius: 9999px; color: var(--text); background: var(--surface2); font-weight: 700; }
.display-control:hover { background: var(--primary); color: white; }
</style>
