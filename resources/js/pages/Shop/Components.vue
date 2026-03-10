<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ChevronDown } from "lucide-vue-next";
import { onMounted, ref, computed, watch } from 'vue';
import PaginationControls from '@/components/PaginationControls.vue';
import SearchBar from '@/components/shop/layout/SearchBar.vue';
import ProductCard from "@/components/shop/products/ProductCard.vue";

import Layout from '@/layouts/MainLayout.vue';
import type { Product } from '@/types/product';

defineOptions({ layout: Layout });

const props = withDefaults(defineProps<{
    products?: Product[];
}>(), {
    products: () => [],
});

const PER_PAGE = 16;
const currentPage = ref<number>(1);
const searchQuery = ref<string>("");
const isLoading = ref<boolean>(true);

// --- New Filter & Accordion State ---
const isCategoryOpen = ref<boolean>(true);
const selectedCategories = ref<number[]>([]);

// Dynamically extract unique categories from the products array
const uniqueCategories = computed(() => {
    const map = new Map();
    props.products.forEach(p => {
        if (!map.has(p.category.id)) {
            map.set(p.category.id, p.category);
        }
    });
    return Array.from(map.values());
});
// ------------------------------------


onMounted(() => {
    router.reload({
        only: ['products'],
        onFinish: () => {
            isLoading.value = false;
        }
    });
});


// Updated to filter by both search query AND selected categories
const filteredProducts = computed(() => {
    return props.products.filter(p => {
        const matchesSearch = p.name.toLowerCase().includes(searchQuery.value.toLowerCase());

        // If no categories are checked, show all. Otherwise, check if the product's category is in the selected array.
        const matchesCategory = selectedCategories.value.length === 0 || selectedCategories.value.includes(p.category.id);

        return matchesSearch && matchesCategory;
    });
});

const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * PER_PAGE;
    return filteredProducts.value.slice(start, start + PER_PAGE);
});

// Reset to page 1 when filters change
watch([searchQuery, selectedCategories], () => {
    currentPage.value = 1;
});

const handlePageChange = (page: number): void => {
    currentPage.value = page;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
    <Head title="Components">
        <meta head-key="description" name="description" content="Browse all PC components at King's PC with filters for search and category." />
    </Head>

    <div class="container mx-auto p-4 xl:p-6">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl font-bold mb-9 md:mb-0">All PC Components</h1>
            <SearchBar v-model="searchQuery"/>
        </div>

        <div class="flex flex-col md:flex-row gap-6">

            <aside class="w-40 hidden lg:block">
                <div class="border rounded-lg shadow-sm bg-white dark:bg-neutral-950 overflow-hidden">
                    <button
                        @click="isCategoryOpen = !isCategoryOpen"
                        class="w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition-colors dark:bg-neutral-900 dark:hover:bg-neutral-800"
                    >
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Categories</span>
                        <ChevronDown
                            class="w-5 h-5 text-gray-500  transition-transform duration-200"
                            :class="{ 'rotate-180': isCategoryOpen }"
                        />
                    </button>

                    <div v-show="isCategoryOpen" class="p-4 border-t space-y-3">
                        <label
                            v-for="category in uniqueCategories"
                            :key="category.id"
                            class="flex items-center space-x-3 cursor-pointer group"
                        >
                            <input
                                type="checkbox"
                                :value="category.id"
                                v-model="selectedCategories"
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-neutral-950 dark:text-blue-500"
                            />
                            <span class="text-sm text-gray-600 dark:text-gray-300 group-hover:text-gray-900 transition-colors">
                                {{ category.name }}
                            </span>
                        </label>
                    </div>
                </div>
            </aside>

            <main class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 items-stretch ">

                <template v-if="isLoading">
                    <div
                        v-for="i in 8"
                        :key="`skeleton-${i}`"
                        class="border rounded-lg p-4 shadow-sm h-full flex flex-col"
                    >
                        <ProductCard  isLoading />
                    </div>
                </template>

                <template v-else>
                    <div v-if="paginatedProducts.length === 0" class="col-span-full text-center py-12 text-gray-500">
                        No products found matching your current filters.
                    </div>

                    <div
                        v-for="product in paginatedProducts"
                        :key="product.id"
                        class="border rounded-lg p-4 shadow-sm hover:shadow-md transition h-full flex flex-col"
                    >
                        <ProductCard :product="product" :is-loading="false" />
                    </div>
                </template>

            </main>
        </div>

        <PaginationControls
            v-if="filteredProducts.length > PER_PAGE"
            :total-items="filteredProducts.length"
            :current-page="currentPage"
            :items-per-page="PER_PAGE"
            @update:page="handlePageChange"
        />

    </div>
</template>
