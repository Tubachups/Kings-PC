<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { index as productsIndex, create as productsCreate, store as productsStore } from '@/actions/App/Http/Controllers/Admin/ProductController';
import ProductForm from '@/components/shop/admin/ProductForm.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Category } from '@/types/product';

interface Props {
    categories: Category[];
}

defineProps<Props>();

const form = useForm({
    name: '',
    price: '',
    category_id: null as number | null,
    stock: '',
    image: null as File | null,
    specs: JSON.stringify({}, null, 2),
    is_active: true,
});

function submit() {
    const data = {
        ...form.data(),
        is_active: form.is_active === true,
    };
    form.transform(() => data).post(productsStore().url, {
        onSuccess: () => {
            toast.success(`${form.name} created successfully!`);
        },
    });
}

const breadcrumbs = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Products', href: productsIndex().url },
    { title: 'Create', href: productsCreate().url },
];
</script>

<template>
    <Head title="Create Product">
        <meta head-key="description" name="description" content="Create and publish a new product listing in the King's PC admin dashboard." />
    </Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Create Product</h1>
                <p class="text-muted-foreground">
                    Add a new PC part to your inventory
                </p>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Product Information</CardTitle>
                    <CardDescription>
                        Fill in the details for the new product
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <ProductForm
                        :form="form"
                        :categories="categories"
                        submit-label="Create Product"
                        :cancel-href="productsIndex().url"
                        image-field-mode="file"
                        specs-behavior="always"
                        :show-specs-reference="true"
                        @submit="submit"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
