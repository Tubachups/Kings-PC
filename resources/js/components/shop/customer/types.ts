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
    total: number;
    status: string;
    created_at: string;
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
