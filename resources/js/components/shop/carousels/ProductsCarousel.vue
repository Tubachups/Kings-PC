<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Card, CardContent } from '@/components/ui/card';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel';

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const images = Array.from({ length: 20 }, (_, i) => ({
    id: i + 1,
    url: `https://picsum.photos/400/400?random=${i + 1}`,
}));
</script>

<template>
    <Carousel
        class="h-full w-full overflow-hidden"
        :opts="{
            align: 'start',
        }"
    >
        <CarouselContent class="w-full">
            <CarouselItem
                v-for="image in images"
                :key="image.id"
                :class="cn(props.class)"
            >
                <div class="p-1">
                    <Card>
                        <CardContent
                            class="flex aspect-square items-center justify-center p-0"
                        >
                            <img
                                :src="image.url"
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
