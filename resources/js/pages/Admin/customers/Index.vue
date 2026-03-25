<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { BadgeCheck, KeyRound, MailCheck, Search, ShieldCheck, Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { index as customersIndex } from '@/actions/App/Http/Controllers/Admin/CustomerController';
import PaginationControls from '@/components/PaginationControls.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { PaginatedCustomers } from '@/types/customer';
import { formatDate, pesoFormatter } from '@/utils/helpers';

const props = defineProps<{
    customers: PaginatedCustomers;
    filters: {
        search?: string;
    };
    summary: {
        totalCustomers: number;
        verifiedCustomers: number;
        customersWithOrders: number;
    };
}>();

const search = ref(props.filters.search ?? '');

const summaryCards = computed(() => [
    {
        title: 'Total Customers',
        value: props.summary.totalCustomers,
        icon: Users,
    },
    {
        title: 'Verified Emails',
        value: props.summary.verifiedCustomers,
        icon: MailCheck,
    },
    {
        title: 'Customers With Orders',
        value: props.summary.customersWithOrders,
        icon: BadgeCheck,
    },
]);

const breadcrumbs = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Customers', href: customersIndex().url },
];

let debounceTimer: ReturnType<typeof setTimeout>;

watch(search, (value) => {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        router.get(
            customersIndex().url,
            {
                search: value || undefined,
                page: 1,
                per_page: props.customers.per_page,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 250);
});

const changePage = (page: number) => {
    router.get(
        customersIndex().url,
        {
            search: search.value || undefined,
            page,
            per_page: props.customers.per_page,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const credentialBadges = (customer: PaginatedCustomers['data'][number]) => [
    { label: customer.credentials.password_set ? 'Password set' : 'No password', active: customer.credentials.password_set, icon: KeyRound },
    { label: customer.credentials.email_verified ? 'Email verified' : 'Unverified email', active: customer.credentials.email_verified, icon: MailCheck },
    { label: customer.credentials.two_factor_enabled ? '2FA enabled' : '2FA off', active: customer.credentials.two_factor_enabled, icon: ShieldCheck },
    { label: customer.credentials.google_linked ? 'Google linked' : 'Google off', active: customer.credentials.google_linked, icon: BadgeCheck },
    { label: customer.credentials.facebook_linked ? 'Facebook linked' : 'Facebook off', active: customer.credentials.facebook_linked, icon: BadgeCheck },
];
</script>

<template>
    <Head title="Customer Management">
        <meta head-key="description" name="description" content="Review customer accounts, login setup, and account security details in King's PC admin." />
    </Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Customer Management</h1>
                    <p class="text-muted-foreground">
                        Review customer accounts and login setup. Plaintext passwords are not stored by Laravel, so this page shows credential status instead.
                    </p>
                </div>

                <div class="relative w-full max-w-md">
                    <Search class="text-muted-foreground absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2" />
                    <Input v-model="search" class="pl-9" placeholder="Search by customer name or email..." />
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <Card v-for="card in summaryCards" :key="card.title">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">{{ card.title }}</CardTitle>
                        <component :is="card.icon" class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ card.value }}</div>
                    </CardContent>
                </Card>
            </div>

            <Card class="overflow-hidden">
                <CardHeader class="flex flex-col gap-3 border-b bg-slate-50/80 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <CardTitle>Customer Accounts</CardTitle>
                        <CardDescription>
                            See who can sign in, which providers are linked, and which customers are actively buying.
                        </CardDescription>
                    </div>
                    <Link :href="dashboard().url">
                        <Button variant="outline">Back to Dashboard</Button>
                    </Link>
                </CardHeader>

                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Customer</TableHead>
                                    <TableHead>Credential Summary</TableHead>
                                    <TableHead>Orders</TableHead>
                                    <TableHead>Total Delivered Spend</TableHead>
                                    <TableHead>Joined</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="customer in customers.data" :key="customer.id">
                                    <TableCell class="align-top">
                                        <div class="space-y-1">
                                            <p class="font-medium">{{ customer.name }}</p>
                                            <p class="text-muted-foreground text-sm">{{ customer.email }}</p>
                                        </div>
                                    </TableCell>
                                    <TableCell class="align-top">
                                        <div class="flex flex-wrap gap-2">
                                            <Badge
                                                v-for="badge in credentialBadges(customer)"
                                                :key="badge.label"
                                                :variant="badge.active ? 'default' : 'secondary'"
                                                class="gap-1.5"
                                            >
                                                <component :is="badge.icon" class="h-3.5 w-3.5" />
                                                {{ badge.label }}
                                            </Badge>
                                        </div>
                                    </TableCell>
                                    <TableCell class="align-top font-medium">
                                        {{ customer.orders_count }}
                                    </TableCell>
                                    <TableCell class="align-top font-medium">
                                        {{ pesoFormatter.format(customer.delivered_spend) }}
                                    </TableCell>
                                    <TableCell class="align-top text-sm text-muted-foreground">
                                        {{ customer.created_at ? formatDate(customer.created_at) : 'N/A' }}
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="customers.data.length === 0">
                                    <TableCell colspan="5" class="text-muted-foreground h-32 text-center">
                                        No customers matched your search.
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <PaginationControls
                v-if="customers.total > customers.per_page"
                :total-items="customers.total"
                :current-page="customers.current_page"
                :items-per-page="customers.per_page"
                @update:page="changePage"
            />
        </div>
    </AppLayout>
</template>
