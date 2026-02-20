<script setup lang="ts">
import { useCartStore } from '@/stores/cartStore';
import { Product } from '@/types/product';
import { router, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const page = usePage();


const {product} = defineProps<{
    product: Product
} > ()
const cartStore = useCartStore()
const updateCartQuantity = (product: Product) => {
    const existingItem = cartStore.items.find(item => item?.product?.id === product.id);
    const currentQty = existingItem ? existingItem.quantity : 0;
    const newQty = currentQty + 1

    if (existingItem) {
        cartStore.updateQuantity(product.id, newQty)
    } else {
            cartStore.addItem({
            product_id: product.id,
            quantity: newQty,
            product: {
                id: product.id,
                name: product.name,
                price: product.price,
                image_url: product.image_url
            }
        })
    }

    router.put(`/cart/${product.id}`, {
        quantity: newQty
    } , {
        preserveScroll: true,
        preserveState: true,
        except: ['cart'],
        showProgress: false,
        onStart: () => {
            toast.success("Cart Updated", {
                description: `${product.name} is now in your cart.`,
            });
        },
        onError: (error) => {
            cartStore.updateQuantity(product.id, currentQty)
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

    <div :class="[categoryColors[product.category.name.toLowerCase()] ?? 'bg-gray-600', 'text-center rounded-2xl w-fit px-3']">
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

    <button @click="updateCartQuantity(product)" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 transition duration-300 active:scale-95 active:bg-blue-700">
        Add to Cart
    </button>
</template>
