<script setup lang="ts">
import { DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import type { PendingOrder } from '@/types/orders';
import { formatCurrency } from '@/utils/helpers';

defineProps<{
    selectedOrder: PendingOrder | null;
}>()

</script>
<template>
    <DialogContent class="max-h-[80vh] overflow-y-auto sm:max-w-2xl">
        <DialogHeader>
            <DialogTitle>
                {{ selectedOrder ? `Order #${selectedOrder.order_number} Items` : 'Order Items' }}
            </DialogTitle>
            <DialogDescription>
                Products included in this delivered order.
            </DialogDescription>
        </DialogHeader>

        <div v-if="selectedOrder" class="space-y-3">
            <div v-for="item in selectedOrder.order_items" :key="item.id"
                class="flex items-center gap-3 rounded border p-3">
                <img :src="item.product?.image_url || 'https://via.placeholder.com/150'" alt="Product image"
                    class="h-14 w-14 rounded object-cover" />
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
</template>
