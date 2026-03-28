<script setup lang="ts">
import { router, useForm, usePage } from '@inertiajs/vue3';
import { MapPin, Pencil, Star, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { usePsgc } from '@/composables/usePsgc';
import addresses from '@/routes/addresses';
import type { CustomerPageProps, DashboardAddress } from './types';

const page = usePage<CustomerPageProps>();

const customerData = computed(() => page.props.customerData ?? {});
const customerSummary = computed(() => page.props.customerSummary ?? {});
const customerAddresses = computed<DashboardAddress[]>(() => customerData.value.addresses ?? []);

const editingAddressId = ref<number | null>(null);
const addressForm = useForm({
    label: '',
    full_name: '',
    address: '',
    region: '',
    province: '',
    city: '',
    barangay: '',
    is_default: false,
});

const isEditingAddress = computed(() => editingAddressId.value !== null);

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
    hydrateSelections,
    onRegionChange,
    onProvinceChange,
    onCityChange,
    onBarangayChange,
} = usePsgc((field, value) => {
    if (field === 'region' || field === 'province' || field === 'city' || field === 'barangay') {
        addressForm[field] = value;
    }
});

const clearLocationSelections = (): void => {
    selectedRegionCode.value = '';
    selectedProvinceCode.value = '';
    selectedCityCode.value = '';
    selectedBarangayCode.value = '';
};

const resetAddressForm = (): void => {
    editingAddressId.value = null;
    addressForm.reset();
    addressForm.clearErrors();
    clearLocationSelections();
};

const startEditAddress = (address: DashboardAddress): void => {
    editingAddressId.value = address.id;
    addressForm.label = address.label ?? '';
    addressForm.full_name = address.full_name;
    addressForm.address = address.address;
    addressForm.region = address.region;
    addressForm.province = address.province ?? '';
    addressForm.city = address.city;
    addressForm.barangay = address.barangay;
    addressForm.is_default = address.is_default;

    void hydrateSelections({
        region: address.region,
        province: address.province,
        city: address.city,
        barangay: address.barangay,
    });
};

const submitAddressForm = (): void => {
    const submitOptions = {
        preserveScroll: true,
        preserveState: true,
        only: ['customerData', 'customerSummary', 'defaultAddress', 'flash'],
        onSuccess: () => {
            resetAddressForm();
        },
    };

    if (editingAddressId.value) {
        addressForm.put(addresses.update(editingAddressId.value).url, submitOptions);

        return;
    }

    addressForm.post(addresses.store().url, submitOptions);
};

const setDefaultAddress = (addressId: number): void => {
    router.post(addresses.default(addressId).url, {}, {
        preserveScroll: true,
        preserveState: true,
        only: ['customerData', 'customerSummary', 'defaultAddress', 'flash'],
    });
};

const deleteAddress = (addressId: number): void => {
    router.delete(addresses.destroy(addressId).url, {
        preserveScroll: true,
        preserveState: true,
        only: ['customerData', 'customerSummary', 'defaultAddress', 'flash'],
        onSuccess: () => {
            if (editingAddressId.value === addressId) {
                resetAddressForm();
            }
        },
    });
};

const formatAddress = (address: DashboardAddress): string => {
    return [
        address.address,
        address.barangay,
        address.city,
        address.province,
        address.region,
    ]
        .filter((value) => typeof value === 'string' && value.length > 0)
        .join(', ');
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-base">
                <MapPin class="h-4 w-4" />
                Address Book
            </CardTitle>
            <CardDescription>
                {{ customerSummary.addressCount ?? 0 }} saved addresses
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-3">
            <form class="space-y-2 rounded-lg border p-3" @submit.prevent="submitAddressForm">
                <p class="text-sm font-semibold">
                    {{ isEditingAddress ? 'Edit address' : 'Add address' }}
                </p>

                <div class="grid gap-2">
                    <Label for="dashboard-address-label">Label</Label>
                    <Input id="dashboard-address-label" v-model="addressForm.label" placeholder="Home, Office" />
                    <p v-if="addressForm.errors.label" class="text-xs text-red-500">{{ addressForm.errors.label }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="dashboard-address-full-name">Full name</Label>
                    <Input id="dashboard-address-full-name" v-model="addressForm.full_name" placeholder="Juan Dela Cruz" />
                    <p v-if="addressForm.errors.full_name" class="text-xs text-red-500">{{ addressForm.errors.full_name }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="dashboard-address-line">Street address</Label>
                    <Input id="dashboard-address-line" v-model="addressForm.address" placeholder="House no / street / landmark" />
                    <p v-if="addressForm.errors.address" class="text-xs text-red-500">{{ addressForm.errors.address }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="dashboard-address-region">Region</Label>
                    <Select
                        :model-value="selectedRegionCode"
                        @update:model-value="(value) => {
                            if (typeof value === 'string' && value.length > 0) {
                                onRegionChange(value);
                            }
                        }"
                    >
                        <SelectTrigger id="dashboard-address-region" class="w-full">
                            <SelectValue placeholder="Select region" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="region in regions" :key="region.code" :value="region.code">
                                {{ region.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="addressForm.errors.region" class="text-xs text-red-500">{{ addressForm.errors.region }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="dashboard-address-province">Province</Label>
                    <Select
                        :model-value="selectedProvinceCode"
                        :disabled="isRegionWithoutProvinces || !provinces.length"
                        @update:model-value="(value) => {
                            if (typeof value === 'string' && value.length > 0) {
                                onProvinceChange(value);
                            }
                        }"
                    >
                        <SelectTrigger id="dashboard-address-province" class="w-full">
                            <SelectValue :placeholder="isRegionWithoutProvinces ? 'N/A' : 'Select province'" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="province in provinces" :key="province.code" :value="province.code">
                                {{ province.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="addressForm.errors.province" class="text-xs text-red-500">{{ addressForm.errors.province }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="dashboard-address-city">City</Label>
                    <Select
                        :model-value="selectedCityCode"
                        :disabled="!cities.length"
                        @update:model-value="(value) => {
                            if (typeof value === 'string' && value.length > 0) {
                                onCityChange(value);
                            }
                        }"
                    >
                        <SelectTrigger id="dashboard-address-city" class="w-full">
                            <SelectValue placeholder="Select city / municipality" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="city in cities" :key="city.code" :value="city.code">
                                {{ city.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="addressForm.errors.city" class="text-xs text-red-500">{{ addressForm.errors.city }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="dashboard-address-barangay">Barangay</Label>
                    <Select
                        :model-value="selectedBarangayCode"
                        :disabled="!barangays.length"
                        @update:model-value="(value) => {
                            if (typeof value === 'string' && value.length > 0) {
                                onBarangayChange(value);
                            }
                        }"
                    >
                        <SelectTrigger id="dashboard-address-barangay" class="w-full">
                            <SelectValue placeholder="Select barangay" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="barangay in barangays" :key="barangay.code" :value="barangay.code">
                                {{ barangay.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="addressForm.errors.barangay" class="text-xs text-red-500">{{ addressForm.errors.barangay }}</p>
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox
                        id="dashboard-address-default"
                        :model-value="addressForm.is_default"
                        @update:model-value="(value) => (addressForm.is_default = Boolean(value))"
                    />
                    <Label for="dashboard-address-default">Set as default</Label>
                </div>

                <div class="flex items-center gap-2">
                    <Button type="submit" class="cursor-pointer" :disabled="addressForm.processing">
                        {{ isEditingAddress ? 'Update' : 'Save' }}
                    </Button>
                    <Button v-if="isEditingAddress" type="button" variant="outline" class="cursor-pointer" @click="resetAddressForm">
                        Cancel
                    </Button>
                </div>
            </form>

            <div v-if="customerAddresses.length === 0" class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground">
                No saved addresses yet.
            </div>
            <div v-for="address in customerAddresses" :key="`dashboard-address-${address.id}`" class="rounded-lg border p-3">
                <div class="mb-1 flex items-center gap-2">
                    <p class="text-sm font-semibold">{{ address.label || 'Saved address' }}</p>
                    <span v-if="address.is_default" class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">
                        <Star class="mr-1 h-3 w-3" /> Default
                    </span>
                </div>
                <p class="text-xs text-muted-foreground">{{ address.full_name }}</p>
                <p class="mt-1 flex items-start gap-1.5 text-xs text-muted-foreground">
                    <MapPin class="mt-0.5 h-3 w-3 shrink-0" />
                    <span>{{ formatAddress(address) }}</span>
                </p>

                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <Button size="sm" variant="outline" class="cursor-pointer" @click="startEditAddress(address)">
                        <Pencil class="mr-1 h-3.5 w-3.5" /> Edit
                    </Button>
                    <Button
                        v-if="!address.is_default"
                        size="sm"
                        variant="outline"
                        class="cursor-pointer"
                        @click="setDefaultAddress(address.id)"
                    >
                        <Star class="mr-1 h-3.5 w-3.5" /> Default
                    </Button>
                    <Button size="sm" variant="destructive" class="cursor-pointer" @click="deleteAddress(address.id)">
                        <Trash2 class="mr-1 h-3.5 w-3.5" /> Remove
                    </Button>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
