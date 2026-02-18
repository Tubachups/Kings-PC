<script setup lang="ts">
import NavBar from '@/components/shop/NavBar.vue';
import Footer from '@/components/shop/Footer.vue';
</script>

<template>
    <main>
        <NavBar />
        <slot />
        <Footer />
    </main>
</template>
