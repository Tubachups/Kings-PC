<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useAppearance } from '@/composables/useAppearance';
import type { DashboardPageProps } from '@/types/admin';
import { pesoFormatter } from '@/utils/helpers';

const page = usePage<DashboardPageProps>();
const { resolvedAppearance } = useAppearance();

const salesChart = computed(() => page.props.salesChart ?? {
    labels: [],
    revenues: [],
    orders: [],
    totalRevenue: 0,
    totalSales: 0,
});
const isDarkMode = computed(() => resolvedAppearance.value === 'dark');
const axisLabelColor = computed(() => isDarkMode.value ? '#f8fafc' : '#475569');
const gridBorderColor = computed(() => isDarkMode.value ? '#334155' : '#e5e7eb');

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
        foreColor: axisLabelColor.value,
    },
    colors: ['#0f766e', '#f97316'],
    dataLabels: {
        enabled: false,
    },
    stroke: {
        curve: 'smooth' as const,
        width: [3, 3],
    },
    fill: {
        type: 'gradient' as const,
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
        labels: {
            style: {
                colors: salesChart.value.labels.map(() => axisLabelColor.value),
            },
        },
    },
    yaxis: [
        {
            labels: {
                style: {
                    colors: [axisLabelColor.value],
                },
                formatter: (value: number) => pesoFormatter.format(value),
            },
        },
        {
            opposite: true,
            labels: {
                style: {
                    colors: [axisLabelColor.value],
                },
                formatter: (value: number) => Math.round(value).toString(),
            },
        },
    ],
    legend: {
        position: 'top' as const,
        horizontalAlign: 'left' as const,
        labels: {
            colors: axisLabelColor.value,
        },
    },
    grid: {
        borderColor: gridBorderColor.value,
        strokeDashArray: 4,
    },
    tooltip: {
        theme: isDarkMode.value ? 'dark' : 'light',
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
    <Card class="overflow-hidden border-border bg-linear-to-br from-background via-muted/60 to-accent/40">
        <CardHeader class="gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <CardTitle>Revenue and Sales Tracking</CardTitle>
                <CardDescription>
                    Last 12 months of completed order revenue and sales activity.
                </CardDescription>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-border/70 bg-card/80 px-4 py-3 shadow-sm">
                    <p class="text-muted-foreground text-xs uppercase tracking-[0.2em]">Revenue</p>
                    <p class="mt-2 text-xl font-semibold tracking-wider">{{ pesoFormatter.format(salesChart.totalRevenue) }}</p>
                </div>
                <div class="rounded-xl border border-border/70 bg-card/80 px-4 py-3 shadow-sm">
                    <p class="text-muted-foreground text-xs uppercase tracking-[0.2em]">Sales</p>
                    <p class="mt-2 text-xl font-semibold tracking-wider">{{ salesChart.totalSales }}</p>
                </div>
                <div class="rounded-xl border border-border/70 bg-card/80 px-4 py-3 shadow-sm">
                    <p class="text-muted-foreground text-xs uppercase tracking-[0.2em]">Avg Order</p>
                    <p class="mt-2 text-xl font-semibold tracking-wider">{{ pesoFormatter.format(averageOrderValue) }}</p>
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
