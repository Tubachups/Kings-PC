<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
import NavBar from '@/components/shop/NavBar.vue';
import Footer from '@/components/shop/Footer.vue';
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/cartStore';
import { CartItem } from '@/types/cart';

const page = usePage();
const cartStore = useCartStore()

cartStore.setItems(page.props.cart as CartItem[])

watch(() => page.props.cart, (newCart) => {
    cartStore.setItems(newCart as CartItem[])
}, { deep: true })

</script>

<template>
    <main>
        <NavBar />
        <Toaster position="bottom-right" richColors/>
        <slot />
        <Footer />
    </main>
</template>
