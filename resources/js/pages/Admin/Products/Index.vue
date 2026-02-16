<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import DataTable from '@/components/ui/data-table/DataTable.vue';
import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import { ArrowUpDown, Pencil, Trash2, Plus } from 'lucide-vue-next';
import { dashboard } from '@/routes';
import { index as productsIndex, create as productsCreate, edit as productsEdit, destroy as productsDestroy } from '@/actions/App/Http/Controllers/Admin/ProductController';
import type { Category, Product } from '@/types/product'

interface Props {
    products: {
        data: Product[];
    };
    categories: Array<Category>;
    filters: {
        search?: string;
        category?: number;
    };
}

const props = defineProps<Props>();

const columns: ColumnDef<Product>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () =>
                        column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]
            );
        },
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('name'));
        },
    },
    {
        accessorKey: 'category.name',
        header: 'Category',
        cell: ({ row }) => {
            return h(
                'div',
                { class: 'text-sm' },
                row.original.category?.name || 'N/A'
            );
        },
    },
    {
        accessorKey: 'price',
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () =>
                        column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Price', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]
            );
        },
        cell: ({ row }) => {
            const price = parseFloat(row.getValue('price'));
            const formatted = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'PHP',
            }).format(price);
            return h('div', { class: 'font-medium' }, formatted);
        },
    },
    {
        accessorKey: 'stock',
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () =>
                        column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Stock', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]
            );
        },
        cell: ({ row }) => {
            const stock = parseInt(row.getValue('stock'));
            return h(
                'div',
                { class: stock < 10 ? 'text-destructive font-medium' : '' },
                stock.toString()
            );
        },
    },
    {
        accessorKey: 'is_active',
        header: 'Status',
        cell: ({ row }) => {
            const isActive = row.getValue('is_active');
            return h(
                Badge,
                {
                    variant: isActive ? 'default' : 'secondary',
                },
                () => (isActive ? 'Active' : 'Inactive')
            );
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            const product = row.original;
            return h('div', { class: 'flex gap-2' }, [
                h(
                    Link,
                    {
                        href: productsEdit(product.id).url,
                    },
                    () =>
                        h(
                            Button,
                            { variant: 'outline', size: 'sm' },
                            () => [h(Pencil, { class: 'mr-2 h-4 w-4' }), 'Edit']
                        )
                ),
                h(
                    Button,
                    {
                        variant: 'destructive',
                        size: 'sm',
                        onClick: () => deleteProduct(product),
                    },
                    () => [h(Trash2, { class: 'mr-2 h-4 w-4' }), 'Delete']
                ),
            ]);
        },
    },
];

function deleteProduct(product: Product) {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(productsDestroy(product.id).url);
    }
}

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
                <Link :href="productsCreate().url">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Add Product
                    </Button>
                </Link>
            </div>

            <DataTable :columns="columns" :data="products.data" />
        </div>
    </AppLayout>
</template>
