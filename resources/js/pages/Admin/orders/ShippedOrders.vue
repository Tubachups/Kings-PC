<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    advancePendingOrder,
    shippedOrders as shippedOrdersAction,
} from '@/actions/App/Http/Controllers/Admin/ProductController';
import PaginationControls from '@/components/PaginationControls.vue';
import OrderItemsDialog from '@/components/shop/orders/OrderItemsDialog.vue';
import OrderList from '@/components/shop/orders/OrderList.vue';
import OrdersHeader from '@/components/shop/orders/OrdersHeader.vue';
import { Dialog } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { PaginatedOrders, PendingOrder } from '@/types/orders';
import { pesoFormatter } from '@/utils/helpers';

const props = defineProps<{
    orders: PaginatedOrders;
}>();

const formattedOrders = computed(() => {
    return props.orders.data.map((order) => ({
        ...order,
        totalFormatted: pesoFormatter.format(Number(order.total || 0)),
    }));
});

const breadcrumbs = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Shipped Orders', href: shippedOrdersAction().url },
];

const isItemsDialogOpen = ref(false);
const selectedOrder = ref<PendingOrder | null>(null);
const advancingOrderId = ref<number | null>(null);

const openItemsDialog = (order: PendingOrder) => {
    selectedOrder.value = order;
    isItemsDialogOpen.value = true;
};

const changePage = (page: number) => {
    router.get(
        shippedOrdersAction().url,
        { page, per_page: props.orders.per_page },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const advanceOrderStatus = (order: PendingOrder) => {
    if (!order.next_status || advancingOrderId.value === order.id) {
        return;
    }

    advancingOrderId.value = order.id;

    router.patch(
        advancePendingOrder(order.id).url,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                advancingOrderId.value = null;
            },
        },
    );
};
</script>

<template>
    <Head title="Shipped Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <OrdersHeader title="Shipped Orders" description="Orders in transit and awaiting customer delivery confirmation." />

            <OrderList
                :formattedOrders
                :advancingOrderId
                @open-items-dialog="openItemsDialog"
                @advance-order-status="advanceOrderStatus"
            />

            <PaginationControls
                v-if="orders.total > orders.per_page"
                :total-items="orders.total"
                :current-page="orders.current_page"
                :items-per-page="orders.per_page"
                @update:page="changePage"
            />

            <Dialog v-model:open="isItemsDialogOpen">
                <OrderItemsDialog :selectedOrder />
            </Dialog>
        </div>
    </AppLayout>
</template>
