<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import StickyCart from '@/components/shop/cart/StickyCart.vue';
import Footer from '@/components/shop/layout/Footer.vue';
import NavBar from '@/components/shop/layout/NavBar.vue';
import { Toaster } from '@/components/ui/sonner';
import { useCartStore } from '@/stores/cartStore';
import type { CartItem } from '@/types/cart';

const page = usePage<any>();
const cartStore = useCartStore();

const hiddenStickyCartPaths = ['/checkout', '/payment', '/order/success'];

const shouldShowStickyCart = computed(() => {
    const isAuthenticated = !!page.props.auth?.user;

    if (!isAuthenticated) {
        return false;
    }

    const currentPath = page.url.split('?')[0];

    return !hiddenStickyCartPaths.some((path) => {
        return currentPath === path || currentPath.startsWith(`${path}/`);
    });
});

cartStore.setItems(page.props.cart as CartItem[]);

watch(() => page.props.cart, (newCart) => {
    if (!cartStore.isSyncing) {
        cartStore.setItems(newCart as CartItem[]);
    }
}, { deep: true });

watch(() => page.props.flash, (flash) => {
  if (flash?.success) {
    toast.success(flash.success);
  }

  if (flash?.error) {
    toast.error(flash.error);
  }
}, { deep: true });
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
            <StickyCart v-if="shouldShowStickyCart" />
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
