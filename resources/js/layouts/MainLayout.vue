<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
import NavBar from '@/components/shop/NavBar.vue';
import Footer from '@/components/shop/Footer.vue';
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

</script>

<template>
    <main>
        <NavBar />
        <Toaster position="bottom-right" richColors/>
        <slot />
        <Footer />
    </main>
</template>
