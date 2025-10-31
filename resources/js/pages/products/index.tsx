import ProductController from '@/actions/App/Http/Controllers/ProductController';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import products from '@/routes/products';
import { type BreadcrumbItem } from '@/types';
import { type Product } from '@/types/products';
import { Form, Head, Link } from '@inertiajs/react';
import { Edit, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface PaginatedProducts {
    current_page: number;
    data: Product[];
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: products.index().url,
    },
];

export default function ProductIndex({
    products: productList,
}: {
    products: PaginatedProducts;
}) {
    const [deleteId, setDeleteId] = useState<number | null>(null);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Products" />

            <div className="flex flex-1 flex-col gap-6 overflow-auto rounded-xl p-4">
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle>Product Management</CardTitle>
                            <CardDescription>
                                Manage your product catalog and pricing
                            </CardDescription>
                        </div>
                        <Link href={products.create().url}>
                            <Button>
                                <Plus className="mr-2 size-4" />
                                Add Product
                            </Button>
                        </Link>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>SKU</TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead className="text-right">
                                        Unit Price
                                    </TableHead>
                                    <TableHead className="text-center">
                                        Status
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Actions
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {productList.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={5}
                                            className="text-center text-muted-foreground"
                                        >
                                            No products found. Create your first
                                            product to get started.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    productList.data.map((product) => (
                                        <TableRow key={product.id}>
                                            <TableCell className="font-mono font-medium">
                                                {product.sku}
                                            </TableCell>
                                            <TableCell>
                                                {product.name}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                ${product.unit_price.toFixed(2)}
                                            </TableCell>
                                            <TableCell className="text-center">
                                                {product.is_active ? (
                                                    <span className="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-green-600/20 ring-inset dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                                        Active
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-gray-500/10 ring-inset dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                                                        Inactive
                                                    </span>
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex justify-end gap-2">
                                                    <Link
                                                        href={
                                                            products.edit({
                                                                product:
                                                                    product.id,
                                                            }).url
                                                        }
                                                    >
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                        >
                                                            <Edit className="size-4" />
                                                        </Button>
                                                    </Link>
                                                    {deleteId === product.id ? (
                                                        <div className="flex gap-1">
                                                            <Form
                                                                {...ProductController.destroy.form(
                                                                    {
                                                                        product:
                                                                            product.id,
                                                                    },
                                                                )}
                                                                onSuccess={() =>
                                                                    setDeleteId(
                                                                        null,
                                                                    )
                                                                }
                                                            >
                                                                <Button
                                                                    type="submit"
                                                                    variant="destructive"
                                                                    size="sm"
                                                                >
                                                                    Confirm
                                                                </Button>
                                                            </Form>
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                                onClick={() =>
                                                                    setDeleteId(
                                                                        null,
                                                                    )
                                                                }
                                                            >
                                                                Cancel
                                                            </Button>
                                                        </div>
                                                    ) : (
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            onClick={() =>
                                                                setDeleteId(
                                                                    product.id,
                                                                )
                                                            }
                                                        >
                                                            <Trash2 className="size-4 text-destructive" />
                                                        </Button>
                                                    )}
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>

                        {/* Pagination */}
                        {productList.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-between border-t pt-4">
                                <div className="text-sm text-muted-foreground">
                                    Showing {productList.from} to{' '}
                                    {productList.to} of {productList.total}{' '}
                                    products
                                </div>
                                <div className="flex gap-2">
                                    {productList.prev_page_url && (
                                        <Link href={productList.prev_page_url}>
                                            <Button variant="outline" size="sm">
                                                Previous
                                            </Button>
                                        </Link>
                                    )}
                                    <div className="flex items-center gap-1 text-sm">
                                        Page {productList.current_page} of{' '}
                                        {productList.last_page}
                                    </div>
                                    {productList.next_page_url && (
                                        <Link href={productList.next_page_url}>
                                            <Button variant="outline" size="sm">
                                                Next
                                            </Button>
                                        </Link>
                                    )}
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
