<script setup lang="ts">
import CartItems from '@/components/shop/CartItems.vue';
import Layout from '@/layouts/MainLayout.vue';
import { CartItem } from '@/types/cart';
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';

defineOptions({ layout: Layout });

const props = defineProps<{
    cart_items?: CartItem[];
}>();

onMounted(() => {
    router.reload({ only: ['cart_items'] });
});

const tags = Array.from({ length: 50 }).map(
    (_, i, a) => `v1.2.0-beta.${a.length - i}`,
);
</script>

<template>
    <div class="flex h-screen w-full flex-col overflow-hidden">
        <div class="flex h-16 shrink-0 items-center justify-center">
            <header class="text-5xl font-extrabold">My Cart</header>
        </div>

        <div class="flex h-full flex-1 flex-row">
            <div class="mb-2 h-full flex-2 overflow-y-auto p-6">
                <CartItems :items="cart_items || []" />
            </div>

            <div class="flex-1">
                <div class="mb-2 h-full flex-2 p-6">
                    <Card class="h-full w-full">
                        <CardHeader>
                            <CardTitle class="text-4xl">Cart Total</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ScrollArea class="h-72 w-48 rounded-md border">
                                <div class="p-4">
                                    <h4
                                        class="mb-4 text-sm leading-none font-medium"
                                    >
                                        Tags
                                    </h4>
                                    <template v-for="tag in tags" :key="tag">
                                        <div class="text-sm">
                                            {{ tag }}
                                        </div>
                                        <Separator class="my-2" />
                                    </template>
                                </div>
                            </ScrollArea>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
