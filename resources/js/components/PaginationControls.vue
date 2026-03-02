<script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
} from '@/components/ui/pagination';

const props = defineProps<{
    totalItems: number;
    currentPage: number;
    itemsPerPage?: number;
}>();

const emit = defineEmits<{
    (event: 'update:page', page: number): void;
}>();

const perPage = computed(() => props.itemsPerPage ?? 15);
const totalPages = computed(() => Math.ceil(props.totalItems / perPage.value));
const fromItem = computed(() => (props.currentPage - 1) * perPage.value + 1);
const toItem = computed(() => Math.min(props.currentPage * perPage.value, props.totalItems));

const handlePageChange = (page: number): void => {
    emit('update:page', page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
    <div v-if="totalItems > perPage" class="mt-8 flex flex-col items-center gap-2">
        <p class="text-muted-foreground text-sm">
            Showing
            <span class="font-medium">{{ fromItem }}</span>-<span class="font-medium">{{ toItem }}</span>
            of <span class="font-medium">{{ totalItems }}</span> results
        </p>
        <Pagination :total="totalItems" :items-per-page="perPage" :page="currentPage" :sibling-count="1" show-edges
            @update:page="handlePageChange">
            <PaginationContent v-slot="{ items }">
                <PaginationItem :value="1" class="mr-3">
                    <PaginationFirst />
                </PaginationItem>
                <template v-for="(item, index) in items" :key="index">
                    <PaginationItem v-if="item.type === 'page'" :value="item.value">
                        <Button variant="outline" size="icon" :class="[
                            'mx-1 rounded-md transition-colors duration-150',
                            item.value === currentPage
                                ? 'bg-primary border-primary text-primary-foreground hover:bg-primary/90'
                                : 'border border-gray-200 bg-white text-black hover:bg-muted',
                        ]">
                            {{ item.value }}
                        </Button>
                    </PaginationItem>
                    <PaginationItem v-else :value="0">
                        <PaginationEllipsis />
                    </PaginationItem>
                </template>
                <PaginationItem :value="totalPages" class="ml-3">
                    <PaginationLast />
                </PaginationItem>
            </PaginationContent>
        </Pagination>
    </div>
</template>
