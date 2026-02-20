export interface Product {
    id: number;
    name: string;
    description: string;
    category_id: number;
    price: number;
    stock: number;
    image_url: string;
    is_active: boolean;
    category: Category;
    specs?: Record<string, any>;
}

export interface Category {
    id: number;
    name: string;
    slug: string;
}
