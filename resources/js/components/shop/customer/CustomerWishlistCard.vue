<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Heart, ShoppingCart, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useCart } from '@/composables/useCart';
import type { Product } from '@/types/product';
import { getFilteredImageUrl } from '@/utils/helpers';
import type { CustomerPageProps } from './types';

const page = usePage<CustomerPageProps>();
const { handleAddToCart } = useCart();

const customerData = computed(() => page.props.customerData ?? {});
const customerSummary = computed(() => page.props.customerSummary ?? {});
const customerWishlist = computed<Product[]>(() => customerData.value.wishlist ?? []);

const formatPrice = (value: number): string => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2,
    }).format(value);
};

const toggleWishlist = (productId: number): void => {
    router.post(`/wishlist/${productId}`, {}, {
        preserveScroll: true,
        preserveState: true,
        only: ['customerData', 'customerSummary', 'wishlistProductIds', 'flash'],
    });
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-base">
                <Heart class="h-4 w-4" />
                Wishlist
            </CardTitle>
            <CardDescription>
                {{ customerSummary.wishlistCount ?? 0 }} saved items
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-3">
            <div v-if="customerWishlist.length === 0" class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground">
                Wishlist is empty.
            </div>
            <div v-for="product in customerWishlist" :key="`dashboard-wishlist-${product.id}`" class="rounded-lg border p-2">
                <div class="flex items-center gap-2">
                    <img :src="getFilteredImageUrl(product.image_url)" :alt="product.name" class="h-12 w-12 rounded object-cover" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium">{{ product.name }}</p>
                        <p class="text-xs text-green-600">{{ formatPrice(Number(product.price)) }}</p>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <Button size="sm" class="flex-1 cursor-pointer" @click="handleAddToCart(product)">
                        <ShoppingCart class="mr-1 h-3.5 w-3.5" /> Add
                    </Button>
                    <Button size="sm" variant="destructive" class="cursor-pointer" @click="toggleWishlist(product.id)">
                        <Trash2 class="h-3.5 w-3.5" />
                    </Button>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
