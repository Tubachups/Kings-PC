<script setup lang="ts">
import { cn } from '@/lib/utils'
import { Card, CardContent } from '@/components/ui/card'
import { Star } from 'lucide-vue-next'
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel'

interface Props {
  class?: string
}

const props = defineProps<Props>()

</script>

<template>
  <Carousel
    class="w-full overflow-hidden h-full"
    :opts="{
      align: 'start',
    }"
  >
    <CarouselContent class="w-full">
      <CarouselItem v-for="(_, index) in 20" :key="index" :class="cn(props.class)">
        <div class="p-1">
          <Card>
            <CardContent class="flex flex-col aspect-video items-center justify-center p-6">
              <div>
                <div class="flex flex-row"><Star :fill="'#facc15'" class="text-yellow-400"/><Star :fill="'#facc15'" class="text-yellow-400"/><Star :fill="'#facc15'" class="text-yellow-400"/><Star :fill="'#facc15'" class="text-yellow-400"/><Star :fill="'#facc15'" class="text-yellow-400"/></div>
                <div class="font-bold text-1xl flex justify-center mb-2"> bangis </div>
                <div class="font-light text-md flex justify-center"> "Solit par" </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </CarouselItem>
    </CarouselContent>
    <CarouselPrevious />
    <CarouselNext />
  </Carousel>
</template>
