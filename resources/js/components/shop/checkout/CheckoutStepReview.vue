<script setup lang="ts">
import { X } from 'lucide-vue-next'
import { computed} from 'vue'
import { Button } from '@/components/ui/button'
import { useCart } from '@/composables/useCart'
import { formatCurrency } from '@/utils/helpers'

const props = defineProps<{
  shippingFee: number
  couponDiscount: number
}>()

const { items, subTotal, deleteCartItem } = useCart()

const reviewTotal = computed(() => subTotal.value + props.shippingFee - props.couponDiscount)

const filteredImageUrl = (imageUrl: string): string => {
  if (!imageUrl) return ''
  return imageUrl.startsWith('/storage') ? imageUrl.replace(/^\/storage/, '') : imageUrl
}
</script>

<template>
  <div class="col-span-4">
    <div class="rounded-xl border border-border/70 bg-card p-6">
      <h2 class="text-2xl font-semibold">Shopping Cart</h2>

      <div class="mt-6 overflow-hidden rounded-lg border border-border/70">
        <div class="grid grid-cols-12 gap-4 border-b bg-muted/30 px-4 py-3 text-sm font-medium">
          <div class="col-span-7">Product</div>
          <div class="col-span-2 text-right">Price</div>
        </div>

        <div
          v-for="item in items"
          :key="item.id"
          class="grid grid-cols-12 items-center gap-4 border-b border-border/60 px-4 py-5 last:border-b-0"
        >
          <div class="col-span-7 flex items-center gap-3">
            <Button
              variant="ghost"
              size="icon"
              class="h-8 w-8 text-muted-foreground hover:text-foreground"
              @click="deleteCartItem(item)"
            >
              <X class="h-4 w-4" />
            </Button>
            <img
              :src="filteredImageUrl(item.product.image_url)"
              :alt="item.product.name"
              class="h-16 w-16 rounded-lg border bg-muted/50 object-cover"
            />
            <div class="min-w-0">
              <p class="truncate text-base font-medium">{{ item.product.name }}</p>
            </div>
          </div>


          <div class="col-span-2 text-right text-xl font-semibold">
            {{ formatCurrency(item.product.price) }}
          </div>
        </div>
      </div>

      <div class="mt-8 grid gap-8 lg:grid-cols-2">
        <div class="space-y-3 text-base">
          <div class="flex items-center justify-between">
            <span class="text-muted-foreground">Subtotal</span>
            <span class="font-medium">{{ formatCurrency(subTotal) }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-muted-foreground">Shipping Cost</span>
            <span class="font-medium">{{ formatCurrency(shippingFee) }}</span>
          </div>
          <div class="flex items-center justify-between border-b border-border/70 pb-3">
            <span class="text-muted-foreground">Discount</span>
            <span class="font-medium">{{ formatCurrency(couponDiscount) }}</span>
          </div>
          <div class="flex items-center justify-between text-2xl font-semibold">
            <span>Total Payable</span>
            <span>{{ formatCurrency(reviewTotal) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
