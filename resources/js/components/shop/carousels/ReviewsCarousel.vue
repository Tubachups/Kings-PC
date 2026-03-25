<script setup lang="ts">
import { Quote } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import { index as reviewsIndex } from '@/actions/App/Http/Controllers/ReviewController'
import { Card, CardContent } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'
import { cn } from '@/lib/utils'

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
const isLoading = ref<boolean>(true)

async function fetchReviews(): Promise<void> {
    isLoading.value = true
    try {
        const response = await fetch(reviewsIndex().url)
        const data: Review[] = await response.json()

        const half = Math.ceil(data.length / 2)
        rowOne.value = data.slice(0, half)
        rowTwo.value = data.slice(half)
    } catch (error) {
        console.error("Failed to fetch reviews:", error)
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    fetchReviews()
})
</script>

<template>
    <div :class="cn('w-full overflow-hidden flex flex-col gap-4 py-4', props.class)">

        <template v-if="isLoading">
            <div v-for="row in 2" :key="`skeleton-row-${row}`" class="group flex flex-col md:flex-row gap-4 overflow-hidden mask-edges">
                <div class="flex flex-col md:flex-row shrink-0 gap-4">
                    <div v-for="i in 5" :key="`skel-${row}-${i}`" class="w-full md:w-auto md:min-w-75 md:max-w-75">
                        <Card class="h-full bg-gray-50 border-gray-200 dark:bg-neutral-950 dark:border-gray-800">
                            <CardContent class="flex flex-col gap-3 p-5 h-full">
                                <Skeleton class="w-6 h-6 rounded-md shrink-0 bg-gray-200 dark:bg-gray-800" />

                                <div class="space-y-2 mt-2">
                                    <Skeleton class="h-4 w-full bg-gray-200 dark:bg-gray-800" />
                                    <Skeleton class="h-4 w-[90%] bg-gray-200 dark:bg-gray-800" />
                                    <Skeleton class="h-4 w-[80%] bg-gray-200 dark:bg-gray-800" />
                                </div>

                                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-gray-100 dark:border-gray-800">
                                    <Skeleton class="w-8 h-8 rounded-full shrink-0 bg-gray-200 dark:bg-gray-800" />
                                    <Skeleton class="h-4 w-24 bg-gray-200 dark:bg-gray-800" />
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <div class="group flex flex-col md:flex-row gap-4 overflow-hidden mask-edges">
                <div
                    v-for="i in 2"
                    :key="`row1-loop-${i}`"
                    class="flex flex-col md:flex-row shrink-0 animate-marquee gap-4 group-hover:paused"
                    :aria-hidden="i === 2 ? 'true' : 'false'"
                >
                    <div v-for="review in rowOne" :key="`r1-${review.id}`" class="w-full md:w-auto md:min-w-75 md:max-w-75">
                        <Card class="h-full bg-gray-50 border-gray-200 dark:bg-neutral-950 dark:border-gray-800">
                            <CardContent class="flex flex-col gap-3 p-5 h-full">
                                <Quote class="text-gray-300 w-6 h-6 shrink-0  dark:text-white" />
                                <p class="text-sm text-gray-700 line-clamp-4 dark:text-white">{{ review.Feedback }}</p>
                                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-gray-100 dark:border-gray-800">
                                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 shrink-0">
                                        {{ review.Name.charAt(0) }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ review.Name }}</span>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>

            <div class="group flex flex-col md:flex-row gap-4 overflow-hidden mask-edges">
                <div
                    v-for="i in 2"
                    :key="`row2-loop-${i}`"
                    class="flex flex-col md:flex-row shrink-0 animate-marquee-reverse gap-4 group-hover:paused"
                    :aria-hidden="i === 2 ? 'true' : 'false'"
                >
                    <div v-for="review in rowTwo" :key="`r2-${review.id}`" class="w-full md:w-auto md:min-w-75 md:max-w-75">
                        <Card class="h-full bg-gray-50 border-gray-200 dark:bg-neutral-950 dark:border-gray-800">
                            <CardContent class="flex flex-col gap-3 p-5 h-full">
                                <Quote class="text-gray-300 dark:text-white w-6 h-6 shrink-0" />
                                <p class="text-sm text-gray-700 dark:text-white line-clamp-4">{{ review.Feedback }}</p>
                                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-gray-100 dark:border-gray-800">
                                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 shrink-0">
                                        {{ review.Name.charAt(0) }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ review.Name }}</span>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </template>

    </div>
</template>

<style scoped>
/* =========================================
   Mobile First (Vertical Scrolling)
   ========================================= */
.animate-marquee {
    animation: marquee-vertical-reverse 60s linear infinite;
}
.animate-marquee-reverse {
    animation: marquee-vertical-reverse 60s linear infinite;
}

.mask-edges {
    mask-image: linear-gradient(to bottom, transparent, black 10%, black 90%, transparent);
    height: 500px; /* A fixed height is required for vertical overflow masking */
}

@keyframes marquee-vertical {
    0% { transform: translateY(0%); }
    100% { transform: translateY(calc(-100% - 1rem)); }
}

@keyframes marquee-vertical-reverse {
    0% { transform: translateY(calc(-100% - 1rem)); }
    100% { transform: translateY(0%); }
}

/* =========================================
   Desktop (md breakpoint - Horizontal Scrolling)
   ========================================= */
@media (min-width: 768px) {
    .animate-marquee {
        animation: marquee-horizontal 60s linear infinite;
    }
    .animate-marquee-reverse {
        animation: marquee-horizontal-reverse 60s linear infinite;
    }

    .mask-edges {
        mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        height: auto; /* Remove the fixed height restriction */
    }

    @keyframes marquee-horizontal {
        0% { transform: translateX(0%); }
        100% { transform: translateX(calc(-100% - 1rem)); }
    }

    @keyframes marquee-horizontal-reverse {
        0% { transform: translateX(calc(-100% - 1rem)); }
        100% { transform: translateX(0%); }
    }
}

.paused {
    animation-play-state: paused;
}
</style>
