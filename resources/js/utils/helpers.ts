import { h } from 'vue';
import { Button } from '@/components/ui/button';
import { ArrowUpDown } from 'lucide-vue-next';

const pesoFormatter = new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    maximumFractionDigits: 0,
});


export const formatSortableHeader = (label: string) => {
    return ({ column }: { column: any }) =>
        h(
            Button,
            {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            },
            () => [label, h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
        );
};

export const toggleProductSelection = (selectedProducts: Set<number>, product: { id: number }) => {
    if (selectedProducts.has(product.id)) {
        selectedProducts.delete(product.id);
    } else {
        selectedProducts.add(product.id);
    }
};



export const formatCurrency = (amount: string | number): string => {
    return pesoFormatter.format(Number(amount || 0));
};

export const formatAddress = (address?: Record<string, any>) => {
    if (!address) return 'N/A';
    return [
        address.address,
        address.barangay,
        address.city,
        address.province,
        address.region,
    ]
        .filter(Boolean)
        .join(', ');
};

export const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

export const formatPrice = (price: number | null): string | null => {
    return price !== null ? pesoFormatter.format(price) : null;
};
export const getFilteredImageUrl = (image_url : string) => {
    if (!image_url) return '';
    return !image_url.startsWith('/storage')
        ? image_url.replace(/^\/storage/, '')
        : image_url;
};
