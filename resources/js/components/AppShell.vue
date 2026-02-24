<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { SidebarProvider } from '@/components/ui/sidebar';
import { Toaster } from '@/components/ui/sonner';
import type { AppShellVariant } from '@/types';

type Props = {
    variant?: AppShellVariant;
};

defineProps<Props>();

const isOpen = usePage().props.sidebarOpen;
</script>

<template>
    <Toaster position="top-center" rich-colors/>
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
        <slot />
    </div>
    <SidebarProvider v-else :default-open="isOpen">
        <slot />
    </SidebarProvider>
</template>
