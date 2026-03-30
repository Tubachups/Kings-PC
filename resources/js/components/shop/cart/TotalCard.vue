<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Package, WalletCards } from 'lucide-vue-next';
import { computed } from 'vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import { Separator } from '@/components/ui/separator';
import type { CartItem } from '@/types/cart';
import { formatCurrency } from '@/utils/helpers';

const props = withDefaults(defineProps<{
    items: CartItem[];
    isCheckout?: boolean;
    shippingFee?: number;
    showCheckoutButton?: boolean;
}>(), {
    isCheckout: false,
    shippingFee: 0,
    showCheckoutButton: false,
});
const emit = defineEmits<{
    checkoutClick: [];
}>();

const freeShippingThreshold = 200;

const subtotal = computed(() => {
    return props.items.reduce((total, item) => {
        return total + item.quantity * item.product.price;
    }, 0);
});

const appliedShippingFee = computed(() => {
    if (props.isCheckout) {
        return props.shippingFee;
    }

    return 0;
});

const total = computed(() => {
    return subtotal.value + appliedShippingFee.value;
});

const amountNeededForFreeShipping = computed(() => {
    return Math.max(0, freeShippingThreshold - subtotal.value);
});

const isCheckoutDisabled = computed(() => {
    return subtotal.value <= 0;
});

const handleCheckoutClick = (event: MouseEvent): void => {
    if (isCheckoutDisabled.value) {
        event.preventDefault();

        return;
    }

    emit('checkoutClick');
};

</script>

<template>
    <Card class=" border-border/70 bg-card/90 py-5">
        <CardHeader class="px-5 pb-0">
            <CardTitle class="">Order Summary</CardTitle>
        </CardHeader>

        <CardContent class="space-y-5 px-5">
            <div class="space-y-3 ">
                <div class="flex items-center justify-between">
                    <span>Subtotal</span>
                    <span class="font-medium">{{ formatCurrency(subtotal) }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span>Shipping</span>
                    <span class="font-medium">
                        {{ appliedShippingFee > 0 ? formatCurrency(appliedShippingFee) : 'Free' }}
                    </span>
                </div>
            </div>

            <Separator />

            <div class="flex items-center justify-between font-semibold">
                <span>Total</span>
                <span>{{ formatCurrency(total) }}</span>
            </div>

            <p class="flex items-center gap-2 text-sm text-muted-foreground">
                <Package class="h-4 w-4 shrink-0" />
                <span v-if="amountNeededForFreeShipping === 0">
                    Free shipping on orders over {{ formatCurrency(freeShippingThreshold) }}
                </span>
                <span v-else>
                    Add {{ formatCurrency(amountNeededForFreeShipping) }} for free shipping
                </span>
            </p>

            <Link
                v-if="showCheckoutButton"
                :href="isCheckoutDisabled ? '#' : '/checkout'"
                :aria-disabled="isCheckoutDisabled"
                :tabindex="isCheckoutDisabled ? -1 : 0"
                @click="handleCheckoutClick"
                :class="[
                    'inline-flex h-11 w-full items-center justify-center gap-2 rounded-md border bg-primary px-4 text-base font-semibold text-primary-foreground transition',
                    isCheckoutDisabled
                        ? 'pointer-events-none cursor-not-allowed opacity-50'
                        : 'hover:bg-primary/90',
                ]"
            >
                <WalletCards class="h-4 w-4" />
                Checkout
            </Link>
        </CardContent>
    </Card>
</template>


