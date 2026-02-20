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
    }
}

export function useCart() {
    const page = usePage()

    const deleteCartItem = (item: CartItem) => {
        router.delete(`/cart/${item.product.id}`, {
            preserveScroll: true,
            showProgress: false,
            preserveState: false,
            only: ['cart'],
            onSuccess: () => {
                toast.success("Cart Updated", {
                    description: `${item.product.name} is now removed from your cart.`,
                });
            }
        })
    }


    return deleteCartItem
}