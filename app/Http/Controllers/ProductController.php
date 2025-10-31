<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(): Response
    {
        $products = Product::with('activePromotion')
            ->orderBy('sku')
            ->paginate(10)
            ->through(function ($product) {
                return [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'unit_price' => (float) $product->unit_price,
                    'is_active' => $product->is_active,
                    'promotion' => $product->activePromotion ? [
                        'id' => $product->activePromotion->id,
                        'quantity' => $product->activePromotion->quantity,
                        'special_price' => (float) $product->activePromotion->special_price,
                    ] : null,
                ];
            });

        return Inertia::render('products/index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): Response
    {
        return Inertia::render('products/create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): Response
    {
        return Inertia::render('products/show', [
            'product' => [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'unit_price' => (float) $product->unit_price,
                'is_active' => $product->is_active,
                'promotions' => $product->promotions->map(function ($promotion) {
                    return [
                        'id' => $promotion->id,
                        'quantity' => $promotion->quantity,
                        'special_price' => (float) $promotion->special_price,
                        'is_active' => $promotion->is_active,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('products/edit', [
            'product' => [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'unit_price' => (float) $product->unit_price,
                'is_active' => $product->is_active,
            ],
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
