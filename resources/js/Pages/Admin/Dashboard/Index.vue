<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import VueApexCharts from 'vue3-apexcharts';
import { useI18n } from '@/i18n';
import { useThemeStore } from '@/Stores/theme';
import {
    UsersIcon,
    BookOpenIcon,
    CreditCardIcon,
    BanknotesIcon,
    AcademicCapIcon,
    InboxIcon,
    Cog6ToothIcon,
    ArrowRightIcon,
    CalendarDaysIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ stats: Object, charts: Object });
const { t, locale } = useI18n();
const theme = useThemeStore();
const admin = usePage().props.auth?.admin;

const isDark = computed(() => theme.mode === 'dark');
const axisColor = computed(() => (isDark.value ? '#a2aabc' : '#657188'));
const gridColor = computed(() => (isDark.value ? '#303644' : '#dce3ee'));

const statCards = computed(() => [
    { label: t('admin.dashboard.total_students'), value: props.stats.total_students, icon: UsersIcon, tint: 'bg-[var(--secondary)]/10 text-[var(--secondary)]', href: '/admin/students' },
    { label: t('admin.dashboard.total_books'), value: props.stats.total_books, icon: BookOpenIcon, tint: 'bg-[var(--primary)]/10 text-[var(--primary)]', href: '/admin/books' },
    { label: t('admin.dashboard.active_subscriptions'), value: props.stats.active_subscriptions, icon: CreditCardIcon, tint: 'bg-[var(--tertiary)]/10 text-[var(--tertiary)]', href: '/admin/plans' },
    { label: t('admin.dashboard.revenue_total'), value: `৳${props.stats.revenue_total}`, icon: BanknotesIcon, tint: 'bg-[var(--accent)]/10 text-[var(--accent)]', href: '/admin/orders' },
]);

const quickActions = [
    { label: t('admin.nav.books'), href: '/admin/books', icon: BookOpenIcon },
    { label: t('admin.nav.students'), href: '/admin/students', icon: UsersIcon },
    { label: t('admin.nav.orders'), href: '/admin/orders', icon: InboxIcon },
    { label: t('admin.nav.settings'), href: '/admin/settings', icon: Cog6ToothIcon },
];

const revenueSeries = computed(() => [{ name: 'Revenue', data: props.charts.revenue }]);
const revenueOptions = computed(() => ({
    chart: { type: 'area', toolbar: { show: false }, background: 'transparent', foreColor: axisColor.value },
    theme: { mode: isDark.value ? 'dark' : 'light' },
    colors: ['#f97316'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2.5 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 90, 100] } },
    xaxis: { categories: props.charts.labels, labels: { style: { colors: axisColor.value } }, axisBorder: { show: false }, axisTicks: { show: false } },
    yaxis: { labels: { style: { colors: axisColor.value } } },
    grid: { borderColor: gridColor.value },
}));

const regSeries = computed(() => [{ name: 'Registrations', data: props.charts.registrations }]);
const regOptions = computed(() => ({
    chart: { type: 'bar', toolbar: { show: false }, background: 'transparent', foreColor: axisColor.value },
    theme: { mode: isDark.value ? 'dark' : 'light' },
    colors: ['#2757cc'],
    dataLabels: { enabled: false },
    plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
    xaxis: { categories: props.charts.labels, labels: { style: { colors: axisColor.value } }, axisBorder: { show: false }, axisTicks: { show: false } },
    yaxis: { labels: { style: { colors: axisColor.value } } },
    grid: { borderColor: gridColor.value },
}));

const today = computed(() => new Date().toLocaleDateString(locale.value === 'bn' ? 'bn-BD' : 'en-GB', {
    weekday: 'long', day: 'numeric', month: 'long',
}));
</script>

<template>
    <Head :title="t('admin.dashboard.title')" />
    <AdminLayout>
        <!-- Hero -->
        <section class="sheen anim-rise relative mb-6 overflow-hidden rounded-3xl border border-[var(--border)] bg-gradient-to-br from-[var(--secondary)]/12 via-[var(--surface)] to-[var(--primary)]/10 p-6 sm:p-8">
            <div class="hero-glow pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full"></div>
            <div class="relative flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div class="min-w-0">
                    <span class="eyebrow">{{ locale === 'en' ? 'Admin overview' : 'অ্যাডমিন ওভারভিউ' }}</span>
                    <h1 class="mt-2 font-heading text-2xl font-extrabold leading-tight sm:text-3xl">
                        {{ locale === 'en' ? 'Welcome back' : 'স্বাগতম' }}, {{ admin?.name?.split(' ')[0] }} <span class="inline-block">👋</span>
                    </h1>
                    <p class="mt-1.5 text-sm text-[var(--text-muted)] sm:text-base">
                        {{ locale === 'en' ? "Here's how Sikhun is performing today." : 'আজ সিখুন কেমন চলছে দেখুন।' }}
                    </p>
                    <p class="mt-3 inline-flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--surface)]/70 px-3 py-1 text-xs font-medium text-[var(--text-muted)]">
                        <CalendarDaysIcon class="h-3.5 w-3.5" /> {{ today }}
                    </p>
                </div>

                <div class="shrink-0 rounded-2xl border border-[var(--primary)]/30 bg-[var(--surface)]/80 px-4 py-3 backdrop-blur">
                    <div class="text-xs font-medium text-[var(--text-muted)]">{{ t('admin.dashboard.revenue_today') }}</div>
                    <div class="mt-0.5 font-heading text-xl font-extrabold text-[var(--primary)]">৳{{ stats.revenue_today }}</div>
                </div>
            </div>
        </section>

        <!-- Stat cards -->
        <div class="stagger mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <Link v-for="card in statCards" :key="card.label" :href="card.href" class="card card-hover rounded-2xl p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm text-[var(--text-muted)]">{{ card.label }}</div>
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl" :class="card.tint">
                        <component :is="card.icon" class="h-[1.15rem] w-[1.15rem]" />
                    </span>
                </div>
                <div class="mt-3 font-heading text-2xl font-extrabold">{{ card.value }}</div>
            </Link>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Charts -->
            <div class="grid gap-6 lg:col-span-2">
                <div class="card anim-rise rounded-2xl p-5">
                    <div class="mb-4 flex items-center gap-2 font-heading font-semibold">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-[var(--primary)]/10 text-[var(--primary)]">
                            <BanknotesIcon class="h-4 w-4" />
                        </span>
                        {{ t('admin.dashboard.revenue_chart') }}
                    </div>
                    <VueApexCharts type="area" height="260" :options="revenueOptions" :series="revenueSeries" />
                </div>
                <div class="card anim-rise rounded-2xl p-5">
                    <div class="mb-4 flex items-center gap-2 font-heading font-semibold">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-[var(--secondary)]/10 text-[var(--secondary)]">
                            <UsersIcon class="h-4 w-4" />
                        </span>
                        {{ t('admin.dashboard.registrations_chart') }}
                    </div>
                    <VueApexCharts type="bar" height="260" :options="regOptions" :series="regSeries" />
                </div>
            </div>

            <!-- Quick actions -->
            <div class="space-y-4">
                <div class="card anim-rise rounded-2xl p-5">
                    <h2 class="mb-3 font-heading font-bold">{{ locale === 'en' ? 'Quick Actions' : 'দ্রুত পদক্ষেপ' }}</h2>
                    <div class="grid grid-cols-2 gap-2">
                        <Link v-for="action in quickActions" :key="action.href" :href="action.href" class="flex flex-col items-start gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 p-3 text-xs font-medium transition-all hover:-translate-y-0.5 hover:border-[var(--primary)]/40 hover:shadow-sm">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-[var(--surface)] text-[var(--secondary)]">
                                <component :is="action.icon" class="h-4 w-4" />
                            </span>
                            <span class="leading-tight">{{ action.label }}</span>
                        </Link>
                    </div>
                </div>

                <div class="card anim-rise rounded-2xl p-5">
                    <div class="mb-3 flex items-center gap-2 font-heading font-bold">
                        <AcademicCapIcon class="h-4.5 w-4.5 text-[var(--primary)]" />
                        {{ locale === 'en' ? 'Platform Snapshot' : 'প্ল্যাটফর্ম স্ন্যাপশট' }}
                    </div>
                    <ul class="space-y-2.5 text-sm">
                        <li class="flex items-center justify-between">
                            <span class="text-[var(--text-muted)]">{{ t('admin.dashboard.total_students') }}</span>
                            <span class="font-semibold">{{ stats.total_students }}</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="text-[var(--text-muted)]">{{ t('admin.dashboard.active_subscriptions') }}</span>
                            <span class="font-semibold">{{ stats.active_subscriptions }}</span>
                        </li>
                        <li class="flex items-center justify-between border-t border-[var(--border)] pt-2.5">
                            <span class="text-[var(--text-muted)]">{{ t('admin.dashboard.revenue_total') }}</span>
                            <span class="font-semibold text-[var(--primary)]">৳{{ stats.revenue_total }}</span>
                        </li>
                    </ul>
                    <Link href="/admin/orders" class="mt-4 inline-flex items-center gap-1.5 text-xs font-medium text-[var(--primary)]">
                        {{ locale === 'en' ? 'View all orders' : 'সব অর্ডার দেখুন' }} <ArrowRightIcon class="h-3.5 w-3.5" />
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
