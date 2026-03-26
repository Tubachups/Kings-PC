<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Layout from '@/layouts/MainLayout.vue'

defineOptions({ layout: Layout })
</script>

<template>
    <Head title="Terms & Condition">
        <meta
            head-key="description"
            name="description"
            content="Terms and conditions for using King's PC services, website pages, and custom build requests."
        />
    </Head>

    <div class="bg-background py-10">
        <div class="mx-auto max-w-4xl space-y-8 px-4 md:px-6">
            <header class="space-y-3">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-muted-foreground">
                    Legal
                </p>
                <h1 class="text-3xl font-bold text-foreground md:text-4xl">Terms & Condition</h1>
                <p class="text-sm text-muted-foreground">
                    Last updated: March 13, 2026
                </p>
            </header>

            <section class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <p class="text-sm leading-7 text-muted-foreground">
                    By browsing this website, requesting parts, or placing an order with King's PC, you agree to the terms below.
                    These terms are designed to keep communication clear, expectations realistic, and project timelines manageable.
                </p>
            </section>

            <section class="space-y-4 rounded-2xl border border-border bg-card p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-foreground">Services and quotations</h2>
                <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-muted-foreground">
                    <li>All quotations are based on component availability and may change before final confirmation.</li>
                    <li>Lead times are estimates and can shift due to supplier delays or customer-requested changes.</li>
                    <li>Custom build recommendations are based on the performance goals and budget you provide.</li>
                </ul>
            </section>

            <section class="space-y-4 rounded-2xl border border-border bg-card p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-foreground">Payments and order handling</h2>
                <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-muted-foreground">
                    <li>Orders are confirmed only after payment or required reservation terms are completed.</li>
                    <li>Cancellation requests are evaluated case-by-case, especially when parts are already ordered.</li>
                    <li>Build completion photos, benchmarks, and testing details may be shared for confirmation before release.</li>
                </ul>
            </section>

            <section class="space-y-4 rounded-2xl border border-border bg-card p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-foreground">Warranty and support</h2>
                <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-muted-foreground">
                    <li>Component warranties follow each manufacturer or distributor policy.</li>
                    <li>King's PC provides build-level support for setup, diagnostics, and upgrade guidance.</li>
                    <li>Physical damage, misuse, or unauthorized modifications may void service coverage.</li>
                </ul>
            </section>
        </div>
    </div>
</template>
