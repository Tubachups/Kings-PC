<script setup lang="ts">
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faPhone, faEnvelope } from '@fortawesome/free-solid-svg-icons';
import {  faFacebookSquare } from '@fortawesome/free-brands-svg-icons';
import { Link } from '@inertiajs/vue3';
import {
    builder as builderRoute,
    builds as buildsRoute,
    components as componentsRoute,
} from '@/routes';

library.add(faPhone, faEnvelope, faFacebookSquare);

const email = ref('');

const usefulLinks = [
    { label: 'Career' },
    { label: 'Help' },
    { label: 'Works' },
    { label: 'News' },
    { label: 'Partners' },
    { label: 'Community' },
    { label: 'Support' },
];

const pages = [
    { label: 'Builder', route: builderRoute() },
    { label: 'Products', route: componentsRoute() },
    { label: 'Completed Builds', route: buildsRoute() },
    { label: 'Testimonials' },
    { label: 'Blogs' },
];
</script>

<template>
    <footer
        class="border-t border-gray-200 bg-white px-6 py-12 md:px-12 lg:px-20"
    >
        <div class="mx-auto max-w-7xl">
            <div class="grid grid-cols-1 gap-12 md:grid-cols-2 lg:grid-cols-4">
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
                        <span class="text-xl font-bold text-black"
                            >King's PC</span
                        >
                    </div>

                    <!-- Newsletter -->
                    <div>
                        <p class="mb-1 text-sm font-semibold text-black">
                            Subscribe to newsletter
                        </p>
                        <p class="mb-4 text-xs text-gray-500">
                            Have questions or need assistance?
                            <a href="#" class="text-orange-500 hover:underline"
                                >Get in touch</a
                            >
                            with our team for personalised support.
                        </p>
                        <div class="flex items-center gap-2">
                            <input
                                v-model="email"
                                type="email"
                                placeholder="Your email..."
                                class="w-full rounded-full border border-gray-300 px-4 py-2 text-sm outline-none focus:border-gray-400"
                            />
                            <button
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-black text-white transition-colors hover:bg-gray-800"
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
                    <div>
                        <div class="flex">
                            <font-awesome-icon icon="fa-brands fa-facebook-square" size="lg"/>
                            <a href="https://www.facebook.com/profile.php?id=100063578996223" target="_blank"> King's PC</a>
                        </div>
                        <div>
                            <FontAwesomeIcon :icon="faPhone" />0943 404 1792
                        </div>
                        <div>
                            <FontAwesomeIcon :icon="faEnvelope" />kingedward0723@yahoo.com.ph
                        </div>
                    </div>
                </div>

                <!-- Robot image (hidden on mobile, visible on lg) -->
                <div class="hidden lg:flex lg:items-end lg:justify-center">
                    <img
                        src="/images/robot-footer.png"
                        alt="Robot"
                        class="h-64 w-auto object-contain"
                        onerror="this.style.display = 'none'"
                    />
                </div>

                <!-- Useful Links -->
                <div>
                    <p class="mb-6 text-sm font-medium text-gray-400">
                        Useful links
                    </p>
                    <ul class="space-y-3">
                        <li
                            v-for="(link, index) in usefulLinks"
                            :key="link.label"
                            class="flex items-baseline gap-4"
                        >
                            <span class="w-6 text-xs text-gray-400">{{
                                String(index + 1).padStart(2, '0')
                            }}</span>
                            <a
                                href="#"
                                class="text-xl font-light text-black transition-colors hover:text-gray-600"
                            >
                                {{ link.label }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Pages -->
                <div>
                    <p class="mb-6 text-sm font-medium text-gray-400">Pages</p>
                    <ul class="space-y-3">

                        <li
                            v-for="(page, index) in pages"
                            :key="page.label"
                            class="flex items-baseline gap-4"
                        >
                            <span class="w-6 text-xs text-gray-400">{{
                                String(index + 1).padStart(2, '0')
                            }}</span>
                            <Link
                                v-if="page.route"
                                :href="page.route.url"
                                class="text-xl font-light text-black transition-colors hover:text-gray-600"
                            >
                                {{ page.label }}
                            </Link>
                            <a
                                v-else
                                href="#"
                                class="text-xl font-light text-black transition-colors hover:text-gray-600"
                            >
                                {{ page.label }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div
                class="mt-12 flex flex-col items-center justify-end gap-2 border-t border-gray-100 pt-6 text-xs text-gray-400 sm:flex-row sm:gap-4"
            >
                <a href="#" class="transition-colors hover:text-black"
                    >Terms &amp; condition</a
                >
                <a href="#" class="transition-colors hover:text-black"
                    >Privacy policy</a
                >
                <a href="#" class="transition-colors hover:text-black"
                    >Accessibility statement</a
                >
                <span>© King's PC 2026</span>
            </div>
        </div>
    </footer>
</template>
