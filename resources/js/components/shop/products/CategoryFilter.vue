<script setup lang="ts">
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    categories: string[];
}>();

const isCategoryOpen = ref<boolean>(true);
const model = defineModel<string[]>();

</script>

<template>
    <aside class="w-40 hidden lg:block">
        <div class="overflow-hidden rounded-lg border border-border bg-card shadow-sm">
            <button @click="isCategoryOpen = !isCategoryOpen"
                class="flex w-full items-center justify-between bg-muted/60 p-4 transition-colors hover:bg-muted">
                <span class="font-semibold text-foreground">Category</span>
                <ChevronDown class="h-5 w-5 text-muted-foreground transition-transform duration-200"
                    :class="{ 'rotate-180': isCategoryOpen }" />
            </button>

            <div v-show="isCategoryOpen" class="space-y-3 border-t border-border p-4">
                <label v-for="category in categories" :key="category"
                    class="flex items-center space-x-3 cursor-pointer group">
                    <input type="checkbox" :value="category" v-model="model"
                        class="h-4 w-4 rounded border-input bg-background text-primary focus:ring-primary cursor-pointer" />
                    <span class="text-sm text-muted-foreground transition-colors group-hover:text-foreground">
                        {{ category }}
                    </span>
                </label>
            </div>
        </div>
    </aside>
</template>
