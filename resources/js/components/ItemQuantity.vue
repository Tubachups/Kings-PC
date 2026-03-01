<script setup lang="ts">
import {
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
} from '@/components/ui/number-field'
import { Label } from '@/components/ui/label'
import { watch } from 'vue';
import { debounce } from 'lodash';
import { useCart } from '@/composables/useCart';
import { CartItem } from '@/types/cart';

const props = defineProps<{
    item: CartItem;
}>();

const { updateQuantity } = useCart()

const syncWithServer = debounce((newQty: number) => {
    if (!newQty || newQty < 1) return;
    
    updateQuantity(props.item.product, newQty);
}, 500);

watch(() => props.item.quantity, (newVal, oldVal) => {
    if (newVal === oldVal) return; 
    syncWithServer(newVal);
});

</script>

<template>
  <NumberField id="qty" v-model="props.item.quantity" :min="1">
    <Label for="qty"></Label>
    <NumberFieldContent>
      <NumberFieldDecrement />
      <NumberFieldInput readonly/>
      <NumberFieldIncrement />
    </NumberFieldContent>
  </NumberField>
</template>
