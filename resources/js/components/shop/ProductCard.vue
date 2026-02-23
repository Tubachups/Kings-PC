<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';
import { useCart } from '@/composables/useCart';
import { Product } from '@/types/product';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';


const filteredImageUrl = computed(() => {
    if (!product?.image_url) return '';
    return product.image_url.startsWith('/storage')
        ? product.image_url.replace(/^\/storage/, '')
        : product.image_url;
});

// Added isLoading prop and made product optional so you can render skeletons safely
const { product, isLoading = false } = defineProps<{
    product: Product;
    isLoading?: boolean;
}>();

const { updateQuantity, items } = useCart();

const handleAddToCart = () => {
    const existingItem = items.value.find(i => i.product.id === product.id);

    const newQty = existingItem ? existingItem.quantity + 1 : 1;

    updateQuantity(product, newQty);

}

const categoryColors: Record<string, string> = {
    gpu: 'bg-red-600',
    case: 'bg-blue-600',
    cooling: 'bg-purple-600',
    cpu: 'bg-green-600',
    motherboard: 'bg-yellow-600',
    ram: 'bg-orange-600',
    'ssd/hdd': 'bg-lime-600',
    psu: 'bg-indigo-600',
};
</script>

<template>
    <template v-if="isLoading">
        <Skeleton class="mb-1 h-6 w-20 rounded-2xl" />

        <Skeleton class="my-2 aspect-square w-full rounded-lg" />

        <Skeleton class="mt-2 mb-2 h-6 w-3/4" />

        <Skeleton class="mb-4 h-7 w-1/3" />

        <div class="mb-4 rounded bg-gray-50 p-3 text-sm">
            <Skeleton class="mb-3 h-5 w-1/2" />
            <div class="mt-2 space-y-2 border-t border-gray-200 pt-2">
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

    <template v-else-if="product">
        <img
            :src="filteredImageUrl"
            :alt="product.name"
            class="my-2 aspect-square w-full rounded-lg object-cover"
        />

        <h2 class="mt-2 mb-2 text-lg leading-tight font-bold">
            {{ product.name }}
        </h2>

        <p class="mb-4 text-xl font-extrabold text-green-600">
            ₱{{ product.price }}
        </p>

        <div class="mb-4 rounded bg-gray-50 p-3 text-sm">
            <h3 class="mb-2 border-b pb-1 font-bold text-gray-700">
                Specifications
            </h3>
            <ul>
                <template v-for="(value, key) in product.specs" :key="key">
                    <li
                        v-if="
                            !(
                                String(key).toLowerCase() === 'tdp' &&
                                product.category?.name?.toLowerCase() === 'cpu'
                            )
                        "
                        class="flex justify-between py-1"
                    >
                        <span class="text-gray-600 capitalize"
                            >{{ String(key).replace('_', ' ') }}:</span
                        >
                        <span class="font-medium text-gray-900">{{
                            value
                        }}</span>
                    </li>
                </template>
            </ul>
        </div>

    <button @click="handleAddToCart" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 transition duration-300 active:scale-95 active:bg-blue-700">
        Add to Cart
    </button>
    </template>
</template>
