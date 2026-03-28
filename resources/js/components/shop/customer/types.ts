import type { Product } from '@/types/product';

export type DashboardAddress = {
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

export type DashboardOrder = {
    id: number;
    order_number?: string;
    total: number;
    status: string;
    created_at: string;
    order_items?: DashboardOrderItem[];
    shipping_address?: DashboardOrderAddress | null;
    billing_address?: DashboardOrderAddress | null;
};

export type DashboardOrderAddress = {
    address?: string;
    region?: string;
    province?: string | null;
    city?: string;
    barangay?: string;
};

export type DashboardOrderItem = {
    id: number;
    quantity: number;
    unit_price: number;
    product?: {
        id: number;
        name: string;
        image_url: string | null;
    } | null;
};

export type CustomerSummary = {
    ordersCount?: number;
    addressCount?: number;
    wishlistCount?: number;
};

export type CustomerPageProps = {
    customerData?: {
        addresses?: DashboardAddress[];
        orders?: DashboardOrder[];
        wishlist?: Product[];
    };
    customerSummary?: CustomerSummary;
};
