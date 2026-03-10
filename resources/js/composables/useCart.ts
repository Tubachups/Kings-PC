import { router, usePage } from "@inertiajs/vue3";
import { storeToRefs } from "pinia";
import { computed } from "vue";
import { toast } from "vue-sonner";
import { useCartStore } from "@/stores/cartStore";
import type { Product, CartItem } from "@/types/cart";

export function useCart() {
    const cartStore = useCartStore()
    const page = usePage()
    const { items } = storeToRefs(cartStore)

    const updateQuantity = (product: Product, newQty: number) => {
        const existingItem = cartStore.items.find(i => i.product.id === product.id);
        const prevQty = existingItem ? existingItem.quantity : 0;

        if(existingItem){
            cartStore.updateLocalQuantity(product.id, newQty)
        } else {
            cartStore.addItem({
                id: Date.now(),
                user_id: page.props.auth.user.id,
                product_id: product.id,
                quantity: newQty,
                variant: null,
                product: product
            });
        }

        router.put(`/cart/${product.id}`, {quantity: newQty}, {
            preserveScroll: true,
            preserveState: true,
            only: ['cart'],
            showProgress: false,
            onStart: () => {
                cartStore.setSyncing(true)
                toast.success("Cart Updated", {
                    description: `${product.name} is now in your cart.`,
                });
            },
            onFinish: () => {
                cartStore.setSyncing(false)
            },
            onError: () => {
                if (prevQty === 0) {
                    cartStore.removeLocalItem(product.id);
                } else {
                    cartStore.updateLocalQuantity(product.id, prevQty);
                }
                toast.error("Cart sync failed");
            }
        })
    }

    const deleteCartItem = (item: CartItem) => {
        const snapshot = [...cartStore.items];
        cartStore.removeLocalItem(item.product.id);
        router.delete(`/cart/${item.product.id}`, {
            preserveScroll: true,
            showProgress: false,
            preserveState: true,
            only: ['cart'],
            onStart: () => {
                cartStore.setSyncing(true)
                toast.success("Cart Updated", {
                    description: `${item.product.name} is now removed.`,
                });                
            },
            onFinish: () => {
                cartStore.setSyncing(false)
            },
            onError: () => {
                cartStore.setItems(snapshot);
                toast.error("Error deleting item")
            }
        })
    }

    const clearCart = () => {
        cartStore.clearItems()
        router.delete("/cart/clear", {
            preserveScroll: true,
            showProgress: false,
            preserveState: true,
            onSuccess: () => {
                cartStore.clearItems()
                toast.success("Cart Updated", {
                    description: `Your Cart is now clear.`,
                });
            }
        })
    }

    const handleAddToCart = (product : Product) => {
        const existingItem = items.value.find(i => i.product.id === product.id);
        const newQty = existingItem ? existingItem.quantity + 1 : 1;
        updateQuantity(product, newQty);
    }


    return {
        items,
        subTotal: computed(() => cartStore.subTotal),
        totalItems: computed(() => cartStore.totalItems),
        updateQuantity,
        deleteCartItem,
        clearCart,
        handleAddToCart
    };
}