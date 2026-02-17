<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import Autoplay from 'embla-carousel-autoplay';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel';

const images = [
    {
        id: 1,
        imageUrl:
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
    },
    {
        id: 2,
        imageUrl:
            'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=800&q=80',
    },
    {
        id: 3,
        imageUrl:
            'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=800&q=80',
    },
    {
        id: 4,
        imageUrl:
            'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=800&q=80',
    },
    {
        id: 5,
        imageUrl: 'https://picsum.photos/800/300?random=5',
    },
];
</script>

<template>
    <Carousel
        class="w-full max-w-7xl"
        :plugins="[
            Autoplay({
                delay: 2000,
            }),
        ]"
    >
        <CarouselContent>
            <CarouselItem v-for="image in images" :key="id" class="basis-full">
                <div class="">
                    <Card>
                        <CardContent
                            class="flex h-[200px] items-center justify-center p-0 md:h-[300px]"
                        >
                            <img
                                :src="image.imageUrl"
                                class="h-full w-full object-cover"
                                style="margin: 0; padding: 0"
                            />
                        </CardContent>
                    </Card>
                </div>
            </CarouselItem>
        </CarouselContent>
        <CarouselPrevious />
        <CarouselNext />
    </Carousel>
</template>
