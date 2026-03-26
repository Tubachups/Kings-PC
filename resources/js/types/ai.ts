export type BuildProduct = {
    id: number;
    name: string;
    description?: string;
    category_id?: number;
    price: string | number;
    stock?: number;
    image_url: string;
    is_active?: boolean;
    category?: {
        id: number;
        name: string;
        slug: string;
    } | string;
    specs?: Record<string, any>;
};

export type AiBuildResponse = {
    build: BuildProduct[];
    total_price?: number | null;
    explanation?: string;
    budget?: number | null;
};
