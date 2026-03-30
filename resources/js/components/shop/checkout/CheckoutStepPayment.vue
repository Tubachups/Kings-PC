<script setup lang="ts">
import { useFormContext } from 'vee-validate'
import { watch } from 'vue'
import { FormField, FormItem, FormControl, FormMessage } from '@/components/ui/form'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useCheckoutStore } from '@/stores/checkoutStore'

const { values } = useFormContext()
const checkoutStore = useCheckoutStore()

watch(() => values.payment_method, (paymentMethod) => {
    checkoutStore.setPaymentMethod(
        paymentMethod === 'gcash' || paymentMethod === 'cod' || paymentMethod === 'card'
            ? paymentMethod
            : '',
    )
}, { immediate: true })
</script>

<template>
    <div class="rounded-xl border border-border/70 bg-background p-6 shadow-sm">
        <div class="mb-5">
            <h2 class="text-xl font-semibold tracking-tight sm:text-2xl">Payment method</h2>
            <p class="text-sm leading-6 text-muted-foreground">
                Choose your preferred payment method to complete your purchase securely.
            </p>
        </div>

        <FormField v-slot="{ componentField }" name="payment_method">
            <FormItem>
                <Select :Pmodel-value="componentField.modelValue"
                    @update:model-value="componentField['onUpdate:modelValue']">
                    <FormControl>
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select Method" />
                        </SelectTrigger>
                    </FormControl>
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
