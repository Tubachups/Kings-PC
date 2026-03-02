<script setup lang="ts">
import axios from 'axios';
import { reactive, ref } from 'vue';
import { toast } from 'vue-sonner';
import ProductCard from '@/components/shop/products/ProductCard.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import Layout from '@/layouts/MainLayout.vue';
import type { AiBuildResponse } from '@/types/ai';
import { formatCurrency } from '@/utils/helpers';

defineOptions({ layout: Layout });

const isSubmitting = ref(false);
const aiBuild = ref<AiBuildResponse | null>(null);
const form = reactive({
    prompt: '',
    budget: 0,
});


const toNumber = (value?: number | string | null) => Number(value ?? 0);

// Use shared formatCurrency helper

const handleSubmit = async () => {
    isSubmitting.value = true;

    try {
        const { data } = await axios.post<AiBuildResponse>('/builder/ai', form);
        aiBuild.value = data;
        toast.success('AI build generated successfully!');
    } catch (error: any) {
        const message = error?.response?.data?.error ?? 'Failed to generate AI build.';
        toast.error(message);
    } finally {
        isSubmitting.value = false;
    }
};

</script>

<template>
    <div class="m-12 flex flex-col md:flex-row h-full">
        <Card class="h-full md:w-1/3 m-2">
            <div class="flex flex-col justify-center p-6">
                <form @submit.prevent="handleSubmit">
                    <Label class="block text-sm font-medium text-slate-700">Build Idea</Label>
                    <Textarea v-model="form.prompt" class="h-96 resize-none" placeholder="PC build idea here" />
                    <Label class="mt-4 block text-sm font-medium text-slate-700">Budget (PHP)</Label>
                    <Input v-model="form.budget" type="number" class="mt-2" />
                    <Button :disabled="isSubmitting" type="submit" class="mt-2">
                        {{ isSubmitting ? 'Generating...' : 'Submit' }}
                    </Button>
                </form>
            </div>
        </Card>
        <div class="h-full md:w-2/3 m-2">
            <Card class="flex h-full items-center justify-center text-slate-600 p-6">
                <div v-if="!aiBuild && !isSubmitting">
                    <img class="rounded-lg object-fill" src="/images/ai.png" alt="PC Build" />
                </div>
                <div v-else-if="isSubmitting">
                    Generating your build...
                    <img class="rounded-lg object-cover w-full h-96" src="/images/053.jpg" alt="PC Build" />
                </div>
                <div v-if="aiBuild" class="flex h-full flex-col gap-4">
                    <Card class="rounded-xl border bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Build Summary</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900">
                            Total: {{ formatCurrency(toNumber(aiBuild?.total_price)) }}
                        </p>
                        <p class="mt-2 text-sm leading-relaxed text-slate-700">
                            {{ aiBuild?.explanation || 'No explanation returned by AI.' }}
                        </p>
                    </Card>
                    <div class="grid max-h-[45vh] grid-cols-1 gap-3 overflow-y-auto pr-1 md:grid-cols-4">
                        <Card
                            v-for="product in aiBuild.build"
                            :key="product.id"
                            class="flex flex-col rounded-xl border border-slate-200 p-4 shadow-sm"
                        >
                            <ProductCard  :product="{
                                id: product.id,
                                name: product.name,
                                price: toNumber(product.price),
                                image_url: product.image_url,
                                specs: product.specs,
                                description: product.description ?? '',
                                category_id: product.category_id ?? 0,
                                stock: product.stock ?? 0,
                                is_active: product.is_active ?? true,
                                category: product.category ?? '',
                            }" />
                        </Card>
                    </div>
                </div>
            </Card>
        </div>
    </div>
</template>
