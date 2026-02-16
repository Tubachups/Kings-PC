<script setup>
import { Crown, Menu, MenuIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const dropDown = ref(false);

const showDropDown = () => {
    dropDown.value = !dropDown.value
};

</script>

<template>
    <div class="sticky top-0 px-6 md:px-16 h-16 flex flex-row items-center bg-gradient-to-b from-black via-black/60 to-black/0 via-50%">
        <span class="flex flex-row font-bold w-1/2 md:w-1/4">
            <Crown class="mx-2"/>
            KINGS PC
        </span>

        <div class="flex-row gap-6 ml-auto font-bold hidden md:flex">
            <span class="hover:text-blue-600 transition duration-300">Home</span>
            <span class="hover:text-blue-600 transition duration-300">Products</span>
            <span class="hover:text-blue-600 transition duration-300">About Us</span>
            <span class="hover:text-blue-600 transition duration-300">Contacts</span>
        </div>

        <div class="relative flex md:hidden ml-auto flex-col">
            <button @click="showDropDown" class="border border-white rounded p-1 hover:bg-white transition duration-300">
                <Menu class="h-6 w-6 text-white hover:text-black transition duration-300"/>
            </button>

            <div v-if="dropDown" class="absolute right-0 mt-10 w-40 bg-black border border-white rounded shadow-lg z-10 flex flex-col">
                <span class="hover:text-blue-600 transition duration-300 px-4 py-2">Home</span>
                <span class="hover:text-blue-600 transition duration-300 px-4 py-2">Products</span>
                <span class="hover:text-blue-600 transition duration-300 px-4 py-2">About Us</span>
                <span class="hover:text-blue-600 transition duration-300 px-4 py-2">Contacts</span>
            </div>
        </div>
    </div>
</template>
