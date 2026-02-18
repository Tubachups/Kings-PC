<script setup lang="ts">
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import ItemQuantity from '../ItemQuantity.vue';
import { computed, ref } from 'vue';

const tags = Array.from({ length: 50 }).map(
    (_, i, a) => `v1.2.0-beta.${a.length - i}`,
);

const items = ref([
    {
        id: 1,
        item: 'Fantech CG80 Aero Mid Tower ATX PC Case',
        quantity: 1,
        price: 1000,
        image_url: 'https://picsum.photos/id/100/400/300',
        total: 1000,
    },
    {
        id: 2,
        item: 'ASUS TUF Gaming B550-PLUS Motherboard',
        quantity: 1,
        price: 180,
        image_url: 'https://picsum.photos/id/101/400/300',
        total: 180,
    },
    {
        id: 3,
        item: 'Corsair Vengeance LPX 16GB (2x8GB) DDR4-3200',
        quantity: 2,
        price: 80,
        image_url: 'https://picsum.photos/id/102/400/300',
        total: 160,
    },
    {
        id: 4,
        item: 'NVIDIA GTX 1660 Super 6GB',
        quantity: 1,
        price: 250,
        image_url: 'https://picsum.photos/id/103/400/300',
        total: 250,
    },
    {
        id: 5,
        item: 'Samsung 970 EVO Plus 1TB NVMe SSD',
        quantity: 1,
        price: 120,
        image_url: 'https://picsum.photos/id/104/400/300',
        total: 120,
    },
    {
        id: 6,
        item: 'Cooler Master Hyper 212 RGB CPU Cooler',
        quantity: 1,
        price: 40,
        image_url: 'https://picsum.photos/id/106/400/300',
        total: 40,
    },
]);

const totalAll = computed(() => {
    return items.value.reduce((acc, item) => {
        return acc + item.total
    },0)
});

</script>

<template>
    <ScrollArea class="h-full w-full rounded-md border">
        <div class="p-4">
            <h4 class="mb-4 flex flex-row text-sm leading-none font-medium">
                <div class="flex flex-1 justify-center">total: {{ totalAll }}</div>
                <div class="flex flex-1 justify-center p-6">Item</div>
                <div class="flex flex-1 justify-center p-6">Quantity</div>
                <div class="flex flex-1 justify-center p-6">Price</div>
            </h4>
            <template v-for="item in items" key="id">
                <div class="flex flex-1 flex-row">
                    <div class="flex-1">
                        <img
                            :src="item.image_url"
                            :alt="item.item"
                            class="my-2 aspect-square w-full rounded-lg object-cover"
                        />
                    </div>
                    <div class="flex flex-3 flex-col">
                        <div class="flex flex-row flex-3">
                            <div class="flex-1 p-6">
                                <p class="text-2xl font-bold">
                                    {{ item.item }}
                                </p>
                            </div>
                            <div class="flex flex-1 justify-center p-6">
                                <p class="text-1xl font-bold">
                                    <ItemQuantity
                                        :defaultValue="item.quantity"
                                    />
                                </p>
                            </div>
                            <div class="flex flex-1 justify-center p-6">
                                <p class="text-1xl font-bold">
                                     ₱{{ item.price }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-row justify-end-safe items-baseline flex-1 mr-20 p-6 text-2xl font-bold">
                            Total: ₱{{ item.total }}
                        </div>
                    </div>
                </div>
                <Separator class="my-2" />
            </template>
        </div>
    </ScrollArea>
</template>
