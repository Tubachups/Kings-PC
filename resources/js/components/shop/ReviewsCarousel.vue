<script setup lang="ts">
import { cn } from '@/lib/utils'
import { Card, CardContent } from '@/components/ui/card'
import { Quote } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import { index as reviewsIndex } from '@/actions/App/Http/Controllers/ReviewController'

interface Props {
    class?: string
}

interface Review {
    id: number
    Name: string
    Feedback: string
}

const props = defineProps<Props>()

const rowOne = ref<Review[]>([])
const rowTwo = ref<Review[]>([])

async function fetchReviews(): Promise<void> {
    // Using Wayfinder's generated action to get the URL
    const response = await fetch(reviewsIndex().url)
    const data: Review[] = await response.json()

    // Split data into two rows
    const half = Math.ceil(data.length / 2)
    rowOne.value = data.slice(0, half)
    rowTwo.value = data.slice(half)
}

onMounted(() => {
    fetchReviews()
})
</script>

<template>
    <div :class="cn('w-full overflow-hidden flex flex-col gap-4 py-4', props.class)">

        <div class="group flex gap-4 overflow-hidden mask-edges">
            <div
                v-for="i in 2"
                :key="`row1-loop-${i}`"
                class="flex shrink-0 animate-marquee gap-4 group-hover:[animation-play-state:paused]"
                :aria-hidden="i === 2 ? 'true' : 'false'"
            >
                <div v-for="review in rowOne" :key="`r1-${review.id}`" class="min-w-[300px] max-w-[300px]">
                    <Card class="h-full bg-gray-50 border-gray-200">
                        <CardContent class="flex flex-col gap-3 p-5 h-full">
                            <Quote class="text-gray-300 w-6 h-6 shrink-0" />
                            <p class="text-sm text-gray-700 line-clamp-3">{{ review.Feedback }}</p>
                            <div class="flex items-center gap-2 mt-auto pt-4 border-t border-gray-100">
                                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 shrink-0">
                                    {{ review.Name.charAt(0) }}
                                </div>
                                <span class="text-sm font-semibold text-gray-800 truncate">{{ review.Name }}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <div class="group flex gap-4 overflow-hidden mask-edges">
            <div
                v-for="i in 2"
                :key="`row2-loop-${i}`"
                class="flex shrink-0 animate-marquee-reverse gap-4 group-hover:[animation-play-state:paused]"
                :aria-hidden="i === 2 ? 'true' : 'false'"
            >
                <div v-for="review in rowTwo" :key="`r2-${review.id}`" class="min-w-[300px] max-w-[300px]">
                    <Card class="h-full bg-gray-50 border-gray-200">
                        <CardContent class="flex flex-col gap-3 p-5 h-full">
                            <Quote class="text-gray-300 w-6 h-6 shrink-0" />
                            <p class="text-sm text-gray-700 line-clamp-3">{{ review.Feedback }}</p>
                            <div class="flex items-center gap-2 mt-auto pt-4 border-t border-gray-100">
                                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 shrink-0">
                                    {{ review.Name.charAt(0) }}
                                </div>
                                <span class="text-sm font-semibold text-gray-800 truncate">{{ review.Name }}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* Smooth infinite scrolling */
.animate-marquee {
    animation: marquee 60s linear infinite;
}
.animate-marquee-reverse {
    animation: marquee-reverse 60s linear infinite;
}

/* calc(-100% - 1rem) accounts for the content width + gap-4 (1rem) */
@keyframes marquee {
    0% { transform: translateX(0%); }
    100% { transform: translateX(calc(-100% - 1rem)); }
}

@keyframes marquee-reverse {
    0% { transform: translateX(calc(-100% - 1rem)); }
    100% { transform: translateX(0%); }
}

/* Fades the left and right edges for a polished look */
.mask-edges {
    mask-image: linear-gradient(
        to right,
        transparent,
        black 10%,
        black 90%,
        transparent
    );
}
</style>
