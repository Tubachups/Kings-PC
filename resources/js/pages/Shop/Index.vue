<script setup lang="ts">
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


</script>

<template>

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-8">All PC Components</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div
                v-for="product in products"
                :key="product.id"
                class="border rounded-lg p-4 shadow-sm hover:shadow-md transition"
            >
                <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">
                    {{ product.category.name }}
                </span>

                <h2 class="text-lg font-bold mt-2 mb-2 leading-tight">
                    {{ product.name }}
                </h2>

                <p class="text-xl text-green-600 font-extrabold mb-4">
                    ₱{{ product.price }}
                </p>

                <div class="bg-gray-50 rounded p-3 text-sm mb-4">
                    <h3 class="font-bold border-b pb-1 mb-2 text-gray-700">Specifications</h3>
                    <ul>
                        <li v-for="(value, key) in product.specs" :key="key" class="flex justify-between py-1">
                            <span class="capitalize text-gray-600">{{ key.replace('_', ' ') }}:</span>
                            <span class="font-medium text-gray-900">{{ value }}</span>
                        </li>
                    </ul>
                </div>

                <button class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700">
                    Add to Cart
                </button>
            </div>

        </div>
    </div>
</template>
