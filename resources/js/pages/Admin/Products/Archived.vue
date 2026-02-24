<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import DataTable from '@/components/ui/data-table/DataTable.vue';
import { h, ref } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import { ArrowUpDown, RotateCcw, Trash2 } from 'lucide-vue-next';
import { dashboard } from '@/routes';
import {
    index as productsIndex,
    archived as productsArchived,
    restore as productsRestore,
    forceDelete as productsForceDelete,
} from '@/actions/App/Http/Controllers/Admin/ProductController';
import type { Category, Product } from '@/types/product';

interface Props {
    products: {
        data: Product[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    filters: {
        name?: string;
    };
}

const props = defineProps<Props>();

const selectedProducts = ref<Set<number>>(new Set());

const currencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'PHP',
});

function sortableHeader(label: string) {
    return ({ column }: { column: any }) =>
        h(
            Button,
            {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            },
            () => [label, h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
        );
}

function restoreProduct(product: Product) {
    router.patch(productsRestore(product.id).url, {}, { preserveScroll: true });
}

function permanentlyDeleteProduct(product: Product) {
    toast(`Permanently delete "${product.name}"?`, {
        description: 'This cannot be undone.',
        cancel: {
            label: 'Delete',
            onClick: () => router.delete(productsForceDelete(product.id).url, { preserveScroll: true }),
        },
        action: {
            label: 'Cancel',
        },
    });
}

function toggleProductSelection(product: Product) {
    if (selectedProducts.value.has(product.id)) {
        selectedProducts.value.delete(product.id);
    } else {
        selectedProducts.value.add(product.id);
    }
}

function bulkRestore() {
    const ids = Array.from(selectedProducts.value);
    if (ids.length === 0) return;

    router.post(
        '/admin/products/bulk-restore',
        { ids },
        {
            onSuccess: () => {
                selectedProducts.value.clear();
                toast.success(`${ids.length} product(s) restored successfully!`);
            },
        },
    );
}

function bulkDelete() {
    const ids = Array.from(selectedProducts.value);
    if (ids.length === 0) return;

    toast(`Permanently delete ${ids.length} product(s)?`, {
        description: 'This cannot be undone.',
        cancel: {
            label: 'Delete',
            onClick: () => {
                router.post(
                    '/admin/products/bulk-force-delete',
                    { ids },
                    {
                        onSuccess: () => {
                            selectedProducts.value.clear();
                            toast.success(`${ids.length} product(s) permanently deleted!`);
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

const columns: ColumnDef<Product>[] = [
    {
        id: 'select',
        header: 'Select',
        cell: ({ row }) =>
            h('input', {
                type: 'checkbox',
                checked: selectedProducts.value.has(row.original.id),
                onChange: () => toggleProductSelection(row.original),
                class: 'cursor-pointer',
            }),
    },
    {
        accessorKey: 'name',
        header: sortableHeader('Name'),
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
    },
    {
        accessorKey: 'category.name',
        header: 'Category',
        cell: ({ row }) => h('div', { class: 'text-sm' }, row.original.category?.name || 'N/A'),
    },
    {
        accessorKey: 'price',
        header: sortableHeader('Price'),
        cell: ({ row }) =>
            h('div', { class: 'font-medium' }, currencyFormatter.format(parseFloat(row.getValue('price')))),
    },
    {
        accessorKey: 'stock',
        header: 'Stock',
        cell: ({ row }) => h('div', {}, parseInt(row.getValue('stock')).toString()),
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            const product = row.original;

            return h('div', { class: 'flex gap-2' }, [
                h(
                    Button,
                    { variant: 'outline', size: 'sm', onClick: () => restoreProduct(product) },
                    () => [h(RotateCcw, { class: 'mr-2 h-4 w-4' }), 'Restore'],
                ),
                h(
                    Button,
                    { variant: 'destructive', size: 'sm', onClick: () => permanentlyDeleteProduct(product) },
                    () => [h(Trash2, { class: 'mr-2 h-4 w-4' }), 'Delete Permanently'],
                ),
            ]);
        },
    },
];

const breadcrumbs = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Products', href: productsIndex().url },
    { title: 'Archived', href: productsArchived().url },
];
</script>

<template>
    <Head title="Archived Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Archived Products
                    </h1>
                    <p class="text-muted-foreground">
                        Restore or permanently delete archived products
                    </p>
                </div>
                <div class="flex gap-2">
                    <template v-if="selectedProducts.size > 0">
                        <Button @click="bulkRestore" variant="outline">
                            <RotateCcw class="mr-2 h-4 w-4" />
                            Restore ({{ selectedProducts.size }})
                        </Button>
                        <Button @click="bulkDelete" variant="destructive">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete ({{ selectedProducts.size }})
                        </Button>
                    </template>
                    <Link :href="productsIndex().url">
                        <Button variant="outline">
                            Back to Products
                        </Button>
                    </Link>
                </div>
            </div>

            <DataTable
                :columns="columns"
                :data="products.data"
                :meta="{
                    current_page: products.current_page,
                    last_page: products.last_page,
                    per_page: products.per_page,
                    total: products.total,
                    from: products.from,
                    to: products.to,
                }"
                :filters="filters"
            />
        </div>
    </AppLayout>
</template>
