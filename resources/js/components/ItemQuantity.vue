<script setup lang="ts">
import { Label } from '@/components/ui/label'
import {
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
} from '@/components/ui/number-field'
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const props = defineProps<{
    productId: number;
}>();
const model = defineModel<number>({default: 1});

const syncWithServer = debounce((newQty: number) => {
    if (!newQty || newQty < 1) return;
    
    router.put(`/cart/${props.productId}`, { 
        quantity: newQty 
    }, {
        preserveScroll: true,
        showProgress: false,
        only: ['cart', 'cart_items'],
        onSuccess: () => {
            toast.success("Cart Updated", {
                description: `Item quantity updated.`,
            });
        }

    });
}, 500);


watch(model, (newVal, oldVal) => {
    if (newVal === oldVal) return; 
    syncWithServer(newVal);
});


</script>

<template>
  <NumberField id="qty" v-model="model" :min="1">
    <Label for="qty"></Label>
    <NumberFieldContent>
      <NumberFieldDecrement />
      <NumberFieldInput readonly/>
      <NumberFieldIncrement />
    </NumberFieldContent>
  </NumberField>
</template>
