<script setup lang="ts">
import { InfiniteScroll } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Card from '@/components/ui/card/Card.vue';

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { formatDate, formatPrice } from '@/utils/helpers';
import { PART_CATEGORIES } from '@/constants/constants';
import ImageCarousel from './ImageCarousel.vue';
import type { Build, ParsedBuild, PartCategory } from '@/types/build';

const props = defineProps<{
    builds: {data: Build[]};
}>();

// ─── Lightbox State ───────────────────────────────────────────────────────────

const lightboxOpen = ref(false);
const lightboxImage = ref<string | null>(null);

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

const DISPLAY_ORDER: PartCategory[] = ['cpu', 'cooler', 'mobo', 'ram', 'storage', 'gpu', 'psu', 'case', 'fans', 'extra'];


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

</script>


<template>
    <InfiniteScroll data="builds" :buffer="300" manual preserve-url>
        <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 lg:gap-6">
            <Card v-for="build in enrichedBuilds" :key="build.id" class="overflow-hidden p-3">
                <!-- Img -->
                <ImageCarousel v-if="build.images.length" :build="build" />

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

    <!-- Lightbox Dialog -->
    <Dialog v-model:open="lightboxOpen">
        <DialogContent class="max-w-4xl border-0 bg-black/90 p-2 shadow-2xl">
            <DialogTitle class="sr-only">Build image preview</DialogTitle>
            <DialogDescription class="sr-only">
                Enlarged preview of the selected build image.
            </DialogDescription>
            <img v-if="lightboxImage" :src="lightboxImage" alt="Enlarged build image"
                class="max-h-[85vh] w-full object-contain" />
        </DialogContent>
    </Dialog>
</template>
