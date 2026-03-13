<script setup lang="ts">
import {usePage, Link} from '@inertiajs/vue3'
import Autoplay from 'embla-carousel-autoplay';
import {computed} from 'vue'
import { Card, CardContent } from '@/components/ui/card';
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious,
} from '@/components/ui/carousel';


interface Category {
    id: number;
    name: string;
    slug: string;
}


const page = usePage<{categories: Category[]}>();
const categories = computed(() => page.props.categories);

const images = [
  'https://d1q3zw97enxzq2.cloudfront.net/images/uATX.width-702.format-webp.webp',
  'https://www.nanotek.lk/storage/products/1915/0sIFk0MI5W2JSmrooRxpsCkERe6uoqn7p0qKIQvo.webp',
  'https://api.delta-computer.net/storage/products/$2y$12$3f88UFzFvV85Plnq12ObePLDZ2Zcsk3fjGs3HSHKfRigFlyZ22ea.webp',
  'https://varietyinfotech.com/wp-content/uploads/1TTB-600x600.webp',
  'https://d1q3zw97enxzq2.cloudfront.net/images/RM1000e_Series_2025_PSU_m_EN_AA_A.width-540.format-webp.webp',
  'https://netcodex.ph/wp-content/uploads/2025/07/GV-N506TAERO-OC-8GD-11-600x600.webp',
  'https://img.evetech.co.za/repository/ProductImages/antec-flux-pro-computer-case-2000px-v0009.webp?width=600',
  'https://sclgaming.in/wp-content/uploads/2024/12/Arctic-Freezer-36-120mm-CPU-Air-Cooler-Black-1-1.webp'
]


</script>

<template>
  <Carousel :opts="{ align: 'start', loop: true }" :plugins="[Autoplay({ delay: 2500 })]">
    <CarouselContent>
      <CarouselItem
        v-for="(category, idx) in categories"
        :key="category.id"
        class="basis-full sm:basis-1/2 md:basis-1/3 lg:basis-1/4 xl:basis-1/6 2xl:basis-1/7"
      >
        <Link :href="'/' + category.slug">
          <div class="p-1 sm:p-0">
            <Card>
              <CardContent class="relative p-0 h-40 md:h-56 overflow-hidden">
                <img
                  :src="images[idx]"
                  :alt="category.name"
                  class="w-full h-full object-contain transition-transform duration-300 hover:scale-105"
                />
                <div class="absolute bottom-2 left-2 bg-black/60 text-white px-2 py-1 rounded text-sm">
                  {{ category.name }}
                </div>
              </CardContent>
            </Card>
          </div>
        </Link>
      </CarouselItem>
    </CarouselContent>

    <CarouselPrevious class="left-2" />
    <CarouselNext class="right-2 md:right-1" />
  </Carousel>
</template>
