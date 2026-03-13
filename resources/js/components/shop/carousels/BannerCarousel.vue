<script setup lang="ts">
import Autoplay from 'embla-carousel-autoplay';
import { Card, CardContent } from '@/components/ui/card';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel';

// Upgraded data structure to include promotional text and links
const promotions = [
    {
        id: 1,
        title: 'Next-Gen Gaming PCs',
        subtitle: 'Level up your experience with the new RTX 40-Series builds.',
        ctaText: 'Shop Gaming',
        link: '/category/gaming-pcs',
        imageUrl: 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&w=1200&q=80', // Gaming setup
    },
    {
        id: 2,
        title: 'Ultimate Workstations',
        subtitle: 'Unleash your creativity with zero bottlenecks. Built for professionals.',
        ctaText: 'Build Yours',
        link: '/category/workstations',
        imageUrl: 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&w=1200&q=80', // Clean desk/PC
    },
    {
        id: 3,
        title: 'Spring Component Clearance',
        subtitle: 'Save up to 30% on select motherboards, RAM, and storage.',
        ctaText: 'View Deals',
        link: '/sale',
        imageUrl: 'https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=1200&q=80', // PC internals
    },
];
</script>

<template>
    <Carousel
        class="w-full max-w-7xl mx-auto"
        :opts="{ align: 'start', loop: true }"
        :plugins="[Autoplay({ delay: 5000 })]"
    >
        <CarouselContent>
            <CarouselItem v-for="promo in promotions" :key="promo.id" class="basis-full">
                <div class="p-1 sm:p-0">
                    <Card class="border-0 shadow-lg">
                        <CardContent class="relative flex h-64 md:h-96 w-full items-center p-0 overflow-hidden rounded-xl">

                            <img
                                :src="promo.imageUrl"
                                :alt="promo.title"
                                class="absolute inset-0 h-full w-full object-cover"
                            />

                            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/60 to-transparent"></div>

                            <div class="relative z-10 flex flex-col justify-center h-full px-8 md:px-16 text-left w-full md:w-2/3">
                                <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-3 tracking-tight drop-shadow-md">
                                    {{ promo.title }}
                                </h2>
                                <p class="text-base md:text-xl text-gray-200 mb-6 max-w-xl drop-shadow">
                                    {{ promo.subtitle }}
                                </p>
                                <div>
                                    <a :href="promo.link" class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg transition-colors duration-200 shadow-md">
                                        {{ promo.ctaText }}
                                    </a>
                                </div>
                            </div>

                        </CardContent>
                    </Card>
                </div>
            </CarouselItem>
        </CarouselContent>

        <CarouselPrevious class="left-4 bg-black/50 text-white hover:bg-black/80 border-0" />
        <CarouselNext class="right-4 bg-black/50 text-white hover:bg-black/80 border-0" />
    </Carousel>
</template>
