import { Badge } from '@/components/ui/badge';
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
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import checkout from '@/routes/checkout';
import { type BreadcrumbItem } from '@/types';
import { type Sale } from '@/types/sales';
import { Head, Link } from '@inertiajs/react';
import { CheckCircle2, ShoppingCart, Tag } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Checkout',
        href: checkout.index().url,
    },
    {
        title: 'Receipt',
        href: '#',
    },
];

export default function CheckoutReceipt({ sale }: { sale: Sale }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Receipt" />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
                {/* Success Message */}
                <Card className="border-green-200 bg-green-50 dark:border-green-900 dark:bg-green-950">
                    <CardContent className="flex items-center gap-4 pt-6">
                        <CheckCircle2 className="size-8 text-green-600 dark:text-green-400" />
                        <div>
                            <h3 className="font-semibold text-green-900 dark:text-green-100">
                                Checkout Complete!
                            </h3>
                            <p className="text-sm text-green-700 dark:text-green-300">
                                Your purchase has been recorded successfully.
                                {sale.total_savings > 0 && (
                                    <span className="font-semibold">
                                        {' '}
                                        You saved $
                                        {sale.total_savings.toFixed(2)}!
                                    </span>
                                )}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                {/* Receipt Details */}
                <div className="grid gap-6 md:grid-cols-3">
                    <div className="md:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <ShoppingCart className="size-5" />
                                    Receipt #{sale.id}
                                </CardTitle>
                                <CardDescription>
                                    {sale.created_at}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>SKU</TableHead>
                                            <TableHead>Product</TableHead>
                                            <TableHead className="text-center">
                                                Qty
                                            </TableHead>
                                            <TableHead className="text-right">
                                                Price
                                            </TableHead>
                                            <TableHead className="text-right">
                                                Discount
                                            </TableHead>
                                            <TableHead className="text-right">
                                                Total
                                            </TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {sale.items.map((item, index) => (
                                            <TableRow key={index}>
                                                <TableCell className="font-mono font-medium">
                                                    {item.sku}
                                                </TableCell>
                                                <TableCell>
                                                    <div className="flex items-center gap-2">
                                                        <span>
                                                            {item.product_name}
                                                        </span>
                                                        {item.promotion_applied &&
                                                            item.promotion && (
                                                                <Badge
                                                                    variant="secondary"
                                                                    className="bg-green-100 text-xs text-green-700 hover:bg-green-100 dark:bg-green-900 dark:text-green-300"
                                                                >
                                                                    <Tag className="mr-1 size-3" />
                                                                    {
                                                                        item
                                                                            .promotion
                                                                            .quantity
                                                                    }{' '}
                                                                    for $
                                                                    {item.promotion.special_price.toFixed(
                                                                        2,
                                                                    )}
                                                                </Badge>
                                                            )}
                                                    </div>
                                                </TableCell>
                                                <TableCell className="text-center">
                                                    {item.quantity}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {item.promotion_applied ? (
                                                        <div className="space-y-0.5">
                                                            <div className="text-xs text-muted-foreground line-through">
                                                                $
                                                                {item.regular_total.toFixed(
                                                                    2,
                                                                )}
                                                            </div>
                                                            <div className="text-sm">
                                                                $
                                                                {item.line_total.toFixed(
                                                                    2,
                                                                )}
                                                            </div>
                                                        </div>
                                                    ) : (
                                                        <span>
                                                            $
                                                            {item.line_total.toFixed(
                                                                2,
                                                            )}
                                                        </span>
                                                    )}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {item.savings > 0 ? (
                                                        <span className="font-medium text-green-600 dark:text-green-400">
                                                            -$
                                                            {item.savings.toFixed(
                                                                2,
                                                            )}
                                                        </span>
                                                    ) : (
                                                        <span className="text-muted-foreground">
                                                            -
                                                        </span>
                                                    )}
                                                </TableCell>
                                                <TableCell className="text-right font-medium">
                                                    $
                                                    {item.line_total.toFixed(2)}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                    <TableFooter>
                                        {sale.total_savings > 0 && (
                                            <>
                                                <TableRow>
                                                    <TableCell
                                                        colSpan={5}
                                                        className="text-right text-muted-foreground"
                                                    >
                                                        Subtotal
                                                    </TableCell>
                                                    <TableCell className="text-right">
                                                        $
                                                        {sale.regular_total.toFixed(
                                                            2,
                                                        )}
                                                    </TableCell>
                                                </TableRow>
                                                <TableRow>
                                                    <TableCell
                                                        colSpan={5}
                                                        className="text-right text-green-600 dark:text-green-400"
                                                    >
                                                        Total Savings
                                                    </TableCell>
                                                    <TableCell className="text-right font-medium text-green-600 dark:text-green-400">
                                                        -$
                                                        {sale.total_savings.toFixed(
                                                            2,
                                                        )}
                                                    </TableCell>
                                                </TableRow>
                                            </>
                                        )}
                                        <TableRow>
                                            <TableCell
                                                colSpan={5}
                                                className="text-lg font-bold"
                                            >
                                                Total
                                            </TableCell>
                                            <TableCell className="text-right text-lg font-bold">
                                                ${sale.total_amount.toFixed(2)}
                                            </TableCell>
                                        </TableRow>
                                    </TableFooter>
                                </Table>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Side Information */}
                    <div className="space-y-6">
                        {/* Savings Summary */}
                        {sale.total_savings > 0 && (
                            <Card className="border-green-200 bg-green-50 dark:border-green-900 dark:bg-green-950">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-base text-green-900 dark:text-green-100">
                                        <Tag className="size-4" />
                                        You Saved!
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-3">
                                    <div className="flex items-baseline justify-between">
                                        <span className="text-sm text-green-700 dark:text-green-300">
                                            Total Discount:
                                        </span>
                                        <span className="text-2xl font-bold text-green-900 dark:text-green-100">
                                            ${sale.total_savings.toFixed(2)}
                                        </span>
                                    </div>
                                    <div className="space-y-1 border-t border-green-200 pt-3 dark:border-green-800">
                                        <div className="flex justify-between text-sm">
                                            <span className="text-green-700 dark:text-green-300">
                                                Regular Total:
                                            </span>
                                            <span className="text-green-900 dark:text-green-100">
                                                ${sale.regular_total.toFixed(2)}
                                            </span>
                                        </div>
                                        <div className="flex justify-between text-sm">
                                            <span className="text-green-700 dark:text-green-300">
                                                Promotions Applied:
                                            </span>
                                            <span className="text-green-900 dark:text-green-100">
                                                {
                                                    sale.items.filter(
                                                        (i) =>
                                                            i.promotion_applied,
                                                    ).length
                                                }
                                            </span>
                                        </div>
                                        <div className="flex justify-between border-t border-green-200 pt-2 text-sm font-bold dark:border-green-800">
                                            <span className="text-green-900 dark:text-green-100">
                                                Final Total:
                                            </span>
                                            <span className="text-green-900 dark:text-green-100">
                                                ${sale.total_amount.toFixed(2)}
                                            </span>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">
                                    Transaction Details
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">
                                        Sale ID
                                    </p>
                                    <p className="font-mono text-sm">
                                        {sale.id}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">
                                        Date & Time
                                    </p>
                                    <p className="text-sm">{sale.created_at}</p>
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">
                                        Order Details
                                    </p>
                                    <p className="text-sm">
                                        {sale.items_input
                                            .map(
                                                (item) =>
                                                    `${item.quantity}x ${item.sku}`,
                                            )
                                            .join(', ')}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">
                                        Items Count
                                    </p>
                                    <p className="text-sm">
                                        {sale.items.length} unique products
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">
                                    Actions
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-2">
                                <Link
                                    href={checkout.index().url}
                                    className="block"
                                >
                                    <Button
                                        className="w-full"
                                        variant="default"
                                    >
                                        New Checkout
                                    </Button>
                                </Link>
                                <Button
                                    className="w-full"
                                    variant="outline"
                                    onClick={() => window.print()}
                                >
                                    Print Receipt
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
