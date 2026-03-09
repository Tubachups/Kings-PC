<script setup lang="ts">
import { router } from "@inertiajs/vue3"
import { toTypedSchema } from '@vee-validate/zod'
import { Check, Circle, Dot } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import { toast } from 'vue-sonner'
import * as z from 'zod'
import { Button } from '@/components/ui/button'
import { Form } from '@/components/ui/form'
import { Stepper, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper'
import { useCart } from '@/composables/useCart'
import { steps } from '@/constants/constants'

import CheckoutStepPayment from './CheckoutStepPayment.vue'
import CheckoutStepReview from './CheckoutStepReview.vue'
import CheckoutStepShipping from './CheckoutStepShipping.vue'

const props = defineProps<{ shippingFee?: number }>()
const checkoutShippingFee = computed(() => props.shippingFee ?? 150)
const couponDiscount = ref(0)

const stepIndex = ref(1)
const isSubmitting = ref(false)
const formRef = ref<any>(null)
const { clearCart } = useCart()



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
        clearCart();
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
  <Form ref="formRef" v-slot="{ values, validate }" as="" keep-values :validation-schema="currentSchema">
    <Stepper v-model="stepIndex" class="block w-full">
      <form @submit.prevent="async () => {
          const result = await validate();
          if (stepIndex === steps.length && result.valid) onSubmit(values);
      }">
        <div class="mb-8 flex w-full flex-start gap-2 ">
          <StepperItem
            v-for="(step, index) in steps"
            :key="step.step"
            v-slot="{ state }"
            class="relative flex w-full flex-col items-center"
            :step="step.step"
          >
            <StepperSeparator
              v-if="index !== steps.length - 1"
              class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 bg-muted group-data-[state=completed]:bg-primary"
            />
            <StepperTrigger as-child>
              <Button
                :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                size="icon"
                type="button"
                tabindex="-1"
                aria-disabled="true"
                class="z-10 rounded-full pointer-events-none"
              >
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

        <div class="grid grid-cols-4 gap-4 mt-8">
          <CheckoutStepShipping v-if="stepIndex === 1" />

          <CheckoutStepReview
            v-if="stepIndex === 2"
            :shipping-fee="checkoutShippingFee"
            :coupon-discount="couponDiscount"
          />

          <CheckoutStepPayment v-if="stepIndex === 3" />
        </div>

        <div class="flex justify-between mt-10">
          <Button type="button" variant="outline" :disabled="stepIndex === 1" @click="stepIndex--">Back</Button>
          <Button v-if="stepIndex < 3" type="button" @click="async () => { const res = await validate(); if (res.valid) stepIndex++ }">Next</Button>
          <Button v-else :disabled="isSubmitting" type="submit">
            {{ isSubmitting ? 'Placing Order...' : 'Place Order' }}
          </Button>
        </div>
      </form>
    </Stepper>
  </Form>
</template>
