import PromotionController from '@/actions/App/Http/Controllers/PromotionController';
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
import promotions from '@/routes/promotions';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/react';
import { Edit, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface PromotionWithProduct {
    id: number;
    product_id: number;
    product: {
        sku: string;
        name: string;
    };
    quantity: number;
    special_price: number;
    is_active: boolean;
}

interface PaginatedPromotions {
    current_page: number;
    data: PromotionWithProduct[];
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
        title: 'Promotions',
        href: promotions.index().url,
    },
];

export default function PromotionIndex({
    promotions: promotionList,
}: {
    promotions: PaginatedPromotions;
}) {
    const [deleteId, setDeleteId] = useState<number | null>(null);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Promotions" />

            <div className="flex flex-1 flex-col gap-6 overflow-auto rounded-xl p-4">
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle>Promotion Management</CardTitle>
                            <CardDescription>
                                Manage special pricing and bulk discounts
                            </CardDescription>
                        </div>
                        <Link href={promotions.create().url}>
                            <Button>
                                <Plus className="mr-2 size-4" />
                                Add Promotion
                            </Button>
                        </Link>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Product</TableHead>
                                    <TableHead className="text-center">
                                        Quantity
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Special Price
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
                                {promotionList.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={5}
                                            className="text-center text-muted-foreground"
                                        >
                                            No promotions found. Create your
                                            first promotion to offer special
                                            pricing.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    promotionList.data.map((promotion) => (
                                        <TableRow key={promotion.id}>
                                            <TableCell>
                                                <div>
                                                    <span className="font-medium">
                                                        {promotion.product.name}
                                                    </span>
                                                    <div className="font-mono text-sm text-muted-foreground">
                                                        SKU:{' '}
                                                        {promotion.product.sku}
                                                    </div>
                                                </div>
                                            </TableCell>
                                            <TableCell className="text-center font-medium">
                                                {promotion.quantity}
                                            </TableCell>
                                            <TableCell className="text-right font-medium">
                                                $
                                                {promotion.special_price.toFixed(
                                                    2,
                                                )}
                                            </TableCell>
                                            <TableCell className="text-center">
                                                {promotion.is_active ? (
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
                                                            promotions.edit({
                                                                promotion:
                                                                    promotion.id,
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
                                                    {deleteId ===
                                                    promotion.id ? (
                                                        <div className="flex gap-1">
                                                            <Form
                                                                {...PromotionController.destroy.form(
                                                                    {
                                                                        promotion:
                                                                            promotion.id,
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
                                                                    promotion.id,
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
                        {promotionList.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-between border-t pt-4">
                                <div className="text-sm text-muted-foreground">
                                    Showing {promotionList.from} to{' '}
                                    {promotionList.to} of {promotionList.total}{' '}
                                    promotions
                                </div>
                                <div className="flex gap-2">
                                    {promotionList.prev_page_url && (
                                        <Link
                                            href={promotionList.prev_page_url}
                                        >
                                            <Button variant="outline" size="sm">
                                                Previous
                                            </Button>
                                        </Link>
                                    )}
                                    <div className="flex items-center gap-1 text-sm">
                                        Page {promotionList.current_page} of{' '}
                                        {promotionList.last_page}
                                    </div>
                                    {promotionList.next_page_url && (
                                        <Link
                                            href={promotionList.next_page_url}
                                        >
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
