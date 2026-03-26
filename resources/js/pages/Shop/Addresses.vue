<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { MapPin, Pencil, Star, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Layout from '@/layouts/MainLayout.vue';

defineOptions({ layout: Layout });

type Address = {
    id: number;
    label: string | null;
    full_name: string;
    address: string;
    region: string;
    province: string | null;
    city: string;
    barangay: string;
    is_default: boolean;
};

defineProps<{
    addresses: Address[];
}>();

const editingId = ref<number | null>(null);

const form = useForm({
    label: '',
    full_name: '',
    address: '',
    region: '',
    province: '',
    city: '',
    barangay: '',
    is_default: false,
});

const pageTitle = computed(() => (editingId.value ? 'Edit address' : 'Add new address'));

const resetForm = (): void => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
};

const startEdit = (address: Address): void => {
    editingId.value = address.id;
    form.label = address.label ?? '';
    form.full_name = address.full_name;
    form.address = address.address;
    form.region = address.region;
    form.province = address.province ?? '';
    form.city = address.city;
    form.barangay = address.barangay;
    form.is_default = address.is_default;
};

const submit = (): void => {
    if (editingId.value) {
        form.put(`/addresses/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                resetForm();
            },
        });

        return;
    }

    form.post('/addresses', {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
        },
    });
};

const setDefault = (id: number): void => {
    router.post(`/addresses/${id}/default`, {}, {
        preserveScroll: true,
    });
};

const removeAddress = (id: number): void => {
    router.delete(`/addresses/${id}`, {
        preserveScroll: true,
    });
};

const formatAddress = (address: Address): string => {
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
    <Head title="Address Book">
        <meta head-key="description" name="description" content="Manage your saved shipping addresses for faster checkout at King's PC." />
    </Head>

    <div class="mx-auto grid max-w-7xl gap-6 px-4 py-6 lg:grid-cols-5">
        <Card class="lg:col-span-2">
            <CardHeader>
                <CardTitle>{{ pageTitle }}</CardTitle>
                <CardDescription>Saved addresses can auto-fill your checkout shipping details.</CardDescription>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <Label for="label">Label</Label>
                        <Input id="label" v-model="form.label" placeholder="Home, Office, etc." />
                        <p v-if="form.errors.label" class="text-sm text-red-500">{{ form.errors.label }}</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="full_name">Full name</Label>
                        <Input id="full_name" v-model="form.full_name" placeholder="Juan Dela Cruz" />
                        <p v-if="form.errors.full_name" class="text-sm text-red-500">{{ form.errors.full_name }}</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="address">Street address</Label>
                        <Input id="address" v-model="form.address" placeholder="House no / street / landmark" />
                        <p v-if="form.errors.address" class="text-sm text-red-500">{{ form.errors.address }}</p>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="region">Region</Label>
                            <Input id="region" v-model="form.region" placeholder="Region" />
                            <p v-if="form.errors.region" class="text-sm text-red-500">{{ form.errors.region }}</p>
                        </div>
                        <div class="grid gap-2">
                            <Label for="province">Province</Label>
                            <Input id="province" v-model="form.province" placeholder="Province (optional)" />
                            <p v-if="form.errors.province" class="text-sm text-red-500">{{ form.errors.province }}</p>
                        </div>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="city">City / Municipality</Label>
                            <Input id="city" v-model="form.city" placeholder="City or municipality" />
                            <p v-if="form.errors.city" class="text-sm text-red-500">{{ form.errors.city }}</p>
                        </div>
                        <div class="grid gap-2">
                            <Label for="barangay">Barangay</Label>
                            <Input id="barangay" v-model="form.barangay" placeholder="Barangay" />
                            <p v-if="form.errors.barangay" class="text-sm text-red-500">{{ form.errors.barangay }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox id="is_default" :model-value="form.is_default" @update:model-value="(value) => (form.is_default = Boolean(value))" />
                        <Label for="is_default">Set as default shipping address</Label>
                    </div>

                    <div class="flex items-center gap-2">
                        <Button type="submit" :disabled="form.processing" class="cursor-pointer">
                            {{ editingId ? 'Update address' : 'Save address' }}
                        </Button>
                        <Button v-if="editingId" type="button" variant="outline" class="cursor-pointer" @click="resetForm">
                            Cancel
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card class="lg:col-span-3">
            <CardHeader>
                <CardTitle>Your addresses</CardTitle>
                <CardDescription>Manage your delivery destinations and default shipping address.</CardDescription>
            </CardHeader>
            <CardContent>
                <div v-if="addresses.length === 0" class="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">
                    No saved addresses yet.
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="address in addresses"
                        :key="address.id"
                        class="rounded-lg border bg-card p-4 shadow-sm"
                    >
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-foreground">
                                        {{ address.label || 'Saved address' }}
                                    </h3>
                                    <span v-if="address.is_default" class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary">
                                        <Star class="mr-1 h-3 w-3" /> Default
                                    </span>
                                </div>
                                <p class="text-sm font-medium text-foreground">{{ address.full_name }}</p>
                                <p class="flex items-start gap-2 text-sm text-muted-foreground">
                                    <MapPin class="mt-0.5 h-4 w-4 shrink-0" />
                                    <span>{{ formatAddress(address) }}</span>
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <Button type="button" variant="outline" size="sm" class="cursor-pointer" @click="startEdit(address)">
                                    <Pencil class="mr-1 h-4 w-4" /> Edit
                                </Button>
                                <Button
                                    v-if="!address.is_default"
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    class="cursor-pointer"
                                    @click="setDefault(address.id)"
                                >
                                    <Star class="mr-1 h-4 w-4" /> Set default
                                </Button>
                                <Button type="button" variant="destructive" size="sm" class="cursor-pointer" @click="removeAddress(address.id)">
                                    <Trash2 class="mr-1 h-4 w-4" /> Remove
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
