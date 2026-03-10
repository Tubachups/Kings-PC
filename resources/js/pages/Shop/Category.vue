<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import PaginationControls from '@/components/PaginationControls.vue';
import SearchBar from '@/components/shop/layout/SearchBar.vue';
import ProductCard from '@/components/shop/products/ProductCard.vue';
import Layout from '@/layouts/MainLayout.vue';
import type { Product } from '@/types/product';


defineOptions({ layout: Layout });

const props = defineProps<{
    products: Product[];
}>();

const PER_PAGE = 16;
const currentPage = ref<number>(1);
const searchQuery = ref<string>('');

const filteredProducts = computed(() =>
    props.products.filter((p) =>
        p.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
    ),
);
const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * PER_PAGE;
    return filteredProducts.value.slice(start, start + PER_PAGE);
});

// Reset to page 1 when search changes
watch(searchQuery, () => {
    currentPage.value = 1;
});


</script>

<template>
    <Head title="Category Products">
        <meta head-key="description" name="description" content="Browse King's PC products by category and quickly find the right parts." />
    </Head>

    <div class="container mx-auto p-6">
        <div class="mb-8 flex flex-col items-center justify-between md:flex-row">
            <h1 class="mb-9 text-3xl font-bold md:mb-0">
                {{ products[0]?.category.name.toUpperCase() }}
            </h1>
            <SearchBar v-model="searchQuery" />
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <template v-if="paginatedProducts.length > 0">
                <div v-for="product in paginatedProducts" :key="product.id"
                    class="rounded-lg border p-4 shadow-sm transition hover:shadow-md">
                    <ProductCard :product="product" :is-loading="false" />
                </div>
            </template>
            <div v-else class="col-span-full py-12 text-center text-gray-500">
                No products found.
            </div>
        </div>

        <PaginationControls
            :total-items="filteredProducts.length"
            :current-page="currentPage"
            :items-per-page="PER_PAGE"
            @update:page="(page) => (currentPage = page)"
        />
    </div>
</template>
