<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { DashboardPageProps } from '@/types/admin';
import { pesoFormatter } from '@/utils/helpers';

const page = usePage<DashboardPageProps>();

const salesChart = computed(() => page.props.salesChart ?? {
    labels: [],
    revenues: [],
    orders: [],
    totalRevenue: 0,
    totalSales: 0,
});

const averageOrderValue = computed(() => {
    if (!salesChart.value.totalSales) {
        return 0;
    }

    return salesChart.value.totalRevenue / salesChart.value.totalSales;
});

const series = computed(() => [
    {
        name: 'Revenue',
        type: 'area',
        data: salesChart.value.revenues,
    },
    {
        name: 'Orders',
        type: 'line',
        data: salesChart.value.orders,
    },
]);

const chartOptions = computed(() => ({
    chart: {
        toolbar: {
            show: false,
        },
        zoom: {
            enabled: false,
        },
        fontFamily: 'Inter, sans-serif',
    },
    colors: ['#0f766e', '#f97316'],
    dataLabels: {
        enabled: false,
    },
    stroke: {
        curve: 'smooth',
        width: [3, 3],
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.32,
            opacityTo: 0.04,
            stops: [0, 95, 100],
        },
    },
    xaxis: {
        categories: salesChart.value.labels,
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false,
        },
    },
    yaxis: [
        {
            labels: {
                formatter: (value: number) => pesoFormatter.format(value),
            },
        },
        {
            opposite: true,
            labels: {
                formatter: (value: number) => Math.round(value).toString(),
            },
        },
    ],
    legend: {
        position: 'top',
        horizontalAlign: 'left',
    },
    grid: {
        borderColor: '#e5e7eb',
        strokeDashArray: 4,
    },
    tooltip: {
        shared: true,
        intersect: false,
        y: [
            {
                formatter: (value: number) => pesoFormatter.format(value),
            },
            {
                formatter: (value: number) => `${Math.round(value)} orders`,
            },
        ],
    },
}));
</script>

<template>
    <Card class="overflow-hidden border-slate-200 bg-linear-to-br from-white via-slate-50 to-teal-50/70">
        <CardHeader class="gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <CardTitle>Revenue and Sales Tracking</CardTitle>
                <CardDescription>
                    Last 7 days of completed order revenue and sales activity.
                </CardDescription>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-white/70 bg-white/80 px-4 py-3 shadow-sm">
                    <p class="text-muted-foreground text-xs uppercase tracking-[0.2em]">Revenue</p>
                    <p class="mt-2 text-xl font-semibold">{{ pesoFormatter.format(salesChart.totalRevenue) }}</p>
                </div>
                <div class="rounded-xl border border-white/70 bg-white/80 px-4 py-3 shadow-sm">
                    <p class="text-muted-foreground text-xs uppercase tracking-[0.2em]">Sales</p>
                    <p class="mt-2 text-xl font-semibold">{{ salesChart.totalSales }}</p>
                </div>
                <div class="rounded-xl border border-white/70 bg-white/80 px-4 py-3 shadow-sm">
                    <p class="text-muted-foreground text-xs uppercase tracking-[0.2em]">Avg Order</p>
                    <p class="mt-2 text-xl font-semibold">{{ pesoFormatter.format(averageOrderValue) }}</p>
                </div>
            </div>
        </CardHeader>

        <CardContent>
            <VueApexCharts
                type="line"
                height="320"
                :options="chartOptions"
                :series="series"
            />
        </CardContent>
    </Card>
</template>
