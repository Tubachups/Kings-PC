<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, CheckCircle2, Clock3, FolderKanban, Folder, LayoutGrid, Plus, Settings, Store, Truck } from 'lucide-vue-next';
import { computed } from 'vue';
import { create as productsCreate, deliveredOrders, index as productsIndex, pendingOrders, processedOrders, shippedOrders } from '@/actions/App/Http/Controllers/Admin/ProductController';
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
import { dashboard, shop } from '@/routes';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

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
            icon: FolderKanban,
        },
        {
            title: 'Add New Product',
            href: productsCreate().url,
            icon: Plus,
        },
        {
            title: 'View Shop',
            href: shop().url,
            icon: Store,
        },
        {
            title: 'Pending Orders',
            href: pendingOrders().url,
            icon: Clock3,
        },
        {
            title: 'Processed Orders',
            href: processedOrders().url,
            icon: Settings,
        },
        {
            title: 'Shipped Orders',
            href: shippedOrders().url,
            icon: Truck,
        },
        {
            title: 'Delivered Orders',
            href: deliveredOrders().url,
            icon: CheckCircle2,
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
