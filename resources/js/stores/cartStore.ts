import { defineStore } from "pinia";
import type { CartItem } from '@/types/cart';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [] as CartItem[],
        isSyncing: false
    }),

    getters: {
        totalItems: (state) => {
            return state.items.reduce((acc, item) => acc + (item.quantity || 0), 0);
        },
        subTotal:   (state) =>
            state.items.reduce((t, i) => t + i.quantity * i.product.price, 0),
    },

    actions: {
        setItems(items: CartItem[]) {
            this.items = items.filter(item => typeof item === 'object' && item !== null);        },
        addItem(item: CartItem) {
            // Use == instead of === to avoid string/number ID mismatches
            const existingItem = this.items.find(i => i.id == item.id);

            if (existingItem) {
                existingItem.quantity += item.quantity;
            } else {
                this.items.push({ ...item });
            }
        },
        updateLocalQuantity(productId: number, quantity: number) {
            const item = this.items.find(i => i.product.id === productId);
            if (item) {
                item.quantity = quantity
            }
        },
        removeLocalItem(productId: number) {
            this.items = this.items.filter(i => i.product.id !== productId)
        },
        setSyncing(status: boolean) {
            this.isSyncing = status
        },
        clearItems() {
            this.items = []
        }
    }
})