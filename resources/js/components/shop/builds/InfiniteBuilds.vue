<script setup lang="ts">
import { InfiniteScroll } from '@inertiajs/vue3';
import { computed } from 'vue';
import Card from '@/components/ui/card/Card.vue';
import { PART_CATEGORIES } from '@/constants/constants';
import ImageCarousel from './ImageCarousel.vue';
import InfoBuilds from './InfoBuilds.vue';
import type { Build, ParsedBuild, PartCategory } from '@/types/build';

const props = defineProps<{
    builds: { data: Build[] };
}>();

const DISPLAY_ORDER: PartCategory[] = ['cpu', 'cooler', 'mobo', 'ram', 'storage', 'gpu', 'psu', 'case', 'fans', 'extra'];

const enrichedBuilds = computed(() =>
    props.builds.data.map((build) => ({
        ...build,
        images: [build.image_preview_1, build.image_preview_2, build.image_preview_3, build.image_preview_4].filter(Boolean) as string[],
        parsed: parseBuildText(build.text),
    })),
);

function categorizePart(label: string): PartCategory {
    const lower = label.toLowerCase();

    for (const { cat, keywords } of PART_CATEGORIES) {
        if (keywords.some((keyword) => lower.includes(keyword.toLowerCase()))) {
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
        if (!inBullets && line.trim().startsWith('*')) {
            inBullets = true;
        }

        if (inBullets) {
            if (line.trim()) {
                bulletLines.push(line.trim());
            }
        } else {
            headerLines.push(line);
        }
    }

    return { headerLines, bulletLines };
}

function extractPrice(text: string): number | null {
    const match = text.match(/[Uu]nit\s+price[:\s]+([0-9,.]+\s*k?)/);

    if (!match) {
        return null;
    }

    const raw = match[1].replace(/,/g, '').trim();

    return raw.toLowerCase().endsWith('k') ? parseFloat(raw) * 1000 : parseFloat(raw);
}

function parseBuildText(text: string): ParsedBuild {
    if (!text) {
        return { header: '', price: null, parts: [] };
    }

    const { headerLines, bulletLines } = splitBuildLines(text);
    const header = headerLines.find((line) => line.trim().startsWith('Client'))?.trim() ?? '';
    const price = extractPrice(text);
    const parts = bulletLines
        .map((line) => {
            const label = line.replace(/^\*\s*/, '').trim();
            return { cat: categorizePart(label), label };
        })
        .sort((a, b) => DISPLAY_ORDER.indexOf(a.cat) - DISPLAY_ORDER.indexOf(b.cat));

    return { header, price, parts };
}
</script>

<template>
    <InfiniteScroll data="builds" :buffer="300" manual preserve-url>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6 2xl:grid-cols-4">
            <Card v-for="build in enrichedBuilds" :key="build.id" class="overflow-hidden p-3">

                <ImageCarousel v-if="build.images.length" :build="build" />
                <InfoBuilds :build="build" />

            </Card>
        </div>

        <template #next="{ loading, fetch, hasMore }">
            <button
                v-if="hasMore"
                :disabled="loading"
                class="relative mx-auto mt-6 flex items-center justify-center rounded-lg bg-primary px-6 py-2 font-semibold text-white shadow-lg transition-all duration-200 hover:bg-primary/90 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60 focus:outline-none focus:ring-2 focus:ring-primary/50"
                @click="fetch"
            >
                <span v-if="loading" class="absolute left-4 flex items-center">
                    <svg class="h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                    </svg>
                </span>
                <span :class="loading ? 'ml-4 opacity-70' : ''">
                    {{ loading ? 'Loading...' : 'Load more' }}
                </span>
            </button>
        </template>
    </InfiniteScroll>
</template>
