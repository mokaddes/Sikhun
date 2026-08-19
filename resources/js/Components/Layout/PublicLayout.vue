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
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[var(--bg)] text-[var(--text)]">
        <header class="sticky top-0 z-40 border-b border-[var(--border)] bg-[var(--bg)]/90 backdrop-blur">
            <div class="max-w-7xl mx-auto px-5 h-16 flex items-center justify-between">
                <BrandLogo href="/" />

                <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-[var(--text-muted)]">
                    <Link href="/courses" class="hover:text-[var(--text)] transition-colors">{{ t('courses_page.title') }}</Link>
                    <Link href="/library" class="hover:text-[var(--text)] transition-colors">{{ t('nav.library') }}</Link>
                    <Link href="/#pricing" class="hover:text-[var(--text)] transition-colors">{{ t('plans_page.title') }}</Link>
                    <Link href="/p/about" class="hover:text-[var(--text)] transition-colors">{{ t('nav.about') }}</Link>
                    <Link href="/p/faq" class="hover:text-[var(--text)] transition-colors">{{ t('nav.faq') }}</Link>
                </nav>

                <div class="flex items-center gap-3">
                    <LanguageSwitcher />
                    <ThemeToggle />
                    <template v-if="student">
                        <Link href="/dashboard" class="hidden sm:inline-flex items-center px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold transition-colors">
                            {{ t('nav.dashboard') }}
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="hidden sm:inline-flex text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text)]">{{ t('nav.login') }}</Link>
                        <Link href="/register" class="inline-flex items-center px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold transition-colors">
                            {{ t('nav.get_started') }}
                        </Link>
                    </template>
                    <button class="md:hidden w-9 h-9 flex items-center justify-center rounded-lg border border-[var(--border)]" @click="mobileOpen = !mobileOpen">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                </div>
            </div>

            <div v-if="mobileOpen" class="md:hidden border-t border-[var(--border)] px-5 py-4 flex flex-col gap-3 text-sm bg-[var(--surface)]">
                <Link href="/courses">{{ t('courses_page.title') }}</Link>
                <Link href="/library">{{ t('nav.library') }}</Link>
                <Link href="/#pricing">{{ t('plans_page.title') }}</Link>
                <Link href="/p/about">{{ t('nav.about') }}</Link>
                <Link href="/p/faq">{{ t('nav.faq') }}</Link>
                <Link href="/contact">{{ t('nav.contact') }}</Link>
                <Link v-if="!student" href="/login">{{ t('nav.login') }}</Link>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-[var(--border)] bg-[var(--surface2)] mt-8">
            <div class="max-w-7xl mx-auto px-5 py-12 grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
                <div class="col-span-2 md:col-span-1">
                    <BrandLogo href="/" compact size="text-lg" />
                    <p class="text-[var(--text-muted)]">{{ t('footer.tagline') }}</p>
                </div>
                <div>
                    <div class="font-semibold mb-3">{{ t('footer.platform') }}</div>
                    <ul class="space-y-2 text-[var(--text-muted)]">
                        <li><Link href="/courses">{{ t('courses_page.title') }}</Link></li>
                        <li><Link href="/library">{{ t('nav.library') }}</Link></li>
                        <li><Link href="/#pricing">{{ t('plans_page.title') }}</Link></li>
                        <li><Link href="/p/how-it-works">{{ t('nav.how_it_works') }}</Link></li>
                    </ul>
                </div>
                <div>
                    <div class="font-semibold mb-3">{{ t('footer.organization') }}</div>
                    <ul class="space-y-2 text-[var(--text-muted)]">
                        <li><Link href="/p/about">{{ t('nav.about') }}</Link></li>
                        <li><Link href="/p/faq">{{ t('nav.faq') }}</Link></li>
                        <li><Link href="/contact">{{ t('nav.contact') }}</Link></li>
                    </ul>
                </div>
                <div>
                    <div class="font-semibold mb-3">{{ t('footer.legal') }}</div>
                    <ul class="space-y-2 text-[var(--text-muted)]">
                        <li><Link href="/p/terms">{{ t('footer.terms') }}</Link></li>
                        <li><Link href="/p/privacy">{{ t('footer.privacy') }}</Link></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-[var(--border)] py-5 text-center text-xs text-[var(--text-muted)]">
                © {{ new Date().getFullYear() }} {{ site?.name || 'Sikhun.com' }} — {{ t('footer.rights') }}
            </div>
        </footer>
    </div>
    <SupportWidget />
</template>
