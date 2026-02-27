<script setup lang="ts">

import { Head, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import {
    edit as productsEdit,
    index as productsIndex,
    update as productsUpdate,
} from '@/actions/App/Http/Controllers/Admin/ProductController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { Category, Product } from '@/types/product';
import { specsTemplates } from '@/utils/specsTemplates';


interface Props {
    product: Product;
    categories: Category[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.product.name,
    category_id: props.product.category_id.toString(),
    price: props.product.price.toString(),
    stock: props.product.stock.toString(),
    image: props.product.image_url || '',
    specs: JSON.stringify(props.product.specs || {}, null, 2),
    is_active: props.product.is_active,
});



const specsPlaceholder = computed(() => {
    const template = specsTemplates[form.category_id as keyof typeof specsTemplates];
    return template ? JSON.stringify(template, null, 2) : '{}';
});

const isActiveString = computed({
    get: () => form.is_active ? '1' : '0',
    set: (val: string) => {
        form.is_active = val === '1';
    },
});

watch(() => form.category_id, (newCategoryId) => {
    const template = specsTemplates[newCategoryId as keyof typeof specsTemplates];
    if (template && (!form.specs || form.specs === '{}')) {
        form.specs = JSON.stringify(template, null, 2);
    }
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
    } catch (e) {
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
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Name *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., NVIDIA RTX 4090"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="category">Category *</Label>
                            <Select v-model="form.category_id">
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Select a category"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="category in categories"
                                            :key="category.id"
                                            :value="category.id.toString()"
                                        >{{ category.name }}</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.category_id" />
                        </div>

                        <div class="space-y-2">
                            <Label for="specs">Specifications (JSON) *</Label>
                            <Textarea
                                id="specs"
                                v-model="form.specs"
                                :placeholder="specsPlaceholder"
                                required
                                rows="8"
                                class="font-mono text-sm"
                            />
                            <p class="text-xs text-muted-foreground">
                                Enter product specifications in JSON format. The template updates based on selected category.
                            </p>


                            <InputError :message="form.errors.specs" />
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="price">Price (PHP) *</Label>
                                <Input
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    required
                                />
                                <InputError :message="form.errors.price" />
                            </div>

                            <div class="space-y-2">
                                <Label for="stock">Stock Quantity *</Label>
                                <Input
                                    id="stock"
                                    v-model="form.stock"
                                    type="number"
                                    placeholder="0"
                                    required
                                />
                                <InputError :message="form.errors.stock" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="image">Image URL</Label>
                            <Input
                                id="image"
                                v-model="form.image"
                                type="text"
                                placeholder="https://example.com/image.jpg"
                            />
                            <InputError :message="form.errors.image" />
                        </div>

                        <div class="space-y-2">
                            <Label for="is_active">Status *</Label>
                            <Select v-model="isActiveString">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="1">Active (Visible in shop)</SelectItem>
                                    <SelectItem value="0">Inactive (Hidden from shop)</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.is_active" />
                        </div>

                        <div class="flex gap-2 pt-4">
                            <Button type="submit" :disabled="form.processing">
                                Update Product
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="$inertia.visit(productsIndex().url)"
                            >
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
