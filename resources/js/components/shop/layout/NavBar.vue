<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import {
    CircleUserRound,
    Crown,
    Menu,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetTrigger,
    SheetClose,
} from '@/components/ui/sheet';
import type { Category } from '@/types/product';

const page = usePage();
const categories = computed(() => page.props.categories as Category[]);
</script>

<template>
    <header
        class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-backdrop-filter:bg-background/60"
    >
        <div class="flex h-16 items-center justify-between px-4 md:px-16">
            <div class="flex items-center font-bold">
                <Crown class="mr-1 h-6 w-6" />
                <Link href="/" class="md:text-sm lg:text-xl">King's PC</Link>
            </div>

            <NavigationMenu :viewport="false" class="ml-auto hidden lg:grid">
                <NavigationMenuList>
                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/" >Home</Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>

                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/builder">AI Builder</Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>

                    <NavigationMenuItem>
                        <NavigationMenuTrigger>
                            <NavigationMenuLink
                                as-child
                                :class="navigationMenuTriggerStyle()"
                            >
                                <Link href="/components" :only="['products']"
                                    >Products</Link
                                >
                            </NavigationMenuLink>
                        </NavigationMenuTrigger>
                        <NavigationMenuContent>
                            <ul class="grid w-50 gap-2 p-2">
                                <li
                                    v-for="category in categories"
                                    :key="category.id"
                                >
                                    <NavigationMenuLink as-child>
                                        <Link
                                            :href="'/' + category.slug"
                                            class="block rounded-md p-2 text-sm hover:bg-accent"
                                        >
                                            {{ category.name.toUpperCase() }}
                                        </Link>
                                    </NavigationMenuLink>
                                </li>
                            </ul>
                        </NavigationMenuContent>
                    </NavigationMenuItem>

                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/contacts">Contact Us</Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>

                    <NavigationMenuItem v-if="page.props.auth && page.props.auth.user">
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/orders">Orders</Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>


                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/login">
                                <CircleUserRound class="h-5 w-5" />
                            </Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>
                </NavigationMenuList>
            </NavigationMenu>

            <div class="flex items-center gap-4 lg:hidden">
                <Sheet>
                    <SheetTrigger as-child>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="cursor-pointer"
                        >
                            <Menu class="h-6 w-6" />
                        </Button>
                    </SheetTrigger>
                    <SheetContent
                        side="right"
                        class="w-75 sm:w-100 [&_.sheet-close]:cursor-pointer"
                    >
                        <nav class="mt-8 flex flex-col gap-6 pl-3">
                            <SheetClose as-child>
                                <Link
                                    href="/"
                                    class="rounded-md p-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                    >Home</Link
                                >
                            </SheetClose>

                            <SheetClose as-child>
                                <Link
                                    href="/builder"
                                    class="rounded-md p-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                    >AI Builder</Link
                                >
                            </SheetClose>

                            <div class="flex flex-col gap-3">
                                <SheetClose as-child>
                                    <Link
                                        href="/components"
                                        class="rounded-md p-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                        >Components</Link
                                    >
                                </SheetClose>

                                <div
                                    class="ml-2 flex flex-col gap-2 border-l-2 pl-4"
                                >
                                    <SheetClose
                                        as-child
                                        v-for="category in categories"
                                        :key="category.id"
                                    >
                                        <Link
                                            :href="'/' + category.slug"
                                            class="rounded-md p-1 text-sm text-muted-foreground transition hover:bg-accent hover:text-primary"
                                        >
                                            {{ category.name.toUpperCase() }}
                                        </Link>
                                    </SheetClose>
                                </div>
                            </div>

                            <SheetClose as-child>
                                <Link
                                    href="/contacts"
                                    class="rounded-md p-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                    >Contact Us</Link
                                >
                            </SheetClose>

                            <SheetClose as-child>
                                <Link
                                    href="/login"
                                    class="flex items-center gap-2 rounded-md border-t p-1 pt-2 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                >
                                    <CircleUserRound class="h-5 w-5" /> Login /
                                    Register
                                </Link>
                            </SheetClose>
                        </nav>
                    </SheetContent>
                </Sheet>
            </div>
        </div>
    </header>
</template>
