<script setup lang="ts">
import { Label } from '@/components/ui/label'
import {
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
} from '@/components/ui/number-field'

interface Props {
    defaultValue: number
}

const props = defineProps<Props>();

</script>

<template>
  <NumberField id="qty" :default-value="props.defaultValue" :min="0">
    <Label for="qty"></Label>
    <NumberFieldContent>
      <NumberFieldDecrement />
      <NumberFieldInput />
      <NumberFieldIncrement />
    </NumberFieldContent>
  </NumberField>
</template>
