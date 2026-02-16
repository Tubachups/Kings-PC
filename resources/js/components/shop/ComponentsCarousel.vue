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
import {usePage} from '@inertiajs/vue3'
import {computed} from 'vue'

const page = usePage();
const categories = computed(() => page.props.categories);

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
                v-for="(category, idx) in categories"
                :key="category.id"
                :class="cn(props.class)"
            >
                <div class="p-1">
                    <Card>
                        <CardContent
                            class="flex aspect-square items-center justify-center p-0"
                        >
                            <div>{{category.name}}</div>
                            <img
                                :src="images[idx % images.length].url"
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
