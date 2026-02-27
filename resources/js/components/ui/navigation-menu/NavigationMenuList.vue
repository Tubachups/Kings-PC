<script setup lang="ts">
import type { NavigationMenuListProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { reactiveOmit } from "@vueuse/core"
import { NavigationMenuList, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<NavigationMenuListProps & { class?: HTMLAttributes["class"] }>()

const delegatedProps = reactiveOmit(props, "class")

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <NavigationMenuList
    data-slot="navigation-menu-list"
    v-bind="forwardedProps"
    :class="
      cn(
        'group flex  list-none items-center justify-center lg:gap-4 md:gap-1 ',
        props.class,
      )
    "
  >
    <slot />
  </NavigationMenuList>
</template>
