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

const promotions = [
    {
        id: 1,
        title: 'Next-Gen Gaming PCs',
        subtitle: 'Level up your experience with the new RTX 40-Series builds.',
        ctaText: 'Shop Gaming',
        imageUrl: 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?fm=webp&fit=crop&w=1200&q=80',
    },
    {
        id: 2,
        title: 'Ultimate Workstations',
        subtitle: 'Unleash your creativity with zero bottlenecks. Built for professionals.',
        ctaText: 'Build Yours',
        imageUrl: 'https://images.unsplash.com/photo-1636489948870-058e57b5f7d9?q=80&w=1172&fm=webp&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    },
    {
        id: 3,
        title: 'Business & Office Towers',
        subtitle: 'Reliable, whisper-quiet system units engineered for enterprise productivity.',
        ctaText: 'Shop Office PCs',
        imageUrl: 'https://images.unsplash.com/photo-1630201129622-a8e8ef3f7245?q=80&w=1074&fm=webp&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    },
    {
        id: 4,
        title: 'Starter Gaming Rigs',
        subtitle: 'Enter the PC gaming world with our optimized, budget-friendly 1080p builds.',
        ctaText: 'Shop Starter PCs',
        imageUrl: 'https://images.unsplash.com/photo-1582731595896-f959c4fb1509?q=80&w=1086&fm=webp&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    },
    {
        id: 5,
        title: 'Compact Powerhouses',
        subtitle: 'Mini-ITX system units that maximize desk space without sacrificing performance.',
        ctaText: 'Shop Small Form Factor',
        imageUrl: 'https://images.unsplash.com/photo-1622013378145-e8d89cfbd990?q=80&w=1170&fm=webp&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    },
    {
        id: 6,
        title: 'Stream-Ready Desktops',
        subtitle: 'Multitask effortlessly with high-core-count units built for playing and streaming.',
        ctaText: 'Shop Streaming PCs',
        imageUrl: 'https://images.unsplash.com/photo-1600861194942-f883de0dfe96?fm=webp&fit=crop&w=1200&q=80',
    },
    {
        id: 7,
        title: 'Everyday Home Essentials',
        subtitle: 'Dependable and fast desktop units perfect for homework, browsing, and media.',
        ctaText: 'Shop Home PCs',
        imageUrl: 'https://images.unsplash.com/photo-1598550476439-6847785fcea6?q=80&w=1170&fm=webp&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    }
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
                    <Card class="border-0 shadow-lg bg-colorless">
                        <CardContent class="relative flex h-75 md:h-96 w-full items-center p-0 overflow-hidden rounded-xl">

                            <img
                                :src="promo.imageUrl"
                                :alt="promo.title"
                                class="absolute inset-0 h-full w-full object-cover"
                            />

                            <div class="absolute inset-0 bg-linear-to-r from-black/90 via-black/60 to-transparent"></div>

                            <div class="relative z-10 flex flex-col justify-center h-full px-8 md:px-16 text-left w-full md:w-2/3">
                                <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-3 tracking-tight drop-shadow-md">
                                    {{ promo.title }}
                                </h2>
                                <p class="text-base md:text-xl text-gray-200 mb-6 max-w-xl drop-shadow">
                                    {{ promo.subtitle }}
                                </p>
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
