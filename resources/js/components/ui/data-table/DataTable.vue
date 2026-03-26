<script setup lang="ts" generic="TData, TValue">
import type {
    ColumnDef,
    SortingState,
} from '@tanstack/vue-table';
import {
    FlexRender,
    getCoreRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
} from '@/components/ui/pagination';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { valueUpdater } from '@/lib/utils';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

const props = defineProps<{
    columns: ColumnDef<TData, TValue>[];
    data: TData[];
    meta?: PaginationMeta;
    filters?: {
        name?: string;
        category?: string;
        low_stock?: boolean;
    };
}>();

const sorting = ref<SortingState>([]);
const nameFilter = ref<string>(props.filters?.name ?? '');
const categoryFilter = ref<string>(props.filters?.category ?? '');
const lowStockFilter = ref<boolean>(props.filters?.low_stock ?? false);

const table = useVueTable({
    get data() {
        return props.data;
    },
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    manualPagination: true,
    manualFiltering: true,
    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
    state: {
        get sorting() {
            return sorting.value;
        },
    },
});

function goToPage(page: number) {
    router.get(
        window.location.pathname,
        { ...Object.fromEntries(new URLSearchParams(window.location.search)), page },
        { preserveState: true, preserveScroll: true },
    );
}

let debounceTimer: ReturnType<typeof setTimeout>;

function applyFilters() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            window.location.pathname,
            {
                name: nameFilter.value || undefined,
                category: categoryFilter.value || undefined,
                low_stock: lowStockFilter.value || undefined,
                page: 1,
            },
            { preserveState: true, preserveScroll: true },
        );
    }, 200);
}

watch([nameFilter, categoryFilter], applyFilters);
</script>

<template>
    <div class="w-full">
        <div class="flex items-center gap-2 py-4">
            <Input
                class="max-w-sm"
                placeholder="Filter products..."
                v-model="nameFilter"
            />
            <Input
                class="max-w-sm"
                placeholder="Filter by category..."
                v-model="categoryFilter"
            />
        </div>
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                        >
                            <FlexRender
                                v-if="!header.isPlaceholder"
                                :render="header.column.columnDef.header"
                                :props="header.getContext()"
                            />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            :data-state="
                                row.getIsSelected() ? 'selected' : undefined
                            "
                        >
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                            >
                                <FlexRender
                                    :render="cell.column.columnDef.cell"
                                    :props="cell.getContext()"
                                />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell
                                :col-span="columns.length"
                                class="h-24 text-center"
                            >
                                No results.
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>

        <div v-if="meta" class="flex flex-col md:flex-row items-center justify-around gap-4 py-4">
            <p class="text-muted-foreground text-sm mb-2 md:mb-0">
                Showing <span class="font-medium">{{ meta.from }}</span>–<span class="font-medium">{{ meta.to }}</span> of <span class="font-medium">{{ meta.total }}</span> results
            </p>
            <div class="w-full md:w-auto">
                <Pagination
                    :total="meta.total"
                    :items-per-page="meta.per_page"
                    :default-page="meta.current_page"
                    :sibling-count="1"
                    show-edges
                    @update:page="goToPage"
                >
                    <PaginationContent v-slot="{ items }" >
                        <PaginationItem :value="0" class="mr-3">
                            <PaginationFirst />
                        </PaginationItem>
                        <template v-for="(item, index) in items" :key="index" >
                            <PaginationItem v-if="item.type === 'page'" :value="item.value" >
                                <Button
                                    variant="outline"
                                    size="icon"
                                    :class="[
                                        'rounded-md transition-colors duration-150',
                                        item.value === meta.current_page ? 'bg-primary text-primary-foreground dark:text-white hover:bg-primary/90 border-primary' : 'bg-white text-black dark:text-white hover:bg-muted border border-gray-200',
                                        'mx-1'
                                    ]"
                                >
                                    {{ item.value }}
                                </Button>
                            </PaginationItem>
                            <PaginationItem v-else :value="0" >
                                <PaginationEllipsis />
                            </PaginationItem>
                        </template>

                        <PaginationItem :value="meta.last_page"class="ml-3" >
                            <PaginationLast />
                        </PaginationItem>
                    </PaginationContent>
                </Pagination>
            </div>
        </div>
    </div>
</template>
