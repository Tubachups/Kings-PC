<script setup lang="ts">
import dayjs from 'dayjs'
import DialogContent from '@/components/ui/dialog/DialogContent.vue';

const props = defineProps<{
    isLoading: boolean
    selectedOrder?: any
}>();

const formatCurrency = (amount: string | number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(Number(amount || 0));
};

const formatAddress = (address?: Record<string, any>) => {
    if (!address) return 'N/A';
    return [
        address.address,
        address.barangay,
        address.city,
        address.province,
        address.region,
    ]
        .filter(Boolean)
        .join(', ');
};

</script>

<template>
    <DialogContent class="max-h-[80vh] overflow-y-auto sm:max-w-2xl">
        <div v-if="isLoading" class="flex justify-center p-8">
            <p>Loading items...</p> </div>

        <div v-else-if="selectedOrder" class="space-y-4">
            <div>
                <h3 class="text-lg font-semibold">Order #{{ selectedOrder.order_number }}</h3>
                <p class="text-sm text-muted-foreground">
                    Placed on {{ dayjs(selectedOrder.created_at).format('MMMM D, YYYY h:mm A') }}
                </p>
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
                    class="mb-2 rounded border p-3 last:mb-0 flex flex-col items-start gap-2 sm:flex-row sm:items-center"
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
