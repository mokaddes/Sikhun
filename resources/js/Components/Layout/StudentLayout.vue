<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useThemeStore } from '@/Stores/theme';
import { useI18n } from '@/i18n';
import ThemeToggle from '@/Components/UI/ThemeToggle.vue';
import LanguageSwitcher from '@/Components/UI/LanguageSwitcher.vue';
import NotificationBell from '@/Components/UI/NotificationBell.vue';
import FlashBanner from '@/Components/UI/FlashBanner.vue';
import SupportWidget from '@/Components/Support/SupportWidget.vue';
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
    ChevronDoubleLeftIcon,
    MagnifyingGlassIcon,
    BellIcon,
    EnvelopeIcon,
    DocumentTextIcon,
} from '@heroicons/vue/24/outline';

const theme = useThemeStore();
const { t, locale, money, digits } = useI18n();
const page = usePage();
const student = page.props.auth?.student;
const site = page.props.site;
const brandName = computed(() => (site?.name || 'Sikhun.com').replace(/\.com.*$/i, '').toUpperCase());

const sidebarOpen = ref(false);   // mobile off-canvas drawer
const panelOpen = ref(false);     // second-level panel expanded?
const search = ref('');

// Reader pages host their own in-page chat widget in the bottom-right
// corner, so the floating support bot must not sit on top of it.
const hideSupport = computed(() => String(page.component ?? '').endsWith('/Reader'));

onMounted(() => theme.init());

const url = computed(() => page.url.split('?')[0]);

function isActive(href) {
    if (href === '/dashboard' || href === '/' || href === '/profile') return url.value === href;
    return url.value === href || url.value.startsWith(href + '/');
}

// First-level areas. "Dashboard" stands on its own (no submenu); every
// other area opens the contextual second level.
const nav = computed(() => [
    {
        id: 'dashboard',
        label: t('nav.dashboard'),
        icon: HomeIcon,
        href: '/dashboard',
        items: [],
    },
    {
        id: 'learn',
        label: t('nav.learn'),
        icon: BookOpenIcon,
        items: [
            { label: t('nav.library'), href: '/library', icon: BookOpenIcon },
            { label: t('nav.bookshelf'), href: '/bookshelf', icon: Squares2X2Icon },
            { label: t('nav.courses'), href: '/courses', icon: AcademicCapIcon },
        ],
    },
    {
        id: 'practice',
        label: t('nav.practice'),
        icon: ClipboardDocumentCheckIcon,
        items: [
            { label: t('nav.exams'), href: '/exams', icon: ClipboardDocumentCheckIcon },
            { label: t('nav.flashcards'), href: '/flashcards', icon: RectangleStackIcon },
            { label: t('nav.essays'), href: '/essays', icon: PencilSquareIcon },
            { label: t('nav.schedules'), href: '/schedules', icon: CalendarDaysIcon },
        ],
    },
    {
        id: 'ai',
        label: t('nav.ai_studio'),
        icon: SparklesIcon,
        items: [
            { label: t('nav.ai_chat'), href: '/ai/chat', icon: SparklesIcon },
        ],
    },
    {
        id: 'grow',
        label: t('nav.grow'),
        icon: TrophyIcon,
        items: [
            { label: t('nav.leaderboard'), href: '/leaderboard', icon: TrophyIcon },
            { label: t('nav.referrals'), href: '/referrals', icon: UserGroupIcon },
        ],
    },
    {
        id: 'account',
        label: t('nav.account'),
        icon: CreditCardIcon,
        items: [
            { label: t('nav.plans'), href: '/plans', icon: CreditCardIcon },
            { label: t('nav.wallet'), href: '/wallet', icon: WalletIcon },
            { label: t('nav.invoices'), href: '/invoices', icon: DocumentTextIcon },
            { label: t('nav.tickets'), href: '/support/tickets', icon: ShieldCheckIcon },
            { label: t('nav.feedback'), href: '/feedback', icon: PencilSquareIcon },
            { label: t('nav.access'), href: '/access', icon: ShieldCheckIcon },
        ],
    },
    {
        id: 'settings',
        label: t('nav.settings'),
        icon: Cog6ToothIcon,
        items: [
            { label: t('nav.notification_settings'), href: '/profile', icon: BellIcon },
            { label: t('nav.profile_settings'), href: '/profile/settings', icon: Cog6ToothIcon },
        ],
    },
]);

const activeGroupId = ref('dashboard');
const activeGroup = computed(() => nav.value.find(group => group.id === activeGroupId.value) ?? nav.value[0]);

function selectGroup(group) {
    if (group.items.length && group.id === activeGroupId.value) {
        panelOpen.value = !panelOpen.value;   // toggle the same area
        return;
    }
    activeGroupId.value = group.id;
    panelOpen.value = group.items.length > 0;  // Dashboard has none -> stays collapsed
}

let hoverTimer = null;
function onHoverGroup(group) {
    if (typeof window !== 'undefined' && window.innerWidth < 1024) return;
    window.clearTimeout(hoverTimer);
    hoverTimer = window.setTimeout(() => {
        activeGroupId.value = group.id;
        panelOpen.value = group.items.length > 0;
    }, 70);
}

function cancelHover() {
    window.clearTimeout(hoverTimer);
}

// Keep the rail + second level in sync with the current URL.
watch(url, () => {
    const match = nav.value.find(group => group.items.some(item => isActive(item.href)));
    if (match) {
        activeGroupId.value = match.id;
        panelOpen.value = true;
    } else if (url.value === '/dashboard') {
        activeGroupId.value = 'dashboard';
        panelOpen.value = false;
    }
}, { immediate: true });

const mobileNav = computed(() => [
    { label: t('nav.dashboard'), href: '/dashboard', icon: HomeIcon },
    { label: t('nav.library'), href: '/library', icon: BookOpenIcon },
    { label: t('nav.ai_chat'), href: '/ai/chat', icon: SparklesIcon },
    { label: t('nav.exams'), href: '/exams', icon: ClipboardDocumentCheckIcon },
    { label: t('nav.profile'), href: '/profile/settings', icon: Cog6ToothIcon },
]);

function submitSearch() {
    if (!search.value.trim()) return;
    router.get('/library', { search: search.value.trim() });
    search.value = '';
    sidebarOpen.value = false;
}

function logout() {
    router.post('/logout');
}
</script>

<template>
    <div class="flex min-h-screen bg-[var(--bg)] text-[var(--text)]">
        <aside
            class="fixed inset-y-0 left-0 z-50 flex -translate-x-full transform bg-[var(--surface)] shadow-[var(--shadow-lg)]
                   transition-transform duration-300 ease-out
                   lg:sticky lg:inset-y-auto lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-none"
            :class="sidebarOpen ? 'translate-x-0' : ''"
        >
            <!-- Level one: icon + label rail (menu scrolls, logout pinned at the bottom) -->
            <div class="flex h-full w-[5.5rem] shrink-0 flex-col border-r border-[var(--border)] bg-[var(--surface2)]/70">
                <nav class="no-scrollbar flex flex-1 flex-col items-center gap-1.5 overflow-y-auto px-2 py-4" @mouseleave="cancelHover">
                    <component
                        :is="group.href ? Link : 'button'"
                        v-for="group in nav"
                        :key="group.id"
                        :href="group.href"
                        type="button"
                        :title="group.label"
                        :aria-label="group.label"
                        :aria-current="activeGroupId === group.id ? 'page' : undefined"
                        class="relative flex w-full flex-col items-center gap-1 rounded-2xl px-1 py-2.5 text-[var(--text-muted)]
                               transition-all duration-200 hover:bg-[var(--surface)] hover:text-[var(--text)]"
                        :class="activeGroupId === group.id ? 'bg-[var(--secondary)]/12 font-semibold text-[var(--secondary)] shadow-sm ring-1 ring-[var(--secondary)]/15' : ''"
                        @click="selectGroup(group)"
                        @mouseenter="onHoverGroup(group)"
                    >
                        <component :is="group.icon" class="h-5 w-5" />
                        <span class="w-full text-center text-[10px] font-semibold leading-tight">{{ group.label }}</span>
                        <span v-if="activeGroupId === group.id" class="absolute -left-2 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-[var(--primary)]"></span>
                    </component>
                </nav>

                <div class="shrink-0 border-t border-[var(--border)] p-2">
                    <button
                        type="button"
                        :title="t('nav.logout')"
                        :aria-label="t('nav.logout')"
                        class="flex w-full flex-col items-center gap-1 rounded-2xl px-1 py-2.5 text-[var(--text-muted)] transition-all duration-200 hover:bg-[var(--accent)]/10 hover:text-[var(--accent)]"
                        @click="logout"
                    >
                        <ArrowRightStartOnRectangleIcon class="h-5 w-5" />
                        <span class="w-full text-center text-[10px] font-semibold leading-tight">{{ t('nav.logout') }}</span>
                    </button>
                </div>
            </div>

            <!-- Level two: contextual menu, collapses for areas without children -->
            <div
                class="shrink-0 overflow-hidden bg-[var(--surface)] transition-[width] duration-300 ease-out max-w-[calc(100vw-5.5rem)]"
                :style="{ width: panelOpen ? '16rem' : '0px' }"
            >
                <div class="flex h-full w-64 flex-col">
                    <header class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-[var(--border)] px-4">
                        <div class="flex min-w-0 items-center gap-2">
                            <img src="/images/sikhun-mark.svg" alt="" class="h-9 w-9 shrink-0 object-contain" />
                            <span class="truncate font-heading text-lg font-extrabold tracking-tight">{{ brandName }}</span>
                        </div>
                        <button
                            type="button"
                            class="icon-btn h-8 w-8 shrink-0"
                            :aria-label="locale === 'bn' ? 'মেনু গুটিয়ে নিন' : 'Collapse menu'"
                            @click="panelOpen = false"
                        >
                            <ChevronDoubleLeftIcon class="h-4 w-4" />
                        </button>
                    </header>

                    <nav class="flex-1 overflow-y-auto p-3">
                        <p class="truncate px-3 pb-2 pt-1 font-heading text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">
                            {{ activeGroup?.label }}
                        </p>
                        <div class="space-y-1">
                        <Link
                            v-for="item in activeGroup?.items ?? []"
                            :key="item.href"
                            :href="item.href"
                            class="group flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all
                                   hover:bg-[var(--surface2)] hover:text-[var(--text)]"
                            :class="isActive(item.href) ? 'bg-[var(--secondary)]/10 font-semibold text-[var(--secondary)] shadow-sm' : 'text-[var(--text-muted)]'"
                            @click="sidebarOpen = false"
                        >
                            <span
                                class="grid h-8 w-8 shrink-0 place-items-center rounded-lg transition-colors"
                                :class="isActive(item.href) ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--surface2)] text-[var(--text-muted)] group-hover:text-[var(--text)]'"
                            >
                                <component :is="item.icon" class="h-4 w-4" />
                            </span>
                            <span class="min-w-0 flex-1 truncate">{{ item.label }}</span>
                            <span v-if="isActive(item.href)" class="h-1.5 w-1.5 shrink-0 rounded-full bg-[var(--primary)]"></span>
                        </Link>
                        </div>
                    </nav>
                </div>
            </div>
        </aside>

        <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" />

        <div class="flex min-h-screen flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-[var(--border)] bg-[var(--bg)]/80 px-4 backdrop-blur-xl sm:px-6">
                <button class="icon-btn lg:hidden" aria-label="Open menu" @click="sidebarOpen = true">
                    <Bars3Icon class="h-5 w-5" />
                </button>

                <div class="hidden min-w-0 flex-1 lg:block">
                    <form class="group relative max-w-sm" @submit.prevent="submitSearch">
                        <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                        <input
                            v-model="search"
                            type="search"
                            :placeholder="t('common.search')"
                            class="w-full rounded-full border border-[var(--border)] bg-[var(--surface)] py-2 pl-9 pr-4 text-sm transition-all focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                        />
                    </form>
                </div>
                <div class="flex-1 lg:hidden" />

                <div class="flex items-center gap-2 sm:gap-2.5">
                    <div class="hidden items-center gap-1.5 rounded-full border border-[var(--secondary)]/20 bg-[var(--secondary)]/10 px-3.5 py-1.5 text-sm font-medium sm:flex">
                        <span class="text-[var(--text-muted)]">{{ t('dashboard.wallet_balance') }}:</span>
                        <span class="font-semibold">৳{{ money(student?.wallet_balance ?? 0, 2) }}</span>
                    </div>
                    <LanguageSwitcher />
                    <ThemeToggle />
                    <NotificationBell />
                    <Link
                        href="/profile/settings"
                        :title="t('nav.profile')"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--secondary)] text-sm font-semibold text-white ring-2 ring-[var(--secondary)]/20 transition hover:brightness-110"
                    >
                        {{ student?.name?.charAt(0) ?? 'S' }}
                    </Link>
                </div>
            </header>

            <main class="flex-1 px-4 pb-24 pt-6 sm:px-6 lg:px-8 lg:pb-10">
                <FlashBanner />

                <!-- 7-Day Free Trial Notice Banner -->
                <div
                    v-if="student && !student.has_active_subscription && student.is_in_trial"
                    class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-xs text-emerald-800 dark:text-emerald-300 shadow-sm"
                >
                    <div class="flex items-center gap-2.5">
                        <SparklesIcon class="h-4 w-4 text-emerald-500 shrink-0" />
                        <span>
                            <strong>{{ locale === 'en' ? '7-Day Free Trial Active' : '৭ দিনের ফ্রি ট্রায়াল চালু রয়েছে' }}</strong>
                            ({{ locale === 'en' ? `${student.trial_days_remaining} day${student.trial_days_remaining === 1 ? '' : 's'} remaining` : `বাকি ${digits(student.trial_days_remaining)} দিন` }}).
                            {{ locale === 'en' ? 'Enjoy full access to AI Chat, practice exams & book study.' : 'বই রিডার, এআই চ্যাট ও পরীক্ষার সম্পূর্ণ সুবিধা বিনামূল্যে ব্যবহার করুন।' }}
                        </span>
                    </div>
                    <Link href="/plans" class="btn btn-sm btn-primary rounded-xl font-bold shadow-sm">
                        {{ locale === 'en' ? 'Upgrade Plan →' : 'প্ল্যান আপগ্রেড করুন →' }}
                    </Link>
                </div>
                <div
                    v-else-if="student && !student.has_active_subscription && !student.is_in_trial"
                    class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-xs text-amber-800 dark:text-amber-300 shadow-sm"
                >
                    <div class="flex items-center gap-2.5">
                        <SparklesIcon class="h-4 w-4 text-amber-500 shrink-0" />
                        <span>
                            <strong>{{ locale === 'en' ? 'Free Trial Expired' : 'ফ্রি ট্রায়াল শেষ হয়ে গেছে' }}</strong>:
                            {{ locale === 'en' ? 'Subscribe to an affordable plan to continue unlimited AI study & exam preparation.' : 'সীমাহীন এআই চ্যাট ও পরীক্ষার প্রস্তুতি চালু রাখতে সাশ্রয়ী প্ল্যান বেছে নিন।' }}
                        </span>
                    </div>
                    <Link href="/plans" class="btn btn-sm btn-primary rounded-xl font-bold shadow-sm">
                        {{ locale === 'en' ? 'Choose a Plan →' : 'প্ল্যান বেছে নিন →' }}
                    </Link>
                </div>

                <!-- Email verification nudge. Non-blocking by design: students
                     can keep learning, but password reset and receipts work best
                     with a confirmed address. -->
                <div
                    v-if="student && student.email_verified === false"
                    class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-xs text-sky-800 shadow-sm dark:text-sky-300"
                >
                    <div class="flex items-center gap-2.5">
                        <EnvelopeIcon class="h-4 w-4 shrink-0 text-sky-500" />
                        <span>
                            {{ t('auth.email_unverified_banner') }}
                            {{ locale === 'en' ? 'Verify it so password resets and receipts reach you.' : 'পাসওয়ার্ড রিসেট ও রিসিট নিরাপদে পেতে যাচাই করুন।' }}
                        </span>
                    </div>
                    <Link href="/email/verify" class="rounded-xl border border-sky-500/40 px-3 py-1.5 font-bold transition hover:bg-sky-500/10">
                        {{ t('auth.verify_now') }}
                    </Link>
                </div>

                <Transition name="page" mode="out-in">
                    <div :key="url">
                        <slot />
                    </div>
                </Transition>
            </main>
        </div>

        <!-- Mobile bottom navigation -->
        <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-[var(--border)] bg-[var(--surface)]/95 backdrop-blur-xl lg:hidden">
            <div class="grid grid-cols-5">
                <Link
                    v-for="item in mobileNav"
                    :key="item.href"
                    :href="item.href"
                    class="relative flex flex-col items-center gap-1 py-2.5 text-[10px] font-medium transition-colors"
                    :class="isActive(item.href) ? 'text-[var(--secondary)]' : 'text-[var(--text-muted)]'"
                >
                    <component :is="item.icon" class="h-5 w-5" />
                    <span class="max-w-full truncate px-1">{{ item.label }}</span>
                    <span v-if="isActive(item.href)" class="absolute inset-x-5 top-0 h-0.5 rounded-full bg-[var(--primary)]"></span>
                </Link>
            </div>
        </nav>
    </div>
    <SupportWidget v-if="!hideSupport" />
</template>
