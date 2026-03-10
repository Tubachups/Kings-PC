<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Package, ShoppingCart, Users, ArrowBigDownDash, Clock3, Settings, Truck, CheckCircle2 } from 'lucide-vue-next';
import { computed } from 'vue';
import type { DashboardPageProps, Metric } from '@/types/admin';
import DashboardMetricCard from './DashboardMetricCard.vue';

const page = usePage<DashboardPageProps>();

const metrics = computed<Metric[]>(() => [
    {
        key: 'total-products',
        title: 'Total Products',
        value: page.props.totalProducts ?? '-',
        description: 'Active inventory items',
        icon: Package,
    },
    {
        key: 'total-orders',
        title: 'Total Orders',
        value: page.props.totalOrders ?? '-',
        description: 'Across 4 order stages',
        icon: ShoppingCart,
    },
    {
        key: 'total-customers',
        title: 'Total Customers',
        value: page.props.totalCustomers ?? '-',
        description: 'Registered users',
        icon: Users,
    },
    {
        key: 'low-stock',
        title: 'Low Stock Items',
        value: page.props.lowStockCount ?? '-',
        description: 'Items below 10 units',
        icon: ArrowBigDownDash,
    },
    {
        key: 'pending-orders',
        title: 'Pending Orders',
        value: page.props.pendingOrdersCount ?? '-',
        description: 'Awaiting processing',
        icon: Clock3,
    },
    {
        key: 'processed-orders',
        title: 'Processed Orders',
        value: page.props.processedOrdersCount ?? '-',
        description: 'In processing stage',
        icon: Settings,
    },
    {
        key: 'shipped-orders',
        title: 'Shipped Orders',
        value: page.props.shippedOrdersCount ?? '-',
        description: 'Out for delivery',
        icon: Truck,
    },
    {
        key: 'delivered-orders',
        title: 'Delivered Orders',
        value: page.props.deliveredOrdersCount ?? '-',
        description: 'Completed deliveries',
        icon: CheckCircle2,
    },
]);
</script>

<template>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <DashboardMetricCard
            v-for="metric in metrics"
            :key="metric.key"
            :title="metric.title"
            :value="metric.value"
            :description="metric.description"
            :icon="metric.icon"
        />
    </div>
</template>
