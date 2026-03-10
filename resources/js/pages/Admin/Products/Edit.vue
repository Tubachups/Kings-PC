<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import {
    edit as productsEdit,
    index as productsIndex,
    update as productsUpdate,
} from '@/actions/App/Http/Controllers/Admin/ProductController';
import ProductForm from '@/components/shop/admin/ProductForm.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Category, Product } from '@/types/product';

interface Props {
    product: Product;
    categories: Category[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.product.name,
    category_id: props.product.category_id,
    price: props.product.price.toString(),
    stock: props.product.stock.toString(),
    image: props.product.image_url || '',
    specs: JSON.stringify(props.product.specs || {}, null, 2),
    is_active: props.product.is_active,
});

function submit() {
    // Parse specs and convert is_active before submitting
    try {
        const parsedForm = {
            ...form.data(),
            specs: JSON.parse(form.specs),
            is_active: form.is_active === true,
        };
        form.transform(() => parsedForm).put(productsUpdate(props.product.id).url, {
            onSuccess: () => {
                toast.success(`${props.product.name} updated successfully!`);
            },
        });
    } catch {
        form.setError('specs', 'Invalid JSON format');
    }
}

const breadcrumbs = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Products', href: productsIndex().url },
    { title: 'Edit', href: productsEdit(props.product.id).url },
];
</script>

<template>
    <Head title="Edit Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Edit Product</h1>
                <p class="text-muted-foreground">Update product information</p>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Product Information</CardTitle>
                    <CardDescription>
                        Edit the details for {{ product.name }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <ProductForm
                        :form="form"
                        :categories="categories"
                        submit-label="Update Product"
                        :cancel-href="productsIndex().url"
                        image-field-mode="url"
                        specs-behavior="if-empty"
                        @submit="submit"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
