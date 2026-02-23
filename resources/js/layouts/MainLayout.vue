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
    if (!cartStore.isSyncing) {
        cartStore.setItems(newCart as CartItem[])
    }
}, { deep: true })

</script>

<template>
    <main>
        <NavBar />
        <Toaster position="top-center" richColors/>
        <Transition name="slide" mode="out-in">
            <div :key="$page.component">
                <slot />
            </div>
        </Transition>
        <Footer />
    </main>
</template>

<style>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.2s ease;
}
.slide-enter-from {
    opacity: 0;
    filter: blur(4px);
}
.slide-leave-to {
    opacity: 0;
    filter: blur(4px);
}
</style>
