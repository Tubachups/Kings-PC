<script setup lang="ts">
import { ref } from 'vue';
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
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
import type { Build } from '@/types/build';

const props = defineProps<{
    build: Build & { images: string[] };
}>();

const lightboxOpen = ref(false);
const lightboxImage = ref<string | null>(null);
const loadedImages = ref<Record<string, boolean>>({});


function openLightbox(image: string): void {
    lightboxImage.value = image;
    lightboxOpen.value = true;
}


function getImageKey(buildId: number | string, index: number): string {
    return `${buildId}-${index}`;
}

function isImageLoaded(buildId: number | string, index: number): boolean {
    return Boolean(loadedImages.value[getImageKey(buildId, index)]);
}

function markImageLoaded(buildId: number | string, index: number): void {
    loadedImages.value[getImageKey(buildId, index)] = true;
}
</script>

<template>
    <!-- Image Carousel -->
    <Carousel v-if="props.build.images.length" class="w-full" :opts="{ loop: true }">
        <CarouselContent>
            <CarouselItem v-for="(image, idx) in props.build.images" :key="`${props.build.id}-${idx}`" class="basis-full">
                <div class="relative h-56 md:h-72 w-full overflow-hidden rounded-md bg-muted cursor-zoom-in"
                    @click="openLightbox(image)">
                    <Skeleton v-if="!isImageLoaded(props.build.id, idx)" class="absolute inset-0 h-full w-full rounded-md" />
                    <img :src="image" aria-hidden="true" loading="lazy" decoding="async"
                        class="absolute inset-0 h-full w-full scale-110 object-cover blur-lg transition-opacity duration-300"
                        :class="isImageLoaded(props.build.id, idx) ? 'opacity-30' : 'opacity-0'" />
                    <img :src="image" :alt="`Build ${props.build.id} image ${idx + 1}`" loading="lazy" decoding="async"
                        class="relative z-10 h-full w-full object-contain transition-opacity duration-300"
                        :class="isImageLoaded(props.build.id, idx) ? 'opacity-100' : 'opacity-0'"
                        @load="markImageLoaded(props.build.id, idx)" @error="markImageLoaded(props.build.id, idx)" />
                </div>
            </CarouselItem>
        </CarouselContent>
        <CarouselPrevious class="left-2 sm:flex" />
        <CarouselNext class="right-2 sm:flex" />
    </Carousel>

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
