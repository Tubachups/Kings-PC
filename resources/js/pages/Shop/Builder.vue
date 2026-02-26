<script setup lang="ts">
import ProductCard from '@/components/shop/ProductCard.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import Layout from '@/layouts/MainLayout.vue';
import { AiBuildResponse } from '@/types/ai';
import axios from 'axios';
import { reactive, ref } from 'vue';
import { toast } from 'vue-sonner';
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
    <div class="m-12 flex h-full items-center justify-center">
        <Card class="grid h-[75vh] w-[85%] grid-cols-1 md:grid-cols-2">
            <div class="flex flex-col justify-center p-6">
                <form @submit.prevent="handleSubmit">
                    <Textarea v-model="form.prompt" class="h-full resize-none" placeholder="PC build idea here" />
                    <Input v-model="form.budget" type="number" class="mt-2" />
                    <Button :disabled="isSubmitting" type="submit" class="mt-2">
                        {{ isSubmitting ? 'Generating...' : 'Submit' }}
                    </Button>
                </form>
            </div>
            <div class="p-6">
                <Card v-if="!aiBuild && !isSubmitting" class="flex h-fit w-fit items-center justify-center">
                    <img class="rounded-lg object-fill" src="/images/ai.png" alt="PC Build" />
                </Card>
                <Card v-else-if="isSubmitting" class="p-6 text-center text-slate-600">
                    Generating your build...
                    <img class="rounded-lg object-cover w-full h-96" src="/images/053.jpg" alt="PC Build" />                
                </Card>
                <div v-else-if="aiBuild" class="flex h-full flex-col gap-4">
                    <Card class="rounded-xl border bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Build Summary</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900">
                            Total: {{ formatCurrency(aiBuild.total_price) }}
                        </p>
                        <p class="mt-2 text-sm leading-relaxed text-slate-700">
                            {{ aiBuild.explanation || 'No explanation returned by AI.' }}
                        </p>
                    </Card>
                    <div class="grid max-h-[45vh] grid-cols-1 gap-3 overflow-y-auto pr-1 md:grid-cols-2">
                        <Card
                            v-for="product in aiBuild.build"
                            :key="product.id"
                            class="rounded-xl border border-slate-200 p-4 shadow-sm"
                        >
                            <ProductCard :product="{
                                id: product.id,
                                name: product.name,
                                price: toNumber(product.price),
                                image_url: product.image_url,
                                specs: product.specs,
                            }" />
                        </Card>
                    </div>
                </div>
            </div>
        </Card>
    </div>
</template>
