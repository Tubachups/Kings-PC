<script setup lang="ts">
import { ref } from 'vue';
import ProductCard from "@/components/shop/ProductCard.vue";
import {Delete, Crown} from "lucide-vue-next";
import NavigationMenu from '@/components/ui/navigation-menu/NavigationMenu.vue';
import SearchBar from '@/components/shop/SearchBar.vue';
import BannerCarousel from '@/components/shop/BannerCarousel.vue';
import ProductsCarousel from '@/components/shop/ProductsCarousel.vue';
import ReviewsCarousel from '@/components/shop/ReviewsCarousel.vue';
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
    
    <div class="container mx-auto p-6 w-full flex justify-center">
        <BannerCarousel />
    </div>

    <div class="container mx-auto p-6 w-full flex flex-col justify-center">
        <div class="flex flex-row justify-between items-baseline mb-2">
            <div class="text-3xl font-bold underline">Components</div>
            <Link href="" class="text-md">See more...</Link>
        </div>

        <ComponentsCarousel class="basis-1/6"/>
    </div>

    <div class="bg-black mx-auto p-6 w-full flex flex-row justify-center gap-6">
        <div class="bg-amber-100 container w-1/2 rounded-2xl mx-6 min-w-0 flex flex-col">
            <div class="py-4 px-8 flex flex-row justify-between items-baseline">
                <span class="text-2xl font-bold">DESKTOPS</span>
                <span class="text-1xl">See More</span>
            </div>
            <div class="mx-4 mb-2 flex justify-center items-center">
                <ProductsCarousel class="basis-1/2"/>    
            </div>
        </div>
        <div class="bg-amber-100 container w-1/2 rounded-2xl mx-6 min-w-0 flex flex-col">
            <div class="py-4 px-8 flex flex-row justify-between items-baseline">
                <span class="text-2xl font-bold">LAPTOPS</span>
                <span class="text-1xl">See More</span>
            </div>
            <div class="mx-4 mb-2 flex justify-center items-center">
                <ProductsCarousel class="basis-1/2"/>    
            </div>
        </div>
    </div>

    <div class="container mx-auto p-6 w-full flex justify-center flex-col items-center">
        <div class="text-3xl font-bold underline">REVIEWS</div>
        <div class="text-md mb-4">From our satisfied customers...</div>
        <ReviewsCarousel class="basis-1/4"/>
    </div>

</template>
