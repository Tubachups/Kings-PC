import { computed, ref, watch, type ComputedRef, type Ref } from 'vue';
import type { Product } from '@/types/product';

type MaybeRef<T> = Ref<T> | ComputedRef<T>;

interface UseProductListingOptions {
    products: MaybeRef<Product[]>;
    perPage?: number;
    extraResetTriggers?: MaybeRef<unknown>[];
    filterPredicate?: (product: Product, searchQuery: string) => boolean;
}

export function useProductListing(options: UseProductListingOptions) {
    const perPage = options.perPage ?? 16;

    const currentPage = ref<number>(1);
    const searchQuery = ref<string>('');
    const viewMode = ref<'grid' | 'list'>('grid');

    const defaultPredicate = (product: Product, query: string): boolean => {
        return product.name.toLowerCase().includes(query.toLowerCase());
    };

    const predicate = options.filterPredicate ?? defaultPredicate;

    const filteredProducts = computed<Product[]>(() => {
        const query = searchQuery.value;

        return options.products.value.filter((product) => predicate(product, query));
    });

    const paginatedProducts = computed<Product[]>(() => {
        const start = (currentPage.value - 1) * perPage;

        return filteredProducts.value.slice(start, start + perPage);
    });

    watch([searchQuery, ...(options.extraResetTriggers ?? [])], () => {
        currentPage.value = 1;
    }, { deep: true });

    const handlePageChange = (page: number): void => {
        currentPage.value = page;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    return {
        perPage,
        currentPage,
        searchQuery,
        viewMode,
        filteredProducts,
        paginatedProducts,
        handlePageChange,
    };
}
