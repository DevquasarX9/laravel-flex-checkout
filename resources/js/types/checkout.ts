export interface CheckoutItem {
    sku: string;
    quantity: number;
}

export interface CheckoutProduct {
    sku: string;
    name: string;
    unit_price: number;
    promotion: {
        quantity: number;
        special_price: number;
    } | null;
}
