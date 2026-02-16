<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { useAttrs } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
    class?: HTMLAttributes['class'];
    modelValue?: string;
}

const props =defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const attrs = useAttrs();
</script>

<template>
    <textarea
        :class="
            cn(
                'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                props.class
            )
        "
        :value="modelValue"
        @input="
            emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)
        "
        v-bind="attrs"
    />
</template>
