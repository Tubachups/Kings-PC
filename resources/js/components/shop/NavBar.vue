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
import { Badge } from '@/components/ui/badge'
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Category } from '@/types/product';

const page = usePage();
const categories = computed(() => page.props.categories as Category[]);
const totalItems = computed(() => {
const cart = page.props.cart;
    // 1. Safety check: If cart is null/undefined, return 0
    if (!cart) return 0;

    // 2. Normalize the data: Ensure we are working with an Array
    // Redis data via Inertia can sometimes arrive as a keyed Object
    const items = Array.isArray(cart) ? cart : Object.values(cart);

    // 3. Sum every item's quantity
    return items.reduce((total, item) => {
        // Handle both "Rich" objects {quantity: 5, product: {}} 
        // and simple values (just in case)
        const qty = (typeof item === 'object' && item !== null) 
            ? parseInt(item.quantity) 
            : parseInt(item);

        return total + (isNaN(qty) ? 0 : qty);
    }, 0);
});
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
                        <NavigationMenuTrigger>
                            <NavigationMenuLink
                                as-child
                                :class="navigationMenuTriggerStyle()"
                            >
                                <Link href="/components" :only="['products']">Components</Link>
                            </NavigationMenuLink>
                        </NavigationMenuTrigger>
                        <NavigationMenuContent>
                            <ul class="grid w-[200px] gap-4">
                                <li v-for="category in categories" :key="category.id">
                                    <NavigationMenuLink as-child>
                                        <Link :href="'/' + category.slug">{{category.name.toUpperCase()}}</Link>
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
                            <a href="/contacts">Contacts</a>
                        </NavigationMenuLink>
                    </NavigationMenuItem>

                    <NavigationMenuItem>
                        <NavigationMenuLink
                            as-child
                            :class="navigationMenuTriggerStyle(), 'relative inline-block'"
                        >
                            <a href="/cart" :only="['cart_items']"
                                ><ShoppingCart
                                    style="width: 48px; height: 48px"
                            /></a>

                            <Badge 
                                variant="destructive" 
                                class="absolute -top-1 right-4 flex h-5 w-5 items-center justify-center rounded-full p-0 text-[10px]"
                            >
                               {{ totalItems }}
                            </Badge>
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
