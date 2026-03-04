<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Mail, Package, Truck } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { advancePendingOrder, index as productsIndex } from '@/actions/App/Http/Controllers/Admin/ProductController';
import PaginationControls from '@/components/PaginationControls.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { PendingOrder } from '@/types/pending-order';
import { formatCurrency, formatDate, pesoFormatter } from '@/utils/helpers';

const props = defineProps<{
    orders: {
        data: PendingOrder[];
        current_page: number;
        per_page: number;
        total: number;
    };
}>();

const shippedOrdersPath = '/admin/products/shipped-orders';

const formattedOrders = computed(() => {
    return props.orders.data.map((order) => ({
        ...order,
        totalFormatted: pesoFormatter.format(Number(order.total || 0)),
    }));
});

const breadcrumbs = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Products', href: productsIndex().url },
    { title: 'Shipped Orders', href: shippedOrdersPath },
];

const isItemsDialogOpen = ref(false);
const selectedOrder = ref<PendingOrder | null>(null);
const advancingOrderId = ref<number | null>(null);

const openItemsDialog = (order: PendingOrder) => {
    selectedOrder.value = order;
    isItemsDialogOpen.value = true;
};

const changePage = (page: number) => {
    router.get(shippedOrdersPath, { page, per_page: props.orders.per_page }, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const advanceOrderStatus = (order: PendingOrder) => {
    if (!order.next_status || advancingOrderId.value === order.id) {
        return;
    }

    advancingOrderId.value = order.id;

    router.patch(advancePendingOrder(order.id).url, {}, {
        preserveScroll: true,
        onFinish: () => {
            advancingOrderId.value = null;
        },
    });
};
</script>

<template>
    <Head title="Shipped Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Shipped Orders</h1>
                    <p class="text-muted-foreground">
                        Orders in transit and awaiting customer delivery confirmation.
                    </p>
                </div>
                <Link :href="productsIndex().url">
                    <Button variant="outline">Back to Products</Button>
                </Link>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card v-for="order in formattedOrders" :key="order.id">
                    <CardHeader>
                        <CardTitle class="text-base">Order #{{ order.order_number }}</CardTitle>
                        <CardDescription>
                            {{ order.status || 'Shipped' }}
                            - {{ order.payment_method ? order.payment_method.toUpperCase() : 'Unknown payment method' }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <p><span class="font-medium">Customer:</span> {{ order.customer?.name || 'Unknown customer' }}</p>
                        <p><span class="font-medium">Status:</span> {{ order.status || 'Shipped' }}</p>
                        <p>
                            <span class="font-medium">Email:</span>
                            <a
                                v-if="order.customer?.email"
                                :href="`mailto:${order.customer.email}`"
                                class="inline-flex items-center gap-1 text-primary hover:underline"
                            >
                                <Mail class="h-3.5 w-3.5" />
                                {{ order.customer.email }}
                            </a>
                            <span v-else>No email</span>
                        </p>
                        <button
                            type="button"
                            class="cursor-pointer flex items-center gap-1 text-left hover:text-primary"
                            @click="openItemsDialog(order)"
                        >
                            <Package class="h-4 w-4 text-muted-foreground" />
                            {{ order.items_count }} item(s)
                        </button>
                        <p><span class="font-medium">Total:</span> {{ order.totalFormatted }}</p>
                        <p class="text-xs text-muted-foreground">Shipped: {{ formatDate(order.created_at || '') || '-' }}</p>
                        <Button
                            v-if="order.next_action_label"
                            type="button"
                            size="sm"
                            class="mt-2"
                            :disabled="advancingOrderId === order.id"
                            @click="advanceOrderStatus(order)"
                        >
                            {{ advancingOrderId === order.id ? 'Updating...' : order.next_action_label }}
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <PaginationControls
                v-if="orders.total > orders.per_page"
                :total-items="orders.total"
                :current-page="orders.current_page"
                :items-per-page="orders.per_page"
                @update:page="changePage"
            />

            <Dialog v-model:open="isItemsDialogOpen">
                <DialogContent class="max-h-[80vh] overflow-y-auto sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>
                            {{ selectedOrder ? `Order #${selectedOrder.order_number} Items` : 'Order Items' }}
                        </DialogTitle>
                        <DialogDescription>
                            Products included in this shipped order.
                        </DialogDescription>
                    </DialogHeader>

                    <div v-if="selectedOrder" class="space-y-3">
                        <div
                            v-for="item in selectedOrder.order_items"
                            :key="item.id"
                            class="flex items-center gap-3 rounded border p-3"
                        >
                            <img
                                :src="item.product?.image_url || 'https://via.placeholder.com/150'"
                                alt="Product image"
                                class="h-14 w-14 rounded object-cover"
                            />
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium">{{ item.product?.name || 'Unknown product' }}</p>
                                <p class="text-sm text-muted-foreground">Qty: {{ item.quantity }}</p>
                            </div>
                            <p class="text-sm font-medium">{{ formatCurrency(item.unit_price) }}</p>
                        </div>

                        <p v-if="selectedOrder.order_items.length === 0" class="text-sm text-muted-foreground">
                            No items found for this order.
                        </p>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
