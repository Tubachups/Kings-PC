<script setup lang="ts">
import CartItems from '@/components/shop/CartItems.vue';
import { useCart } from '@/composables/useCart';
import TotalCard from '@/components/shop/TotalCard.vue';
import Layout from '@/layouts/MainLayout.vue';
import { Link } from '@inertiajs/vue3';
defineOptions({ layout: Layout });

const { items, subTotal } = useCart();
</script>

<template>
    <div class="flex flex-col h-screen w-full overflow-hidden">

        <div class="flex h-16 shrink-0 justify-center items-center">
            <header class="text-5xl font-extrabold">My Cart</header>
        </div>

        <div class="flex flex-row flex-1 h-full w-full">
            <div class="flex-2 h-full overflow-y-auto p-6 mb-2">
                <CartItems />
            </div>

            <div class="flex-1 h-full overflow-y-auto p-6 mb-2 w-full">
                <div class="">
                    <TotalCard :items="items" />
                </div>
                <Link href="/checkout" class="bg-black text-white p-3 rounded-xl w-full mt-2 block text-center">
                    Proceed to checkout
                </Link>
            </div>
        </div>

    </div>
</template>