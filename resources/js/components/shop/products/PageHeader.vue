<script setup lang="ts">
import { LayoutGrid, List } from 'lucide-vue-next';
import SearchBar from '@/components/shop/layout/SearchBar.vue';
import { Button } from '@/components/ui/button';

defineProps<{
    title: string;
}>();

const searchQuery = defineModel<string>('search-query');
const viewMode = defineModel<'grid' | 'list'>('view-mode');
</script>


<template>
    <div class="mb-8 flex flex-col items-center justify-between gap-4 md:flex-row">
        <h1 class="text-2xl font-bold md:mb-0 md:text-3xl">{{ title }}</h1>
        <div class="flex w-full flex-col items-stretch gap-3 md:w-auto md:flex-row md:items-center">
            <SearchBar v-model="searchQuery" />
            <div class="hidden lg:flex items-center gap-2 self-end md:self-auto ">
                <Button :variant="viewMode === 'grid' ? 'default' : 'outline'" size="icon" class="cursor-pointer"
                    @click="viewMode = 'grid'" aria-label="Grid view">
                    <LayoutGrid class="h-4 w-4" />
                </Button>
                <Button :variant="viewMode === 'list' ? 'default' : 'outline'" size="icon" class="cursor-pointer"
                    @click="viewMode = 'list'" aria-label="List view">
                    <List class="h-4 w-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
