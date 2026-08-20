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
    colors: ['#f0873c'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 90, 100] } },
    xaxis: { categories: props.charts.labels, labels: { style: { colors: '#9aa79c' } }, axisBorder: { show: false }, axisTicks: { show: false } },
    yaxis: { labels: { style: { colors: '#9aa79c' } } },
    grid: { borderColor: '#2c3a31' },
}));

const regSeries = computed(() => [{ name: 'Registrations', data: props.charts.registrations }]);
const regOptions = computed(() => ({
    chart: { type: 'bar', toolbar: { show: false }, background: 'transparent' },
    theme: { mode: 'dark' },
    colors: ['#63c79d'],
    dataLabels: { enabled: false },
    plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
    xaxis: { categories: props.charts.labels, labels: { style: { colors: '#9aa79c' } }, axisBorder: { show: false }, axisTicks: { show: false } },
    yaxis: { labels: { style: { colors: '#9aa79c' } } },
    grid: { borderColor: '#2c3a31' },
}));
</script>

<template>
    <Head :title="t('admin.dashboard.title')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-8">{{ t('admin.dashboard.title') }}</h1>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="card rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('admin.dashboard.total_students') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.total_students }}</div>
            </div>
            <div class="card rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('admin.dashboard.total_books') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.total_books }}</div>
            </div>
            <div class="card rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('admin.dashboard.active_subscriptions') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.active_subscriptions }}</div>
            </div>
            <div class="card rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('admin.dashboard.revenue_total') }}</div>
                <div class="text-2xl font-heading font-extrabold text-[var(--primary)]">৳{{ stats.revenue_total }}</div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="card rounded-2xl p-5">
                <div class="text-sm font-semibold mb-4">{{ t('admin.dashboard.revenue_chart') }}</div>
                <apexchart type="area" height="260" :options="revenueOptions" :series="revenueSeries" />
            </div>
            <div class="card rounded-2xl p-5">
                <div class="text-sm font-semibold mb-4">{{ t('admin.dashboard.registrations_chart') }}</div>
                <apexchart type="bar" height="260" :options="regOptions" :series="regSeries" />
            </div>
        </div>
    </AdminLayout>
</template>