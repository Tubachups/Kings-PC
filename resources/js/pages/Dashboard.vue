<script setup lang="ts">
import { Head, usePage, usePoll } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import DashboardMetricsGrid from '@/components/shop/admin/DashboardMetricsGrid.vue';
import DashboardRevenueChart from '@/components/shop/admin/DashboardRevenueChart.vue';
import CustomerAddressBookCard from '@/components/shop/customer/CustomerAddressBookCard.vue';
import CustomerOrdersCard from '@/components/shop/customer/CustomerOrdersCard.vue';
import CustomerWishlistCard from '@/components/shop/customer/CustomerWishlistCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => Boolean(user.value?.is_admin));

const { start, stop } = usePoll(
    2000,
    {
        only: [
            'totalProducts',
            'totalOrders',
            'lowStockCount',
            'totalCustomers',
            'pendingOrdersCount',
            'processedOrdersCount',
            'shippedOrdersCount',
            'deliveredOrdersCount',
        ],
    },
    {
        autoStart: false,
        keepAlive: true,
    },
);

watch(
    isAdmin,
    (admin) => {
        if (admin) {
            start();
        } else {
            stop();
        }
    },
    { immediate: true },
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard">
        <meta
            head-key="description"
            name="description"
            content="View your King's PC dashboard, account activity, and quick actions."
        />
    </Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Welcome Section -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Welcome back, {{ user?.name }}!
                </h1>
                <p v-if="user?.is_admin" class="text-muted-foreground">
                    Here's what's happening with your PC parts store.
                </p>
                <p v-else class="text-muted-foreground">
                    Here's a quick overview of your account and recent activity.
                </p>
            </div>

            <!-- Admin Quick Actions -->
            <div v-if="user?.is_admin">
                <DashboardMetricsGrid />
                <DashboardRevenueChart class="mt-6" />
            </div>

            <!-- Customer View -->
            <div v-else>
                <div
                    class="grid items-start gap-4 lg:grid-cols-2 xl:grid-cols-3"
                >
                    <CustomerAddressBookCard />
                    <CustomerOrdersCard />
                    <CustomerWishlistCard />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
