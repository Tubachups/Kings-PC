<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { aboutHighlights, aboutStats, aboutValues } from '@/constants/constants'
import Layout from '@/layouts/MainLayout.vue'
import { builder as builderRoute, contacts as contactsRoute } from '@/routes'

defineOptions({ layout: Layout })
</script>

<template>
    <Head title="About">
        <meta
            head-key="description"
            name="description"
            content="Learn how King's PC builds reliable custom rigs, sources premium components, and supports builders at every stage."
        />
    </Head>

    <div class="bg-background py-10">
        <div class="mx-auto flex max-w-6xl flex-col gap-10 px-4 md:px-6">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-muted-foreground">
                        About King's PC
                    </p>
                    <h1 class="mt-3 text-3xl font-bold text-foreground md:text-4xl">
                        Built by builders, for builders.
                    </h1>
                    <p class="mt-4 text-lg text-muted-foreground">
                        At King's PC, we craft machines as unique as the creators who use them. Whether you're stepping into your first custom rig or commanding a heavy-duty workstation, we obsess over raw performance, flawless cable management, and rock-solid reliability that lasts for years.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link
                            :href="builderRoute().url"
                            class="rounded-full bg-primary px-5 py-2 text-sm font-semibold text-primary-foreground transition hover:bg-primary/90"
                        >
                            Start a build
                        </Link>
                        <Link
                            :href="contactsRoute().url"
                            class="rounded-full border border-border px-5 py-2 text-sm font-semibold text-foreground transition hover:bg-accent"
                        >
                            Talk to the team
                        </Link>
                    </div>
                </div>
                <div class="grid gap-4 rounded-3xl border border-border bg-card p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-muted-foreground">Workshop focus</p>
                        <span class="rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">
                            Updated weekly
                        </span>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div
                            v-for="stat in aboutStats"
                            :key="stat.label"
                            class="rounded-2xl border border-border bg-muted/60 p-4 text-left"
                        >
                            <p class="text-2xl font-bold text-foreground">{{ stat.value }}</p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ stat.label }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div
                    v-for="highlight in aboutHighlights"
                    :key="highlight.title"
                    class="rounded-2xl border border-border bg-card p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <h2 class="text-lg font-semibold text-foreground">{{ highlight.title }}</h2>
                    <p class="mt-2 text-sm text-muted-foreground">{{ highlight.description }}</p>
                </div>
            </div>

            <div class="grid gap-6 rounded-3xl border border-border bg-card p-6 lg:grid-cols-2">
                <div class="flex flex-col justify-evenly">
                    <div>
                        <h2 class="text-2xl font-semibold text-foreground">What we care about</h2>
                        <p class="mt-3 text-sm text-muted-foreground">
                            Every build gets the same obsessive attention to thermals, airflow, and part compatibility. We
                            share transparent pricing and send build progress updates so you always know whats happening.
                        </p>
                    </div>
                    <div class="mt-3 rounded-2xl border border-border bg-muted/60 px-4 py-3 text-left lg:mt-0">
                        <p class="text-sm font-semibold text-foreground">Tailored aesthetics</p>
                            <p class="mt-1 text-sm text-muted-foreground">From stealthy, minimalist workstations to fully synchronized RGB showcases, we build to match your exact style.</p>
                    </div>
                </div>
                <ul class="grid gap-3">
                    <li
                        v-for="value in aboutValues"
                        :key="value.title"
                        class="rounded-2xl border border-border bg-muted/60 px-4 py-3 text-left"
                    >
                        <p class="text-sm font-semibold text-foreground">{{ value.title }}</p>
                        <p class="mt-1 text-sm text-muted-foreground">{{ value.description }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
