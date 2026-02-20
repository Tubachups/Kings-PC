<script setup lang="ts">
import CartItems from '@/components/shop/CartItems.vue';
import Layout from '@/layouts/MainLayout.vue';
import { CartItem } from '@/types/cart';
import { computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';

defineOptions({ layout: Layout });

const page = usePage();

const cart_items = computed(() => page.props.cart || [])

</script>

<template>
    <div class="flex flex-col h-screen w-full overflow-hidden">

        <div class="flex h-16 shrink-0 justify-center items-center">
            <header class="text-5xl font-extrabold">My Cart</header>
        </div>

        <div class="flex flex-row flex-1 h-full">
            <div class="flex-2 h-full overflow-y-auto p-6 mb-2">
                <CartItems :items="cart_items || []"/>
            </div>

            <div class="flex-1 h-full overflow-y-auto p-6 mb-2">
                <div class="h-full w-full">
                    <Card class="w-full h-full">
                        <CardHeader>
                            <CardTitle class="text-4xl">Cart Total</CardTitle>
                        </CardHeader>
                        <CardContent> 
                            <ScrollArea>

                            </ScrollArea>
                        </CardContent>
                    </Card>
                </div>

            </div>
        </div>

    </div>
</template>
