<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Product;
use App\Services\CheckoutService;
use Inertia\Inertia;
use Inertia\Response;

final class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkoutService
    ) {
    }

    /**
     * Show the checkout form.
     */
    public function index(): Response
    {
        $products = Product::with('activePromotion')
            ->active()
            ->orderBy('sku')
            ->get()
            ->map->toCheckoutArray();

        return Inertia::render('checkout/index', [
            'products' => $products,
        ]);
    }

    /**
     * Process the checkout.
     */
    public function store(CheckoutRequest $request)
    {
        try {
            $sale = $this->checkoutService->process(
                $request->user(),
                $request->validated('items')
            );

            return Inertia::render('checkout/receipt', [
                'sale' => $sale->toInertiaArray(),
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['items' => $e->getMessage()]);
        }
    }
}
