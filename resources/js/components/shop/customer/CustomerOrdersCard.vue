<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { CalendarDays } from 'lucide-vue-next';
import { computed } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { CustomerPageProps, DashboardOrder } from './types';

const page = usePage<CustomerPageProps>();

const customerData = computed(() => page.props.customerData ?? {});
const customerSummary = computed(() => page.props.customerSummary ?? {});
const customerOrders = computed<DashboardOrder[]>(() => customerData.value.orders ?? []);

const formatPrice = (value: number): string => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2,
    }).format(value);
};

const formatDate = (value: string): string => {
    return new Date(value).toLocaleDateString('en-PH', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const statusPillClass = (status: string): string => {
    const normalizedStatus = status.toLowerCase();

    if (normalizedStatus === 'delivered') {
        return 'bg-green-100 text-green-700';
    }

    if (normalizedStatus === 'shipped' || normalizedStatus === 'processing') {
        return 'bg-blue-100 text-blue-700';
    }

    return 'bg-muted text-muted-foreground';
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-base">
                <CalendarDays class="h-4 w-4" />
                Orders
            </CardTitle>
            <CardDescription>
                {{ customerSummary.ordersCount ?? 0 }} orders placed
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-3">
            <div v-if="customerOrders.length === 0" class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground">
                No orders yet.
            </div>
            <div v-for="order in customerOrders" :key="`dashboard-order-${order.id}`" class="rounded-lg border p-3 text-sm">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <p class="font-semibold">Order #{{ order.id }}</p>
                    <span class="rounded-full px-2 py-0.5 text-[11px] font-medium" :class="statusPillClass(order.status)">
                        {{ order.status }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs text-muted-foreground">
                    <span>{{ formatDate(order.created_at) }}</span>
                    <span>{{ formatPrice(order.total) }}</span>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
