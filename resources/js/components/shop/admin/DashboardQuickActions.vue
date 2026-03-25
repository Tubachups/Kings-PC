<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CheckCircle2, Clock3, FolderKanban, Plus, Settings, Store, Truck, Users } from 'lucide-vue-next';
import { create as productsCreate, deliveredOrders, index as productsIndex, pendingOrders, processedOrders, shippedOrders } from '@/actions/App/Http/Controllers/Admin/ProductController';
import { index as customersIndex } from '@/actions/App/Http/Controllers/Admin/CustomerController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

import { shop } from '@/routes';
</script>


<template>
    <Card class="p-3">
        <CardHeader>
            <CardTitle>Quick Actions</CardTitle>
            <CardDescription>
                Manage your store inventory and track customer orders
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
                <Link :href="customersIndex().url">
                <Button variant="outline">
                    <Users class="mr-2 h-4 w-4" />
                    Customers
                </Button>
                </Link>
                <Link :href="processedOrders().url">
                <Button variant="outline">
                    <Settings class="mr-2 h-4 w-4" />
                    Processed Orders
                </Button>
                </Link>
                <Link :href="shippedOrders().url">
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
</template>
