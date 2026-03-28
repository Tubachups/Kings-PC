<script setup lang="ts">
import { X } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { useCart } from '@/composables/useCart';
import { formatCurrency } from '@/utils/helpers';

const props = defineProps<{
    shippingFee: number;
    couponDiscount: number;
}>();

const { items, subTotal, deleteCartItem } = useCart();

const reviewTotal = computed(
    () => subTotal.value + props.shippingFee - props.couponDiscount,
);

const filteredImageUrl = (imageUrl: string): string => {
    if (!imageUrl) return '';
    return imageUrl.startsWith('/storage')
        ? imageUrl.replace(/^\/storage/, '')
        : imageUrl;
};
</script>

<template>
    <div
        class="rounded-xl border border-border/70 bg-card p-4 shadow-sm sm:p-6"
    >
        <div>
            <h2 class="text-xl font-semibold tracking-tight sm:text-2xl">
                Shopping Cart
            </h2>
            <p class="mt-1 text-sm text-muted-foreground">
                Review your items before proceeding to checkout.
            </p>
        </div>

        <div class="mt-6 overflow-hidden rounded-lg border border-border/70">
            <div
                class="hidden justify-between border-b bg-muted/30 px-4 py-3 text-sm font-medium sm:flex"
            >
                <div>Product</div>
                <div>Price</div>
            </div>

            <div
                v-for="item in items"
                :key="item.id"
                class="flex flex-col gap-4 border-b border-border/60 p-4 last:border-b-0 sm:flex-row sm:items-center sm:justify-between sm:py-5"
            >
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-2">
                    <div class="flex flex-row items-center justify-center gap-2">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 shrink-0 cursor-pointer text-muted-foreground hover:text-foreground"
                            @click="deleteCartItem(item)"
                        >
                            <X class="h-4 w-4" />
                        </Button>
                        <img
                            :src="filteredImageUrl(item.product.image_url)"
                            :alt="item.product.name"
                            class="h-20 w-20 shrink-0 rounded-lg border bg-muted/50 object-cover sm:h-25 sm:w-25"
                        />
                    </div>

                    <p class="text-center text-sm sm:text-base">
                        {{ item.product.name }}
                    </p>
                </div>

                <div
                    class="flex items-center justify-between rounded-lg bg-muted/30 p-3 font-semibold sm:bg-transparent sm:p-0 sm:text-right"
                >
                    <span
                        class="text-xs tracking-wide text-muted-foreground uppercase sm:hidden"
                        >Price</span
                    >
                    <span>{{ item.quantity }} x {{ formatCurrency(item.product.price) }}</span>
                </div>
            </div>
        </div>

        <div class="mt-6 sm:mt-8">
            <div
                class="space-y-3 rounded-lg bg-muted/20 p-4 text-sm sm:text-base"
            >
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Subtotal</span>
                    <span class="font-medium">{{
                        formatCurrency(subTotal)
                    }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Shipping Cost</span>
                    <span class="font-medium">{{
                        formatCurrency(shippingFee)
                    }}</span>
                </div>
                <div
                    class="flex justify-between gap-4 pt-1 font-semibold sm:text-xl"
                >
                    <span>Total Payable</span>
                    <span>{{ formatCurrency(reviewTotal) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
