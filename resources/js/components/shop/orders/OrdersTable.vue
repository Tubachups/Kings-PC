<script setup lang="ts">
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import dayjs from 'dayjs';
import Card from '@/components/ui/card/Card.vue';
import { formatCurrency } from '@/utils/helpers';

const props = defineProps<{
    orders: Array<any>
    handleViewDetails: (id: any) => void
}>();
</script>

<template>
  <Card class="p-5">

    <div class="grid grid-cols-1 gap-4 md:hidden">
      <div
        v-for="order in orders"
        :key="order.id"
        class="flex flex-col gap-4 p-4 border rounded-lg shadow-sm bg-card"
      >
        <div class="space-y-3">
          <div>
            <p class="text-xs font-medium text-muted-foreground">Order ID</p>
            <p class="text-sm font-medium">#{{ order.id }}</p>
          </div>

          <div>
            <p class="text-xs font-medium text-muted-foreground">Status</p>
            <p class="text-sm">{{ order.status }}</p>
          </div>

          <div>
            <p class="text-xs font-medium text-muted-foreground">Total</p>
            <p class="text-sm font-medium">{{ formatCurrency(order.total) }}</p>
          </div>

          <div>
            <p class="text-xs font-medium text-muted-foreground">Date</p>
            <p class="text-sm">{{ dayjs(order.created_at).format('MMM D, YYYY h:mm A') }}</p>
          </div>
        </div>

        <button
          @click="handleViewDetails(order.id)"
          class="w-full px-4 py-2 mt-1 text-sm text-white transition-colors bg-blue-500 rounded hover:bg-blue-600"
        >
          View Details
        </button>
      </div>
    </div>

    <div class="hidden md:block overflow-x-auto">
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
            <TableCell>{{ dayjs(order.created_at).format('MMMM D, YYYY h:mm A') }}</TableCell>
            <TableCell class="text-right">
              <button
                @click.stop="handleViewDetails(order.id)"
                class="px-4 py-2 text-sm text-white transition-colors bg-blue-500 rounded hover:bg-blue-600"
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
