<script setup lang="ts">
import type { Build, ParsedBuild, PartCategory } from '@/types/build';
import { formatDate, formatPrice } from '@/utils/helpers';

type BuildCard = Build & {
    parsed: ParsedBuild;
};

const categoryLabels: Record<PartCategory, string> = {
    cpu: 'CPU',
    cooler: 'Cooler',
    mobo: 'Motherboard',
    ram: 'Memory',
    storage: 'Storage',
    gpu: 'GPU',
    psu: 'Power Supply',
    case: 'Case',
    fans: 'Fans',
    extra: 'Extras',
};

defineProps<{
    build: BuildCard;
}>();
</script>

<template>
    <div class="grid h-full grid-rows-[auto_1fr_auto] gap-2">
        <template v-if="build.parsed.parts.length">
            <div class="flex items-start justify-between gap-2">
                <p class="text-sm font-semibold leading-snug">
                    {{ build.parsed.header || 'Anonymous Client' }}
                </p>
                <span
                    v-if="build.parsed.price"
                    class="shrink-0 rounded-full bg-primary/10 px-2 py-0.5 text-xs font-bold text-primary"
                >
                    {{ formatPrice(build.parsed.price) }}
                </span>
            </div>

            <ul class="mt-1 space-y-1">
                <li
                    v-for="part in build.parsed.parts"
                    :key="part.label"
                    class="flex items-start gap-1.5 text-xs text-muted-foreground"
                >
                    <span class="mt-px shrink-0 text-[11px] leading-none" :title="categoryLabels[part.cat]">&bull;</span>
                    <span class="leading-snug">{{ part.label }}</span>
                </li>
            </ul>

            <div class="my-2 mt-auto flex items-center justify-between border-t pt-2">
                <p class="text-xs font-medium">Likes: {{ build.likes ?? 0 }}</p>
                <p class="text-xs text-muted-foreground">
                    {{ formatDate(build.created_at) }}
                </p>
            </div>
        </template>
    </div>
</template>
