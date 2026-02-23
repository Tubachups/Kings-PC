<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { Check, Circle, Dot } from 'lucide-vue-next'
import { h, onMounted, ref } from 'vue'
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

const regions = ref([]);
const provinces = ref([]);
const cities = ref([]);
const barangays = ref([]);

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
    // Add these for your dropdowns
    region: z.string().min(1),
    province: z.string().optional(),
    city: z.string().optional(),
  }),
  z.object({
    password: z.string().min(2).max(50),
    confirmPassword: z.string(),
  }).refine(
    (values) => {
      return values.password === values.confirmPassword
    },
    {
      message: 'Passwords must match!',
      path: ['confirmPassword'],
    },
  ),
  z.object({
    favoriteDrink: z.union([z.literal('coffee'), z.literal('tea'), z.literal('soda')]),
  }),
]

const formRef = ref<any>(null);

const onRegionChange = async (regionCode: string) => {
  const res = await fetch(`https://psgc.gitlab.io/api/regions/${regionCode}/provinces/`);
  provinces.value = await res.json();
  
  // Use the ref to set values
  if (formRef.value) {
    formRef.value.setFieldValue('province', '');
    formRef.value.setFieldValue('city', '');
  }
};

const stepIndex = ref(1)
const steps = [
  {
    step: 1,
    title: 'Shipping details',
    description: 'Provide shipping information',
  },
  {
    step: 2,
    title: 'Review Order',
    description: 'Review order summary',
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
              <StepperTitle
                :class="[state === 'active' && 'text-primary']"
                class="text-sm font-semibold transition lg:text-base"
              >
                {{ step.title }}
              </StepperTitle>
              <StepperDescription
                :class="[state === 'active' && 'text-primary']"
                class="sr-only text-xs text-muted-foreground transition md:not-sr-only lg:text-sm"
              >
                {{ step.description }}
              </StepperDescription>
            </div>
          </StepperItem>
        </div>

        <div class="grid grid-cols-4 gap-4 mt-4">
          <template v-if="stepIndex === 1">
            
            <div class="col-span-4">
            <FormField v-slot="{ componentField }" name="first_name" >
              <FormItem>
                <FormLabel>Full Name</FormLabel>
                <FormControl>
                  <Input type="text " v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
            </div>

            <div class="col-span-2">
            <FormField v-slot="{ componentField }" name="email" >
              <FormItem>
                <FormLabel>Email</FormLabel>
                <FormControl>
                  <Input type="email " v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
            </div>

            <div class="col-span-2">
            <FormField v-slot="{ componentField }" name="mobile_number" >
              <FormItem>
                <FormLabel>Contact Number</FormLabel>
                <FormControl>
                  <Input type="tel " v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
            </div>

            <div class="col-span-4">
            <FormField v-slot="{ componentField }" name="email" >
              <FormItem>
                <FormLabel>Address</FormLabel>
                <FormControl>
                  <Input type="tel " v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
            </div>

            <div class="col-span-2">
            <FormField v-slot="{ componentField }" name="region">
                <FormItem>
                <FormLabel>Region</FormLabel>
                <Select v-bind="componentField" @update:model-value="onRegionChange">
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

          </template>

          <template v-if="stepIndex === 2">
            <FormField v-slot="{ componentField }" name="password">
              <FormItem>
                <FormLabel>Password</FormLabel>
                <FormControl>
                  <Input type="password" v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="confirmPassword">
              <FormItem>
                <FormLabel>Confirm Password</FormLabel>
                <FormControl>
                  <Input type="password" v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
          </template>

          <template v-if="stepIndex === 3">
            <FormField v-slot="{ componentField }" name="favoriteDrink">
              <FormItem>
                <FormLabel>Drink</FormLabel>

                <Select v-bind="componentField">
                  <FormControl>
                    <SelectTrigger class="w-full!">
                      <SelectValue placeholder="Select a drink" />
                    </SelectTrigger>
                  </FormControl>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem value="coffee">
                        Coffee
                      </SelectItem>
                      <SelectItem value="tea">
                        Tea
                      </SelectItem>
                      <SelectItem value="soda">
                        Soda
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                <FormMessage />
              </FormItem>
            </FormField>
          </template>
        </div>

        <div class="flex items-center justify-between mt-4">
          <Button :disabled="isPrevDisabled" variant="outline" size="sm" @click="prevStep()">
            Back
          </Button>
          <div class="flex items-center gap-3">
            <Button v-if="stepIndex !== 3" :type="meta.valid ? 'button' : 'submit'" :disabled="isNextDisabled" size="sm" @click="meta.valid && nextStep()">
              Next
            </Button>
            <Button
              v-if="stepIndex === 3" size="sm" type="submit"
            >
              Submit
            </Button>
          </div>
        </div>
      </form>
    </Stepper>
  </Form>
</template>
