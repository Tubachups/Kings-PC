<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCart } from '@/composables/useCart';
import type { CartItem } from '@/types/cart';
import { formatCurrency } from '@/utils/helpers';
import { Minus, Plus, ShoppingCart, Trash2Icon } from 'lucide-vue-next';

const { deleteCartItem, updateQuantity, items } = useCart();

const filteredImageUrl = (imageUrl: string): string => {
    if (!imageUrl) {
        return '';
    }

    return imageUrl.startsWith('/storage')
        ? imageUrl.replace(/^\/storage/, '')
        : imageUrl;
};

const increaseQuantity = (item: CartItem): void => {
    updateQuantity(item.product, item.quantity + 1);
};

const decreaseQuantity = (item: CartItem): void => {
    if (item.quantity <= 1) {
        return;
    }

    updateQuantity(item.product, item.quantity - 1);
};
</script>

<template>
    <div class="w-full rounded-2xl border border-border/70 bg-background/60">
        <div v-if="items.length === 0"
            class="flex min-h-56 flex-col items-center justify-center gap-3 px-4 py-10 text-center text-muted-foreground">
            <ShoppingCart class="h-8 w-8" />
            <p class="text-sm font-medium sm:text-base">Your cart is empty.</p>
        </div>

        <div v-else class="w-full">
            <div v-for="(item, index) in items" :key="item.id">
                <div class="flex items-start gap-3 p-3 sm:gap-4 sm:p-5">
                    <img :src="filteredImageUrl(item.product.image_url)" :alt="item.product.name"
                        class="h-16 w-16 shrink-0 rounded-xl border object-cover sm:h-20 sm:w-20 bg-muted/50" />

                    <div class="min-w-0 flex-1 flex flex-col justify-between h-full">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class=" text-sm sm:text-base font-semibold">{{ item.product.name }}</p>
                            </div>

                            <Button variant="ghost" size="icon" class="h-8 w-8 shrink-0 text-muted-foreground hover:text-foreground" @click="deleteCartItem(item)">
                                <Trash2Icon class="h-4 w-4" />
                            </Button>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center justify-between gap-2 sm:mt-4 sm:gap-3">
                            <div class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-border/80 bg-background p-1 sm:gap-2">
                                <Button variant="ghost" class="h-6 w-6 p-0 sm:h-8 sm:w-8" :disabled="item.quantity <= 1"
                                    @click="decreaseQuantity(item)">
                                    <Minus class="h-3 w-3 sm:h-4 sm:w-4" />
                                </Button>
                                <span class="w-6 sm:w-8 text-center text-sm font-semibold">{{ item.quantity }}</span>
                                <Button variant="ghost" class="h-6 w-6 p-0 sm:h-8 sm:w-8" @click="increaseQuantity(item)">
                                    <Plus class="h-3 w-3 sm:h-4 sm:w-4" />
                                </Button>
                            </div>

                            <div class="text-right shrink-0">
                                <p class="font-semibold leading-none text-sm sm:text-base">
                                    {{ formatCurrency(item.quantity * item.product.price) }}
                                </p>
                                <p v-if="item.quantity > 1" class="mt-1 text-[10px] sm:text-xs text-muted-foreground line-through">
                                    {{ formatCurrency(item.product.price) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <Separator v-if="index !== items.length - 1" />
            </div>
        </div>
    </div>
</template>
