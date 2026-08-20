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
import BrandLogo from '@/Components/BrandLogo.vue';
import {
    HomeIcon,
    BookOpenIcon,
    Squares2X2Icon,
    AcademicCapIcon,
    ClipboardDocumentCheckIcon,
    RectangleStackIcon,
    PencilSquareIcon,
    CalendarDaysIcon,
    SparklesIcon,
    TrophyIcon,
    UserGroupIcon,
    CreditCardIcon,
    WalletIcon,
    ShieldCheckIcon,
    Cog6ToothIcon,
    ArrowRightStartOnRectangleIcon,
    Bars3Icon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';

const theme = useThemeStore();
const { t } = useI18n();
const page = usePage();
const student = page.props.auth?.student;
const sidebarOpen = ref(false);

onMounted(() => theme.init());

const url = computed(() => page.url.split('?')[0]);

function isActive(href) {
    if (href === '/dashboard') return url.value === '/dashboard';
    if (href === '/') return url.value === '/';
    return url.value === href || url.value.startsWith(href + '/');
}

const nav = computed(() => [
    {
        label: t('nav.learn'),
        items: [
            { label: t('nav.dashboard'), href: '/dashboard', icon: HomeIcon },
            { label: t('nav.library'), href: '/library', icon: BookOpenIcon },
            { label: t('nav.bookshelf'), href: '/bookshelf', icon: Squares2X2Icon },
            { label: t('nav.courses'), href: '/courses', icon: AcademicCapIcon },
        ],
    },
    {
        label: t('nav.practice'),
        items: [
            { label: t('nav.exams'), href: '/exams', icon: ClipboardDocumentCheckIcon },
            { label: t('nav.flashcards'), href: '/flashcards', icon: RectangleStackIcon },
            { label: t('nav.essays'), href: '/essays', icon: PencilSquareIcon },
            { label: t('nav.schedules'), href: '/schedules', icon: CalendarDaysIcon },
        ],
    },
    {
        label: t('nav.ai_studio'),
        items: [
            { label: t('nav.ai_chat'), href: '/ai/chat', icon: SparklesIcon },
        ],
    },
    {
        label: t('nav.grow'),
        items: [
            { label: t('nav.leaderboard'), href: '/leaderboard', icon: TrophyIcon },
            { label: t('nav.referrals'), href: '/referrals', icon: UserGroupIcon },
        ],
    },
    {
        label: t('nav.account'),
        items: [
            { label: t('nav.plans'), href: '/plans', icon: CreditCardIcon },
            { label: t('nav.wallet'), href: '/wallet', icon: WalletIcon },
            { label: 'Access', href: '/access', icon: ShieldCheckIcon },
            { label: t('nav.profile'), href: '/profile', icon: Cog6ToothIcon },
        ],
    },
]);

const mobileNav = computed(() => [
    { label: t('nav.dashboard'), href: '/dashboard', icon: HomeIcon },
    { label: t('nav.library'), href: '/library', icon: BookOpenIcon },
    { label: t('nav.ai_chat'), href: '/ai/chat', icon: SparklesIcon },
    { label: t('nav.exams'), href: '/exams', icon: ClipboardDocumentCheckIcon },
    { label: t('nav.profile'), href: '/profile', icon: Cog6ToothIcon },
]);

function logout() {
    router.post('/logout');
}
</script>

<template>
    <div class="min-h-screen flex bg-[var(--bg)] text-[var(--text)]">
        <aside
            class="fixed inset-y-0 left-0 z-40 w-72 border-r border-[var(--border)] bg-[var(--surface)]
                   flex flex-col transform transition-transform duration-200 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center justify-between gap-2 px-5 border-b border-[var(--border)]">
                <BrandLogo href="/dashboard" size="text-lg" />
                <button class="lg:hidden icon-btn w-8 h-8" aria-label="Close menu" @click="sidebarOpen = false">
                    <XMarkIcon class="w-5 h-5" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-3 space-y-5">
                <div v-for="group in nav" :key="group.label">
                    <div class="eyebrow px-3 mb-1.5">{{ group.label }}</div>
                    <div class="space-y-0.5">
                        <Link
                            v-for="item in group.items"
                            :key="item.href"
                            :href="item.href"
                            class="nav-link relative"
                            :class="isActive(item.href) ? '!text-[var(--secondary)] !bg-[var(--secondary)]/10 font-semibold' : ''"
                        >
                            <component :is="item.icon" class="w-5 h-5 shrink-0" :class="isActive(item.href) ? 'text-[var(--secondary)]' : ''" />
                            <span class="truncate">{{ item.label }}</span>
                            <span
                                v-if="isActive(item.href)"
                                class="ml-auto h-1.5 w-1.5 rounded-full bg-[var(--primary)] shrink-0"
                            ></span>
                        </Link>
                    </div>
                </div>
            </nav>

            <div class="p-3 border-t border-[var(--border)]">
                <button @click="logout" class="nav-link w-full !text-[var(--accent)]">
                    <ArrowRightStartOnRectangleIcon class="w-5 h-5 shrink-0" />
                    {{ t('nav.logout') }}
                </button>
            </div>
        </aside>

        <div v-if="sidebarOpen" class="fixed inset-0 bg-black/40 z-30 lg:hidden" @click="sidebarOpen = false" />

        <div class="flex-1 lg:ml-72 flex flex-col min-h-screen">
            <header class="h-16 sticky top-0 z-20 flex items-center justify-between gap-3 px-4 sm:px-6 border-b border-[var(--border)] bg-[var(--bg)]/85 backdrop-blur-md">
                <button class="lg:hidden icon-btn" aria-label="Open menu" @click="sidebarOpen = true">
                    <Bars3Icon class="w-5 h-5" />
                </button>

                <div class="flex-1" />

                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="hidden sm:flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[var(--secondary)]/10 border border-[var(--secondary)]/20 text-sm font-medium">
                        <span class="text-[var(--text-muted)]">{{ t('dashboard.wallet_balance') }}:</span>
                        <span class="font-semibold">৳{{ student?.wallet_balance ?? 0 }}</span>
                    </div>
                    <LanguageSwitcher />
                    <ThemeToggle />
                    <NotificationBell />
                    <Link href="/profile" :title="t('nav.profile')" class="w-9 h-9 rounded-full bg-[var(--secondary)] text-white flex items-center justify-center text-sm font-semibold ring-2 ring-[var(--secondary)]/20">
                        {{ student?.name?.charAt(0) ?? 'S' }}
                    </Link>
                </div>
            </header>

            <main class="flex-1 px-4 sm:px-6 lg:px-8 pt-6 pb-24 lg:pb-8">
                <FlashBanner />
                <slot />
            </main>
        </div>

        <!-- Mobile bottom navigation -->
        <nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 border-t border-[var(--border)] bg-[var(--surface)]/95 backdrop-blur-md">
            <div class="grid grid-cols-5">
                <Link
                    v-for="item in mobileNav"
                    :key="item.href"
                    :href="item.href"
                    class="flex flex-col items-center gap-1 py-2.5 text-[10px] font-medium"
                    :class="isActive(item.href) ? 'text-[var(--secondary)]' : 'text-[var(--text-muted)]'"
                >
                    <component :is="item.icon" class="w-5 h-5" />
                    <span class="truncate max-w-full px-1">{{ item.label }}</span>
                </Link>
            </div>
        </nav>
    </div>
    <SupportWidget />
</template>