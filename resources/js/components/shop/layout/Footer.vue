<script setup lang="ts">
import { library } from '@fortawesome/fontawesome-svg-core'
import {  faFacebookSquare } from '@fortawesome/free-brands-svg-icons';
import { faPhone, faEnvelope } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usefulLinks, pages } from '@/constants/constants';

library.add(faPhone, faEnvelope, faFacebookSquare);

const email = ref('');

</script>

<template>
    <footer
        class="border-t border-gray-200 bg-white px-6 py-12 dark:border-none dark:bg-neutral-950 md:px-12 lg:px-20"
    >
        <div class="mx-auto max-w-7xl">
            <div class="grid grid-cols-1 gap-12 md:grid-cols-2 lg:grid-cols-3 justify-between">
                <!-- Brand + Newsletter -->
                <div class="flex flex-col gap-6 lg:col-span-1">
                    <!-- Logo -->
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-black text-white"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"
                                />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-black dark:text-white"
                            >King's PC</span
                        >
                    </div>

                    <!-- Newsletter -->
                    <div>
                        <p class="mb-1 text-sm font-semibold text-black dark:text-white">
                            Subscribe to newsletter
                        </p>
                        <p class="mb-4 text-xs text-black dark:text-gray-400">
                            Have questions or need assistance? Get in touch with our team for personalised support.
                        </p>
                        <div class="flex items-center gap-2">
                            <input
                                v-model="email"
                                type="email"
                                placeholder="Your email..."
                                class="w-full rounded-full border border-gray-300 px-4 py-2 text-sm outline-none focus:border-gray-400 dark:border-gray-700 dark:bg-neutral-900 dark:text-gray-100 dark:placeholder:text-gray-500"
                            />
                            <button
                                aria-label="Subscribe to newsletter"
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-black text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Social Icons -->
                    <div class="text-gray-700 dark:text-gray-300">
                        <div class="flex">
                            <font-awesome-icon icon="fa-brands fa-facebook-square" size="lg"/>
                            <a href="https://www.facebook.com/profile.php?id=100063578996223" target="_blank" class="hover:text-black dark:hover:text-white"> King's PC</a>
                        </div>
                        <div>
                            <FontAwesomeIcon :icon="faPhone" />0943 404 1792
                        </div>
                        <div>
                            <FontAwesomeIcon :icon="faEnvelope" />kingedward0723@yahoo.com.ph
                        </div>
                    </div>
                </div>

                <!-- Useful Links -->
                <div>
                    <p class="mb-6 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Useful links
                    </p>
                    <ul class="space-y-3">
                        <li
                            v-for="(link, index) in usefulLinks"
                            :key="link.label"
                            class="flex items-baseline gap-4"
                        >
                            <span class="w-6 text-xs text-gray-700 dark:text-gray-300">{{
                                String(index + 1).padStart(2, '0')
                            }}</span>
                            <a
                                href="#"
                                class="text-xl font-light text-black transition-colors hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300"
                            >
                                {{ link.label }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Pages -->
                <div>
                    <p class="mb-6 text-sm font-medium text-gray-700 dark:text-gray-300">Pages</p>
                    <ul class="space-y-3">

                        <li
                            v-for="(page, index) in pages"
                            :key="page.label"
                            class="flex items-baseline gap-4"
                        >
                            <span class="w-6 text-xs text-gray-700 dark:text-gray-300">{{
                                String(index + 1).padStart(2, '0')
                            }}</span>
                            <Link
                                v-if="page.route"
                                :href="page.route.url"
                                class="text-xl font-light text-black transition-colors hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300"
                            >
                                {{ page.label }}
                            </Link>
                            <a
                                v-else
                                href="#"
                                class="text-xl font-light text-black transition-colors hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300"
                            >
                                {{ page.label }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div
                class="mt-12 flex flex-col items-center justify-end gap-2 border-t border-gray-100 pt-6 text-xs text-gray-700 dark:text-gray-300 sm:flex-row sm:gap-4"
            >
                <a href="#" class="transition-colors hover:text-black dark:hover:text-white"
                    >Terms &amp; condition</a
                >
                <a href="#" class="transition-colors hover:text-black dark:hover:text-white"
                    >Privacy policy</a
                >
                <a href="#" class="transition-colors hover:text-black dark:hover:text-white"
                    >Accessibility statement</a
                >
                <span>© King's PC 2026</span>
            </div>
        </div>
    </footer>
</template>
