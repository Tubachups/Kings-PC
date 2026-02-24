<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import ProductCard from '@/components/shop/ProductCard.vue';
import SearchBar from '@/components/shop/SearchBar.vue';
import Layout from '@/layouts/MainLayout.vue';
import { router } from '@inertiajs/vue3';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
} from '@/components/ui/pagination';
import { Button } from '@/components/ui/button';

defineOptions({ layout: Layout });

const props = defineProps<{
    products: Array<{
        id: number;
        name: string;
        price: number;
        specs: Record<string, string>;
        image_url: string;
        category: {
            name: string;
        };
    }>;
}>();

const PER_PAGE = 15;
const searchQuery = ref<string>('');
const currentPage = ref<number>(1);
const isLoading = ref<boolean>(true);

onMounted(() => {
    router.reload({
        only: ['products'],
        onFinish: () => {
            isLoading.value = false;
        },
    });
});

const filteredProducts = computed(() =>
    props.products.filter((p) =>
        p.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
    ),
);

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / PER_PAGE));

const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * PER_PAGE;
    return filteredProducts.value.slice(start, start + PER_PAGE);
});

// Reset to page 1 when search changes
watch(searchQuery, () => {
    currentPage.value = 1;
});

const handlePageChange = (page: number): void => {
    currentPage.value = page;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
    <div class="container mx-auto p-6">
        <div class="mb-8 flex flex-col items-center justify-between md:flex-row">
            <h1 class="mb-9 text-3xl font-bold md:mb-0">
                {{ products[0]?.category.name.toUpperCase() }}
            </h1>
            <SearchBar v-model="searchQuery" />
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <template v-if="isLoading">
                <div
                    v-for="i in 8"
                    :key="`skeleton-${i}`"
                    class="flex h-full flex-col rounded-lg border p-4 shadow-sm"
                >
                    <ProductCard isLoading />
                </div>
            </template>

            <template v-else-if="paginatedProducts.length > 0">
                <div
                    v-for="product in paginatedProducts"
                    :key="product.id"
                    class="rounded-lg border p-4 shadow-sm transition hover:shadow-md"
                >
                    <ProductCard :product="product" />
                </div>
            </template>
            <div v-else class="col-span-full py-12 text-center text-gray-500">
                No products found.
            </div>
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
