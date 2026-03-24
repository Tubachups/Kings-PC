<script setup lang="ts">
import dayjs from 'dayjs'
import {
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { formatCurrency, formatAddress } from '@/utils/helpers';

defineProps<{
    isLoading: boolean
    selectedOrder?: any
}>();


</script>

<template>
    <DialogContent class="max-h-[80vh] overflow-y-auto sm:max-w-2xl">
        <DialogTitle class="sr-only">
            {{ selectedOrder ? `Order #${selectedOrder.order_number}` : 'Order details' }}
        </DialogTitle>
        <DialogDescription class="sr-only">
            {{ selectedOrder ? `Placed on ${dayjs(selectedOrder.created_at).format('MMMM D, YYYY h:mm A')}` : 'View order details and items.' }}
        </DialogDescription>

        <div v-if="isLoading" class="flex justify-center p-8">
            <p>Loading items...</p> </div>

        <div v-else-if="selectedOrder" class="space-y-4">
            <div>
                <h2 class="text-lg font-semibold">Order #{{ selectedOrder.order_number }}</h2>
                <DialogDescription class="text-sm text-muted-foreground">
                    Placed on {{ dayjs(selectedOrder.created_at).format('MMMM D, YYYY h:mm A') }}
                </DialogDescription>
            </div>

            <div class="grid gap-2 text-sm">
                <p><span class="font-medium">Status:</span> {{ selectedOrder.status }}</p>
                <p><span class="font-medium">Payment:</span> {{ selectedOrder.payment_method?.toUpperCase() }} ({{ selectedOrder.payment_status }})</p>
                <p><span class="font-medium">Total:</span> {{ formatCurrency(selectedOrder.total) }}</p>
            </div>

            <div class="grid gap-3 text-sm sm:grid-cols-2">
                <div>
                    <h4 class="mb-1 font-medium">Shipping Address</h4>
                    <p class="text-muted-foreground">{{ formatAddress(selectedOrder.shipping_address) }}</p>
                </div>
                <div>
                    <h4 class="mb-1 font-medium">Billing Address</h4>
                    <p class="text-muted-foreground">{{ formatAddress(selectedOrder.billing_address) }}</p>
                </div>
            </div>

            <div>
                <h4 class="mb-2 font-medium">Items</h4>
                <div
                    v-for="item in selectedOrder.order_items"
                    :key="item.id"
                    class="mb-2 rounded border p-3 last:mb-0 flex flex-col  gap-2 sm:flex-row items-center "
                >
                    <img
                        :src="item.product?.image_url || 'https://via.placeholder.com/150'"
                        alt="Product Image"
                        class="mb-2 h-20 w-20 rounded object-cover"
                    />
                    <p class="font-medium">{{ item.product?.name || 'Unknown product' }}</p>
                    <p class="text-sm text-muted-foreground">
                        Qty: {{ item.quantity }} | Unit Price: {{ formatCurrency(item.unit_price) }}
                    </p>
                </div>
            </div>
        </div>
    </DialogContent>
</template>
