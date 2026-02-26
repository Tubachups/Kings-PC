export type BuildProduct = {
    id: number;
    name: string;
    price: string | number;
    image_url: string;
    specs?: Record<string, any>;
};

export type AiBuildResponse = {
    build: BuildProduct[];
    total_price?: number | null;
    explanation?: string;
};