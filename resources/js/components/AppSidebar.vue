<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Package, Plus, ShoppingCart } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';
import { dashboard, shop } from '@/routes';
import { index as productsIndex, create as productsCreate } from '@/actions/App/Http/Controllers/Admin/ProductController';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const quickLinkItems = computed(() => {
    if (!user.value?.is_admin) {
        return [];
    }
    return [
        {
            title: 'Manage Products',
            href: productsIndex().url,
            icon: Package,
        },
        {
            title: 'Add New Product',
            href: productsCreate().url,
            icon: Plus,
        },
        {
            title: 'View Shop',
            href: shop().url,
            icon: ShoppingCart,
        },
    ];
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavMain v-if="quickLinkItems.length > 0" :items="quickLinkItems" label="Quick Actions" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
