<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import LanguageSwitcher from '@/Components/UI/LanguageSwitcher.vue';
import ThemeToggle from '@/Components/UI/ThemeToggle.vue';
import FlashBanner from '@/Components/UI/FlashBanner.vue';
import {
    Squares2X2Icon,
    BookOpenIcon,
    AcademicCapIcon,
    TagIcon,
    UserIcon,
    NewspaperIcon,
    UserGroupIcon,
    CreditCardIcon,
    TicketIcon,
    MegaphoneIcon,
    BoltIcon,
    InboxIcon,
    UsersIcon,
    QueueListIcon,
    ClipboardDocumentCheckIcon,
    ShieldCheckIcon,
    Cog6ToothIcon,
    ArrowTrendingUpIcon,
    ChatBubbleLeftRightIcon,
    ArrowRightStartOnRectangleIcon,
    Bars3Icon,
    ChevronDoubleLeftIcon,
    MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline';

const { t } = useI18n();
const page = usePage();
const admin = page.props.auth?.admin;
const site = page.props.site;
const brandName = computed(() => (site?.name || 'Sikhun.com').replace(/\.com.*$/i, '').toUpperCase());

const sidebarOpen = ref(false);   // mobile off-canvas drawer
const panelOpen = ref(false);     // second-level panel expanded?
const search = ref('');

const url = computed(() => page.url.split('?')[0]);

function isActive(href) {
    if (href === '/admin') return url.value === '/admin';
    return url.value === href || url.value.startsWith(href + '/');
}

// First-level areas except Dashboard are pure toggles (no URL). Each
// section's own list page lives as a second-level item so it stays reachable.
const nav = computed(() => [
    { id: 'dashboard', label: t('admin.nav.dashboard'), icon: Squares2X2Icon, href: '/admin', items: [] },
    {
        id: 'books',
        label: t('admin.nav.books'),
        icon: BookOpenIcon,
        items: [
            { label: t('admin.nav.categories'), href: '/admin/categories', icon: TagIcon },
            { label: t('admin.nav.authors'), href: '/admin/authors', icon: UserIcon },
            { label: t('admin.nav.publications'), href: '/admin/publications', icon: NewspaperIcon },
            { label: t('admin.nav.books'), href: '/admin/books', icon: BookOpenIcon },
        ],
    },
    {
        id: 'courses',
        label: t('admin.nav.courses'),
        icon: AcademicCapIcon,
        items: [
            { label: t('admin.nav.courses'), href: '/admin/courses', icon: AcademicCapIcon },
        ],
    },
    {
        id: 'students',
        label: t('admin.nav.students'),
        icon: UsersIcon,
        items: [
            { label: t('admin.nav.mentors'), href: '/admin/mentors', icon: UserGroupIcon },
            { label: t('admin.nav.students'), href: '/admin/students', icon: UsersIcon },
        ],
    },
    {
        id: 'orders',
        label: t('admin.nav.orders'),
        icon: InboxIcon,
        items: [
            { label: t('admin.nav.plans'), href: '/admin/plans', icon: CreditCardIcon },
            { label: t('admin.nav.coupons'), href: '/admin/coupons', icon: TicketIcon },
            { label: t('admin.nav.free_campaigns'), href: '/admin/free-campaigns', icon: MegaphoneIcon },
            { label: t('admin.nav.orders'), href: '/admin/orders', icon: InboxIcon },
        ],
    },
    {
        id: 'growth',
        label: t('admin.nav.growth'),
        icon: ArrowTrendingUpIcon,
        items: [
            { label: t('admin.nav.referrals'), href: '/admin/referrals', icon: UsersIcon },
            { label: t('admin.nav.leaderboard'), href: '/admin/leaderboard', icon: QueueListIcon },
        ],
    },
    {
        id: 'community',
        label: t('admin.nav.community'),
        icon: ChatBubbleLeftRightIcon,
        items: [
            { label: t('admin.nav.notifications'), href: '/admin/notifications', icon: MegaphoneIcon },
            { label: t('admin.nav.support'), href: '/admin/support', icon: ShieldCheckIcon },
        ],
    },
    {
        id: 'platform',
        label: t('admin.nav.platform'),
        icon: Cog6ToothIcon,
        items: [
            { label: t('admin.nav.pages'), href: '/admin/pages', icon: ClipboardDocumentCheckIcon },
            { label: t('admin.nav.ai_providers'), href: '/admin/ai-providers', icon: BoltIcon },
            { label: t('admin.nav.settings'), href: '/admin/settings', icon: Cog6ToothIcon },
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
    panelOpen.value = group.items.length > 0;  // sections without children stay collapsed
}

// Keep the rail + second level in sync with the current URL.
watch(url, () => {
    const match = nav.value.find(group => group.items.some(item => isActive(item.href)));
    if (match) {
        activeGroupId.value = match.id;
        panelOpen.value = true;
    } else if (url.value === '/admin') {
        activeGroupId.value = 'dashboard';
        panelOpen.value = false;
    }
}, { immediate: true });

const mobileNav = computed(() => [
    { label: t('admin.nav.dashboard'), href: '/admin', icon: Squares2X2Icon },
    { label: t('admin.nav.books'), href: '/admin/books', icon: BookOpenIcon },
    { label: t('admin.nav.students'), href: '/admin/students', icon: UsersIcon },
    { label: t('admin.nav.orders'), href: '/admin/orders', icon: InboxIcon },
    { label: t('admin.nav.settings'), href: '/admin/settings', icon: Cog6ToothIcon },
]);

function submitSearch() {
    if (!search.value.trim()) return;
    router.get('/admin/students', { search: search.value.trim() });
    search.value = '';
    sidebarOpen.value = false;
}

function logout() {
    router.post('/admin/logout');
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
                <nav class="no-scrollbar flex flex-1 flex-col items-center gap-1.5 overflow-y-auto px-2 py-4">
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
                            <div class="min-w-0 leading-tight">
                                <div class="truncate font-heading text-base font-extrabold tracking-tight">{{ brandName }}</div>
                                <div class="text-[10px] font-semibold uppercase tracking-widest text-[var(--text-muted)]">Admin</div>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="icon-btn h-8 w-8 shrink-0"
                            aria-label="Collapse menu"
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
                            placeholder="Search students..."
                            class="w-full rounded-full border border-[var(--border)] bg-[var(--surface)] py-2 pl-9 pr-4 text-sm transition-all focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20"
                        />
                    </form>
                </div>
                <div class="flex-1 lg:hidden" />

                <div class="flex items-center gap-2 sm:gap-2.5">
                    <LanguageSwitcher />
                    <ThemeToggle />
                    <Link
                        href="/admin/profile"
                        :title="admin?.name"
                        class="flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--surface)] py-1 pl-1 pr-3 transition hover:border-[var(--primary)]/40"
                        :class="isActive('/admin/profile') ? 'border-[var(--primary)]/40' : ''"
                    >
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-[var(--secondary)] text-xs font-semibold text-white">
                            {{ admin?.name?.charAt(0) ?? 'A' }}
                        </span>
                        <span class="hidden text-xs font-medium sm:block">{{ admin?.name }}</span>
                    </Link>
                </div>
            </header>

            <main class="flex-1 px-4 pb-24 pt-6 sm:px-6 lg:px-8 lg:pb-10">
                <FlashBanner />
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
</template>
