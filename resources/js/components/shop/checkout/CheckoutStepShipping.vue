<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { useFormContext } from 'vee-validate'
import { onMounted, watch } from 'vue'
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { usePsgc } from '@/composables/usePsgc'
import { useCheckoutStore } from '@/stores/checkoutStore'

// Grab the setFieldValue function from the parent's form context
const { setFieldValue, values } = useFormContext()
const page = usePage()
const checkoutStore = useCheckoutStore()
const authenticatedUserName = page.props.auth?.user?.name
const defaultAddress = page.props.defaultAddress as {
    full_name?: string
    address?: string
    region?: string
    province?: string | null
    city?: string
    barangay?: string
} | null

const {
    regions, provinces, cities, barangays,
    selectedRegionCode, selectedProvinceCode, selectedCityCode, selectedBarangayCode,
    isRegionWithoutProvinces,
    onRegionChange, onProvinceChange, onCityChange, onBarangayChange,
} = usePsgc((field, value, shouldValidate = false) => {
    setFieldValue(field, value, shouldValidate)
}, values)

onMounted(() => {
    if (!values.full_name && defaultAddress?.full_name) {
        setFieldValue('full_name', defaultAddress.full_name, false)
    } else if (!values.full_name && typeof authenticatedUserName === 'string' && authenticatedUserName.length > 0) {
        setFieldValue('full_name', authenticatedUserName, false)
    }

    if (!values.address && defaultAddress?.address) {
        setFieldValue('address', defaultAddress.address, false)
    }

    if (!values.region && defaultAddress?.region) {
        setFieldValue('region', defaultAddress.region, false)
    }

    if (!values.province && defaultAddress?.province) {
        setFieldValue('province', defaultAddress.province, false)
    }

    if (!values.city && defaultAddress?.city) {
        setFieldValue('city', defaultAddress.city, false)
    }

    if (!values.barangay && defaultAddress?.barangay) {
        setFieldValue('barangay', defaultAddress.barangay, false)
    }
})

watch(() => [
    values.full_name,
    values.address,
    values.region,
    values.province,
    values.city,
    values.barangay,
], ([fullName, address, region, province, city, barangay]) => {
    checkoutStore.setShippingDraft({
        full_name: typeof fullName === 'string' ? fullName : '',
        address: typeof address === 'string' ? address : '',
        region: typeof region === 'string' ? region : '',
        province: typeof province === 'string' ? province : '',
        city: typeof city === 'string' ? city : '',
        barangay: typeof barangay === 'string' ? barangay : '',
    })
}, { immediate: true })
</script>

<template>
    <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm">
        <div >
            <h2 class="text-xl font-semibold tracking-tight sm:text-2xl">Shipping details</h2>
            <p class="text-sm leading-6 text-muted-foreground">
                Let’s get your order headed your way! Please enter the exact address where you'd like to receive your package.
            </p>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <FormField v-slot="{ componentField }" name="full_name">
                    <FormItem>
                        <FormLabel>Full Name</FormLabel>
                        <FormControl>
                            <Input autocomplete="name" placeholder="Juan Dela Cruz" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </div>

            <div>
                <FormField name="region">
                    <FormItem>
                        <FormLabel>Region</FormLabel>
                        <Select :model-value="selectedRegionCode"
                            @update:model-value="(val) => { if (typeof val === 'string' && val.length > 0) onRegionChange(val) }">
                            <FormControl>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select Region" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem v-for="r in regions" :key="r.code" :value="r.code">{{ r.name }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </div>

            <div>
                <FormField name="province">
                    <FormItem>
                        <FormLabel>Province</FormLabel>
                        <Select :model-value="selectedProvinceCode"
                            :disabled="isRegionWithoutProvinces || !provinces.length"
                            @update:model-value="(val) => { if (typeof val === 'string' && val.length > 0) onProvinceChange(val) }">
                            <FormControl>
                                <SelectTrigger class="w-full">
                                    <SelectValue :placeholder="isRegionWithoutProvinces ? 'N/A' : 'Select Province'" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem v-for="p in provinces" :key="p.code" :value="p.code">{{ p.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </div>

            <div>
                <FormField name="city">
                    <FormItem>
                        <FormLabel>City</FormLabel>
                        <Select :model-value="selectedCityCode" :disabled="!cities.length"
                            @update:model-value="(val) => { if (typeof val === 'string' && val.length > 0) onCityChange(val) }">
                            <FormControl>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select City" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem v-for="c in cities" :key="c.code" :value="c.code">{{ c.name }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </div>

            <div>
                <FormField name="barangay">
                    <FormItem>
                        <FormLabel>Barangay</FormLabel>
                        <Select :model-value="selectedBarangayCode" :disabled="!barangays.length"
                            @update:model-value="(val) => { if (typeof val === 'string' && val.length > 0) onBarangayChange(val) }">
                            <FormControl>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select Barangay" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem v-for="b in barangays" :key="b.code" :value="b.code">{{ b.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </div>

            <div class="md:col-span-2">
                <FormField v-slot="{ componentField }" name="address">
                    <FormItem>
                        <FormLabel>Street Address</FormLabel>
                        <FormControl>
                            <Input autocomplete="street-address" placeholder="House No / Street / Landmark"
                                v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </div>
        </div>
    </div>
</template>
