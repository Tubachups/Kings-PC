<script setup lang="ts">
import { ref } from 'vue';
import ProductCard from './ProductCard.vue';
import {Delete} from "lucide-vue-next";


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

const searchQuery = ref("");

const handleClear = () => {
    searchQuery.value = "";
}

</script>

<template>

    <div class="container mx-auto p-6">

        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">All PC Components</h1>
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    class="p-2 outline-amber-50 border border-amber-50 rounded-2xl"
                    placeholder="Search item..."
                    v-model="searchQuery"
                />
                <button class="p-2" v-on:click="handleClear"><Delete /></button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div
                v-for="product in products.filter(p => p.name.toLowerCase().includes(searchQuery.toLowerCase()))"
                :key="product.id"
                class="border rounded-lg p-4 shadow-sm hover:shadow-md transition"
            >
                <ProductCard :product="product" />
            </div>

        </div>
    </div>
</template>
