<script setup lang="ts">
import { library } from '@fortawesome/fontawesome-svg-core';
import { faComputer, faHouse, faPhone, faRobot, faCartArrowDown } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
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
    SheetHeader,
    SheetTitle,
    SheetTrigger,
    SheetClose,
} from '@/components/ui/sheet';
import { categoryOrder } from '@/constants/constants';
import type { Category } from '@/types/product';
import 'bootstrap-icons/font/bootstrap-icons.css'

library.add(faHouse, faRobot, faComputer, faPhone, faCartArrowDown);

const page = usePage();

const categoryIconClassesBySlug: Record<string, string> = {
    motherboard: 'bi bi-motherboard',
    cpu: 'bi bi-cpu',
    ram: 'bi bi-memory',
    storages: 'bi bi-device-ssd',
    psu: 'bi bi-power',
    gpu: 'bi bi-gpu-card',
    case: 'bi bi-suitcase-lg',
    cooling: 'bi bi-fan',
};

const categoryOrderPosition = new Map<string, number>(
    categoryOrder.map((slug, index) => [slug, index]),
);

const sortedCategories = computed(() => {
    const categories = [...(page.props.categories as Category[])];

    return categories.sort((firstCategory, secondCategory) => {
        const firstPosition = categoryOrderPosition.get(firstCategory.slug) ?? Number.MAX_SAFE_INTEGER;
        const secondPosition = categoryOrderPosition.get(secondCategory.slug) ?? Number.MAX_SAFE_INTEGER;

        if (firstPosition === secondPosition) {
            return firstCategory.name.localeCompare(secondCategory.name);
        }

        return firstPosition - secondPosition;
    });
});
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

            <NavigationMenu :viewport="false" class="ml-auto hidden md:grid">
                <NavigationMenuList>
                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/" ><span>
                                <FontAwesomeIcon :icon="faHouse"/> Home
                            </span></Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>

                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/builder"><span>
                                <FontAwesomeIcon :icon="faRobot"/> AI Builder
                            </span></Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>

                    <NavigationMenuItem>
                        <NavigationMenuTrigger>
                            <NavigationMenuLink
                                as-child
                                :class="navigationMenuTriggerStyle()"
                            >
                                <Link href="/components" :only="['products']"
                                    ><span>
                                        <FontAwesomeIcon :icon="faComputer"/> Products
                                    </span></Link
                                >
                            </NavigationMenuLink>
                        </NavigationMenuTrigger>
                        <NavigationMenuContent>
                            <ul class="grid w-50 gap-2 p-2">
                                <li
                                    v-for="category in sortedCategories"
                                    :key="category.id"
                                >
                                    <NavigationMenuLink as-child>
                                        <Link
                                            :href="'/' + category.slug"
                                            class="flex items-start gap-2 rounded-md p-2 text-sm hover:bg-accent"
                                        >
                                            <span>
                                                <i
                                                    :class="categoryIconClassesBySlug[category.slug] + ' mr-1'"
                                                    aria-hidden="true"
                                                />  {{ category.name.toUpperCase() }}
                                            </span>
                                        </Link>
                                    </NavigationMenuLink>
                                </li>
                            </ul>
                        </NavigationMenuContent>
                    </NavigationMenuItem>

                    <NavigationMenuItem v-if="page.props.auth && page.props.auth.user">
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/orders"><span>
                                <FontAwesomeIcon :icon="faCartArrowDown"/> Orders
                            </span></Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>
                    
                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/contacts"><span>
                                <FontAwesomeIcon :icon="faPhone"/> Contact Us
                            </span></Link>
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

            <div class="flex items-center gap-4 md:hidden">
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
                        <SheetHeader>
                            <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
                        </SheetHeader>
                        <nav class="mt-8 flex flex-col gap-6 pl-3">
                            <SheetClose as-child>
                                <Link
                                    href="/"
                                    class="rounded-md p-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                    ><FontAwesomeIcon :icon="faHouse"/> Home</Link
                                >
                            </SheetClose>

                            <SheetClose as-child>
                                <Link
                                    href="/builder"
                                    class="rounded-md p-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                    ><FontAwesomeIcon :icon="faRobot"/> AI Builder</Link
                                >
                            </SheetClose>

                            <div class="flex flex-col gap-3">
                                <SheetClose as-child>
                                    <Link
                                        href="/components"
                                        class="rounded-md p-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                        ><FontAwesomeIcon :icon="faComputer"/> Products</Link
                                    >
                                </SheetClose>

                                <div
                                    class="ml-2 flex flex-col gap-2 border-l-2 pl-4"
                                >
                                    <SheetClose
                                        as-child
                                        v-for="category in sortedCategories"
                                        :key="category.id"
                                    >
                                        <Link
                                            :href="'/' + category.slug"
                                            class="flex items-center gap-2 rounded-md p-1 text-sm text-muted-foreground transition hover:bg-accent hover:text-primary"
                                        >
                                            <i
                                                :class="categoryIconClassesBySlug[category.slug]"
                                                aria-hidden="true"
                                            />
                                            {{ category.name.toUpperCase() }}
                                        </Link>
                                    </SheetClose>
                                </div>
                            </div>

                            <SheetClose
                                v-if="page.props.auth && page.props.auth.user"
                                as-child
                            >
                                <Link
                                    href="/orders"
                                    class="rounded-md p-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                >
                                    <FontAwesomeIcon :icon="faCartArrowDown" /> Orders
                                </Link>
                            </SheetClose>

                            <SheetClose as-child>
                                <Link
                                    href="/contacts"
                                    class="rounded-md p-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                    ><FontAwesomeIcon :icon="faPhone" /> Contact Us</Link
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
