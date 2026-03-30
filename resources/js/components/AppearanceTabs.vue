<script setup lang="ts">
import { Coffee, Monitor, Moon, Palette, Sun, Waves } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

type AppearanceTab = {
    value: 'light' | 'dark' | 'coffee' | 'ocean' | 'rose' | 'system';
    Icon: typeof Sun;
    label: string;
    previewClass?: string;
};

const tabs: AppearanceTab[] = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'coffee', Icon: Coffee, label: 'Coffee' },
    { value: 'ocean', Icon: Waves, label: 'Ocean' },
    { value: 'rose', Icon: Palette, label: 'Rose' },
    { value: 'system', Icon: Monitor, label: 'System' },
];
</script>

<template>
    <div class="inline-flex flex-wrap gap-1 rounded-xl border border-border/70 bg-card/80 p-1.5 shadow-sm">
        <button v-for="{ value, Icon, label } in tabs" :key="value" @click="updateAppearance(value)" :class="[
            'flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm transition-all',
            appearance === value
                ? 'bg-background text-foreground shadow-xs ring-1 ring-border'
                : 'text-muted-foreground hover:bg-muted/70 hover:text-foreground',
        ]">
            <component :is="Icon" class="h-4 w-4" />
            <span>{{ label }}</span>
        </button>
    </div>
</template>
