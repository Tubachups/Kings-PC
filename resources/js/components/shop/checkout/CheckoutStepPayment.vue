<script setup lang="ts">
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
</script>

<template>
  <div class="col-span-4">
    <FormField v-slot="{ componentField }" name="payment_method">
      <FormItem>
        <FormLabel>Payment Method</FormLabel>
        <Select :model-value="componentField.modelValue" @update:model-value="componentField['onUpdate:modelValue']">
          <FormControl><SelectTrigger><SelectValue placeholder="Select Method" /></SelectTrigger></FormControl>
          <SelectContent>
            <SelectItem value="gcash">GCash</SelectItem>
            <SelectItem value="cod">Cash on Delivery</SelectItem>
            <SelectItem value="card">Card</SelectItem>
          </SelectContent>
        </Select>
        <FormMessage />
      </FormItem>
    </FormField>
  </div>
</template>
