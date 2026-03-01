<script setup lang="ts">

import { Head, useForm } from '@inertiajs/vue3';
import { computed,  watch } from 'vue';
import { toast } from 'vue-sonner';

import { index as productsIndex, create as productsCreate, store as productsStore } from '@/actions/App/Http/Controllers/Admin/ProductController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
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
import type { Category } from '@/types/product';
import { specsTemplates, specsCategoryNames } from '@/utils/specsTemplates';

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

// Helper for Select binding

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

const categoryIdString = computed({
    get: () => form.category_id !== null ? String(form.category_id) : '',
    set: (val: string) => {
        form.category_id = val ? Number(val) : null;
    },
});

const isActiveString = computed({
    get: () => form.is_active ? '1' : '0',
    set: (val: string) => {
        form.is_active = val === '1';
    },
});

// Watch for category changes and update specs template
watch(() => form.category_id, (newCategoryId) => {
    if (newCategoryId && specsTemplates[String(newCategoryId) as keyof typeof specsTemplates]) {
        form.specs = JSON.stringify(specsTemplates[String(newCategoryId) as keyof typeof specsTemplates], null, 2);
    } else {
        form.specs = JSON.stringify({}, null, 2);
    }
});
</script>

<template>
    <Head title="Create Product" />

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
                            <Select v-model="categoryIdString">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select a category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id.toString()"
                                    >
                                        {{ category.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.category_id" />
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
                            <Label for="image" class="text-sm font-medium">Product Image</Label>

                            <input
                                id="image"
                                type="file"
                                accept="image/png, image/jpeg"
                                @change="e => form.image = (e.target as HTMLInputElement).files?.[0] ?? null"
                                class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-black file:text-white
                                    hover:file:bg-gray-800
                                    cursor:pointer border border-gray-300 rounded-md shadow-sm"
                            />

                            <InputError :message="form.errors.image" />

                            <p v-if="form.image" class="text-xs text-green-600">
                                Selected: {{ (form.image as File).name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="specs">Specifications</Label>
                            <Textarea
                                id="specs"
                                v-model="form.specs"
                                placeholder="Select a category to see the specifications template"
                                class="min-h-75 font-mono text-sm"
                            />
                            <p class="text-xs text-muted-foreground">
                                Specifications will auto-populate based on the selected category. You can edit the JSON structure as needed.
                            </p>

                            <!-- JSON Format Reference -->
                            <div v-if="form.category_id" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <p class="text-xs font-semibold text-blue-900 mb-2">
                                    📋 JSON Format Reference for {{ specsCategoryNames[String(form.category_id)] }}:
                                </p>
                                <pre class="text-xs bg-white p-2 rounded border border-blue-100 overflow-x-auto">{{ form.specs }}</pre>
                                <p class="text-xs text-blue-800 mt-2">
                                    Follow this structure and adjust values as needed. Ensure valid JSON format.
                                </p>
                            </div>

                            <InputError :message="form.errors.specs" />
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
                                Create Product
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="$inertia.visit('/admin/products')"
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
