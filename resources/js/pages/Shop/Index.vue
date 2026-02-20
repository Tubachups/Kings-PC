<script setup lang="ts">
import { ref } from 'vue';
import ProductCard from "@/components/shop/ProductCard.vue";
import {Delete, Crown} from "lucide-vue-next";
import NavigationMenu from '@/components/ui/navigation-menu/NavigationMenu.vue';
import SearchBar from '@/components/shop/SearchBar.vue';
import BannerCarousel from '@/components/shop/BannerCarousel.vue';
import ProductsCarousel from '@/components/shop/ProductsCarousel.vue';
import ReviewsCarousel from '@/components/shop/ReviewsCarousel.vue';
import ComponentsCarousel from '@/components/shop/ComponentsCarousel.vue';
import Layout from '@/layouts/MainLayout.vue';
import { Link } from '@inertiajs/vue3'

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
            <p class="text-xl md:text-3xl font-bold underline">Components</p>
            <Link href="/components" class="text-sm md:text-lg">See more...</Link>
        </div>

        <ComponentsCarousel />
    </div>

    <div class="container mx-auto p-6 w-full flex justify-center flex-col items-center">
        <div class="text-2xl md:text-3xl font-bold underline">Reviews</div>
        <div class="text-sm sm:text-lg mb-4">From our satisfied customers...</div>
        <ReviewsCarousel class="basis-1/4"/>
    </div>

</template>
