<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import ThemeToggle from '@/Components/UI/ThemeToggle.vue';
import LanguageSwitcher from '@/Components/UI/LanguageSwitcher.vue';
import SupportWidget from '@/Components/Support/SupportWidget.vue';
import BrandLogo from '@/Components/BrandLogo.vue';

const { t } = useI18n();
const mobileOpen = ref(false);
const student = usePage().props.auth?.student;
const site = usePage().props.site;

const nav = [
    { label: 'courses', href: '/courses' },
    { label: 'library', href: '/library' },
    { label: 'pricing', href: '/#pricing', tKey: 'plans_page.title' },
    { label: 'about', href: '/p/about' },
    { label: 'faq', href: '/p/faq' },
];
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[var(--bg)] text-[var(--text)]">
        <header class="sticky top-0 z-40 border-b border-[var(--border)] bg-[var(--bg)]/85 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-5 h-16 flex items-center justify-between gap-4">
                <BrandLogo href="/" />

                <nav class="hidden lg:flex items-center gap-1 text-sm font-medium text-[var(--text-muted)]">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="relative px-3.5 py-2 rounded-full hover:text-[var(--text)] transition-colors after:absolute after:left-1/2 after:-translate-x-1/2 after:-bottom-0.5 after:h-1 after:w-1 after:rounded-full after:bg-[var(--primary)] after:opacity-0 after:transition-opacity hover:after:opacity-100"
                    >{{ t(item.tKey || `nav.${item.label}`) }}</Link>
                </nav>

                <div class="flex items-center gap-2">
                    <LanguageSwitcher />
                    <ThemeToggle />
                    <template v-if="student">
                        <Link href="/dashboard" class="btn btn-primary hidden sm:inline-flex h-9 px-4 text-[13px]">
                            {{ t('nav.dashboard') }}
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="btn btn-ghost hidden sm:inline-flex h-9 px-4 text-[13px]">{{ t('nav.login') }}</Link>
                        <Link href="/register" class="btn btn-primary h-9 px-4 text-[13px]">{{ t('nav.get_started') }}</Link>
                    </template>
                    <button
                        class="lg:hidden icon-btn"
                        :aria-label="mobileOpen ? 'Close menu' : 'Open menu'"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <transition name="fade">
                <div v-if="mobileOpen" class="lg:hidden border-t border-[var(--border)] px-5 py-4 bg-[var(--surface)]">
                    <div class="flex flex-col gap-1 text-sm">
                        <Link
                            v-for="item in nav"
                            :key="item.href"
                            :href="item.href"
                            class="nav-link"
                            @click="mobileOpen = false"
                        >{{ t(item.tKey || `nav.${item.label}`) }}</Link>
                        <Link href="/contact" class="nav-link" @click="mobileOpen = false">{{ t('nav.contact') }}</Link>
                        <div class="my-2 dot-seam"></div>
                        <Link v-if="!student" href="/login" class="btn btn-outline" @click="mobileOpen = false">{{ t('nav.login') }}</Link>
                    </div>
                </div>
            </transition>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-[var(--border)] bg-[var(--surface)] mt-8">
            <div class="max-w-7xl mx-auto px-5 pt-14 pb-8 grid grid-cols-2 md:grid-cols-4 gap-10 text-sm">
                <div class="col-span-2 md:col-span-1">
                    <BrandLogo href="/" compact size="text-lg" />
                    <p class="text-[var(--text-muted)] mt-3 max-w-[220px]">{{ t('footer.tagline') }}</p>

                </div>
                <div>
                    <div class="eyebrow mb-4">{{ t('footer.platform') }}</div>
                    <ul class="space-y-2.5 text-[var(--text-muted)]">
                        <li><Link href="/courses" class="hover:text-[var(--primary)] transition-colors">{{ t('courses_page.title') }}</Link></li>
                        <li><Link href="/library" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.library') }}</Link></li>
                        <li><Link href="/#pricing" class="hover:text-[var(--primary)] transition-colors">{{ t('plans_page.title') }}</Link></li>
                        <li><Link href="/p/how-it-works" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.how_it_works') }}</Link></li>
                    </ul>
                </div>
                <div>
                    <div class="eyebrow mb-4">{{ t('footer.organization') }}</div>
                    <ul class="space-y-2.5 text-[var(--text-muted)]">
                        <li><Link href="/p/about" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.about') }}</Link></li>
                        <li><Link href="/p/faq" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.faq') }}</Link></li>
                        <li><Link href="/contact" class="hover:text-[var(--primary)] transition-colors">{{ t('nav.contact') }}</Link></li>
                    </ul>
                </div>
                <div>
                    <div class="eyebrow mb-4">{{ t('footer.legal') }}</div>
                    <ul class="space-y-2.5 text-[var(--text-muted)]">
                        <li><Link href="/p/terms" class="hover:text-[var(--primary)] transition-colors">{{ t('footer.terms') }}</Link></li>
                        <li><Link href="/p/privacy" class="hover:text-[var(--primary)] transition-colors">{{ t('footer.privacy') }}</Link></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-[var(--border)] py-5 px-5 text-center text-xs text-[var(--text-muted)]">
                © {{ new Date().getFullYear() }} {{ site?.name || 'Sikhun.com' }} — {{ t('footer.rights') }}
            </div>
        </footer>
    </div>
    <SupportWidget />


</template>
<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.18s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>