<script setup lang="ts">
import { ref } from 'vue';
import ProductCard from '@/components/shop/ProductCard.vue';
import SearchBar from '@/components/shop/SearchBar.vue';
import Layout from '@/layouts/MainLayout.vue';

defineOptions({ layout: Layout });

const props = defineProps<{
    products: Array<{
        id: number;
        name: string;
        price: number;
        specs: Record<string, string>;
        category: {
            name: string;
        };
    }>;
}>();

const searchQuery = ref<string>('');
</script>

<template>
        <div class="container mx-auto p-6">
            <div
                class="mb-8 flex flex-col items-center justify-between md:flex-row"
            >
                <h1 class="mb-9 text-3xl font-bold md:mb-0">
                    {{ products[0]?.category.name.toUpperCase() }}
                </h1>
                <SearchBar v-model="searchQuery" />
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="product in products.filter((p) =>
                        p.name
                            .toLowerCase()
                            .includes(searchQuery.toLowerCase()),
                    )"
                    :key="product.id"
                    class="rounded-lg border p-4 shadow-sm transition hover:shadow-md"
                >
                    <ProductCard :product="product" />
                </div>
            </div>
        </div>
    
</template>
