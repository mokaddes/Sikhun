<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useThemeStore } from '@/Stores/theme';
import { useI18n } from '@/i18n';
import ThemeToggle from '@/Components/UI/ThemeToggle.vue';
import LanguageSwitcher from '@/Components/UI/LanguageSwitcher.vue';
import NotificationBell from '@/Components/UI/NotificationBell.vue';
import FlashBanner from '@/Components/UI/FlashBanner.vue';
import SupportWidget from '@/Components/Support/SupportWidget.vue';

const theme = useThemeStore();
const { t } = useI18n();
const student = usePage().props.auth?.student;
const sidebarOpen = ref(false);

onMounted(() => theme.init());

const nav = computed(() => [
    { label: t('nav.dashboard'), href: '/dashboard' },
    { label: t('nav.library'), href: '/library' },
    { label: t('nav.courses'), href: '/courses' },
    { label: t('nav.bookshelf'), href: '/bookshelf' },
    { label: t('nav.ai_chat'), href: '/ai/chat' },
    { label: t('nav.exams'), href: '/exams' },
    { label: t('nav.flashcards'), href: '/flashcards' },
    { label: t('nav.essays'), href: '/essays' },
    { label: t('nav.schedules'), href: '/schedules' },
    { label: t('nav.leaderboard'), href: '/leaderboard' },
    { label: t('nav.referrals'), href: '/referrals' },
    { label: t('nav.plans'), href: '/plans' },
    { label: t('nav.wallet'), href: '/wallet' },
]);

function logout() {
    router.post('/logout');
}
</script>

<template>
    <div class="min-h-screen flex bg-[var(--bg)] text-[var(--text)]">
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 border-r border-[var(--border)] bg-[var(--surface)]
                   transform transition-transform duration-200 md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center gap-2 px-5 border-b border-[var(--border)] font-heading font-extrabold text-lg">
                <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-[var(--primary)] to-[var(--secondary)] flex items-center justify-center text-white text-sm">শি</span>
                Sikhun<span class="text-[var(--primary)]">.com</span>
            </div>

            <nav class="p-3 space-y-1">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                           text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--surface2)]
                           transition-colors"
                >
                    {{ item.label }}
                </Link>
            </nav>

            <div class="absolute bottom-0 inset-x-0 p-3 border-t border-[var(--border)]">
                <button @click="logout" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--surface2)] transition-colors">
                    {{ t('nav.logout') }}
                </button>
            </div>
        </aside>

        <div v-if="sidebarOpen" class="fixed inset-0 bg-black/40 z-20 md:hidden" @click="sidebarOpen = false" />

        <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
            <header class="h-16 sticky top-0 z-10 flex items-center justify-between px-5 border-b border-[var(--border)] bg-[var(--bg)]/90 backdrop-blur">
                <button class="md:hidden w-9 h-9 flex items-center justify-center rounded-lg border border-[var(--border)]" @click="sidebarOpen = true">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>

                <div class="flex-1" />

                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[var(--surface2)] text-sm font-medium">
                        <span class="text-[var(--text-muted)]">{{ t('dashboard.wallet_balance') }}:</span>
                        <span>৳{{ student?.wallet_balance ?? 0 }}</span>
                    </div>
                    <LanguageSwitcher />
                    <ThemeToggle />
                    <NotificationBell />
                    <Link href="/profile" :title="t('nav.profile')" class="w-9 h-9 rounded-full bg-[var(--primary)] text-white flex items-center justify-center text-sm font-semibold">
                        {{ student?.name?.charAt(0) ?? 'S' }}
                    </Link>
                </div>
            </header>

            <main class="flex-1 p-5 md:p-8">
                <FlashBanner />
                <slot />
            </main>
        </div>
    </div>
    <SupportWidget />
</template>
