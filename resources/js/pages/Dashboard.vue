<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard, shop } from '@/routes';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Package, ShoppingCart, Users, Settings, Plus, LayoutGrid } from 'lucide-vue-next';
import { computed } from 'vue';
import { index as productsIndex, create as productsCreate } from '@/actions/App/Http/Controllers/Admin/ProductController';

const page = usePage();
const user = computed(() => page.props.auth?.user);

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
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Products
                        </CardTitle>
                        <Package class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">-</div>
                        <p class="text-xs text-muted-foreground">
                            Active inventory items
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Orders
                        </CardTitle>
                        <ShoppingCart class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">-</div>
                        <p class="text-xs text-muted-foreground">
                            Pending fulfillment
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Customers
                        </CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">-</div>
                        <p class="text-xs text-muted-foreground">
                            Registered users
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Low Stock Items
                        </CardTitle>
                        <Settings class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">-</div>
                        <p class="text-xs text-muted-foreground">
                            Items below 10 units
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Admin Actions -->
            <div v-if="user?.is_admin">
                <Card>
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>
                            Manage your store inventory and settings
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-wrap gap-4">
                        <Link :href="productsIndex().url">
                            <Button variant="default">
                                <LayoutGrid class="mr-2 h-4 w-4" />
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
                                <ShoppingCart class="mr-2 h-4 w-4" />
                                View Shop
                            </Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>

            <!-- Customer View -->
            <div v-else>
                <Card>
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
