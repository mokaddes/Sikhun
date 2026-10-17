<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import VueApexCharts from 'vue3-apexcharts';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
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
    NewspaperIcon,
    EyeIcon,
    CheckCircleIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: Object,
    charts: Object,
    recent_orders: { type: Array, default: () => [] },
    recent_students: { type: Array, default: () => [] },
});

const { t, locale } = useI18n();
const theme = useThemeStore();
const admin = usePage().props.auth?.admin;

const isDark = computed(() => theme.mode === 'dark');
const axisColor = computed(() => (isDark.value ? '#a2aabc' : '#657188'));
const gridColor = computed(() => (isDark.value ? '#303644' : '#dce3ee'));

const statCards = computed(() => [
    { label: t('admin.dashboard.total_students'), value: props.stats.total_students, icon: UsersIcon, tint: 'bg-[var(--secondary)]/10 text-[var(--secondary)] ring-1 ring-[var(--secondary)]/20', href: '/admin/students' },
    { label: t('admin.dashboard.total_books'), value: props.stats.total_books, icon: BookOpenIcon, tint: 'bg-[var(--primary)]/10 text-[var(--primary)] ring-1 ring-[var(--primary)]/20', href: '/admin/books' },
    { label: t('admin.dashboard.active_subscriptions'), value: props.stats.active_subscriptions, icon: CreditCardIcon, tint: 'bg-[var(--tertiary)]/10 text-[var(--tertiary)] ring-1 ring-[var(--tertiary)]/20', href: '/admin/plans' },
    { label: t('admin.dashboard.revenue_total'), value: `৳${props.stats.revenue_total}`, icon: BanknotesIcon, tint: 'bg-[var(--accent)]/10 text-[var(--accent)] ring-1 ring-[var(--accent)]/20', href: '/admin/orders' },
    { label: t('admin.dashboard.draft_blogs'), value: props.stats.draft_blogs ?? 0, icon: NewspaperIcon, tint: 'bg-amber-500/10 text-amber-500 ring-1 ring-amber-500/20', href: '/admin/blog?status=draft' },
]);

const quickActions = [
    { label: t('admin.nav.books'), href: '/admin/books', icon: BookOpenIcon, desc: 'Manage catalogue & PDFs' },
    { label: t('admin.nav.courses'), href: '/admin/courses', icon: AcademicCapIcon, desc: 'Courses & lessons' },
    { label: t('admin.nav.orders'), href: '/admin/orders', icon: InboxIcon, desc: 'Verify payments & orders' },
    { label: t('admin.nav.blog'), href: '/admin/blog', icon: NewspaperIcon, desc: 'Posts & auto-aggregator' },
    { label: t('admin.nav.settings'), href: '/admin/settings', icon: Cog6ToothIcon, desc: 'System configuration' },
];

const revenueSeries = computed(() => [{ name: 'Revenue (৳)', data: props.charts.revenue }]);
const revenueOptions = computed(() => ({
    chart: { type: 'area', toolbar: { show: false }, background: 'transparent', foreColor: axisColor.value, fontFamily: 'inherit' },
    theme: { mode: isDark.value ? 'dark' : 'light' },
    colors: ['#f97316'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2.5 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] } },
    xaxis: { categories: props.charts.labels, labels: { style: { colors: axisColor.value, fontSize: '11px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
    yaxis: { labels: { style: { colors: axisColor.value, fontSize: '11px' }, formatter: (v) => `৳${v}` } },
    grid: { borderColor: gridColor.value, strokeDashArray: 3 },
    tooltip: { theme: isDark.value ? 'dark' : 'light' },
}));

const regSeries = computed(() => [{ name: 'Students', data: props.charts.registrations }]);
const regOptions = computed(() => ({
    chart: { type: 'bar', toolbar: { show: false }, background: 'transparent', foreColor: axisColor.value, fontFamily: 'inherit' },
    theme: { mode: isDark.value ? 'dark' : 'light' },
    colors: ['#2757cc'],
    dataLabels: { enabled: false },
    plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
    xaxis: { categories: props.charts.labels, labels: { style: { colors: axisColor.value, fontSize: '11px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
    yaxis: { labels: { style: { colors: axisColor.value, fontSize: '11px' } } },
    grid: { borderColor: gridColor.value, strokeDashArray: 3 },
    tooltip: { theme: isDark.value ? 'dark' : 'light' },
}));

const today = computed(() => new Date().toLocaleDateString(locale.value === 'bn' ? 'bn-BD' : 'en-GB', {
    weekday: 'long', day: 'numeric', month: 'long',
}));

const statusBadge = (s) => ({
    pending: 'bg-amber-500/10 text-amber-500 ring-1 ring-amber-500/20',
    completed: 'bg-[var(--secondary)]/10 text-[var(--secondary)] ring-1 ring-[var(--secondary)]/20',
    failed: 'bg-red-500/10 text-red-500 ring-1 ring-red-500/20',
    refunded: 'bg-[var(--text-muted)]/10 text-[var(--text-muted)] ring-1 ring-[var(--text-muted)]/20',
    active: 'bg-[var(--secondary)]/10 text-[var(--secondary)] ring-1 ring-[var(--secondary)]/20',
    inactive: 'bg-red-500/10 text-red-500 ring-1 ring-red-500/20',
}[s] ?? 'bg-[var(--surface2)] text-[var(--text-muted)]');
</script>

<template>
    <Head :title="t('admin.dashboard.title')" />
    <AdminLayout>
        <!-- Hero Banner with Overview & Today's Revenue -->
        <section class="sheen anim-rise relative mb-8 overflow-hidden rounded-3xl border border-[var(--border)] bg-gradient-to-br from-[var(--secondary)]/12 via-[var(--surface)] to-[var(--primary)]/10 p-6 sm:p-8 shadow-sm">
            <div class="hero-glow pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full"></div>
            <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <span class="eyebrow">{{ locale === 'en' ? 'Executive Dashboard' : 'অ্যাডমিন ড্যাশবোর্ড' }}</span>
                    <h1 class="mt-2 font-heading text-2xl font-extrabold leading-tight sm:text-3xl text-[var(--text)]">
                        {{ locale === 'en' ? 'Welcome back' : 'স্বাগতম' }}, {{ admin?.name?.split(' ')[0] }} <span class="inline-block">👋</span>
                    </h1>
                    <p class="mt-1.5 text-sm text-[var(--text-muted)] sm:text-base">
                        {{ locale === 'en' ? "Here's an overview of Sikhun's live performance and activities today." : 'আজ সিখুন প্ল্যাটফর্মের সার্বিক পারফরম্যান্স ও কার্যক্রমের চিত্র।' }}
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--surface)]/80 px-3 py-1 text-xs font-medium text-[var(--text-muted)] shadow-xs">
                            <CalendarDaysIcon class="h-3.5 w-3.5 text-[var(--secondary)]" /> {{ today }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 shadow-xs">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ locale === 'en' ? 'System Operational' : 'সিস্টেম সচল' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="rounded-2xl border border-[var(--primary)]/30 bg-[var(--surface)]/85 px-5 py-3.5 backdrop-blur shadow-xs">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ t('admin.dashboard.revenue_today') }}</div>
                        <div class="mt-1 font-heading text-2xl font-extrabold text-[var(--primary)]">৳{{ stats.revenue_today }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- High Density Stat Cards -->
        <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            <Link
                v-for="card in statCards"
                :key="card.label"
                :href="card.href"
                class="card card-hover group relative flex flex-col justify-between rounded-2xl p-4.5 transition-all"
            >
                <div class="flex items-start justify-between gap-2">
                    <span class="text-xs font-medium text-[var(--text-muted)] line-clamp-1">{{ card.label }}</span>
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl" :class="card.tint">
                        <component :is="card.icon" class="h-4 w-4" />
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-heading text-xl font-extrabold sm:text-2xl text-[var(--text)]">{{ card.value }}</span>
                    <ArrowRightIcon class="h-3.5 w-3.5 text-[var(--text-muted)] opacity-0 transition-all group-hover:opacity-100 group-hover:translate-x-0.5 text-[var(--primary)]" />
                </div>
            </Link>
        </div>

        <!-- Charts Grid -->
        <div class="mb-8 grid gap-6 lg:grid-cols-2">
            <!-- Revenue Area Chart -->
            <div class="card anim-rise rounded-2xl p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 font-heading font-bold text-base">
                        <span class="grid h-8 w-8 place-items-center rounded-xl bg-[var(--primary)]/10 text-[var(--primary)]">
                            <BanknotesIcon class="h-4 w-4" />
                        </span>
                        {{ t('admin.dashboard.revenue_chart') }}
                    </div>
                    <Link href="/admin/orders" class="text-xs font-semibold text-[var(--primary)] hover:underline">
                        {{ locale === 'en' ? 'View Orders' : 'অর্ডার দেখুন' }} →
                    </Link>
                </div>
                <VueApexCharts type="area" height="260" :options="revenueOptions" :series="revenueSeries" />
            </div>

            <!-- Student Registrations Bar Chart -->
            <div class="card anim-rise rounded-2xl p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 font-heading font-bold text-base">
                        <span class="grid h-8 w-8 place-items-center rounded-xl bg-[var(--secondary)]/10 text-[var(--secondary)]">
                            <UsersIcon class="h-4 w-4" />
                        </span>
                        {{ t('admin.dashboard.registrations_chart') }}
                    </div>
                    <Link href="/admin/students" class="text-xs font-semibold text-[var(--secondary)] hover:underline">
                        {{ locale === 'en' ? 'All Students' : 'শিক্ষার্থী তালিকা' }} →
                    </Link>
                </div>
                <VueApexCharts type="bar" height="260" :options="regOptions" :series="regSeries" />
            </div>
        </div>

        <!-- Recent Tables & Quick Actions Grid -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Recent Orders (2 cols on lg) -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Recent Orders Datatable Card -->
                <div class="card anim-rise rounded-2xl p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2 font-heading font-bold text-base">
                            <span class="grid h-8 w-8 place-items-center rounded-xl bg-[var(--accent)]/10 text-[var(--accent)]">
                                <InboxIcon class="h-4 w-4" />
                            </span>
                            {{ locale === 'en' ? 'Recent Orders' : 'সাম্প্রতিক অর্ডার' }}
                        </div>
                        <Link href="/admin/orders" class="text-xs font-semibold text-[var(--primary)] hover:underline">
                            {{ locale === 'en' ? 'Manage Orders' : 'সব অর্ডার' }} →
                        </Link>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-[var(--border)]">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[var(--surface2)]/70 text-[var(--text-muted)] uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="px-4 py-2.5 font-semibold">Order</th>
                                    <th class="px-4 py-2.5 font-semibold">Student</th>
                                    <th class="px-4 py-2.5 font-semibold">Amount</th>
                                    <th class="px-4 py-2.5 font-semibold">Method</th>
                                    <th class="px-4 py-2.5 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border)]">
                                <tr v-for="o in recent_orders" :key="o.id" class="transition-colors hover:bg-[var(--surface2)]/40">
                                    <td class="px-4 py-3 font-mono font-medium text-[var(--text)]">{{ o.order_number }}</td>
                                    <td class="px-4 py-3 font-medium">{{ o.student?.name || '—' }}</td>
                                    <td class="px-4 py-3 font-bold text-[var(--text)]">৳{{ o.amount }}</td>
                                    <td class="px-4 py-3 uppercase text-[var(--text-muted)] font-mono text-[11px]">{{ o.payment_method }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-semibold uppercase" :class="statusBadge(o.status)">
                                            {{ o.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!recent_orders.length">
                                    <td colspan="5" class="px-4 py-8 text-center text-xs text-[var(--text-muted)]">
                                        {{ locale === 'en' ? 'No recent orders.' : 'কোনো সাম্প্রতিক অর্ডার নেই।' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Students Datatable Card -->
                <div class="card anim-rise rounded-2xl p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2 font-heading font-bold text-base">
                            <span class="grid h-8 w-8 place-items-center rounded-xl bg-[var(--secondary)]/10 text-[var(--secondary)]">
                                <UsersIcon class="h-4 w-4" />
                            </span>
                            {{ locale === 'en' ? 'New Registrations' : 'নতুন নিবন্ধিত শিক্ষার্থী' }}
                        </div>
                        <Link href="/admin/students" class="text-xs font-semibold text-[var(--secondary)] hover:underline">
                            {{ locale === 'en' ? 'All Students' : 'সব শিক্ষার্থী' }} →
                        </Link>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-[var(--border)]">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[var(--surface2)]/70 text-[var(--text-muted)] uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="px-4 py-2.5 font-semibold">Student</th>
                                    <th class="px-4 py-2.5 font-semibold">Email</th>
                                    <th class="px-4 py-2.5 font-semibold">Type</th>
                                    <th class="px-4 py-2.5 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border)]">
                                <tr v-for="s in recent_students" :key="s.id" class="transition-colors hover:bg-[var(--surface2)]/40">
                                    <td class="px-4 py-3 font-semibold text-[var(--text)]">
                                        <Link :href="`/admin/students/${s.id}`" class="hover:text-[var(--primary)] hover:underline">{{ s.name }}</Link>
                                    </td>
                                    <td class="px-4 py-3 text-[var(--text-muted)]">{{ s.email }}</td>
                                    <td class="px-4 py-3 uppercase text-[11px] text-[var(--text-muted)]">{{ s.type }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-semibold" :class="statusBadge(s.status)">
                                            {{ s.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!recent_students.length">
                                    <td colspan="4" class="px-4 py-8 text-center text-xs text-[var(--text-muted)]">
                                        {{ locale === 'en' ? 'No recent student registrations.' : 'কোনো শিক্ষার্থী নেই।' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Shortcuts & Snapshot -->
            <div class="space-y-6">
                <!-- Quick Navigation -->
                <div class="card anim-rise rounded-2xl p-5 shadow-sm">
                    <h2 class="mb-3.5 font-heading font-bold text-base flex items-center gap-2">
                        <SparklesIcon class="h-4 w-4 text-[var(--primary)]" />
                        {{ locale === 'en' ? 'Quick Management' : 'দ্রুত ব্যবস্থাপনা' }}
                    </h2>
                    <div class="space-y-2">
                        <Link
                            v-for="action in quickActions"
                            :key="action.href"
                            :href="action.href"
                            class="group flex items-center justify-between rounded-xl border border-[var(--border)] bg-[var(--surface2)]/40 p-3 transition-all hover:border-[var(--primary)]/40 hover:bg-[var(--surface2)]/80 hover:shadow-xs"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-[var(--surface)] text-[var(--secondary)] shadow-2xs group-hover:text-[var(--primary)] transition-colors">
                                    <component :is="action.icon" class="h-4.5 w-4.5" />
                                </span>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-[var(--text)] group-hover:text-[var(--primary)] transition-colors">{{ action.label }}</div>
                                    <div class="text-[10px] text-[var(--text-muted)] truncate">{{ action.desc }}</div>
                                </div>
                            </div>
                            <ArrowRightIcon class="h-3.5 w-3.5 text-[var(--text-muted)] group-hover:translate-x-1 group-hover:text-[var(--primary)] transition-all shrink-0" />
                        </Link>
                    </div>
                </div>

                <!-- Platform Snapshot Summary -->
                <div class="card anim-rise rounded-2xl p-5 shadow-sm">
                    <div class="mb-3.5 flex items-center gap-2 font-heading font-bold text-base">
                        <AcademicCapIcon class="h-4.5 w-4.5 text-[var(--secondary)]" />
                        {{ locale === 'en' ? 'Platform Health' : 'প্ল্যাটফর্ম সারাংশ' }}
                    </div>
                    <ul class="space-y-3 text-xs">
                        <li class="flex items-center justify-between rounded-lg bg-[var(--surface2)]/50 px-3 py-2">
                            <span class="text-[var(--text-muted)]">{{ t('admin.dashboard.total_students') }}</span>
                            <span class="font-heading font-bold text-sm text-[var(--text)]">{{ stats.total_students }}</span>
                        </li>
                        <li class="flex items-center justify-between rounded-lg bg-[var(--surface2)]/50 px-3 py-2">
                            <span class="text-[var(--text-muted)]">{{ t('admin.dashboard.total_books') }}</span>
                            <span class="font-heading font-bold text-sm text-[var(--text)]">{{ stats.total_books }}</span>
                        </li>
                        <li class="flex items-center justify-between rounded-lg bg-[var(--surface2)]/50 px-3 py-2">
                            <span class="text-[var(--text-muted)]">{{ t('admin.dashboard.active_subscriptions') }}</span>
                            <span class="font-heading font-bold text-sm text-[var(--secondary)]">{{ stats.active_subscriptions }}</span>
                        </li>
                        <li class="flex items-center justify-between rounded-lg bg-[var(--surface2)]/50 px-3 py-2">
                            <span class="text-[var(--text-muted)]">{{ t('admin.dashboard.revenue_total') }}</span>
                            <span class="font-heading font-bold text-sm text-[var(--primary)]">৳{{ stats.revenue_total }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
