<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { CalendarDays } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { formatAddress, formatDate, formatPrice } from '@/utils/helpers';
import type { CustomerPageProps, DashboardOrder } from './types';

const page = usePage<CustomerPageProps>();

const customerData = computed(() => page.props.customerData ?? {});
const customerSummary = computed(() => page.props.customerSummary ?? {});
const customerOrders = computed<DashboardOrder[]>(
    () => customerData.value.orders ?? [],
);
const selectedOrder = ref<DashboardOrder | null>(null);
const isOrderDetailsOpen = ref(false);

const openOrderDetails = (order: DashboardOrder): void => {
    selectedOrder.value = order;
    isOrderDetailsOpen.value = true;
};

const filteredImageUrl = (imageUrl: string | null | undefined): string => {
    if (!imageUrl) {
        return '';
    }

    return imageUrl.startsWith('/storage')
        ? imageUrl.replace(/^\/storage/, '')
        : imageUrl;
};

const formattedOrderAddress = (
    address: Record<string, any> | null | undefined,
): string => {
    return formatAddress(address ?? undefined);
};

const statusPillClass = (status: string): string => {
    const normalizedStatus = status.toLowerCase();

    if (normalizedStatus === 'delivered') {
        return 'bg-green-100 text-green-700';
    }

    if (normalizedStatus === 'shipped' || normalizedStatus === 'processing') {
        return 'bg-blue-100 text-blue-700';
    }

    return 'bg-muted text-muted-foreground';
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-base">
                <CalendarDays class="h-4 w-4" />
                Orders
            </CardTitle>
            <CardDescription>
                {{ customerSummary.ordersCount ?? 0 }} orders placed
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-3">
            <div
                v-if="customerOrders.length === 0"
                class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground"
            >
                No orders yet.
            </div>
            <button
                v-for="order in customerOrders"
                :key="`dashboard-order-${order.id}`"
                type="button"
                class="w-full cursor-pointer rounded-lg border bg-background p-3 text-left text-sm transition-colors hover:bg-muted/40"
                @click="openOrderDetails(order)"
            >
                <div
                    class="mb-1 flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="min-w-0 font-semibold">
                        Order #{{ order.order_number ?? order.id }}
                    </p>
                    <span
                        class="rounded-full px-2 py-0.5 text-[11px]"
                        :class="statusPillClass(order.status)"
                    >
                        {{ order.status }}
                    </span>
                </div>
                <div
                    class="flex flex-col items-start gap-1 text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
                >
                    <span>{{ formatDate(order.created_at) }}</span>
                    <span>{{ formatPrice(order.total) }}</span>
                </div>
            </button>
        </CardContent>
    </Card>

    <Dialog v-model:open="isOrderDetailsOpen">
        <DialogContent
            class="max-h-[80vh] w-[calc(100vw-1rem)] max-w-[calc(100vw-1rem)] overflow-y-auto p-3 sm:max-w-xl sm:p-6"
        >
            <DialogHeader>
                <DialogTitle class="pr-8 text-base sm:text-lg">
                    {{
                        selectedOrder
                            ? `Order #${selectedOrder.order_number ?? selectedOrder.id}`
                            : 'Order details'
                    }}
                </DialogTitle>
                <DialogDescription class="text-xs sm:text-sm">
                    View order items and order summary details.
                </DialogDescription>
            </DialogHeader>

            <div v-if="selectedOrder" class="space-y-4">
                <div
                    class="grid gap-2 text-xs sm:grid-cols-2 sm:gap-3 sm:text-sm"
                >
                    <div class="rounded-lg border p-3">
                        <h4 class="mb-1 font-semibold">Shipping Address</h4>
                        <p class="wrap-break-word text-muted-foreground">
                            {{
                                formattedOrderAddress(
                                    selectedOrder.shipping_address,
                                )
                            }}
                        </p>
                    </div>
                    <div class="rounded-lg border p-3">
                        <h4 class="mb-1 font-semibold">Billing Address</h4>
                        <p class="wrap-break-word text-muted-foreground">
                            {{
                                formattedOrderAddress(
                                    selectedOrder.billing_address,
                                )
                            }}
                        </p>
                    </div>
                </div>

                <div>
                    <h4 class="mb-2 text-sm font-semibold">Items</h4>
                    <div
                        v-if="
                            !selectedOrder.order_items ||
                            selectedOrder.order_items.length === 0
                        "
                        class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground"
                    >
                        No items found for this order.
                    </div>
                    <div
                        v-for="item in selectedOrder.order_items"
                        :key="item.id"
                        class="mb-2 flex flex-col gap-2 rounded-lg border p-3 last:mb-0 sm:flex-row sm:items-center sm:justify-between sm:gap-3"
                    >
                        <div class="flex min-w-0 items-center gap-3">
                            <img
                                :src="
                                    filteredImageUrl(item.product?.image_url) ||
                                    'https://via.placeholder.com/80'
                                "
                                :alt="item.product?.name || 'Order item image'"
                                class="h-12 w-12 rounded object-cover"
                            />
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">
                                    {{
                                        item.product?.name || 'Unknown product'
                                    }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ item.quantity }} x
                                    {{ formatPrice(item.unit_price) }}
                                </p>
                            </div>
                        </div>
                        <p
                            class="text-right text-sm font-semibold sm:text-left"
                        >
                            {{ formatPrice(item.quantity * item.unit_price) }}
                        </p>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
