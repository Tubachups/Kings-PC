<script setup lang="ts">
import { ref } from 'vue';
import ProductCard from "@/components/shop/ProductCard.vue";
import {Delete, Crown} from "lucide-vue-next";
import NavigationMenu from '@/components/ui/navigation-menu/NavigationMenu.vue';
import SearchBar from '@/components/shop/SearchBar.vue';
import BannerCarousel from '@/components/shop/BannerCarousel.vue';
import ProductsCarousel from '@/components/shop/ProductsCarousel.vue';
import Layout from '@/layouts/MainLayout.vue';

defineOptions({ layout: Layout });

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

const searchQuery = ref<string>("");

</script>

<template>
    <div class="container mx-auto p-6">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h1 class="text-3xl font-bold mb-9 md:mb-0">All PC Components</h1>
            <SearchBar v-model="searchQuery"/>
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