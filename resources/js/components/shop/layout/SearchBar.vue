<script setup lang="ts">
import {Delete} from "lucide-vue-next";

const model = defineModel<string>();

const handleClear = () => {
    model.value = '';
};
</script>

<template>
    <div class="flex w-full items-center gap-2 md:w-auto">
        <input
            type="text"
            class="w-full rounded-2xl border border-amber-50 p-2 outline-amber-50 md:w-96"
            placeholder="Search item..."
            v-model="model"
        />
        <button class="p-2" v-on:click="handleClear"><Delete /></button>
    </div>
</template>
