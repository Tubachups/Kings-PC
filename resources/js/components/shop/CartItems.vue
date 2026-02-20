<script setup lang="ts">
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import ItemQuantity from '../ItemQuantity.vue';
import { computed, ref } from 'vue';
import { CartItem } from '@/types/cart';
import { Trash2Icon } from 'lucide-vue-next';
import { router, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { useCart } from '@/composables/useCart';

defineProps<{
    items: CartItem[]
}>();

const deleteCartItem = useCart()
</script>

<template>
    <ScrollArea class="h-full w-full rounded-md border">
        <div class="p-4">
            <h4 class="mb-4 flex flex-row text-sm leading-none font-medium">
                <div class="flex flex-1 justify-center">total: </div>
                <div class="flex flex-1 justify-center p-6">Item</div>
                <div class="flex flex-1 justify-center p-6">Quantity</div>
                <div class="flex flex-1 justify-center p-6">Price</div>
            </h4>
            <template v-for="item in items" key="id">
                <div class="flex flex-1 flex-row">
                    <div class="flex-1">
                        <img
                            :src="item.product.image_url"
                            :alt="item.product.name"
                            class="my-2 aspect-square w-full rounded-lg object-cover"
                        />
                    </div>
                    <div class="flex flex-3 flex-col">
                        <div class="flex flex-row flex-3">
                            <div class="flex-1 p-6">
                                <p class="text-2xl font-bold">
                                    {{ item.product.name}}
                                </p>
                            </div>
                            <div class="flex flex-1 justify-center p-6">
                                <p class="flex flex-col">
                                    <div class="flex-3">
                                        <ItemQuantity
                                            v-model="item.quantity"
                                            :productId="item.product.id"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <button @click="deleteCartItem(item)" class="bg-red-500 h-full w-full flex justify-center items-center rounded"><Trash2Icon class="stroke-white" /></button>
                                    </div>
                                </p>
                            </div>
                            <div class="flex flex-1 justify-center p-6">
                                <p class="text-1xl font-bold">
                                     ₱{{ item.product.price}}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-row justify-end-safe items-baseline flex-1 mr-20 p-6 text-2xl font-bold">
                            Total: ₱{{ (item.quantity * item.product.price).toLocaleString() }}
                        </div>
                    </div>
                </div>
                <Separator class="my-2" />
            </template>
        </div>
    </ScrollArea>
</template>
