<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { dashboard } from '@/routes';
import { index as productsIndex, create as productsCreate, store as productsStore } from '@/actions/App/Http/Controllers/Admin/ProductController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ref, watch } from 'vue';
import type { Category } from '@/types/product'
import { specsTemplates } from '@/utils/specsTemplates';

interface Props {
    categories: Category[];
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    price: '',
    category_id: null as number | null,
    stock: '',
    image: '',
    specs: JSON.stringify({}, null, 2),
    is_active: true,
});

// Helper for Select binding

function submit() {
    form.post(productsStore().url);
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
                                        v-bind="{
                                            key: category.id,
                                            value: category.id.toString(),
                                        }"
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
                            <InputError :message="form.errors.specs" />
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="is_active"
                                v-model:checked="form.is_active"
                            />
                            <Label
                                for="is_active"
                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                            >
                                Active (Product will be visible in the shop)
                            </Label>
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
