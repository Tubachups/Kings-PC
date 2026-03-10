<script setup lang="ts">
import { Mail, Package } from 'lucide-vue-next';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { PendingOrder } from '@/types/orders';
import { formatDate } from '@/utils/helpers';

type FormattedOrder = PendingOrder & {
    totalFormatted: string;
};

defineProps<{
    formattedOrders: FormattedOrder[];
    advancingOrderId: number | null;
}>();

const emit = defineEmits<{
    openItemsDialog: [order: PendingOrder];
    advanceOrderStatus: [order: PendingOrder];
}>();
</script>
<template>
    <div class="grid gap-4 md:grid-cols-2">
        <Card v-for="order in formattedOrders" :key="order.id">
            <CardHeader>
                <CardTitle class="text-base"
                    >Order #{{ order.order_number }}</CardTitle
                >
                <CardDescription>
                    {{ order.status || order.payment_status || 'Pending' }}
                    -
                    {{
                        order.payment_method
                            ? order.payment_method.toUpperCase()
                            : 'Unknown payment method'
                    }}
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-2 text-sm">
                <p>
                    <span class="font-medium">Customer:</span>
                    {{ order.customer?.name || 'Unknown customer' }}
                </p>
                <p>
                    <span class="font-medium">Status:</span>
                    {{ order.status || 'Pending' }}
                </p>
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
                    class="flex cursor-pointer items-center gap-1 text-left hover:text-primary"
                    @click="emit('openItemsDialog', order)"
                >
                    <Package class="h-4 w-4 text-muted-foreground" />
                    {{ order.items_count }} item(s)
                </button>
                <p>
                    <span class="font-medium">Total:</span>
                    {{ order.totalFormatted }}
                </p>
                <p class="text-xs text-muted-foreground">
                    Created: {{ formatDate(order.created_at || '') || '-' }}
                </p>
                <Button
                    v-if="order.next_action_label"
                    type="button"
                    size="sm"
                    class="mt-2 bg-primary text-primary-foreground hover:bg-primary/90 p-2 rounded-lg"
                    :disabled="advancingOrderId === order.id"
                    @click="emit('advanceOrderStatus', order)"
                >
                    {{
                        advancingOrderId === order.id
                            ? 'Updating...'
                            : order.next_action_label
                    }}
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
