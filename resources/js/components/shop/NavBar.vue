<script setup lang="ts">
import {
    CircleCheckIcon,
    CircleHelpIcon,
    CircleIcon,
    ShoppingCart,
    CircleUserRound,
    Crown,
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
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="sticky top-0 z-10 flex h-16 flex-row items-center px-6 md:px-16">
        <div class="flex w-1/2 flex-row">
            <span class="flex w-1/2 flex-row font-bold md:w-1/4">
                <Crown class="mx-2" />
                KINGS PC
            </span>
        </div>
        <NavigationMenu :viewport="false" class="ml-auto">
            <div>
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
                        <NavigationMenuTrigger
                            >Components</NavigationMenuTrigger
                        >
                        <NavigationMenuContent>
                            <ul class="grid w-[200px] gap-4">
                                <li>
                                    <NavigationMenuLink as-child>
                                        <a href="#">GPU</a>
                                    </NavigationMenuLink>
                                    <NavigationMenuLink as-child>
                                        <a href="#">Case</a>
                                    </NavigationMenuLink>
                                    <NavigationMenuLink as-child>
                                        <a href="#">Motherboard</a>
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
                            <a href="/docs">Contacts</a>
                        </NavigationMenuLink>
                    </NavigationMenuItem>

                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <a href="/docs"
                                ><ShoppingCart
                                    style="width: 48px; height: 48px"
                            /></a>
                        </NavigationMenuLink>
                    </NavigationMenuItem>

                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle()"
                        >
                            <Link href="/login"
                                ><CircleUserRound
                                    style="width: 48px; height: 48px"
                            /></Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>
                </NavigationMenuList>
            </div>
        </NavigationMenu>
    </div>
</template>
