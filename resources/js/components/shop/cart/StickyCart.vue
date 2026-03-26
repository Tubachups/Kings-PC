<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronDown, ChevronUp, CreditCard, ShoppingCart, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import CartItems from '@/components/shop/cart/CartItems.vue';
import TotalCard from '@/components/shop/cart/TotalCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useCart } from '@/composables/useCart';
import { formatCurrency } from '@/utils/helpers';

const { clearCart, isSyncing, items, subTotal, totalItems } = useCart();
const isExpanded = ref(false);

const formattedSubtotal = computed(() => {
    return formatCurrency(subTotal.value);
});

const toggleCart = (): void => {
    isExpanded.value = !isExpanded.value;
};

const collapseCart = (): void => {
    isExpanded.value = false;
};
</script>

<template>
    <div>
        <div
            class="fixed inset-x-0 bottom-0 z-40 border-t bg-background/95 backdrop-blur supports-backdrop-filter:bg-background/70 ">
            <div class="mx-auto w-full max-w-7xl">
                <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6">
                    <button type="button" class="flex min-w-0 items-center gap-3 text-left cursor-pointer"
                        @click="toggleCart">
                        <span class="relative inline-flex">
                            <ShoppingCart class="h-6 w-6 shrink-0" />
                            <Badge v-if="totalItems > 0" variant="destructive"
                                class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full p-0 text-[10px]">
                                {{ totalItems }}
                            </Badge>
                        </span>
                        <span class="truncate  font-semibold">Your Cart</span>
                        <span class="truncate  text-muted-foreground">{{ formattedSubtotal }}</span>
                    </button>

                    <div class="flex items-center gap-2">
                        <Button variant="ghost" class="gap-2 text-destructive hover:text-destructive border cursor-pointer"
                            :disabled="isSyncing" @click="clearCart">
                            <Trash2 class="h-4 w-4" />
                            Delete all
                        </Button>
                        <Link href="/checkout" @click="collapseCart"
                            class=" h-9 items-center justify-center gap-2 rounded-md border px-3 text-sm font-medium transition hover:bg-accent sm:px-4 sm:text-base hidden md:inline-flex">
                            <CreditCard class="h-4 w-4" />
                            <span>Checkout</span>
                        </Link>

                        <Button variant="ghost" size="icon" @click="toggleCart" class="cursor-pointer">
                            <ChevronUp v-if="isExpanded" class="h-4 w-4" />
                            <ChevronDown v-else class="h-4 w-4" />
                        </Button>
                    </div>
                </div>

                <Transition name="cart-panel">
                    <div v-if="isExpanded" class="border-t">
                        <div class="max-h-[66vh] overflow-y-auto px-4 py-4 sm:px-6">
                            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0 flex-1">
                                    <div v-if="items.length > 0" class="mb-4 flex items-center justify-end">

                                    </div>

                                    <CartItems />
                                </div>

                                <div class="w-full lg:w-80 lg:shrink-0">
                                    <TotalCard :items="items" :show-checkout-button="true"
                                        @checkout-click="collapseCart" />
                                </div>

                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>

        <div :class="isExpanded ? 'h-[72vh]' : 'h-24'" />
    </div>
</template>

<style scoped>
.cart-panel-enter-active,
.cart-panel-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.cart-panel-enter-from,
.cart-panel-leave-to {
    opacity: 0;
    transform: translateY(8px);
}
</style>
