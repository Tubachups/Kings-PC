<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { Pencil, Plus, Archive } from 'lucide-vue-next';
import type { AcceptableValue } from 'reka-ui';
import { h, ref } from 'vue';
import { toast } from 'vue-sonner';
import {
    index as productsIndex,
    create as productsCreate,
    edit as productsEdit,
    destroy as productsDestroy,
    updateStatus as productsUpdateStatus,
    archived as productsArchived,
} from '@/actions/App/Http/Controllers/Admin/ProductController';
import { Button } from '@/components/ui/button';
import DataTable from '@/components/ui/data-table/DataTable.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useProductColumns } from '@/composables/useProductColumns';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Product } from '@/types/product';
import type { ProductTableProps } from '@/types/product-table';
import { formatCurrency, formatSortableHeader, toggleProductSelection } from '@/utils/helpers';

defineProps<ProductTableProps>();

const selectedProducts = ref<Set<number>>(new Set());


function updateProductStatus(product: Product, value: AcceptableValue) {
    router.patch(
        productsUpdateStatus(product.id).url,
        { is_active: String(value) === '1' },
        { preserveScroll: true },
    );
}

function deleteProduct(product: Product) {
    toast(`Archive "${product.name}"?`, {
        description: 'You can restore it later from Archived Products.',
        cancel: {
            label: 'Archive',
            onClick: () => router.delete(productsDestroy(product.id).url),
        },
        action: {
            label: 'Cancel',
        },
    });
}


function bulkArchive() {
    const ids = Array.from(selectedProducts.value);
    if (ids.length === 0) return;

    toast(`Archive ${ids.length} product(s)?`, {
        description: 'You can restore them later from Archived Products.',
        cancel: {
            label: 'Archive',
            onClick: () => {
                router.post(
                    '/admin/products/bulk-archive',
                    { ids },
                    {
                        onSuccess: () => {
                            selectedProducts.value.clear();
                            toast.success(`${ids.length} product(s) archived successfully!`);
                        },
                    },
                );
            },
        },
        action: {
            label: 'Cancel',
        },
    });
}

function bulkUpdateStatus(is_active: boolean) {
    const ids = Array.from(selectedProducts.value);
    if (ids.length === 0) return;

    const statusLabel = is_active ? 'Activate' : 'Inactivate';

    router.post(
        '/admin/products/bulk-update-status',
        { ids, is_active },
        {
            onSuccess: () => {
                selectedProducts.value.clear();
                toast.success(`${ids.length} product(s) ${statusLabel.toLowerCase()}d successfully!`);
            },
        },
    );
}

const { columns: baseColumns } = useProductColumns({
    selectedProducts,
    toggleProductSelection,
    formatSortableHeader,
    formatCurrency,
    stockHeader: 'Stock',
    isStockSortable: true,
    stockCellClass: (stock) => (stock < 10 ? 'text-destructive font-medium' : ''),
});

const columns: ColumnDef<Product>[] = [
    ...baseColumns,
    {
        accessorKey: 'is_active',
        header: 'Status',
        cell: ({ row }) => {
            const product = row.original;

            return h(
                Select,
                {
                    modelValue: product.is_active ? '1' : '0',
                    'onUpdate:modelValue': (value: AcceptableValue) => updateProductStatus(product, value),
                },
                {
                    default: () => [
                        h(SelectTrigger, { class: 'w-36' }, () => h(SelectValue, { placeholder: 'Status' })),
                        h(SelectContent, {}, () => [
                            h(SelectItem, { value: '1' }, () => 'Active'),
                            h(SelectItem, { value: '0' }, () => 'Inactive'),
                        ]),
                    ],
                },
            );
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            const product = row.original;

            return h('div', { class: 'flex gap-2' }, [
                h(Link, { href: productsEdit(product.id).url }, () =>
                    h(Button, { variant: 'outline', size: 'sm' }, () => [
                        h(Pencil, { class: 'mr-2 h-4 w-4' }),
                        'Edit',
                    ]),
                ),
                h(
                    Button,
                    { variant: 'destructive', size: 'sm', onClick: () => deleteProduct(product) },
                    () => [h(Archive, { class: 'mr-2 h-4 w-4' }), 'Archive'],
                ),
            ]);
        },
    },
];

const breadcrumbs = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Products', href: productsIndex().url },
];
</script>

<template>

    <Head title="Manage Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Manage Products
                    </h1>
                    <p class="text-muted-foreground">
                        Manage your PC parts inventory
                    </p>
                </div>
                <div class="flex gap-2">
                    <template v-if="selectedProducts.size > 0">
                        <Button @click="bulkUpdateStatus(true)" variant="outline" size="sm">
                            Activate ({{ selectedProducts.size }})
                        </Button>
                        <Button @click="bulkUpdateStatus(false)" variant="outline" size="sm">
                            Inactivate ({{ selectedProducts.size }})
                        </Button>
                        <Button @click="bulkArchive" variant="destructive">
                            <Archive class="mr-2 h-4 w-4" />
                            Archive ({{ selectedProducts.size }})
                        </Button>
                    </template>
                    <Link :href="productsArchived().url">
                        <Button variant="outline">
                            <Archive class="mr-2 h-4 w-4" />
                            Archived
                        </Button>
                    </Link>
                    <Link :href="productsCreate().url">
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Add Product
                        </Button>
                    </Link>
                </div>
            </div>

            <DataTable :columns="columns" :data="products.data" :meta="{
                current_page: products.current_page,
                last_page: products.last_page,
                per_page: products.per_page,
                total: products.total,
                from: products.from,
                to: products.to,
            }" :filters="filters" />
        </div>
    </AppLayout>
</template>
