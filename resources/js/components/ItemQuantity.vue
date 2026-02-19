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
    defaultValue: number;
    productId: number;
}>();

const localQty = ref(props.defaultValue);

const syncWithServer = debounce((newQty: number) => {
    router.put(`/cart/${props.productId}`, { 
        quantity: newQty 
    }, {
        preserveScroll: true,
        showProgress: false,
        preserveState: true,
        only: ['cart', 'cart_items'],
    });
}, 500);


watch(localQty, (newVal) => {
    syncWithServer(newVal);
});


watch(() => props.defaultValue, (newVal) => {
    localQty.value = newVal;
});
</script>

<template>
  <NumberField id="qty" v-model="localQty" :min="1">
    <Label for="qty"></Label>
    <NumberFieldContent>
      <NumberFieldDecrement />
      <NumberFieldInput readonly/>
      <NumberFieldIncrement />
    </NumberFieldContent>
  </NumberField>
</template>
