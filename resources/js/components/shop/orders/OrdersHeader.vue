<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';

defineProps<{
    title: string;
    description: string;
}>();

</script>
<template>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">
                {{ title }}
            </h1>
            <p class="text-muted-foreground">
                {{ description }}
            </p>
        </div>
        <Link :href="dashboard().url">
        <Button variant="outline">Back to Dashboard</Button>
        </Link>
    </div>
</template>
