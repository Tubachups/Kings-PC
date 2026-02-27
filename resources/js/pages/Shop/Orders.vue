<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3'
import Dialog from '@/components/ui/dialog/Dialog.vue';
import OrderDialogContent from '@/components/shop/orders/OrderDialogContent.vue';
import OrdersTable from '@/components/shop/orders/OrdersTable.vue';
import Layout from '@/layouts/MainLayout.vue';
defineOptions({ layout: Layout });

const props = defineProps<{
    orders: Array<any>
    selectedOrder?: any
}>();

const isModalOpen = ref(false);
const isLoading = ref(false);


const handleViewDetails = (id: any) => {
    isModalOpen.value = true;
    isLoading.value = true;

    router.reload({
        data: {
            orderId: id
        },
        only: ['selectedOrder'],
        onSuccess: () => {
            isLoading.value = false;
        }
    })
};
</script>

<template>
<div class="mx-auto pb-10 max-w-7xl px-4 sm:px-6 lg:px-8">
<h2 class="my-4 text-2xl font-bold">My Orders</h2>
<OrdersTable :orders="orders" :handleViewDetails="handleViewDetails" />
<Dialog v-model:open="isModalOpen">
    <OrderDialogContent :selectedOrder="selectedOrder" :isLoading="isLoading" />
</Dialog>
</div>
</template>
