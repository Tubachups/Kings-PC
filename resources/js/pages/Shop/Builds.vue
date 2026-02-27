<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel';
import {
    Dialog,
    DialogContent,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import Slider from '@/components/ui/slider/Slider.vue';
import Layout from '@/layouts/MainLayout.vue';
import { InfiniteScroll, router } from '@inertiajs/vue3';
import { builds as buildsRoute } from '@/routes';
import { useDebounceFn } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import type { Build, ParsedBuild, PartCategory } from '@/types/build';
import { PART_CATEGORIES, SORT_OPTIONS } from '@/constants/constants';
import { formatDate, formatPrice } from '@/utils/helpers';

defineOptions({ layout: Layout });

const PRICE_MIN = 0;
const PRICE_MAX = 200_000;
const PRICE_STEP = 1_000;

const props = defineProps<{
    builds: { data: Build[] };
    sort: string;
    minPrice: number | null;
    maxPrice: number | null;
}>();

const priceRange = ref<[number, number]>([
    props.minPrice ?? PRICE_MIN,
    props.maxPrice ?? PRICE_MAX,
]);

const isPriceFiltered = computed(
    () => priceRange.value[0] > PRICE_MIN || priceRange.value[1] < PRICE_MAX,
);

function formatSliderPrice(value: number): string {
    if (value >= 1_000) {
        return `₱${(value / 1_000).toFixed(0)}k`;
    }
    return `₱${value}`;
}

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

watch(priceRange, (range) => navigateWithPrice(range));

// ─── Lightbox State ───────────────────────────────────────────────────────────

const lightboxOpen = ref(false);
const lightboxImage = ref<string | null>(null);

function openLightbox(image: string): void {
    lightboxImage.value = image;
    lightboxOpen.value = true;
}


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

const DISPLAY_ORDER: PartCategory[] = ['cpu', 'cooler', 'mobo', 'ram', 'storage', 'gpu', 'psu', 'case', 'fans', 'extra'];

const CATEGORY_LABELS: Record<PartCategory, string> = {
    cpu: 'CPU',
    cooler: 'Cooler',
    mobo: 'Motherboard',
    ram: 'Memory',
    storage: 'Storage',
    gpu: 'GPU',
    psu: 'Power Supply',
    case: 'Case',
    fans: 'Fans',
    extra: 'Extras',
};

function categorizePart(label: string): PartCategory {
    const lower = label.toLowerCase();
    for (const { cat, keywords } of PART_CATEGORIES) {
        if (keywords.some(kw => lower.includes(kw.toLowerCase()))) {
            return cat;
        }
    }
    return 'extra';
}

function splitBuildLines(text: string): { headerLines: string[]; bulletLines: string[] } {
    const lines = text.replace(/\\r\\n/g, '\n').replace(/\r\n/g, '\n').split('\n');
    const headerLines: string[] = [];
    const bulletLines: string[] = [];
    let inBullets = false;

    for (const line of lines) {
        if (!inBullets && line.trim().startsWith('*')) inBullets = true;
        if (inBullets) {
            if (line.trim()) bulletLines.push(line.trim());
        } else {
            headerLines.push(line);
        }
    }

    return { headerLines, bulletLines };
}

function extractPrice(text: string): number | null {
    const match = text.match(/[Uu]nit\s+price[:\s]+([0-9,\.]+\s*k?)/);
    if (!match) return null;

    const raw = match[1].replace(/,/g, '').trim();
    return raw.toLowerCase().endsWith('k')
        ? parseFloat(raw) * 1000
        : parseFloat(raw);
}

function parseBuildText(text: string): ParsedBuild {
    if (!text) return { header: '', price: null, parts: [] };

    const { headerLines, bulletLines } = splitBuildLines(text);

    const header = headerLines.find(l => l.trim().startsWith('Client'))?.trim() ?? '';
    const price = extractPrice(text);
    const parts = bulletLines
        .map(line => {
            const label = line.replace(/^\*\s*/, '').trim();
            return { cat: categorizePart(label), label };
        })
        .sort((a, b) => DISPLAY_ORDER.indexOf(a.cat) - DISPLAY_ORDER.indexOf(b.cat));

    return { header, price, parts };
}


const enrichedBuilds = computed(() =>
    props.builds.data.map(build => ({
        ...build,
        images: [
            build.image_preview_1,
            build.image_preview_2,
            build.image_preview_3,
            build.image_preview_4,
        ].filter(Boolean) as string[],
        parsed: parseBuildText(build.text),
    }))
);
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

        <InfiniteScroll data="builds" :buffer="300" manual preserve-url>
            <div class="grid gap-3 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 lg:gap-6">
                <Card v-for="build in enrichedBuilds" :key="build.id" class="overflow-hidden p-3">
                    <!-- Image Carousel -->
                    <Carousel v-if="build.images.length" class="w-full" :opts="{ loop: true }">
                        <CarouselContent>
                            <CarouselItem v-for="(image, idx) in build.images" :key="`${build.id}-${idx}`"
                                class="basis-full">
                                <div class="relative h-56 md:h-72 w-full overflow-hidden rounded-md bg-muted cursor-zoom-in"
                                    @click="openLightbox(image)">
                                    <img :src="image" aria-hidden="true"
                                        class="absolute inset-0 h-full w-full scale-110 object-cover blur-lg opacity-30" />
                                    <img :src="image" :alt="`Build ${build.id} image ${idx + 1}`"
                                        class="relative z-10 h-full w-full object-contain" />
                                </div>
                            </CarouselItem>
                        </CarouselContent>
                        <CarouselPrevious class="left-2 sm:flex" />
                        <CarouselNext class="right-2 sm:flex" />
                    </Carousel>

                    <!-- Build Info -->
                    <div class="grid h-full grid-rows-[auto_1fr_auto] gap-2">
                        <template v-if="build.parsed.parts.length">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold leading-snug">
                                    {{ build.parsed.header || 'Anonymous Client' }}
                                </p>
                                <span v-if="build.parsed.price"
                                    class="shrink-0 rounded-full bg-primary/10 px-2 py-0.5 text-xs font-bold text-primary">
                                    {{ formatPrice(build.parsed.price) }}
                                </span>
                            </div>

                            <ul class="mt-1 space-y-1">
                                <li v-for="part in build.parsed.parts" :key="part.label"
                                    class="flex items-start gap-1.5 text-xs text-muted-foreground">
                                    <span class="mt-px shrink-0 text-[11px] leading-none"
                                        :title="CATEGORY_LABELS[part.cat]">•</span>
                                    <span class="leading-snug">{{ part.label }}</span>
                                </li>
                            </ul>

                            <div class="my-2 flex items-center justify-between border-t pt-2 mt-auto">
                                <p class="text-xs font-medium">Likes: {{ build.likes ?? 0 }}</p>
                                <p class="text-xs text-muted-foreground">{{ formatDate(build.created_at) }}</p>
                            </div>
                        </template>
                    </div>  
                </Card>
            </div>

            <template #next="{ loading, fetch, hasMore }">
                <button v-if="hasMore" @click="fetch" :disabled="loading"
                    class="relative mt-6 mx-auto flex items-center justify-center rounded-lg bg-primary px-6 py-2 font-semibold text-white shadow-lg transition-all duration-200 hover:bg-primary/90 active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <span v-if="loading" class="absolute left-4 flex items-center">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                    <span :class="loading ? 'opacity-70 ml-4' : ''">
                        {{ loading ? 'Loading...' : 'Load more' }}
                    </span>
                </button>
            </template>
        </InfiniteScroll>
    </div>

    <!-- Lightbox Dialog -->
    <Dialog v-model:open="lightboxOpen">
        <DialogContent class="max-w-4xl border-0 bg-black/90 p-2 shadow-2xl">
            <img v-if="lightboxImage" :src="lightboxImage" alt="Enlarged build image"
                class="max-h-[85vh] w-full object-contain" />
        </DialogContent>
    </Dialog>
</template>
