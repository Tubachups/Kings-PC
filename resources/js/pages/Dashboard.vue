<script setup lang="ts">
import { Head, Link, router, useForm, usePage, usePoll } from '@inertiajs/vue3';
import { CalendarDays, Heart, MapPin, Pencil, ShoppingCart, Star, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import DashboardMetricsGrid from '@/components/shop/admin/DashboardMetricsGrid.vue';
import DashboardRevenueChart from '@/components/shop/admin/DashboardRevenueChart.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useCart } from '@/composables/useCart';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard, shop } from '@/routes';
import addresses from '@/routes/addresses';
import { type BreadcrumbItem } from '@/types';
import type { Product } from '@/types/product';
import { getFilteredImageUrl } from '@/utils/helpers';

type DashboardAddress = {
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

type DashboardOrder = {
    id: number;
    total: number;
    status: string;
    created_at: string;
};

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => Boolean(user.value?.is_admin));
const { handleAddToCart } = useCart();

const customerData = computed(() => {
    return (page.props.customerData as {
        addresses?: DashboardAddress[];
        orders?: DashboardOrder[];
        wishlist?: Product[];
    } | undefined) ?? {};
});

const customerAddresses = computed<DashboardAddress[]>(() => customerData.value.addresses ?? []);
const customerOrders = computed<DashboardOrder[]>(() => customerData.value.orders ?? []);
const customerWishlist = computed<Product[]>(() => customerData.value.wishlist ?? []);

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

const resetAddressForm = (): void => {
    editingAddressId.value = null;
    addressForm.reset();
    addressForm.clearErrors();
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

const customerSummary = computed(() => {
    return (page.props.customerSummary as {
        ordersCount?: number;
        addressCount?: number;
        wishlistCount?: number;
    } | undefined) ?? {};
});

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

const formatPrice = (value: number): string => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2,
    }).format(value);
};

const formatDate = (value: string): string => {
    return new Date(value).toLocaleDateString('en-PH', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const statusPillClass = (status: string): string => {
    const normalizedStatus = status.toLowerCase();

    if (normalizedStatus === 'delivered') {
        return 'bg-green-100 text-green-700';
    }

    if (normalizedStatus === 'shipped' || normalizedStatus === 'processing') {
        return 'bg-blue-100 text-blue-700';
    }

    return 'bg-muted text-muted-foreground';
};

const toggleWishlist = (productId: number): void => {
    router.post(`/wishlist/${productId}`, {}, {
        preserveScroll: true,
        preserveState: true,
        only: ['customerData', 'customerSummary', 'wishlistProductIds', 'flash'],
    });
};

const { start, stop } = usePoll(
    2000,
    {
        only: [
            'totalProducts',
            'totalOrders',
            'lowStockCount',
            'totalCustomers',
            'pendingOrdersCount',
            'processedOrdersCount',
            'shippedOrdersCount',
            'deliveredOrdersCount',
        ],
    },
    {
        autoStart: false,
        keepAlive: true,
    },
);

watch(
    isAdmin,
    (admin) => {
        if (admin) {
            start();
        } else {
            stop();
        }
    },
    { immediate: true },
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard">
        <meta head-key="description" name="description" content="View your King's PC dashboard, account activity, and quick actions." />
    </Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Welcome Section -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Welcome back, {{ user?.name }}!
                </h1>
                <p class="text-muted-foreground">
                    Here's what's happening with your PC parts store.
                </p>
            </div>

            <!-- Admin Quick Actions -->
            <div v-if="user?.is_admin" >
                <DashboardMetricsGrid />
                <DashboardRevenueChart class="mt-6" />
            </div>

            <!-- Customer View -->
            <div v-else>
                <Card class='p-3'>
                    <CardHeader>
                        <CardTitle>Welcome to PC Parts Store</CardTitle>
                        <CardDescription>
                            Browse our collection of high-quality PC components
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Link :href="shop().url">
                            <Button>
                                <ShoppingCart class="mr-2 h-4 w-4" />
                                Browse Products
                            </Button>
                        </Link>
                    </CardContent>
                </Card>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
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
                                    <Input id="dashboard-address-region" v-model="addressForm.region" placeholder="Region" />
                                    <p v-if="addressForm.errors.region" class="text-xs text-red-500">{{ addressForm.errors.region }}</p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="dashboard-address-province">Province</Label>
                                    <Input id="dashboard-address-province" v-model="addressForm.province" placeholder="Province (optional)" />
                                    <p v-if="addressForm.errors.province" class="text-xs text-red-500">{{ addressForm.errors.province }}</p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="dashboard-address-city">City</Label>
                                    <Input id="dashboard-address-city" v-model="addressForm.city" placeholder="City / Municipality" />
                                    <p v-if="addressForm.errors.city" class="text-xs text-red-500">{{ addressForm.errors.city }}</p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="dashboard-address-barangay">Barangay</Label>
                                    <Input id="dashboard-address-barangay" v-model="addressForm.barangay" placeholder="Barangay" />
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

                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-base">
                                <CalendarDays class="h-4 w-4" />
                                Orders
                            </CardTitle>
                            <CardDescription>
                                {{ customerSummary.ordersCount ?? 0 }} orders placed
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div v-if="customerOrders.length === 0" class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground">
                                No orders yet.
                            </div>
                            <div v-for="order in customerOrders" :key="`dashboard-order-${order.id}`" class="rounded-lg border p-3 text-sm">
                                <div class="mb-1 flex items-center justify-between gap-2">
                                    <p class="font-semibold">Order #{{ order.id }}</p>
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-medium" :class="statusPillClass(order.status)">
                                        {{ order.status }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-muted-foreground">
                                    <span>{{ formatDate(order.created_at) }}</span>
                                    <span>{{ formatPrice(order.total) }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-base">
                                <Heart class="h-4 w-4" />
                                Wishlist
                            </CardTitle>
                            <CardDescription>
                                {{ customerSummary.wishlistCount ?? 0 }} saved items
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div v-if="customerWishlist.length === 0" class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground">
                                Wishlist is empty.
                            </div>
                            <div v-for="product in customerWishlist" :key="`dashboard-wishlist-${product.id}`" class="rounded-lg border p-2">
                                <div class="flex items-center gap-2">
                                    <img :src="getFilteredImageUrl(product.image_url)" :alt="product.name" class="h-12 w-12 rounded object-cover" />
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium">{{ product.name }}</p>
                                        <p class="text-xs text-green-600">{{ formatPrice(Number(product.price)) }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <Button size="sm" class="flex-1 cursor-pointer" @click="handleAddToCart(product)">
                                        <ShoppingCart class="mr-1 h-3.5 w-3.5" /> Add
                                    </Button>
                                    <Button size="sm" variant="destructive" class="cursor-pointer" @click="toggleWishlist(product.id)">
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
