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
    const page = usePage()
    const pendingDeletes = new Set<number>()
    const deleteCartItem = (item: CartItem) => {
        const cart = page.props.cart as CartItem[]
        pendingDeletes.add(item.product.id)
        page.props.cart = cart.filter(i => i.product.id !== item.product.id)
        router.delete(`/cart/${item.product.id}`, {
            preserveScroll: true,
            showProgress: false,
            preserveState: true,
            only: ['cart', 'cart_items'],
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


    return deleteCartItem
}