<script setup lang="ts">
import CartItems from '@/components/shop/CartItems.vue';
import { useCart } from '@/composables/useCart';
import TotalCard from '@/components/shop/TotalCard.vue';
import Layout from '@/layouts/MainLayout.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CheckoutForm from '@/components/shop/CheckoutForm.vue';
defineOptions({ layout: Layout });

const { items, subTotal } = useCart();
</script>

<template>
    <div class="flex flex-col h-screen w-full overflow-hidden justify-center align-middle p-6">
        <Card>
            <CardContent>
                <CheckoutForm />
            </CardContent>
        </Card>
    </div>
</template>