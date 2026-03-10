import type { Category } from '@/types/product';

type SpecsBehavior = 'always' | 'if-empty';


export interface ProductFormState {
    name: string;
    price: string;
    category_id: number | string | null;
    stock: string;
    image: File | string | null;
    specs: string;
    is_active: boolean;
    errors: Record<string, string | undefined>;
    processing: boolean;
}

export interface ProductFormProps {
    form: ProductFormState;
    categories: Category[];
    submitLabel: string;
    cancelHref: string;
    imageFieldMode: 'file' | 'url';
    specsBehavior?: SpecsBehavior;
    showSpecsReference?: boolean;
}
