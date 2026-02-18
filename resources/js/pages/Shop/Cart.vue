<script setup lang="ts">
import CartItems from '@/components/shop/CartItems.vue';
import Layout from '@/layouts/MainLayout.vue';

defineOptions({ layout: Layout });

</script>

<template>
    <div class="flex flex-col h-screen w-full overflow-hidden">

        <div class="flex h-16 shrink-0 justify-center items-center">
            <header class="text-5xl font-extrabold">My Cart</header>
        </div>

        <div class="flex flex-row flex-1 h-full">
            <div class="flex-2 h-full overflow-y-auto p-6 mb-2">
                <CartItems />
            </div>

            <div class="flex-1 bg-blue-800">
                
            </div>
        </div>

    </div>
</template>
