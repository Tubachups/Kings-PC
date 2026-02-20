export interface CartItem {
    id: number,
    user_id: number,
    product_id: number,
    quantity: number,
    variant: string | null,
    product: {
        id: number,
        name: string,
        description: string,
        price: number,
        stock: number,
        image_url: string
    }
}