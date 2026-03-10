<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import CheckoutForm from '@/components/shop/checkout/CheckoutForm.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Layout from '@/layouts/MainLayout.vue';
defineOptions({ layout: Layout });
</script>

<template>
    <Head title="Checkout">
        <meta head-key="description" name="description" content="Complete your order securely with King's PC checkout and payment details." />
    </Head>

    <div class="flex w-full justify-center p-6 md:px-12">
        <Card >
            <CardContent>
                <CheckoutForm />
            </CardContent>
        </Card>
    </div>
</template>
