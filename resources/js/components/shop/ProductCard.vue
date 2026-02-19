<script setup lang="ts">
import { Product } from '@/types/product';
import { router, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const page = usePage();


const {product} = defineProps<{
    product: Product
} > ()

const updateCartQuantity = (product: Product) => {
    const cart = page.props.cart as Record<number, number>;
    const currentQty = cart[product.id] || 0;
    router.put(`/cart/${product.id}`, {
        quantity: currentQty + 1
    } , {
        preserveScroll: true,
        preserveState: true,
        only: ['cart', 'product'],
        showProgress: false,
        onSuccess: () => {
            toast.success("Cart Updated", {
                description: `${product.name} is now in your cart.`,
            });
        },
        onError: (error) => {
            toast.error("Error occured!");
            console.error(error);
        }
    });
}


const categoryColors: Record<string, string> = {
    gpu:'bg-red-600',
    case: 'bg-blue-600',
    cooling: 'bg-purple-600',
    cpu: 'bg-green-600',
    motherboard: 'bg-yellow-600',
    ram: 'bg-orange-600',
    'ssd/hdd': 'bg-lime-600',
    psu: 'bg-indigo-600',
}

</script>

<template>

    <div :class="[categoryColors[product.category?.name?.toLowerCase()] ?? 'bg-gray-600', 'text-center rounded-2xl w-fit px-3']">
        <span class="text-xs font-semibold text-white uppercase tracking-wider">
            {{ product.category?.name }}
        </span>
    </div>

    <img
    :src="product.image_url"
    :alt="product.name"
    class="aspect-square w-full my-2 object-cover rounded-lg"
    />  


    <h2 class="text-lg font-bold mt-2 mb-2 leading-tight">
        {{ product.name }}
    </h2>

    <p class="text-xl text-green-600 font-extrabold mb-4">
        ₱{{ product.price }}
    </p>

    <div class="bg-gray-50 rounded p-3 text-sm mb-4">
        <h3 class="font-bold border-b pb-1 mb-2 text-gray-700">Specifications</h3>
        <ul>
            <li v-for="(value, key) in product.specs" :key="key" class="flex justify-between py-1">
                <span class="capitalize text-gray-600">{{ key . replace('_', ' ') }}:</span>
                <span class="font-medium text-gray-900">{{ value }}</span>
            </li>
        </ul>
    </div>

    <button @click="updateCartQuantity(product)" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 transition duration-300">
        Add to Cart
    </button>
</template>
