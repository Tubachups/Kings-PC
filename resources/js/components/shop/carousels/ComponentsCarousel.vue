<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Card, CardContent } from '@/components/ui/card';
import Autoplay from 'embla-carousel-autoplay';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel';
import {usePage} from '@inertiajs/vue3'
import {computed} from 'vue'
import { Link } from '@inertiajs/vue3'

interface Props {
    class?: string;
}

interface Category {
    id: number;
    name: string;
    slug: string;
}


const page = usePage<{categories: Category[]}>();
const categories = computed(() => page.props.categories);

const props = defineProps<Props>();
const images = [
  'https://cdn.britannica.com/64/170564-050-7A0A86A2/motherboard-for-computer.jpg',
  'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTnIjYBVGPZG4cPXqcexrS18prvUg45Dl2hgw&s',
  'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRy52pK2zoSp7eiN7356R43LVEhB80UVRNJLw&s',
  'https://securis.com/wp-content/uploads/2020/03/hard-drive.jpg',
  'https://www.minitool.com/images/uploads/2020/08/power-supply-unit-1.jpg',
  'https://www.hellotech.com/blog/wp-content/uploads/2020/02/what-is-a-gpu.jpg',
  'https://cdn.mos.cms.futurecdn.net/67tVEqtigu4aNw5ZvrPQMa.jpg',
  'https://cdn.mos.cms.futurecdn.net/9fdc0e6c7e4e1e175a8b986dc221e1cb.png'
]

</script>

<template>

  <Carousel  :opts="{ align: 'start', loop: true }" :plugins="[Autoplay({ delay: 2500 })]">
    <CarouselContent class="">
      <CarouselItem
        v-for="(category, idx) in categories"
        :key="category.id"
        class=" sm:basis-1/2 md:basis-1/3 lg:basis-1/4 xl:basis-1/6 2xl:basis-1/8"
      >
      <Link :href="'/' + category.slug">
          <div class="p-1 sm:p-0">
            <Card>
              <CardContent class="relative p-0 h-40 md:h-56 overflow-hidden">
                <img
                  :src="images[idx]"
                  :alt="category.name"
                  class=" object-cover w-full h-full transition-transform duration-300 hover:scale-105"
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

    <CarouselPrevious class="left-2 " />
    <CarouselNext class="right-2 md:right-1" />
  </Carousel>

</template>
