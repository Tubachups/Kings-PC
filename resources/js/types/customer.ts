export interface CustomerCredentialSummary {
    password_set: boolean;
    google_linked: boolean;
    facebook_linked: boolean;
    email_verified: boolean;
    two_factor_enabled: boolean;
}

export interface CustomerRecord {
    id: number;
    name: string;
    email: string;
    orders_count: number;
    delivered_spend: number;
    created_at?: string | null;
    credentials: CustomerCredentialSummary;
}

export interface PaginatedCustomers {
    data: CustomerRecord[];
    current_page: number;
    per_page: number;
    total: number;
}
