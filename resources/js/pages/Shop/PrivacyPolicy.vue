<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Layout from '@/layouts/MainLayout.vue'

defineOptions({ layout: Layout })
</script>

<template>
    <Head title="Privacy Policy">
        <meta
            head-key="description"
            name="description"
            content="Privacy policy for King's PC regarding customer details, inquiries, and order-related records."
        />
    </Head>

    <div class="bg-background py-10">
        <div class="mx-auto max-w-4xl space-y-8 px-4 md:px-6">
            <header class="space-y-3">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-muted-foreground">
                    Legal
                </p>
                <h1 class="text-3xl font-bold text-foreground md:text-4xl">Privacy Policy</h1>
                <p class="text-sm text-muted-foreground">
                    Last updated: March 13, 2026
                </p>
            </header>

            <section class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <p class="text-sm leading-7 text-muted-foreground">
                    King's PC respects your privacy. This page explains what personal information we collect, why we collect it,
                    and how we keep it secure when you contact us, request a quote, or place an order.
                </p>
            </section>

            <section class="space-y-4 rounded-2xl border border-border bg-card p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-foreground">Information we collect</h2>
                <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-muted-foreground">
                    <li>Contact details such as name, email address, mobile number, and delivery details.</li>
                    <li>Build preferences, budget targets, and product choices shared during consultations.</li>
                    <li>Transaction references needed to confirm payments, fulfillment, and support history.</li>
                </ul>
            </section>

            <section class="space-y-4 rounded-2xl border border-border bg-card p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-foreground">How data is used</h2>
                <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-muted-foreground">
                    <li>To respond to inquiries and prepare part recommendations that match your requirements.</li>
                    <li>To process orders, manage shipment coordination, and provide post-build support.</li>
                    <li>To improve service quality, communication speed, and support reliability.</li>
                </ul>
            </section>

            <section class="space-y-4 rounded-2xl border border-border bg-card p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-foreground">Data handling and retention</h2>
                <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-muted-foreground">
                    <li>We store information only as long as needed for orders, support, and legal compliance.</li>
                    <li>We do not sell customer information to third parties.</li>
                    <li>You may request corrections or deletion of your details by contacting our support channels.</li>
                </ul>
            </section>
        </div>
    </div>
</template>
