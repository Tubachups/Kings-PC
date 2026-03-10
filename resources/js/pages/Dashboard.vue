<script setup lang="ts">
import { Head, Link, usePage, usePoll } from '@inertiajs/vue3';
import { ShoppingCart} from 'lucide-vue-next';
import { computed, watch } from 'vue';
import DashboardMetricsGrid from '@/components/shop/admin/DashboardMetricsGrid.vue';
import DashboardQuickActions from '@/components/shop/admin/DashboardQuickActions.vue'
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard, shop } from '@/routes';
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
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Welcome Section -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Welcome back, {{ user?.name }}!
                </h1>
                <p class="text-muted-foreground">
                    Here's what's happening with your PC parts store.
                </p>
            </div>

            <!-- Admin Quick Actions -->
            <div v-if="user?.is_admin" >
                <DashboardMetricsGrid />
                <br/>
                <DashboardQuickActions/>
            </div>

            <!-- Customer View -->
            <div v-else>
                <Card class='p-3'>
                    <CardHeader>
                        <CardTitle>Welcome to PC Parts Store</CardTitle>
                        <CardDescription>
                            Browse our collection of high-quality PC components
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Link :href="shop().url">
                            <Button>
                                <ShoppingCart class="mr-2 h-4 w-4" />
                                Browse Products
                            </Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
