import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import checkout from '@/routes/checkout';
import { type BreadcrumbItem } from '@/types';
import { type CheckoutItem, type CheckoutProduct } from '@/types/checkout';
import { Head, useForm } from '@inertiajs/react';
import { Plus, Trash2 } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Checkout',
        href: checkout.index().url,
    },
];

export default function CheckoutIndex({
    products,
}: {
    products: CheckoutProduct[];
}) {
    const form = useForm<{ items: CheckoutItem[] }>({
        items: [{ sku: '', quantity: 1 }],
    });

    const addRow = () => {
        form.setData('items', [...form.data.items, { sku: '', quantity: 1 }]);
    };

    const removeRow = (index: number) => {
        const newItems = form.data.items.filter((_, i) => i !== index);
        form.setData(
            'items',
            newItems.length > 0 ? newItems : [{ sku: '', quantity: 1 }],
        );
    };

    const updateRow = (
        index: number,
        field: keyof CheckoutItem,
        value: string | number,
    ) => {
        const newItems = [...form.data.items];
        newItems[index] = { ...newItems[index], [field]: value };
        form.setData('items', newItems);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        // Filter out rows with empty SKU before submission
        const validItems = form.data.items.filter(
            (item) => item.sku.trim() !== '',
        );
        if (validItems.length === 0) {
            form.setError('items', 'Please add at least one product.');
            return;
        }
        form.clearErrors();

        // Transform form data before submission
        form.transform(() => ({ items: validItems }));
        form.post(checkout.store().url, {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Checkout" />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
                {/* Instructions - Always visible at the top */}
                <Card className="bg-muted/50">
                    <CardHeader className="pb-3">
                        <CardTitle className="text-lg">How to Use</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-2 sm:grid-cols-2">
                            <p className="text-sm text-muted-foreground">
                                <span className="font-medium text-foreground">
                                    1.
                                </span>{' '}
                                Select products from the dropdown and enter
                                quantities
                            </p>
                            <p className="text-sm text-muted-foreground">
                                <span className="font-medium text-foreground">
                                    2.
                                </span>{' '}
                                Click "Add Item" to add more products to your
                                order
                            </p>
                            <p className="text-sm text-muted-foreground">
                                <span className="font-medium text-foreground">
                                    3.
                                </span>{' '}
                                The system will automatically apply any active
                                promotions
                            </p>
                            <p className="text-sm text-muted-foreground">
                                <span className="font-medium text-foreground">
                                    4.
                                </span>{' '}
                                Click "Calculate Total" to view your receipt
                                with itemized breakdown
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <div className="grid gap-6 md:grid-cols-2">
                    {/* Checkout Form */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Add Items</CardTitle>
                            <CardDescription>
                                Select products and quantities to calculate
                                total
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div className="space-y-3">
                                    {form.data.items.map((item, index) => (
                                        <div
                                            key={index}
                                            className="flex items-end gap-2"
                                        >
                                            <div className="flex-1 space-y-2">
                                                <Label
                                                    htmlFor={`product-${index}`}
                                                >
                                                    Product
                                                </Label>
                                                <Select
                                                    value={item.sku}
                                                    onValueChange={(value) =>
                                                        updateRow(
                                                            index,
                                                            'sku',
                                                            value,
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger
                                                        id={`product-${index}`}
                                                    >
                                                        <SelectValue placeholder="Select product" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {products.map(
                                                            (product) => (
                                                                <SelectItem
                                                                    key={
                                                                        product.sku
                                                                    }
                                                                    value={
                                                                        product.sku
                                                                    }
                                                                >
                                                                    {
                                                                        product.sku
                                                                    }{' '}
                                                                    -{' '}
                                                                    {
                                                                        product.name
                                                                    }{' '}
                                                                    ($
                                                                    {product.unit_price.toFixed(
                                                                        2,
                                                                    )}
                                                                    )
                                                                    {product.promotion && (
                                                                        <span className="text-xs text-green-600 dark:text-green-400">
                                                                            {' '}
                                                                            •{' '}
                                                                            {
                                                                                product
                                                                                    .promotion
                                                                                    .quantity
                                                                            }{' '}
                                                                            for
                                                                            $
                                                                            {product.promotion.special_price.toFixed(
                                                                                2,
                                                                            )}
                                                                        </span>
                                                                    )}
                                                                </SelectItem>
                                                            ),
                                                        )}
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                            <div className="w-24 space-y-2">
                                                <Label
                                                    htmlFor={`quantity-${index}`}
                                                >
                                                    Qty
                                                </Label>
                                                <Input
                                                    id={`quantity-${index}`}
                                                    type="number"
                                                    min="1"
                                                    value={item.quantity}
                                                    onChange={(e) =>
                                                        updateRow(
                                                            index,
                                                            'quantity',
                                                            parseInt(
                                                                e.target.value,
                                                            ) || 1,
                                                        )
                                                    }
                                                />
                                            </div>
                                            {form.data.items.length > 1 && (
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="icon"
                                                    onClick={() =>
                                                        removeRow(index)
                                                    }
                                                    className="flex-shrink-0"
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            )}
                                        </div>
                                    ))}
                                </div>

                                {form.errors.items && (
                                    <p className="text-sm text-destructive">
                                        {form.errors.items}
                                    </p>
                                )}

                                <div className="flex gap-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={addRow}
                                        className="flex-1"
                                    >
                                        <Plus className="mr-2 h-4 w-4" />
                                        Add Item
                                    </Button>
                                    <Button
                                        type="submit"
                                        className="flex-1"
                                        disabled={form.processing}
                                    >
                                        {form.processing
                                            ? 'Calculating...'
                                            : 'Calculate Total'}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    {/* Available Products Reference */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Available Products</CardTitle>
                            <CardDescription>
                                Active products with current prices and
                                promotions
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>SKU</TableHead>
                                        <TableHead>Name</TableHead>
                                        <TableHead className="text-right">
                                            Price
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {products.length === 0 ? (
                                        <TableRow>
                                            <TableCell
                                                colSpan={3}
                                                className="text-center text-muted-foreground"
                                            >
                                                No active products available
                                            </TableCell>
                                        </TableRow>
                                    ) : (
                                        products.map((product) => (
                                            <TableRow key={product.sku}>
                                                <TableCell className="font-mono font-medium">
                                                    {product.sku}
                                                </TableCell>
                                                <TableCell>
                                                    {product.name}
                                                    {product.promotion && (
                                                        <div className="text-xs text-green-600 dark:text-green-400">
                                                            Special:{' '}
                                                            {
                                                                product
                                                                    .promotion
                                                                    .quantity
                                                            }{' '}
                                                            for $
                                                            {product.promotion.special_price.toFixed(
                                                                2,
                                                            )}
                                                        </div>
                                                    )}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    $
                                                    {product.unit_price.toFixed(
                                                        2,
                                                    )}
                                                </TableCell>
                                            </TableRow>
                                        ))
                                    )}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
