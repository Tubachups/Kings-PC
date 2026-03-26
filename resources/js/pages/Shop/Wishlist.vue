<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Heart, ShoppingCart, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { useCart } from '@/composables/useCart';
import Layout from '@/layouts/MainLayout.vue';
import type { Product } from '@/types/product';
import { getFilteredImageUrl } from '@/utils/helpers';

defineOptions({ layout: Layout });

const props = defineProps<{
    products: Product[];
}>();

const { handleAddToCart } = useCart();

const hasProducts = computed<boolean>(() => props.products.length > 0);

const toggleWishlist = (productId: number): void => {
    router.post(`/wishlist/${productId}`, {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Wishlist">
        <meta head-key="description" name="description" content="See your saved PC components and move them to cart when you're ready." />
    </Head>

    <div class="mx-auto max-w-7xl px-4 py-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">My Wishlist</h1>
                <p class="text-sm text-muted-foreground">Save products you want to buy later and add them to cart in one click.</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                <Heart class="mr-2 h-4 w-4" /> {{ products.length }} saved
            </span>
        </div>

        <div v-if="!hasProducts" class="rounded-xl border border-dashed p-10 text-center">
            <p class="mb-4 text-muted-foreground">Your wishlist is empty right now.</p>
            <Button as-child class="cursor-pointer">
                <a href="/components">Browse components</a>
            </Button>
        </div>

        <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="product in products" :key="product.id" class="overflow-hidden">
                <CardContent class="p-0">
                    <img
                        :src="getFilteredImageUrl(product.image_url)"
                        :alt="product.name"
                        class="h-48 w-full object-cover"
                    />
                    <div class="space-y-3 p-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-muted-foreground">{{ product.category.name }}</p>
                            <h2 class="line-clamp-2 font-semibold">{{ product.name }}</h2>
                        </div>

                        <p class="text-lg font-semibold tracking-wide text-green-600">₱{{ product.price }}</p>

                        <div class="flex items-center gap-2">
                            <Button class="flex-1 cursor-pointer" @click="handleAddToCart(product)">
                                <ShoppingCart class="mr-2 h-4 w-4" /> Add to Cart
                            </Button>
                            <Button variant="destructive" class="cursor-pointer" @click="toggleWishlist(product.id)">
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
