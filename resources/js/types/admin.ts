import type { Component } from 'vue';


export type Metric = {
    key: string;
    title: string;
    value: string | number;
    description: string;
    icon: Component;
};

export type DashboardPageProps = {
    totalProducts?: number;
    totalOrders?: number;
    lowStockCount?: number;
    totalCustomers?: number;
    pendingOrdersCount?: number;
    processedOrdersCount?: number;
    shippedOrdersCount?: number;
    deliveredOrdersCount?: number;
};
