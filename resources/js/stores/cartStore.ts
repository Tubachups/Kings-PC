import { defineStore } from "pinia";
import { CartItem } from '@/types/cart';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [] as CartItem[]
    }),

    getters: {
        totalItems: (state) => 
            state.items.reduce((t, i) => t + i.quantity, 0),
        subTotal:   (state) =>
            state.items.reduce((t, i) => t + i.quantity * i.product.price, 0),
    },

    actions: {
        setItems(items: CartItem[]) {
            this.items = items
        },
        addItem(item: CartItem) {
            this.items.push(item)
        },
        removeItem(productId: number) {
            this.items = this.items.filter(i => i.product.id !== productId)
        },
        updateQuantity(productId: number, quantity: number) {
            const item = this.items.find(i => i.product.id === productId)
            if (item) item.quantity = quantity
        },
        clearItems() {
            this.items = []
        }
    }
})