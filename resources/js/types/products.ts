export interface Product {
    id: number;
    sku: string;
    name: string;
    unit_price: number;
    is_active: boolean;
}

export interface ProductListItem {
    id: number;
    sku: string;
    name: string;
    unit_price: number;
}
