<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Layout from '@/layouts/MainLayout.vue'

defineOptions({ layout: Layout })
</script>

<template>
    <Head title="Accessibility Statement">
        <meta
            head-key="description"
            name="description"
            content="Accessibility statement for King's PC website and customer support channels."
        />
    </Head>

    <div class="bg-gray-50 py-10 dark:bg-neutral-950">
        <div class="mx-auto max-w-4xl space-y-8 px-4 md:px-6">
            <header class="space-y-3">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400">
                    Legal
                </p>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 md:text-4xl">Accessibility Statement</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Last updated: March 13, 2026
                </p>
            </header>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-neutral-900">
                <p class="text-sm leading-7 text-gray-700 dark:text-gray-300">
                    King's PC is committed to making our website and support experience accessible to as many users as possible,
                    including customers using assistive technologies.
                </p>
            </section>

            <section class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-neutral-900">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Accessibility measures</h2>
                <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-gray-700 dark:text-gray-300">
                    <li>Consistent page structures with clear heading order for improved screen reader navigation.</li>
                    <li>Readable contrast and scalable text to support users with low-vision preferences.</li>
                    <li>Keyboard-friendly navigation for primary links, forms, and action controls.</li>
                </ul>
            </section>

            <section class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-neutral-900">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Known limitations</h2>
                <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-gray-700 dark:text-gray-300">
                    <li>Some older product images may have incomplete descriptive metadata.</li>
                    <li>Third-party embeds and external links may not fully match this site's accessibility baseline.</li>
                </ul>
            </section>

            <section class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-neutral-900">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Feedback and assistance</h2>
                <p class="text-sm leading-7 text-gray-700 dark:text-gray-300">
                    If you encounter accessibility barriers, please contact us through the Contact Us page. Share the page URL,
                    issue details, and device/browser used so we can investigate and improve quickly.
                </p>
            </section>
        </div>
    </div>
</template>
