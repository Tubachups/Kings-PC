export type BuildProduct = {
    id: number;
    name: string;
    price: string | number;
};

export type AiBuildResponse = {
    build: BuildProduct[];
    total_price?: number | null;
    explanation?: string;
};