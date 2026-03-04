<script setup lang="ts">
import { Head, Link, usePage, usePoll } from '@inertiajs/vue3';
import { ArrowBigDownDash, CheckCircle2, Clock3, FolderKanban, Package, Plus, Settings, ShoppingCart, Store, Truck, Users } from 'lucide-vue-next';
import { computed, watch } from 'vue';
import { create as productsCreate, deliveredOrders, index as productsIndex, pendingOrders } from '@/actions/App/Http/Controllers/Admin/ProductController';
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


const totalProducts = computed(() => page.props.totalProducts ?? '-');
const totalOrders = computed(() => page.props.totalOrders ?? '-');
const lowStockCount = computed(() => page.props.lowStockCount ?? '-');
const totalCustomers = computed(() => page.props.totalCustomers ?? '-');
const pendingOrdersCount = computed(() => page.props.pendingOrdersCount ?? '-');
const processedOrdersCount = computed(() => page.props.processedOrdersCount ?? '-');
const shippedOrdersCount = computed(() => page.props.shippedOrdersCount ?? '-');
const deliveredOrdersCount = computed(() => page.props.deliveredOrdersCount ?? '-');

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
            <div v-if="user?.is_admin" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card class="p-2">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Products
                        </CardTitle>
                        <Package class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalProducts }}</div>
                        <p class="text-xs text-muted-foreground">
                            Active inventory items
                        </p>
                    </CardContent>
                </Card>

                <Card class="p-2">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Orders
                        </CardTitle>
                        <ShoppingCart class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalOrders }}</div>
                        <p class="text-xs text-muted-foreground">
                            Across 4 order stages
                        </p>
                    </CardContent>
                </Card>

                <Card class="p-2">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Customers
                        </CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalCustomers }}</div>
                        <p class="text-xs text-muted-foreground">
                            Registered users
                        </p>
                    </CardContent>
                </Card>

                <Card class="p-2">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Low Stock Items
                        </CardTitle>
                        <ArrowBigDownDash class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ lowStockCount }}</div>
                        <p class="text-xs text-muted-foreground">
                            Items below 10 units
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div v-if="user?.is_admin" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card class="p-2">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Pending Orders
                        </CardTitle>
                        <Clock3 class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ pendingOrdersCount }}</div>
                        <p class="text-xs text-muted-foreground">
                            Awaiting processing
                        </p>
                    </CardContent>
                </Card>

                <Card class="p-2">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Processed Orders
                        </CardTitle>
                        <Settings class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ processedOrdersCount }}</div>
                        <p class="text-xs text-muted-foreground">
                            In processing stage
                        </p>
                    </CardContent>
                </Card>

                <Card class="p-2">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Shipped Orders
                        </CardTitle>
                        <Truck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ shippedOrdersCount }}</div>
                        <p class="text-xs text-muted-foreground">
                            Out for delivery
                        </p>
                    </CardContent>
                </Card>

                <Card class="p-2">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Delivered Orders
                        </CardTitle>
                        <CheckCircle2 class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ deliveredOrdersCount }}</div>
                        <p class="text-xs text-muted-foreground">
                            Completed deliveries
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Admin Actions -->
            <div v-if="user?.is_admin">
                <Card class="p-3">
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>
                            Manage your store inventory and track  customer orders
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="flex flex-wrap gap-4">
                            <Link :href="productsIndex().url">
                                <Button variant="default">
                                    <FolderKanban class="mr-2 h-4 w-4" />
                                    Manage Products
                                </Button>
                            </Link>
                            <Link :href="productsCreate().url">
                                <Button variant="outline">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Add New Product
                                </Button>
                            </Link>
                            <Link :href="shop().url">
                                <Button variant="outline">
                                    <Store class="mr-2 h-4 w-4" />
                                    View Shop
                                </Button>
                            </Link>
                            <Link :href="pendingOrders().url">
                                <Button variant="outline">
                                    <Clock3 class="mr-2 h-4 w-4" />
                                    Pending Orders
                                </Button>
                            </Link>
                            <Link href="/admin/products/processed-orders">
                                <Button variant="outline">
                                    <Settings class="mr-2 h-4 w-4" />
                                    Processed Orders
                                </Button>
                            </Link>
                            <Link href="/admin/products/shipped-orders">
                                <Button variant="outline">
                                    <Truck class="mr-2 h-4 w-4" />
                                    Shipped Orders
                                </Button>
                            </Link>
                            <Link :href="deliveredOrders().url">
                                <Button variant="outline">
                                    <CheckCircle2 class="mr-2 h-4 w-4" />
                                    Delivered Orders
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>
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
