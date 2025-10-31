import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import promotions from '@/routes/promotions';
import { type BreadcrumbItem } from '@/types';
import { type ProductListItem } from '@/types/products';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Promotions',
        href: promotions.index().url,
    },
    {
        title: 'Create',
        href: promotions.create().url,
    },
];

export default function PromotionCreate({
    products,
}: {
    products: ProductListItem[];
}) {
    const form = useForm({
        product_id: '',
        quantity: '',
        special_price: '',
        is_active: true,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(promotions.store().url, {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Promotion" />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
                <div className="max-w-2xl">
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-4">
                                <Link href={promotions.index().url}>
                                    <Button variant="outline" size="icon">
                                        <ArrowLeft className="size-4" />
                                    </Button>
                                </Link>
                                <div>
                                    <CardTitle>Create Promotion</CardTitle>
                                    <CardDescription>
                                        Add a special pricing offer for a
                                        product
                                    </CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="space-y-2">
                                    <Label htmlFor="product_id">Product</Label>
                                    <Select
                                        name="product_id"
                                        value={form.data.product_id}
                                        onValueChange={(value) =>
                                            form.setData('product_id', value)
                                        }
                                        required
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select a product" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {products.map((product) => (
                                                <SelectItem
                                                    key={product.id}
                                                    value={product.id.toString()}
                                                >
                                                    {product.sku} -{' '}
                                                    {product.name} ($
                                                    {product.unit_price.toFixed(
                                                        2,
                                                    )}
                                                    )
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {form.errors.product_id && (
                                        <p className="text-sm text-destructive">
                                            {form.errors.product_id}
                                        </p>
                                    )}
                                    <p className="text-sm text-muted-foreground">
                                        Select the product for this promotion
                                    </p>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="quantity">Quantity</Label>
                                    <Input
                                        id="quantity"
                                        name="quantity"
                                        type="number"
                                        min="2"
                                        value={form.data.quantity}
                                        onChange={(e) =>
                                            form.setData(
                                                'quantity',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="e.g., 3"
                                        required
                                    />
                                    {form.errors.quantity && (
                                        <p className="text-sm text-destructive">
                                            {form.errors.quantity}
                                        </p>
                                    )}
                                    <p className="text-sm text-muted-foreground">
                                        Number of items required for this price
                                        (minimum 2)
                                    </p>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="special_price">
                                        Special Price
                                    </Label>
                                    <Input
                                        id="special_price"
                                        name="special_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={form.data.special_price}
                                        onChange={(e) =>
                                            form.setData(
                                                'special_price',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="0.00"
                                        required
                                    />
                                    {form.errors.special_price && (
                                        <p className="text-sm text-destructive">
                                            {form.errors.special_price}
                                        </p>
                                    )}
                                    <p className="text-sm text-muted-foreground">
                                        Total price for the specified quantity
                                        (e.g., 3 for $1.30)
                                    </p>
                                </div>

                                <div className="flex items-center space-x-2">
                                    <Checkbox
                                        id="is_active"
                                        name="is_active"
                                        checked={form.data.is_active}
                                        onCheckedChange={(checked) =>
                                            form.setData('is_active', !!checked)
                                        }
                                    />
                                    <Label
                                        htmlFor="is_active"
                                        className="cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                    >
                                        Active (apply this promotion at
                                        checkout)
                                    </Label>
                                </div>

                                <div className="flex gap-4">
                                    <Button
                                        type="submit"
                                        className="flex-1"
                                        disabled={form.processing}
                                    >
                                        {form.processing
                                            ? 'Creating...'
                                            : 'Create Promotion'}
                                    </Button>
                                    <Link
                                        href={promotions.index().url}
                                        className="flex-1"
                                    >
                                        <Button
                                            type="button"
                                            variant="outline"
                                            className="w-full"
                                        >
                                            Cancel
                                        </Button>
                                    </Link>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
