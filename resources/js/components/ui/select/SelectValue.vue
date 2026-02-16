<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue';
import {
    SelectValue,
    type SelectValueProps,
    useForwardProps,
} from 'radix-vue';
import { cn } from '@/lib/utils';

const props = defineProps<
    SelectValueProps & { class?: HTMLAttributes['class'] }
>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <SelectValue
        v-bind="forwardedProps"
        :class="cn('text-sm', props.class)"
    >
        <slot />
    </SelectValue>
</template>
