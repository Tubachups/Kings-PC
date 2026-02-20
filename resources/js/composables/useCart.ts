import { useCartStore } from "@/stores/cartStore";
import { router, usePage } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
interface Product {
    id: number;
    name: string;
}

interface CartItem {
    product_id: number;
    product: {
        name: string;
        id: number;
    }
}

export function useCart() {
    const cartStore = useCartStore()
    const page = usePage()
    const user = page.props.auth.user.id;
    const pendingDeletes = new Set<number>()

    const deleteCartItem = (item: CartItem) => {
        const cart = page.props.cart as CartItem[]
        pendingDeletes.add(item.product.id)
        page.props.cart = cart.filter(i => i.product.id !== item.product.id)
        router.delete(`/cart/${item.product.id}`, {
            preserveScroll: true,
            showProgress: false,
            preserveState: true,
            only: ['cart', 'CartItem'],
            onError: () => {
                pendingDeletes.delete(item.product.id)
                page.props.cart = (page.props.cart as CartItem[]).concat(
                    cart.filter(i => 
                        i.product.id === item.product.id && 
                        !pendingDeletes.has(i.product.id)
                    )
                )
                toast.error("Failed to remove item", {
                    description: `${item.product.name} could not be removed.`
                })
            },
            onSuccess: () => {
                toast.success("Cart Updated", {
                    description: `${item.product.name} is now removed from your cart.`,
                });
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
                toast.success("Cart Updated", {
                    description: `Your Cart is now clear.`,
                });
            }
        })
    }


    return {deleteCartItem, clearCart}
}