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
import AppLayout from '@/layouts/app-layout';
import products from '@/routes/products';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: products.index().url,
    },
    {
        title: 'Create',
        href: products.create().url,
    },
];

export default function ProductCreate() {
    const form = useForm({
        sku: '',
        name: '',
        unit_price: '',
        is_active: true,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(products.store().url, {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Product" />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
                <div className="max-w-2xl">
                    <Card>
                        <CardHeader>
                            <div className="flex items-center gap-4">
                                <Link href={products.index().url}>
                                    <Button variant="outline" size="icon">
                                        <ArrowLeft className="size-4" />
                                    </Button>
                                </Link>
                                <div>
                                    <CardTitle>Create Product</CardTitle>
                                    <CardDescription>
                                        Add a new product to your catalog
                                    </CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="space-y-2">
                                    <Label htmlFor="sku">SKU</Label>
                                    <Input
                                        id="sku"
                                        name="sku"
                                        type="text"
                                        value={form.data.sku}
                                        onChange={(e) =>
                                            form.setData(
                                                'sku',
                                                e.target.value.toUpperCase(),
                                            )
                                        }
                                        placeholder="e.g., A, B, ABC123"
                                        className="uppercase"
                                        required
                                    />
                                    {form.errors.sku && (
                                        <p className="text-sm text-destructive">
                                            {form.errors.sku}
                                        </p>
                                    )}
                                    <p className="text-sm text-muted-foreground">
                                        Alphanumeric code to identify this
                                        product
                                    </p>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="name">Product Name</Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        type="text"
                                        value={form.data.name}
                                        onChange={(e) =>
                                            form.setData('name', e.target.value)
                                        }
                                        placeholder="e.g., Apple, Banana, Orange"
                                        required
                                    />
                                    {form.errors.name && (
                                        <p className="text-sm text-destructive">
                                            {form.errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="unit_price">
                                        Unit Price
                                    </Label>
                                    <Input
                                        id="unit_price"
                                        name="unit_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={form.data.unit_price}
                                        onChange={(e) =>
                                            form.setData(
                                                'unit_price',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="0.00"
                                        required
                                    />
                                    {form.errors.unit_price && (
                                        <p className="text-sm text-destructive">
                                            {form.errors.unit_price}
                                        </p>
                                    )}
                                    <p className="text-sm text-muted-foreground">
                                        Price per unit in dollars
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
                                        Active (available for checkout)
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
                                            : 'Create Product'}
                                    </Button>
                                    <Link
                                        href={products.index().url}
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
