<script setup lang="ts">
import { ArrowBigUpDash } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';

const showScrollTopButton = ref(false);

function scrollToTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleWindowScroll(): void {
    showScrollTopButton.value = window.scrollY > 500;
}

onMounted(() => {
    window.addEventListener('scroll', handleWindowScroll, { passive: true });
    handleWindowScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleWindowScroll);
});

</script>

<template>
    <button v-show="showScrollTopButton" type="button"
        class="fixed bottom-17 right-6 z-50 rounded-full bg-primary px-2 py-1 text-sm  text-primary-foreground shadow-lg transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 cursor-pointer"
        @click="scrollToTop">
        <ArrowBigUpDash />
    </button>
</template>
