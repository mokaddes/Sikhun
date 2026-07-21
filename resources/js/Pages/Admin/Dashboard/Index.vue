<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ stats: Object, charts: Object });
const { t } = useI18n();

const revenueSeries = computed(() => [{ name: 'Revenue', data: props.charts.revenue }]);
const revenueOptions = computed(() => ({
    chart: { type: 'area', toolbar: { show: false }, background: 'transparent' },
    theme: { mode: 'dark' },
    colors: ['#6c63ff'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 90, 100] } },
    xaxis: { categories: props.charts.labels, labels: { style: { colors: '#7a7a9a' } }, axisBorder: { show: false }, axisTicks: { show: false } },
    yaxis: { labels: { style: { colors: '#7a7a9a' } } },
    grid: { borderColor: '#2a2a38' },
}));

const regSeries = computed(() => [{ name: 'Registrations', data: props.charts.registrations }]);
const regOptions = computed(() => ({
    chart: { type: 'bar', toolbar: { show: false }, background: 'transparent' },
    theme: { mode: 'dark' },
    colors: ['#00d4aa'],
    dataLabels: { enabled: false },
    plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
    xaxis: { categories: props.charts.labels, labels: { style: { colors: '#7a7a9a' } }, axisBorder: { show: false }, axisTicks: { show: false } },
    yaxis: { labels: { style: { colors: '#7a7a9a' } } },
    grid: { borderColor: '#2a2a38' },
}));
</script>

<template>
    <Head :title="t('admin.dashboard.title')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-8">{{ t('admin.dashboard.title') }}</h1>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="rounded-xl border border-[#2a2a38] bg-[#111118] p-5">
                <div class="text-sm text-[#7a7a9a] mb-1">{{ t('admin.dashboard.total_students') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.total_students }}</div>
            </div>
            <div class="rounded-xl border border-[#2a2a38] bg-[#111118] p-5">
                <div class="text-sm text-[#7a7a9a] mb-1">{{ t('admin.dashboard.total_books') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.total_books }}</div>
            </div>
            <div class="rounded-xl border border-[#2a2a38] bg-[#111118] p-5">
                <div class="text-sm text-[#7a7a9a] mb-1">{{ t('admin.dashboard.active_subscriptions') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.active_subscriptions }}</div>
            </div>
            <div class="rounded-xl border border-[#2a2a38] bg-[#111118] p-5">
                <div class="text-sm text-[#7a7a9a] mb-1">{{ t('admin.dashboard.revenue_total') }}</div>
                <div class="text-2xl font-heading font-extrabold">৳{{ stats.revenue_total }}</div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-[#2a2a38] bg-[#111118] p-5">
                <div class="text-sm font-semibold mb-4">{{ t('admin.dashboard.revenue_chart') }}</div>
                <apexchart type="area" height="260" :options="revenueOptions" :series="revenueSeries" />
            </div>
            <div class="rounded-xl border border-[#2a2a38] bg-[#111118] p-5">
                <div class="text-sm font-semibold mb-4">{{ t('admin.dashboard.registrations_chart') }}</div>
                <apexchart type="bar" height="260" :options="regOptions" :series="regSeries" />
            </div>
        </div>
    </AdminLayout>
</template>
