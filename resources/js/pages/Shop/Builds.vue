<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel';
import Layout from '@/layouts/MainLayout.vue';
defineOptions({ layout: Layout });

const props = defineProps<{
    builds: Array<any>
}>();

const getBuildImages = (build: any) => {
    return [
        build.image_preview_1,
        build.image_preview_2,
        build.image_preview_3,
        build.image_preview_4,
    ].filter(Boolean);
};
</script>

<template>
<div class="p-24">
    <h2 class="mb-4 text-2xl font-bold">Builds</h2>
    <p>Here you can view and manage your PC builds.</p>
    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-4">
        <Card
            v-for="build in builds"
            :key="build.id"
            class="col-span-1 overflow-hidden p-4 md:col-span-2"
        >
            <Carousel v-if="getBuildImages(build).length" class="mb-3 w-full">
                <CarouselContent>
                    <CarouselItem
                        v-for="(image, idx) in getBuildImages(build)"
                        :key="`${build.id}-${idx}`"
                        class="basis-full"
                    >
                        <div class="relative h-72 w-full overflow-hidden rounded-md bg-muted">
                            <img
                                :src="image"
                                aria-hidden="true"
                                class="absolute inset-0 h-full w-full scale-110 object-cover blur-lg opacity-30"
                            />
                            <img
                                :src="image"
                                :alt="`Build ${build.id} image ${idx + 1}`"
                                class="relative z-10 h-full w-full object-contain"
                            />
                        </div>
                    </CarouselItem>
                </CarouselContent>
                <CarouselPrevious class="left-2" />
                <CarouselNext class="right-2" />
            </Carousel>
            <p class="line-clamp-3 text-sm text-muted-foreground">
                {{ build.text || 'No description available.' }}
            </p>
            <p class="mt-3 text-sm font-medium">Likes: {{ build.likes ?? 0 }}</p>
        </Card>
    </div>

</div>
</template>
