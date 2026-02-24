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

const props = defineProps<{
    orders: Array<any>
    handleViewDetails: (id: any) => void
}>();
</script>

<template>
  <Card class="p-6">
    <Table>
      <TableCaption>A list of your recent invoices.</TableCaption>
      <TableHeader>
        <TableRow>
          <TableHead class="w-[200px]">
            Order ID
          </TableHead>
          <TableHead>Status</TableHead>
          <TableHead>Total</TableHead>
          <TableHead>Order Date</TableHead>
          <TableHead class="text-right">
            Action
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="order in orders" :key="order.id" @click="handleViewDetails(order.id)" class="cursor-pointer hover:bg-muted">
          <TableCell class="font-medium">
            {{ order.id }}
          </TableCell>
          <TableCell>{{ order.status }}</TableCell>
          <TableCell>{{ order.total }}</TableCell>
          <TableCell>{{ dayjs(order.created_at).format('MMMM D, YYYY h:mm A') }}</TableCell>
          <TableCell class="text-right">
              <button @click="handleViewDetails(order.id)" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                  View Details
              </button>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Card>
</template>