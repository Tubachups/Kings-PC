<script setup lang="ts">
import { ref, watch } from 'vue';
import ProductCard from './ProductCard.vue';
import {Delete, Crown} from "lucide-vue-next";
import NavigationMenu from '@/components/ui/navigation-menu/NavigationMenu.vue';
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

const searchQuery = ref("");
const debouncedQuery = ref("")
let timeout: ReturnType<typeof setTimeout>;

const handleClear = () => {
    searchQuery.value = "";
}

watch(searchQuery, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        debouncedQuery.value = val;
    }, 400);
})

</script>

<template>

    <NavBar />

    <div class="container mx-auto p-6">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h1 class="text-3xl font-bold mb-9 md:mb-0">All PC Components</h1>
            <div class="flex w-full md:w-auto items-center gap-2">
                <input
                    type="text"
                    class="p-2 outline-amber-50 border border-amber-50 rounded-2xl w-full md:w-96"
                    placeholder="Search item..."
                    v-model="searchQuery"
                />
                <button class="p-2" v-on:click="handleClear"><Delete /></button>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div
                v-for="product in products.filter(p => p.name.toLowerCase().includes(debouncedQuery.toLowerCase()))"
                :key="product.id"
                class="border rounded-lg p-4 shadow-sm hover:shadow-md transition"
            >
                <ProductCard :product="product" />
            </div>

        </div>

    </div>
</template>
