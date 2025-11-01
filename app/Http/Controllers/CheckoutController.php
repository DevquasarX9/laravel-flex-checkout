<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Product;
use App\Services\CheckoutService;
use App\Support\CacheKeys;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

final class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkoutService
    ) {
    }

    public function index(): Response
    {
        $products = Cache::remember(
            CacheKeys::CHECKOUT_PRODUCTS,
            now()->addMinutes(CacheKeys::CHECKOUT_PRODUCTS_TTL),
            static fn () => Product::with('activePromotion')
                ->active()
                ->orderBy('sku')
                ->get()
                ->map->toCheckoutArray()
        );

        return Inertia::render('checkout/index', [
            'products' => $products,
        ]);
    }

    public function store(CheckoutRequest $request): Response
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
