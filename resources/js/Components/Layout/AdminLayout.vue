<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import LanguageSwitcher from '@/Components/UI/LanguageSwitcher.vue';
import FlashBanner from '@/Components/UI/FlashBanner.vue';
import BrandLogo from '@/Components/BrandLogo.vue';
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
    ArrowRightStartOnRectangleIcon,
    Bars3Icon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';

const { t } = useI18n();
const page = usePage();
const admin = page.props.auth?.admin;
const sidebarOpen = ref(false);

const url = computed(() => page.url.split('?')[0]);

function isActive(href) {
    if (href === '/admin') return url.value === '/admin';
    return url.value === href || url.value.startsWith(href + '/');
}

const nav = computed(() => [
    { label: 'Overview', href: '/admin', icon: Squares2X2Icon },
    {
        label: 'Content',
        items: [
            { label: t('admin.nav.books'), href: '/admin/books', icon: BookOpenIcon },
            { label: t('admin.nav.courses'), href: '/admin/courses', icon: AcademicCapIcon },
            { label: t('admin.nav.categories'), href: '/admin/categories', icon: TagIcon },
            { label: t('admin.nav.authors'), href: '/admin/authors', icon: UserIcon },
            { label: t('admin.nav.publications'), href: '/admin/publications', icon: NewspaperIcon },
            { label: t('admin.nav.pages'), href: '/admin/pages', icon: ClipboardDocumentCheckIcon },
        ],
    },
    {
        label: 'People & Plans',
        items: [
            { label: t('admin.nav.mentors'), href: '/admin/mentors', icon: UserGroupIcon },
            { label: t('admin.nav.students'), href: '/admin/students', icon: UsersIcon },
            { label: t('admin.nav.plans'), href: '/admin/plans', icon: CreditCardIcon },
            { label: t('admin.nav.coupons'), href: '/admin/coupons', icon: TicketIcon },
            { label: t('admin.nav.free_campaigns'), href: '/admin/free-campaigns', icon: MegaphoneIcon },
            { label: t('admin.nav.ai_providers'), href: '/admin/ai-providers', icon: BoltIcon },
        ],
    },
    {
        label: 'Operations',
        items: [
            { label: t('admin.nav.orders'), href: '/admin/orders', icon: InboxIcon },
            { label: 'Referrals', href: '/admin/referrals', icon: UsersIcon },
            { label: 'Leaderboard', href: '/admin/leaderboard', icon: QueueListIcon },
            { label: 'Notifications', href: '/admin/notifications', icon: MegaphoneIcon },
            { label: 'Support', href: '/admin/support', icon: ShieldCheckIcon },
            { label: t('admin.nav.settings'), href: '/admin/settings', icon: Cog6ToothIcon },
        ],
    },
]);

function logout() {
    router.post('/admin/logout');
}
</script>

<template>
    <div class="dark min-h-screen flex bg-[var(--bg)] text-[var(--text)]">
        <aside
            class="fixed inset-y-0 left-0 z-40 w-72 border-r border-[var(--border)] bg-[var(--surface)]
                   flex flex-col transform transition-transform duration-200 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center justify-between gap-2 px-5 border-b border-[var(--border)]">
                <BrandLogo href="/admin" size="text-lg" suffix="Admin" suffix-class="text-xs font-normal text-[var(--text-muted)]" />
                <button class="lg:hidden icon-btn w-8 h-8" aria-label="Close menu" @click="sidebarOpen = false">
                    <XMarkIcon class="w-5 h-5" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-3 space-y-5">
                <div>
                    <div class="space-y-0.5">
                        <Link
                            v-for="item in nav.filter((g) => !g.items)"
                            :key="item.href"
                            :href="item.href"
                            class="nav-link relative"
                            :class="isActive(item.href) ? '!text-[var(--secondary)] !bg-[var(--secondary)]/10 font-semibold' : ''"
                        >
                            <component :is="item.icon" class="w-5 h-5 shrink-0" />
                            <span class="truncate">{{ item.label }}</span>
                            <span v-if="isActive(item.href)" class="ml-auto h-1.5 w-1.5 rounded-full bg-[var(--primary)] shrink-0"></span>
                        </Link>
                    </div>
                </div>
                <div v-for="group in nav.filter((g) => g.items)" :key="group.label">
                    <div class="eyebrow px-3 mb-1.5">{{ group.label }}</div>
                    <div class="space-y-0.5">
                        <Link
                            v-for="item in group.items"
                            :key="item.href"
                            :href="item.href"
                            class="nav-link relative"
                            :class="isActive(item.href) ? '!text-[var(--secondary)] !bg-[var(--secondary)]/10 font-semibold' : ''"
                        >
                            <component :is="item.icon" class="w-5 h-5 shrink-0" />
                            <span class="truncate">{{ item.label }}</span>
                            <span v-if="isActive(item.href)" class="ml-auto h-1.5 w-1.5 rounded-full bg-[var(--primary)] shrink-0"></span>
                        </Link>
                    </div>
                </div>
            </nav>

            <div class="p-3 border-t border-[var(--border)]">
                <div class="px-3 py-1.5 text-xs text-[var(--text-muted)]">{{ admin?.name }} · {{ admin?.role }}</div>
                <button @click="logout" class="nav-link w-full !text-[var(--accent)]">
                    <ArrowRightStartOnRectangleIcon class="w-5 h-5 shrink-0" />
                    {{ t('nav.logout') }}
                </button>
            </div>
        </aside>

        <div v-if="sidebarOpen" class="fixed inset-0 bg-black/40 z-30 lg:hidden" @click="sidebarOpen = false" />

        <div class="flex-1 lg:ml-72 flex flex-col min-h-screen">
            <header class="h-16 sticky top-0 z-20 flex items-center justify-between gap-3 px-4 sm:px-6 border-b border-[var(--border)] bg-[var(--bg)]/90 backdrop-blur-md">
                <button class="lg:hidden icon-btn" aria-label="Open menu" @click="sidebarOpen = true">
                    <Bars3Icon class="w-5 h-5" />
                </button>
                <div class="flex-1" />
                <LanguageSwitcher />
            </header>

            <main class="flex-1 px-4 sm:px-6 lg:px-8 pt-6 pb-10">
                <FlashBanner />
                <slot />
            </main>
        </div>
    </div>
</template>