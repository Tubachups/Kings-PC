import { PART_CATEGORIES} from '@/constants/constants';

export interface Build {
    id: number;
    text: string;
    image_preview_1?: string;
    image_preview_2?: string;
    image_preview_3?: string;
    image_preview_4?: string;
    likes?: number;
    created_at: string;
}

export interface ParsedBuild {
    header: string;
    price: number | null;
    parts: { cat: PartCategory; label: string }[];
}

export type PartCategory = typeof PART_CATEGORIES[number]['cat'];


