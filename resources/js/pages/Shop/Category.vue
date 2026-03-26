<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import PaginationControls from '@/components/PaginationControls.vue';
import ScrollToTop from '@/components/ScrollToTop.vue';
import PageHeader from '@/components/shop/products/PageHeader.vue';
import ProductCard from '@/components/shop/products/ProductCard.vue';
import { useProductListing } from '@/composables/useProductListing';
import Layout from '@/layouts/MainLayout.vue';
import type { Product } from '@/types/product';

defineOptions({ layout: Layout });

const props = defineProps<{
    products: Product[];
}>();

const {
    perPage,
    currentPage,
    searchQuery,
    viewMode,
    filteredProducts,
    paginatedProducts,
    handlePageChange,
} = useProductListing({
    products: computed(() => props.products),
    perPage: 16,
});

</script>

<template>

    <Head title="Category Products">
        <meta head-key="description" name="description"
            content="Browse King's PC products by category and quickly find the right parts." />
    </Head>

    <div class="container mx-auto p-6">

        <PageHeader v-model:search-query="searchQuery" v-model:view-mode="viewMode" :title="products[0]?.category.name" />

        <div :class="viewMode === 'grid'
            ? 'grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4'
            : 'flex flex-col gap-4'">
            <template v-if="paginatedProducts.length > 0">
                <div v-for="product in paginatedProducts" :key="product.id" :class="viewMode === 'grid'
                    ? 'rounded-lg border p-4 shadow-sm transition hover:shadow-md'
                    : 'rounded-lg border p-4 shadow-sm transition hover:shadow-md'">
                    <ProductCard :product="product" :is-loading="false" :view-mode="viewMode" />
                </div>
            </template>
            <div v-else class="col-span-full py-12 text-center text-muted-foreground">
                No products found.
            </div>
        </div>

        <PaginationControls :total-items="filteredProducts.length" :current-page="currentPage" :items-per-page="perPage"
            @update:page="handlePageChange" />
    </div>
    <ScrollToTop />
</template>
