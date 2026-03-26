<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import PaginationControls from '@/components/PaginationControls.vue';
import ScrollToTop from '@/components/ScrollToTop.vue';
import CategoryFilter from '@/components/shop/products/CategoryFilter.vue';
import PageHeader from '@/components/shop/products/PageHeader.vue';
import ProductCard from '@/components/shop/products/ProductCard.vue';
import { useProductListing } from '@/composables/useProductListing';
import { categories } from '@/constants/constants';
import Layout from '@/layouts/MainLayout.vue';
import type { Product } from '@/types/product';

defineOptions({ layout: Layout });

const props = withDefaults(defineProps<{
    products?: Product[];
}>(), {
    products: () => [],
});

const isLoading = ref<boolean>(true);
const selectedCategories = ref<string[]>([]);
const normalizeCategory = (value: string): string => value.toLowerCase().replace(/[^a-z0-9]/g, '');

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
    extraResetTriggers: [selectedCategories],
    filterPredicate: (product, query) => {
        const matchesSearch = product.name.toLowerCase().includes(query.toLowerCase());

        const productCategory = normalizeCategory(product.category.name);
        const matchesCategory = selectedCategories.value.length === 0
            || selectedCategories.value.some((category) => normalizeCategory(category) === productCategory);

        return matchesSearch && matchesCategory;
    },
});

onMounted(() => {
    router.reload({
        only: ['products'],
        onFinish: () => {
            isLoading.value = false;
        }
    });
});

</script>

<template>

    <Head title="Components">
        <meta head-key="description" name="description"
            content="Browse all PC components at King's PC with filters for search and category." />
    </Head>

    <div class="container mx-auto p-4 xl:p-5">
        <PageHeader v-model:search-query="searchQuery" v-model:view-mode="viewMode" title="All PC components" />

        <div class="flex flex-col md:flex-row gap-6">

            <CategoryFilter :categories="categories" v-model="selectedCategories" />

            <main :class="viewMode === 'grid'
                ? 'flex-1 grid grid-cols-1 gap-6 items-stretch md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5'
                : 'flex-1 flex flex-col gap-4'">

                <template v-if="isLoading">
                    <div v-for="i in 8" :key="`skeleton-${i}`" :class="viewMode === 'grid'
                        ? 'flex h-full flex-col rounded-lg border p-4 shadow-sm'
                        : 'rounded-lg border p-4 shadow-sm'">
                        <ProductCard isLoading :view-mode="viewMode" />
                    </div>
                </template>

                <template v-else>
                    <div v-if="paginatedProducts.length === 0"
                        class="col-span-full py-12 text-center text-muted-foreground">
                        No products found matching your current filters.
                    </div>

                    <div v-for="product in paginatedProducts" :key="product.id" :class="viewMode === 'grid'
                        ? 'flex h-full flex-col rounded-lg border p-4 shadow-sm transition hover:shadow-md'
                        : 'rounded-lg border p-4 shadow-sm transition hover:shadow-md'">
                        <ProductCard :product="product" :is-loading="false" :view-mode="viewMode" />
                    </div>
                </template>

            </main>
        </div>

        <PaginationControls v-if="filteredProducts.length > perPage" :total-items="filteredProducts.length"
            :current-page="currentPage" :items-per-page="perPage" @update:page="handlePageChange" />

    </div>
    <ScrollToTop />
</template>
