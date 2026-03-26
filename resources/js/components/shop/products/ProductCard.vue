<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Heart } from 'lucide-vue-next';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { Button } from '@/components/ui/button';
import { useCart } from '@/composables/useCart';
import type { Product } from '@/types/product';
import { getFilteredImageUrl } from '@/utils/helpers';

const { handleAddToCart } = useCart();
const page = usePage();
const { product, isLoading = false } = defineProps<{
    product?: Product;
    isLoading?: boolean;
    viewMode?: 'grid' | 'list';
}>();

const filteredImageUrl = computed(() =>
    product?.image_url ? getFilteredImageUrl(product.image_url) : ''
);

const wishlistProductIds = computed<number[]>(() => {
    const value = page.props.wishlistProductIds;

    if (!Array.isArray(value)) {
        return [];
    }

    return value
        .map((item) => Number(item))
        .filter((item) => Number.isFinite(item));
});

const isWishlisted = computed<boolean>(() => {
    if (!product) {
        return false;
    }

    return wishlistProductIds.value.includes(product.id);
});

const toggleWishlist = (): void => {
    if (!product) {
        return;
    }

    const wasWishlisted = isWishlisted.value;

    router.post(`/wishlist/${product.id}`, {}, {
        preserveScroll: true,
        preserveState: true,
        only: ['wishlistProductIds', 'flash'],
        onSuccess: () => {
            toast.success(wasWishlisted ? 'Removed from wishlist.' : 'Added to wishlist.');
        },
        onError: () => {
            toast.error('Unable to update wishlist right now.');
        },
    });
};
</script>

<template>
    <template v-if="isLoading">
        <Skeleton class="mb-1 h-6 w-20 rounded-2xl" />

        <Skeleton class="my-2 aspect-square w-full rounded-lg" />

        <Skeleton class="mt-2 mb-2 h-6 w-3/4" />

        <Skeleton class="mb-4 h-7 w-1/3" />

        <div class="mb-4 rounded bg-muted/60 p-3 text-sm">
            <Skeleton class="mb-3 h-5 w-1/2" />
            <div class="mt-2 space-y-2 border-t border-border pt-2">
                <div
                    v-for="i in 4"
                    :key="`spec-skel-${i}`"
                    class="flex justify-between py-1"
                >
                    <Skeleton class="h-4 w-1/3" />
                    <Skeleton class="h-4 w-1/4" />
                </div>
            </div>
        </div>

        <Skeleton class="mt-auto h-10 w-full rounded" />
    </template>

    <template v-else-if="product && viewMode === 'list'">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <img
                :src="filteredImageUrl"
                :alt="product.name"
                class="h-32 w-full rounded-lg bg-card object-cover sm:h-36 sm:w-36 sm:shrink-0"
            />

            <div class="flex min-w-0 flex-1 flex-col">
                <h2 class="leading-tight font-bold">
                    {{ product.name }}
                </h2>
                <p class="mb-2 text-lg font-semibold tracking-wider text-green-600">
                    ₱{{ product.price }}
                </p>

                <div
                    v-if="product.specs && Object.keys(product.specs).length > 0"
                    class="mt-2 rounded-md border border-border bg-muted/40 p-3"
                >
                    <p class="mb-2 text-xs font-semibold tracking-wide text-foreground uppercase">
                        Specifications
                    </p>
                    <ul class="grid gap-1.5 text-sm sm:grid-cols-2">
                        <li
                            v-for="(value, key) in product.specs"
                            :key="`list-spec-${key}`"
                            class="flex items-start justify-between gap-2 border-b border-border/50 pb-1 "
                        >
                            <span class="text-muted-foreground capitalize">{{ String(key).replace('_', ' ') }}:</span>
                            <span class="text-right font-medium text-foreground">{{ value }}</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-4 flex items-center gap-3" v-if="page.props.auth && page.props.auth.user">
                    <Button
                        @click="handleAddToCart(product)"
                        class="w-full cursor-pointer px-6 py-2 font-bold sm:w-auto"
                    >
                        Add to Cart
                    </Button>
                    <Button
                        variant="outline"
                        class="cursor-pointer"
                        :aria-label="isWishlisted ? 'Remove from wishlist' : 'Add to wishlist'"
                        @click="toggleWishlist"
                    >
                        <Heart class="h-4 w-4" :class="isWishlisted ? 'fill-red-500 text-red-500' : ''" />
                    </Button>
                </div>
            </div>
        </div>
    </template>

    <template v-else-if="product">
        <div class="flex h-full flex-col">
            <img
                :src="filteredImageUrl"
                :alt="product.name"
                class="my-2 aspect-square w-full rounded-lg bg-card object-cover"
            />
            <h2 class="mt-2 mb-2  leading-tight font-bold">
                {{ product.name }}
            </h2>
            <p class="mb-4 text-lg font-semibold tracking-wider text-green-600">
                ₱{{ product.price }}
            </p>
            <div class="mb-4 rounded bg-muted/60 p-3 text-sm">
                <h3 class="mb-2 border-b border-border pb-1 font-bold text-foreground">
                    Specifications
                </h3>
                <ul>
                    <template v-for="(value, key) in product.specs" :key="key">
                        <li
                            class="flex justify-between py-1"
                        >
                            <span class="text-muted-foreground capitalize"
                                >{{ String(key).replace('_', ' ') }}:</span
                            >
                            <span class="font-medium text-foreground">{{
                                value
                            }}</span>
                        </li>
                    </template>
                </ul>
            </div>

            <div v-if="page.props.auth && page.props.auth.user" class="mt-auto flex items-center gap-2">
                <Button @click="handleAddToCart(product)" class="w-full cursor-pointer font-bold">
                    Add to Cart
                </Button>
                <Button
                    variant="outline"
                    class="cursor-pointer"
                    :aria-label="isWishlisted ? 'Remove from wishlist' : 'Add to wishlist'"
                    @click="toggleWishlist"
                >
                    <Heart class="h-4 w-4" :class="isWishlisted ? 'fill-red-500 text-red-500' : ''" />
                </Button>
            </div>
        </div>
    </template>
</template>
