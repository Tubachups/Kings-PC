<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
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
import type { ProductFormProps } from '@/types/product-form';
import { specsCategoryNames, specsTemplates } from '@/utils/specsTemplates';

const props = withDefaults(defineProps<ProductFormProps>(), {
    specsBehavior: 'if-empty',
    showSpecsReference: false,
});

const form = props.form;

const emit = defineEmits<{
    submit: [];
}>();

const categoryIdString = computed({
    get: () => (form.category_id !== null ? String(form.category_id) : ''),
    set: (value: string) => {
        form.category_id = value ? Number(value) : null;
    },
});

const isActiveString = computed({
    get: () => (form.is_active ? '1' : '0'),
    set: (value: string) => {
        form.is_active = value === '1';
    },
});

const imageUrl = computed({
    get: () => (typeof form.image === 'string' ? form.image : ''),
    set: (value: string) => {
        form.image = value;
    },
});

const specsPlaceholder = computed(() => {
    const template = specsTemplates[String(form.category_id) as keyof typeof specsTemplates];

    if (!template) {
        return '{}';
    }

    return JSON.stringify(template, null, 2);
});

watch(
    () => form.category_id,
    (newCategoryId) => {
        const template = specsTemplates[String(newCategoryId) as keyof typeof specsTemplates];

        if (!template) {
            if (props.specsBehavior === 'always') {
                form.specs = JSON.stringify({}, null, 2);
            }

            return;
        }

        if (props.specsBehavior === 'always') {
            form.specs = JSON.stringify(template, null, 2);
            return;
        }

        if (!form.specs || form.specs === '{}') {
            form.specs = JSON.stringify(template, null, 2);
        }
    },
);

function submit(): void {
    emit('submit');
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
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
            <Label for="image" class="text-sm font-medium">
                {{ imageFieldMode === 'file' ? 'Product Image' : 'Image URL' }}
            </Label>

            <input
                v-if="imageFieldMode === 'file'"
                id="image"
                type="file"
                accept="image/png, image/jpeg"
                class="block w-full cursor-pointer rounded-md border border-input bg-background text-sm text-muted-foreground shadow-sm file:mr-4 file:rounded-md file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-foreground hover:file:bg-primary/90"
                @change="event => form.image = (event.target as HTMLInputElement).files?.[0] ?? null"
            />

            <Input
                v-else
                id="image"
                v-model="imageUrl"
                type="text"
                placeholder="https://example.com/image.jpg"
            />

            <InputError :message="form.errors.image" />

            <p v-if="imageFieldMode === 'file' && form.image" class="text-xs text-green-600">
                Selected: {{ (form.image as File).name }}
            </p>
        </div>

        <div class="space-y-2">
            <Label for="specs">Specifications (JSON)</Label>
            <Textarea
                id="specs"
                v-model="form.specs"
                :placeholder="specsPlaceholder"
                rows="8"
                class="font-mono text-sm"
            />
            <p class="text-xs text-muted-foreground">
                Enter product specifications in JSON format. The template updates based on selected category.
            </p>

            <div
                v-if="showSpecsReference && form.category_id"
                class="mt-4 rounded-md border border-info/20 bg-info/10 p-3"
            >
                <p class="mb-2 text-xs font-semibold text-info-foreground">
                    JSON Format Reference for {{ specsCategoryNames[String(form.category_id)] }}:
                </p>
                <pre class="overflow-x-auto rounded border border-info/15 bg-background/80 p-2 text-xs text-foreground">{{ form.specs }}</pre>
                <p class="mt-2 text-xs text-info-foreground">
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
                {{ submitLabel }}
            </Button>
            <Link :href="cancelHref">
                <Button type="button" variant="outline">
                    Cancel
                </Button>
            </Link>
        </div>
    </form>
</template>
