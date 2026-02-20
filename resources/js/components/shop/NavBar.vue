<script setup lang="ts">
import {
    CircleCheckIcon,
    CircleHelpIcon,
    CircleIcon,
    ShoppingCart,
    CircleUserRound,
    Crown,
    Menu, // <-- Added for mobile hamburger icon
} from 'lucide-vue-next';
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
    SheetHeader,
    SheetTitle,
    SheetClose,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Category } from '@/types/product';
import { useCartStore } from '@/stores/cartStore';

const page = usePage();
const categories = computed(() => page.props.categories as Category[]);
const cartStore = useCartStore()
</script>

<template>
    <header
        class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-backdrop-filter:bg-background/60"
    >
        <div class="flex h-16 items-center justify-between px-4 md:px-16">
            <div class="flex items-center font-bold">
                <Crown class="mr-2 h-6 w-6" />
                <Link href="/" class="text-lg">King's PC</Link>
            </div>

            <NavigationMenu :viewport="false" class="ml-auto hidden md:flex">
                <NavigationMenuList>
                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/">Home</Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>

                    <NavigationMenuItem>
                        <NavigationMenuTrigger>
                            <NavigationMenuLink
                                as-child
                                :class="navigationMenuTriggerStyle()"
                            >
                                <Link href="/components" :only="['products']"
                                    >Components</Link
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

                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="[navigationMenuTriggerStyle(), 'relative']"
                        >
                            <Link href="/cart" :only="['cart_items']">
                                <ShoppingCart class="h-5 w-5" />
                                <Badge
                                    v-if="cartStore.totalItems > 0"
                                    variant="destructive"
                                    class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full p-0 text-[10px]"
                                >
                                    {{ cartStore.totalItems }}
                                </Badge>
                            </Link>
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
                <Link href="/cart" :only="['cart_items']" class="relative">
                    <ShoppingCart class="h-6 w-6" />
                    <Badge
                        v-if="cartStore.totalItems > 0"
                        variant="destructive"
                        class="absolute -top-2 -right-2 flex h-5 w-5 items-center justify-center rounded-full p-0 text-[10px]"
                    >
                        {{cartStore.totalItems }}
                    </Badge>
                </Link>

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
                                    class="rounded-md px-2 py-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                    >Home</Link
                                >
                            </SheetClose>

                            <div class="flex flex-col gap-3">
                                <SheetClose as-child>
                                    <Link
                                        href="/components"
                                        class="rounded-md px-2 py-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
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
                                            class="rounded-md px-2 py-1 text-sm text-muted-foreground transition hover:bg-accent hover:text-primary"
                                        >
                                            {{ category.name.toUpperCase() }}
                                        </Link>
                                    </SheetClose>
                                </div>
                            </div>

                            <SheetClose as-child>
                                <Link
                                    href="/contacts"
                                    class="rounded-md px-2 py-1 text-lg font-medium transition hover:bg-accent hover:text-primary"
                                    >Contact Us</Link
                                >
                            </SheetClose>

                            <SheetClose as-child>
                                <Link
                                    href="/login"
                                    class="flex items-center gap-2 rounded-md border-t px-2 py-1 pt-2 text-lg font-medium transition hover:bg-accent hover:text-primary"
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
