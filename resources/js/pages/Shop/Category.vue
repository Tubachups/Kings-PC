<script setup lang="ts">
import ProductCard from './ProductCard.vue';
import NavBar from './NavBar.vue';

const props = defineProps<{
    products: Array<{
        id: number
        name: string
        price: number
        specs: Record<string, string>
        category: {
            name: string
        }
    }>
}>()

</script>

<template>
    <div>
        <NavBar />

        <div class="container mx-auto p-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="border rounded-lg p-4 shadow-sm hover:shadow-md transition"
                >
                    <ProductCard :product="product" />
                </div>
            </div>
        </div>
    </div>
</template>
