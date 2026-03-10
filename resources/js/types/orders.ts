export interface PendingOrder {
    id: number;
    order_number: string;
    total: number | string;
    status?: string | null;
    next_status?: string | null;
    next_action_label?: string | null;
    payment_method?: string | null;
    payment_status?: string | null;
    created_at?: string | null;
    items_count: number;
    customer?: {
        name?: string | null;
        email?: string | null;
    };
    order_items: Array<{
        id: number;
        quantity: number;
        unit_price: number;
        product?: {
            name?: string | null;
            image_url?: string | null;
        };
    }>;
};

export interface PaginatedOrders {
    data: PendingOrder[];
    current_page: number;
    per_page: number;
    total: number;
}
