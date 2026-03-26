<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Archive, Plus } from 'lucide-vue-next';
import {
    archived as productsArchived,
    create as productsCreate,
} from '@/actions/App/Http/Controllers/Admin/ProductController';
import { Button } from '@/components/ui/button';

defineProps<{
    selectedProducts: Set<number>;
    isLowStockFilterActive: boolean;
}>();

defineEmits<{
    'toggle-low-stock-filter': [];
    'bulk-update-status': [isActive: boolean];
    'bulk-archive': [];
}>();
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <div v-if="selectedProducts.size > 0" class="flex flex-wrap items-center gap-2">
            <Button
                @click="$emit('bulk-update-status', true)"
                variant="outline"
                size="sm"
                class="cursor-pointer"
            >
                Activate ({{ selectedProducts.size }})
            </Button>
            <Button
                @click="$emit('bulk-update-status', false)"
                variant="outline"
                size="sm"
                class="cursor-pointer"
            >
                Inactivate ({{ selectedProducts.size }})
            </Button>
            <Button @click="$emit('bulk-archive')" variant="destructive" class="cursor-pointer">
                <Archive class="mr-2 h-4 w-4" />
                Archive ({{ selectedProducts.size }})
            </Button>
        </div>

        <Button
            :variant="isLowStockFilterActive ? 'default' : 'outline'"
            @click="$emit('toggle-low-stock-filter')"
            class="cursor-pointer"
        >
            {{ isLowStockFilterActive ? 'Showing Low Stock' : 'Low Stock Below 10' }}
        </Button>

        <Link :href="productsArchived().url">
            <Button variant="outline" class="cursor-pointer">
                <Archive class="mr-2 h-4 w-4" />
                Archived
            </Button>
        </Link>

        <Link :href="productsCreate().url">
            <Button class="cursor-pointer">
                <Plus class="mr-2 h-4 w-4" />
                Add Product
            </Button>
        </Link>
    </div>
</template>
