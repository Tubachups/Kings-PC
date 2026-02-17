<script setup lang="ts">
import Layout from '@/layouts/MainLayout.vue';

defineOptions({ layout: Layout });

</script>

<template>

    <div class="flex flex-row justify-center mx-12 p-6 mt-8">
        <div class="text-3xl font-bold w-1/2">
            Store Location
        </div>

        <div class="w-1/2">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.4456144253263!2d121.0799753!3d14.573665700000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c7df9a5178cd%3A0xaae67460a6454e84!2sKing&#39;s%20Pc!5e0!3m2!1sen!2sph!4v1771234911051!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
    
</template>
