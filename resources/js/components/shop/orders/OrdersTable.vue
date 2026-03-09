<script setup lang="ts">
import dayjs from 'dayjs';
import Card from '@/components/ui/card/Card.vue';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatCurrency } from '@/utils/helpers';

defineProps<{
    orders: Array<any>;
    handleViewDetails: (id: any) => void;
}>();
</script>

<template>
    <Card class="p-5">
        <div class="grid grid-cols-1 gap-4 md:hidden">
            <div
                v-if="orders.length === 0"
                class="rounded-lg border border-dashed bg-muted/30 p-6 text-center"
            >
                <p class="text-base font-semibold">No orders yet</p>
                <p class="mt-2 text-sm text-muted-foreground">
                    Your orders will appear here after you complete checkout.
                </p>
            </div>

            <div
                v-else
                v-for="order in orders"
                :key="order.id"
                class="flex flex-col gap-4 rounded-lg border bg-card p-4 shadow-sm"
            >
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">
                            Order ID
                        </p>
                        <p class="text-sm font-medium">#{{ order.id }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-muted-foreground">
                            Status
                        </p>
                        <p class="text-sm">{{ order.status }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-muted-foreground">
                            Total
                        </p>
                        <p class="text-sm font-medium">
                            {{ formatCurrency(order.total) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-muted-foreground">
                            Date
                        </p>
                        <p class="text-sm">
                            {{
                                dayjs(order.created_at).format(
                                    'MMM D, YYYY h:mm A',
                                )
                            }}
                        </p>
                    </div>
                </div>

                <button
                    @click="handleViewDetails(order.id)"
                    class="mt-1 w-full rounded bg-blue-500 px-4 py-2 text-sm text-white transition-colors hover:bg-blue-600"
                >
                    View Details
                </button>
            </div>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <Table>
                <TableCaption>A list of your recent invoices.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[200px]">Order ID</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Total</TableHead>
                        <TableHead>Order Date</TableHead>
                        <TableHead class="text-right">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="order in orders"
                        :key="order.id"
                        @click="handleViewDetails(order.id)"
                        class="cursor-pointer hover:bg-muted"
                    >
                        <TableCell class="font-medium">
                            #{{ order.id }}
                        </TableCell>
                        <TableCell>{{ order.status }}</TableCell>
                        <TableCell>{{ formatCurrency(order.total) }}</TableCell>
                        <TableCell>{{
                            dayjs(order.created_at).format(
                                'MMMM D, YYYY h:mm A',
                            )
                        }}</TableCell>
                        <TableCell class="text-right">
                            <button
                                @click.stop="handleViewDetails(order.id)"
                                class="rounded bg-blue-500 px-4 py-2 text-sm text-white transition-colors hover:bg-blue-600"
                            >
                                View Details
                            </button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </Card>
</template>
