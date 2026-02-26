<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { toTypedSchema } from '@vee-validate/zod'
import { Check, Circle, Dot } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import * as z from 'zod'
import { usePsgc } from '@/composables/usePsgc' 
import { Button } from '@/components/ui/button'
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Stepper, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper'
import { router } from "@inertiajs/vue3";
import { useCart } from '@/composables/useCart'
import TotalCard from './TotalCard.vue'

const props = defineProps<{
    shippingFee?: number
}>()

const {
  regions,
  provinces,
  cities,
  barangays,
  selectedRegionCode,
  selectedProvinceCode,
  selectedCityCode,
  selectedBarangayCode,
  isRegionWithoutProvinces,
  onRegionChange,
  onProvinceChange,
  onCityChange,
  onBarangayChange,
} = usePsgc((field, value) => {
  formRef.value?.setFieldValue(field, value);
});

const checkoutShippingFee = computed(() => props.shippingFee ?? 150)
const stepIndex = ref(1)
const { items, clearCart } = useCart();
const formRef = ref<any>(null);
const isSubmitting = ref(false);


const steps = [
  { step: 1, title: 'Shipping', description: 'Address info' },
  { step: 2, title: 'Review', description: 'Check items' },
  { step: 3, title: 'Payment', description: 'Pay method' },
]

const step1Schema = z.object({
  full_name: z.string().min(2, "Name is required"),
  address: z.string().min(5, "Address is required"),
  region: z.string().min(1, "Region is required"),
  province: z.string().optional().nullable().or(z.literal('')),
  city: z.string().min(1, "City is required"),
  barangay: z.string().min(1, "Barangay is required"),
})

const step2Schema = z.object({}) 

const step3Schema = z.object({
  payment_method: z.enum(['gcash', 'cod', 'card'], {
    required_error: "Please select a payment method",
  }),
})

const currentSchema = computed(() => {
  if (stepIndex.value === 1) return toTypedSchema(step1Schema)
  if (stepIndex.value === 2) return toTypedSchema(step1Schema.merge(step2Schema))
  return toTypedSchema(step1Schema.merge(step2Schema).merge(step3Schema))
})


function onSubmit(values: any) {
  isSubmitting.value = true;
  const loadingToastId = toast.loading('Processing your order...');

  router.post('/checkout/confirm', values, {
    onFinish: () => {
      toast.dismiss(loadingToastId);
      isSubmitting.value = false;
    },
    onSuccess: () => {
      toast.dismiss(loadingToastId);
      clearCart();
      toast.success('Order placed successfully!');
      isSubmitting.value = false;
    },
    onError: (errors) => {
      toast.dismiss(loadingToastId);
      toast.error('Order failed. Please check your details.');
      console.log('Server Validation Errors:', errors);
      isSubmitting.value = false;
    }
  });
}

</script>

<template>
  <div v-if="Object.keys(formRef?.errors || {}).length > 0" class="mt-4 p-4 bg-red-50 border border-red-200 rounded text-red-600 mb-4">
    <p class="font-bold">Please correct the following:</p>
    <ul class="list-disc ml-5">
      <li v-for="(error, field) in formRef?.errors" :key="field">{{ field }}: {{ error }}</li>
    </ul>
  </div>

  <Form
    ref="formRef"
    v-slot="{ values, validate }"
    as=""
    keep-values
    :validation-schema="currentSchema"
  >
    <Stepper v-model="stepIndex" class="block w-full">
      <form @submit.prevent="async () => {
          const result = await validate();
          if (stepIndex === steps.length && result.valid) {
            onSubmit(values);
          }
        }">
        
        <div class="flex w-full flex-start gap-2 mb-8">
          <StepperItem v-for="(step, index) in steps" :key="step.step" v-slot="{ state }" class="relative flex w-full flex-col items-center" :step="step.step">
            <StepperSeparator v-if="index !== steps.length - 1" class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 bg-muted group-data-[state=completed]:bg-primary" />
            <StepperTrigger as-child>
              <Button :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'" size="icon" class="z-10 rounded-full">
                <Check v-if="state === 'completed'" class="size-5" />
                <Circle v-if="state === 'active'" />
                <Dot v-if="state === 'inactive'" />
              </Button>
            </StepperTrigger>
            <div class="mt-2 text-center">
              <StepperTitle class="text-sm font-bold">{{ step.title }}</StepperTitle>
            </div>
          </StepperItem>
        </div>

        <div class="grid grid-cols-4 gap-4">
          <template v-if="stepIndex === 1">
            <div class="col-span-4">
              <FormField v-slot="{ componentField }" name="full_name">
                <FormItem><FormLabel>Full Name</FormLabel><FormControl><Input v-bind="componentField" /></FormControl><FormMessage /></FormItem>
              </FormField>
            </div>
            
            <div class="col-span-2">
              <FormField name="region">
                <FormItem>
                  <FormLabel>Region</FormLabel>
                  <Select :model-value="selectedRegionCode" @update:model-value="(val) => { if (typeof val === 'string') onRegionChange(val) }">
                    <FormControl><SelectTrigger><SelectValue placeholder="Select Region" /></SelectTrigger></FormControl>
                    <SelectContent>
                      <SelectItem v-for="r in regions" :key="r.code" :value="r.code">{{ r.name }}</SelectItem>
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              </FormField>
            </div>

            <div class="col-span-2">
              <FormField name="province">
                <FormItem>
                  <FormLabel>Province</FormLabel>
                  <Select :model-value="selectedProvinceCode" :disabled="isRegionWithoutProvinces || !provinces.length" @update:model-value="(val) => { if (typeof val === 'string') onProvinceChange(val) }">
                    <FormControl><SelectTrigger><SelectValue :placeholder="isRegionWithoutProvinces ? 'N/A' : 'Select Province'" /></SelectTrigger></FormControl>
                    <SelectContent>
                      <SelectItem v-for="p in provinces" :key="p.code" :value="p.code">{{ p.name }}</SelectItem>
                    </SelectContent>
                  </Select>
                </FormItem>
              </FormField>
            </div>

            <div class="col-span-2">
              <FormField name="city">
                <FormItem>
                  <FormLabel>City</FormLabel>
                  <Select :model-value="selectedCityCode" :disabled="!cities.length" @update:model-value="(val) => { if (typeof val === 'string') onCityChange(val) }">
                    <FormControl><SelectTrigger><SelectValue placeholder="Select City" /></SelectTrigger></FormControl>
                    <SelectContent>
                      <SelectItem v-for="c in cities" :key="c.code" :value="c.code">{{ c.name }}</SelectItem>
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              </FormField>
            </div>

            <div class="col-span-2">
              <FormField name="barangay">
                <FormItem>
                  <FormLabel>Barangay</FormLabel>
                  <Select :model-value="selectedBarangayCode" :disabled="!barangays.length" @update:model-value="(val) => { if (typeof val === 'string') onBarangayChange(val) }">
                    <FormControl><SelectTrigger><SelectValue placeholder="Select Barangay" /></SelectTrigger></FormControl>
                    <SelectContent>
                      <SelectItem v-for="b in barangays" :key="b.code" :value="b.code">{{ b.name }}</SelectItem>
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              </FormField>
            </div>
            
            <div class="col-span-4">
                <FormField v-slot="{ componentField }" name="address">
                    <FormItem><FormLabel>Street Address</FormLabel><FormControl><Input placeholder="House No / Street / Landmark" v-bind="componentField" /></FormControl><FormMessage /></FormItem>
                </FormField>
            </div>
          </template>

          <template v-if="stepIndex === 2">
            <div class="col-span-4">
              <TotalCard :items="items" :shipping-fee="checkoutShippingFee" :is-checkout="true"/>
            </div>
          </template>

          <template v-if="stepIndex === 3">
            <div class="col-span-4">
              <FormField v-slot="{ componentField }" name="payment_method">
                <FormItem>
                  <FormLabel>Payment Method</FormLabel>
                  <Select
                    :model-value="componentField.modelValue"
                    @update:model-value="componentField['onUpdate:modelValue']"
                  >
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
        </div>

        <div class="flex justify-between mt-10">
          <Button type="button" variant="outline" :disabled="stepIndex === 1" @click="stepIndex--">Back</Button>
          
          <Button v-if="stepIndex < 3" type="button" @click="async () => { const res = await validate(); if (res.valid) stepIndex++ }">
            Next
          </Button>
          
          <Button v-else :disabled="isSubmitting" type="submit">
            {{ isSubmitting ? 'Placing Order...' : 'Place Order' }}
          </Button>
        </div>
      </form>
    </Stepper>
  </Form>
</template>
