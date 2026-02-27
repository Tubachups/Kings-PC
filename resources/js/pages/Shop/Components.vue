<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue';
import ProductCard from "@/components/shop/products/ProductCard.vue";
import { ChevronDown } from "lucide-vue-next";
import SearchBar from '@/components/shop/layout/SearchBar.vue';
import Layout from '@/layouts/MainLayout.vue';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
} from '@/components/ui/pagination';
import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/vue3';

defineOptions({ layout: Layout });

const props = defineProps<{
    products: Array<{
        id: number
        name: string
        description: string
        category_id: number
        price: number
        stock: number
        specs: Record<string, string>
        image_url: string
        is_active: boolean
        category: {
            id: number
            name: string
            slug: string
        }
    }>
}>()

const searchQuery = ref<string>("");

// --- New Filter & Accordion State ---
const isCategoryOpen = ref<boolean>(true);
const selectedCategories = ref<number[]>([]);

// Dynamically extract unique categories from the products array
const uniqueCategories = computed(() => {
    if (!props.products) return [];
    const map = new Map();
    props.products.forEach(p => {
        if (!map.has(p.category.id)) {
            map.set(p.category.id, p.category);
        }
    });
    return Array.from(map.values());
});
// ------------------------------------

const isLoading = ref<boolean>(true);

onMounted(() => {
    router.reload({
        only: ['products'],
        onFinish: () => {
            isLoading.value = false;
        }
    });
});

const PER_PAGE = 16;
const currentPage = ref<number>(1);

// Updated to filter by both search query AND selected categories
const filteredProducts = computed(() => {
    if (!props.products) return [];

    return props.products.filter(p => {
        const matchesSearch = p.name.toLowerCase().includes(searchQuery.value.toLowerCase());

        // If no categories are checked, show all. Otherwise, check if the product's category is in the selected array.
        const matchesCategory = selectedCategories.value.length === 0 || selectedCategories.value.includes(p.category.id);

        return matchesSearch && matchesCategory;
    });
});

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / PER_PAGE));

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
    <div class="container mx-auto p-4 xl:p-6">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl font-bold mb-9 md:mb-0">All PC Components</h1>
            <SearchBar v-model="searchQuery"/>
        </div>

        <div class="flex flex-col md:flex-row gap-6">

            <aside class="w-40 hidden lg:block">
                <div class="border rounded-lg shadow-sm bg-white overflow-hidden">
                    <button
                        @click="isCategoryOpen = !isCategoryOpen"
                        class="w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition-colors"
                    >
                        <span class="font-semibold text-gray-700">Categories</span>
                        <ChevronDown
                            class="w-5 h-5 text-gray-500 transition-transform duration-200"
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
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">
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
                        <ProductCard isLoading />
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
                        <ProductCard :product="product" />
                    </div>
                </template>

            </main>
        </div>

        <div v-if="!isLoading && filteredProducts.length > PER_PAGE" class="mt-8 flex flex-col items-center gap-2">
            <p class="text-muted-foreground text-sm">
                Showing
                <span class="font-medium">{{ (currentPage - 1) * PER_PAGE + 1 }}</span>–<span class="font-medium">{{ Math.min(currentPage * PER_PAGE, filteredProducts.length) }}</span>
                of <span class="font-medium">{{ filteredProducts.length }}</span> results
            </p>
            <Pagination
                :total="filteredProducts.length"
                :items-per-page="PER_PAGE"
                :default-page="currentPage"
                :sibling-count="1"
                show-edges
                @update:page="handlePageChange"
            >
                <PaginationContent v-slot="{ items }">
                    <PaginationItem :value="1" class="mr-3">
                        <PaginationFirst />
                    </PaginationItem>
                    <template v-for="(item, index) in items" :key="index">
                        <PaginationItem v-if="item.type === 'page'" :value="item.value">
                            <Button
                                variant="outline"
                                size="icon"
                                :class="[
                                    'mx-1 rounded-md transition-colors duration-150',
                                    item.value === currentPage
                                        ? 'bg-primary text-primary-foreground hover:bg-primary/90 border-primary'
                                        : 'border border-gray-200 bg-white text-black hover:bg-muted',
                                ]"
                            >
                                {{ item.value }}
                            </Button>
                        </PaginationItem>
                        <PaginationItem v-else :value="0">
                            <PaginationEllipsis />
                        </PaginationItem>
                    </template>
                    <PaginationItem :value="totalPages" class="ml-3">
                        <PaginationLast />
                    </PaginationItem>
                </PaginationContent>
            </Pagination>
        </div>

    </div>
</template>
