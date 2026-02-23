<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { Check, Circle, Dot } from 'lucide-vue-next'
import { h, onMounted, ref, nextTick } from 'vue'
import { toast } from 'vue-sonner'
import * as z from 'zod'
import { Button } from '@/components/ui/button'
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper'
import { router } from "@inertiajs/vue3";
import { useCartStore } from '@/stores/cartStore'
import TotalCard from './TotalCard.vue'
import { useCart } from '@/composables/useCart'

const props = defineProps<{
    freshCart?: Array<{
        id:         number,
        price:      number,
        name:       string,
        quantity:   number
    }>;
    shippingFee?:    number
}>()
const reviewedItems = ref([]);
const shippingFee = ref(0);
const regions = ref([]);
const provinces = ref([]);
const cities = ref([]);
const barangays = ref([]);
const {items} = useCart();

onMounted(async () => {
  const res = await fetch('https://psgc.gitlab.io/api/regions/');
  regions.value = await res.json();
});

const formSchema = [
  z.object({
    first_name: z.string().min(2, "Name is required"),
    email: z.string().email(),
    mobile_number: z.string().min(11, "Invalid phone number"),
    address: z.string().min(5, "Address is required"),
    region: z.string().min(1, "Region is required"),
    province: z.string().optional(),
    city: z.string().min(1, "City is required"),
    barangay: z.string().min(1, "Barangay is required"),
  }),
  z.object({
    // step 2 - order review, no fields needed
  }),
  z.object({
    payment_method: z.union([z.literal('gcash'), z.literal('cod'), z.literal('card')]),
  }),
]

const formRef = ref<any>(null);

const isRegionWithoutProvinces = ref(false);

const onRegionChange = async (regionCode: string) => {
  provinces.value = [];
  cities.value = [];
  barangays.value = [];
  formRef.value?.setFieldValue('province', '');
  formRef.value?.setFieldValue('city', '');
  formRef.value?.setFieldValue('barangay', '');

  const res = await fetch(`https://psgc.gitlab.io/api/regions/${regionCode}/provinces/`);
  const data = await res.json();

  if (!data.length) {
    isRegionWithoutProvinces.value = true;
    const cityRes = await fetch(`https://psgc.gitlab.io/api/regions/${regionCode}/cities-municipalities/`);
    cities.value = await cityRes.json();
  } else {
    isRegionWithoutProvinces.value = false;
    provinces.value = data;
  }
};

const onProvinceChange = async (provinceCode: string) => {
  cities.value = [];
  barangays.value = [];
  formRef.value?.setFieldValue('city', '');
  formRef.value?.setFieldValue('barangay', '');

  const res = await fetch(`https://psgc.gitlab.io/api/provinces/${provinceCode}/cities-municipalities/`);
  cities.value = await res.json();
};

const onCityChange = async (cityCode: string) => {
  barangays.value = [];
  formRef.value?.setFieldValue('barangay', '');

  const res = await fetch(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`);
  barangays.value = await res.json();
};

const stepIndex = ref(1)
const steps = [
  {
    step: 1,
    title: 'Shipping Details',
    description: 'Provide shipping information',
  },
  {
    step: 2,
    title: 'Review Order',
    description: 'Review your order',
  },
  {
    step: 3,
    title: 'Payment',
    description: 'Provide payment details',
  },
]

function onSubmit(values: any) {
  toast('You submitted the following values:', {
    description: h('pre', { class: 'mt-2 w-[320px] rounded-md bg-neutral-950 p-4' }, h('code', { class: 'text-white' }, JSON.stringify(values, null, 2))),
  })
}

</script>

<template>
  <Form
    ref="formRef"
    v-slot="{ meta, values, validate, setFieldValue }"
    as=""
    keep-values
    :validation-schema="toTypedSchema(formSchema[stepIndex - 1])"
  >
    <Stepper v-slot="{ isNextDisabled, isPrevDisabled, nextStep, prevStep, modelValue }" v-model="stepIndex" class="block w-full">
      <form
        @submit="(e) => {
          e.preventDefault()
          validate()
          if (stepIndex === steps.length && meta.valid) {
            onSubmit(values)
          }
        }"
      >
        <div class="flex w-full flex-start gap-2">
          <StepperItem
            v-for="(step, index) in steps"
            :key="step.step"
            v-slot="{ state }"
            class="relative flex w-full flex-col items-center justify-center"
            :step="step.step"
          >
            <StepperSeparator
              v-if="step.step !== steps[steps.length - 1].step"
              class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary"
            />
            <StepperTrigger as-child>
              <Button
                :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                size="icon"
                class="z-10 rounded-full shrink-0"
                :class="[state === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                :disabled="state !== 'completed' && (index >= (modelValue || 0) && !meta.valid)"
              >
                <Check v-if="state === 'completed'" class="size-5" />
                <Circle v-if="state === 'active'" />
                <Dot v-if="state === 'inactive'" />
              </Button>
            </StepperTrigger>
            <div class="mt-5 flex flex-col items-center text-center">
              <StepperTitle :class="[state === 'active' && 'text-primary']" class="text-sm font-semibold transition lg:text-base">
                {{ step.title }}
              </StepperTitle>
              <StepperDescription :class="[state === 'active' && 'text-primary']" class="sr-only text-xs text-muted-foreground transition md:not-sr-only lg:text-sm">
                {{ step.description }}
              </StepperDescription>
            </div>
          </StepperItem>
        </div>

        <div class="grid grid-cols-4 gap-4 mt-6">

          <!-- STEP 1: Shipping Details -->
          <template v-if="stepIndex === 1">

            <div class="col-span-4">
              <FormField v-slot="{ componentField }" name="first_name">
                <FormItem>
                  <FormLabel>Full Name</FormLabel>
                  <FormControl>
                    <Input type="text" v-bind="componentField" />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              </FormField>
            </div>

            <div class="col-span-2">
              <FormField v-slot="{ componentField }" name="email">
                <FormItem>
                  <FormLabel>Email</FormLabel>
                  <FormControl>
                    <Input type="email" v-bind="componentField" />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              </FormField>
            </div>

            <div class="col-span-2">
              <FormField v-slot="{ componentField }" name="mobile_number">
                <FormItem>
                  <FormLabel>Contact Number</FormLabel>
                  <FormControl>
                    <Input type="tel" v-bind="componentField" />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              </FormField>
            </div>

            <div class="col-span-4">
              <FormField v-slot="{ componentField }" name="address">
                <FormItem>
                  <FormLabel>Street Address</FormLabel>
                  <FormControl>
                    <Input type="text" v-bind="componentField" />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              </FormField>
            </div>

            <div class="col-span-2">
            <FormField v-slot="{ value, handleChange, errorMessage }" name="region">
            <FormItem>
                <FormLabel>Region</FormLabel>
                <Select :model-value="value" @update:model-value="async (val) => { await nextTick(); handleChange(val, false); onRegionChange(val); }">
                <FormControl>
                    <SelectTrigger>
                    <SelectValue placeholder="Select Region" />
                    </SelectTrigger>
                </FormControl>
                <SelectContent>
                    <SelectItem v-for="region in regions" :key="region.code" :value="region.code">
                    {{ region.name }}
                    </SelectItem>
                </SelectContent>
                </Select>
                <FormMessage />
            </FormItem>
            </FormField>
            </div>

            <div class="col-span-2">
            <FormField v-slot="{ value, handleChange }" name="province">
            <FormItem>
                <FormLabel>Province</FormLabel>
                <Select 
                :model-value="value" 
                @update:model-value="async (val) => { await nextTick(); handleChange(val, false); onProvinceChange(val); }"
                :disabled="isRegionWithoutProvinces || !provinces.length"
                >
                <FormControl>
                    <SelectTrigger>
                    <SelectValue :placeholder="isRegionWithoutProvinces ? 'N/A for this region' : 'Select Province'" />
                    </SelectTrigger>
                </FormControl>
                <SelectContent>
                    <SelectItem v-for="province in provinces" :key="province.code" :value="province.code">
                    {{ province.name }}
                    </SelectItem>
                </SelectContent>
                </Select>
                <FormMessage />
            </FormItem>
            </FormField>
            </div>

            <div class="col-span-2">
            <FormField v-slot="{ value, handleChange }" name="city">
            <FormItem>
                <FormLabel>City / Municipality</FormLabel>
                <Select 
                :model-value="value" 
                @update:model-value="async (val) => { await nextTick(); handleChange(val, false); onCityChange(val); }"
                :disabled="!cities.length"
                >
                <FormControl>
                    <SelectTrigger>
                    <SelectValue placeholder="Select City" />
                    </SelectTrigger>
                </FormControl>
                <SelectContent>
                    <SelectItem v-for="city in cities" :key="city.code" :value="city.code">
                    {{ city.name }}
                    </SelectItem>
                </SelectContent>
                </Select>
                <FormMessage />
            </FormItem>
            </FormField>
            </div>

            <div class="col-span-2">
            <FormField v-slot="{ value, handleChange }" name="barangay">
                <FormItem >
                    <FormLabel>Barangay</FormLabel>
                    <Select 
                    :model-value="value" 
                    @update:model-value="async (val) => { await nextTick(); handleChange(val, false); }"
                    :disabled="!barangays.length"
                    >
                    <FormControl>
                        <SelectTrigger>
                        <SelectValue placeholder="Select Barangay" />
                        </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                        <SelectItem v-for="barangay in barangays" :key="barangay.code" :value="barangay.code">
                        {{ barangay.name }}
                        </SelectItem>
                    </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>
            </div>

          </template>

          <!-- STEP 2: Order Review -->
          <template v-if="stepIndex === 2">
            <div class="col-span-4">
              <p class="text-muted-foreground text-lg text-center">Please review your order before proceeding to payment.</p>
                <div class="">
                    <TotalCard :items="items" :shipping-fee="150" :is-checkout="true"/>
                </div>
            </div>
          </template>

          <!-- STEP 3: Payment -->
          <template v-if="stepIndex === 3">
            <div class="col-span-4">
              <FormField v-slot="{ componentField }" name="payment_method">
                <FormItem>
                  <FormLabel>Payment Method</FormLabel>
                  <Select v-bind="componentField">
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder="Select payment method" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem value="gcash">GCash</SelectItem>
                        <SelectItem value="cod">Cash on Delivery</SelectItem>
                        <SelectItem value="card">Credit / Debit Card</SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              </FormField>
            </div>
          </template>

        </div>

        <div class="flex items-center justify-between mt-6">
          <Button :disabled="isPrevDisabled" variant="outline" size="sm" @click="prevStep()">
            Back
          </Button>
          <div class="flex items-center gap-3">
            <Button
              v-if="stepIndex !== steps.length"
              :type="meta.valid ? 'button' : 'submit'"
              :disabled="isNextDisabled"
              size="sm"
              @click="meta.valid && nextStep()"
            >
              Next
            </Button>
            <Button v-if="stepIndex === steps.length" size="sm" type="submit">
              Place Order
            </Button>
          </div>
        </div>

      </form>
    </Stepper>
  </Form>
</template>