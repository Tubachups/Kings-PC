<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { useFormContext } from 'vee-validate'
import { computed, onMounted, ref, watch } from 'vue'
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
const selectedAddressId = ref('')

type CheckoutAddress = {
    id?: number
    full_name?: string
    address?: string
    region?: string
    province?: string | null
    city?: string
    barangay?: string
    label?: string | null
    is_default?: boolean
}

const defaultAddress = computed(() => page.props.defaultAddress as CheckoutAddress | null)
const customerAddresses = computed<CheckoutAddress[]>(() => {
    const customerData = page.props.customerData as { addresses?: CheckoutAddress[] } | undefined
    const addresses = customerData?.addresses

    return Array.isArray(addresses) ? addresses : []
})

const {
    regions, provinces, cities, barangays,
    selectedRegionCode, selectedProvinceCode, selectedCityCode, selectedBarangayCode,
    isRegionWithoutProvinces,
    hydrateSelections,
    onRegionChange, onProvinceChange, onCityChange, onBarangayChange,
} = usePsgc((field, value, shouldValidate = false) => {
    setFieldValue(field, value, shouldValidate)
}, values)

const applyAddressToFields = (address: CheckoutAddress): void => {
    setFieldValue('full_name', address.full_name ?? '', false)
    setFieldValue('address', address.address ?? '', false)
    setFieldValue('region', address.region ?? '', false)
    setFieldValue('province', address.province ?? '', false)
    setFieldValue('city', address.city ?? '', false)
    setFieldValue('barangay', address.barangay ?? '', false)
    void hydrateSelections({
        region: address.region,
        province: address.province,
        city: address.city,
        barangay: address.barangay,
    })
}

const selectSavedAddress = (addressId: string): void => {
    selectedAddressId.value = addressId

    const parsedAddressId = Number(addressId)
    if (!Number.isFinite(parsedAddressId)) {
        return
    }

    const selectedAddress = customerAddresses.value.find((address) => address.id === parsedAddressId)

    if (!selectedAddress) {
        return
    }

    applyAddressToFields(selectedAddress)
}

onMounted(() => {
    const address = defaultAddress.value

    if (!values.full_name && address?.full_name) {
        setFieldValue('full_name', address.full_name, false)
    } else if (!values.full_name && typeof authenticatedUserName === 'string' && authenticatedUserName.length > 0) {
        setFieldValue('full_name', authenticatedUserName, false)
    }

    if (!values.address && address?.address) {
        setFieldValue('address', address.address, false)
    }

    if (!values.region && address?.region) {
        setFieldValue('region', address.region, false)
    }

    if (!values.province && address?.province) {
        setFieldValue('province', address.province, false)
    }

    if (!values.city && address?.city) {
        setFieldValue('city', address.city, false)
    }

    if (!values.barangay && address?.barangay) {
        setFieldValue('barangay', address.barangay, false)
    }

    if (address?.id) {
        selectedAddressId.value = String(address.id)
    }

    void hydrateSelections({
        region: typeof values.region === 'string' && values.region.length > 0 ? values.region : address?.region,
        province: typeof values.province === 'string' && values.province.length > 0 ? values.province : address?.province,
        city: typeof values.city === 'string' && values.city.length > 0 ? values.city : address?.city,
        barangay: typeof values.barangay === 'string' && values.barangay.length > 0 ? values.barangay : address?.barangay,
    })
})

watch(defaultAddress, (address) => {
    if (!address?.id) {
        return
    }

    selectedAddressId.value = String(address.id)
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
            <div class="md:col-span-2" v-if="customerAddresses.length">
                <FormItem>
                    <FormLabel>Saved address</FormLabel>
                    <Select :model-value="selectedAddressId" @update:model-value="(value) => { if (typeof value === 'string') selectSavedAddress(value) }">
                        <FormControl>
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select a saved address" />
                            </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                            <SelectItem v-for="savedAddress in customerAddresses" :key="savedAddress.id" :value="String(savedAddress.id)">
                                {{ savedAddress.label || savedAddress.full_name || 'Saved address' }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </FormItem>
            </div>

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
