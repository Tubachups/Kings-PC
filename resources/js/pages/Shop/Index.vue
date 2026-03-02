<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue';
import BannerCarousel from '@/components/shop/carousels/BannerCarousel.vue';
import ComponentsCarousel from '@/components/shop/carousels/ComponentsCarousel.vue';
import ReviewsCarousel from '@/components/shop/carousels/ReviewsCarousel.vue';
import Layout from '@/layouts/MainLayout.vue';
import { builds as buildsRoute } from '@/routes';
import type { Build } from '@/types/build';
import { formatPrice, extractPrice, extractBuildTitle } from '@/utils/helpers';

defineOptions({ layout: Layout });

defineProps<{
    topBuilds: Build[];
}>()


function firstImage(build: { image_preview_1?: string; image_preview_2?: string; image_preview_3?: string; image_preview_4?: string }): string {
    return (
        build.image_preview_1
        ?? build.image_preview_2
        ?? build.image_preview_3
        ?? build.image_preview_4
        ?? '/images/ai.png'
    );
}

</script>

<template>

    <div class="container mx-auto p-6 w-full flex justify-center">
        <BannerCarousel />
    </div>

    <div class="container mx-auto p-6 w-full flex flex-col justify-center">
        <div class="flex flex-row justify-between items-baseline mb-2">
            <p class="text-xl md:text-3xl font-bold underline">Components</p>
            <Link href="/components" class="text-sm md:text-lg">See more...</Link>
        </div>

        <ComponentsCarousel />
    </div>

    <div class="container mx-auto p-6 w-full flex flex-col justify-center">
        <div class="mb-6 text-center">
            <h1 class="text-xl md:text-3xl font-bold underline">Completed Builds</h1>
            <p class="mt-2 text-sm md:text-base text-muted-foreground">
                A showcase of our highest-priced completed builds, highlighting premium PC systems crafted for performance.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="build in topBuilds"
                :key="build.id"
                class="overflow-hidden rounded-xl border bg-card"
            >
                <img
                    :src="firstImage(build)"
                    :alt="`Build ${build.id}`"
                    class="h-75 w-full object-cover transition-transform duration-300 hover:scale-105"
                />
                <div class="space-y-2 p-4">
                    <p class="line-clamp-2 text-sm font-semibold">
                        {{ extractBuildTitle(build.text) }}
                    </p>
                    <p class="text-lg font-bold text-primary">
                        {{ formatPrice(extractPrice(build.text)) ?? 'Price unavailable' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-4 text-right">
            <Link :href="buildsRoute().url" class="text-sm md:text-lg underline">
                See all completed builds...
            </Link>
        </div>
    </div>

    <div class="container mx-auto p-6 w-full flex justify-center flex-col items-center">
        <div class="text-2xl md:text-3xl font-bold underline">Reviews</div>
        <div class="text-sm sm:text-lg mb-4">From our satisfied customers...</div>
        <ReviewsCarousel class="basis-1/4"/>
    </div>

</template>
