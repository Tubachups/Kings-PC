import type { Category, Product } from './product';

export interface ProductTableProps {
    products: {
        data: Product[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    categories?: Array<Category>;
    filters: {
        name?: string;
        category?: string;
    };
}
