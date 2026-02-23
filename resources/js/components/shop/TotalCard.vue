<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import { Separator } from '@/components/ui/separator';
import { CartItem } from '@/types/cart';
import { useCart } from '@/composables/useCart';

const props = withDefaults(defineProps<{
    items: CartItem[];
    isCheckout?: boolean;
    shippingFee?: number;
}>(), {
    isCheckout: false,
    shippingFee: 0,
});

const { subTotal } = useCart();
</script>

<template>
    <Card class="w-full h-full">
    <CardHeader>
        <CardTitle class="text-4xl">Cart Total</CardTitle>
    </CardHeader>
    <CardContent>
        <ScrollArea class="h-96">
            <div class="flex flex-col gap-2 pr-4">
                <div
                    v-for="item in items"
                    :key="item.product.id"
                    class="flex justify-between text-sm"
                >
                    <span class="text-muted-foreground">
                        {{ item.product.name }}
                        <span class="text-xs">(x{{ item.quantity }})</span>
                    </span>
                    <span class="font-medium">
                        ₱{{ (item.quantity * item.product.price).toLocaleString() }}
                    </span>
                </div>
            </div>
        </ScrollArea>
        <div v-if="isCheckout" class="flex justify-between font-medium text-sms">
            <span>Shipping Fee</span>
            <span>₱{{ shippingFee.toLocaleString() }}</span>
        </div>
        <Separator class="my-2" />
        <div class="flex justify-between font-extrabold text-lg">
            <span>Total</span>
            <span>₱{{ (subTotal + shippingFee).toLocaleString() }}</span>
        </div>
    </CardContent>
</Card>
</template>