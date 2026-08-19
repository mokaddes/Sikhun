<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import LanguageSwitcher from '@/Components/UI/LanguageSwitcher.vue';
import FlashBanner from '@/Components/UI/FlashBanner.vue';
import BrandLogo from '@/Components/BrandLogo.vue';

const { t } = useI18n();
const admin = usePage().props.auth?.admin;
const sidebarOpen = ref(false);

const nav = computed(() => [
    { label: t('admin.nav.dashboard'), href: '/admin' },
    { label: t('admin.nav.books'), href: '/admin/books' },
    { label: t('admin.nav.courses'), href: '/admin/courses' },
    { label: t('admin.nav.categories'), href: '/admin/categories' },
    { label: t('admin.nav.authors'), href: '/admin/authors' },
    { label: t('admin.nav.publications'), href: '/admin/publications' },
    { label: t('admin.nav.mentors'), href: '/admin/mentors' },
    { label: t('admin.nav.students'), href: '/admin/students' },
    { label: t('admin.nav.plans'), href: '/admin/plans' },
    { label: t('admin.nav.coupons'), href: '/admin/coupons' },
    { label: t('admin.nav.free_campaigns'), href: '/admin/free-campaigns' },
    { label: t('admin.nav.ai_providers'), href: '/admin/ai-providers' },
    { label: t('admin.nav.orders'), href: '/admin/orders' },
    { label: 'Referrals', href: '/admin/referrals' },
    { label: 'Leaderboard', href: '/admin/leaderboard' },
    { label: 'Notifications', href: '/admin/notifications' },
    { label: 'Support', href: '/admin/support' },
    { label: t('admin.nav.pages'), href: '/admin/pages' },
    { label: t('admin.nav.settings'), href: '/admin/settings' },
]);

function logout() {
    router.post('/admin/logout');
}
</script>

<template>
    <!-- Admin panel is intentionally fixed dark for focus, but still
         supports EN/BN via the same LanguageSwitcher used site-wide. -->
    <div class="dark min-h-screen flex bg-[#09090f] text-[#e8e8f0]">
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 border-r border-[#2a2a38] bg-[#111118]
                   bg-gradient-to-b from-[#151520] to-[#0c0c14] overflow-y-auto
                   transform transition-transform duration-200 md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center gap-2 px-5 border-b border-[#2a2a38] sticky top-0 bg-[#111118] z-10">
                <BrandLogo href="/admin" size="text-lg" suffix="Admin" suffix-class="text-xs font-normal text-[#7a7a9a]" img-class="h-8 w-auto max-w-[150px]" />
            </div>

            <nav class="p-3 space-y-1 pb-24">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                           text-[#9a9ab8] hover:text-white hover:bg-[#1c1c26] transition-colors"
                >
                    {{ item.label }}
                </Link>
            </nav>

            <div class="absolute bottom-0 inset-x-0 p-3 border-t border-[#2a2a38] bg-[#111118]">
                <div class="px-3 py-2 text-xs text-[#7a7a9a]">{{ admin?.name }} · {{ admin?.role }}</div>
                <button @click="logout" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-[#9a9ab8] hover:text-[#ff6b6b] hover:bg-[#1c1c26] transition-colors">
                    {{ t('nav.logout') }}
                </button>
            </div>
        </aside>

        <div v-if="sidebarOpen" class="fixed inset-0 bg-black/40 z-20 md:hidden" @click="sidebarOpen = false" />

        <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
            <header class="h-16 sticky top-0 z-10 flex items-center justify-between px-5 border-b border-[#2a2a38] bg-[#09090f]/90 backdrop-blur">
                <button class="md:hidden w-9 h-9 flex items-center justify-center rounded-lg border border-[#2a2a38]" @click="sidebarOpen = true">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <div class="flex-1" />
                <LanguageSwitcher />
            </header>

            <main class="flex-1 p-5 md:p-8">
                <FlashBanner />
                <slot />
            </main>
        </div>
    </div>
</template>
