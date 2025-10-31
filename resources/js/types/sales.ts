import type { CheckoutItem } from './checkout';

export interface Sale {
    id: number;
    total_amount: number;
    regular_total: number;
    total_savings: number;
    items_input: CheckoutItem[];
    created_at: string;
    items: SaleItem[];
}

export interface SaleItem {
    sku: string;
    product_name: string;
    quantity: number;
    unit_price: number;
    regular_total: number;
    line_total: number;
    savings: number;
    promotion_applied: boolean;
    promotion?: {
        quantity: number;
        special_price: number;
    } | null;
}
