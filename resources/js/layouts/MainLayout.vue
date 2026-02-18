<script setup lang="ts">
import { Toaster } from '@/Components/ui/sonner';
import NavBar from '@/components/shop/NavBar.vue';
import Footer from '@/components/shop/Footer.vue';
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
watch(
    () => page.props.flash?.success, // Watch the specific string
    (message) => {
        if (message) {
            toast.success(message);
        }
    },
    { immediate: true } 
);
</script>

<template>
    <main>
        <NavBar />
        <Toaster />
        <slot />
        <Footer />
    </main>
</template>
