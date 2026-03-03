<script setup lang="ts">
import CheckoutForm from '@/components/shop/checkout/CheckoutForm.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Layout from '@/layouts/MainLayout.vue';
defineOptions({ layout: Layout });
</script>

<template>
    <div class="flex w-full justify-center p-6 md:px-12">
        <Card >
            <CardContent>
                <CheckoutForm />
            </CardContent>
        </Card>
    </div>
</template>
