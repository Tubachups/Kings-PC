import type { ColumnDef } from '@tanstack/vue-table';
import { h, type Ref } from 'vue';
import type { Product } from '@/types/product';

export interface ProductColumnOptions {
    selectedProducts: Ref<Set<number>>;
    toggleProductSelection: (selected: Set<number>, product: Product) => void;
    formatSortableHeader: (title: string) => any;
    formatCurrency: (value: string | number) => string;
    stockHeader?: string;
    isStockSortable?: boolean;
    stockCellClass?: (stock: number) => string;
}

export function useProductColumns(options: ProductColumnOptions) {
    const {
        selectedProducts,
        toggleProductSelection,
        formatSortableHeader,
        formatCurrency,
        stockHeader = 'Stock',
        isStockSortable = false,
        stockCellClass,
    } = options;

    const columns: ColumnDef<Product>[] = [
        {
            id: 'select',
            header: 'Select',
            cell: ({ row }) =>
                h('input', {
                    type: 'checkbox',
                    checked: selectedProducts.value.has(row.original.id),
                    onChange: () => toggleProductSelection(selectedProducts.value, row.original),
                    class: 'cursor-pointer',
                }),
        },
        {
            accessorKey: 'name',
            header: formatSortableHeader('Name'),
            cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
        },
        {
            accessorKey: 'category.name',
            header: 'Category',
            cell: ({ row }) => h('div', { class: 'text-sm' }, row.original.category?.name || 'N/A'),
        },
        {
            accessorKey: 'price',
            header: formatSortableHeader('Price'),
            cell: ({ row }) =>
                h('div', { class: 'font-medium' }, formatCurrency(row.getValue('price') as string | number)),
        },
        {
            accessorKey: 'stock',
            header: isStockSortable ? formatSortableHeader(stockHeader) : stockHeader,
            cell: ({ row }) => {
                const stock = parseInt(row.getValue('stock'));
                return h('div', { class: stockCellClass ? stockCellClass(stock) : '' }, stock.toString());
            },
        },
    ];

    return {
        columns,
    };
}