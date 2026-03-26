<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, reactive, ref } from 'vue';
import { toast } from 'vue-sonner';
import ProductCard from '@/components/shop/products/ProductCard.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
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
const isInputEmpty = computed(() => form.prompt.trim().length === 0 || toNumber(form.budget) <= 0);

const handleSubmit = async () => {
    if (isInputEmpty.value || isSubmitting.value) return;

    isSubmitting.value = true;
    aiBuild.value = null;

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
    <Head title="AI Builder">
        <meta head-key="description" name="description" content="Generate an AI-powered custom PC build based on your budget and needs at King's PC." />
    </Head>

    <div class="m-5 flex flex-col lg:flex-row gap-4">
        <!-- ── Form ── -->
        <Card class="w-full lg:w-1/3 lg:max-w-sm self-start">
            <div class="flex flex-col justify-center p-6">
                <form @submit.prevent="handleSubmit" class="flex flex-col">
                    <Label class="block text-sm font-medium text-foreground">Build Idea</Label>
                    <Textarea
                        v-model="form.prompt"
                        class="mt-2 h-40 resize-none lg:h-96"
                        placeholder="PC build idea here"
                    />

                    <Label class="mt-4 block text-sm font-medium text-foreground">Budget (PHP)</Label>
                    <Input v-model="form.budget" type="number" min="1" class="mt-2" />

                    <Button :disabled="isSubmitting || isInputEmpty" type="submit" class="mt-6 w-full cursor-pointer">
                        {{ isSubmitting ? 'Generating...' : 'Generate Build' }}
                    </Button>
                </form>
            </div>
        </Card>

        <!-- ── Results ── -->
        <div class="flex flex-1 flex-col min-w-0">
            <Card class="flex min-h-[50vh] flex-1 flex-col items-center justify-center p-4 text-muted-foreground">

                <!-- Idle state -->
                <div v-if="!aiBuild && !isSubmitting" class="w-full max-w-sm">
                    <img class="w-full rounded-lg object-contain" src="/images/ai.png" alt="PC Build" />
                </div>

                <!-- Loading state -->
                <div v-else-if="isSubmitting" class="flex w-full max-w-sm flex-col items-center gap-4 text-center">
                    <span class="animate-pulse font-medium">Generating your build...</span>
                    <img class="w-full rounded-lg object-cover" src="/images/053.jpg" alt="PC Build" />
                </div>

                <!-- Build result -->
                <div v-if="aiBuild" class="flex w-full flex-col gap-4">

                    <!-- Summary card -->
                    <Card class="rounded-xl border border-border bg-muted/60 p-4">
                        <p class="text-xs uppercase tracking-wide text-muted-foreground">
                            Build Summary
                        </p>

                        <!-- Price row -->
                        <div class="flex  items-baseline ">
                            <p class="text-lg font-semibold text-foreground">
                                Total: {{ formatCurrency(toNumber(aiBuild.total_price)) }} of {{ formatCurrency(toNumber(aiBuild.budget)) }} budget
                            </p>
                        </div>

                        <p class="text-xs text-muted-foreground">
                            {{ aiBuild.explanation || 'No explanation returned by AI.' }}
                        </p>
                    </Card>

                    <!-- Product grid -->
                    <div class="grid w-full grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <Card
                            v-for="product in aiBuild.build"
                            :key="product.id"
                            class="flex flex-col rounded-xl border border-border p-4 shadow-sm"
                        >
                            <ProductCard :product="{
                                id:          product.id,
                                name:        product.name,
                                price:       toNumber(product.price),
                                image_url:   product.image_url,
                                specs:       product.specs,
                                description: product.description ?? '',
                                category_id: product.category_id ?? 0,
                                stock:       product.stock ?? 0,
                                is_active:   product.is_active ?? true,
                                category:    typeof product.category === 'string'
                                    ? { id: product.category_id ?? 0, name: product.category, slug: '' }
                                    : (product.category ?? { id: product.category_id ?? 0, name: '', slug: '' }),
                            }" />
                        </Card>
                    </div>
                </div>

            </Card>
        </div>
    </div>
</template>
