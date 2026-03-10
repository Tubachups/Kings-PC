<script setup lang="ts">
import {  router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ArrowBigUpDash } from 'lucide-vue-next';
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import InfiniteBuilds from '@/components/shop/builds/InfiniteBuilds.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import Slider from '@/components/ui/slider/Slider.vue';
import { SORT_OPTIONS } from '@/constants/constants';
import Layout from '@/layouts/MainLayout.vue';
import { builds as buildsRoute } from '@/routes';
import type { Build } from '@/types/build';
import { formatSliderPrice } from '@/utils/helpers';

defineOptions({ layout: Layout });

const PRICE_MIN = 0;
const PRICE_MAX = 200_000;
const PRICE_STEP = 1_000;
const showScrollTopButton = ref(false);

const props = defineProps<{
    builds: {data: Build[];};
    sort: string;
    minPrice: number | null;
    maxPrice: number | null;
}>();

const priceRange = ref<[number, number]>([
    props.minPrice ?? PRICE_MIN,
    props.maxPrice ?? PRICE_MAX,
]);


function resetPriceFilter(): void {
    priceRange.value = [PRICE_MIN, PRICE_MAX];
}

const navigateWithPrice = useDebounceFn((range: [number, number]) => {
    const params: Record<string, string | undefined> = {
        sort: props.sort !== 'newest' ? props.sort : undefined,
        min_price: range[0] > PRICE_MIN ? String(range[0]) : undefined,
        max_price: range[1] < PRICE_MAX ? String(range[1]) : undefined,
    };
    router.get(buildsRoute(), params, { preserveScroll: true });
}, 400);


function onSortChange(value: any): void {
    if (value) {
        const params: Record<string, string | undefined> = {
            sort: String(value),
            min_price: priceRange.value[0] > PRICE_MIN ? String(priceRange.value[0]) : undefined,
            max_price: priceRange.value[1] < PRICE_MAX ? String(priceRange.value[1]) : undefined,
        };
        router.get(buildsRoute(), params, { preserveScroll: false });
    }
}

function handleWindowScroll(): void {
    showScrollTopButton.value = window.scrollY > 500;
}

function scrollToTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

onMounted(() => {
    window.addEventListener('scroll', handleWindowScroll, { passive: true });
    handleWindowScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleWindowScroll);
});

const isPriceFiltered = computed(
    () => priceRange.value[0] > PRICE_MIN || priceRange.value[1] < PRICE_MAX,
);

watch(priceRange, (range) => navigateWithPrice(range));
</script>

<template>
    <div class="p-3 md:p-12 lg:p-24">
        <div class="mb-6 flex flex-col gap-3 md:flex-row  md:justify-between ">
            <div class=" flex flex-col md:w-1/2">
                <h2 class="text-2xl font-bold">Completed Builds</h2>
                <p class="mt-1 text-sm md:text-base">
                    Explore hundreds of custom rigs crafted by King's PC. Dive into detailed part lists and check out
                    the galleries.
                </p>
            </div>

            <div class="flex w-full flex-col lg:flex-row gap-3 md:w-1/2 items-center justify-end">
                <!-- Price Range Slider -->
                <div class="flex w-full flex-col gap-2 sm:w-72">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium">Price Range</span>
                        <button v-if="isPriceFiltered"
                            class="text-xs text-muted-foreground underline underline-offset-2 hover:text-foreground"
                            @click="resetPriceFilter">
                            Reset
                        </button>
                    </div>
                    <Slider v-model="priceRange" :min="PRICE_MIN" :max="PRICE_MAX" :step="PRICE_STEP" class="w-full" />
                    <div class="flex items-center justify-between text-xs text-muted-foreground">
                        <span>{{ formatSliderPrice(priceRange[0]) }}</span>
                        <span>{{ formatSliderPrice(priceRange[1]) }}</span>
                    </div>
                </div>

                <!-- Sort Select -->
                <Select :model-value="sort" @update:model-value="onSortChange">
                    <SelectTrigger class="w-full sm:w-56">
                        <SelectValue placeholder="Sort by…" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="option in SORT_OPTIONS" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <InfiniteBuilds :builds="builds" />

        <button
            v-show="showScrollTopButton"
            type="button"
            class="fixed bottom-17 right-6 z-50 rounded-full bg-primary px-2 py-1 text-sm  text-primary-foreground shadow-lg transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 cursor-pointer"
            @click="scrollToTop"
        >
            <ArrowBigUpDash />
        </button>


    </div>


</template>
